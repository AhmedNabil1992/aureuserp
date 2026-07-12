<?php

namespace Webkul\Software\Services;

use Illuminate\Support\Facades\Auth;
use Webkul\Account\Enums\DisplayType;
use Webkul\Account\Enums\JournalType;
use Webkul\Account\Enums\MoveState;
use Webkul\Account\Enums\MoveType;
use Webkul\Account\Facades\Account as AccountFacade;
use Webkul\Account\Models\Journal;
use Webkul\Account\Models\Move as AccountMove;
use Webkul\Product\Enums\ProductType;
use Webkul\Product\Models\Product;
use Webkul\Software\Enums\ServiceType;
use Webkul\Software\Models\License;
use Webkul\Software\Models\LicenseInvoice;
use Webkul\Software\Models\ProgramEdition;
use Webkul\Software\Models\ProgramEditionFeature;
use Webkul\Software\Models\ProgramFeature;
use Webkul\Support\Models\Company;

class LicenseInvoiceManager
{
    /**
     * Create invoice for a license
     *
     * @return array{invoice: LicenseInvoice, accountMove: AccountMove}
     */
    public function createInvoice(
        License $license,
        int $editionId,
        string $licensePlan,
        string $billingContext = 'initial'
    ): array {
        $edition = ProgramEdition::findOrFail($editionId);
        $user = Auth::user();
        $company = $user?->default_company_id
            ? Company::find($user->default_company_id)
            : null;

        if (! $company || ! $company->currency_id) {
            throw new \RuntimeException('No default company or currency configured for the current user.');
        }

        $billingProduct = $this->resolveEditionBillingProduct($edition);
        $this->validateEdition($edition, $license, $billingProduct);
        $itemName = $billingProduct->name;

        // Create AccountMove (invoice)
        $accountMove = $this->createAccountMove($license, $company);

        // Create invoice lines
        $this->createEditionInvoiceLine($accountMove, $billingProduct, $licensePlan);
        $this->createFeatureInvoiceLines($accountMove, $license, $billingContext);

        // Compute totals
        AccountFacade::computeAccountMove($accountMove);
        $accountMove->refresh();

        // Create local invoice record
        $invoiceNumber = filled($accountMove->name)
            ? (string) $accountMove->name
            : 'MOVE-'.$accountMove->id;

        $licenseInvoice = LicenseInvoice::create([
            'license_id'      => $license->id,
            'program_id'      => $license->program_id,
            'edition_id'      => $edition->id,
            'license_plan'    => $licensePlan,
            'invoice_number'  => $invoiceNumber,
            'item_name'       => $itemName,
            'quantity'        => 1,
            'unit_price'      => $this->getEditionPrice($billingProduct),
            'amount'          => $this->getEditionPrice($billingProduct),
            'billed_by'       => Auth::id(),
            'billed_at'       => now(),
            'notes'           => 'Linked to accounts invoice #'.$invoiceNumber,
            'account_move_id' => $accountMove->id,
        ]);

        return [
            'invoice'     => $licenseInvoice,
            'accountMove' => $accountMove,
        ];
    }

    /**
     * Validate edition before billing
     */
    private function validateEdition(
        ProgramEdition $edition,
        License $license,
        Product $billingProduct
    ): void {
        if ($edition->program_id !== $license->program_id) {
            throw new \RuntimeException('Edition does not belong to the program.');
        }

        $baseProduct = $edition->program?->product;
        if (! $baseProduct) {
            throw new \RuntimeException('Program must be linked to a base service product first.');
        }

        $productType = $billingProduct->type?->value ?? $billingProduct->type;
        if ($productType !== ProductType::SERVICE->value) {
            throw new \RuntimeException('Edition variant product must be of type service.');
        }

        $price = $this->getEditionPrice($billingProduct);
        if ($price <= 0) {
            throw new \RuntimeException('Selected variant price must be greater than zero.');
        }
    }

    /**
     * Resolve the billable edition product.
     */
    private function resolveEditionBillingProduct(ProgramEdition $edition): Product
    {
        $baseProduct = $edition->program?->product;
        if (! $baseProduct) {
            throw new \RuntimeException('Program must be linked to a base service product first.');
        }

        $variantProduct = $edition->variantProduct;
        if (! $variantProduct) {
            throw new \RuntimeException('Edition must be linked to a variant product.');
        }

        if ((int) $variantProduct->parent_id !== (int) $baseProduct->id) {
            throw new \RuntimeException('Linked variant does not belong to the selected program base product.');
        }

        return $variantProduct;
    }

    /**
     * Get edition price based on license plan
     */
    private function getEditionPrice(Product $product): float
    {
        return (float) ($product->price ?? 0);
    }

    /**
     * Create AccountMove (invoice)
     */
    private function createAccountMove(
        License $license,
        Company $company
    ): AccountMove {
        $journal = $this->getSalesJournal($company);

        return AccountMove::create([
            'move_type'        => MoveType::OUT_INVOICE,
            'state'            => MoveState::DRAFT,
            'journal_id'       => $journal->id,
            'invoice_origin'   => $license->serial_number,
            'date'             => now()->toDateString(),
            'invoice_date'     => now()->toDateString(),
            'invoice_date_due' => now()->toDateString(),
            'company_id'       => $company->id,
            'currency_id'      => $company->currency_id,
            'partner_id'       => $license->partner_id,
            'creator_id'       => Auth::id(),
            'invoice_user_id'  => Auth::id(),
        ]);
    }

    /**
     * Create invoice line for edition
     */
    private function createEditionInvoiceLine(
        AccountMove $accountMove,
        Product $billingProduct,
        string $licensePlan
    ): void {
        $price = $this->getEditionPrice($billingProduct);
        $itemName = $billingProduct->name;

        $accountMove->invoiceLines()->create([
            'name'         => $itemName.' ('.ucfirst($licensePlan).')',
            'date'         => $accountMove->date,
            'display_type' => DisplayType::PRODUCT,
            'parent_state' => MoveState::DRAFT,
            'quantity'     => 1,
            'price_unit'   => $price,
            'currency_id'  => $accountMove->currency_id,
            'product_id'   => $billingProduct->id,
            'uom_id'       => $billingProduct->uom_id,
            'creator_id'   => Auth::id(),
        ]);
    }

    /**
     * Create invoice lines for features
     */
    private function createFeatureInvoiceLines(
        AccountMove $accountMove,
        License $license,
        string $billingContext
    ): void {
        $edition = $license->edition?->loadMissing('featureRules.feature.product');

        if ($edition?->featureRules->isNotEmpty()) {
            $this->createConfiguredFeatureInvoiceLines($accountMove, $edition, $billingContext);

            return;
        }

        $this->createLegacyFeatureInvoiceLines($accountMove, $license);
    }

    private function createConfiguredFeatureInvoiceLines(
        AccountMove $accountMove,
        ProgramEdition $edition,
        string $billingContext
    ): void {
        $rules = $edition->featureRules->filter(function (ProgramEditionFeature $rule) use ($billingContext): bool {
            if ($billingContext === 'renewal') {
                return $rule->auto_renew_with_license && $rule->invoice_on_renewal;
            }

            return $rule->auto_attach_on_final_license
                && $rule->invoice_on_initial_billing
                && ! $rule->is_complimentary;
        });

        foreach ($rules as $rule) {
            $feature = $rule->feature;

            if (! $feature) {
                throw new \RuntimeException('Edition feature rule is linked to a missing feature.');
            }

            $featureProduct = $feature->product;

            if (! $featureProduct) {
                throw new \RuntimeException('Feature '.$feature->name.' must be linked to a service product.');
            }

            $productType = $featureProduct->type?->value ?? $featureProduct->type;
            if ($productType !== ProductType::SERVICE->value) {
                throw new \RuntimeException('Feature '.$feature->name.' must be linked to a product of type service.');
            }

            $featurePrice = $this->getConfiguredFeaturePrice($rule, $featureProduct);
            if ($featurePrice <= 0) {
                throw new \RuntimeException('Feature '.$feature->name.' has invalid configured price.');
            }

            $accountMove->invoiceLines()->create([
                'name'         => $feature->name,
                'date'         => $accountMove->date,
                'display_type' => DisplayType::PRODUCT,
                'parent_state' => MoveState::DRAFT,
                'quantity'     => 1,
                'price_unit'   => $featurePrice,
                'currency_id'  => $accountMove->currency_id,
                'product_id'   => $featureProduct->id,
                'uom_id'       => $featureProduct->uom_id,
                'creator_id'   => Auth::id(),
            ]);
        }
    }

    private function createLegacyFeatureInvoiceLines(
        AccountMove $accountMove,
        License $license
    ): void {
        $serviceFeatures = ProgramFeature::query()
            ->where('program_id', $license->program_id)
            ->whereIn('service_type', [ServiceType::TechnicalSupport->value, ServiceType::Mail->value])
            ->with('product')
            ->get()
            ->groupBy('service_type');

        foreach ([ServiceType::TechnicalSupport->value, ServiceType::Mail->value] as $requiredType) {
            $featuresOfType = $serviceFeatures->get($requiredType, collect());

            if ($featuresOfType->count() !== 1) {
                throw new \RuntimeException('Exactly one feature row is required for '.$requiredType.'.');
            }

            $feature = $featuresOfType->first();
            $featureProduct = $feature->product;

            if (! $featureProduct) {
                throw new \RuntimeException('Feature '.$feature->name.' must be linked to a service product.');
            }

            $productType = $featureProduct->type?->value ?? $featureProduct->type;
            if ($productType !== ProductType::SERVICE->value) {
                throw new \RuntimeException('Feature '.$feature->name.' must be linked to a product of type service.');
            }

            $featurePrice = (float) ($featureProduct->price ?? 0);
            if ($featurePrice <= 0) {
                throw new \RuntimeException('Feature '.$feature->name.' has invalid service price.');
            }

            $accountMove->invoiceLines()->create([
                'name'         => $feature->name,
                'date'         => $accountMove->date,
                'display_type' => DisplayType::PRODUCT,
                'parent_state' => MoveState::DRAFT,
                'quantity'     => 1,
                'price_unit'   => $featurePrice,
                'currency_id'  => $accountMove->currency_id,
                'product_id'   => $featureProduct->id,
                'uom_id'       => $featureProduct->uom_id,
                'creator_id'   => Auth::id(),
            ]);
        }
    }

    private function getConfiguredFeaturePrice(ProgramEditionFeature $rule, Product $featureProduct): float
    {
        return (float) ($rule->price ?? $featureProduct->price ?? 0);
    }

    /**
     * Get sales journal for company
     */
    private function getSalesJournal(Company $company): Journal
    {
        $journal = Journal::query()
            ->where('type', JournalType::SALE)
            ->where('company_id', $company->id)
            ->first();

        if (! $journal) {
            throw new \RuntimeException('No Sales Journal configured for your default company.');
        }

        return $journal;
    }
}

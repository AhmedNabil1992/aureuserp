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
use Webkul\Software\Enums\ServiceType;
use Webkul\Software\Models\License;
use Webkul\Software\Models\LicenseInvoice;
use Webkul\Software\Models\ProgramEdition;
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
        string $licensePlan
    ): array {
        $edition = ProgramEdition::findOrFail($editionId);
        $user = Auth::user();
        $company = $user?->default_company_id
            ? Company::find($user->default_company_id)
            : null;

        if (! $company || ! $company->currency_id) {
            throw new \RuntimeException('No default company or currency configured for the current user.');
        }

        $this->validateEdition($edition, $license);

        $variantProduct = $edition->variantProduct ?? ($edition->product_id ? $edition->product : null);
        $itemName = $variantProduct?->name ?? $edition->name;

        // Create AccountMove (invoice)
        $accountMove = $this->createAccountMove($license, $company);

        // Create invoice lines
        $this->createEditionInvoiceLine($accountMove, $edition, $licensePlan);
        $this->createFeatureInvoiceLines($accountMove, $license);

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
            'unit_price'      => $this->getEditionPrice($edition),
            'amount'          => $this->getEditionPrice($edition),
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
        License $license
    ): void {
        if ($edition->program_id !== $license->program_id) {
            throw new \RuntimeException('Edition does not belong to the program.');
        }

        $baseProduct = $edition->program?->product;
        if (! $baseProduct) {
            throw new \RuntimeException('Program must be linked to a base service product first.');
        }

        $variantProduct = $edition->variantProduct ?? ($edition->product_id ? $edition->product : null);
        if (! $variantProduct) {
            throw new \RuntimeException('Edition must be linked to a variant product.');
        }

        if ((int) $variantProduct->parent_id !== (int) $baseProduct->id) {
            throw new \RuntimeException('Linked variant does not belong to the selected program base product.');
        }

        $productType = $variantProduct->type?->value ?? $variantProduct->type;
        if ($productType !== ProductType::SERVICE->value) {
            throw new \RuntimeException('Edition variant product must be of type service.');
        }

        $price = $this->getEditionPrice($edition);
        if ($price <= 0) {
            throw new \RuntimeException('Selected variant price must be greater than zero.');
        }
    }

    /**
     * Get edition price based on license plan
     */
    private function getEditionPrice(ProgramEdition $edition): float
    {
        $variantProduct = $edition->variantProduct ?? ($edition->product_id ? $edition->product : null);

        return (float) ($variantProduct?->price ?? 0);
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
        ProgramEdition $edition,
        string $licensePlan
    ): void {
        $variantProduct = $edition->variantProduct ?? ($edition->product_id ? $edition->product : null);
        $price = $this->getEditionPrice($edition);
        $itemName = $variantProduct?->name ?? $edition->name;

        $accountMove->invoiceLines()->create([
            'name'         => $itemName.' ('.ucfirst($licensePlan).')',
            'date'         => $accountMove->date,
            'display_type' => DisplayType::PRODUCT,
            'parent_state' => MoveState::DRAFT,
            'quantity'     => 1,
            'price_unit'   => $price,
            'currency_id'  => $accountMove->currency_id,
            'product_id'   => $variantProduct?->id,
            'uom_id'       => $variantProduct?->uom_id,
            'creator_id'   => Auth::id(),
        ]);
    }

    /**
     * Create invoice lines for features
     */
    private function createFeatureInvoiceLines(
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

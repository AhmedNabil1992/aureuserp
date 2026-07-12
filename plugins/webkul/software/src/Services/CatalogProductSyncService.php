<?php

namespace Webkul\Software\Services;

use Illuminate\Support\Facades\Auth;
use Webkul\Product\Enums\ProductType;
use Webkul\Product\Models\Product;
use Webkul\Software\Models\Program;
use Webkul\Software\Models\ProgramEdition;
use Webkul\Software\Models\ProgramFeature;

class CatalogProductSyncService
{
    public function syncProgram(Program $program): Product
    {
        $baseName = trim((string) $program->name);

        $baseProduct = Product::query()->firstOrCreate(
            [
                'name'      => $baseName,
                'parent_id' => null,
            ],
            $this->buildBaseProductAttributes($baseName)
        );

        if ((int) $program->product_id !== (int) $baseProduct->id) {
            $program->updateQuietly(['product_id' => $baseProduct->id]);
        }

        return $baseProduct;
    }

    public function syncEdition(ProgramEdition $edition): Product
    {
        $program = $edition->program()->firstOrFail();
        $baseProduct = $this->syncProgram($program);
        $variantName = trim($program->name.' - '.$edition->name);

        $variantProduct = Product::query()->firstOrCreate(
            [
                'parent_id' => $baseProduct->id,
                'name'      => $variantName,
            ],
            $this->buildVariantProductAttributes($baseProduct, (float) ($edition->license_price ?? 0))
        );

        $variantProduct->update([
            'price'           => (float) ($edition->license_price ?? 0),
            'enable_sales'    => true,
            'enable_purchase' => false,
        ]);

        $editionUpdates = [];

        if ((int) $edition->variant_product_id !== (int) $variantProduct->id) {
            $editionUpdates['variant_product_id'] = $variantProduct->id;
        }

        if ($editionUpdates !== []) {
            $edition->updateQuietly($editionUpdates);
        }

        return $variantProduct;
    }

    public function syncFeature(ProgramFeature $feature): Product
    {
        $program = $feature->program()->firstOrFail();
        $baseProduct = $this->syncProgram($program);
        $featureName = trim($program->name.' - Feature - '.$feature->name);

        $featureProduct = Product::query()->firstOrCreate(
            [
                'parent_id' => $baseProduct->id,
                'name'      => $featureName,
            ],
            $this->buildVariantProductAttributes($baseProduct, (float) ($feature->amount ?? 0))
        );

        $featureProduct->update([
            'price'           => (float) ($feature->amount ?? 0),
            'enable_sales'    => true,
            'enable_purchase' => false,
        ]);

        if ((int) $feature->product_id !== (int) $featureProduct->id) {
            $feature->updateQuietly(['product_id' => $featureProduct->id]);
        }

        return $featureProduct;
    }

    private function buildBaseProductAttributes(string $name): array
    {
        $template = $this->getTemplateServiceProduct();

        return [
            'type'             => ProductType::SERVICE->value,
            'service_tracking' => $template?->service_tracking ?? 'none',
            'reference'        => null,
            'price'            => 0,
            'cost'             => 0,
            'enable_sales'     => true,
            'enable_purchase'  => false,
            'is_favorite'      => false,
            'is_configurable'  => false,
            'uom_id'           => $template?->uom_id,
            'uom_po_id'        => $template?->uom_po_id,
            'category_id'      => $template?->category_id,
            'company_id'       => $template?->company_id,
            'creator_id'       => Auth::id() ?? $template?->creator_id,
            'name'             => $name,
        ];
    }

    private function buildVariantProductAttributes(Product $baseProduct, float $price): array
    {
        return [
            'type'             => ProductType::SERVICE->value,
            'service_tracking' => $baseProduct->service_tracking,
            'reference'        => null,
            'price'            => $price,
            'cost'             => 0,
            'enable_sales'     => true,
            'enable_purchase'  => false,
            'is_favorite'      => false,
            'is_configurable'  => false,
            'uom_id'           => $baseProduct->uom_id,
            'uom_po_id'        => $baseProduct->uom_po_id,
            'category_id'      => $baseProduct->category_id,
            'company_id'       => $baseProduct->company_id,
            'creator_id'       => Auth::id() ?? $baseProduct->creator_id,
        ];
    }

    private function getTemplateServiceProduct(): ?Product
    {
        return Product::query()
            ->where('type', ProductType::SERVICE->value)
            ->whereNull('parent_id')
            ->orderBy('id')
            ->first();
    }
}

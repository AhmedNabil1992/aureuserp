<?php

namespace Webkul\Product\Listeners;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Webkul\Account\Enums\MoveType;
use Webkul\Account\Events\MoveConfirmed;
use Webkul\Account\Models\Move;
use Webkul\Inventory\Enums\LocationType;
use Webkul\Inventory\Enums\MoveState;
use Webkul\Inventory\Enums\MoveType as InventoryMoveType;
use Webkul\Inventory\Enums\OperationState;
use Webkul\Inventory\Enums\OperationType as InventoryOperationType;
use Webkul\Inventory\Enums\ProcureMethod;
use Webkul\Inventory\Facades\Inventory;
use Webkul\Inventory\Models\Delivery;
use Webkul\Inventory\Models\Location;
use Webkul\Inventory\Models\Move as InventoryMove;
use Webkul\Inventory\Models\OperationType;
use Webkul\PluginManager\Package;
use Webkul\Product\Enums\BomType;
use Webkul\Product\Enums\ProductType;
use Webkul\Product\Models\BillOfMaterial;

class DeductComponentsOnInvoiceConfirmed
{
    public function handle(MoveConfirmed $event): void
    {
        $move = $event->move;

        if ($move->move_type !== MoveType::OUT_INVOICE) {
            return;
        }

        if (! Package::isPluginInstalled('inventories')) {
            return;
        }

        if ($this->isLinkedToSaleOrder($move->id)) {
            return;
        }

        $operationType = $this->resolveOutgoingOperationType($move);

        if (! $operationType) {
            return;
        }

        $destinationLocation = $this->resolveDestinationLocation($operationType, $move->company_id);

        if (! $destinationLocation) {
            return;
        }

        $move->loadMissing(['lines.product.uom', 'lines.uom']);

        $productLines = $move->lines->filter(
            fn ($line) => $line->product_id && $line->quantity > 0
        );

        foreach ($productLines as $line) {
            if (! $line->product || ! in_array($line->product->type, [ProductType::GOODS, ProductType::PRODUCT], true)) {
                continue;
            }

            $bom = $this->resolveBillOfMaterial($line->product_id, $move->company_id);

            if (! $bom || $bom->lines->isEmpty()) {
                $sourceLocation = $this->resolveDefaultSourceLocation($move, $operationType);

                if (! $sourceLocation) {
                    continue;
                }

                $lineUom = $line->uom ?? $line->product->uom;

                if (! $lineUom || ! $line->product->uom) {
                    continue;
                }

                $moveUomQuantity = (float) $line->quantity;

                if ($moveUomQuantity <= 0) {
                    continue;
                }

                $productQuantity = $lineUom->computeQuantity($moveUomQuantity, $line->product->uom, true, 'HALF-UP');

                $operation = Delivery::create([
                    'state'                   => OperationState::DRAFT,
                    'move_type'               => InventoryMoveType::DIRECT,
                    'origin'                  => $move->name.' / PRODUCT / '.$line->name,
                    'partner_id'              => $move->partner_id,
                    'date'                    => $move->date ?? now(),
                    'operation_type_id'       => $operationType->id,
                    'source_location_id'      => $sourceLocation->id,
                    'destination_location_id' => $destinationLocation->id,
                    'company_id'              => $move->company_id,
                    'user_id'                 => Auth::id(),
                    'creator_id'              => Auth::id(),
                ]);

                InventoryMove::create([
                    'operation_id'            => $operation->id,
                    'name'                    => $line->product->name,
                    'reference'               => $operation->name,
                    'origin'                  => $move->name,
                    'state'                   => MoveState::DRAFT,
                    'scheduled_at'            => $move->date ?? now(),
                    'deadline'                => $move->date ?? now(),
                    'reservation_date'        => now(),
                    'product_id'              => $line->product->id,
                    'product_qty'             => $productQuantity,
                    'product_uom_qty'         => $moveUomQuantity,
                    'quantity'                => $moveUomQuantity,
                    'uom_id'                  => $lineUom->id,
                    'partner_id'              => $operation->partner_id,
                    'warehouse_id'            => $operationType->warehouse_id,
                    'source_location_id'      => $sourceLocation->id,
                    'destination_location_id' => $destinationLocation->id,
                    'final_location_id'       => $destinationLocation->id,
                    'operation_type_id'       => $operation->operation_type_id,
                    'company_id'              => $operation->company_id,
                    'procure_method'          => ProcureMethod::MAKE_TO_STOCK,
                ]);

                $operation = Inventory::computeTransfer($operation->refresh());
                Inventory::validateTransfer($operation);

                $move->addMessage([
                    'body' => "A delivery <strong>{$operation->name}</strong> has been created automatically for product stock deduction.",
                    'type' => 'comment',
                ]);

                continue;
            }

            $sourceLocation = $this->resolveSourceLocation($move, $bom, $operationType);

            if (! $sourceLocation) {
                continue;
            }

            $lineUom = $line->uom ?? $line->product?->uom;
            $bomUom = $bom->uom ?? $line->product?->uom;

            if (! $lineUom || ! $bomUom || (float) $bom->quantity <= 0) {
                continue;
            }

            $producedQuantity = $lineUom->computeQuantity((float) $line->quantity, $bomUom, true, 'HALF-UP');

            $scale = $producedQuantity / (float) $bom->quantity;

            $operation = Delivery::create([
                'state'                   => OperationState::DRAFT,
                'move_type'               => InventoryMoveType::DIRECT,
                'origin'                  => $move->name.' / BOM / '.$line->name,
                'partner_id'              => $move->partner_id,
                'date'                    => $move->date ?? now(),
                'operation_type_id'       => $operationType->id,
                'source_location_id'      => $sourceLocation->id,
                'destination_location_id' => $destinationLocation->id,
                'company_id'              => $move->company_id,
                'user_id'                 => Auth::id(),
                'creator_id'              => Auth::id(),
            ]);

            foreach ($bom->lines as $bomLine) {
                $component = $bomLine->component;

                if (! $component) {
                    continue;
                }

                $moveUom = $bomLine->uom ?? $component->uom;

                if (! $moveUom || ! $component->uom) {
                    continue;
                }

                $moveUomQuantity = round((float) $bomLine->quantity * $scale, 4);

                if ($moveUomQuantity <= 0) {
                    continue;
                }

                $productQuantity = $moveUom->computeQuantity($moveUomQuantity, $component->uom, true, 'HALF-UP');

                InventoryMove::create([
                    'operation_id'            => $operation->id,
                    'name'                    => $component->name,
                    'reference'               => $operation->name,
                    'origin'                  => $move->name,
                    'state'                   => MoveState::DRAFT,
                    'scheduled_at'            => $move->date ?? now(),
                    'deadline'                => $move->date ?? now(),
                    'reservation_date'        => now(),
                    'product_id'              => $component->id,
                    'product_qty'             => $productQuantity,
                    'product_uom_qty'         => $moveUomQuantity,
                    'quantity'                => $moveUomQuantity,
                    'uom_id'                  => $moveUom->id,
                    'partner_id'              => $operation->partner_id,
                    'warehouse_id'            => $operationType->warehouse_id,
                    'source_location_id'      => $sourceLocation->id,
                    'destination_location_id' => $destinationLocation->id,
                    'final_location_id'       => $destinationLocation->id,
                    'operation_type_id'       => $operation->operation_type_id,
                    'company_id'              => $operation->company_id,
                    'procure_method'          => ProcureMethod::MAKE_TO_STOCK,
                ]);
            }

            $operation->refresh();

            if ($operation->moves()->count() === 0) {
                $operation->delete();

                continue;
            }

            $operation = Inventory::computeTransfer($operation);
            Inventory::validateTransfer($operation);

            $move->addMessage([
                'body' => "A delivery <strong>{$operation->name}</strong> has been created automatically for BOM component consumption.",
                'type' => 'comment',
            ]);
        }
    }

    private function isLinkedToSaleOrder(int $moveId): bool
    {
        if (! Package::isPluginInstalled('sales')) {
            return false;
        }

        return DB::table('sales_order_invoices')
            ->where('move_id', $moveId)
            ->exists();
    }

    private function resolveOutgoingOperationType(Move $move): ?OperationType
    {
        $operationType = OperationType::where('type', InventoryOperationType::OUTGOING)
            ->whereHas('warehouse', function ($query) use ($move): void {
                $query->where('company_id', $move->company_id);
            })
            ->first();

        if (! $operationType) {
            $operationType = OperationType::where('type', InventoryOperationType::OUTGOING)
                ->whereDoesntHave('warehouse')
                ->first();
        }

        return $operationType;
    }

    private function resolveDestinationLocation(OperationType $operationType, ?int $companyId): ?Location
    {
        if ($operationType->destinationLocation) {
            return $operationType->destinationLocation;
        }

        return Location::where('type', LocationType::CUSTOMER)
            ->where(function ($query) use ($companyId): void {
                $query->where('company_id', $companyId)
                    ->orWhereNull('company_id');
            })
            ->first();
    }

    private function resolveBillOfMaterial(int $productId, ?int $companyId): ?BillOfMaterial
    {
        return BillOfMaterial::query()
            ->with(['uom', 'sourceLocation', 'lines.component.uom', 'lines.uom'])
            ->where('product_id', $productId)
            ->where('type', BomType::Manufacture->value)
            ->where(function ($query) use ($companyId): void {
                $query->where('company_id', $companyId)
                    ->orWhereNull('company_id');
            })
            ->orderByRaw('CASE WHEN company_id = ? THEN 0 WHEN company_id IS NULL THEN 1 ELSE 2 END', [$companyId])
            ->orderBy('id')
            ->first();
    }

    private function resolveSourceLocation(Move $move, BillOfMaterial $billOfMaterial, OperationType $operationType): ?Location
    {
        if ($billOfMaterial->sourceLocation) {
            return $billOfMaterial->sourceLocation;
        }

        if ($operationType->sourceLocation) {
            return $operationType->sourceLocation;
        }

        return Location::where('type', LocationType::INTERNAL)
            ->where('is_scrap', false)
            ->when($move->company_id, function ($query) use ($move): void {
                $query->where(function ($locationQuery) use ($move): void {
                    $locationQuery->where('company_id', $move->company_id)->orWhereNull('company_id');
                });
            })
            ->first();
    }

    private function resolveDefaultSourceLocation(Move $move, OperationType $operationType): ?Location
    {
        if ($operationType->sourceLocation) {
            return $operationType->sourceLocation;
        }

        return Location::where('type', LocationType::INTERNAL)
            ->where('is_scrap', false)
            ->when($move->company_id, function ($query) use ($move): void {
                $query->where(function ($locationQuery) use ($move): void {
                    $locationQuery->where('company_id', $move->company_id)->orWhereNull('company_id');
                });
            })
            ->first();
    }
}

<?php

namespace Webkul\Purchase\Listeners;

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
use Webkul\Inventory\Facades\Inventory;
use Webkul\Inventory\Models\Location;
use Webkul\Inventory\Models\Move as InventoryMove;
use Webkul\Inventory\Models\OperationType;
use Webkul\Inventory\Models\Receipt;
use Webkul\PluginManager\Package;
use Webkul\Product\Enums\ProductType;

class CreateReceiptOnBillConfirmed
{
    public function handle(MoveConfirmed $event): void
    {
        $move = $event->move;

        if ($move->move_type !== MoveType::IN_INVOICE) {
            return;
        }

        if (! Package::isPluginInstalled('inventories')) {
            return;
        }

        // Check if this bill is already linked to a purchase order.
        // If it is, the PO flow already handles receipt creation.
        $linkedToOrder = DB::table('purchases_order_account_moves')
            ->where('move_id', $move->id)
            ->exists();

        if ($linkedToOrder) {
            return;
        }

        $move->loadMissing(['lines.product', 'company']);

        $goodsLines = $move->lines->filter(
            fn ($line) => $line->product && $line->product->type === ProductType::GOODS && $line->quantity > 0
        );

        if ($goodsLines->isEmpty()) {
            return;
        }

        $operationType = $this->getOperationType($move);

        if (! $operationType) {
            return;
        }

        $supplierLocation = Location::where('type', LocationType::SUPPLIER)->first();

        if (! $supplierLocation) {
            return;
        }

        $operation = Receipt::create([
            'state'                   => OperationState::DRAFT,
            'move_type'               => InventoryMoveType::DIRECT,
            'origin'                  => $move->name,
            'partner_id'              => $move->partner_id,
            'date'                    => $move->date ?? now(),
            'operation_type_id'       => $operationType->id,
            'source_location_id'      => $supplierLocation->id,
            'destination_location_id' => $operationType->destination_location_id,
            'company_id'              => $move->company_id,
            'user_id'                 => Auth::id(),
            'creator_id'              => Auth::id(),
        ]);

        foreach ($goodsLines as $line) {
            InventoryMove::create([
                'operation_id'            => $operation->id,
                'name'                    => $operation->name,
                'reference'               => $operation->name,
                'description_picking'     => $line->product->picking_description ?? $line->name,
                'state'                   => MoveState::DRAFT,
                'scheduled_at'            => $move->date ?? now(),
                'deadline'                => $move->date ?? now(),
                'reservation_date'        => now(),
                'product_id'              => $line->product_id,
                'product_qty'             => $line->quantity,
                'product_uom_qty'         => $line->quantity,
                'quantity'                => $line->quantity,
                'uom_id'                  => $line->uom_id ?? $line->product->uom_id,
                'partner_id'              => $operation->partner_id,
                'warehouse_id'            => $operation->destinationLocation?->warehouse_id,
                'source_location_id'      => $operation->source_location_id,
                'destination_location_id' => $operation->destination_location_id,
                'operation_type_id'       => $operation->operation_type_id,
                'final_location_id'       => $operation->destinationLocation?->id,
                'company_id'              => $operation->company_id,
            ]);
        }

        $operation->refresh();

        Inventory::computeTransfer($operation);

        $move->addMessage([
            'body' => "A receipt <strong>{$operation->name}</strong> has been created automatically for the goods in this bill.",
            'type' => 'comment',
        ]);
    }

    protected function getOperationType(Move $move): ?OperationType
    {
        $operationType = OperationType::where('type', InventoryOperationType::INCOMING)
            ->whereHas('warehouse', function ($query) use ($move) {
                $query->where('company_id', $move->company_id);
            })
            ->first();

        if (! $operationType) {
            $operationType = OperationType::where('type', InventoryOperationType::INCOMING)
                ->whereDoesntHave('warehouse')
                ->first();
        }

        return $operationType;
    }
}

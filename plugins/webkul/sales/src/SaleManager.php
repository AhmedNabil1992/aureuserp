<?php

namespace Webkul\Sale;

use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Webkul\Account\Enums as AccountEnums;
use Webkul\Account\Enums\InvoicePolicy;
use Webkul\Account\Facades\Account as AccountFacade;
use Webkul\Account\Facades\Tax;
use Webkul\Account\Models\Move as AccountMove;
use Webkul\Inventory\Enums as InventoryEnums;
use Webkul\Inventory\Facades\Inventory as InventoryFacade;
use Webkul\Inventory\Models\Location;
use Webkul\Inventory\Models\Operation as InventoryOperation;
use Webkul\Inventory\Models\Product as InventoryProduct;
use Webkul\Inventory\Models\Warehouse;
use Webkul\Partner\Models\Partner;
use Webkul\PluginManager\Package;
use Webkul\Product\Enums\ProductType;
use Webkul\Product\Models\BillOfMaterial;
use Webkul\Sale\Enums\AdvancedPayment;
use Webkul\Sale\Enums\InvoiceStatus;
use Webkul\Sale\Enums\OrderDeliveryStatus;
use Webkul\Sale\Enums\OrderState;
use Webkul\Sale\Enums\QtyDeliveredMethod;
use Webkul\Sale\Events\OrderCanceled;
use Webkul\Sale\Events\OrderConfirmed;
use Webkul\Sale\Events\OrderDrafted;
use Webkul\Sale\Events\OrderLocked;
use Webkul\Sale\Events\OrderUnlocked;
use Webkul\Sale\Mail\SaleOrderCancelQuotation;
use Webkul\Sale\Mail\SaleOrderQuotation;
use Webkul\Sale\Models\AdvancedPaymentInvoice;
use Webkul\Sale\Models\Order;
use Webkul\Sale\Models\OrderLine;
use Webkul\Sale\Settings\InvoiceSettings;
use Webkul\Sale\Settings\QuotationAndOrderSettings;
use Webkul\Support\Services\EmailService;

class SaleManager
{
    public function __construct(
        protected QuotationAndOrderSettings $quotationAndOrderSettings,
        protected InvoiceSettings $invoiceSettings,
    ) {}

    public function sendQuotationOrOrderByEmail(Order $record, array $data = []): array
    {
        $result = $this->sendByEmail($record, $data);

        if (! empty($result['sent'])) {
            $record = $this->computeSaleOrder($record);
        }

        return $result;
    }

    public function lockAndUnlock(Order $record): Order
    {
        $record->update(['locked' => ! $record->locked]);

        $record = $this->computeSaleOrder($record);

        if ($record->locked) {
            OrderLocked::dispatch($record);
        } else {
            OrderUnlocked::dispatch($record);
        }

        return $record;
    }

    public function confirmSaleOrder(Order $record): Order
    {
        $record->update([
            'state'          => OrderState::SALE,
            'invoice_status' => InvoiceStatus::TO_INVOICE,
        ]);

            $this->consumeBillOfMaterials($record);

        $record->update([
            'locked' => $this->quotationAndOrderSettings->enable_lock_confirm_sales,
        ]);

        $record = $this->computeSaleOrder($record);

            $record = $this->computeSaleOrder($record->refresh());

            OrderConfirmed::dispatch($record);

            return $record;
        ;
    }

    public function backToQuotation(Order $record): Order
    {
        $record->update([
            'state'          => OrderState::DRAFT,
            'invoice_status' => InvoiceStatus::NO,
        ]);

        $record = $this->computeSaleOrder($record);

        OrderDrafted::dispatch($record);

        return $record;
    }

    public function cancelSaleOrder(Order $record, array $data = []): Order
    {
        $record->update([
            'state'          => OrderState::CANCEL,
            'invoice_status' => InvoiceStatus::NO,
        ]);

        if (! empty($data)) {
            $this->cancelAndSendEmail($record, $data);
        }

        $record = $this->computeSaleOrder($record);

        $this->cancelInventoryOperation($record);

        OrderCanceled::dispatch($record);

        return $record;
    }

    public function createInvoice(Order $record, array $data = [])
    {
        if ($data['advance_payment_method'] == AdvancedPayment::DELIVERED->value) {
            $this->createAccountMove($record);
        }

        $advancedPaymentInvoice = AdvancedPaymentInvoice::create([
            ...$data,
            'currency_id'          => $record->currency_id,
            'company_id'           => $record->company_id,
            'creator_id'           => Auth::id(),
            'deduct_down_payments' => true,
            'consolidated_billing' => true,
        ]);

        $advancedPaymentInvoice->orders()->attach($record->id);

        return $this->computeSaleOrder($record);
    }

    /**
     * Compute the sale order.
     */
    public function computeSaleOrder(Order $record): Order
    {
        $record->amount_untaxed = 0;
        $record->amount_tax = 0;
        $record->amount_total = 0;

        foreach ($record->lines as $line) {
            $line->state = $record->state;
            $line->salesman_id = $record->user_id;
            $line->order_partner_id = $record->partner_id;
            $line->invoice_status = $record->invoice_status;

            $line = $this->computeSaleOrderLine($line);

            $record->amount_untaxed += $line->price_subtotal;
            $record->amount_tax += $line->price_tax;
            $record->amount_total += $line->price_total;
        }

        $record = $this->computeDeliveryStatus($record);

        $record = $this->computeInvoiceStatus($record);

        $record->save();

        $record->refresh();

        return $record;
    }

    /**
     * Compute the sale order line.
     */
    public function computeSaleOrderLine(OrderLine $line): OrderLine
    {
        $line = $this->computeQtyInvoiced($line);

        $line = $this->computeQtyDelivered($line);

        $line = $this->computeQtyToInvoice($line);

        $priceUnit = $line->discount > 0
            ? $line->price_unit * (1 - ($line->discount / 100))
            : $line->price_unit;

        if ($line->taxes->isEmpty()) {
            $subTotal = $priceUnit * $line->product_qty;

            $line->price_subtotal = round($subTotal, 4);

            $line->price_tax = 0;

            $line->price_total = round($subTotal, 4);
        } else {
            $taxResult = Tax::computeAll(
                $line->taxes,
                $priceUnit,
                $line->order->currency,
                $line->product_qty,
                $line->product,
                $line->order->partner,
            );

            $line->price_subtotal = round($taxResult['total_excluded'], 4);

            $line->price_tax = round($taxResult['total_included'] - $taxResult['total_excluded'], 4);

            $line->price_total = round($taxResult['total_included'], 4);
        }

        $line->sort = $line->sort ?? OrderLine::max('sort') + 1;

        $line->technical_price_unit = $line->price_unit;

        $line->price_reduce_taxexcl = $line->product_uom_qty ? round($line->price_subtotal / $line->product_uom_qty, 4) : 0.0;

        $line->price_reduce_taxinc = $line->product_uom_qty ? round($line->price_total / $line->product_uom_qty, 4) : 0.0;

        $line->state = $line->order->state;

        $line = $this->computeOrderLineDeliveryMethod($line);

        $line = $this->computeOrderLineInvoiceStatus($line);

        $line = $this->computeQtyInvoiced($line);

        $line = $this->computeOrderLineUntaxedAmountToInvoice($line);

        $line = $this->untaxedOrderLineAmountToInvoiced($line);

        $line->save();

        return $line;
    }

    public function computeQtyInvoiced(OrderLine $line): OrderLine
    {
        $qtyInvoiced = 0.000;

        foreach ($line->accountMoveLines as $accountMoveLine) {
            if (
                $accountMoveLine->move->state !== AccountEnums\MoveState::CANCEL
                || $accountMoveLine->move->payment_state === AccountEnums\PaymentState::INVOICING_LEGACY->value
            ) {
                $convertedQty = $accountMoveLine->uom->computeQuantity($accountMoveLine->quantity, $line->uom);

                if ($accountMoveLine->move->move_type === AccountEnums\MoveType::OUT_INVOICE) {
                    $qtyInvoiced += $convertedQty;
                } elseif ($accountMoveLine->move->move_type === AccountEnums\MoveType::OUT_REFUND) {
                    $qtyInvoiced -= $convertedQty;
                }
            }
        }

        $line->qty_invoiced = $qtyInvoiced;

        return $line;
    }

    public function computeQtyDelivered(OrderLine $line): OrderLine
    {
        if ($line->qty_delivered_method == QtyDeliveredMethod::MANUAL) {
            $line->qty_delivered = $line->qty_delivered ?? 0.0;
        }

        if ($line->qty_delivered_method == QtyDeliveredMethod::STOCK_MOVE) {
            $qty = 0.0;

            [$outgoingMoves, $incomingMoves] = $this->getOutgoingIncomingMoves($line);

            foreach ($outgoingMoves as $move) {
                if ($move->state != InventoryEnums\MoveState::DONE) {
                    continue;
                }

                $qty += $move->uom->computeQuantity($move->quantity, $line->uom, true, 'HALF-UP');
            }

            foreach ($incomingMoves as $move) {
                if ($move->state != InventoryEnums\MoveState::DONE) {
                    continue;
                }

                $qty -= $move->uom->computeQuantity($move->quantity, $line->uom, true, 'HALF-UP');
            }

            $line->qty_delivered = $qty;
        }

        return $line;
    }

    public function computeQtyToInvoice(OrderLine $line): OrderLine
    {
        $policy = $line->product?->invoice_policy ?? $line->product?->parent?->invoice_policy ?? $this->invoiceSettings->invoice_policy->value;

        if ($line->state == OrderState::SALE && ! $line->display_type) {
            if ($policy === InvoicePolicy::ORDER->value) {
                $line->qty_to_invoice = $line->product_uom_qty - $line->qty_invoiced;
            } else {
                $line->qty_to_invoice = $line->qty_delivered - $line->qty_invoiced;
            }
        } else {
            $line->qty_to_invoice = 0.0;
        }

        return $line;
    }

    public function computeDeliveryStatus(Order $order): Order
    {
        if (! Package::isPluginInstalled('inventories')) {
            $order->delivery_status = OrderDeliveryStatus::NO;

            return $order;
        }

        if ($order->operations->isEmpty() || $order->operations->every(function ($receipt) {
            return $receipt->state == InventoryEnums\OperationState::CANCELED;
        })) {
            $order->delivery_status = OrderDeliveryStatus::NO;
        } elseif ($order->operations->every(function ($receipt) {
            return in_array($receipt->state, [InventoryEnums\OperationState::DONE, InventoryEnums\OperationState::CANCELED]);
        })) {
            $order->delivery_status = OrderDeliveryStatus::FULL;
        } elseif (
            $order->operations->contains(fn ($receipt) => $receipt->state == InventoryEnums\OperationState::DONE)
            && $order->lines->contains(fn ($line) => (float) $line->qty_delivered > 0)
        ) {
            $order->delivery_status = OrderDeliveryStatus::PARTIAL;
        } elseif ($order->operations->contains(fn ($receipt) => $receipt->state == InventoryEnums\OperationState::DONE)) {
            $order->delivery_status = OrderDeliveryStatus::STARTED;
        } else {
            $order->delivery_status = OrderDeliveryStatus::PENDING;
        }

        return $order;
    }

    public function computeInvoiceStatus(Order $order): Order
    {
        if ($order->state != OrderState::SALE) {
            $order->invoice_status = InvoiceStatus::NO;

            return $order;
        }

        if ($order->lines->contains(function ($line) {
            return $line->invoice_status == InvoiceStatus::TO_INVOICE;
        })) {
            $order->invoice_status = InvoiceStatus::TO_INVOICE;
        } elseif ($order->lines->contains(function ($line) {
            return $line->invoice_status == InvoiceStatus::INVOICED;
        })) {
            $order->invoice_status = InvoiceStatus::INVOICED;
        } elseif ($order->lines->contains(function ($line) {
            return in_array($line->invoice_status, [InvoiceStatus::INVOICED, InvoiceStatus::UP_SELLING]);
        })) {
            $order->invoice_status = InvoiceStatus::UP_SELLING;
        } else {
            $order->invoice_status = InvoiceStatus::NO;
        }

        return $order;
    }

    public function computeOrderLineDeliveryMethod(OrderLine $line): OrderLine
    {
        if ($line->qty_delivered_method) {
            return $line;
        }

        if ($line->is_expense) {
            $line->qty_delivered_method = 'analytic';
        } else {
            $line->qty_delivered_method ??= 'stock_move';
        }

        return $line;
    }

    public function computeOrderLineInvoiceStatus(OrderLine $line): OrderLine
    {
        if ($line->state !== OrderState::SALE) {
            $line->invoice_status = InvoiceStatus::NO;

            return $line;
        }

        $policy = $line->product?->invoice_policy ?? $line->product?->parent?->invoice_policy ?? $this->invoiceSettings->invoice_policy->value;

        if (
            $line->is_downpayment
            && $line->untaxed_amount_to_invoice == 0
        ) {
            $line->invoice_status = InvoiceStatus::INVOICED;
        } elseif ($policy === InvoicePolicy::ORDER->value) {
            if ($line->qty_invoiced >= $line->product_uom_qty) {
                $line->invoice_status = InvoiceStatus::INVOICED;
            } elseif ($line->qty_delivered > $line->product_uom_qty) {
                $line->invoice_status = InvoiceStatus::UP_SELLING;
            } else {
                $line->invoice_status = InvoiceStatus::TO_INVOICE;
            }
        } elseif ($policy === InvoicePolicy::DELIVERY->value) {
            if ($line->qty_invoiced >= $line->product_uom_qty) {
                $line->invoice_status = InvoiceStatus::INVOICED;
            } elseif ($line->qty_to_invoice != 0 || $line->qty_delivered == $line->product_uom_qty) {
                $line->invoice_status = InvoiceStatus::TO_INVOICE;
            } else {
                $line->invoice_status = InvoiceStatus::NO;
            }
        } else {
            $line->invoice_status = InvoiceStatus::NO;
        }

        return $line;
    }

    public function computeOrderLineUntaxedAmountToInvoice(OrderLine $line): OrderLine
    {
        if ($line->state !== OrderState::SALE) {
            $line->untaxed_amount_to_invoice = 0;

            return $line;
        }

        $priceSubtotal = 0;

        if ($line->product->invoice_policy === InvoicePolicy::DELIVERY->value) {
            $uomQtyToConsider = $line->qty_delivered;
        } else {
            $uomQtyToConsider = $line->product_uom_qty;
        }

        $discount = $line->discount ?? 0.0;
        $priceReduce = $line->price_unit * (1 - ($discount / 100.0));
        $priceSubtotal = $priceReduce * $uomQtyToConsider;

        $line->untaxed_amount_to_invoice = $priceSubtotal - $line->untaxed_amount_invoiced;

        return $line;
    }

    public function untaxedOrderLineAmountToInvoiced(OrderLine $line): OrderLine
    {
        $amountInvoiced = 0.0;

        foreach ($line->accountMoveLines as $accountMoveLine) {
            if (
                $accountMoveLine->move->state === AccountEnums\MoveState::POSTED
                || $accountMoveLine->move->payment_state === AccountEnums\PaymentState::INVOICING_LEGACY
            ) {
                if ($accountMoveLine->move->move_type === AccountEnums\MoveType::OUT_INVOICE) {
                    $amountInvoiced += $line->price_subtotal;
                } elseif ($accountMoveLine->move->move_type === AccountEnums\MoveType::OUT_REFUND) {
                    $amountInvoiced -= $line->price_subtotal;
                }
            }
        }

        $line->untaxed_amount_invoiced = $amountInvoiced;

        return $line;
    }

    public function sendByEmail(Order $record, array $data): array
    {
        $partners = Partner::whereIn('id', $data['partners'])->get();

        $sent = [];
        $failed = [];

        foreach ($partners as $partner) {
            if (empty($partner->email)) {
                $failed[$partner->name] = 'No email address';

                continue;
            }

            try {
                $payload = [
                    'record_name'    => $record->name,
                    'model_name'     => $record->state->getLabel(),
                    'subject'        => $data['subject'],
                    'description'    => $data['description'],
                    'to'             => [
                        'address' => $partner->email,
                        'name'    => $partner->name,
                    ],
                ];

                app(EmailService::class)->send(
                    mailClass: SaleOrderQuotation::class,
                    view: $viewName = 'sales::mails.sale-order-quotation',
                    payload: $payload,
                    attachments: [
                        [
                            'path' => $data['file'],
                            'name' => basename($data['file']),
                        ],
                    ]
                );

                $message = $record->addMessage([
                    'from' => [
                        'company' => Auth::user()->defaultCompany->toArray(),
                    ],
                    'body' => view($viewName, compact('payload'))->render(),
                    'type' => 'comment',
                ]);

                $record->addAttachments(
                    [$data['file']],
                    ['message_id' => $message->id],
                );

                $sent[] = $partner->name;
            } catch (Exception $e) {
                $failed[$partner->name] = 'Email service error: '.$e->getMessage();
            }
        }

        if (! empty($sent) && $record->state === OrderState::DRAFT) {
            $record->state = OrderState::SENT;
            $record->save();
        }

        return [
            'sent'   => $sent,
            'failed' => $failed,
        ];
    }

    public function cancelAndSendEmail(Order $record, array $data)
    {
        $partners = Partner::whereIn('id', $data['partners'])->get();

        foreach ($partners as $partner) {
            $payload = [
                'record_name'    => $record->name,
                'model_name'     => 'Quotation',
                'subject'        => $data['subject'],
                'description'    => $data['description'],
                'to'             => [
                    'address' => $partner?->email,
                    'name'    => $partner?->name,
                ],
            ];

            app(EmailService::class)->send(
                mailClass: SaleOrderCancelQuotation::class,
                view: $viewName = 'sales::mails.sale-order-cancel-quotation',
                payload: $payload,
            );

            $record->addMessage([
                'from' => [
                    'company' => Auth::user()->defaultCompany->toArray(),
                ],
                'body' => view($viewName, compact('payload'))->render(),
                'type' => 'comment',
            ]);
        }
    }

    private function createAccountMove(Order $record): AccountMove
    {
        $accountMove = AccountMove::create([
            'move_type'               => AccountEnums\MoveType::OUT_INVOICE,
            'invoice_origin'          => $record->name,
            'date'                    => now(),
            'company_id'              => $record->company_id,
            'currency_id'             => $record->currency_id,
            'invoice_payment_term_id' => $record->payment_term_id,
            'partner_id'              => $record->partner_id,
            'fiscal_position_id'      => $record->fiscal_position_id,
        ]);

        $record->accountMoves()->attach($accountMove->id);

        foreach ($record->lines as $line) {
            $this->createAccountMoveLine($accountMove, $line);
        }

        $accountMove = AccountFacade::computeAccountMove($accountMove);

        return $accountMove;
    }

    private function createAccountMoveLine(AccountMove $accountMove, OrderLine $orderLine): void
    {
        $productInvoicePolicy = $orderLine->product?->invoice_policy;
        $invoiceSetting = $this->invoiceSettings->invoice_policy->value;

        $quantity = ($productInvoicePolicy ?? $invoiceSetting) === InvoicePolicy::ORDER->value
            ? $orderLine->product_uom_qty
            : $orderLine->qty_to_invoice;

        $accountMoveLine = $accountMove->lines()->create([
            'name'         => $orderLine->name,
            'date'         => $accountMove->date,
            'creator_id'   => $accountMove?->creator_id,
            'parent_state' => $accountMove->state,
            'quantity'     => $quantity,
            'price_unit'   => $orderLine->price_unit,
            'discount'     => $orderLine->discount,
            'currency_id'  => $accountMove->currency_id,
            'product_id'   => $orderLine->product_id,
            'uom_id'       => $orderLine->product_uom_id,
        ]);

        $orderLine->accountMoveLines()->sync($accountMoveLine->id);

        $accountMoveLine->taxes()->sync($orderLine->taxes->pluck('id'));
    }

    public function applyInventoryRules($lines, $previousProductUOMQty = false): void
    {
        if (! Package::isPluginInstalled('inventories')) {
            return;
        }

        $procurements = collect();

        foreach ($lines as $line) {
            $line->refresh();

            if (
                $line->state !== OrderState::SALE
                || $line->order->locked
                || $line->product?->type !== ProductType::GOODS
            ) {
                continue;
            }

            $qty = $this->getQtyProcurement($line, $previousProductUOMQty);

            if (float_compare($qty, $line->product_qty, precisionDigits: 2) == 0) {
                continue;
            }

            $procurementGroup = $line->order->procurementGroup;

            if (! $procurementGroup) {
                $procurementGroup = $line->order->procurementGroup()->create([
                    'name'          => $line->order->name,
                    'move_type'     => $line->order->picking_policy,
                    'partner_id'    => $line->order->partner_shipping_id,
                    'sale_order_id' => $line->order->id,
                ]);

                $line->order->procurement_group_id = $procurementGroup->id;
                $line->order->save();
            } else {
                if ($procurementGroup->partner_id !== $line->order->partner_shipping_id) {
                    $procurementGroup->update([
                        'partner_id' => $line->order->partner_shipping_id,
                    ]);
                }

                if ($procurementGroup->move_type !== $line->order->picking_policy) {
                    $procurementGroup->update([
                        'move_type' => $line->order->picking_policy,
                    ]);
                }
            }

            $values = $this->prepareProcurementValues($line, $procurementGroup);

            $productQty = $line->product_qty - $qty;

            $origin = $line->order->client_order_ref
                ? "{$line->order->name} - {$line->order->client_order_ref}"
                : $line->order->name;

            [$productQty, $procurementUom] = $line->uom->adjustUomQuantities($productQty, $line->product->uom);

            $procurements->push($this->createProcurements($line, $productQty, $procurementUom, $origin, $values));
        }

        InventoryFacade::runProcurements($procurements);
    }

    public function getOutgoingIncomingMoves(OrderLine $orderLine, bool $strict = true)
    {
        $outgoingMoveIds = [];

        $incomingMoveIds = [];

        $moves = $orderLine->inventoryMoves->filter(function ($inventoryMove) use ($orderLine) {
            return $inventoryMove->state != InventoryEnums\MoveState::CANCELED
                && ! $inventoryMove->is_scraped
                && $orderLine->product_id == $inventoryMove->product_id;
        });

        $triggeringRuleIds = [];

        if ($moves->isNotEmpty() && ! $strict) {
            $sortedMoves = $moves->sortBy('id');

            $seenWarehouseIds = [];

            foreach ($sortedMoves as $move) {
                if (! in_array($move->warehouse->id, $seenWarehouseIds)) {
                    $triggeringRuleIds[] = $move->rule_id;

                    $seenWarehouseIds[] = $move->warehouse_id;
                }
            }
        }

        foreach ($moves as $move) {
            $isOutgoingStrict = $strict && $move->destinationLocation->type == InventoryEnums\LocationType::CUSTOMER;

            $isOutgoingNonStrict = ! $strict
                && in_array($move->rule_id, $triggeringRuleIds)
                && ($move->finalLocation?->type ?? $move->destinationLocation->type) == InventoryEnums\LocationType::CUSTOMER;

            if ($isOutgoingStrict || $isOutgoingNonStrict) {
                if (
                    ! $move->origin_returned_move_id
                    || (
                        $move->origin_returned_move_id
                        && $move->is_refund
                    )
                ) {
                    $outgoingMoveIds[] = $move->id;
                }
            } elseif ($move->sourceLocation->type == InventoryEnums\LocationType::CUSTOMER && $move->is_refund) {
                $incomingMoveIds[] = $move->id;
            }
        }

        return [
            $moves->whereIn('id', $outgoingMoveIds),
            $moves->whereIn('id', $incomingMoveIds),
        ];
    }

    public function getQtyProcurement(OrderLine $line, $previousProductUOMQty = false)
    {
        $qty = 0.0;

        [$outgoingMoves, $incomingMoves] = $this->getOutgoingIncomingMoves($line, strict: false);

        foreach ($outgoingMoves as $move) {
            $qtyToCompute = $move->state === InventoryEnums\MoveState::DONE ? $move->quantity : $move->product_uom_qty;

            $qty += $move->uom->computeQuantity($qtyToCompute, $line->uom, roundingMethod: 'HALF-UP');
        }

        foreach ($incomingMoves as $move) {
            $qtyToCompute = $move->state === InventoryEnums\MoveState::DONE ? $move->quantity : $move->product_uom_qty;

            $qty -= $move->uom->computeQuantity($qtyToCompute, $line->uom, roundingMethod: 'HALF-UP');
        }

        return $qty;
    }

    public function prepareProcurementValues(OrderLine $line, $procurementGroup = null): array
    {
        $location = Location::where('type', InventoryEnums\LocationType::CUSTOMER)->first();

        $deadline = $line->order->commitment_date ?? $line->expected_date;

        // TODO: This value will be set in the configuration
        $datePlanned = $deadline->subDays(0);

        return [
            'procurement_group'  => $procurementGroup,
            'sale_order_line_id' => $line->id,
            'scheduled_at'       => $datePlanned,
            'planned'            => $datePlanned,
            'deadline'           => $deadline,
            'routes'             => $line->route ? collect([$line->route]) : collect(),
            'warehouse'          => $line->warehouse,
            'partner'            => $line->order->partner,
            'final_location'     => $location,
            'company'            => $line->company,
            'product_packaging'  => $line->productPackaging,
        ];
    }

    public function createProcurements(OrderLine $line, $productQty, $procurementUom, $origin, $values)
    {
        $product = InventoryProduct::find($line->product_id);

        return [
            'product'     => $product,
            'product_qty' => $productQty,
            'product_uom' => $procurementUom,
            'location'    => $values['final_location'],
            'name'        => $line->product->name,
            'origin'      => $origin,
            'company'     => $line->company,
            'values'      => $values,
        ];
    }

    protected function consumeBillOfMaterials(Order $record): void
    {
        if (! Package::isPluginInstalled('inventories')) {
            return;
        }

        $warehouse = $record->warehouse;

        if (! $warehouse) {
            throw new Exception('No warehouse has been resolved for this order.');
        }

        if (! $warehouse->internal_type_id || ! $warehouse->lot_stock_location_id) {
            throw new Exception("Warehouse '{$warehouse->name}' is missing the internal operation type or stock location required for BOM consumption.");
        }

        $productionLocation = $this->resolveProductionLocation($record);

        foreach ($record->lines as $line) {
            if ($line->product?->type !== ProductType::PRODUCT) {
                continue;
            }

            $billOfMaterial = $this->resolveBillOfMaterial($line);

            if (! $billOfMaterial || $billOfMaterial->lines->isEmpty()) {
                continue;
            }

            if ((float) $billOfMaterial->quantity <= 0) {
                throw new Exception("Bill of materials '{$billOfMaterial->reference}' must have a quantity greater than zero.");
            }

            $sourceLocation = $this->resolveBomSourceLocation($record, $warehouse, $billOfMaterial);

            $operation = InventoryOperation::create([
                'state'                   => InventoryEnums\OperationState::DRAFT,
                'origin'                  => $record->name.' / BOM / '.$line->name,
                'partner_id'              => $record->partner_id,
                'operation_type_id'       => $warehouse->internal_type_id,
                'source_location_id'      => $sourceLocation->id,
                'destination_location_id' => $productionLocation->id,
                'scheduled_at'            => now(),
                'company_id'              => $record->company_id,
                'sale_order_id'           => $record->id,
                'user_id'                 => Auth::id(),
                'creator_id'              => Auth::id(),
            ]);

            $producedQuantity = $line->uom->computeQuantity(
                $line->product_uom_qty,
                $billOfMaterial->uom ?? $line->product->uom,
                true,
                'HALF-UP'
            );

            $scale = $producedQuantity / (float) $billOfMaterial->quantity;

            foreach ($billOfMaterial->lines as $billOfMaterialLine) {
                $component = $billOfMaterialLine->component;

                if (! $component) {
                    throw new Exception('A BOM line references a missing component product.');
                }

                $moveUom = $billOfMaterialLine->uom ?? $component->uom;

                if (! $moveUom || ! $component->uom) {
                    throw new Exception("Component '{$component->name}' is missing a unit of measure required for BOM consumption.");
                }

                $moveUomQuantity = round((float) $billOfMaterialLine->quantity * $scale, 4);

                if ($moveUomQuantity <= 0) {
                    continue;
                }

                $productQuantity = $moveUom->computeQuantity($moveUomQuantity, $component->uom, true, 'HALF-UP');

                $operation->moves()->create([
                    'name'                    => $component->name,
                    'reference'               => $operation->name,
                    'state'                   => InventoryEnums\MoveState::DRAFT,
                    'product_id'              => $component->id,
                    'product_qty'             => $productQuantity,
                    'product_uom_qty'         => $moveUomQuantity,
                    'quantity'                => $moveUomQuantity,
                    'uom_id'                  => $moveUom->id,
                    'origin'                  => $record->name,
                    'scheduled_at'            => $operation->scheduled_at,
                    'source_location_id'      => $sourceLocation->id,
                    'destination_location_id' => $productionLocation->id,
                    'final_location_id'       => $productionLocation->id,
                    'company_id'              => $record->company_id,
                    'operation_type_id'       => $warehouse->internal_type_id,
                    'warehouse_id'            => $warehouse->id,
                    'procure_method'          => InventoryEnums\ProcureMethod::MAKE_TO_STOCK,
                    'sale_order_line_id'      => $line->id,
                ]);
            }

            $operation->refresh();
            $operation = InventoryFacade::computeTransfer($operation);

            foreach ($operation->moves as $move) {
                if (
                    $move->state !== InventoryEnums\MoveState::ASSIGNED
                    || abs((float) $move->quantity - (float) $move->product_uom_qty) > 0.0001
                ) {
                    throw new Exception("Not enough stock to consume BOM components for '{$line->name}'.");
                }
            }

            InventoryFacade::validateTransfer($operation);
        }
    }

    protected function resolveBillOfMaterial(OrderLine $line): ?BillOfMaterial
    {
        return $line->product?->billOfMaterials()
            ->with(['uom', 'lines.component.uom', 'lines.uom'])
            ->where(function ($query) use ($line): void {
                $query->where('company_id', $line->company_id)
                    ->orWhereNull('company_id');
            })
            ->orderByRaw('CASE WHEN company_id = ? THEN 0 WHEN company_id IS NULL THEN 1 ELSE 2 END', [$line->company_id])
            ->orderBy('id')
            ->first();
    }

    protected function resolveProductionLocation(Order $record): Location
    {
        $location = Location::query()
            ->where('type', InventoryEnums\LocationType::PRODUCTION)
            ->where(function ($query) use ($record): void {
                $query->where('company_id', $record->company_id)
                    ->orWhereNull('company_id');
            })
            ->orderByRaw('CASE WHEN company_id = ? THEN 0 ELSE 1 END', [$record->company_id])
            ->first();

        if (! $location) {
            throw new Exception('No production location is configured for BOM consumption.');
        }

        return $location;
    }

    protected function resolveBomSourceLocation(Order $record, Warehouse $warehouse, BillOfMaterial $billOfMaterial): Location
    {
        if ($billOfMaterial->source_location_id) {
            $location = Location::query()->find($billOfMaterial->source_location_id);

            if (! $location) {
                throw new Exception('The selected BOM source location no longer exists.');
            }

            if (
                $location->type !== InventoryEnums\LocationType::INTERNAL
                || $location->is_scrap
                || ($location->company_id && $location->company_id !== $record->company_id)
            ) {
                throw new Exception('The selected BOM source location is not a valid internal location for this order company.');
            }

            return $location;
        }

        $location = Location::query()->find($warehouse->lot_stock_location_id);

        if (! $location) {
            throw new Exception("Warehouse '{$warehouse->name}' has no valid stock location for BOM consumption.");
        }

        return $location;
    }

    protected function cancelInventoryOperation(Order $record): void
    {
        if (! Package::isPluginInstalled('inventories')) {
            return;
        }

        if ($record->operations->isEmpty()) {
            return;
        }

        $record->operations
            ->filter(fn ($operation) => ! in_array($operation->state, [
                InventoryEnums\OperationState::DONE,
                InventoryEnums\OperationState::CANCELED,
            ]))
            ->each(fn ($operation) => InventoryFacade::cancelTransfer($operation));
    }
}

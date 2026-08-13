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
use Webkul\Account\Models\Move as AccountMove;
use Webkul\Sale\Models\Order;
use Webkul\Sale\Models\OrderLine;
use Webkul\Sale\Services\Invoicer;
use Webkul\Sale\Services\OrderCalculator;
use Webkul\Sale\Services\OrderMailer;
use Webkul\Sale\Services\OrderWorkflow;
use Webkul\Sale\Services\ProcurementRequester;

class SaleManager
{
    public function __construct(
        protected OrderWorkflow $workflow,
        protected OrderCalculator $calculator,
        protected Invoicer $invoicer,
        protected ProcurementRequester $procurement,
        protected OrderMailer $mailer,
    ) {}

    public function sendQuotationOrOrderByEmail(Order $record, array $data = []): array
    {
        return $this->workflow->sendByEmail($record, $data);
    }

    public function lockAndUnlock(Order $record): Order
    {
        return $this->workflow->toggleLock($record);
    }

    public function confirmSaleOrder(Order $record): Order
    {
        return $this->workflow->confirm($record);
    }

    public function backToQuotation(Order $record): Order
    {
        return $this->workflow->backToQuotation($record);
    }

    public function cancelSaleOrder(Order $record, array $data = []): Order
    {
        return $this->workflow->cancel($record, $data);
    }

    public function createInvoice(Order $record, array $data = []): Order
    {
        $this->invoicer->invoiceOrder($record, $data);

        return $this->calculator->recompute($record);
    }

    public function computeSaleOrder(Order $record): Order
    {
        return $this->calculator->recompute($record);
    }

    public function computeSaleOrderLine(OrderLine $line): OrderLine
    {
        return $this->calculator->recomputeLine($line);
    }

    public function applyInventoryRules($lines, $previousProductUOMQty = false): void
    {
        $this->procurement->requestForLines($lines, $previousProductUOMQty);
    }

    public function outgoingAndIncomingMoves(OrderLine $orderLine, bool $strict = true): array
    {
        return $this->procurement->outgoingAndIncomingMoves($orderLine, $strict);
    }

    public function procuredQuantity(OrderLine $line, $previousProductUOMQty = false)
    {
        return $this->procurement->procuredQuantity($line, $previousProductUOMQty);
    }

    public function createAccountMove(Order $record): AccountMove
    {
        return $this->invoicer->createInvoice($record);
    }

    public function sendByEmail(Order $record, array $data): array
    {
        return $this->mailer->sendQuotation($record, $data);
    }

    public function cancelAndSendEmail(Order $record, array $data): void
    {
        $this->mailer->sendCancellation($record, $data);
    }
}

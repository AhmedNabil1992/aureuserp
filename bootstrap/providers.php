<?php

use App\Providers\AppServiceProvider;
use App\Providers\Filament\AdminPanelProvider;
use App\Providers\Filament\CustomerPanelProvider;
use App\Providers\Filament\WebsitePanelProvider;
use Webkul\Account\AccountServiceProvider;
use Webkul\Accounting\AccountingServiceProvider;
use Webkul\Analytic\AnalyticServiceProvider;
use Webkul\Barcode\BarcodeServiceProvider;
use Webkul\Blog\BlogServiceProvider;
use Webkul\Chatter\ChatterServiceProvider;
use Webkul\Contact\ContactServiceProvider;
use Webkul\Employee\EmployeeServiceProvider;
use Webkul\Field\FieldServiceProvider;
use Webkul\FullCalendar\FullCalendarServiceProvider;
use Webkul\Inventory\InventoryServiceProvider;
use Webkul\Invoice\InvoiceServiceProvider;
use Webkul\Maintenance\MaintenanceServiceProvider;
use Webkul\Manufacturing\ManufacturingServiceProvider;
use Webkul\Marketing\MarketingServiceProvider;
use Webkul\Partner\PartnerServiceProvider;
use Webkul\Payment\PaymentServiceProvider;
use Webkul\PluginManager\PluginManagerServiceProvider;
use Webkul\Product\ProductServiceProvider;
use Webkul\Project\ProjectServiceProvider;
use Webkul\Purchase\PurchaseServiceProvider;
use Webkul\Recruitment\RecruitmentServiceProvider;
use Webkul\Sale\SaleServiceProvider;
use Webkul\Security\SecurityServiceProvider;
use Webkul\Software\SoftwareServiceProvider;
use Webkul\Support\SupportServiceProvider;
use Webkul\TableViews\TableViewsServiceProvider;
use Webkul\TimeOff\TimeOffServiceProvider;
use Webkul\Timesheet\TimesheetServiceProvider;
use Webkul\Website\WebsiteServiceProvider;
use Webkul\Wifi\WifiServiceProvider;
use Webkul\Psmonitor\PsmonitorServiceProvider;
use Webkul\Article\ArticleServiceProvider;
use Webkul\Lead\LeadServiceProvider;

// use App\Providers\AppServiceProvider;
// use App\Providers\Filament\AdminPanelProvider;
// use App\Providers\Filament\CustomerPanelProvider;
// use Webkul\Account\AccountServiceProvider;
// use Webkul\Accounting\AccountingServiceProvider;
// use Webkul\Analytic\AnalyticServiceProvider;
// use Webkul\Blog\BlogServiceProvider;
// use Webkul\Chatter\ChatterServiceProvider;
// use Webkul\Contact\ContactServiceProvider;
// use Webkul\Employee\EmployeeServiceProvider;
// use Webkul\Field\FieldServiceProvider;
// use Webkul\FullCalendar\FullCalendarServiceProvider;
// use Webkul\Inventory\InventoryServiceProvider;
// use Webkul\Invoice\InvoiceServiceProvider;
// use Webkul\Manufacturing\ManufacturingServiceProvider;
// use Webkul\Partner\PartnerServiceProvider;
// use Webkul\Payment\PaymentServiceProvider;
// use Webkul\PluginManager\PluginManagerServiceProvider;
// use Webkul\Product\ProductServiceProvider;
// use Webkul\Project\ProjectServiceProvider;
// use Webkul\Purchase\PurchaseServiceProvider;
// use Webkul\Recruitment\RecruitmentServiceProvider;
// use Webkul\Sale\SaleServiceProvider;
// use Webkul\Security\SecurityServiceProvider;
// use Webkul\Support\SupportServiceProvider;
// use Webkul\TableViews\TableViewsServiceProvider;
// use Webkul\TimeOff\TimeOffServiceProvider;
// use Webkul\Timesheet\TimesheetServiceProvider;
// use Webkul\Website\WebsiteServiceProvider;

return [
    AppServiceProvider::class,
    AdminPanelProvider::class,
    CustomerPanelProvider::class,
    WebsitePanelProvider::class,
    AccountingServiceProvider::class,
    AccountServiceProvider::class,
    AnalyticServiceProvider::class,
    BarcodeServiceProvider::class,
    BlogServiceProvider::class,
    ChatterServiceProvider::class,
    ContactServiceProvider::class,
    EmployeeServiceProvider::class,
    FieldServiceProvider::class,
    InventoryServiceProvider::class,
    InvoiceServiceProvider::class,
    PartnerServiceProvider::class,
    ManufacturingServiceProvider::class,
    MaintenanceServiceProvider::class,
    PaymentServiceProvider::class,
    ProductServiceProvider::class,
    ProjectServiceProvider::class,
    PurchaseServiceProvider::class,
    RecruitmentServiceProvider::class,
    SaleServiceProvider::class,
    SecurityServiceProvider::class,
    SoftwareServiceProvider::class,
    SupportServiceProvider::class,
    TableViewsServiceProvider::class,
    TimeOffServiceProvider::class,
    FullCalendarServiceProvider::class,
    TimesheetServiceProvider::class,
    MarketingServiceProvider::class,
    WifiServiceProvider::class,
    WebsiteServiceProvider::class,
    PluginManagerServiceProvider::class,
    PsmonitorServiceProvider::class,
    ArticleServiceProvider::class,
    LeadServiceProvider::class,
];

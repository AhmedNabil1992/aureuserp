<?php

use App\Providers\AppServiceProvider;
use App\Providers\Filament\AdminPanelProvider;
use App\Providers\Filament\CustomerPanelProvider;
use App\Providers\Filament\WebsitePanelProvider;
use Webkul\Account\AccountServiceProvider;
use Webkul\Accounting\AccountingServiceProvider;
use Webkul\Analytic\AnalyticServiceProvider;
use Webkul\Article\ArticleServiceProvider;
use Webkul\Blog\BlogServiceProvider;
use Webkul\Chatter\ChatterServiceProvider;
use Webkul\Contact\ContactServiceProvider;
use Webkul\Employee\EmployeeServiceProvider;
use Webkul\Field\FieldServiceProvider;
use Webkul\FullCalendar\FullCalendarServiceProvider;
use Webkul\Inventory\InventoryServiceProvider;
use Webkul\Invoice\InvoiceServiceProvider;
use Webkul\Lead\LeadServiceProvider;
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

return [
    AppServiceProvider::class,
    AdminPanelProvider::class,
    WebsitePanelProvider::class,
    CustomerPanelProvider::class,
    AccountingServiceProvider::class,
    AccountServiceProvider::class,
    AnalyticServiceProvider::class,
    ArticleServiceProvider::class,
    BlogServiceProvider::class,
    LeadServiceProvider::class,
    MarketingServiceProvider::class,
    ChatterServiceProvider::class,
    ContactServiceProvider::class,
    EmployeeServiceProvider::class,
    FieldServiceProvider::class,
    InventoryServiceProvider::class,
    InvoiceServiceProvider::class,
    PartnerServiceProvider::class,
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
    WebsiteServiceProvider::class,
    WifiServiceProvider::class,
    PluginManagerServiceProvider::class,
];

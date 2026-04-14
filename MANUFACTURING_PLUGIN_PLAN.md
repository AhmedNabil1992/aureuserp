# خطة بناء Plugin: Manufacturing (وصفات الإنتاج)

## الهدف

بناء Plugin جديد `webkul/manufacturing` يتيح:

1. تعريف **وصفة إنتاج (Bill of Materials)** لكل منتج مركب — كمية كل خامة مطلوبة لتصنيع وحدة واحدة.
2. **خصم المخزون تلقائياً** من الخامات عند تأكيد أمر البيع (state → `sale`).
3. **نقل إدارة المنتجات** من قائمة Purchase وSales إلى navigation group جديدة خاصة بـ Manufacturing.

---

## قاعدة البيانات

### جدول 1: `manufacturing_bill_of_materials`

```
id                 BIGINT PK
product_id         FK → products_products.id
type               ENUM('manufacture', 'kit')  default: manufacture
quantity           DECIMAL(15,4) default: 1   ← كمية المنتج المنتج
uom_id             FK → support_uoms.id
reference          VARCHAR nullable            ← كود مرجعي اختياري
notes              TEXT nullable
company_id         FK → support_companies.id
creator_id         FK → security_users.id
created_at / updated_at
```

### جدول 2: `manufacturing_bill_of_material_lines`

```
id                      BIGINT PK
bill_of_material_id     FK → manufacturing_bill_of_materials.id
component_id            FK → products_products.id  ← الخامة
quantity                DECIMAL(15,4)              ← الكمية المستهلكة
uom_id                  FK → support_uoms.id
sort                    INT default: 0
notes                   TEXT nullable
created_at / updated_at
```

---

## هيكل ملفات الـ Plugin

```
plugins/webkul/manufacturing/
├── composer.json
├── config/
│   └── filament-shield.php
├── database/
│   ├── factories/
│   │   ├── BillOfMaterialFactory.php
│   │   └── BillOfMaterialLineFactory.php
│   └── migrations/
│       ├── 2026_04_14_000001_create_manufacturing_bill_of_materials_table.php
│       └── 2026_04_14_000002_create_manufacturing_bill_of_material_lines_table.php
├── resources/
│   └── lang/
│       └── en/
│           ├── app.php                          ← navigation labels
│           ├── enums/
│           │   └── bom-type.php
│           └── filament/
│               ├── clusters/
│               │   ├── products.php
│               │   └── bill-of-materials.php
│               └── resources/
│                   ├── bill-of-material.php
│                   ├── bill-of-material/
│                   │   └── pages/
│                   │       ├── list-bill-of-materials.php
│                   │       ├── create-bill-of-material.php
│                   │       ├── edit-bill-of-material.php
│                   │       └── view-bill-of-material.php
│                   └── product/
│                       └── pages/
│                           └── manage-bill-of-materials.php
└── src/
    ├── ManufacturingPlugin.php
    ├── ManufacturingServiceProvider.php
    ├── ManufacturingManager.php
    ├── Enums/
    │   └── BomType.php
    ├── Models/
    │   ├── BillOfMaterial.php
    │   └── BillOfMaterialLine.php
    ├── Observers/
    │   └── OrderObserver.php
    ├── Policies/
    │   └── BillOfMaterialPolicy.php
    └── Filament/
        └── Clusters/
            ├── Products.php
            ├── Products/
            │   └── Resources/
            │       ├── ProductResource.php
            │       └── ProductResource/
            │           └── Pages/
            │               ├── ListProducts.php
            │               ├── CreateProduct.php
            │               ├── EditProduct.php
            │               ├── ViewProduct.php
            │               ├── ManageAttributes.php
            │               ├── ManageVariants.php
            │               ├── ManageQuantities.php
            │               └── ManageBillOfMaterials.php
            ├── BillOfMaterials.php
            └── BillOfMaterials/
                └── Resources/
                    ├── BillOfMaterialResource.php
                    └── BillOfMaterialResource/
                        └── Pages/
                            ├── ListBillOfMaterials.php
                            ├── CreateBillOfMaterial.php
                            ├── EditBillOfMaterial.php
                            └── ViewBillOfMaterial.php
```

**إجمالي الملفات الجديدة: 37 ملف**

---

## تفاصيل كل ملف

### 1. `composer.json`

```json
{
    "name": "webkul/manufacturing",
    "description": "Bill of Materials and component stock deduction",
    "extra": {
        "laravel": {
            "providers": ["Webkul\\Manufacturing\\ManufacturingServiceProvider"]
        }
    },
    "autoload": {
        "psr-4": {
            "Webkul\\Manufacturing\\": "src/",
            "Webkul\\Manufacturing\\Database\\Factories\\": "database/factories/",
            "Webkul\\Manufacturing\\Database\\Seeders\\": "database/seeders/"
        }
    }
}
```

يُضاف أيضاً في `composer.json` الجذري:

- تحت `repositories`: مسار `plugins/webkul/manufacturing`
- تحت `require`: `"webkul/manufacturing": "*"`

---

### 2. `ManufacturingServiceProvider.php`

- يرث `PackageServiceProvider`
- يسجل migrations الجديدة
- يسجل `Order::observe(OrderObserver::class)` في `packageBooted()`
- يعلن dependencies: `['inventories', 'sales']`
- install command: `installDependencies()->runsMigrations()`

---

### 3. `ManufacturingPlugin.php`

- يسجل Cluster `Products` (ينقل المنتجات هنا)
- يسجل Cluster `BillOfMaterials`
- يعمل فقط على panel `admin`
- يتحقق من `Package::isPluginInstalled('manufacturing')`

---

### 4. `ManufacturingManager.php`

المنطق الأساسي:

```
consumeComponents(Order $order):
    foreach $order->lines as $line:
        $bom = BillOfMaterial::where('product_id', $line->product_id)->first()
        if (!$bom) continue
        foreach $bom->lines as $bomLine:
            $needed = $bomLine->quantity * $line->product_uom_qty
            // خصم من inventories_product_quantities
            $qty = ProductQuantity::where('product_id', $bomLine->component_id)
                                  ->where('location_id', $sourceLocation)
                                  ->first()
            $qty->decrement('quantity', $needed)
```

---

### 5. `OrderObserver.php`

```php
public function updated(Order $order): void
{
    if ($order->wasChanged('state') && $order->state === OrderState::SALE) {
        app(ManufacturingManager::class)->consumeComponents($order);
    }
}
```

---

### 6. `BomType.php` (Enum)

```php
enum BomType: string {
    case Manufacture = 'manufacture';  ← يُصنَّع من الخامات
    case Kit         = 'kit';          ← مجموعة تُباع معاً
}
```

---

### 7. `BillOfMaterial.php` (Model)

- `$table = 'manufacturing_bill_of_materials'`
- relations: `belongsTo(Product)`, `hasMany(BillOfMaterialLine)`, `belongsTo(UOM)`, `belongsTo(Company)`

---

### 8. `BillOfMaterialLine.php` (Model)

- `$table = 'manufacturing_bill_of_material_lines'`
- relations: `belongsTo(BillOfMaterial)`, `belongsTo(Product, 'component_id')`, `belongsTo(UOM)`
- implements `Sortable`

---

### 9. `Products.php` (Cluster)

- slug: `manufacturing/products`
- navigationGroup: `Manufacturing`
- يجمع إدارة المنتجات بديلاً عن Purchase/Sales

---

### 10. `ProductResource.php` (في Manufacturing)

- يرث `Webkul\Inventory\Filament\Clusters\Products\Resources\ProductResource`
- يضيف تبويب `ManageBillOfMaterials` في `getRecordSubNavigation()`
- يعيد تعيين `$cluster = ManufacturingProducts::class`

---

### 11. `ManageBillOfMaterials.php` (Page)

- يرث `ManageRelatedRecords`
- relationship: `billOfMaterials`
- يعرض جدول الوصفات المرتبطة بهذا المنتج
- زر "إضافة وصفة" يفتح نموذج BOM مع Repeater للمكونات

---

### 12. `BillOfMaterialResource.php`

**Form:**

- Section "الوصفة":
    - Select: المنتج المركب (`product_id`)
    - Select: النوع (`type`)
    - TextInput: الكمية المنتجة (`quantity`)
    - Select: وحدة القياس (`uom_id`)
    - TextInput: الكود المرجعي (`reference`)
- Section "المكونات" (Repeater):
    - Select: اسم الخامة (`component_id`)
    - TextInput: الكمية (`quantity`)
    - Select: وحدة القياس (`uom_id`)
    - TextArea: ملاحظات

**Table:**

- أعمدة: المنتج، النوع، عدد المكونات، الكمية، الكود
- Actions: View, Edit, Delete
- Filters: النوع، المنتج

---

### 13. `BillOfMaterialPolicy.php`

- `viewAny`, `view`, `create`, `update`, `delete`, `restore`, `forceDelete`
- يتبع نمط policies الموجودة في purchases

---

## التعديلات على Plugins الحالية

### Purchase — إخفاء Products Cluster

**الملف:** `plugins/webkul/purchases/src/Filament/Admin/Clusters/Products.php`

```php
// إضافة هذا الـ method:
public static function shouldRegisterNavigation(): bool
{
    return ! \Webkul\PluginManager\Package::isPluginInstalled('manufacturing');
}
```

### Sales — إخفاء Products Cluster

**الملف:** `plugins/webkul/sales/src/Filament/Clusters/Products.php`

```php
// إضافة نفس الـ method
```

---

## تسلسل التنفيذ

```
1. composer.json (plugin + root)
2. Migrations (x2)
3. Models: BillOfMaterial + BillOfMaterialLine
4. Factories (للـ models)
5. Enum: BomType
6. ManufacturingManager (خصم المخزون)
7. OrderObserver (hook تأكيد البيع)
8. ManufacturingServiceProvider
9. ManufacturingPlugin
10. Clusters: Products + BillOfMaterials
11. ProductResource + Pages (8 صفحات)
12. BillOfMaterialResource + Pages (4 صفحات)
13. BillOfMaterialPolicy
14. config/filament-shield.php
15. Translation files (en)
16. تعديل Purchase Cluster
17. تعديل Sales Cluster
18. composer dump-autoload + php artisan manufacturing:install
```

---

## dependencies graph

```
manufacturing
├── depends on → sales      (OrderObserver)
├── depends on → inventories (ProductQuantity deduction + ProductResource base)
└── depends on → products   (base Product model)
```

---

## سيناريو الاستخدام الكامل

```
1. المستخدم يدخل Manufacturing > Products
   → يضيف خامة "IC555" كـ Storable Product، يضع 100 قطعة مخزون

2. يضيف خامة "مقاومة 10K" → 500 قطعة

3. يضيف منتج مركب "لوحة تحكم إلكترونية"
   → يفتح تبويب "وصفة الإنتاج"
   → ينشئ BOM: لكل لوحة = 2× IC555 + 10× مقاومة

4. في Sales، يعمل order لـ 5 لوحات تحكم
   → عند تأكيد الأمر (Confirm Order):
      - IC555:   100 - (2 × 5) = 90 قطعة
      - مقاومة: 500 - (10 × 5) = 450 قطعة
```

---

## ما لا يشمله MVP هذا

- حسابات التكلفة التلقائية للمنتج المركب
- تحذير نقص المخزون قبل البيع
- أوامر التصنيع (Manufacturing Orders) — مرحلة لاحقة
- dossiers الجودة والتتبع بالـ Serial/Lot لكل مكون

---

_تاريخ الإنشاء: 14 أبريل 2026_

Base URL

استخدم دومين مشروعك، مثال: http://127.0.0.1:8000
الـ Legacy endpoints هنا بدون auth middleware.
إنشاء License
Method: POST
URL: /api/insert-licenses
Required fields:
CompanyName
ProductID (لازم موجود في software_programs.id)
ClientID (لازم موجود في partners_partners.id)
Optional:
GoverID, CityID, Address, EditionID, LicenseType, Period, ServerIP
مثال curl:
curl -X POST "http://127.0.0.1:8000/api/insert-licenses"
-H "Content-Type: application/json"
-d "{"CompanyName":"Acme Co","ProductID":1,"ClientID":12,"GoverID":3,"CityID":25,"Address":"Nasr City","EditionID":2,"LicenseType":"annual","Period":365,"ServerIP":"192.168.1.10"}"

نجاح متوقع:
{
"success": true,
"message": "Inserted",
"data": {
"result": 123
}
}
القيمة result هي License ID.

إضافة Device Keys للـ License
Method: POST
URL: /api/insert-keys
Required:
License_ID (من الخطوة السابقة)
Computer_ID
Bios_ID
Disk_ID
Base_ID
Video_ID
Mac_ID
مثال curl:
curl -X POST "http://127.0.0.1:8000/api/insert-keys"
-H "Content-Type: application/json"
-d "{"License_ID":123,"Computer_ID":"PC-001","Bios_ID":"BIOS-AAA","Disk_ID":"DISK-BBB","Base_ID":"BASE-CCC","Video_ID":"GPU-DDD","Mac_ID":"00-11-22-33-44-55"}"

فحص Product Key
Method: POST
URL:
/api/license-info
أو alias: /api/LicGen/info
Required:
Computer_ID
ProductKey
مثال curl:
curl -X POST "http://127.0.0.1:8000/api/license-info"
-H "Content-Type: application/json"
-d "{"Computer_ID":"PC-001","ProductKey":"ABCDE-FGHIJ-KLMNO-PQRST"}"

نجاح متوقع (شكل عام):
{
"ProductCode": "...",
"ProductKey": "...",
"LicenseType": "...",
"Expiration": "...",
"Edition": "...",
"IsMain": "..."
}

مهم

الـ requests تقبل JSON وأيضاً form-data أو x-www-form-urlencoded.
لو حصل 422 غالباً بسبب ID غير موجود في الجداول المطلوبة أو نقص حقول required.
لو تريد، أقدر أجهز لك Collection جاهزة لـ Postman للثلاث endpoints.

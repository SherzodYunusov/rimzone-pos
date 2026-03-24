# 🚀 RIMzone - Tez Boshlash Ko'llanmasi

## 📦 1. Database Migration Ishlatish

```bash
# 1-qadam: Migration ishlatish (products table yaratadi)
php artisan migrate

# 2-qadam: Test uchun misoliy ma'lumotlar qo'shish (ixtiyoriy)
php artisan db:seed --class=ProductSeeder
```

---

## 🎨 2. Frontend Assets Kompilyatsiyasi

```bash
# NPM dependencies o'rnatish (birinchi marta)
npm install

# Development mode (CSS/JS real-time bilan kompilyatsiya qiladi)
npm run dev

# Production mode (optimizatsiyalangan)
npm run build
```

**Eslatma:** `npm run dev` ishlatayotganda, terminalda yozuv bo'lib turadi. Yangi tab ochmiz serverga ishga tushirish.

---

## 🌐 3. Laravel Development Server Ishlatish

**Yangi terminalda:**

```bash
# Development server boshlash
php artisan serve

# Keyin browser da
http://localhost:8000/products
```

---

## ✅ To'liq Boshlash Jarayoni

```bash
# 📁 Loyihaning root faylidagi terminalda:

# 1️⃣ Database tablalar yaratashi
php artisan migrate

# 2️⃣ Test ma'lumotlar qo'shish (ixtiyoriy)
php artisan db:seed

# 3️⃣ Frontend assets kompilyatsiyasi (Terminal 1)
npm run dev

# 4️⃣ Laravel server (Terminal 2)
php artisan serve

# 5️⃣ Browser da ochish
# http://localhost:8000/products ✨
```

---

## 🎯 Sahifadagi Funksiyalarni Sinash

| Amaliyot | Qanday Qilish |
|---------|--------------|
| **Mahsulot qo'shish** | "Mahsulot qo'shish" tugmasini bosish → Modal ochiladi → Ma'lumotlar kiritish → "Saqlash" |
| **Mahsulotni ko'rish** | Grid kartalarda ko'rsatiladi (responsive) |
| **Tahrirlash** | Har bir kartaning "Tahrirlash" tugmasini bosish → Modal ochiladi → O'zgartirishlar qilish → "Saqlash" |
| **O'chirish** | "O'chirish" tugmasini bosish → Tasdiqlash → Bajariladi |
| **Notification** | Amal bajarilgandan keyin yuqor-o'ng burchakda xabar ko'rsatiladi |

---

## 🔥 Juda Tez Usuli (Misoliy Ma'lumot Bilan)

```bash
# Bitta commandda hamma:
php artisan migrate:fresh --seed

# Bu javob beradi:
✓ migrate:fresh - Barcha tablalar o'chiriladi va qayta yaratiladi
✓ --seed - ProductSeeder avtomatik ishlatiladi (8 ta misoliy mahsulot)

# Keyin server boshlang:
php artisan serve
```

**Bu usul** yangi boshlaganda, test qilishni tez qilish uchun!

---

## 📋 Fayllar Batafsil

| Fayl | Maqsadi |
|-----|---------|
| `database/migrations/2026_03_20_000001_create_products_table.php` | Products jadvalini yaratadi |
| `app/Models/Product.php` | Mahsulot modeli, database bilan ulanish |
| `app/Http/Controllers/ProductController.php` | CRUD logikasi (Create, Read, Update, Delete) |
| `resources/views/products/index.blade.php` | Frontend HTML + Alpine.js + Tailwind CSS |
| `routes/web.php` | API routes |
| `database/seeders/ProductSeeder.php` | Test uchun misoliy ma'lumotlar |

---

## 🎨 Customization Examples

### ✏️ Validation Qoidalarini O'zgartirish

Faylni oching: `app/Http/Controllers/ProductController.php`

```php
// store() metodida:
$validated = $request->validate([
    'name' => 'required|string|max:100',  // max:100 ← O'zgartir
    'price' => 'required|numeric|min:0',
    'quantity' => 'required|integer|min:0',
]);
```

### 🎨 Rangi O'zgartirish

Faylni oching: `resources/views/products/index.blade.php`

```html
<!-- Blue rangni boshqa rangga -->
<h1 class="text-blue-600">RIMzone</h1>
<!-- Shunday o'zgartir -->
<h1 class="text-green-600">RIMzone</h1>

<!-- Tailwind colors: -->
<!-- red-600, yellow-600, green-600, purple-600, pink-600, etc. -->
```

### 📊 Grid Ustunlarini O'zgartirish

Faylni oching: `resources/views/products/index.blade.php`

```html
<!-- Hozir: 3 ustun (lg:grid-cols-3) -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

<!-- Shunday o'zgartir (2 yoki 4 ustun uchun) -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
```

---

## 🐛 Common Issues Va Yechish

### ❌ "Column 'products' table doesn't exist"
```bash
# Yechish:
php artisan migrate

# Agar xato qaytsa:
php artisan migrate:fresh --seed
```

### ❌ "npm: command not found"
```bash
# Node.js o'ynatilmagan demak, o'rnating:
# https://nodejs.org dari yuklab oling
```

### ❌ Assets (CSS) yuklanmayapti
```bash
# Yechish:
npm run dev
# Terminal 1da ishlatib turing!
```

### ❌ "POST /products 404"
```bash
# Route qo'llanganini tekshir:
php artisan route:list

# Yoki routes/web.php ni kontrol qil
```

---

## 📝 API Testing (Postman/Thunder Client)

### ➕ Yangi Mahsulot Qo'shish
```
POST http://localhost:8000/products
Content-Type: application/json
X-CSRF-TOKEN: [csrf-token]

{
  "name": "iPhone 15 Pro",
  "price": 999.99,
  "quantity": 20,
  "description": "Latest Apple smartphone"
}
```

### 📖 Barcha Mahsulotlarni Olish
```
GET http://localhost:8000/products
```

### ✏️ Mahsulotni Tahrirlash
```
PUT http://localhost:8000/products/1
Content-Type: application/json
X-CSRF-TOKEN: [csrf-token]

{
  "name": "iPhone 15 Pro Max",
  "price": 1099.99,
  "quantity": 15,
  "description": "Updated description"
}
```

### 🗑️ Mahsulotni O'chirish
```
DELETE http://localhost:8000/products/1
X-CSRF-TOKEN: [csrf-token]
```

---

## ✨ Features Checklist

| Feature | Status |
|---------|--------|
| Create (Qo'shish) | ✅ |
| Read (Ko'rish) | ✅ |
| Update (Tahrirlash) | ✅ |
| Delete (O'chirish) | ✅ |
| Modal Animation | ✅ |
| Responsive Design | ✅ |
| Error Handling | ✅ |
| Notifications | ✅ |
| Form Validation | ✅ |
| CSRF Protection | ✅ |

---

## 🎓 Oquvchi Uchun Ko'shimcha

### Laravel Tinker (Database Testing)
```bash
php artisan tinker

# Rasm:
>>> $product = App\Models\Product::first();
>>> $product->name;
>>> $product->update(['price' => 500]);
>>> App\Models\Product::all();
```

### Log Faylini Ko'rish
```bash
# Latest errors:
tail -f storage/logs/laravel.log

# Windows:
Get-Content storage/logs/laravel.log -tail 20
```

---

## 🎉 Tayyor!

Endi **http://localhost:8000/products** ga kirib, dasturni sinab ko'ring!

```
╔════════════════════════════════════════╗
║  🎨 RIMzone - Omborni Boshqarish       ║
║  ✨ Zamonaviy Full-Stack Dastur        ║
║  ✅ Hamma Funksiya Tayyori             ║
╚════════════════════════════════════════╝
```

**Barakalla! 🚀**

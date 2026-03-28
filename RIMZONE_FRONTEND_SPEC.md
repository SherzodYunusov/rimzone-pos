# RIMzone — Frontend Specification Document
> **Maqsad:** Ushbu hujjat frontend AI uchun yozilgan. RIMzone POS tizimining barcha sahifalari, funksiyalari, matnlari, API endpointlari va UI/UX tavsiflarini to'liq qamrab oladi. Ushbu hujjat asosida siz ideal, zamonaviy, mobile-first dizayndagi to'liq frontend clone yaratishingiz kerak.

---

## 1. LOYIHA HAQIDA

**RIMzone** — kichik va o'rta biznes uchun mo'ljallangan Uzbek tilidagi Point-of-Sale (POS) boshqaruv tizimi.

**Asosiy funksiyalar:**
- 🏭 **Ombor** — mahsulotlarni boshqarish (narx, tannarx, miqdor)
- 👥 **Mijozlar** — mijoz bazasi (foto, manzil, Google Maps joylashuv)
- 🛒 **Savdo (POS)** — real-time kassa, savatcha, naqd/karta/nasiya to'lov
- 📊 **Hisobotlar** — daromad, foyda, nasiya qarzlari, ombor holati

**Til:** Uzbek (lotin)
**Pul birligi:** so'm (UZS)
**Raqam formati:** `1 234 567 so'm` (bo'sh joy separator)

---

## 2. TECH STACK

| Layer | Texnologiya |
|---|---|
| Backend | Laravel 13, PHP 8.3 |
| DB | SQLite (production: MySQL/PostgreSQL) |
| Frontend JS | Alpine.js v3 (CDN) |
| CSS | Tailwind CSS v3 (CDN) |
| Font | Inter (Google Fonts: 400, 500, 600, 700, 800) |
| Maps | Leaflet.js v1.9.4 (faqat Mijozlar sahifasida) |
| Icons | Inline SVG (Heroicons style) |

**Muhim:** Hech qanday build process yo'q. Barcha JS/CSS CDN orqali yuklanadi.

---

## 3. DIZAYN TIZIMI (Design System)

### 3.1 Rang Palitasi

```
Asosiy:      #2563eb (blue-600)
Sidebar:     #ffffff (white), border: #e2e8f0
Background:  #f8fafc (slate-50)
Matn:        #334155 (slate-700)
Matn dim:    #94a3b8 (slate-400)
```

**Har bir bo'lim o'z rangiga ega:**

| Bo'lim | Rang | Tailwind class |
|---|---|---|
| Ombor (mahsulotlar) | Ko'k | `blue-600`, `bg-blue-50`, `active-blue` |
| Mijozlar | Binafsha | `violet-600`, `bg-violet-50`, `active-violet` |
| Savdo (POS) | Yashil | `green-600`, `bg-green-50`, `active-green` |
| Hisobotlar | To'q sariq | `amber-600`, `bg-amber-50`, `active-amber` |

**Status ranglari:**
- `paid` → Yashil (emerald-500)
- `debt` → Qizil (red-500)
- `partial` → To'q sariq (amber-500)

### 3.2 Tipografiya

```
Sarlavha:    font-bold text-slate-800
Sub-sarlavha: font-semibold text-slate-700
Label:       text-xs font-medium text-slate-600
Tavsif:      text-xs text-slate-400
Badge:       text-[10px] font-black uppercase tracking-widest
```

### 3.3 Komponentlar

**Kartochka:**
```
bg-white border border-slate-200 rounded-xl shadow-sm
hover:shadow-md transition-shadow duration-200
```

**Birlamchi tugma:**
```
bg-blue-600 hover:bg-blue-700 text-white font-medium text-sm
py-2 px-4 rounded-lg transition-colors shadow-sm
```

**Input field:**
```
w-full px-3 py-2.5 text-sm border border-slate-200 rounded-lg
focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-400
text-slate-700 placeholder-slate-400
```

**Modal:**
```
bg-white rounded-2xl shadow-2xl border border-slate-200
w-full max-w-lg overflow-hidden max-h-[90vh] flex flex-col
```
Modal overlay: `fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/40 backdrop-blur-sm`

### 3.4 Animatsiyalar

```css
/* Sahifa kirish animatsiyasi */
@keyframes slideUpFade {
  from { opacity: 0; transform: translateY(10px); }
  to   { opacity: 1; transform: translateY(0); }
}

/* Kartochka kirish */
@keyframes fadeInUp {
  from { opacity: 0; transform: translateY(14px) scale(0.98); }
  to   { opacity: 1; transform: translateY(0) scale(1); }
}
```

---

## 4. DATABASE SCHEMA (So'nggi holat)

### products
| Ustun | Tur | Izoh |
|---|---|---|
| id | bigint PK | |
| name | varchar(255) | Har doim UPPERCASE saqlanadi |
| price | decimal(15,2) | Sotish narxi (so'm) |
| cost_price | decimal(15,2) nullable | Tannarx (foyda hisoblash uchun) |
| quantity | integer | Ombordagi miqdor (dona) |
| unit_type | enum('kg','litr') nullable | O'lchov turi |
| unit_value | decimal(10,3) nullable | Bir dona necha kg/litr |
| description | text nullable | Qo'shimcha tavsif |
| created_at / updated_at | timestamp | |

### customers
| Ustun | Tur | Izoh |
|---|---|---|
| id | bigint PK | |
| name | varchar(255) | To'liq ism |
| phone | varchar(50) | Telefon raqam |
| address | varchar(255) | Shahar, ko'cha |
| company_name | varchar(255) | Korxona nomi |
| photo | varchar nullable | Storage path (`customers/file.jpg`) |
| lat | decimal(10,8) nullable | GPS kenglik |
| lng | decimal(11,8) nullable | GPS uzunlik |
| map_link | text nullable | Google Maps URL |
| created_at / updated_at | timestamp | |

### sales
| Ustun | Tur | Izoh |
|---|---|---|
| id | bigint PK | |
| customer_id | bigint FK nullable | `customers.id` — nasiyada majburiy |
| total_price | decimal(15,2) | Jami to'lov summasi |
| sale_date | date | Savdo sanasi |
| payment_method | enum('naqd','karta','nasiya') | To'lov usuli |
| status | enum('paid','debt','partial') | paid=to'langan, debt=qarz, partial=qisman |
| paid_amount | decimal(15,2) default 0 | Hozircha to'langan summa |
| due_date | date nullable | Nasiya muddati |
| created_at / updated_at | timestamp | |

### sale_items
| Ustun | Tur | Izoh |
|---|---|---|
| id | bigint PK | |
| sale_id | bigint FK | `sales.id` CASCADE DELETE |
| product_id | bigint FK | `products.id` CASCADE DELETE |
| quantity | integer | Sotilgan miqdor |
| unit_price | decimal(15,2) | Sotilgan paytdagi narx (snapshot) |
| cost_price | decimal(15,2) nullable | Sotilgan paytdagi tannarx (snapshot) |
| created_at / updated_at | timestamp | |

### sale_payments
| Ustun | Tur | Izoh |
|---|---|---|
| id | bigint PK | |
| sale_id | bigint FK | `sales.id` CASCADE DELETE |
| amount | decimal(15,2) | To'lov miqdori |
| payment_date | date | To'lov sanasi |
| notes | text nullable | Izoh |
| created_at / updated_at | timestamp | |

**Model computed property:**
```
remaining_debt = max(0, total_price - paid_amount)
```

---

## 5. API ENDPOINTLAR

### Mahsulotlar
| Method | URL | Tavsif | Response |
|---|---|---|---|
| GET | `/products` | Sahifani render qilish | HTML view |
| POST | `/products` | Yangi mahsulot | JSON `{success, message, product}` |
| GET | `/products/{id}/edit` | Tahrirlash ma'lumoti | JSON `product` |
| PUT | `/products/{id}` | Yangilash | JSON `{success, message, product}` |
| DELETE | `/products/{id}` | O'chirish | JSON `{success, message}` |

### Mijozlar
| Method | URL | Tavsif | Response |
|---|---|---|---|
| GET | `/customers` | Sahifani render qilish | HTML view |
| POST | `/customers` | Yangi mijoz (multipart/form-data) | JSON `{success, message, customer}` |
| PUT | `/customers/{id}` | Yangilash (multipart/form-data) | JSON `{success, message, customer}` |
| DELETE | `/customers/{id}` | O'chirish | JSON `{success, message}` |

> Mijoz POST/PUT da `photo_url` (asset URL) ham response ichida keladi.

### Savdolar
| Method | URL | Tavsif | Body |
|---|---|---|---|
| GET | `/sales` | POS sahifasi | — |
| POST | `/sales` | Yangi savdo | `{customer_id?, sale_date, payment_method, due_date?, items:[{product_id, quantity}]}` |
| GET | `/sales/{id}` | Savdo detali | — |
| DELETE | `/sales/{id}` | O'chirish (ombor qaytaradi) | — |
| POST | `/sales/{id}/pay` | Nasiya to'lash | `{amount, payment_date, notes?}` |

### Hisobotlar
| Method | URL | Tavsif |
|---|---|---|
| GET | `/reports?start_date=Y-m-d&end_date=Y-m-d` | Hisobot sahifasi |
| DELETE | `/reports/clear-day` | Kunni o'chirish | `{date: Y-m-d}` |

---

## 6. LAYOUT (Umumiy qobiq)

### Struktura
```
<body> [Alpine: sidebarOpen=false]
  ├── Mobile backdrop (z-40, sidebarOpen bo'lganda)
  ├── <aside> — Sidebar (w-64)
  │   ├── Logo section (h-16)
  │   │   ├── [Mobile] ✕ yopish tugmasi
  │   │   └── RIMzone logo + "Boshqaruv tizimi"
  │   ├── <nav> — Navigatsiya
  │   │   ├── "Asosiy bo'limlar" (label)
  │   │   ├── 🏭 Ombor → /products
  │   │   ├── 👥 Mijozlar → /customers
  │   │   ├── 🛒 Savdo → /sales
  │   │   └── 📊 Hisobotlar → /reports
  │   └── Admin (bottom)
  └── <main-wrapper> [flex-1]
      ├── [Mobile only] Top bar (h-14)
      │   ├── ☰ Hamburger tugma (sidebarOpen=true)
      │   ├── RIMzone mini logo
      │   └── Sahifa nomi (o'ngda, dim)
      └── @yield('content')
```

### Sidebar Mobile Xatti-harakat
- **Mobile (`< 768px`):** `position: fixed`, `inset-y-0 left-0`, `z-50`, `translate-x: -100%` (yashirin)
- **`sidebarOpen = true`** bo'lganda: `translate-x: 0` → sidebar chap tomondan siljib chiqadi (0.3s cubic-bezier)
- Backdrop `z-40` overlay bosish → sidebar yopiladi
- **Desktop (`>= 768px`):** `position: sticky`, `top: 0`, `h-screen` — har doim ko'rinadi

### Sidebar Nav Link Holatlar
```
Faol holat (active):
  Ombor:      bg-blue-50    text-blue-600   border-l-4 border-blue-600
  Mijozlar:   bg-violet-50  text-violet-600 border-l-4 border-violet-600
  Savdo:      bg-green-50   text-green-600  border-l-4 border-green-600
  Hisobotlar: bg-amber-50   text-amber-600  border-l-4 border-amber-600

Hover: bg-slate-50, transform: translateX(4px)
Icon ikon: hover scale(1.1)
```

### Logo Bloki
```
[Gradient kvadrat 32x32 (blue-500→indigo-600)]  RIMzone
                                                  Boshqaruv tizimi (10px, slate-400)
```

### Admin Bloki (sidebar pastki qism)
```
[Avatar aylana]  Admin
                 © 2026 RIMzone
```

---

## 7. OMBOR SAHIFASI (`/products`)

### Maqsad
Omboridagi barcha mahsulotlarni ko'rish, qo'shish, tahrirlash, o'chirish.

### Page Header
```
┌─────────────────────────────────────────────────────────┐
│ Ombor                    [Barchasi] [Kam qoldi] [Tugagan]│
│ 42 ta mahsulot           [🔍 Qidirish...]  [+ Mahsulot]  │
└─────────────────────────────────────────────────────────┘
```

- `h-14 md:h-16`, `px-4 md:px-8`, gradient: `from-white via-white to-blue-50/50`
- **Filter tugmalari** (faqat `md:` va kattada ko'rinadi):
  - `Barchasi` — barcha mahsulotlar
  - `Kam qoldi (<5)` — miqdori 1-4 bo'lgan mahsulotlar
  - `Tugagan (0)` — miqdori 0 bo'lgan mahsulotlar
  - Faol holatda: `bg-white text-blue-600 shadow-sm`
- **Qidiruv input:** `pl-9 pr-4 py-2`, kattalashtirish lupa icon chapda, `w-32 sm:w-48 xl:w-56`
- **"+ Mahsulot qo'shish" tugmasi:** mobilda faqat `+` icon, `sm:` dan text ham chiqadi

### Mahsulot Kartochkasi
```
┌──────────────────────────────┐
│ MAHSULOT NOMI    [Tugagan]   │
│                              │
│ Narxi:       12 500 so'm     │
│ Tannarx:      8 000 so'm     │
│ Qoldiq:       [bar] 24 dona  │
│ O'lchov:      kg (2.5 kg)    │
│                              │
│ [Tahrirlash]  [O'chirish]    │
└──────────────────────────────┘
```

**Holat badgelari:**
- `Tugagan` — qizil badge: `bg-red-500 text-white text-[10px] font-black rounded-full`
- `Kam!` — to'q sariq badge + `animate-pulse` nuqta: `bg-amber-50 text-amber-700 border border-amber-200`

**Miqdor progress bar:**
- Yashil: 10+ dona
- To'q sariq: 5-9 dona
- Qizil + yonib turishi (`glow` animation): 1-4 dona
- Qizil to'liq: 0 dona

**Kartochka hover:** `translateY(-4px)` + `box-shadow: 0 10px 28px -6px rgba(37,99,235,0.13)`

**Grid:** `grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 2xl:grid-cols-4 gap-4`

### Tanlangan Mahsulot Detali (o'chirilishi mumkin)
Mahsulot nomini bosish → sarlavha ostida kengayuvchi karta chiqadi:
```
┌─ MAHSULOT NOMI ─────────── [✕ Yopish] ─┐
│ Birlik narxi: X so'm                     │
│ Qoldiq: Y dona                           │
│ Jami summa: Z so'm                       │
└──────────────────────────────────────────┘
```
Grid: `grid-cols-1 sm:grid-cols-3 gap-4`

### Yangi Mahsulot Modali
**Sarlavha:** "Yangi mahsulot qo'shish" / "Mahsulotni tahrirlash"

**Forma maydonlari:**
```
Mahsulot nomi *         [text input — to'liq kenglikda]

[Sotish narxi (so'm) *] [Tannarx (so'm) — ixtiyoriy]
[Soni (dona) *        ] [O'lchov turi: — / kg / litr  ]
[Unit value (ixtiyoriy)] [Tavsif (ixtiyoriy)           ]
```

**Validatsiya:**
- `name` — required
- `price` — required, numeric, min:0
- `quantity` — required, integer, min:0
- `cost_price` — optional, numeric
- `unit_type` — optional: 'kg' | 'litr'

**Footer tugmalar:**
```
[Bekor qilish]  [Saqlash]
```

**Eslatma:** name serverdа `strtoupper()` qilinadi → kartochkada HAMMAsI KATTA harf.

### Bo'sh Holat (Empty State)
```
[📦 Icon]
Hozircha mahsulotlar yo'q
Birinchi mahsulotingizni qo'shing
[+ Qo'shish]
```

### O'chirish Tasdiqlash Modali
```
Mahsulotni o'chirishni tasdiqlaysizmi?
"MAHSULOT NOMI" ombordan butunlay o'chiriladi.

[Bekor qilish]  [🗑 Ha, o'chirish]
```

### Alpine.js State (`productApp()`)
```js
{
  products: [],          // PHP dan o'tkazilgan
  filterType: 'all',     // 'all' | 'low' | 'out'
  searchTerm: '',
  selectedProductId: null,
  isModalOpen: false,
  editingId: null,
  form: { name:'', price:'', cost_price:'', quantity:'', unit_type:'', unit_value:'', description:'' },
  errors: {},
  deleteModal: { open: false, product: null },
  isSubmitting: false,

  get filteredProducts() {
    // filterType + searchTerm bo'yicha filter
  }
}
```

---

## 8. MIJOZLAR SAHIFASI (`/customers`)

### Maqsad
Mijoz bazasini boshqarish. Har bir mijozda joylashuv (GPS) va Leaflet mini-xarita mavjud.

### Page Header
```
┌──────────────────────────────────────┐
│ Mijozlar              [+ Mijoz]      │
│ 18 ta mijoz                          │
└──────────────────────────────────────┘
```
- `h-14 md:h-16`, `px-4 md:px-8`
- Tugma: mobilda faqat `+` icon

### Mijoz Kartochkasi

**Yuqori qism (120px, joylashuv bo'yicha 3 variant):**

1. **GPS koordinata bor** → Leaflet mini-xarita (zoom:15, dragging/zoom disabled, OpenStreetMap tiles) + "Navigatsiya" tugmasi (pastki o'ng burchak, ko'k)
2. **GPS yo'q, foto bor** → `h-24` img object-cover
3. **GPS ham, foto ham yo'q** → `h-24` avatarga ega bo'sh joy — ismi bosh harfi katta ko'k circle ichida

**Info qismi:**
```
MIJOZ ISMI
KORXONA NOMI (blue-600, uppercase)

┌──────────────────────────────┐
│ 📞  +998 90 123 45 67        │
└──────────────────────────────┘
┌──────────────────────────────┐
│ 📍  Toshkent, Yunusobod      │
└──────────────────────────────┘
```
Info rowlar: `bg-slate-50 rounded-xl border border-slate-100`, hover → `bg-white border-blue-200`

**Action footer:**
```
[✏️ Tahrirlash] | [🗑 O'chirish]
```
Divider: `border-r border-slate-100`

**Grid:** `grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 2xl:grid-cols-4 gap-4`

**Navigatsiya tugmasi:** `https://www.google.com/maps/dir/?api=1&destination={lat},{lng}` → yangi tab

### Mijoz Modali (Add/Edit)

**Forma grid:** `grid-cols-1 sm:grid-cols-2 gap-4`

```
[Ismi *          ] [Telefon *       ]
[Manzil *        ] [Korxona nomi *  ]

Google Maps Linkini tashlang (ixtiyoriy — joylashuv avtomatik aniqlanadi)
[https://maps.google.com/... input + ✓ icon]
📍 41.299496, 69.240073 [✕ Tozalash]

Foto (ixtiyoriy)
[Faylni tanlang yoki shu yerga tashlang]
[Eski foto ko'rinishi]
```

**Google Maps Link Auto-parser:**
Quyidagi URL formatlardan lat/lng ajratiladi:
1. `/@41.2994,69.2400` (coordinates path)
2. `?q=41.2994,69.2400` (query param)
3. `!3d41.2994!4d69.2400` (embed format)
4. `?ll=41.2994,69.2400` (legacy format)

Muvaffaqiyatli parse qilinganda: yashil ✓ icon + `📍 lat, lng` ko'rinadi + "Tozalash" tugmasi.

**Foto upload:** `input[type=file]` accept="image/*", max 4MB. Eski foto serverda o'chiriladi.

**Footer:** `[Bekor qilish] [Saqlash]`

### O'chirish Modali
```
Mijozni o'chirishni tasdiqlaysizmi?
"Ismi" va unga bog'liq barcha ma'lumotlar o'chiriladi.

[Bekor qilish] [🗑 O'chirish]
```

### Leaflet Xarita Texnik Tafsilotlari
```js
// CDN: Leaflet v1.9.4 (unpkg)
// Xarita sozlamalari:
{
  zoomControl: false,
  dragging: false,
  scrollWheelZoom: false,
  doubleClickZoom: false,
  touchZoom: false,
  attributionControl: false
}
// Tiles: https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png
// Marker: L.divIcon — ko'k nuqta (24x24, border: 3px white, border-radius: 50%)
// Re-render: _leafletMaps{} registry + $nextTick() Alpine
```

### Alpine.js State (`customerApp()`)
```js
{
  customers: [],           // PHP dan JSON
  isFormOpen: false,
  editingId: null,
  form: { name:'', phone:'', address:'', company_name:'', map_link:'', lat:'', lng:'' },
  errors: {},
  deleteModal: { open: false, customer: null },
  isSubmitting: false,
  photoPreview: null,
  photoFile: null,

  parseMapLink(url),       // lat/lng ajratish
  renderAllMaps(),          // Leaflet xaritalarni render
  init(),                   // renderAllMaps() chaqiradi
  openAddModal(),
  openEditModal(customer),  // form.lat, form.lng, form.map_link to'ldiradi
  submitForm(),             // FormData + fetch
  openDeleteModal(customer),
  deleteCustomer()
}
```

---

## 9. SAVDO SAHIFASI (`/sales`) — POS

### Maqsad
Real-time kassa tizimi. Mahsulot tanlash → savatcha → to'lov.

### Layout (Desktop)
```
┌─── Top Bar (h-14 md:h-16) ────────────────────────────────┐
│ POS                                   [📋 Hisobot]         │
└────────────────────────────────────────────────────────────┘
│                                                            │
│  ◄── LEFT PANEL (flex-1) ──►  │  ◄── RIGHT: SAVATCHA ──► │
│  Mahsulot grid                 │  Savatcha content          │
│  (overflow-y-auto)             │  (overflow-y-auto)         │
│  pb-24 md:pb-4                 │  w-80 xl:w-96              │
└────────────────────────────────────────────────────────────┘
```

### Layout (Mobile)
```
┌─── Top Bar ──────────────────────┐
│ POS              [📋 icon only]  │
└──────────────────────────────────┘
│                                  │
│  MAHSULOTLAR (full width)        │
│                                  │
│                                  │
│                                  │
└──────────────────────────────────┘
┌─── STICKY BOTTOM BAR ────────────┐
│ Jami: 45 600 so'm                │
│ [Naqd] [💳 Karta] [📋 Nasiya]   │
│        [🛒 3 ta mahsulot]        │
└──────────────────────────────────┘

# Savatcha — Bottom Sheet (siljib chiqadi):
┌─ Drag handle ────────────────────┐
│ ━━━━━━━━                    [✕] │
│ 🛒 Savatcha             2 ta    │
├──────────────────────────────────┤
│ [Mijoz tanlang] ▾               │
│ Sana: [2026-03-27]              │
│ Nasiya muddati: [date]          │
├──────────────────────────────────┤
│ MAHSULOT NOMI          2 × 500 │
│ [−] [2] [+]                     │
├──────────────────────────────────┤
│ Jami:              12 500 so'm  │
│ [✅ To'lash: 12 500 so'm]      │
└──────────────────────────────────┘
```

### Top Bar
- `h-14 md:h-16`, `bg-white border-b border-slate-200`
- Chap: **"POS"** sarlavha (bold)
- O'ng: **"📋 Hisobot"** tugma — savdolar tarixini ochadi (mobilda faqat icon)

### Chap Panel — Mahsulot Gridi

**Qidiruv va filter:**
```
[🔍 Mahsulot qidirish...        ]
[Barchasi] [Kam qoldi] [Tugagan]
```

**Mahsulot kartochkasi (POS versiyasi):**
```
┌─────────────────────────┐
│ [Qty badge: ×3]         │
│                         │
│  MAHSULOT NOMI          │
│  Narxi: 12 500 so'm     │
│  [⚡ 3 dona qoldi]      │
│                         │
│  [−] [2] [+]            │
│                         │
│  [🛒 Savatchaga]        │
└─────────────────────────┘
```

- Savatdagi mahsulotda chap yuqori burchakda ko'k badge: `×3`
- `[⚡ N dona qoldi]` — stock warning (amber)
- `[Tugagan]` — red badge, qo'shib bo'lmaydi
- Savatga qo'shilganda: `pulseSoft` animatsiya

**Mahsulot gridi:** `grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-3`

### O'ng Panel — Savatcha

**Mijoz tanlash:**
```
Mijoz tanlang (nasiya uchun majburiy)
[🔽 Select dropdown — barcha mijozlar]
```

**Sana:**
```
Savdo sanasi:
[date input — bugun]
```

**Nasiya muddati (faqat `payment_method === 'nasiya'` bo'lganda ko'rinadi):**
```
To'lov muddati (nasiya):
[date input]
```

**Savatcha elementlari:**
```
MAHSULOT NOMI                 ×N
                    N × X so'm
[−]  [  N  ]  [+]
```
- `[−]` `[+]` — mikdorni o'zgartiradi
- `[−]` miqdor 1 bo'lganda o'chiradi yoki `🗑` icon chiqadi

**Jami:**
```
━━━━━━━━━━━━━━━━━━━━━━━━━━
Jami:           12 500 so'm
```

**To'lov usuli (3 toggle tugma):**
```
[💵 Naqd]  [💳 Karta]  [📋 Nasiya]
```
Faol holatda:
- Naqd: `bg-emerald-500 text-white`
- Karta: `bg-blue-500 text-white`
- Nasiya: `bg-amber-500 text-white`

**Savat bo'sh holati:**
```
[🛒 icon]
Savatcha bo'sh
Chap tarafdan mahsulot tanlang
```

**To'lash tugmasi:**
```
Savatcha bo'sh:
  [Mahsulotlarni tanlang — disabled, grey]

Mahsulot bor, to'lov usuli tanlanmagan:
  [To'lov usulini tanlang — orange/warning]

Hammasi tayyor:
  [✅ To'lash: 12 500 so'm — green]
```

**Savatchani tozalash:** `[🗑 Tozalash]` link/tugma

### Savdo Tarixi Modal (Hisobot)
```
┌─ Savdolar Tarixi ────────── [✕] ─┐
│ [🔍 Qidirish...] [Barcha status] │
├───────────────────────────────────┤
│ 📅 27.03.2026                    │
│ ┌────────────────────────────┐   │
│ │ Mijoz: Sardor Karimov       │   │
│ │ SHAMPUN × 2                 │   │
│ │ YUVUVCHI × 1               │   │
│ │ 15 000 so'm  [Naqd] [paid] │   │
│ │ [🗑 O'chirish]             │   │
│ └────────────────────────────┘   │
│                                   │
│ 📅 26.03.2026                    │
│ ...                               │
└───────────────────────────────────┘
```

**Savdo karta ranglari (status):**
- `paid` → yashil border + badge
- `debt` → qizil border + badge + qolgan qarz + "To'lash" tugmasi
- `partial` → to'q sariq border + badge + progress bar + "To'lash" tugmasi

**Nasiya to'lash mini-modal:**
```
To'lov qabul qilish
Qolgan qarz: 8 500 so'm

Summa: [8500]
Sana: [date]
Izoh: [...optional...]

[Bekor qilish] [✅ To'lovni tasdiqlash]
```

### Alpine.js State (`posApp()`)
```js
{
  // Data
  products: [],          // PHP dan
  customers: [],
  sales: [],

  // Cart
  cart: [],              // [{product, quantity}]
  selectedCustomer: null,
  paymentMethod: '',     // 'naqd'|'karta'|'nasiya'
  saleDate: today,
  dueDate: '',

  // UI
  searchTerm: '',
  filterStock: 'all',
  showHistory: false,
  showCartMobile: false,  // Mobile bottom sheet
  isSubmitting: false,

  // History filter
  historySearch: '',
  historyStatusFilter: 'all',
  expandedSaleId: null,

  // Pay modal
  payModal: { open: false, sale: null, amount: '', date: today, notes: '' },

  // Computed
  get cartTotal(),
  get filteredProducts(),
  get filteredSales(),
  get cartItemCount(),

  // Methods
  addToCart(product),
  removeFromCart(productId),
  updateQty(productId, delta),
  clearCart(),
  submitSale(),
  deleteSale(sale),
  openPayModal(sale),
  submitPayment(),
}
```

---

## 10. HISOBOTLAR SAHIFASI (`/reports`)

### Maqsad
Biznes analytics: daromad, foyda, nasiya qarzlari, ombor holati.

### Sticky Header
```
┌─ Sticky Top (z-30) ─────────────────────────────────────┐
│ Hisobotlar                    [📅 start] [📅 end] [Filter]│
│ 27.03.2026 oralig'i          [🗑 Kunni o'chirish]        │
├─────────────────────────────────────────────────────────┤
│ [Umumiy] [Nasiya 3] [Mahsulotlar] [Savdolar 12]         │
└─────────────────────────────────────────────────────────┘
```

**Date filter:** Form GET submit. Default: bugun. `start_date` va `end_date` query params.

**"Kunni o'chirish" tugmasi:** Confirm modal ochadi → `DELETE /reports/clear-day` + `{date: startDate}` (faqat `startDate === endDate` bo'lganda mantiqli)

**Tab faol ranglari:**
- Umumiy: `border-blue-600 text-blue-700 bg-blue-50/50`
- Nasiya: `border-amber-500 text-amber-700 bg-amber-50/50`
- Mahsulotlar: `border-violet-600 text-violet-700 bg-violet-50/50`
- Savdolar: `border-teal-600 text-teal-700 bg-teal-50/50`

### 3 Asosiy Karta (har doim ko'rinadi)
**Grid:** `grid grid-cols-1 sm:grid-cols-3 gap-4`

**Karta 1 — Jami Savdo (ko'k gradient):**
```
[📊 icon]  JAMI SAVDO
1 234 567
so'm · 42 ta sotuv
```
`bg-gradient-to-br from-blue-600 to-indigo-700`

**Karta 2 — Sof Foyda Naqd (yashil yoki qizil gradient):**
```
[📈 icon]  SOF FOYDA (NAQD)
+345 678
so'm · hozir cho'ntakda
+ 23 456 so'm nasiyada kutilmoqda (agar mavjud bo'lsa)
```
Ijobiy: `from-emerald-500 to-teal-600`
Salbiy: `from-red-500 to-rose-600`

**Karta 3 — Nasiya Summasi (to'q sariq yoki qizil gradient):**
```
[⏰ icon]  NASIYA SUMMASI
89 000
so'm · 5 ta qarz
123 000 so'm muddati o'tgan (agar mavjud)
```
Normal: `from-amber-400 to-orange-500`
Muddati o'tgan: `from-orange-500 to-red-600` + `pulseRed` animatsiya

### Tab 1: Umumiy

**Naqd Tushum Progress Bar:**
```
Naqd tushum holati                         67.3%
Davr savdolarining necha foizi naqd?

[═══════════════════════════════░░░░░░░░░] (h-8)
  67.3% (yashil)                 32.7% (orange)

● Naqd tushum: +823 456 so'm
                              Qarzda qolgan: −234 000 so'm ●
```
Yashil bar: `from-emerald-500 to-teal-500`
Orange bo'shliq: `bg-orange-100 border border-orange-200`

**To'lov Usullari Breakdown:**
```
┌─ To'lov usullari ──────────────────────────┐
│  💵 Naqd:    523 456 so'm  (42.3%)        │
│  💳 Karta:   300 000 so'm  (24.3%)        │
│  📋 Nasiya:  234 000 so'm  (18.9%)        │
│                                            │
│  Naqd tushum (Naqd+Karta): 823 456 so'm  │
└────────────────────────────────────────────┘
```

**Foyda Tahlili:**
```
┌─ Foyda tahlili ───────────────────────────┐
│  Umumiy daromad:     1 234 567 so'm       │
│  Tannarx:             567 890 so'm        │
│  Gross foyda:        +666 677 so'm (54%)  │
│                                           │
│  Haqiqiy naqd foyda: 345 678 so'm        │
│  Nasiyada kutilmoqda:  23 456 so'm        │
└───────────────────────────────────────────┘
```

**Ombor Qiymati:**
```
┌─ Ombor holati ────────────────────────────┐
│  Ombor qiymati: 3 456 000 so'm  123 dona │
│                                           │
│  Top 5 mahsulot (tannarx × miqdor):       │
│  1. SHAMPUN     1 200 000 so'm  30 dona  │
│  2. YUVUVCHI      800 000 so'm  20 dona  │
│  ...                                      │
└───────────────────────────────────────────┘
```

### Tab 2: Nasiya

**Sarlavha:**
```
Ko'chadagi qarz
Jami: 89 000 so'm  ·  5 ta aktiv qarz
```

**Nasiya karta (har bir qarzdor uchun):**
```
┌─────────────────────────────────────────────────────┐
│ [🔴 muddati o'tgan] SARDOR KARIMOV  [Nasiya]        │
│ OOO Karimov firma                                   │
│ Sana: 27.03.2026  Muddat: 31.03.2026                │
│                                                     │
│ Jami:     15 000 so'm                               │
│ To'langan: 6 500 so'm  ████████░░░░░░░░ 43%        │
│ Qoldi:     8 500 so'm                               │
│                                                     │
│ To'lovlar tarixi:                                   │
│   25.03 — 5 000 so'm  (1-chi to'lov)               │
│   26.03 — 1 500 so'm  (2-chi to'lov)               │
│                                                     │
│ [💰 To'lov qabul qilish]                           │
└─────────────────────────────────────────────────────┘
```

**Status ikonlari:**
- `debt` (to'lanmagan): 🔴 qizil pulsing dot
- `partial` (qisman): 🟡 to'q sariq dot
- `paid` (to'langan): ✅ yashil check

**Muddati o'tgan** → karta `debt-row-overdue` background (qizilroq)
**Qisman to'langan** → karta `debt-row-partial` background (sariqroq)

### Tab 3: Mahsulotlar

**Sarlavha:**
```
Mahsulotlar bo'yicha savdo statistikasi
[Davr] filtrlanmoqda
```

**Mahsulot statistika jadvali:**
```
┌─────────────────────────────────────────────────────────────┐
│ Mahsulot         Sotildi  Daromad      Tannarx    Foyda    │
├─────────────────────────────────────────────────────────────┤
│ SHAMPUN           45 dona  450 000 so'm  270 000  180 000  │
│ YUVUVCHI          23 dona  230 000 so'm  138 000   92 000  │
│ ...                                                         │
└─────────────────────────────────────────────────────────────┘
```
Mobile: `overflow-x-auto` wrapper

**Top 5 Foydali Mahsulotlar:**
```
┌─ Top 5 Foydali Mahsulotlar ─────────────────────────────────┐
│ #1 SHAMPUN    180 000 so'm foyda  [═══════════════════] 100%│
│ #2 YUVUVCHI    92 000 so'm foyda  [═══════════] 51%        │
│ ...                                                         │
└─────────────────────────────────────────────────────────────┘
```

### Tab 4: Savdolar

**Savdolar ro'yxati (teskari tartibda):**
```
📅 27 Mart, 2026
┌─ Sardor Karimov · Naqd · ✅ paid ─────── 15 000 so'm ─┐
│ SHAMPUN × 2 = 10 000  YUVUVCHI × 1 = 5 000           │
└────────────────────────────────────────────────────────┘
┌─ Mijozсиз · Karta · ✅ paid ──────────── 8 500 so'm ──┐
│ KREM × 3 = 8 500                                      │
└────────────────────────────────────────────────────────┘
```

Mobile: card-list view (table yo'q)

**Status badge ranglari:**
- `paid`: `bg-emerald-100 text-emerald-700`
- `debt`: `bg-red-100 text-red-700`
- `partial`: `bg-amber-100 text-amber-700`

### Kunni O'chirish Modali
```
Kunni o'chirishni tasdiqlaysizmi?
27.03.2026 sanasidagi barcha savdolar o'chiriladi
va mahsulotlar omborga qaytariladi.

[Bekor qilish]  [🗑 Ha, o'chirish]
```

### Alpine.js State (`reportsApp()`)
```js
{
  activeTab: 'overview',   // 'overview'|'nasiya'|'products'|'sales'
  clearDayModal: false,
  expandedSaleId: null,
  payModal: { open: false, sale: null, amount: '', date: today, notes: '' },

  openClearDay(),
  clearDay(),
  openPayModal(sale),
  submitPayment(),
}
```

---

## 11. MOBILE-FIRST RESPONSIVE QOIDALAR

### Breakpointlar (Tailwind)
| Prefix | Min-width | Holat |
|---|---|---|
| *(yo'q)* | 0px | Mobile — asosiy |
| `sm:` | 640px | Kichik tablet |
| `md:` | 768px | Tablet / Desktop |
| `lg:` | 1024px | Desktop |
| `xl:` | 1280px | Katta desktop |
| `2xl:` | 1536px | Ultra-wide |

### Har Sahifada Umumiy Qoidalar
1. **Header:** `px-4 md:px-8`, `h-14 md:h-16`
2. **Main content:** `p-4 md:p-8`
3. **Modal form grids:** `grid-cols-1 sm:grid-cols-2`
4. **Product grids:** `grid-cols-1 md:grid-cols-2 xl:grid-cols-3 2xl:grid-cols-4`
5. **Tugma matni:** `<span class="hidden sm:inline">Matn</span>` — mobilda faqat icon

### POS Sahifasi Mobile
- Savatcha: `position: fixed`, `bottom: 0`, `width: 100%`, `height: 90dvh`
- Yopiq holat: `transform: translateY(100%)`
- Ochiq holat: `transform: translateY(0)` (Alpine `:class="showCartMobile ? 'mobile-open' : ''"`)
- Backdrop: `z-[199] bg-slate-900/40 backdrop-blur-sm`
- Bottom action bar: `position: fixed`, `bottom: 0`, `z-[150]`, `md:hidden`
- Bottom bar yuklanishi: `pb-24 md:pb-4` (mahsulot grid)

---

## 12. YASHIRIN QOIDALAR VA MUHIM DETALLAR

### Raqam Formatlash (JavaScript)
```js
// 1234567 → "1 234 567 so'm"
function formatMoney(n) {
  return parseFloat(n).toFixed(0)
    .replace(/\B(?=(\d{3})+(?!\d))/g, ' ') + " so'm";
}
```

### Matnlar (Uzbek Copy)
```
Muvaffaqiyatli:   "...muvaffaqiyatli qo'shildi!"
                  "...muvaffaqiyatli yangilandi!"
                  "...muvaffaqiyatli o'chirildi!"
Nasiya xato:      "Nasiya to'lovida mijoz tanlash shart!"
Stok xato:        "«{name}» uchun yetarli stok yo'q. Omborda: N dona."
To'lov:           "To'lov muvaffaqiyatli qabul qilindi!"
```

### AJAX Muvaffaqiyat/Xato Ishlov Berish
Barcha API calllar JSON response qaytaradi:
```json
{"success": true, "message": "...", "data": {...}}
{"success": false, "message": "Xato matni"}
```

Xato ko'rsatish: `toast/notification` yoki modal ichida `errors` object.

### Alpine.js `x-cloak`
Barcha `x-show` elementlarida `x-cloak` bo'lishi kerak, CSS:
```css
[x-cloak] { display: none !important; }
```

### `_method` Spoofing
Laravel `PUT`, `DELETE` methodlari uchun form ichida:
```html
<input type="hidden" name="_method" value="PUT">
```
Yoki fetch da: `method: 'POST'`, body da `_method: 'PUT'`

CSRF: `X-CSRF-TOKEN` header yoki `_token` field.

### Foto Upload
- `Content-Type: multipart/form-data`
- Yangilashda foto bo'lmasa, faqat matn maydonlari yuborilsa ham ishlaydi
- Eski foto server tomonida `Storage::delete()` bilan o'chiriladi

### Savdo O'chirilganda
Ombor avtomatik tiklanadi: `product.quantity += sale_item.quantity`

### Nasiya Uchun Majburiy Shartlar
- `customer_id` tanlash shart
- `payment_method === 'nasiya'` bo'lsa `due_date` ixtiyoriy lekin tavsiya etiladi

---

## 13. TOAST / NOTIFICATION TIZIMI

Barcha AJAX operatsiyalardan keyin foydalanuvchiga xabar ko'rsatilishi kerak:

```
✅ Mahsulot muvaffaqiyatli qo'shildi!   [top-right, 3 soniya]
❌ Xato: Stok yetarli emas              [top-right, 5 soniya, qizil]
⚠️ Nasiya uchun mijoz tanlang           [top-right, 4 soniya, to'q sariq]
```

Toast dizayni:
- `fixed top-4 right-4 z-[9999]`
- `rounded-xl shadow-lg border px-4 py-3`
- Avtomatik yopilish: 3-5 soniya
- Animatsiya: `translateX(100%)` → `translateX(0)` → `translateX(100%)`

---

## 14. SAHIFALARARO NAVIGATSIYA

| Sahifa | URL | Sidebar active | Ranglar |
|---|---|---|---|
| Ombor | `/products` | `active-blue` | blue |
| Mijozlar | `/customers` | `active-violet` | violet |
| Savdo | `/sales` | `active-green` | green |
| Hisobotlar | `/reports` | `active-amber` | amber |
| Root | `/` | → redirect `/products` | |

---

## 15. PERFORMANCE VA UX NOZIK JIHATLARI

1. **Lazy map init:** Leaflet xaritalar faqat `x-if="customer.lat && customer.lng"` bo'lganda yaratiladi
2. **Skeleton loading:** Ma'lumotlar yuklanayotganda skeleton placeholder ko'rsatish
3. **Optimistic updates:** Saqlash tugmasini bosganida darhol UI ni yangilash, xato kelsa rollback
4. **Debounce:** Qidiruv inputda 300ms debounce
5. **Empty states:** Har bir bo'sh ro'yxat uchun alohida empty state blok
6. **Disable during submit:** `isSubmitting` holatida tugmalarni disable qilish
7. **Enter key:** Modal formasida Enter → submit
8. **Escape key:** Modal → yopish
9. **Focus trap:** Modal ochiq bo'lganda tab fokuslari modal ichida qolishi
10. **Scroll lock:** Modal ochiq bo'lganda `body` scroll bloklanishi

---

## 16. MUHIM ESLATMALAR FRONTEND AI UCHUN

- **Barcha matnlar Uzbek tilida** — yuqoridagi copy ni aynan ishlating
- **Raqamlar:** `1 234 567 so'm` formatida (bo'sh joy separator, vergul emas)
- **Mahsulot nomlari:** Har doim UPPERCASE (server tomonida `strtoupper()`)
- **Sana formati UI da:** `27.03.2026` (d.m.Y) — API da `Y-m-d`
- **Alpine.js** — boshqa framework ishlatmang (React, Vue yo'q)
- **Tailwind CDN** — build yo'q, barcha klasslar CDN orqali
- **SVG ikonlar** — Heroicons style, inline SVG, hech qanday icon library yo'q
- **Leaflet faqat Mijozlar sahifasida** — boshqa joylarda kerak emas
- **`@section('head')`** — layout `@yield('head')` ishlatadi, `@push` emas
- **CSRF token:** `<meta name="csrf-token">` dan olinadi → `fetch` headeriga qo'shiladi

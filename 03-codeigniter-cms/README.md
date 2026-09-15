# 03. CMS Product & Sales (CodeIgniter 4)

Aplikasi CMS untuk mengelola **Users**, **Products**, dan **Transactions** dengan
relasi antar tabel — jawaban soal #3 technical test WARPSTATION.

Dokumen ini ditujukan untuk reviewer/developer: cara setup, arsitektur, aturan
bisnis (stok), dan cara menguji.

## Stack

- CodeIgniter 4 (PHP 8.1+)
- PostgreSQL (`crud_db`, driver `Postgre`)
- Layout custom sidebar (`app/Views/layouts/main.php`) + `public/css/app.css`

## Setup & Run

```bash
composer install

# konfigurasi di .env (default):
# database.default.hostname = localhost
# database.default.database = crud_db
# database.default.username = postgres
# database.default.DBDriver  = Postgre

php spark migrate
php spark db:seed UserSeeder
php spark db:seed ProductSeeder

php spark serve            # http://localhost:8080
```

## Struktur Database

```
users            products                transactions
-------          ----------              -------------
id PK            id PK                   id PK
name             product_name            user_id    FK -> users.id    (CASCADE)
created_at       price       (10,2)      product_id FK -> products.id (CASCADE)
updated_at       qty_in_stock            payment_method (cash|transfer)
                 created_at/updated_at   qty, created_at/updated_at
```

FK `CASCADE/CASCADE` ⇒ menghapus user/product ikut menghapus transaksi terkait.

## Aturan Bisnis — Manajemen Stok

Semua operasi stok dibungkus **DB transaction** (`transBegin`/`transRollback`/
`transCommit`) sehingga kegagalan validasi tidak menghasilkan stok bocor.

| Operasi | Efek pada `qty_in_stock` |
|---|---|
| **store** | Kurangi sejumlah `qty`; bila stok < qty → error `Stock tidak cukup` |
| **update** | Qty lama dikembalikan ke produk lama, lalu qty baru dikurangkan dari produk (baru). Tersedia = `stok_sekarang + (qty lama bila produk sama)`; bila kurang → error. Produk beda → produk lama restok, produk baru terkurang |
| **delete** | Qty transaksi dikembalikan ke produk |

Validasi model `Transaction`: `qty` wajib integer **minimal 1**; `payment_method`
hanya `cash`/`transfer`; `user_id`/`product_id` integer.

## Routing (`app/Config/Routes.php`)

Update memakai **PUT**, delete memakai **DELETE** — via *method spoofing* bawaan
CI4 (form HTML hanya bisa GET/POST, jadi form membawa hidden field `_method`):
`POST .../update/:id` + `_method=PUT` dirouting sebagai PUT, `POST .../delete/:id`
+ `_method=DELETE` sebagai DELETE. Tombol delete di index memakai form mini
(hidden `_method`), bukan lagi link GET.

| Method | Path | Controller::method |
|---|---|---|
| GET | `/` | Home::index (dashboard rekap jumlah users/products/transactions) |
| GET/POST | `users`, `users/create`, `users/store` | index/create/store |
| GET/**PUT** | `users/edit/(:num)`, `users/update/(:num)` | edit/update |
| **DELETE** | `users/delete/(:num)` | delete |
| GET/POST | `products`, `products/create`, `products/store` | index/create/store |
| GET/**PUT** | `products/edit/(:num)`, `products/update/(:num)` | edit/update |
| **DELETE** | `products/delete/(:num)` | delete |
| GET/POST | `transaction`, `transaction/create`, `transaction/store` | index/create/store |
| GET/**PUT** | `transaction/edit/(:num)`, `transaction/update/(:num)` | edit/update |
| **DELETE** | `transaction/delete/(:num)` | delete |

## Arsitektur

```
app/Controllers/  UserController | ProductController | TransactionController | Home
app/Models/       User | Product | Transaction   (allowedFields, timestamps,
                                     validationRules + pesan error Indonesia)
app/Views/        layouts/main.php (sidebar) + per-modul index/create/edit.php
app/Database/     Migrations: AddUserTables, AddProductTables, AddTransactionTables
                  Seeds: UserSeeder, ProductSeeder
public/css/app.css
```

- Transaksi index memakai join query builder (`select + join + findAll`) — CI4
  Model tidak punya ORM relasi bawaan.
- Controller meng-instance model di constructor; `store()`/`update()` memakai
  `insert()`/`update()` yang menjalankan validasi model; gagal → redirect back +
  flash `errors`, sukses → redirect + flash `success`.

## Pengujian Manual

`php spark serve`, lalu (contoh dengan curl):

```bash
# store valid: stok berkurang
curl -X POST localhost:8080/transaction/store \
  -d user_id=1 -d product_id=1 -d qty=3 -d payment_method=cash

# qty > stok -> error, stok utuh
curl -X POST localhost:8080/transaction/store -d user_id=1 -d product_id=1 \
  -d qty=999 -d payment_method=cash

# qty=0 -> gagal validasi qty minimal 1
curl -X POST localhost:8080/transaction/store -d user_id=1 -d product_id=1 \
  -d qty=0 -d payment_method=cash

# update produk sama / beda -> stok disesuaikan
curl -X POST localhost:8080/transaction/update/1 -d user_id=1 -d product_id=2 \
  -d qty=4 -d payment_method=transfer

# delete -> qty kembali ke produk
curl localhost:8080/transaction/delete/1
```

Verifikasi cepat di SQL: `SELECT id, product_name, qty_in_stock FROM products;`

## Batasan & Roadmap

- Delete/update lewat method spoofing (bukan HTTP verb "asli" dari protokol) —
  pola standar untuk app server-rendered; REST API murni bisa memakai `-X PUT/-X DELETE`.
- Tidak ada log audit stok — tambahkan tabel/event bila butuh void/refund atau
  rekonstilasi stok.
- Seeder dibuat idempotent sederhana (tidak cek duplikat) — jalankan sekali atau
  reset tabel dulu.

# Technical Test WARPSTATION

Repositori pengerjaan technical test, terdiri atas 3 tugas dalam 1 workspace:

| No | Tugas | Teknologi | Status |
|---|---|---|---|
| 01 | Fruit Detection (YOLOv8 + OpenCV) | Python | ✅ selesai |
| 02 | IoT Camera — Kontrol Kamera (OpenCV + V4L2) | Python | ✅ selesai |
| 03 | CMS Product & Sales (CodeIgniter) | PHP | ✅ selesai |

Dua tugas Python berbagi **satu virtual environment** di root repo (`.venv/`).

---

## Setup (Python — tugas 01 & 02)

Prasyarat:

- Python 3.x
- Linux + webcam (USB UVC / laptop builtin)
- `v4l-utils` (`v4l2-ctl`) untuk kontrol shutter/ISO — opsional

```bash
# sekali saja, dari root repo
python3 -m venv .venv
source .venv/bin/activate
pip install -r requirements.txt
```

```bash
# selanjutnya, cukup activate lalu jalankan project apa pun:
source .venv/bin/activate
python 01-yolo-fruit-detection/main.py        # webcam default
python 02-camera-access/main.py               # /dev/video0
python 02-camera-access/main.py /dev/video1   # device spesifik
python 02-camera-access/main.py --selftest    # tanpa kamera
```

`requirements.txt` root adalah gabungan dependensi kedua project (`numpy`,
`opencv-python`, `ultralytics`).

---

## 01. Fruit Detection

Deteksi buah real-time dari webcam dengan model YOLOv8 (`model/fruit-detection.pt`),
bounding box + label + confidence digambar dengan OpenCV.

- Model di atas threshold `CONF_THRESHOLD = 0.5` (edit di atas `main.py`)
- Detail lengkap: [`01-yolo-fruit-detection/`](01-yolo-fruit-detection/)

## 02. IoT Camera — Kontrol Kamera

Live preview kamera + capture keyboard + burst mode + kontrol shutter/ISO via V4L2
(`v4l2-ctl`). Foto tersimpan di `captures/`.

| Tombol | Aksi |
|---|---|
| `c` / `SPACE` | Capture 1 foto |
| `b` (tahan) | Burst capture selama ditahan |
| `+` / `-` | Shutter ±50 (exposure) |
| `[` / `]` | ISO/gain −1 / +1 |
| `h` | Bantuan |
| `q` / `ESC` | Keluar |

- Detail lengkap: [`02-camera-access/README.md`](02-camera-access/README.md)

## 03. CMS (CodeIgniter)

Aplikasi CMS Product & Sales berbasis CodeIgniter 4 dan PostgreSQL dengan fitur:

- Dashboard ringkasan jumlah pengguna, produk, dan transaksi
- CRUD users, products, dan transactions
- Validasi data di level model
- Perhitungan total harga transaksi
- Sinkronisasi stok otomatis saat transaksi dibuat, diubah, atau dihapus
- Database transaction untuk menjaga konsistensi transaksi dan stok

```bash
cd 03-codeigniter-cms
composer install
php spark migrate
php spark db:seed UserSeeder
php spark db:seed ProductSeeder
php spark serve
```

- Akses aplikasi: `http://localhost:8080`
- Dokumentasi lengkap: [`03-codeigniter-cms/README.md`](03-codeigniter-cms/README.md)

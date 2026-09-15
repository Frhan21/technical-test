# 02. IoT Camera — Kontrol Kamera (OpenCV)

Live preview kamera + capture via keyboard + burst mode + kontrol shutter/ISO.
Penuhi soal #2 (IoT & Embedded) technical test WARPSTATION.

## Dependency

- Python 3.x
- `opencv-python` — `pip install -r requirements.txt`
- Linux + `v4l2-ctl` (paket `v4l-utils`) untuk kontrol shutter/ISO — opsional, tanpa ini program tetap jalan (resolusi/fps via OpenCV, shutter/ISO dilewati dengan warning)
- Kamera: webcam USB UVC / laptop builtin

## Cara Run

```bash
python -m venv .venv && source .venv/bin/activate
pip install -r requirements.txt
python main.py              # kamera default (/dev/video0)
python main.py /dev/video1  # device spesifik
python main.py --selftest   # test logika murni, tanpa kamera
```

Foto tersimpan di `captures/` (dibuat otomatis), nama `capture_YYYYmmdd_HHMMSS_mmm.jpg`.

## Key Mapping

| Tombol | Aksi |
|---|---|
| `c` / `SPACE` | Capture 1 foto |
| `b` (tahan) | Burst capture — foto terus selama ditahan, lepas = berhenti |
| `+` / `-` | Shutter +50 / −50 (V4L2 `exposure_time_absolute`, clamp 9–2500) |
| `[` / `]` | ISO/gain −1 / +1 (V4L2 `gain`, clamp 4–8) |
| `h` | Tampilkan/sembunyikan bantuan |
| `q` / `ESC` | Keluar |

HUD di kiri atas menampilkan resolusi, FPS aktual, shutter, ISO, jumlah foto, status burst.

## Parameter Kamera (edit di atas `main.py`)

```python
DEVICE = "/dev/video0"
WIDTH, HEIGHT, FPS = 1280, 720, 30
SHUTTER, ISO = 123, 4   # exposure_time_absolute ~ shutter speed; gain ~ ISO analog
```

## Catatan Implementasi

- **Shutter & ISO**: webcam UVC tidak punya kontrol ISO; `gain` (analog gain 0–48 dB) jadi padanannya. Saat shutter diset manual, `auto_exposure=1` (Manual Mode) di-set otomatis via `v4l2-ctl`.
- **Burst "tahan tombol"**: `cv2.waitKey` tidak punya event key-up, jadi tombol ditahan terdeteksi dari auto-repeat X11/key repeat Wayland: event `b` yang terus datang dianggap masih ditahan; berhenti >0.4 s dianggap dilepas (`HOLD_TIMEOUT`).
- **Kamera gagal**: dicoba `/dev/video0..3`; kalau semua gagal → pesan error jelas + exit 1. Kamera terputus saat jalan juga di-handle.
- Backend OpenCV dipaksa `CAP_V4L2`; di build ini `CAP_ANY` mengabaikan `cap.set()` (format MJPG/ternegosiasi ulang).

## Spek Pengembangan

- OS: Linux (Arch-based), Wayland/GNOME
- Python 3.14.7, opencv-python 5.0.0
- Kamera uji: USB2.0 UVC cam (MJPG max 1600x1200@30)

import subprocess
import sys
import time
from datetime import datetime
from pathlib import Path

import cv2

cv2.setNumThreads(0)

DEVICE = "/dev/video0"
WIDTH, HEIGHT, FPS = 1280, 720, 30
SHUTTER, ISO = 123, 4  # V4L2: exposure_time_absolute (9..2500), gain (4..8 ~ ISO)
CAPTURE_DIR = Path("captures")
BURST_INTERVAL = 0.2   # detik antar foto saat burst
HOLD_TIMEOUT = 0.4     # tanpa event key segini lama -> tombol dianggap dilepas
SHUTTER_MIN, SHUTTER_MAX = 9, 2500
ISO_MIN, ISO_MAX = 4, 8
SHUTTER_STEP = 50


def make_filename(now: datetime) -> Path:
    return CAPTURE_DIR / f"capture_{now:%Y%m%d_%H%M%S}_{now.microsecond // 1000:03d}.jpg"


class BurstHold:
    """Deteksi tahan-tombol dari auto-repeat waitKey (tanpa event key-up)."""

    def __init__(self, hold_timeout: float = HOLD_TIMEOUT, interval: float = BURST_INTERVAL):
        self.hold_timeout = hold_timeout
        self.interval = interval
        self.last_key = -1e9
        self.next_shot = 0.0
        self.active = False

    def key(self, now: float) -> bool:
        self.last_key = now
        if not self.active or now >= self.next_shot:
            self.active = True
            self.next_shot = now + self.interval
            return True
        return False

    def update(self, now: float) -> bool:
        if self.active and now - self.last_key > self.hold_timeout:
            self.active = False
        return self.active


def apply_camera_controls(shutter: int, iso: int) -> bool:
    try:
        r = subprocess.run(
            ["v4l2-ctl", f"--device={DEVICE}",
             "--set-ctrl=auto_exposure=1",
              f"--set-ctrl=exposure_time_absolute={shutter}",
              f"--set-ctrl=gain={iso}"],
            capture_output=True, text=True)
    except FileNotFoundError:
        print("warning: v4l2-ctl tidak ada, shutter/ISO dilewati")
        return False
    if r.returncode != 0:
        print(f"warning: set shutter/ISO gagal: {r.stderr.strip()}")
        return False
    return True


def open_camera():
    # backend harus eksplisit: CAP_ANY di build ini mengabaikan cap.set()
    devices = [DEVICE] if DEVICE.startswith("/dev/video") and DEVICE != "/dev/video0" \
        else [DEVICE] + [f"/dev/video{i}" for i in range(4)]
    for dev in devices:
        cap = cv2.VideoCapture(dev, cv2.CAP_V4L2)
        cap.set(cv2.CAP_PROP_FOURCC, cv2.VideoWriter_fourcc(*"MJPG"))
        cap.set(cv2.CAP_PROP_FRAME_WIDTH, WIDTH)
        cap.set(cv2.CAP_PROP_FRAME_HEIGHT, HEIGHT)
        cap.set(cv2.CAP_PROP_FPS, FPS)
        if cap.isOpened():
            ok, frame = cap.read()
            if ok:
                print(f"kamera: {dev} @ {frame.shape[1]}x{frame.shape[0]}")
                return cap
        cap.release()
    sys.exit(f"error: kamera tidak terdeteksi (dicoba: {DEVICE}, /dev/video0..3)")


def draw_hud(frame, lines, burst_active, flash):
    cv2.rectangle(frame, (0, 0), (330, 20 * len(lines) + 10), (0, 0, 0), -1)
    for i, t in enumerate(lines):
        cv2.putText(frame, t, (8, 20 * (i + 1)), cv2.FONT_HERSHEY_SIMPLEX, 0.5, (255, 255, 255), 1)
    if burst_active:
        cv2.putText(frame, "BURST", (frame.shape[1] - 90, 30), cv2.FONT_HERSHEY_SIMPLEX, 0.8, (0, 0, 255), 2)
    if flash and time.time() < flash:
        cv2.rectangle(frame, (0, 0), (frame.shape[1], frame.shape[0]), (255, 255, 255), 8)


HELP = [
    "c/SPACE : capture   b (tahan) : burst",
    "+/- : shutter   [/] : ISO",
    "h : bantuan   q/ESC : keluar",
]


def main():
    global DEVICE
    if "--selftest" in sys.argv:
        run_selftest()
        print("selftest OK")
        return

    DEVICE = sys.argv[1] if len(sys.argv) > 1 and not sys.argv[1].startswith("-") else DEVICE

    cap = open_camera()
    apply_camera_controls(SHUTTER, ISO)
    CAPTURE_DIR.mkdir(exist_ok=True)

    shutter, iso = SHUTTER, ISO
    count = 0
    burst = BurstHold()
    flash, saved_msg = 0.0, ""
    show_help = True
    fps_ema = 0.0
    last = time.time()

    while True:
        ok, frame = cap.read()
        if not ok:
            sys.exit("error: frame gagal dibaca, kamera terputus?")

        now = time.time()
        fps_ema = 0.9 * fps_ema + 0.1 * (1 / max(now - last, 1e-6)) if fps_ema else 1 / max(now - last, 1e-6)
        last = now

        burst_active = burst.update(now)
        lines = [
            f"res: {frame.shape[1]}x{frame.shape[0]}  fps: {fps_ema:.0f}",
            f"shutter: {shutter}  iso/gain: {iso}",
            f"foto: {count}  burst: {'AKTIF' if burst_active else '-'}",
        ] + (HELP if show_help else [])
        draw_hud(frame, lines, burst_active, flash)
        if saved_msg and time.time() < flash:
            cv2.putText(frame, saved_msg, (8, frame.shape[0] - 12), cv2.FONT_HERSHEY_SIMPLEX, 0.55, (0, 255, 0), 2)

        cv2.imshow("IoT Camera Control", frame)
        key = cv2.waitKeyEx(1) & 0xFFFF
        if key in (ord("q"), 27):
            break
        elif key in (ord("c"), ord(" ")):
            fn = make_filename(datetime.now())
            cv2.imwrite(str(fn), frame)
            count += 1
            saved_msg = f"tersimpan: {fn.name}"
            flash = time.time() + 0.5
        elif key == ord("b"):
            if burst.key(now):
                fn = make_filename(datetime.now())
                cv2.imwrite(str(fn), frame)
                count += 1
                saved_msg = f"burst: {fn.name}"
                flash = time.time() + 0.3
        elif key in (ord("+"), ord("=")):
            shutter = min(shutter + SHUTTER_STEP, SHUTTER_MAX)
            apply_camera_controls(shutter, iso)
        elif key == ord("-"):
            shutter = max(shutter - SHUTTER_STEP, SHUTTER_MIN)
            apply_camera_controls(shutter, iso)
        elif key == ord("]"):
            iso = min(iso + 1, ISO_MAX)
            apply_camera_controls(shutter, iso)
        elif key == ord("["):
            iso = max(iso - 1, ISO_MIN)
            apply_camera_controls(shutter, iso)
        elif key == ord("h"):
            show_help = not show_help

    cap.release()
    cv2.destroyAllWindows()
    print(f"selesai, {count} foto di {CAPTURE_DIR}/")


def run_selftest():
    fn = make_filename(datetime(2026, 9, 15, 5, 43, 21, 123456))
    assert fn.name == "capture_20260915_054321_123.jpg", fn.name

    b = BurstHold(hold_timeout=0.4, interval=0.2)
    assert b.key(0.0) is True            # tekan pertama -> capture
    assert b.key(0.05) is False          # auto-repeat cepat -> rate-limited
    assert b.key(0.2) is True            # interval tercapai -> capture lagi
    assert b.update(0.3) is True         # masih dalam hold_timeout -> tetap aktif
    assert b.update(0.7) is False        # 0.7-0.2=0.5 > 0.4 -> dianggap dilepas
    assert b.key(0.8) is True            # tekan lagi -> capture lagi
    assert b.key(1.0) is True            # interval tercapai
    assert b.update(1.15) is True


if __name__ == "__main__":
    main()

import cv2
from ultralytics import YOLO

MODEL_PATH = "model/fruit-detection.pt"
CONF_THRESHOLD = 0.5

model = YOLO(MODEL_PATH)


def draw_boxes(frame, results):
    for box in results[0].boxes:
        conf = float(box.conf[0])
        if conf < CONF_THRESHOLD:
            continue
        x1, y1, x2, y2 = map(int, box.xyxy[0])
        label = f"{model.names[int(box.cls[0])]} {conf:.2f}"
        cv2.rectangle(frame, (x1, y1), (x2, y2), (0, 255, 0), 2)
        cv2.putText(frame, label, (x1, y1 - 10),
                    cv2.FONT_HERSHEY_SIMPLEX, 0.6, (0, 255, 0), 2)
    return frame


def main(source):
    cap = cv2.VideoCapture(source)
    if not cap.isOpened():
        raise RuntimeError(f"Gagal membuka source: {source}")

    cv2.namedWindow("Fruit Detection")  # agar cek window-close tidak break di iterasi pertama
    while True:
        ret, frame = cap.read()
        if not ret:
            break

        results = model(frame, verbose=False)
        # jika window sudah diclose, imshow akan merecreate window-nya,
        # jadi cek dulu sebelum imshow
        if cv2.getWindowProperty("Fruit Detection", cv2.WND_PROP_VISIBLE) < 1:
            break

        frame = draw_boxes(frame, results)

        cv2.imshow("Fruit Detection", frame)
        if cv2.waitKey(1) & 0xFF == ord("q"):
            break

    cap.release()
    cv2.destroyAllWindows()


if __name__ == "__main__":
    import sys

    # kosong = webcam, atau path gambar/video/URL rtsp
    main(sys.argv[1] if len(sys.argv) > 1 else 0)

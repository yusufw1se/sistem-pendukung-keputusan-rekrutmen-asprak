import pandas as pd
from sklearn.ensemble import RandomForestClassifier
from sklearn.metrics import accuracy_score, classification_report
import joblib
import os

# 1. Pastikan folder 'model' ada
script_dir = os.path.dirname(os.path.abspath(__file__))
model_dir = os.path.join(script_dir, '..', 'model')
if not os.path.exists(model_dir):
    os.makedirs(model_dir)

print("Memulai proses pelatihan model Random Forest...")

# 2. Load data latih
try:
    dataset_dir = os.path.join(script_dir, '..', 'dataset')
    train_df = pd.read_csv(os.path.join(dataset_dir, 'data_latih.csv'))
    test_df = pd.read_csv(os.path.join(dataset_dir, 'data_uji.csv'))
except FileNotFoundError:
    print("Error: File dataset tidak ditemukan. Jalankan file split_data.py terlebih dahulu!")
    exit()

# 3. Definisikan fitur dan target
features = ['IPK', 'Nilai KHS Mata Kuliah Syarat', 'Nilai Praktikum Mata Kuliah Syarat']
target = 'Label'

X_train = train_df[features]
y_train = train_df[target]
X_test = test_df[features]
y_test = test_df[target]

# 4. Inisialisasi dan Latih Model
# n_estimators=100 dan class_weight='balanced' untuk data yang mungkin imbalanced
model = RandomForestClassifier(n_estimators=100, random_state=42, class_weight='balanced')
model.fit(X_train, y_train)

# 5. Evaluasi
predictions = model.predict(X_test)
acc = accuracy_score(y_test, predictions)

print(f"========================================")
print(f"Pelatihan Selesai!")
print(f"Akurasi Model: {acc * 100:.2f}%")
print(f"========================================")
print("\nLaporan Klasifikasi:")
print(classification_report(y_test, predictions))

# 6. Simpan Model (Save as model_rf.pkl)
model_path = os.path.join(model_dir, 'model_rf.pkl')
joblib.dump(model, model_path)
print(f"\nModel berhasil disimpan/diperbarui di lokasi: {model_path}")
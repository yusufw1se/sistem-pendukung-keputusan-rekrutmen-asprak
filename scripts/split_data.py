import pandas as pd
from sklearn.model_selection import train_test_split
import os

# Menentukan lokasi direktori data (membuat folder 'dataset' jika belum ada
data_dir = 'dataset'
if not os.path.exists('dataset'):
    os.makedirs('dataset')

print("Memulai proses pembagian data (otomatis menimpa file lama jika ada)...")

# 1. Load data
# Ditambahkan huruf r di depan string agar backslash Windows aman
df = pd.read_csv('dataset\Dataset_Rekrutmen_Labeled.csv')

# 2. Split data (80% latih, 20% uji)
train_df, test_df = train_test_split(df, test_size=0.2, random_state=42)

# 3. Simpan ke folder dataset
train_df.to_csv(os.path.join(data_dir, 'data_latih.csv'), index=False)
test_df.to_csv(os.path.join(data_dir, 'data_uji.csv'), index=False)

print("Berhasil!")
print(f"Data latih: {len(train_df)} baris")
print(f"Data uji: {len(test_df)} baris")
print("File tersimpan di folder /dataset")
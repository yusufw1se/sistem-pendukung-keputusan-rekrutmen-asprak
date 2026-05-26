-- ============================================================
-- DATABASE SETUP: SPK Rekrutmen Asisten Laboratorium
-- Metode: AHP + SAW + Random Forest
-- Copy-paste seluruh SQL ini ke phpMyAdmin atau MySQL CLI
-- ============================================================

CREATE DATABASE IF NOT EXISTS db_spk_asisten
CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;

USE db_spk_asisten;

-- ============================================================
-- 1. Tabel Kriteria (menyimpan bobot hasil AHP)
-- ============================================================
DROP TABLE IF EXISTS hasil;
DROP TABLE IF EXISTS alternatif;
DROP TABLE IF EXISTS kriteria;
DROP TABLE IF EXISTS ahp_log;
DROP TABLE IF EXISTS model_info;

CREATE TABLE kriteria (
    id_kriteria INT AUTO_INCREMENT PRIMARY KEY,
    kode_kriteria VARCHAR(5) NOT NULL,
    nama_kriteria VARCHAR(100) NOT NULL,
    jenis VARCHAR(10) DEFAULT 'benefit' COMMENT 'benefit atau cost',
    bobot DECIMAL(10,6) DEFAULT 0.333333
) ENGINE=InnoDB;

INSERT INTO kriteria (kode_kriteria, nama_kriteria, jenis, bobot) VALUES
('C1', 'IPK', 'benefit', 0.333333),
('C2', 'Nilai KHS Mata Kuliah Syarat', 'benefit', 0.333333),
('C3', 'Nilai Praktikum Mata Kuliah Syarat', 'benefit', 0.333333);

-- ============================================================
-- 2. Tabel Alternatif (data pendaftar dari CSV)
-- ============================================================
CREATE TABLE alternatif (
    id_alternatif INT AUTO_INCREMENT PRIMARY KEY,
    NIM VARCHAR(30) NOT NULL,
    NAMA VARCHAR(255) NOT NULL,
    `Mata Kuliah Praktikum` VARCHAR(255) NOT NULL,
    IPK DECIMAL(4,2) NOT NULL,
    `Nilai KHS Mata Kuliah Syarat` DECIMAL(5,2) NOT NULL,
    `Nilai Praktikum Mata Kuliah Syarat` DECIMAL(6,2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ============================================================
-- 3. Tabel Hasil (skor SAW + prediksi Random Forest)
-- ============================================================
CREATE TABLE hasil (
    id_hasil INT AUTO_INCREMENT PRIMARY KEY,
    id_alternatif INT NOT NULL,
    r_ipk DECIMAL(10,6) DEFAULT 0,
    r_khs DECIMAL(10,6) DEFAULT 0,
    r_prak DECIMAL(10,6) DEFAULT 0,
    skor_akhir DECIMAL(10,6) DEFAULT 0,
    prediksi_ai VARCHAR(20) DEFAULT 'Belum Diproses',
    confidence DECIMAL(5,2) DEFAULT 0,
    ranking INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_alternatif) REFERENCES alternatif(id_alternatif) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ============================================================
-- 4. Tabel Log AHP (history perhitungan AHP + konsistensi)
-- ============================================================
CREATE TABLE ahp_log (
    id INT AUTO_INCREMENT PRIMARY KEY,
    matriks_perbandingan TEXT NOT NULL COMMENT 'JSON matriks pairwise',
    matriks_normalisasi TEXT COMMENT 'JSON matriks ternormalisasi',
    bobot_prioritas TEXT NOT NULL COMMENT 'JSON bobot prioritas',
    lambda_max DECIMAL(10,6) DEFAULT 0,
    consistency_index DECIMAL(10,6) DEFAULT 0,
    consistency_ratio DECIMAL(10,6) DEFAULT 0,
    is_consistent TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ============================================================
-- 5. Tabel Model Info (metrik model Random Forest)
-- ============================================================
CREATE TABLE model_info (
    id INT AUTO_INCREMENT PRIMARY KEY,
    accuracy DECIMAL(5,2) DEFAULT 0,
    precision_score DECIMAL(5,2) DEFAULT 0,
    recall_score DECIMAL(5,2) DEFAULT 0,
    f1_score DECIMAL(5,2) DEFAULT 0,
    feature_importance TEXT COMMENT 'JSON feature importance',
    classification_report TEXT,
    confusion_matrix TEXT COMMENT 'JSON confusion matrix',
    total_training_data INT DEFAULT 0,
    total_test_data INT DEFAULT 0,
    trained_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

<?php
include 'koneksi.php';

// ============================================================
// PROSES SAW (Simple Additive Weighting) + Random Forest AI
// ============================================================

// 1. Ambil bobot AHP dari tabel kriteria
$w1 = mysqli_fetch_assoc(mysqli_query($conn, "SELECT bobot FROM kriteria WHERE nama_kriteria='IPK'"))['bobot'] ?? 0.333;
$w2 = mysqli_fetch_assoc(mysqli_query($conn, "SELECT bobot FROM kriteria WHERE nama_kriteria='Nilai KHS Mata Kuliah Syarat'"))['bobot'] ?? 0.333;
$w3 = mysqli_fetch_assoc(mysqli_query($conn, "SELECT bobot FROM kriteria WHERE nama_kriteria='Nilai Praktikum Mata Kuliah Syarat'"))['bobot'] ?? 0.333;

// 2. Cek apakah ada data alternatif
$total = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM alternatif"))['c'];
if($total == 0) {
    echo "<script>alert('Tidak ada data pendaftar. Silakan upload CSV terlebih dahulu.'); window.location='upload.php';</script>";
    exit;
}

// 3. Ambil nilai MAX untuk normalisasi SAW (semua kriteria benefit)
$max = mysqli_fetch_assoc(mysqli_query($conn, "SELECT 
    MAX(IPK) as max_ipk, 
    MAX(`Nilai KHS Mata Kuliah Syarat`) as max_khs, 
    MAX(`Nilai Praktikum Mata Kuliah Syarat`) as max_prak 
    FROM alternatif"));

// 4. Kosongkan tabel hasil sebelumnya
mysqli_query($conn, "TRUNCATE TABLE hasil");

// 5. Ambil semua data alternatif
$query = mysqli_query($conn, "SELECT * FROM alternatif");
$all_data = [];
$saw_results = [];

while($row = mysqli_fetch_assoc($query)) {
    // Normalisasi SAW (benefit = nilai / max)
    $r1 = ($max['max_ipk'] != 0) ? ($row['IPK'] / $max['max_ipk']) : 0;
    $r2 = ($max['max_khs'] != 0) ? ($row['Nilai KHS Mata Kuliah Syarat'] / $max['max_khs']) : 0;
    $r3 = ($max['max_prak'] != 0) ? ($row['Nilai Praktikum Mata Kuliah Syarat'] / $max['max_prak']) : 0;
    
    // Skor akhir SAW = Σ (bobot × nilai normalisasi)
    $skor = ($w1 * $r1) + ($w2 * $r2) + ($w3 * $r3);
    
    $saw_results[] = [
        'id' => $row['id_alternatif'],
        'r_ipk' => $r1,
        'r_khs' => $r2,
        'r_prak' => $r3,
        'skor' => $skor,
        'ipk' => floatval($row['IPK']),
        'khs' => floatval($row['Nilai KHS Mata Kuliah Syarat']),
        'prak' => floatval($row['Nilai Praktikum Mata Kuliah Syarat'])
    ];
}

// 6. Prediksi AI menggunakan Random Forest (batch mode - efisien)
$python_path = 'C:\Users\yusufw1se\AppData\Local\Python\bin\python.exe';
$script_path = 'C:\xampp\htdocs\spk-asisten-praktikum\scripts\predict.py';
$model_path ='C:\xampp\htdocs\spk-asisten-praktikum\model\model_rf.pkl';

$ai_predictions = [];

if(file_exists($model_path) && file_exists($python_path)) {
    // Siapkan data untuk batch prediction
    $batch_input = [];
    foreach($saw_results as $sr) {
        $batch_input[] = [
            'id' => $sr['id'],
            'ipk' => $sr['ipk'],
            'khs' => $sr['khs'],
            'prak' => $sr['prak']
        ];
    }
    
    $json_input = json_encode($batch_input);
    
    // Kirim semua data sekaligus ke Python (via stdin)
    $descriptors = [
        0 => ['pipe', 'r'],  // stdin
        1 => ['pipe', 'w'],  // stdout
        2 => ['pipe', 'w']   // stderr
    ];
    
    $cmd = escapeshellarg($python_path) . ' ' . escapeshellarg($script_path);
    $process = proc_open($cmd, $descriptors, $pipes);
    
    if(is_resource($process)) {
        fwrite($pipes[0], $json_input);
        fclose($pipes[0]);
        
        $output = stream_get_contents($pipes[1]);
        fclose($pipes[1]);
        
        $errors = stream_get_contents($pipes[2]);
        fclose($pipes[2]);
        
        $exit_code = proc_close($process);
        
        if($exit_code === 0 && !empty($output)) {
            $predictions = json_decode($output, true);
            if(is_array($predictions)) {
                foreach($predictions as $pred) {
                    $ai_predictions[$pred['id']] = [
                        'prediction' => $pred['prediction'],
                        'confidence' => $pred['confidence']
                    ];
                }
            }
        }
    }
}

// 7. Urutkan berdasarkan skor (descending) untuk ranking
usort($saw_results, function($a, $b) {
    return $b['skor'] <=> $a['skor'];
});

// 8. Insert hasil ke database dengan ranking
$stmt = $conn->prepare("INSERT INTO hasil (id_alternatif, r_ipk, r_khs, r_prak, skor_akhir, prediksi_ai, confidence, ranking) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");

$rank = 1;
foreach($saw_results as $sr) {
    $id = $sr['id'];
    $r_ipk = $sr['r_ipk'];
    $r_khs = $sr['r_khs'];
    $r_prak = $sr['r_prak'];
    $skor = $sr['skor'];
    
    // Ambil prediksi AI jika ada
    if(isset($ai_predictions[$id])) {
        $pred_val = $ai_predictions[$id]['prediction'];
        $prediksi = ($pred_val == 1) ? 'Layak' : 'Tidak Layak';
        $confidence = $ai_predictions[$id]['confidence'];
    } else {
        $prediksi = 'N/A (Model belum ditraining)';
        $confidence = 0;
    }
    
    $stmt->bind_param("iddddsdi", $id, $r_ipk, $r_khs, $r_prak, $skor, $prediksi, $confidence, $rank);
    $stmt->execute();
    $rank++;
}
$stmt->close();

header("location:index.php?status=sukses");
?>
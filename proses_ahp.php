<?php
include 'koneksi.php';

if($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("location:ahp.php");
    exit;
}

// ============================================================
// PROSES AHP (Analytical Hierarchy Process)
// ============================================================

// 1. Ambil nilai slider dan konversi ke skala Saaty
function sliderToAHP($val) {
    $val = intval($val);
    $map = [
        1 => 1/9, 2 => 1/8, 3 => 1/7, 4 => 1/6, 5 => 1/5,
        6 => 1/4, 7 => 1/3, 8 => 1/2, 9 => 1,
        10 => 2, 11 => 3, 12 => 4, 13 => 5,
        14 => 6, 15 => 7, 16 => 8, 17 => 9
    ];
    return $map[$val] ?? 1;
}

$c12 = sliderToAHP($_POST['c12']); // IPK vs KHS
$c13 = sliderToAHP($_POST['c13']); // IPK vs Praktikum
$c23 = sliderToAHP($_POST['c23']); // KHS vs Praktikum

// 2. Susun Matriks Perbandingan Berpasangan (3x3)
$n = 3; // jumlah kriteria
$matriks = [
    [1,      $c12,    $c13],
    [1/$c12, 1,       $c23],
    [1/$c13, 1/$c23,  1]
];

// 3. Hitung total setiap kolom
$col_sum = [0, 0, 0];
for($j = 0; $j < $n; $j++) {
    for($i = 0; $i < $n; $i++) {
        $col_sum[$j] += $matriks[$i][$j];
    }
}

// 4. Normalisasi matriks (bagi setiap elemen dengan total kolomnya)
$norm = [];
for($i = 0; $i < $n; $i++) {
    for($j = 0; $j < $n; $j++) {
        $norm[$i][$j] = $matriks[$i][$j] / $col_sum[$j];
    }
}

// 5. Hitung bobot prioritas (rata-rata setiap baris dari matriks ternormalisasi)
$bobot = [];
for($i = 0; $i < $n; $i++) {
    $bobot[$i] = array_sum($norm[$i]) / $n;
}

// 6. Hitung Consistency Check
// λ_max = Σ (total_kolom × bobot)
$lambda_max = 0;
for($j = 0; $j < $n; $j++) {
    $lambda_max += $col_sum[$j] * $bobot[$j];
}

// CI = (λ_max - n) / (n - 1)
$CI = ($lambda_max - $n) / ($n - 1);

// CR = CI / RI (RI untuk n=3 adalah 0.58)
$RI = 0.58;
$CR = ($RI != 0) ? $CI / $RI : 0;
$is_consistent = ($CR < 0.1) ? 1 : 0;

// 7. Simpan bobot ke tabel kriteria
$nama_kriteria = ['IPK', 'Nilai KHS Mata Kuliah Syarat', 'Nilai Praktikum Mata Kuliah Syarat'];
for($i = 0; $i < $n; $i++) {
    $b = $bobot[$i];
    $nk = $nama_kriteria[$i];
    mysqli_query($conn, "UPDATE kriteria SET bobot = $b WHERE nama_kriteria = '$nk'");
}

// 8. Simpan log AHP untuk transparansi
$matriks_json = json_encode($matriks);
$norm_json = json_encode($norm);
$bobot_json = json_encode($bobot);

$stmt = $conn->prepare("INSERT INTO ahp_log (matriks_perbandingan, matriks_normalisasi, bobot_prioritas, lambda_max, consistency_index, consistency_ratio, is_consistent) VALUES (?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("sssdddi", $matriks_json, $norm_json, $bobot_json, $lambda_max, $CI, $CR, $is_consistent);
$stmt->execute();
$stmt->close();

// 9. Redirect
if($is_consistent) {
    header("location:ahp.php?status=sukses");
} else {
    header("location:ahp.php?status=inconsistent");
}
?>
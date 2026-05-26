<?php
include 'koneksi.php';

// 1. Ambil nilai perbandingan dari form (tahap awal)
$c12 = $_POST['c12']; // IPK vs KHS
$c13 = $_POST['c13']; // IPK vs Prak
$c23 = $_POST['c23']; // KHS vs Prak

// 2. Susun Matriks Perbandingan
$matriks = [
    [1, $c12, $c13],
    [1/$c12, 1, $c23],
    [1/$c13, 1/$c23, 1]
];

// 3. Normalisasi Matriks (bagi tiap nilai dengan total kolomnya)
$total_c1 = $matriks[0][0] + $matriks[1][0] + $matriks[2][0];
$total_c2 = $matriks[0][1] + $matriks[1][1] + $matriks[2][1];
$total_c3 = $matriks[0][2] + $matriks[1][2] + $matriks[2][2];

$bobot_ipk = (($matriks[0][0]/$total_c1) + ($matriks[0][1]/$total_c2) + ($matriks[0][2]/$total_c3)) / 3;
$bobot_khs = (($matriks[1][0]/$total_c1) + ($matriks[1][1]/$total_c2) + ($matriks[1][2]/$total_c3)) / 3;
$bobot_prak = (($matriks[2][0]/$total_c1) + ($matriks[2][1]/$total_c2) + ($matriks[2][2]/$total_c3)) / 3;

// 4. Update bobot baru ke Database
mysqli_query($conn, "UPDATE kriteria SET bobot = '$bobot_ipk' WHERE nama_kriteria = 'IPK'");
mysqli_query($conn, "UPDATE kriteria SET bobot = '$bobot_khs' WHERE nama_kriteria = 'KHS'");
mysqli_query($conn, "UPDATE kriteria SET bobot = '$bobot_prak' WHERE nama_kriteria = 'Praktikum'");

echo "Bobot AHP berhasil dihitung dan diperbarui!";
echo "<br><br>";
echo "<a href='proses_saw.php' style='padding: 10px; background: green; color: white; text-decoration: none;'>Lanjutkan ke Perhitungan SAW (Ranking)</a>";
echo " | ";
echo "<a href='index.php'>Kembali ke Dashboard</a>";
?>
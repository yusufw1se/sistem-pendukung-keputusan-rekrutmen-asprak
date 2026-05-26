<?php
include 'koneksi.php';

$filter_mk = $_GET['mk'] ?? '';

// Build Query
$sql = "SELECT h.*, a.NIM, a.NAMA, a.`Mata Kuliah Praktikum`, a.IPK, a.`Nilai KHS Mata Kuliah Syarat`, a.`Nilai Praktikum Mata Kuliah Syarat` 
        FROM hasil h 
        JOIN alternatif a ON h.id_alternatif = a.id_alternatif";
if(!empty($filter_mk)) {
    $sql .= " WHERE a.`Mata Kuliah Praktikum` = '" . mysqli_real_escape_string($conn, $filter_mk) . "'";
}
$sql .= " ORDER BY h.skor_akhir DESC";
$query = mysqli_query($conn, $sql);

// Header untuk memicu download Excel
$filename = "Hasil_Ranking_Asisten_" . (empty($filter_mk) ? "Semua_MK" : str_replace(" ", "_", $filter_mk)) . "_" . date('Ymd') . ".xls";
header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=\"$filename\"");
?>
<!DOCTYPE html>
<html>
<head>
    <style>
        table { border-collapse: collapse; width: 100%; }
        th, td { border: 1px solid black; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; font-weight: bold; }
    </style>
</head>
<body>
    <h2>Hasil Perangkingan Calon Asisten Laboratorium</h2>
    <?php if(!empty($filter_mk)): ?>
    <p>Filter Mata Kuliah: <strong><?= htmlspecialchars($filter_mk) ?></strong></p>
    <?php endif; ?>
    <p>Tanggal Export: <?= date('d M Y H:i:s') ?></p>
    
    <table>
        <thead>
            <tr>
                <th>Rank</th>
                <th>NIM</th>
                <th>Nama Kandidat</th>
                <th>Mata Kuliah Praktikum</th>
                <th>IPK</th>
                <th>KHS</th>
                <th>Praktikum</th>
                <th>Skor SAW</th>
                <th>Prediksi AI (Random Forest)</th>
                <th>Rekomendasi</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $rank = 1;
            while($row = mysqli_fetch_assoc($query)): 
                $is_layak = ($row['prediksi_ai'] === 'Layak');
                $rekomendasi = $is_layak ? 'Prioritas' : ($rank <= 3 ? 'Tinjau Manual (Skor Tinggi tapi AI Tidak Layak)' : 'Gugur');
            ?>
            <tr>
                <td><?= $rank++ ?></td>
                <td><?= htmlspecialchars($row['NIM']) ?></td>
                <td><?= htmlspecialchars($row['NAMA']) ?></td>
                <td><?= htmlspecialchars($row['Mata Kuliah Praktikum']) ?></td>
                <td><?= $row['IPK'] ?></td>
                <td><?= $row['Nilai KHS Mata Kuliah Syarat'] ?></td>
                <td><?= $row['Nilai Praktikum Mata Kuliah Syarat'] ?></td>
                <td><?= number_format($row['skor_akhir'], 6) ?></td>
                <td><?= $row['prediksi_ai'] ?></td>
                <td><?= $rekomendasi ?></td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</body>
</html>

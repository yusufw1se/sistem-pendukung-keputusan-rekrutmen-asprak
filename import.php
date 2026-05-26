<!DOCTYPE html>
<html>
<head>
    <title>Import Data Pendaftar</title>
</head>
<body>
    <h2>Import Data Pendaftar (CSV)</h2>
    <form action="proses_import.php" method="POST" enctype="multipart/form-data">
        <p>Pastikan file CSV memiliki urutan kolom: NIM, MK Praktikum, Nama, IPK, Nilai KHS, Nilai Praktikum.</p>
        <input type="file" name="file_csv" accept=".csv" required>
        <button type="submit" name="submit">Upload & Import</button>
    </form>
    <br>
    <a href="index.php">Kembali ke Dashboard</a>
</body>
</html>
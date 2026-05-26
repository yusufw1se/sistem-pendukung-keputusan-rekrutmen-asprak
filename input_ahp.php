<?php include 'koneksi.php'; ?>
<!DOCTYPE html>
<html>
<body>
    <h2>Input Bobot AHP</h2>
    <form action="proses_ahp.php" method="POST">
        Bobot IPK (0-1): <input type="number" step="0.01" name="bobot_ipk" required><br>
        Bobot KHS (0-1): <input type="number" step="0.01" name="bobot_khs" required><br>
        Bobot Praktikum (0-1): <input type="number" step="0.01" name="bobot_prak" required><br>
        <button type="submit">Simpan Bobot</button>
    </form>
</body>
</html>
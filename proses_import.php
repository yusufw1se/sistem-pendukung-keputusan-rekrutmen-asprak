<?php
include 'koneksi.php';

if(isset($_POST['submit'])) {
    $file = $_FILES['file_csv']['tmp_name'];
    
    if(!$file || !is_uploaded_file($file)) {
        header("location:upload.php?status=error");
        exit;
    }
    
    $handle = fopen($file, "r");
    if(!$handle) {
        header("location:upload.php?status=error");
        exit;
    }
    
    // Lewati header
    fgetcsv($handle);
    
    $count = 0;
    $errors = 0;
    
    // Gunakan prepared statement untuk keamanan
    $stmt = $conn->prepare("INSERT INTO alternatif (NIM, `Mata Kuliah Praktikum`, NAMA, IPK, `Nilai KHS Mata Kuliah Syarat`, `Nilai Praktikum Mata Kuliah Syarat`) VALUES (?, ?, ?, ?, ?, ?)");
    
    while(($data = fgetcsv($handle, 2000, ",")) !== FALSE) {
        if(count($data) < 6) { $errors++; continue; }
        
        $nim  = trim($data[0]);
        $mk   = trim($data[1]);
        $nama = trim($data[2]);
        $ipk  = floatval(str_replace(',', '.', trim($data[3])));
        $khs  = floatval(str_replace(',', '.', trim($data[4])));
        $prak = floatval(str_replace(',', '.', trim($data[5])));
        
        // Skip jika data tidak valid
        if(empty($nim) || empty($nama)) { $errors++; continue; }
        
        $stmt->bind_param("sssddd", $nim, $mk, $nama, $ipk, $khs, $prak);
        
        if($stmt->execute()) {
            $count++;
        } else {
            $errors++;
        }
    }
    
    $stmt->close();
    fclose($handle);
    
    if($count > 0) {
        header("location:upload.php?status=sukses&count=$count");
    } else {
        header("location:upload.php?status=error");
    }
} else {
    header("location:upload.php");
}
?>
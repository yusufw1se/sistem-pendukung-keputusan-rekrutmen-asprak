<?php
include 'koneksi.php';

mysqli_query($conn, "SET FOREIGN_KEY_CHECKS = 0");
mysqli_query($conn, "TRUNCATE TABLE hasil"); 
$query = mysqli_query($conn, "TRUNCATE TABLE alternatif");
mysqli_query($conn, "SET FOREIGN_KEY_CHECKS = 1");
if($query) {
    header("location:upload.php?status=cleared");
} else {
    header("location:upload.php?status=error");
}
?>

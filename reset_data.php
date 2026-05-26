<?php
include 'koneksi.php';

// Menghapus data hanya di tabel hasil
$query = mysqli_query($conn, "TRUNCATE TABLE hasil");

if($query) {
    header("location:index.php?status=reset");
} else {
    header("location:index.php?status=error");
}
?>
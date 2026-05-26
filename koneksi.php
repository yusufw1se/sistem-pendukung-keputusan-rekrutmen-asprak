<?php
$conn = mysqli_connect("localhost", "root", "", "db_spk_asisten");
if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}
?>
<?php

$koneksi = mysqli_connect(
    "localhost",
    "root",
    "",
    "db_zhafira_media"
);

if (!$koneksi) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}

?>
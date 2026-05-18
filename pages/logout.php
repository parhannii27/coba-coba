<?php
session_start();

// hapus semua session
session_destroy();

// kembali ke login
header("Location: login.php");
exit;
?>
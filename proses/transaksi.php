   <?php
include '../config/koneksi.php';
$buku = mysqli_query($conn, "SELECT * FROM buku");
?>
<?php
include '../config/koneksi.php';

$produk = $_POST['produk'];
$harga = $_POST['harga'];
$qty = $_POST['qty'];
$bayar = $_POST['bayar'];

$total = $harga * $qty;
$kembalian = $bayar - $total;

mysqli_query($conn, "INSERT INTO transaksi VALUES(
'',
CURRENT_TIMESTAMP,
'$total',
'$bayar',
'$kembalian',
'Kasir Medina'
)");

header('Location: ../pages/transaksi.php');
?>
<!DOCTYPE html>
<html>
<head>
    <title>Kasir Medina</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-5">

    <h2>Transaksi Kasir</h2>

    <form action="../proses/transaksi.php" method="POST">

        <div class="mb-3">
            <label>Pilih Buku</label>

            <select name="produk" class="form-control">

                <?php while($row = mysqli_fetch_assoc($buku)) { ?>

                    <option value="<?= $row['judul']; ?>">
                        <?= $row['judul']; ?> - Rp <?= number_format($row['harga_jual']); ?>
                    </option>

                <?php } ?>

            </select>
        </div>

        <div class="mb-3">
            <label>Harga</label>
            <input type="number" name="harga" class="form-control">
        </div>

        <div class="mb-3">
            <label>Jumlah</label>
            <input type="number" name="qty" class="form-control">
        </div>

        <div class="mb-3">
            <label>Bayar</label>
            <input type="number" name="bayar" class="form-control">
        </div>

        <button class="btn btn-success">
</html> 
<?php
session_start();

$conn = mysqli_connect("localhost", "root", "", "db_zhafira_media");

if (!$conn) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}

/* ==========================
   SESSION KERANJANG
========================== */
if (!isset($_SESSION['keranjang'])) {
    $_SESSION['keranjang'] = [];
}

/* ==========================
   TAMBAH KE KERANJANG
========================== */
if (isset($_POST['tambah'])) {

    $id_barang = $_POST['id_barang'];
    $qty = (int)$_POST['qty'];

    $query = mysqli_query($conn, "
        SELECT * FROM barang
        WHERE id_barang='$id_barang'
    ");

    $barang = mysqli_fetch_assoc($query);

    if ($barang) {

        if ($qty > $barang['stok']) {
            echo "<script>alert('Stok tidak mencukupi!');</script>";
        } else {

            if (isset($_SESSION['keranjang'][$id_barang])) {
                $_SESSION['keranjang'][$id_barang]['qty'] += $qty;
            } else {

                $_SESSION['keranjang'][$id_barang] = [
                    'kode_barang' => $barang['kode_barang'],
                    'nama_barang' => $barang['nama_barang'],
                    'harga' => $barang['harga'],
                    'qty' => $qty
                ];
            }
        }
    }
}

/* ==========================
   HAPUS ITEM
========================== */
if (isset($_GET['hapus'])) {
    unset($_SESSION['keranjang'][$_GET['hapus']]);
    header("Location: kasir.php");
    exit;
}

/* ==========================
   SIMPAN TRANSAKSI
========================== */
if (isset($_POST['simpan'])) {

    $total_bayar = $_POST['total_bayar'];
    $uang_bayar = $_POST['uang_bayar'];
    $kembalian = $uang_bayar - $total_bayar;

    if ($uang_bayar < $total_bayar) {

        echo "<script>alert('Uang bayar kurang!');</script>";

    } else {

        mysqli_query($conn, "
            INSERT INTO transaksi (
                tanggal,
                nama_barang,
                total_bayar,
                uang_bayar,
                kembalian
            ) VALUES (
                NOW(),
                '$nama_barang',
                '$total_bayar',
                '$uang_bayar',
                '$kembalian'
            )
        ");

        // Kurangi stok
        foreach ($_SESSION['keranjang'] as $id => $item) {

            mysqli_query($conn, "
                UPDATE barang
                SET stok = stok - ".$item['qty']."
                WHERE id_barang='$id'
            ");
        }

        $_SESSION['keranjang'] = [];

        echo "<script>
            alert('Transaksi berhasil!');
            window.location='kasir.php';
        </script>";
    }
}

/* ==========================
   CARI BARANG
========================== */
$cari = $_GET['cari'] ?? '';

$barang = mysqli_query($conn, "
    SELECT * FROM barang
    WHERE nama_barang LIKE '%$cari%'
    ORDER BY nama_barang ASC
");
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Kasir - Zhafira Media</title>

<style>
*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family:Arial,sans-serif;
}

body{
    background:#f4f6f9;
    display:flex;
}

/* SIDEBAR */
.sidebar{
    width:250px;
    height:100vh;
    background:#1e293b;
    color:white;
    position:fixed;
    padding-top:20px;
}

.logo{
    text-align:center;
    font-size:24px;
    font-weight:bold;
    margin-bottom:30px;
}

.menu{
    list-style:none;
}

.menu li{
    margin:10px 15px;
}

.menu li a{
    text-decoration:none;
    color:white;
    display:block;
    padding:15px;
    border-radius:10px;
    transition:.3s;
}

.menu li a:hover,
.menu li a.active{
    background:#2563eb;
}

/* MAIN */
.main{
    margin-left:250px;
    width:100%;
    padding:30px;
}

.header{
    background:white;
    padding:20px;
    border-radius:20px;
    margin-bottom:25px;
    box-shadow:0 2px 10px rgba(0,0,0,.08);
}

.header h1{
    color:#1e293b;
}

.clock{
    margin-top:8px;
    color:#64748b;
}

/* POS LAYOUT */
.pos{
    display:flex;
    gap:25px;
}

.left{
    flex:2;
}

.right{
    flex:1;
}

/* CARD */
.card{
    background:white;
    border-radius:25px;
    padding:25px;
    box-shadow:0 2px 12px rgba(0,0,0,.08);
}

.title{
    font-size:30px;
    font-weight:bold;
    color:#1e293b;
    margin-bottom:20px;
}

/* SEARCH */
.search{
    width:100%;
    padding:15px;
    border:1px solid #ddd;
    border-radius:15px;
    margin-bottom:25px;
    font-size:16px;
}

/* GRID BARANG */
.grid{
    display:grid;
    grid-template-columns:repeat(auto-fill,minmax(220px,1fr));
    gap:20px;
}

.item{
    background:#fff;
    border:1px solid #e5e7eb;
    border-radius:20px;
    padding:20px;
    text-align:center;
    transition:.3s;
}

.item:hover{
    transform:translateY(-5px);
}

.icon{
    width:90px;
    height:90px;
    background:#e2e8f0;
    border-radius:20px;
    margin:auto;
    margin-bottom:15px;
    display:flex;
    align-items:center;
    justify-content:center;
    font-size:35px;
}

.nama{
    font-size:20px;
    font-weight:bold;
    color:#1e293b;
    min-height:55px;
}

.harga{
    color:#2563eb;
    font-size:24px;
    font-weight:bold;
    margin:12px 0;
}

.stok{
    color:#16a34a;
    margin-bottom:12px;
}

.qty{
    width:100%;
    padding:12px;
    border:1px solid #ddd;
    border-radius:12px;
    margin-bottom:10px;
}

.btn{
    width:100%;
    border:none;
    padding:13px;
    border-radius:14px;
    color:white;
    cursor:pointer;
    font-size:15px;
    font-weight:bold;
}

.btn-blue{
    background:#2563eb;
}

.btn-blue:hover{
    background:#1d4ed8;
}

.btn-green{
    background:#16a34a;
}

.btn-green:hover{
    background:#15803d;
}

/* PESANAN */
.table{
    width:100%;
    border-collapse:collapse;
    margin-top:10px;
}

.table th{
    text-align:left;
    padding:12px 0;
    border-bottom:1px solid #ddd;
    color:#1e293b;
}

.table td{
    padding:12px 0;
    border-bottom:1px solid #eee;
}

.hapus{
    color:red;
    text-decoration:none;
}

.total-box{
    background:#dbeafe;
    border-radius:18px;
    padding:25px;
    margin:25px 0;
    display:flex;
    justify-content:space-between;
    align-items:center;
}

.total-box h2{
    color:#1e3a8a;
    font-size:40px;
}

.label{
    font-size:20px;
    font-weight:bold;
}

.input{
    width:100%;
    padding:15px;
    border:1px solid #ddd;
    border-radius:15px;
    margin-bottom:15px;
    font-size:16px;
}

.empty{
    text-align:center;
    padding:20px;
    color:#64748b;
}

@media(max-width:900px){
    .pos{
        flex-direction:column;
    }

    .sidebar{
        width:200px;
    }

    .main{
        margin-left:200px;
    }
}
</style>
</head>
<body>

<!-- SIDEBAR -->
<div class="sidebar">

    <div class="logo">
        Zhafira Media
    </div>

    <ul class="menu">
        <li><a href="dashboard.php">🏠 Dashboard</a></li>
        <li><a href="barang.php">📦 Barang</a></li>
        <li><a href="kasir.php" class="active">🛒 Kasir</a></li>
        <li><a href="laporan.php">📊 Laporan</a></li>
        <li><a href="supplier.php">🚚 Supplier</a></li>
        <li><a href="logout.php">🚪 Logout</a></li>
    </ul>

</div>

<!-- MAIN -->
<div class="main">

    <div class="header">
        <h1>Kasir Toko Buku Zhafira Media</h1>
        <div class="clock">
            <span id="jam"></span>
        </div>
    </div>

    <div class="pos">

        <!-- KIRI -->
        <div class="left">

            <div class="card">

                <div class="title">
                    📚 Daftar Barang
                </div>

                <form method="GET">
                    <input
                    type="text"
                    name="cari"
                    class="search"
                    placeholder="Cari nama barang..."
                    value="<?= $cari ?>">
                </form>

                <div class="grid">

                    <?php while($row = mysqli_fetch_assoc($barang)) : ?>

                    <div class="item">

                        <div class="icon">📖</div>

                        <div class="nama">
                            <?= $row['nama_barang'] ?>
                        </div>

                        <div class="harga">
                            Rp<?= number_format($row['harga']) ?>
                        </div>

                        <div class="stok">
                            Stok: <?= $row['stok'] ?>
                        </div>

                        <form method="POST">

                            <input
                            type="hidden"
                            name="id_barang"
                            value="<?= $row['id_barang'] ?>">

                            <input
                            type="number"
                            name="qty"
                            class="qty"
                            value="1"
                            min="1"
                            required>

                            <button
                            type="submit"
                            name="tambah"
                            class="btn btn-blue">
                            Tambah
                            </button>

                        </form>

                    </div>

                    <?php endwhile; ?>

                </div>

            </div>

        </div>

        <!-- KANAN -->
        <div class="right">

            <div class="card">

                <div class="title">
                    🛒 Pesanan
                </div>

                <table class="table">

                    <tr>
                        <th>Menu</th>
                        <th>Qty</th>
                        <th>Subtotal</th>
                    </tr>

                    <?php
                    $grand_total = 0;

                    if(!empty($_SESSION['keranjang'])):

                    foreach($_SESSION['keranjang'] as $id => $item):

                    $subtotal =
                    $item['harga'] * $item['qty'];

                    $grand_total += $subtotal;
                    ?>

                    <tr>
                        <td>
                            <?= $item['nama_barang'] ?>
                            <br>
                            <a href="kasir.php?hapus=<?= $id ?>"
                            class="hapus">
                            Hapus
                            </a>
                        </td>

                        <td>
                            <?= $item['qty'] ?>
                        </td>

                        <td>
                            Rp<?= number_format($subtotal) ?>
                        </td>
                    </tr>

                    <?php endforeach; else: ?>

                    <tr>
                        <td colspan="3"
                        class="empty">
                        🛒 Keranjang kosong
                        </td>
                    </tr>

                    <?php endif; ?>

                </table>

                <div class="total-box">

                    <div class="label">
                        Total:
                    </div>

                    <h2>
                        Rp<?= number_format($grand_total) ?>
                    </h2>

                </div>

                <form method="POST">

                    <input
                    type="hidden"
                    name="total_bayar"
                    value="<?= $grand_total ?>">

                    <input
                    type="number"
                    id="uang_bayar"
                    name="uang_bayar"
                    class="input"
                    placeholder="Masukkan uang bayar"
                    required>

                    <input
                    type="text"
                    id="kembalian"
                    class="input"
                    placeholder="Kembalian"
                    readonly>

                    <button
                    type="submit"
                    name="simpan"
                    class="btn btn-green">
                    💾 Simpan Transaksi
                    </button>

                </form>

            </div>

        </div>

    </div>

</div>

<script>
function updateJam() {

    let sekarang = new Date();

    let tanggal =
    sekarang.toLocaleDateString(
        'id-ID',{
        weekday:'long',
        year:'numeric',
        month:'long',
        day:'numeric'
    });

    let jam =
    sekarang.toLocaleTimeString('id-ID');

    document.getElementById('jam')
    .innerHTML =
    tanggal + " | " + jam;
}

setInterval(updateJam,1000);
updateJam();

document.getElementById('uang_bayar')
.addEventListener('input', function(){

    let bayar =
    parseInt(this.value) || 0;

    let total =
    <?= $grand_total ?>;

    let kembali =
    bayar - total;

    document.getElementById(
        'kembalian'
    ).value =
    "Rp " +
    kembali.toLocaleString('id-ID');
});
</script>

</body>
</html>
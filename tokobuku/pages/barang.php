<?php
$conn = mysqli_connect("localhost", "root", "", "db_zhafira_media");

if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

/* ==========================
   GENERATE KODE BARANG
========================== */
function generateKodeBarang($conn)
{
    $query = mysqli_query($conn,
        "SELECT kode_barang
         FROM barang
         ORDER BY id_barang DESC
         LIMIT 1");

    $data = mysqli_fetch_assoc($query);

    if (!$data) {
        return "BK001";
    }

    $angka = substr($data['kode_barang'], 2);
    $angka++;

    return "BK" . str_pad($angka, 3, "0", STR_PAD_LEFT);
}

$kode_otomatis = generateKodeBarang($conn);

/* ==========================
   TAMBAH BARANG
========================== */
if (isset($_POST['tambah'])) {

    $kode_barang = $_POST['kode_barang'];
    $nama_barang = $_POST['nama_barang'];
    $harga = $_POST['harga'];
    $stok = $_POST['stok'];

    mysqli_query($conn, "INSERT INTO barang
    (kode_barang, nama_barang, harga, stok)
    VALUES
    ('$kode_barang','$nama_barang','$harga','$stok')");

    header("Location: barang.php");
    exit;
}

/* ==========================
   EDIT BARANG
========================== */
if (isset($_POST['update'])) {

    $id_barang = $_POST['id_barang'];
    $nama_barang = $_POST['nama_barang'];
    $harga = $_POST['harga'];
    $stok = $_POST['stok'];

    mysqli_query($conn, "UPDATE barang SET
        nama_barang='$nama_barang',
        harga='$harga',
        stok='$stok'
        WHERE id_barang='$id_barang'
    ");

    header("Location: barang.php");
    exit;
}

/* ==========================
   HAPUS BARANG
========================== */
if (isset($_GET['hapus'])) {

    $id = $_GET['hapus'];

    mysqli_query($conn,
        "DELETE FROM barang
         WHERE id_barang='$id'");

    header("Location: barang.php");
    exit;
}

/* ==========================
   AMBIL DATA EDIT
========================== */
$edit = null;

if (isset($_GET['edit'])) {

    $id = $_GET['edit'];

    $query = mysqli_query($conn,
        "SELECT * FROM barang
         WHERE id_barang='$id'");

    $edit = mysqli_fetch_assoc($query);
}

/* ==========================
   CARI BARANG BERDASARKAN
   KODE BARANG
========================== */

$cari = "";

if (isset($_GET['cari'])) {

    $cari = $_GET['cari'];

    $data = mysqli_query($conn,
        "SELECT * FROM barang
         WHERE kode_barang LIKE '%$cari%'
         ORDER BY id_barang DESC");

} else {

    $data = mysqli_query($conn,
        "SELECT * FROM barang
         ORDER BY id_barang DESC");
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Barang - Zhafira Media</title>

<style>
*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family:Arial;
}

body{
    background:#f4f6f9;
    display:flex;
}

/* Sidebar */
.sidebar{
    width:250px;
    height:100vh;
    background:#1e293b;
    position:fixed;
    color:white;
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
    color:white;
    text-decoration:none;
    display:block;
    padding:15px;
    border-radius:10px;
    transition:0.3s;
}

.menu li a:hover,
.active{
    background:#2563eb;
}

/* Main */
.main{
    margin-left:250px;
    width:100%;
    padding:30px;
}

.card{
    background:white;
    border-radius:15px;
    padding:25px;
    box-shadow:0 2px 10px rgba(0,0,0,0.1);
}

h2{
    margin-bottom:20px;
    color:#1e293b;
}

form{
    margin-bottom:25px;
}

input{
    width:100%;
    padding:12px;
    margin:8px 0;
    border:1px solid #ddd;
    border-radius:8px;
}

button{
    background:#2563eb;
    color:white;
    border:none;
    padding:12px 18px;
    border-radius:8px;
    cursor:pointer;
}

button:hover{
    background:#1d4ed8;
}

table{
    width:100%;
    border-collapse:collapse;
}

table th{
    background:#1e293b;
    color:white;
    padding:15px;
}

table td{
    padding:12px;
    border-bottom:1px solid #ddd;
}

.edit{
    background:orange;
    color:white;
    padding:8px 12px;
    border-radius:5px;
    text-decoration:none;
}

.hapus{
    background:red;
    color:white;
    padding:8px 12px;
    border-radius:5px;
    text-decoration:none;
}

.stok-aman{
    color:green;
    font-weight:bold;
}

.stok-tipis{
    color:orange;
    font-weight:bold;
}

.stok-habis{
    color:red;
    font-weight:bold;
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
        <li><a href="barang.php" class="active">📦 Barang</a></li>
        <li><a href="kasir.php">🛒 Kasir</a></li>
        <li><a href="laporan.php">📊 Laporan</a></li>
        <li><a href="supplier.php">🚚 Supplier</a></li>
        <li><a href="logout.php">🚪 Logout</a></li>
    </ul>

</div>

<!-- MAIN -->
<div class="main">

<div class="card">

<h2>Data Barang</h2>
<!-- FORM CARI -->
<form method="GET" style="display:flex; gap:10px; margin-bottom:20px;">

    <input type="text"
    name="cari"
    placeholder="Cari kode barang (contoh: BK001)"
    value="<?= $cari ?>"
    style="flex:1;">

    <button type="submit">
        Cari
    </button>

    <a href="barang.php"
    style="
        background:red;
        color:white;
        text-decoration:none;
        padding:12px 20px;
        border-radius:8px;
        display:flex;
        align-items:center;
        justify-content:center;
    ">
        Reset
    </a>

</form>
<!-- FORM -->
<form method="POST">

<input type="hidden"
name="id_barang"
value="<?= $edit['id_barang'] ?? '' ?>">

<label>Kode Barang</label>
<input type="text"
name="kode_barang"
value="<?= $edit['kode_barang'] ?? $kode_otomatis ?>"
readonly>

<label>Nama Barang</label>
<input type="text"
name="nama_barang"
placeholder="Masukkan nama barang"
value="<?= $edit['nama_barang'] ?? '' ?>"
required>

<label>Harga</label>
<input type="number"
name="harga"
placeholder="Masukkan harga"
value="<?= $edit['harga'] ?? '' ?>"
required>

<label>Stok</label>
<input type="number"
name="stok"
placeholder="Masukkan stok"
value="<?= $edit['stok'] ?? '' ?>"
required>

<?php if($edit){ ?>
<button type="submit" name="update">
Update Barang
</button>
<?php } else { ?>
<button type="submit" name="tambah">
+ Tambah Barang
</button>
<?php } ?>

</form>

<!-- TABEL -->
<table>

<tr>
    <th>ID</th>
    <th>Kode</th>
    <th>Nama Barang</th>
    <th>Harga</th>
    <th>Stok</th>
    <th>Status</th>
    <th>Aksi</th>
</tr>

<?php while($row = mysqli_fetch_assoc($data)) { ?>

<tr>

<td><?= $row['id_barang']; ?></td>

<td><?= $row['kode_barang']; ?></td>

<td><?= $row['nama_barang']; ?></td>

<td>
Rp <?= number_format($row['harga']); ?>
</td>

<td><?= $row['stok']; ?></td>

<td>
<?php
if($row['stok'] == 0){
    echo "<span class='stok-habis'>Habis</span>";
}
elseif($row['stok'] <= 5){
    echo "<span class='stok-tipis'>Menipis</span>";
}
else{
    echo "<span class='stok-aman'>Aman</span>";
}
?>
</td>

<td>
<a class="edit"
href="barang.php?edit=<?= $row['id_barang']; ?>">
Edit
</a>

<a class="hapus"
onclick="return confirm('Yakin hapus data?')"
href="barang.php?hapus=<?= $row['id_barang']; ?>">
Hapus
</a>
</td>

</tr>

<?php } ?>

</table>

</div>
</div>

</body>
</html>
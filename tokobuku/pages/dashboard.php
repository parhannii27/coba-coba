<?php
$conn = mysqli_connect("localhost", "root", "", "db_zhafira_media");

if (!$conn) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}

// Total Barang
$barang = mysqli_query($conn, "SELECT COUNT(*) as total FROM barang");
$total_barang = mysqli_fetch_assoc($barang)['total'] ?? 0;

// Total Supplier
$supplier = mysqli_query($conn, "SELECT COUNT(*) as total FROM supplier");
$total_supplier = mysqli_fetch_assoc($supplier)['total'] ?? 0;

// Total Transaksi
$transaksi = mysqli_query($conn, "SELECT COUNT(*) as total FROM transaksi");
$total_transaksi = mysqli_fetch_assoc($transaksi)['total'] ?? 0;

// Stok Menipis
$stok = mysqli_query($conn, "SELECT COUNT(*) as total FROM barang WHERE stok <= 5");
$stok_menipis = mysqli_fetch_assoc($stok)['total'] ?? 0;
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard - Zhafira Media</title>

    <style>
        *{
            margin:0;
            padding:0;
            box-sizing:border-box;
            font-family:Arial, sans-serif;
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
            color:white;
            position:fixed;
            padding-top:20px;
        }

        .logo{
            text-align:center;
            font-size:24px;
            font-weight:bold;
            margin-bottom:30px;
            color:#fff;
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
            transition:0.3s;
        }

        .menu li a:hover,
        .menu li a.active{
            background:#2563eb;
        }

        /* Main Content */
        .main{
            margin-left:250px;
            width:100%;
            padding:30px;
        }

        .header{
            background:white;
            padding:20px;
            border-radius:15px;
            box-shadow:0 2px 10px rgba(0,0,0,0.1);
            margin-bottom:25px;
        }

        .header h1{
            color:#1e293b;
        }

        .clock{
            margin-top:10px;
            color:gray;
            font-size:15px;
        }

        /* Cards */
        .cards{
            display:grid;
            grid-template-columns:repeat(auto-fit,minmax(220px,1fr));
            gap:20px;
        }

        .card{
            background:white;
            padding:25px;
            border-radius:15px;
            box-shadow:0 2px 10px rgba(0,0,0,0.1);
            transition:0.3s;
        }

        .card:hover{
            transform:translateY(-5px);
        }

        .card h3{
            color:#64748b;
            margin-bottom:10px;
        }

        .card h2{
            color:#1e293b;
            font-size:32px;
        }

        .blue{
            border-left:6px solid #2563eb;
        }

        .green{
            border-left:6px solid #16a34a;
        }

        .orange{
            border-left:6px solid #ea580c;
        }

        .red{
            border-left:6px solid #dc2626;
        }

        @media(max-width:768px){
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

<!-- Sidebar -->
<div class="sidebar">
    <div class="logo">
        Zhafira Media
    </div>

    <ul class="menu">
        <li><a href="dashboard.php" class="active">🏠 Dashboard</a></li>
        <li><a href="barang.php">📦 Barang</a></li>
        <li><a href="kasir.php">🛒 Kasir</a></li>
        <li><a href="laporan.php">📊 Laporan</a></li>
        <li><a href="supplier.php">🚚 Supplier</a></li>
        <li><a href="logout.php">🚪 Logout</a></li>
    </ul>
</div>

<!-- Main -->
<div class="main">

    <div class="header">
        <h1>Dashboard Toko Buku Zhafira Media</h1>
        <div class="clock">
            <span id="jam"></span>
        </div>
    </div>

    <div class="cards">

        <div class="card blue">
            <h3>Total Barang</h3>
            <h2><?= $total_barang; ?></h2>
        </div>

        <div class="card green">
            <h3>Total Supplier</h3>
            <h2><?= $total_supplier; ?></h2>
        </div>

        <div class="card orange">
            <h3>Total Transaksi</h3>
            <h2><?= $total_transaksi; ?></h2>
        </div>

        <div class="card red">
            <h3>Stok Menipis</h3>
            <h2><?= $stok_menipis; ?></h2>
        </div>

    </div>

</div>

<script>
function updateJam() {
    let sekarang = new Date();

    let tanggal = sekarang.toLocaleDateString('id-ID', {
        weekday: 'long',
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });

    let jam = sekarang.toLocaleTimeString('id-ID');

    document.getElementById('jam').innerHTML =
        tanggal + " | " + jam;
}

setInterval(updateJam, 1000);
updateJam();
</script>

</body>
</html>
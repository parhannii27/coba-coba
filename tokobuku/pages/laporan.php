<?php
$conn = mysqli_connect("localhost", "root", "", "db_zhafira_media");

if (!$conn) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}

/*
|--------------------------------------------------------------------------
| FILTER TANGGAL
|--------------------------------------------------------------------------
*/

$tanggal = isset($_GET['tanggal'])
    ? $_GET['tanggal']
    : date('Y-m-d');

/*
|--------------------------------------------------------------------------
| AMBIL BULAN & TAHUN
|--------------------------------------------------------------------------
*/

$bulan = date('m', strtotime($tanggal));
$tahun = date('Y', strtotime($tanggal));

/*
|--------------------------------------------------------------------------
| DATA LAPORAN HARIAN
|--------------------------------------------------------------------------
|
| Pastikan tabel transaksi memiliki:
| - tanggal
| - nama_barang
| - total_bayar
|
*/

$query = mysqli_query($conn, "
    SELECT *
    FROM transaksi
    WHERE tanggal='$tanggal'
    ORDER BY id_transaksi DESC
");

if(!$query){
    die("Query Error : " . mysqli_error($conn));
}

/*
|--------------------------------------------------------------------------
| TOTAL PENDAPATAN HARIAN
|--------------------------------------------------------------------------
*/

$total_harian_query = mysqli_query($conn, "
    SELECT SUM(total_bayar) as total_harian
    FROM transaksi
    WHERE tanggal='$tanggal'
");

$total_harian_data =
    mysqli_fetch_assoc($total_harian_query);

$total_harian =
    $total_harian_data['total_harian'] ?? 0;

/*
|--------------------------------------------------------------------------
| DATA CETAK BULANAN
|--------------------------------------------------------------------------
*/

$bulanan_query = mysqli_query($conn, "
    SELECT tanggal,
    SUM(total_bayar) as pendapatan_harian
    FROM transaksi
    WHERE MONTH(tanggal)='$bulan'
    AND YEAR(tanggal)='$tahun'
    GROUP BY tanggal
    ORDER BY tanggal ASC
");

/*
|--------------------------------------------------------------------------
| TOTAL BULANAN
|--------------------------------------------------------------------------
*/

$total_bulan_query = mysqli_query($conn, "
    SELECT SUM(total_bayar) as total_bulanan
    FROM transaksi
    WHERE MONTH(tanggal)='$bulan'
    AND YEAR(tanggal)='$tahun'
");

$total_bulan_data =
    mysqli_fetch_assoc($total_bulan_query);

$total_bulanan =
    $total_bulan_data['total_bulanan'] ?? 0;

?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Penjualan</title>

    <style>

        *{
            margin:0;
            padding:0;
            box-sizing:border-box;
            font-family:Arial,sans-serif;
        }

        body{
            background:#f1f5f9;
            display:flex;
        }

        /* SIDEBAR */

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
            display:block;
            padding:15px;
            color:white;
            text-decoration:none;
            border-radius:10px;
            transition:0.3s;
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
            padding:25px;
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
        }

        /* FILTER */

        .filter-box{
            background:white;
            padding:20px;
            border-radius:15px;
            box-shadow:0 2px 10px rgba(0,0,0,0.1);
            margin-bottom:25px;
        }

        .filter-box form{
            display:flex;
            gap:15px;
            flex-wrap:wrap;
            align-items:center;
        }

        input[type="date"]{
            padding:12px;
            border:1px solid #ccc;
            border-radius:8px;
        }

        .btn{
            padding:12px 20px;
            border:none;
            border-radius:8px;
            color:white;
            cursor:pointer;
            font-weight:bold;
        }

        .btn-blue{
            background:#2563eb;
        }

        .btn-green{
            background:#16a34a;
        }

        /* TABLE */

        .table-container{
            background:white;
            padding:20px;
            border-radius:15px;
            box-shadow:0 2px 10px rgba(0,0,0,0.1);
            overflow:auto;
        }

        table{
            width:100%;
            border-collapse:collapse;
        }

        table th{
            background:#2563eb;
            color:white;
            padding:15px;
            text-align:center;
        }

        table td{
            padding:12px;
            border-bottom:1px solid #ddd;
            text-align:center;
        }

        table tr:hover{
            background:#f8fafc;
        }

        .total{
            margin-top:20px;
            background:#16a34a;
            color:white;
            padding:20px;
            border-radius:10px;
            font-size:22px;
            font-weight:bold;
            text-align:right;
        }

        /* CETAK */

        .print-area{
            display:none;
        }

        @media print{

            body *{
                visibility:hidden;
            }

            .print-area,
            .print-area *{
                visibility:visible;
            }

            .print-area{
                display:block;
                position:absolute;
                left:0;
                top:0;
                width:100%;
                padding:30px;
            }

            .print-table{
                width:100%;
                border-collapse:collapse;
                margin-top:20px;
            }

            .print-table th,
            .print-table td{
                border:1px solid black;
                padding:12px;
                text-align:center;
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
        <li><a href="kasir.php">🛒 Kasir</a></li>
        <li><a href="laporan.php" class="active">📊 Laporan</a></li>
        <li><a href="supplier.php">🚚 Supplier</a></li>
        <li><a href="logout.php">🚪 Logout</a></li>
    </ul>

</div>

<!-- MAIN -->

<div class="main">

    <div class="header">

        <h1>Laporan Penjualan Harian</h1>

        <div class="clock">
            <span id="jam"></span>
        </div>

    </div>

    <!-- FILTER -->

    <div class="filter-box">

        <form method="GET">

            <input type="date"
                   name="tanggal"
                   value="<?= $tanggal; ?>">

            <button type="submit"
                    class="btn btn-blue">

                🔍 Tampilkan

            </button>

            <button type="button"
                    onclick="window.print()"
                    class="btn btn-green">

                🖨 Cetak Laporan Bulanan

            </button>

        </form>

    </div>

    <!-- TABLE LAPORAN -->

    <div class="table-container">

        <table>

            <tr>
                <th>No</th>
                <th>Tanggal</th>
                <th>Barang Dibeli</th>
                <th>Total Bayar</th>
            </tr>

            <?php
            $no = 1;

            while($row = mysqli_fetch_assoc($query)){
            ?>

            <tr>

                <td><?= $no++; ?></td>

                <td><?= $row['tanggal']; ?></td>

                <td><?= $row['nama_barang']; ?></td>

                <td>
                    Rp <?= number_format($row['total_bayar']); ?>
                </td>

            </tr>

            <?php } ?>

        </table>

        <div class="total">

            Total Pendapatan Hari Ini :
            Rp <?= number_format($total_harian); ?>

        </div>

    </div>

</div>

<!-- CETAK BULANAN -->

<div class="print-area">

    <h1 style="margin-bottom:20px;">
        Laporan Pendapatan Bulanan
    </h1>

    <p>
        Bulan :
        <?= date('F Y', strtotime($tanggal)); ?>
    </p>

    <table class="print-table">

        <tr>
            <th>No</th>
            <th>Tanggal</th>
            <th>Pendapatan</th>
        </tr>

        <?php
        $no = 1;

        while($bulan_row = mysqli_fetch_assoc($bulanan_query)){
        ?>

        <tr>

            <td><?= $no++; ?></td>

            <td><?= $bulan_row['tanggal']; ?></td>

            <td>
                Rp <?= number_format($bulan_row['pendapatan_harian']); ?>
            </td>

        </tr>

        <?php } ?>

        <tr>

            <th colspan="2">
                Total Pendapatan Sebulan
            </th>

            <th>
                Rp <?= number_format($total_bulanan); ?>
            </th>

        </tr>

    </table>

</div>

<script>

function updateJam(){

    let sekarang = new Date();

    let tanggal = sekarang.toLocaleDateString('id-ID',{
        weekday:'long',
        year:'numeric',
        month:'long',
        day:'numeric'
    });

    let jam = sekarang.toLocaleTimeString('id-ID');

    document.getElementById('jam').innerHTML =
        tanggal + " | " + jam;
}

setInterval(updateJam,1000);

updateJam();

</script>

</body>
</html>
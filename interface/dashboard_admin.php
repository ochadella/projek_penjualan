<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            min-height: 100vh;
            overflow-x: hidden;
            background: linear-gradient(135deg, 
                #4B0082 0%,     
                #C13584 25%,    
                #E94057 50%,    
                #F27121 75%,    
                #FFD54F 100%);  
            background-size: 300% 300%;
            animation: gradientFlow 12s ease infinite;
            color: #fff;
            position: relative;
        }

        @keyframes gradientFlow {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        .glow {
            position: absolute;
            border-radius: 50%;
            pointer-events: none;
            filter: blur(40px);
            opacity: 0.4;
            animation: floatGlow 10s ease-in-out infinite alternate;
            z-index: 0;
        }

        @keyframes floatGlow {
            0% { transform: translateY(0) scale(1); opacity: 0.35; }
            50% { transform: translateY(-30px) scale(1.1); opacity: 0.55; }
            100% { transform: translateY(0) scale(1); opacity: 0.35; }
        }

        .glow:nth-child(1) {
            width: 200px; height: 200px;
            top: 10%; left: 8%;
            background: radial-gradient(circle at center, rgba(255, 215, 0, 0.8), rgba(255, 0, 150, 0.1));
            animation-delay: 0s;
        }
        .glow:nth-child(2) {
            width: 180px; height: 180px;
            bottom: 12%; left: 10%;
            background: radial-gradient(circle at center, rgba(255, 105, 180, 0.7), rgba(75, 0, 130, 0.1));
            animation-delay: 2s;
        }
        .glow:nth-child(3) {
            width: 220px; height: 220px;
            top: 18%; right: 8%;
            background: radial-gradient(circle at center, rgba(255, 255, 120, 0.8), rgba(255, 100, 0, 0.1));
            animation-delay: 1s;
        }
        .glow:nth-child(4) {
            width: 200px; height: 200px;
            bottom: 10%; right: 6%;
            background: radial-gradient(circle at center, rgba(173, 216, 230, 0.7), rgba(255, 255, 255, 0.05));
            animation-delay: 3s;
        }

        .navbar {
            background: rgba(20, 20, 35, 0.75);
            backdrop-filter: blur(10px);
            box-shadow: 0 3px 12px rgba(0, 0, 0, 0.25);
            z-index: 5;
        }

        .navbar-brand {
            color: #FFD54F !important;
            font-weight: 600;
            letter-spacing: 0.5px;
        }

        .nav-link {
            color: #FFD54F !important;
            font-weight: 500;
            transition: 0.3s;
        }

        .nav-link:hover {
            color: #fff !important;
            background-color: rgba(255, 213, 79, 0.2);
            border-radius: 6px;
        }

        .btn-logout {
            background-color: #FFD54F;
            color: #4B0082;
            font-weight: 600;
            border: none;
            border-radius: 8px;
            padding: 6px 16px;
            transition: 0.3s;
        }

        .btn-logout:hover {
            background-color: #4B0082;
            color: #fff;
        }

        .dashboard-header {
            text-align: center;
            margin-top: 60px;
            position: relative;
            z-index: 2;
        }

        .dashboard-header h1 {
            font-weight: 600;
            color: #FFD54F;
            text-shadow: 0 2px 8px rgba(0, 0, 0, 0.3);
        }

        .dashboard-header p {
            color: #ffe8b3;
            font-size: 1.1rem;
        }

        .card {
            border: none;
            border-radius: 18px;
            background: linear-gradient(135deg,
                #2B0F4A 0%,   
                #7A1F4D 100%  
            );
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.25);
            transition: transform 0.25s ease, box-shadow 0.25s ease;
            position: relative;
            z-index: 2;
        }

        .card:hover {
            transform: translateY(-6px);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.35);
        }

        .card-title {
            color: #fff7d0;
            font-weight: 700;
            margin-top: 10px;
        }

        .menu-icon {
            font-size: 2.8rem;
            color: #FFD54F;
            margin-bottom: 10px;
        }

        .card-text {
            color: #ffe8b3;
            opacity: 0.9;
        }

        .btn-custom {
            background-color: #FFD54F;
            color: #4B0082;
            border: none;
            font-weight: 700;
            transition: 0.3s;
        }

        .btn-custom:hover {
            background-color: #4B0082;
            color: #fff;
        }

        .footer {
            text-align: center;
            padding: 30px 0;
            color: #fff7d0;
            font-size: 0.9rem;
            margin-top: 70px;
            border-top: 1px solid rgba(255,255,255,0.15);
            position: relative;
            z-index: 2;
        }
    </style>
</head>
<body>
    <div class="glow"></div>
    <div class="glow"></div>
    <div class="glow"></div>
    <div class="glow"></div>

    <nav class="navbar navbar-expand-lg">
        <div class="container">
            <a class="navbar-brand" href="dashboard.php">💼 Admin Dashboard</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
                <ul class="navbar-nav align-items-center">
                    <li class="nav-item"><a class="nav-link" href="../views/admin/barang/DataBarang.php">📦 Data Barang</a></li>
                    <li class="nav-item"><a class="nav-link" href="../admin/pengadaan/DataPengadaan.php">🧾 Pengadaan</a></li>
                    <li class="nav-item"><a class="nav-link" href="../admin/laporan/DataLaporan.php">📊 Laporan</a></li>
                    <li class="nav-item ms-3">
                        <a href="../../auth/logout.php" class="btn btn-logout">🚪 Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <section class="dashboard-header">
        <div class="container">
            <h1>Selamat Datang, Admin!</h1>
            <p>Kelola data barang dan laporan penjualan dengan tampilan yang lebih berwarna ✨</p>
        </div>
    </section>

    <div class="container mt-5">
        <div class="row g-4">
            <!-- Card lama (3 tetap) -->
            <div class="col-md-4">
                <div class="card p-4 h-100">
                    <div class="card-body text-center">
                        <i class="bi bi-box-seam menu-icon"></i>
                        <h5 class="card-title">Data Barang</h5>
                        <p class="card-text">Lihat dan kelola stok barang di sistem.</p>
                        <a href="../views/admin/barang/DataBarang.php" class="btn btn-custom mt-2">Lihat Barang</a>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card p-4 h-100">
                    <div class="card-body text-center">
                        <i class="bi bi-truck menu-icon"></i>
                        <h5 class="card-title">Data Pengadaan</h5>
                        <p class="card-text">Kelola data pengadaan barang.</p>
                        <a href="../views/admin/pengadaan/DataPengadaan.php" class="btn btn-custom mt-2">Buka Pengadaan</a>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card p-4 h-100">
                    <div class="card-body text-center">
                        <i class="bi bi-graph-up-arrow menu-icon"></i>
                        <h5 class="card-title">Laporan Penjualan</h5>
                        <p class="card-text">Pantau performa penjualan secara visual.</p>
                        <a href="../admin/laporan/DataLaporan.php" class="btn btn-custom mt-2">Lihat Laporan</a>
                    </div>
                </div>
            </div>

            <!-- Tambahan baru -->
            <div class="col-md-4">
                <div class="card p-4 h-100">
                    <div class="card-body text-center">
                        <i class="bi bi-building menu-icon"></i>
                        <h5 class="card-title">Data Vendor</h5>
                        <p class="card-text">Kelola daftar vendor penyedia barang.</p>
                        <a href="../views/admin/vendor/DataVendor.php" class="btn btn-custom mt-2">Lihat Vendor</a>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card p-4 h-100">
                    <div class="card-body text-center">
                        <i class="bi bi-box2-heart menu-icon"></i>
                        <h5 class="card-title">Data Penerimaan</h5>
                        <p class="card-text">Lihat dan kelola data penerimaan barang.</p>
                        <a href="../views/admin/penerimaan/DataPenerimaan.php" class="btn btn-custom mt-2">Lihat Penerimaan</a>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card p-4 h-100">
                    <div class="card-body text-center">
                        <i class="bi bi-cash-stack menu-icon"></i>
                        <h5 class="card-title">Data Penjualan</h5>
                        <p class="card-text">Pantau transaksi penjualan secara lengkap.</p>
                        <a href="../views/admin/penjualan/DataPenjualan.php" class="btn btn-custom mt-2">Lihat Penjualan</a>
                    </div>
                </div>
            </div>

            <div class="col-md-4 mx-auto">
                <div class="card p-4 h-100">
                    <div class="card-body text-center">
                        <i class="bi bi-arrow-repeat menu-icon"></i>
                        <h5 class="card-title">Data Retur</h5>
                        <p class="card-text">Kelola data retur barang dari pelanggan.</p>
                        <a href="../views/admin/retur/DataRetur.php" class="btn btn-custom mt-2">Lihat Retur</a>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <footer class="footer">
        © <?= date('Y') ?> Sistem Penjualan — Designed by Ocha Della 💜🔥
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

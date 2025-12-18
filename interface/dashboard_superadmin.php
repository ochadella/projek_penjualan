<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include_once '../config/koneksi.php';

// Gunakan koneksi dari class DBConnection (TIDAK DIUBAH)
$db   = new DBConnection();
$conn = $db->getConnection();

// Ambil data dari database
$viewQuery = "SELECT total_barang, total_vendor, total_penjualan FROM view_dashboard_superadmin LIMIT 1";
$result = mysqli_query($conn, $viewQuery);

if ($result && mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_assoc($result);
    $totalBarang    = $row['total_barang'] ?? 0;
    $totalVendor    = $row['total_vendor'] ?? 0;
    $totalPenjualan = $row['total_penjualan'] ?? 0;
} else {
    // fallback kalau view belum dibuat / kosong
    $totalBarang = $totalVendor = $totalPenjualan = 0;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Superadmin</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary-purple: #4B0082;
            --primary-pink:   #C13584;
            --primary-red:    #E94057;
            --primary-orange: #F27121;
            --primary-yellow: #FFD54F;

            --glass-bg: rgba(12, 10, 30, 0.72);
            --card-bg:  rgba(27, 16, 55, 0.95);
        }

        /* ========== BASE ========== */
        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
            min-height: 100vh;
            overflow-x: hidden;
            background: linear-gradient(
                135deg,
                #4B0082 0%,
                #C13584 25%,
                #E94057 50%,
                #F27121 75%,
                #FFD54F 100%
            );
            background-size: 300% 300%;
            animation: gradientFlow 12s ease infinite;
            color: #fff;
        }

        @keyframes gradientFlow {
            0%   { background-position: 0% 50%; }
            50%  { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        /* Glow blobs */
        .glow {
            position: fixed;
            border-radius: 50%;
            filter: blur(45px);
            opacity: 0.45;
            pointer-events: none;
            animation: floatGlow 10s ease-in-out infinite alternate;
            z-index: 0;
        }

        @keyframes floatGlow {
            0%   { transform: translateY(0) scale(1);   opacity: 0.35; }
            50%  { transform: translateY(-30px) scale(1.1); opacity: 0.6; }
            100% { transform: translateY(0) scale(1);   opacity: 0.35; }
        }

        .glow:nth-child(1) {
            width: 260px; height: 260px;
            top: 5%; left: 6%;
            background: radial-gradient(circle at center, rgba(255,215,0,0.9), rgba(255,0,150,0.15));
        }
        .glow:nth-child(2) {
            width: 220px; height: 220px;
            bottom: 8%; left: 10%;
            background: radial-gradient(circle at center, rgba(255,105,180,0.8), rgba(75,0,130,0.15));
            animation-delay: 1.5s;
        }
        .glow:nth-child(3) {
            width: 260px; height: 260px;
            top: 15%; right: 10%;
            background: radial-gradient(circle at center, rgba(255,255,150,0.9), rgba(255,100,0,0.15));
            animation-delay: 0.8s;
        }
        .glow:nth-child(4) {
            width: 220px; height: 220px;
            bottom: 5%; right: 6%;
            background: radial-gradient(circle at center, rgba(173,216,230,0.8), rgba(255,255,255,0.08));
            animation-delay: 2.2s;
        }

        /* ========== SIDEBAR ========== */
        .sidebar {
            width: 250px;
            height: 100vh;
            position: fixed;
            left: 0;
            top: 0;
            background: rgba(15, 9, 32, 0.92);
            backdrop-filter: blur(14px);
            box-shadow: 14px 0 30px rgba(0,0,0,0.55);
            transition: all 0.3s ease;
            z-index: 1000;
            display: flex;
            flex-direction: column;
        }

        .sidebar.collapsed {
            width: 84px;
        }

        .brand {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            font-weight: 700;
            font-size: 1.5rem;
            color: var(--primary-yellow);
            padding: 24px 16px;
            text-shadow: 0 0 10px rgba(255, 213, 79, 0.9);
            border-bottom: 1px solid rgba(255,255,255,0.06);
        }

        .brand .text {
            display: inline;
            white-space: nowrap;
        }

        .sidebar.collapsed .brand .text {
            display: none;
        }

        .sidebar-nav {
            list-style: none;
            margin: 20px 0 0;
            padding: 0 12px 0 12px;
            flex: 1;
            overflow-y: auto;
        }

        .sidebar-nav li {
            margin-bottom: 8px;
        }

        .sidebar-nav a {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 12px;
            border-radius: 10px;
            text-decoration: none;
            color: #FFECAA;
            font-weight: 500;
            font-size: 0.96rem;
            transition: 0.25s ease;
            cursor: pointer;
        }

        .sidebar-nav a i {
            font-size: 1.25rem;
        }

        .sidebar-nav a:hover,
        .sidebar-nav a.active {
            background: linear-gradient(135deg, rgba(255,213,79,0.25), rgba(255,213,79,0.05));
            color: #ffffff;
            box-shadow: 0 0 14px rgba(255,213,79,0.4);
        }

        .sidebar.collapsed .sidebar-nav a span {
            display: none;
        }

        .sidebar-footer {
            padding: 14px 16px 18px;
            border-top: 1px solid rgba(255,255,255,0.06);
            font-size: 0.78rem;
            color: rgba(255,255,255,0.65);
        }

        /* ========== TOGGLE BUTTON ========== */
        .toggle-btn {
            position: fixed;
            top: 20px;
            left: 260px;
            font-size: 1.4rem;
            background: radial-gradient(circle at 30% 30%, #FFD54F, #F27121);
            color: #4B0082;
            border-radius: 999px;
            width: 44px;
            height: 44px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 8px 20px rgba(0,0,0,0.45);
            z-index: 1100;
            border: none;
        }

        .toggle-btn:hover {
            transform: translateY(-2px) rotate(90deg);
        }

        .sidebar.collapsed ~ .toggle-btn {
            left: 100px;
        }

        /* ========== MAIN CONTENT ========== */
        .content {
            margin-left: 260px;
            padding: 32px 40px;
            transition: margin-left 0.3s ease;
            position: relative;
            z-index: 2;
        }

        .sidebar.collapsed ~ .content {
            margin-left: 120px;
        }

        .topbar {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-bottom: 24px;
            gap: 450px;
        }

        .breadcrumb-text {
            font-size: 0.85rem;
            color: #FFEFD0;
            opacity: 0.85;
        }

        .search-box {
            display: flex;
            align-items: center;
            background: rgba(10, 7, 30, 0.7);
            border-radius: 999px;
            padding: 8px 14px;
            min-width: 240px;
            border: 1px solid rgba(255,255,255,0.18);
        }

        .search-box i {
            font-size: 0.95rem;
            color: #FFECAA;
            margin-right: 6px;
        }

        .search-box input {
            border: none;
            outline: none;
            background: transparent;
            color: #fff;
            font-size: 0.9rem;
            width: 100%;
        }

        .topbar-user {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .avatar {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            background: radial-gradient(circle at 30% 30%, #FFD54F, #F27121);
            color: #4B0082;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 12px rgba(0,0,0,0.35);
        }

        .user-meta {
            font-size: 0.83rem;
        }
        .user-meta .name {
            font-weight: 600;
        }
        .user-meta .role {
            opacity: 0.85;
        }

        /* Main glass container */
        .dashboard-shell {
            overflow: hidden;
            padding-bottom: 20px;
        }

        .dashboard-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 16px;
            margin-bottom: 22px;
        }

        .page-title {
            font-size: 1.4rem;
            font-weight: 600;
        }

        .page-subtitle {
            font-size: 0.9rem;
            color: #FFE8C8;
            opacity: 0.9;
        }

        .header-actions {
            display: flex;
            gap: 10px;
        }

        .btn-pill {
            border-radius: 999px;
            font-size: 0.86rem;
            padding: 7px 16px;
            border-width: 1px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .btn-outline-light-custom {
            border-color: rgba(255,255,255,0.4);
            color: #FFEFD8;
            background: transparent;
        }
        .btn-outline-light-custom:hover {
            background: rgba(255,255,255,0.12);
            color: #fff;
        }

        .btn-primary-custom {
            border: none;
            background: radial-gradient(circle at 30% 30%, #FFD54F, #F27121);
            color: #4B0082;
            font-weight: 600;
            box-shadow: 0 8px 18px rgba(0,0,0,0.45);
        }
        .btn-primary-custom:hover {
            filter: brightness(1.05);
            transform: translateY(-1px);
        }

        /* ========== METRIC CARDS ========== */
        .metric-card {
            background: linear-gradient(135deg,
                rgba(255,255,255,0.12),
                rgba(255,255,255,0.05)
            );
            border-radius: 18px;
            padding: 18px 18px 16px;
            position: relative;
            overflow: hidden;
            box-shadow:
                0 10px 26px rgba(0,0,0,0.45),
                0 0 0 1px rgba(255,255,255,0.05);
        }

        .metric-card::before {
            content: "";
            position: absolute;
            inset: -40%;
            background: radial-gradient(circle at top left,
                rgba(255,213,79,0.32),
                transparent 55%);
            opacity: 0.85;
            pointer-events: none;
        }

        .metric-label {
            font-size: 0.85rem;
            letter-spacing: 0.03em;
            text-transform: uppercase;
            color: #FFEFD0;
            opacity: 0.95;
            position: relative;
            z-index: 1;
        }

        .metric-value {
            font-size: 2.4rem;
            font-weight: 700;
            margin-top: 6px;
            color: #FFECAA;
            text-shadow: 0 0 18px rgba(255,213,79,0.9);
            position: relative;
            z-index: 1;
        }

        .metric-footnote {
            margin-top: 8px;
            font-size: 0.8rem;
            opacity: 0.85;
            position: relative;
            z-index: 1;
        }

        /* ========== PANEL CARDS (middle area) ========== */
        .panel-card {
            background: var(--card-bg);
            border-radius: 18px;
            padding: 18px 18px 16px;
            box-shadow:
                0 10px 26px rgba(0,0,0,0.55),
                0 0 0 1px rgba(255,255,255,0.04);
            height: 100%;
        }

        .panel-head {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 12px;
        }

        .panel-title {
            font-size: 0.96rem;
            font-weight: 600;
        }

        .panel-tag {
            font-size: 0.75rem;
            padding: 3px 10px;
            border-radius: 999px;
            background: rgba(255,255,255,0.06);
            color: #FFEFD0;
        }

        .activity-list {
            list-style: none;
            padding: 0;
            margin: 0;
            font-size: 0.85rem;
        }

        .activity-list li {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 6px 0;
            border-bottom: 1px dashed rgba(255,255,255,0.08);
        }

        .activity-list li:last-child {
            border-bottom: none;
        }

        .activity-label {
            opacity: 0.9;
        }

        .activity-time {
            font-size: 0.75rem;
            opacity: 0.75;
        }

        .admin-info-item {
            display: flex;
            justify-content: space-between;
            font-size: 0.86rem;
            padding: 4px 0;
        }

        .status-pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 3px 8px;
            border-radius: 999px;
            font-size: 0.78rem;
            background: rgba(46, 204, 113, 0.18);
            color: #adffb4;
        }

        .status-dot {
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background: #2ecc71;
            box-shadow: 0 0 6px #2ecc71;
        }

        /* ========== MENU CARDS (bottom) ========== */
        .menu-card {
            border: none;
            border-radius: 20px;
            background: linear-gradient(135deg, #2B0F4A 0%, #7A1F4D 100%);
            box-shadow: 0 12px 26px rgba(0,0,0,0.55);
            transition: transform 0.25s ease, box-shadow 0.25s ease;
            height: 210px;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            position: relative;
        }

        .menu-card::before {
            content: "";
            position: absolute;
            inset: -40%;
            background: radial-gradient(circle at top center,
                rgba(255,213,79,0.25),
                transparent 60%);
            opacity: 0.9;
        }

        .menu-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 18px 32px rgba(0,0,0,0.65);
        }

        .menu-card .card-body {
            position: relative;
            z-index: 1;
        }

        .menu-icon {
            font-size: 2.4rem;
            color: var(--primary-yellow);
            margin-bottom: 8px;
            text-shadow: 0 0 14px rgba(255,213,79,0.9);
        }

        .menu-title {
            font-weight: 600;
            margin-bottom: 12px;
        }

        .btn-menu {
            border-radius: 999px;
            background: #ffffff;
            color: #2B0F4A;
            font-weight: 600;
            padding: 6px 18px;
            font-size: 0.88rem;
            border: none;
        }

        .btn-menu:hover {
            background: #2B0F4A;
            color: #fff;
            transform: translateY(-2px);
        }

        /* ========== RESPONSIVE ========== */
        @media (max-width: 991.98px) {
            .dashboard-header {
                flex-direction: column;
                align-items: flex-start;
            }
            .header-actions {
                align-self: flex-end;
            }
        }

        @media (max-width: 767.98px) {
            .sidebar {
                transform: translateX(-100%);
            }
            .sidebar.collapsed {
                transform: translateX(0);
                width: 210px;
            }
            .toggle-btn {
                left: 20px !important;
            }
            .sidebar.collapsed ~ .toggle-btn {
                left: 240px !important;
            }
            .content {
                margin-left: 0 !important;
                padding: 22px 18px;
            }
        }
        .menu-category {
            font-size: 0.75rem;
            text-transform: uppercase;
            opacity: 0.6;
            margin: 14px 0 6px 4px;
            letter-spacing: 0.5px;
        }

        .submenu a {
            padding-left: 28px !important;
        }


        /* ======== FIX AKHIR LAPISAN GELAP DI BELAKANG MENU-CARD ======== */
        .main-content, 
        .content-wrapper, 
        .dashboard-shell {
            background: transparent !important;
            box-shadow: none !important;
            overflow: visible !important;
        }

        body {
            background: linear-gradient(180deg, #4B0082 0%, #C13584 25%, #E94057 50%, #F27121 75%, #FFD54F 100%) fixed;
        }

        .row.g-3:last-child {
            margin-bottom: 40px;
        }
    </style>
</head>

<body>
    <!-- Glow layers -->
    <div class="glow"></div>
    <div class="glow"></div>
    <div class="glow"></div>
    <div class="glow"></div>

    <!-- SIDEBAR -->
    <div class="sidebar" id="sidebar">
        <div class="brand">
            <span>👑</span>
            <span class="text">Superadmin</span>
        </div>

        <<ul class="sidebar-nav">

        <!-- DASHBOARD -->
        <li class="menu-category">Main Menu</li>
        <li><a href="dashboard_superadmin.php" class="active">
            <i class="bi bi-speedometer2"></i><span>Dashboard</span></a>
        </li>

        <!-- DATA MASTER -->
        <li class="menu-category">Data Master</li>
        <li class="submenu"><a href="../views/superadmin/role/ManajemenRole.php">
            <i class="bi bi-puzzle-fill"></i><span>Data Role</span></a></li>
        <li class="submenu"><a href="../views/superadmin/user/DataUser.php">
            <i class="bi bi-people-fill"></i><span>Data User</span></a></li>
        <li class="submenu"><a href="../views/superadmin/satuan/DataSatuan.php">
            <i class="bi bi-calculator-fill"></i><span>Data Satuan</span></a></li>
        <li class="submenu"><a href="../views/superadmin/vendor/DataVendor.php">
            <i class="bi bi-buildings-fill"></i><span>Data Vendor</span></a></li>
        <li class="submenu"><a href="../views/superadmin/barang/DataBarang.php">
            <i class="bi bi-cart-fill"></i><span>Data Barang</span></a></li>

        <!-- TRANSAKSI -->
        <li class="menu-category">Transaksi</li>
        <li class="submenu"><a href="../views/superadmin/margin/MarginPenjualan.php">
            <i class="bi bi-graph-up-arrow"></i><span>Margin Penjualan</span></a></li>
        <li class="submenu"><a href="../views/superadmin/pengadaan/DataPengadaan.php">
            <i class="bi bi-box-seam"></i><span>Data Pengadaan</span></a></li>
        <li class="submenu"><a href="../views/superadmin/penerimaan/DataPenerimaan.php">
            <i class="bi bi-clipboard-check"></i><span>Data Penerimaan</span></a></li>
        <li class="submenu"><a href="../views/superadmin/penjualan/DataPenjualan.php">
            <i class="bi bi-cash-coin"></i><span>Data Penjualan</span></a></li>
        <li class="submenu"><a href="../views/superadmin/kartustok/DataKartuStok.php">
            <i class="bi bi-clipboard-pulse"></i><span>Kartu Stok</span></a></li>
        <li class="submenu"><a href="../views/superadmin/retur/DataRetur.php">
            <i class="bi bi-recycle"></i><span>Data Retur</span></a></li>

        <!-- LAPORAN -->
        <li class="menu-category">Laporan & Monitoring</li>
        <li class="submenu"><a href="../views/superadmin/log/LogAktivitas.php">
            <i class="bi bi-clipboard-data-fill"></i><span>Log Aktivitas</span></a></li>
        <li class="submenu"><a href="../views/superadmin/laporan/LaporanGlobal.php">
            <i class="bi bi-bar-chart-fill"></i><span>Laporan Global</span></a></li>

        <!-- SISTEM -->
        <li class="menu-category">Sistem</li>
        <li class="submenu"><a href="../auth/login.php">
            <i class="bi bi-box-arrow-right"></i><span>Logout</span></a></li>

    </ul>
        <div class="sidebar-footer">
            © <?= date('Y') ?> Sistem Penjualan<br>Superadmin Panel
        </div>
    </div>

    <!-- TOGGLE -->
    <button class="toggle-btn" id="toggleBtn">
        <i class="bi bi-list"></i>
    </button>

    <!-- MAIN CONTENT -->
    <div class="content">
        <!-- Topbar -->
        <div class="topbar">
            <div class="breadcrumb-text">
                Menu &raquo; Dashboard
            </div>
            <div class="d-flex align-items-center gap-3">
                <div class="search-box">
                    <i class="bi bi-search"></i>
                    <input type="text" placeholder="Cari fitur atau data...">
                </div>
                <div class="topbar-user">
                    <div class="avatar">SA</div>
                    <div class="user-meta">
                        <div class="name">Admin Utama</div>
                        <div class="role">Superadmin</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="dashboard-shell">
            <!-- Header dalam shell -->
            <div class="dashboard-header">
                <div>
                    <div class="page-title">Dashboard Superadmin</div>
                    <div class="page-subtitle">
                        Pantau statistik sistem dan akses cepat ke fitur penting.
                    </div>
                </div>
                <div class="header-actions">
                    <button class="btn btn-sm btn-pill btn-outline-light-custom" onclick="location.href='../views/superadmin/laporan/LaporanGlobal.php'">
                        <i class="bi bi-file-earmark-text"></i> Laporan Global
                    </button>
                    <button class="btn btn-sm btn-pill btn-primary-custom" onclick="location.href='../views/superadmin/user/TambahUser.php'">
                        <i class="bi bi-person-plus"></i> Tambah User
                    </button>
                </div>
            </div>

            <!-- Metric cards -->
            <div class="row g-3 mb-3">
                <div class="col-md-4">
                    <div class="metric-card">
                        <div class="metric-label">Total Barang</div>
                        <div class="metric-value"><?= $totalBarang ?></div>
                        <div class="metric-footnote">Jumlah item yang terdaftar di sistem.</div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="metric-card">
                        <div class="metric-label">Total Vendor</div>
                        <div class="metric-value"><?= $totalVendor ?></div>
                        <div class="metric-footnote">Rekanan pemasok aktif saat ini.</div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="metric-card">
                        <div class="metric-label">Total Penjualan</div>
                        <div class="metric-value"><?= $totalPenjualan ?></div>
                        <div class="metric-footnote">Transaksi penjualan yang tercatat.</div>
                    </div>
                </div>
            </div>

            <!-- Middle panels -->
            <div class="row g-3 mb-3">
                <div class="col-lg-8">
                    <div class="panel-card">
                        <div class="panel-head">
                            <div class="panel-title">Ringkasan Aktivitas Terbaru</div>
                            <span class="panel-tag">Log Sistem</span>
                        </div>
                        <ul class="activity-list">
                            <li>
                                <span class="activity-label">User baru ditambahkan</span>
                                <span class="activity-time">Baru saja</span>
                            </li>
                            <li>
                                <span class="activity-label">Perubahan hak akses role</span>
                                <span class="activity-time">10 menit lalu</span>
                            </li>
                            <li>
                                <span class="activity-label">Export laporan penjualan</span>
                                <span class="activity-time">Hari ini, 09:32</span>
                            </li>
                            <li>
                                <span class="activity-label">Login Superadmin</span>
                                <span class="activity-time">Hari ini, 08:15</span>
                            </li>
                        </ul>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="panel-card mb-3">
                        <div class="panel-head">
                            <div class="panel-title">Info Admin</div>
                        </div>
                        <div class="admin-info-item">
                            <span>Versi Sistem</span>
                            <span>1.0.0</span>
                        </div>
                        <div class="admin-info-item">
                            <span>Database</span>
                            <span class="status-pill">
                                <span class="status-dot"></span> Aktif
                            </span>
                        </div>
                        <div class="admin-info-item">
                            <span>Last Update</span>
                            <span><?= date('d M Y') ?></span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bottom menu cards -->
            <div class="row g-3">
                <div class="col-md-3">
                    <div class="menu-card text-center">
                        <div class="card-body">
                            <i class="bi bi-people-fill menu-icon"></i>
                            <div class="menu-title">Kelola User</div>
                            <button class="btn btn-menu" onclick="location.href='../views/superadmin/user/DataUser.php'">Kelola</button>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="menu-card text-center">
                        <div class="card-body">
                            <i class="bi bi-gear-fill menu-icon"></i>
                            <div class="menu-title">Manajemen Role</div>
                            <button class="btn btn-menu" onclick="location.href='../views/superadmin/role/ManajemenRole.php'">Atur</button>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="menu-card text-center">
                        <div class="card-body">
                            <i class="bi bi-clipboard-data-fill menu-icon"></i>
                            <div class="menu-title">Log Aktivitas</div>
                            <button class="btn btn-menu" onclick="location.href='../views/superadmin/log/LogAktivitas.php'">Lihat</button>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="menu-card text-center">
                        <div class="card-body">
                            <i class="bi bi-bar-chart-fill menu-icon"></i>
                            <div class="menu-title">Laporan Global</div>
                            <button class="btn btn-menu" onclick="location.href='../views/superadmin/laporan/LaporanGlobal.php'">Lihat</button>
                        </div>
                    </div>
                </div>
            </div>

        </div><!-- /dashboard-shell -->
    </div><!-- /content -->

    <script>
        const sidebar  = document.getElementById('sidebar');
        const toggleBtn = document.getElementById('toggleBtn');

        toggleBtn.onclick = () => {
            sidebar.classList.toggle('collapsed');
        };
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
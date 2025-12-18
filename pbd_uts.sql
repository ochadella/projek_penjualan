-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:8889
-- Waktu pembuatan: 01 Okt 2025 pada 02.03
-- Versi server: 8.0.40
-- Versi PHP: 8.3.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `pbd_uts`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `barang`
--

CREATE TABLE `barang` (
  `idbarang` int NOT NULL,
  `jenis` varchar(20) NOT NULL,
  `nama` varchar(45) NOT NULL,
  `idsatuan` int NOT NULL,
  `jumlah` int NOT NULL,
  `status` tinyint NOT NULL,
  `harga` int NOT NULL,
  `nama_satuan` varchar(50) DEFAULT NULL,
  `harga_beli` int DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data untuk tabel `barang`
--

INSERT INTO `barang` (`idbarang`, `jenis`, `nama`, `idsatuan`, `jumlah`, `status`, `harga`, `nama_satuan`, `harga_beli`) VALUES
(1, 'ATK', 'Buku', 3, 1, 1, 40000, 'Lusin', 0);

-- --------------------------------------------------------

--
-- Struktur dari tabel `detail_penerimaan`
--

CREATE TABLE `detail_penerimaan` (
  `iddetail_penerimaan` bigint NOT NULL,
  `idpenerimaan` bigint NOT NULL,
  `barang_idbarang` int NOT NULL,
  `jumlah_terima` int NOT NULL,
  `harga_satuan_terima` int NOT NULL,
  `sub_total_terima` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `detail_pengadaan`
--

CREATE TABLE `detail_pengadaan` (
  `iddetail_pengadaan` bigint NOT NULL,
  `harga_satuan` int NOT NULL,
  `jumlah` int NOT NULL,
  `sub_total` int NOT NULL,
  `idbarang` int NOT NULL,
  `idpengadaan` bigint NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `detail_penjualan`
--

CREATE TABLE `detail_penjualan` (
  `iddetail_penjualan` bigint NOT NULL,
  `harga_satuan` int NOT NULL,
  `jumlah` int NOT NULL,
  `subtotal` int NOT NULL,
  `idpenjualan` int NOT NULL,
  `idbarang` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `detail_retur_barang`
--

CREATE TABLE `detail_retur_barang` (
  `iddetail_retur` int NOT NULL,
  `jumlah` int NOT NULL,
  `alasan` varchar(200) NOT NULL,
  `idretur` bigint NOT NULL,
  `iddetail_penerimaan` bigint NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `kartu_stok`
--

CREATE TABLE `kartu_stok` (
  `idkartu_stok` bigint NOT NULL,
  `jenis_transaksi` char(1) NOT NULL,
  `masuk` int NOT NULL,
  `keluar` int NOT NULL,
  `stock` int NOT NULL,
  `created_at` timestamp NOT NULL,
  `idtransaksi` bigint NOT NULL,
  `idbarang` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `margin_penjualan`
--

CREATE TABLE `margin_penjualan` (
  `idmargin_penjualan` int NOT NULL,
  `created_at` timestamp NOT NULL,
  `persen` double NOT NULL,
  `status` tinyint NOT NULL,
  `iduser` int NOT NULL,
  `updated_at` timestamp NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `penerimaan`
--

CREATE TABLE `penerimaan` (
  `idpenerimaan` bigint NOT NULL,
  `created_at` timestamp NOT NULL,
  `status` char(1) NOT NULL,
  `idpengadaan` bigint NOT NULL,
  `iduser` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `pengadaan`
--

CREATE TABLE `pengadaan` (
  `idpengadaan` bigint NOT NULL,
  `timestamp` timestamp NOT NULL,
  `user_iduser` int NOT NULL,
  `status` char(1) NOT NULL,
  `vendor_idvendor` int NOT NULL,
  `subtotal_nilai` int NOT NULL,
  `ppn` int NOT NULL,
  `total_nilai` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `penjualan`
--

CREATE TABLE `penjualan` (
  `idpenjualan` int NOT NULL,
  `created_at` timestamp NOT NULL,
  `subtotal_nilai` int NOT NULL,
  `ppn` int NOT NULL,
  `total_nilai` int NOT NULL,
  `iduser` int NOT NULL,
  `idmargin_penjualan` int NOT NULL,
  `tanggal` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `retur_barang`
--

CREATE TABLE `retur_barang` (
  `idretur` bigint NOT NULL,
  `created_at` timestamp NOT NULL,
  `idpenerimaan` bigint NOT NULL,
  `iduser` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `role`
--

CREATE TABLE `role` (
  `idrole` int NOT NULL,
  `nama_role` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `satuan`
--

CREATE TABLE `satuan` (
  `idsatuan` int NOT NULL,
  `nama_satuan` varchar(45) DEFAULT NULL,
  `status` tinyint DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data untuk tabel `satuan`
--

INSERT INTO `satuan` (`idsatuan`, `nama_satuan`, `status`) VALUES
(1, 'Pcs', 1),
(2, 'Box', 1),
(3, 'Lusin', 1),
(4, 'Kg', 1),
(5, 'Liter', 1);

-- --------------------------------------------------------

--
-- Struktur dari tabel `user`
--

CREATE TABLE `user` (
  `iduser` int NOT NULL,
  `username` varchar(45) NOT NULL,
  `password` varchar(100) NOT NULL,
  `idrole` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `vendor`
--

CREATE TABLE `vendor` (
  `idvendor` int NOT NULL,
  `nama_vendor` varchar(100) NOT NULL,
  `badan_hukum` char(1) NOT NULL,
  `status` char(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `barang`
--
ALTER TABLE `barang`
  ADD PRIMARY KEY (`idbarang`),
  ADD KEY `barang_ibfk_1` (`idsatuan`);

--
-- Indeks untuk tabel `detail_penerimaan`
--
ALTER TABLE `detail_penerimaan`
  ADD PRIMARY KEY (`iddetail_penerimaan`),
  ADD KEY `idpenerimaan` (`idpenerimaan`),
  ADD KEY `barang_idbarang` (`barang_idbarang`);

--
-- Indeks untuk tabel `detail_pengadaan`
--
ALTER TABLE `detail_pengadaan`
  ADD PRIMARY KEY (`iddetail_pengadaan`),
  ADD KEY `idbarang` (`idbarang`),
  ADD KEY `idpengadaan` (`idpengadaan`);

--
-- Indeks untuk tabel `detail_penjualan`
--
ALTER TABLE `detail_penjualan`
  ADD PRIMARY KEY (`iddetail_penjualan`),
  ADD KEY `idpenjualan` (`idpenjualan`),
  ADD KEY `idbarang` (`idbarang`);

--
-- Indeks untuk tabel `detail_retur_barang`
--
ALTER TABLE `detail_retur_barang`
  ADD PRIMARY KEY (`iddetail_retur`),
  ADD KEY `idretur` (`idretur`),
  ADD KEY `iddetail_penerimaan` (`iddetail_penerimaan`);

--
-- Indeks untuk tabel `kartu_stok`
--
ALTER TABLE `kartu_stok`
  ADD PRIMARY KEY (`idkartu_stok`),
  ADD KEY `idbarang` (`idbarang`);

--
-- Indeks untuk tabel `margin_penjualan`
--
ALTER TABLE `margin_penjualan`
  ADD PRIMARY KEY (`idmargin_penjualan`),
  ADD KEY `iduser` (`iduser`);

--
-- Indeks untuk tabel `penerimaan`
--
ALTER TABLE `penerimaan`
  ADD PRIMARY KEY (`idpenerimaan`),
  ADD KEY `idpengadaan` (`idpengadaan`),
  ADD KEY `iduser` (`iduser`);

--
-- Indeks untuk tabel `pengadaan`
--
ALTER TABLE `pengadaan`
  ADD PRIMARY KEY (`idpengadaan`),
  ADD KEY `user_iduser` (`user_iduser`),
  ADD KEY `vendor_idvendor` (`vendor_idvendor`);

--
-- Indeks untuk tabel `penjualan`
--
ALTER TABLE `penjualan`
  ADD PRIMARY KEY (`idpenjualan`),
  ADD KEY `iduser` (`iduser`),
  ADD KEY `idmargin_penjualan` (`idmargin_penjualan`);

--
-- Indeks untuk tabel `retur_barang`
--
ALTER TABLE `retur_barang`
  ADD PRIMARY KEY (`idretur`),
  ADD KEY `idpenerimaan` (`idpenerimaan`),
  ADD KEY `iduser` (`iduser`);

--
-- Indeks untuk tabel `role`
--
ALTER TABLE `role`
  ADD PRIMARY KEY (`idrole`);

--
-- Indeks untuk tabel `satuan`
--
ALTER TABLE `satuan`
  ADD PRIMARY KEY (`idsatuan`);

--
-- Indeks untuk tabel `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`iduser`),
  ADD KEY `idrole` (`idrole`);

--
-- Indeks untuk tabel `vendor`
--
ALTER TABLE `vendor`
  ADD PRIMARY KEY (`idvendor`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `barang`
--
ALTER TABLE `barang`
  MODIFY `idbarang` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `detail_penerimaan`
--
ALTER TABLE `detail_penerimaan`
  MODIFY `iddetail_penerimaan` bigint NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `detail_pengadaan`
--
ALTER TABLE `detail_pengadaan`
  MODIFY `iddetail_pengadaan` bigint NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `detail_penjualan`
--
ALTER TABLE `detail_penjualan`
  MODIFY `iddetail_penjualan` bigint NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `detail_retur_barang`
--
ALTER TABLE `detail_retur_barang`
  MODIFY `iddetail_retur` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `kartu_stok`
--
ALTER TABLE `kartu_stok`
  MODIFY `idkartu_stok` bigint NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `margin_penjualan`
--
ALTER TABLE `margin_penjualan`
  MODIFY `idmargin_penjualan` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `penerimaan`
--
ALTER TABLE `penerimaan`
  MODIFY `idpenerimaan` bigint NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `pengadaan`
--
ALTER TABLE `pengadaan`
  MODIFY `idpengadaan` bigint NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `penjualan`
--
ALTER TABLE `penjualan`
  MODIFY `idpenjualan` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `retur_barang`
--
ALTER TABLE `retur_barang`
  MODIFY `idretur` bigint NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `role`
--
ALTER TABLE `role`
  MODIFY `idrole` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `satuan`
--
ALTER TABLE `satuan`
  MODIFY `idsatuan` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT untuk tabel `user`
--
ALTER TABLE `user`
  MODIFY `iduser` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `vendor`
--
ALTER TABLE `vendor`
  MODIFY `idvendor` int NOT NULL AUTO_INCREMENT;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `barang`
--
ALTER TABLE `barang`
  ADD CONSTRAINT `barang_ibfk_1` FOREIGN KEY (`idsatuan`) REFERENCES `satuan` (`idsatuan`);

--
-- Ketidakleluasaan untuk tabel `detail_penerimaan`
--
ALTER TABLE `detail_penerimaan`
  ADD CONSTRAINT `detail_penerimaan_ibfk_1` FOREIGN KEY (`idpenerimaan`) REFERENCES `penerimaan` (`idpenerimaan`),
  ADD CONSTRAINT `detail_penerimaan_ibfk_2` FOREIGN KEY (`barang_idbarang`) REFERENCES `barang` (`idbarang`);

--
-- Ketidakleluasaan untuk tabel `detail_pengadaan`
--
ALTER TABLE `detail_pengadaan`
  ADD CONSTRAINT `detail_pengadaan_ibfk_1` FOREIGN KEY (`idbarang`) REFERENCES `barang` (`idbarang`),
  ADD CONSTRAINT `detail_pengadaan_ibfk_2` FOREIGN KEY (`idpengadaan`) REFERENCES `pengadaan` (`idpengadaan`);

--
-- Ketidakleluasaan untuk tabel `detail_penjualan`
--
ALTER TABLE `detail_penjualan`
  ADD CONSTRAINT `detail_penjualan_ibfk_1` FOREIGN KEY (`idpenjualan`) REFERENCES `penjualan` (`idpenjualan`),
  ADD CONSTRAINT `detail_penjualan_ibfk_2` FOREIGN KEY (`idbarang`) REFERENCES `barang` (`idbarang`);

--
-- Ketidakleluasaan untuk tabel `detail_retur_barang`
--
ALTER TABLE `detail_retur_barang`
  ADD CONSTRAINT `detail_retur_barang_ibfk_1` FOREIGN KEY (`idretur`) REFERENCES `retur_barang` (`idretur`),
  ADD CONSTRAINT `detail_retur_barang_ibfk_2` FOREIGN KEY (`iddetail_penerimaan`) REFERENCES `detail_penerimaan` (`iddetail_penerimaan`);

--
-- Ketidakleluasaan untuk tabel `kartu_stok`
--
ALTER TABLE `kartu_stok`
  ADD CONSTRAINT `kartu_stok_ibfk_1` FOREIGN KEY (`idbarang`) REFERENCES `barang` (`idbarang`);

--
-- Ketidakleluasaan untuk tabel `margin_penjualan`
--
ALTER TABLE `margin_penjualan`
  ADD CONSTRAINT `margin_penjualan_ibfk_1` FOREIGN KEY (`iduser`) REFERENCES `user` (`iduser`);

--
-- Ketidakleluasaan untuk tabel `penerimaan`
--
ALTER TABLE `penerimaan`
  ADD CONSTRAINT `penerimaan_ibfk_1` FOREIGN KEY (`idpengadaan`) REFERENCES `pengadaan` (`idpengadaan`),
  ADD CONSTRAINT `penerimaan_ibfk_2` FOREIGN KEY (`iduser`) REFERENCES `user` (`iduser`);

--
-- Ketidakleluasaan untuk tabel `pengadaan`
--
ALTER TABLE `pengadaan`
  ADD CONSTRAINT `pengadaan_ibfk_1` FOREIGN KEY (`user_iduser`) REFERENCES `user` (`iduser`),
  ADD CONSTRAINT `pengadaan_ibfk_2` FOREIGN KEY (`vendor_idvendor`) REFERENCES `vendor` (`idvendor`);

--
-- Ketidakleluasaan untuk tabel `penjualan`
--
ALTER TABLE `penjualan`
  ADD CONSTRAINT `penjualan_ibfk_1` FOREIGN KEY (`iduser`) REFERENCES `user` (`iduser`),
  ADD CONSTRAINT `penjualan_ibfk_2` FOREIGN KEY (`idmargin_penjualan`) REFERENCES `margin_penjualan` (`idmargin_penjualan`);

--
-- Ketidakleluasaan untuk tabel `retur_barang`
--
ALTER TABLE `retur_barang`
  ADD CONSTRAINT `retur_barang_ibfk_1` FOREIGN KEY (`idpenerimaan`) REFERENCES `penerimaan` (`idpenerimaan`),
  ADD CONSTRAINT `retur_barang_ibfk_2` FOREIGN KEY (`iduser`) REFERENCES `user` (`iduser`);

--
-- Ketidakleluasaan untuk tabel `user`
--
ALTER TABLE `user`
  ADD CONSTRAINT `user_ibfk_1` FOREIGN KEY (`idrole`) REFERENCES `role` (`idrole`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

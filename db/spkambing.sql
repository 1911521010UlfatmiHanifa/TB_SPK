-- phpMyAdmin SQL Dump
-- version 5.0.4
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 15 Bulan Mei 2022 pada 13.30
-- Versi server: 10.4.17-MariaDB
-- Versi PHP: 8.0.0

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `spkambing`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `alternatif`
--

CREATE TABLE `alternatif` (
  `id_alternatif` int(10) NOT NULL,
  `nama_alternatif` varchar(40) NOT NULL,
  `keterangan` text NOT NULL,
  `tanggal_input` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data untuk tabel `alternatif`
--

INSERT INTO `alternatif` (`id_alternatif`, `nama_alternatif`, `keterangan`, `tanggal_input`) VALUES
(41, 'RUMAH PAK SUTISNA', 'JALAN A1', '2022-05-15'),
(42, 'RUMAH PAK NUNO', 'JALAN A2', '2022-05-15'),
(43, 'RUMAH PAK ADIO', 'JALAN A3', '2022-05-15'),
(44, 'RUMAH PAK MIRO', 'JALAN A4', '2022-05-15');

-- --------------------------------------------------------

--
-- Struktur dari tabel `kriteria`
--

CREATE TABLE `kriteria` (
  `id_kriteria` int(10) NOT NULL,
  `nama` varchar(30) NOT NULL,
  `id_tipe` int(11) NOT NULL,
  `bobot` float NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data untuk tabel `kriteria`
--

INSERT INTO `kriteria` (`id_kriteria`, `nama`, `id_tipe`, `bobot`) VALUES
(66, 'RATA-RATA PENGHASILAN PERBULAN', 2, 5),
(67, 'KONDISI LANTAI', 1, 3),
(68, 'JUMLAH PENGHUNI', 2, 4),
(69, 'KONDISI ATAP', 1, 4),
(70, 'KONDISI DINDING', 1, 4);

-- --------------------------------------------------------

--
-- Struktur dari tabel `nilai_alternatif`
--

CREATE TABLE `nilai_alternatif` (
  `id_nilai_alternatif` int(11) NOT NULL,
  `id_alternatif` int(10) NOT NULL,
  `id_kriteria` int(10) NOT NULL,
  `id_skala` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data untuk tabel `nilai_alternatif`
--

INSERT INTO `nilai_alternatif` (`id_nilai_alternatif`, `id_alternatif`, `id_kriteria`, `id_skala`) VALUES
(226, 41, 66, 62),
(227, 41, 67, 66),
(228, 41, 68, 72),
(229, 41, 69, 79),
(230, 41, 70, 83),
(251, 42, 66, 64),
(252, 42, 67, 66),
(253, 42, 68, 74),
(254, 42, 69, 79),
(255, 42, 70, 83),
(256, 43, 66, 61),
(257, 43, 67, 68),
(258, 43, 68, 73),
(259, 43, 69, 78),
(260, 43, 70, 85),
(266, 44, 66, 62),
(267, 44, 67, 70),
(268, 44, 68, 75),
(269, 44, 69, 81),
(270, 44, 70, 86);

-- --------------------------------------------------------

--
-- Struktur dari tabel `skala_penilaian`
--

CREATE TABLE `skala_penilaian` (
  `id_skala` int(11) NOT NULL,
  `id_kriteria` int(11) NOT NULL,
  `nama` varchar(30) NOT NULL,
  `nilai` float NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `skala_penilaian`
--

INSERT INTO `skala_penilaian` (`id_skala`, `id_kriteria`, `nama`, `nilai`) VALUES
(61, 66, '0 - 1.000.000', 1),
(62, 66, '1.000.001-2.000.000', 2),
(63, 66, '2.000.001-3.000.000', 3),
(64, 66, '3.000.001-4.000.000', 4),
(65, 66, 'DI ATAS 4.000.001', 5),
(66, 67, 'RUSAK SANGAT RINGAN', 1),
(67, 67, 'RUSAK RINGAN', 2),
(68, 67, 'RUSAK SEDANG', 3),
(69, 67, 'RUSAK BERAT', 4),
(70, 67, 'RUSAK SANGAT BERAT', 5),
(71, 68, '8 ORANG ATAU DI ATAS 8 ORANG', 1),
(72, 68, '6-7 ORANG', 2),
(73, 68, '4-5 ORANG', 3),
(74, 68, '2-3 ORANG', 4),
(75, 68, '1 ORANG', 5),
(76, 69, 'RUSAK SANGAT RINGAN', 1),
(78, 69, 'RUSAK RINGAN', 2),
(79, 69, 'RUSAK SEDANG', 3),
(80, 69, 'RUSAK BERAT', 4),
(81, 69, 'RUSAK SANGAT BERAT', 5),
(82, 70, 'RUSAK SANGAT RINGAN', 1),
(83, 70, 'RUSAK RINGAN', 2),
(84, 70, 'RUSAK SEDANG', 3),
(85, 70, 'RUSAK BERAT', 4),
(86, 70, 'RUSAK SANGAT BERAT', 5);

-- --------------------------------------------------------

--
-- Struktur dari tabel `tipe_kriteria`
--

CREATE TABLE `tipe_kriteria` (
  `id_tipe` int(11) NOT NULL,
  `tipe` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `tipe_kriteria`
--

INSERT INTO `tipe_kriteria` (`id_tipe`, `tipe`) VALUES
(1, 'Benefit'),
(2, 'Cost');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `alternatif`
--
ALTER TABLE `alternatif`
  ADD PRIMARY KEY (`id_alternatif`);

--
-- Indeks untuk tabel `kriteria`
--
ALTER TABLE `kriteria`
  ADD PRIMARY KEY (`id_kriteria`),
  ADD KEY `id_tipe` (`id_tipe`);

--
-- Indeks untuk tabel `nilai_alternatif`
--
ALTER TABLE `nilai_alternatif`
  ADD PRIMARY KEY (`id_nilai_alternatif`),
  ADD UNIQUE KEY `id_kambing_2` (`id_alternatif`,`id_kriteria`),
  ADD KEY `id_kambing` (`id_alternatif`),
  ADD KEY `id_kriteria` (`id_kriteria`),
  ADD KEY `id_skala` (`id_skala`);

--
-- Indeks untuk tabel `skala_penilaian`
--
ALTER TABLE `skala_penilaian`
  ADD PRIMARY KEY (`id_skala`),
  ADD KEY `id_kriteria` (`id_kriteria`);

--
-- Indeks untuk tabel `tipe_kriteria`
--
ALTER TABLE `tipe_kriteria`
  ADD PRIMARY KEY (`id_tipe`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `alternatif`
--
ALTER TABLE `alternatif`
  MODIFY `id_alternatif` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=45;

--
-- AUTO_INCREMENT untuk tabel `kriteria`
--
ALTER TABLE `kriteria`
  MODIFY `id_kriteria` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=71;

--
-- AUTO_INCREMENT untuk tabel `nilai_alternatif`
--
ALTER TABLE `nilai_alternatif`
  MODIFY `id_nilai_alternatif` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=306;

--
-- AUTO_INCREMENT untuk tabel `skala_penilaian`
--
ALTER TABLE `skala_penilaian`
  MODIFY `id_skala` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=87;

--
-- AUTO_INCREMENT untuk tabel `tipe_kriteria`
--
ALTER TABLE `tipe_kriteria`
  MODIFY `id_tipe` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `kriteria`
--
ALTER TABLE `kriteria`
  ADD CONSTRAINT `id_tipe` FOREIGN KEY (`id_tipe`) REFERENCES `tipe_kriteria` (`id_tipe`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Ketidakleluasaan untuk tabel `nilai_alternatif`
--
ALTER TABLE `nilai_alternatif`
  ADD CONSTRAINT `nilai_alternatif_ibfk_1` FOREIGN KEY (`id_alternatif`) REFERENCES `alternatif` (`id_alternatif`),
  ADD CONSTRAINT `nilai_alternatif_ibfk_2` FOREIGN KEY (`id_kriteria`) REFERENCES `kriteria` (`id_kriteria`),
  ADD CONSTRAINT `nilai_alternatif_ibfk_3` FOREIGN KEY (`id_skala`) REFERENCES `skala_penilaian` (`id_skala`);

--
-- Ketidakleluasaan untuk tabel `skala_penilaian`
--
ALTER TABLE `skala_penilaian`
  ADD CONSTRAINT `kriteria_fk` FOREIGN KEY (`id_kriteria`) REFERENCES `kriteria` (`id_kriteria`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

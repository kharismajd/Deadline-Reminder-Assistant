-- phpMyAdmin SQL Dump
-- version 5.1.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 28 Apr 2021 pada 05.05
-- Versi server: 10.4.18-MariaDB
-- Versi PHP: 8.0.3

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `tubes-stima-chatbot`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `new_task_keywords`
--

CREATE TABLE `new_task_keywords` (
  `id` int(8) NOT NULL,
  `type` varchar(20) DEFAULT NULL,
  `keyword` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `new_task_keywords`
--

INSERT INTO `new_task_keywords` (`id`, `type`, `keyword`) VALUES
(1, 'Task', 'Tubes'),
(2, 'Task', 'Tucil'),
(3, 'Task', 'Tugas'),
(4, 'Event', 'Kuis'),
(5, 'Event', 'Praktikum'),
(6, 'Event', 'Ujian');

-- --------------------------------------------------------

--
-- Struktur dari tabel `questions`
--

CREATE TABLE `questions` (
  `id` int(11) NOT NULL,
  `question` varchar(80) NOT NULL,
  `reply` longtext NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `questions`
--

INSERT INTO `questions` (`id`, `question`, `reply`) VALUES
(1, 'Siapa yang menciptakanmu?', 'Leonardus Brandon Luwianto<br>\r\nMade Kharisma Jagaddhita<br>\r\nFarrell Abieza Zidan'),
(2, 'Halo', 'Hai');

-- --------------------------------------------------------

--
-- Struktur dari tabel `tasks`
--

CREATE TABLE `tasks` (
  `id` int(11) NOT NULL,
  `course_id` varchar(10) NOT NULL,
  `type` varchar(20) NOT NULL,
  `deadline` date NOT NULL,
  `topic` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `tasks`
--

INSERT INTO `tasks` (`id`, `course_id`, `type`, `deadline`, `topic`) VALUES
(12, 'if2121', 'Tugas', '2021-05-15', ' tentang os'),
(16, 'MA2230', 'Kuis', '2021-04-29', ' Tentang pencocokan string');

-- --------------------------------------------------------

--
-- Struktur dari tabel `task_date_changed_keywords`
--

CREATE TABLE `task_date_changed_keywords` (
  `id` int(11) NOT NULL,
  `keyword` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `task_date_changed_keywords`
--

INSERT INTO `task_date_changed_keywords` (`id`, `keyword`) VALUES
(1, 'Diundur'),
(2, 'Mundur'),
(3, 'Ditunda'),
(5, 'Maju');

-- --------------------------------------------------------

--
-- Struktur dari tabel `task_done_keywords`
--

CREATE TABLE `task_done_keywords` (
  `id` int(11) NOT NULL,
  `keyword` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `task_done_keywords`
--

INSERT INTO `task_done_keywords` (`id`, `keyword`) VALUES
(1, 'Done'),
(2, 'Selesai'),
(3, 'Berhasil');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `new_task_keywords`
--
ALTER TABLE `new_task_keywords`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `questions`
--
ALTER TABLE `questions`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `tasks`
--
ALTER TABLE `tasks`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `task_date_changed_keywords`
--
ALTER TABLE `task_date_changed_keywords`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `task_done_keywords`
--
ALTER TABLE `task_done_keywords`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `new_task_keywords`
--
ALTER TABLE `new_task_keywords`
  MODIFY `id` int(8) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT untuk tabel `questions`
--
ALTER TABLE `questions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `tasks`
--
ALTER TABLE `tasks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT untuk tabel `task_date_changed_keywords`
--
ALTER TABLE `task_date_changed_keywords`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT untuk tabel `task_done_keywords`
--
ALTER TABLE `task_done_keywords`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

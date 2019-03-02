-- phpMyAdmin SQL Dump
-- version 4.8.3
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 22, 2018 at 06:26 PM
-- Server version: 10.1.35-MariaDB
-- PHP Version: 7.2.9

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `mini_instagram`
--

-- --------------------------------------------------------

--
-- Table structure for table `pictures`
--

CREATE TABLE `pictures` (
  `id` int(11) NOT NULL,
  `author_id` int(11) NOT NULL,
  `number_of_likes` int(11) NOT NULL,
  `users_liked` varchar(255) NOT NULL,
  `name` varchar(50) NOT NULL,
  `img_src` varchar(255) NOT NULL,
  `posted_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `pictures`
--

INSERT INTO `pictures` (`id`, `author_id`, `number_of_likes`, `users_liked`, `name`, `img_src`, `posted_at`) VALUES
(6, 15, 2, '<|>NekoPoy><|>Marko', 'MynewImage', '../public/assets/images/705b64e32be80e4.jpg', '2018-08-04 01:20:11'),
(7, 17, 0, '', 'slikaz', '../public/assets/images/785b64e35d1bcec.jpg', '2018-08-04 01:21:01'),
(8, 17, 2, '<|>milan2<|>Marko', 'Hello', '../public/assets/images/115b64e36e584e4.jpg', '2018-08-04 01:21:18'),
(9, 18, 1, '<|>Marko', 'Some tt', '../public/assets/images/85c0fe907b8208.jpg', '2018-12-11 17:42:47');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `picture_num` int(100) NOT NULL DEFAULT '0',
  `auth_key` varchar(255) NOT NULL,
  `registration_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `picture_num`, `auth_key`, `registration_date`) VALUES
(11, 'majki', 'zzz@gmail.com', '$2y$10$/1P96Sp/wGQupBAad8z//eAOaBjutKWDPLoDLO7uHtDL5QdpukLYm', 0, '53f68544846c72365afffa880c0f0587', '2018-08-01 05:15:49'),
(12, 'ZocaZZ', 'zora.medic@gmail.com', '$2y$10$z7uYxfYmOG7C/9/ESvm5Z.7bUg20jScNdJTJe8qArEN64d/r1o0aS', 0, 'ba7b3d050746f79efba85d3baf27841d', '2018-08-01 15:17:08'),
(13, 'NekoTamo', 'marko.medic@gmail.com', '$2y$10$LtIccpNvdRKqt7ZTvT7sVu9rw5fJn/oJBkkSkK2ACOwAyY0GE2E9O', 0, 'd456a0ede762032e3c87bb8c675dc88c', '2018-08-02 00:23:24'),
(14, 'milan', 'marko.medic19@gmail.com', '$2y$10$lduuaT5TOyb4J3Sin5aALO/48JZU4Aro6YqM9j2kkww2NlcVL/P42', 0, '3d5a0aabc6392ac040b4a0574210f6ed', '2018-08-02 14:45:17'),
(15, 'NekoPoy>', 'marko.medi1@gmail.com', '$2y$10$ZQ3nXbqxAjnk6Aa7xj1VVuzjyqoPvTEb1nuVVoCmKkLdvYOWupzfq', 1, 'bf2b796fe363c7bb369b27e71edd71b0', '2018-08-03 18:19:35'),
(16, 'danijela', 'danijelamedic22@gmail.com', '$2y$10$r4pz7U8.KNk7JS1OSmbT/O0DHA8FpsfTlHG5jrtj1NX7jKpBXMJWa', 0, '45b894a62e6247118f3d76ef1357b433', '2018-08-03 23:28:45'),
(17, 'milan2', 'milanmedic@gmail.com', '$2y$10$a2Fd8jxf6zHh0D6cE.UIFO6a/IdnRaVQiZW5cLXzxdoalyYQEMJ/e', 2, '4d98204ca40169848bc654b8ad0b7e39', '2018-08-03 23:32:53'),
(18, 'Marko', 'marko.medic59@gmail.com', '$2y$10$2s906YJ3jkN.Ez/pj/xdgerW1H6UsKe6dbwY3knS96BOO2k2UyWwO', 1, '3bae210773a566d20c63745223f54c76', '2018-12-11 17:37:56');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `pictures`
--
ALTER TABLE `pictures`
  ADD PRIMARY KEY (`id`),
  ADD KEY `author_id` (`author_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `pictures`
--
ALTER TABLE `pictures`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `pictures`
--
ALTER TABLE `pictures`
  ADD CONSTRAINT `fk_author_id` FOREIGN KEY (`author_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

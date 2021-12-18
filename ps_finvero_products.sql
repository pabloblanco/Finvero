-- phpMyAdmin SQL Dump
-- version 5.0.2
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1:3306
-- Tiempo de generación: 02-11-2021 a las 05:36:11
-- Versión del servidor: 5.7.31
-- Versión de PHP: 7.3.21

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `prestashop`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ps_finvero_products`
--

DROP TABLE IF EXISTS `ps_finvero_products`;
CREATE TABLE IF NOT EXISTS `ps_finvero_products` (
  `id_finvero_product` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_product` int(10) UNSIGNED NOT NULL,
  `is_finvero_product` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_finvero_product`)
) ENGINE=InnoDB AUTO_INCREMENT=35 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Volcado de datos para la tabla `ps_finvero_products`
--

INSERT INTO `ps_finvero_products` (`id_finvero_product`, `id_product`, `is_finvero_product`) VALUES
(1, 1, 1),
(2, 2, 1),
(3, 3, 0),
(4, 4, 0),
(5, 5, 1),
(6, 6, 1),
(7, 7, 0),
(8, 8, 0),
(9, 9, 0),
(10, 10, 1),
(11, 11, 0),
(12, 12, 0),
(13, 13, 0),
(14, 14, 1),
(15, 15, 0),
(16, 16, 0),
(17, 17, 0),
(18, 18, 1),
(19, 19, 1);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

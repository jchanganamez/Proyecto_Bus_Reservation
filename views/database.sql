-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 02-12-2024 a las 17:15:53
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `database`
--

DELIMITER $$
--
-- Procedimientos
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `exportar_y_limpiar` ()   BEGIN
    DECLARE total_reportes INT;
    DECLARE file_name VARCHAR(255);
    
    -- Verificar cuántos registros hay en la tabla reportes
    SELECT COUNT(*) INTO total_reportes FROM reportes;

    -- Si hay 20 o más registros, proceder a exportar y limpiar
    IF total_reportes >= 10 THEN
        -- Crear un nombre de archivo dinámico basado en la fecha y hora
        SET file_name = CONCAT('C:/xampp/htdocs/project/Reportes/reportes_', DATE_FORMAT(NOW(), '%Y%m%d_%H%i%s'), '.txt');

        -- Exportar los datos a un archivo
        SET @query = CONCAT('SELECT * FROM reportes INTO OUTFILE ''', file_name, ''' 
                             FIELDS TERMINATED BY '','' 
                             ENCLOSED BY ''"'' 
                             LINES TERMINATED BY ''\n'';');
        
        PREPARE stmt FROM @query;
        EXECUTE stmt;
        DEALLOCATE PREPARE stmt;

        -- Limpiar la tabla
        TRUNCATE TABLE reportes;  -- Esto eliminará todos los registros de la tabla
    END IF;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `asientos`
--

CREATE TABLE `asientos` (
  `id` int(11) NOT NULL,
  `bus_id` int(11) NOT NULL,
  `numero_asiento` int(11) NOT NULL,
  `categoria` enum('vip','estandar','economico') NOT NULL,
  `estado` enum('disponible','reservado','ocupado') DEFAULT 'disponible',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `asientos`
--

INSERT INTO `asientos` (`id`, `bus_id`, `numero_asiento`, `categoria`, `estado`, `created_at`) VALUES
(1, 1, 1, 'vip', 'disponible', '2024-11-22 05:18:00'),
(2, 1, 2, 'vip', 'disponible', '2024-11-22 05:18:00'),
(3, 1, 3, 'vip', 'disponible', '2024-11-22 05:18:00'),
(4, 1, 4, 'vip', 'disponible', '2024-11-22 05:18:00'),
(5, 1, 5, 'vip', 'ocupado', '2024-11-22 05:18:00'),
(6, 1, 6, 'vip', 'ocupado', '2024-11-22 05:18:00'),
(7, 1, 7, 'vip', 'disponible', '2024-11-22 05:18:00'),
(8, 1, 8, 'vip', 'disponible', '2024-11-22 05:18:00'),
(9, 1, 9, 'vip', 'disponible', '2024-11-22 05:18:00'),
(10, 1, 10, 'vip', 'disponible', '2024-11-22 05:18:00'),
(11, 1, 11, 'vip', 'disponible', '2024-11-22 05:18:00'),
(12, 1, 12, 'vip', 'disponible', '2024-11-22 05:18:00'),
(13, 1, 13, 'vip', 'disponible', '2024-11-22 05:18:00'),
(14, 1, 14, 'vip', 'disponible', '2024-11-22 05:18:00'),
(16, 1, 16, 'vip', 'disponible', '2024-11-22 05:18:00'),
(17, 1, 17, 'vip', 'disponible', '2024-11-22 05:18:00'),
(18, 1, 18, 'vip', 'disponible', '2024-11-22 05:18:00'),
(19, 1, 19, 'vip', 'disponible', '2024-11-22 05:18:00'),
(20, 1, 20, 'vip', 'disponible', '2024-11-22 05:18:00'),
(21, 1, 21, 'estandar', 'disponible', '2024-11-22 05:18:00'),
(22, 1, 22, 'estandar', 'disponible', '2024-11-22 05:18:00'),
(23, 1, 23, 'estandar', 'disponible', '2024-11-22 05:18:00'),
(24, 1, 24, 'estandar', 'disponible', '2024-11-22 05:18:00'),
(25, 1, 25, 'estandar', 'disponible', '2024-11-22 05:18:00'),
(26, 1, 26, 'estandar', 'disponible', '2024-11-22 05:18:00'),
(27, 1, 27, 'estandar', 'disponible', '2024-11-22 05:18:00'),
(28, 1, 28, 'estandar', 'disponible', '2024-11-22 05:18:00'),
(29, 1, 29, 'estandar', 'disponible', '2024-11-22 05:18:00'),
(30, 1, 30, 'estandar', 'disponible', '2024-11-22 05:18:00'),
(31, 1, 31, 'economico', 'disponible', '2024-11-22 05:18:00'),
(32, 1, 32, 'economico', 'disponible', '2024-11-22 05:18:00'),
(33, 1, 33, 'economico', 'disponible', '2024-11-22 05:18:00'),
(34, 1, 34, 'economico', 'disponible', '2024-11-22 05:18:00'),
(35, 1, 35, 'economico', 'disponible', '2024-11-22 05:18:00'),
(36, 1, 36, 'economico', 'disponible', '2024-11-22 05:18:00'),
(37, 1, 37, 'economico', 'disponible', '2024-11-22 05:18:00'),
(38, 1, 38, 'economico', 'disponible', '2024-11-22 05:18:00'),
(39, 1, 39, 'economico', 'disponible', '2024-11-22 05:18:00'),
(40, 1, 40, 'economico', 'disponible', '2024-11-22 05:18:00'),
(41, 2, 1, 'vip', 'disponible', '2024-11-22 05:18:01'),
(42, 2, 2, 'vip', 'disponible', '2024-11-22 05:18:01'),
(43, 2, 3, 'vip', 'disponible', '2024-11-22 05:18:01'),
(44, 2, 4, 'vip', 'disponible', '2024-11-22 05:18:01'),
(45, 2, 5, 'vip', 'disponible', '2024-11-22 05:18:01'),
(46, 2, 6, 'vip', 'disponible', '2024-11-22 05:18:01'),
(47, 2, 7, 'vip', 'disponible', '2024-11-22 05:18:01'),
(48, 2, 8, 'vip', 'ocupado', '2024-11-22 05:18:01'),
(49, 2, 9, 'vip', 'disponible', '2024-11-22 05:18:01'),
(50, 2, 10, 'vip', 'disponible', '2024-11-22 05:18:01'),
(51, 2, 11, 'estandar', 'disponible', '2024-11-22 05:18:01'),
(52, 2, 12, 'estandar', 'disponible', '2024-11-22 05:18:01'),
(53, 2, 13, 'estandar', 'disponible', '2024-11-22 05:18:01'),
(54, 2, 14, 'estandar', 'disponible', '2024-11-22 05:18:01'),
(55, 2, 15, 'estandar', 'disponible', '2024-11-22 05:18:01'),
(56, 2, 16, 'estandar', 'disponible', '2024-11-22 05:18:01'),
(57, 2, 17, 'estandar', 'disponible', '2024-11-22 05:18:01'),
(58, 2, 18, 'estandar', 'disponible', '2024-11-22 05:18:01'),
(59, 2, 19, 'estandar', 'disponible', '2024-11-22 05:18:01'),
(60, 2, 20, 'estandar', 'disponible', '2024-11-22 05:18:01'),
(61, 2, 21, 'economico', 'disponible', '2024-11-22 05:18:01'),
(62, 2, 22, 'economico', 'disponible', '2024-11-22 05:18:01'),
(63, 2, 23, 'economico', 'disponible', '2024-11-22 05:18:01'),
(64, 2, 24, 'economico', 'disponible', '2024-11-22 05:18:01'),
(65, 2, 25, 'economico', 'disponible', '2024-11-22 05:18:01'),
(66, 2, 26, 'economico', 'disponible', '2024-11-22 05:18:01'),
(67, 2, 27, 'economico', 'disponible', '2024-11-22 05:18:01'),
(68, 2, 28, 'economico', 'disponible', '2024-11-22 05:18:01'),
(69, 2, 29, 'economico', 'disponible', '2024-11-22 05:18:01'),
(70, 2, 30, 'economico', 'disponible', '2024-11-22 05:18:01'),
(71, 2, 31, 'economico', 'disponible', '2024-11-22 05:18:01'),
(72, 2, 32, 'economico', 'disponible', '2024-11-22 05:18:01'),
(73, 2, 33, 'economico', 'disponible', '2024-11-22 05:18:01'),
(74, 2, 34, 'economico', 'disponible', '2024-11-22 05:18:01'),
(75, 2, 35, 'economico', 'disponible', '2024-11-22 05:18:01'),
(76, 2, 36, 'economico', 'disponible', '2024-11-22 05:18:01'),
(77, 2, 37, 'economico', 'disponible', '2024-11-22 05:18:01'),
(78, 2, 38, 'economico', 'disponible', '2024-11-22 05:18:01'),
(79, 2, 39, 'economico', 'disponible', '2024-11-22 05:18:01'),
(80, 2, 40, 'economico', 'disponible', '2024-11-22 05:18:01'),
(81, 2, 41, 'economico', 'disponible', '2024-11-22 05:18:01'),
(82, 2, 42, 'economico', 'disponible', '2024-11-22 05:18:01'),
(83, 2, 43, 'economico', 'disponible', '2024-11-22 05:18:01'),
(84, 2, 44, 'economico', 'disponible', '2024-11-22 05:18:01'),
(85, 2, 45, 'economico', 'disponible', '2024-11-22 05:18:01'),
(86, 2, 46, 'economico', 'disponible', '2024-11-22 05:18:01'),
(87, 2, 47, 'economico', 'disponible', '2024-11-22 05:18:01'),
(88, 2, 48, 'economico', 'disponible', '2024-11-22 05:18:01'),
(89, 2, 49, 'economico', 'disponible', '2024-11-22 05:18:01'),
(90, 2, 50, 'economico', 'disponible', '2024-11-22 05:18:01'),
(91, 3, 1, 'vip', 'ocupado', '2024-11-22 05:18:01'),
(92, 3, 2, 'vip', 'ocupado', '2024-11-22 05:18:01'),
(93, 3, 3, 'vip', 'disponible', '2024-11-22 05:18:01'),
(94, 3, 4, 'vip', 'disponible', '2024-11-22 05:18:01'),
(95, 3, 5, 'vip', 'disponible', '2024-11-22 05:18:01'),
(96, 3, 6, 'vip', 'disponible', '2024-11-22 05:18:01'),
(97, 3, 7, 'vip', 'disponible', '2024-11-22 05:18:01'),
(98, 3, 8, 'vip', 'disponible', '2024-11-22 05:18:01'),
(99, 3, 9, 'vip', 'disponible', '2024-11-22 05:18:01'),
(100, 3, 10, 'estandar', 'disponible', '2024-11-22 05:18:01'),
(101, 3, 11, 'estandar', 'disponible', '2024-11-22 05:18:01'),
(102, 3, 12, 'estandar', 'disponible', '2024-11-22 05:18:01'),
(103, 3, 13, 'estandar', 'disponible', '2024-11-22 05:18:01'),
(104, 3, 14, 'estandar', 'disponible', '2024-11-22 05:18:01'),
(105, 3, 15, 'estandar', 'disponible', '2024-11-22 05:18:01'),
(106, 3, 16, 'estandar', 'disponible', '2024-11-22 05:18:01'),
(107, 3, 17, 'estandar', 'disponible', '2024-11-22 05:18:01'),
(108, 3, 18, 'estandar', 'disponible', '2024-11-22 05:18:01'),
(109, 3, 19, 'estandar', 'disponible', '2024-11-22 05:18:01'),
(110, 3, 20, 'estandar', 'disponible', '2024-11-22 05:18:01'),
(111, 3, 21, 'economico', 'disponible', '2024-11-22 05:18:01'),
(112, 3, 22, 'economico', 'disponible', '2024-11-22 05:18:01'),
(113, 3, 23, 'economico', 'disponible', '2024-11-22 05:18:01'),
(114, 3, 24, 'economico', 'disponible', '2024-11-22 05:18:01'),
(115, 3, 25, 'economico', 'disponible', '2024-11-22 05:18:01'),
(116, 3, 26, 'economico', 'disponible', '2024-11-22 05:18:01'),
(117, 3, 27, 'economico', 'disponible', '2024-11-22 05:18:01'),
(118, 3, 28, 'economico', 'disponible', '2024-11-22 05:18:01'),
(119, 3, 29, 'economico', 'disponible', '2024-11-22 05:18:01'),
(120, 3, 30, 'economico', 'disponible', '2024-11-22 05:18:01'),
(121, 15, 1, 'vip', 'disponible', '2024-12-01 15:54:57'),
(122, 15, 2, 'vip', 'disponible', '2024-12-01 15:54:57'),
(123, 15, 3, 'vip', 'disponible', '2024-12-01 15:54:57'),
(124, 15, 4, 'vip', 'disponible', '2024-12-01 15:54:57'),
(125, 15, 5, 'vip', 'disponible', '2024-12-01 15:54:57'),
(126, 15, 6, 'vip', 'disponible', '2024-12-01 15:54:57'),
(127, 15, 7, 'vip', 'disponible', '2024-12-01 15:54:57'),
(128, 15, 8, 'vip', 'disponible', '2024-12-01 15:54:57'),
(129, 15, 9, 'vip', 'disponible', '2024-12-01 15:54:57'),
(130, 15, 10, 'vip', 'disponible', '2024-12-01 15:54:57'),
(131, 15, 11, 'vip', 'disponible', '2024-12-01 15:54:57'),
(132, 15, 12, 'vip', 'disponible', '2024-12-01 15:54:57'),
(133, 15, 13, 'vip', 'disponible', '2024-12-01 15:54:57'),
(134, 15, 14, 'vip', 'disponible', '2024-12-01 15:54:57'),
(135, 15, 15, 'vip', 'disponible', '2024-12-01 15:54:57'),
(136, 15, 16, 'vip', 'disponible', '2024-12-01 15:54:57'),
(137, 15, 17, 'vip', 'disponible', '2024-12-01 15:54:57'),
(138, 15, 18, 'vip', 'disponible', '2024-12-01 15:54:57'),
(139, 15, 19, 'vip', 'disponible', '2024-12-01 15:54:57'),
(140, 15, 20, 'vip', 'disponible', '2024-12-01 15:54:57'),
(141, 15, 21, 'estandar', 'disponible', '2024-12-01 15:54:57'),
(142, 15, 22, 'estandar', 'disponible', '2024-12-01 15:54:57'),
(143, 15, 23, 'estandar', 'disponible', '2024-12-01 15:54:57'),
(144, 15, 24, 'estandar', 'disponible', '2024-12-01 15:54:57'),
(145, 15, 25, 'estandar', 'disponible', '2024-12-01 15:54:57'),
(146, 15, 26, 'estandar', 'disponible', '2024-12-01 15:54:57'),
(147, 15, 27, 'estandar', 'disponible', '2024-12-01 15:54:57'),
(148, 15, 28, 'estandar', 'disponible', '2024-12-01 15:54:57'),
(149, 15, 29, 'estandar', 'disponible', '2024-12-01 15:54:57'),
(150, 15, 30, 'estandar', 'disponible', '2024-12-01 15:54:57'),
(151, 15, 31, 'economico', 'disponible', '2024-12-01 15:54:57'),
(152, 15, 32, 'economico', 'disponible', '2024-12-01 15:54:57'),
(153, 15, 33, 'economico', 'disponible', '2024-12-01 15:54:57'),
(154, 15, 34, 'economico', 'disponible', '2024-12-01 15:54:57'),
(155, 15, 35, 'economico', 'disponible', '2024-12-01 15:54:57'),
(196, 17, 1, 'vip', 'disponible', '2024-12-02 14:34:41'),
(197, 17, 2, 'vip', 'disponible', '2024-12-02 14:34:41'),
(198, 17, 3, 'vip', 'disponible', '2024-12-02 14:34:41'),
(199, 17, 4, 'vip', 'disponible', '2024-12-02 14:34:41'),
(200, 17, 5, 'vip', 'disponible', '2024-12-02 14:34:41'),
(201, 17, 6, 'vip', 'disponible', '2024-12-02 14:34:41'),
(202, 17, 7, 'vip', 'disponible', '2024-12-02 14:34:41'),
(203, 17, 8, 'vip', 'disponible', '2024-12-02 14:34:41'),
(204, 17, 9, 'vip', 'disponible', '2024-12-02 14:34:41'),
(205, 17, 10, 'vip', 'disponible', '2024-12-02 14:34:41'),
(206, 17, 11, 'vip', 'disponible', '2024-12-02 14:34:41'),
(207, 17, 12, 'vip', 'disponible', '2024-12-02 14:34:41'),
(208, 17, 13, 'vip', 'disponible', '2024-12-02 14:34:41'),
(209, 17, 14, 'vip', 'disponible', '2024-12-02 14:34:41'),
(210, 17, 15, 'vip', 'disponible', '2024-12-02 14:34:41'),
(211, 17, 16, 'vip', 'disponible', '2024-12-02 14:34:41'),
(212, 17, 17, 'vip', 'disponible', '2024-12-02 14:34:41'),
(213, 17, 18, 'vip', 'disponible', '2024-12-02 14:34:41'),
(214, 17, 19, 'vip', 'disponible', '2024-12-02 14:34:41'),
(215, 17, 20, 'vip', 'disponible', '2024-12-02 14:34:41'),
(216, 17, 21, 'estandar', 'disponible', '2024-12-02 14:34:41'),
(217, 17, 22, 'estandar', 'disponible', '2024-12-02 14:34:41'),
(218, 17, 23, 'estandar', 'disponible', '2024-12-02 14:34:41'),
(219, 17, 24, 'estandar', 'disponible', '2024-12-02 14:34:41'),
(220, 17, 25, 'estandar', 'disponible', '2024-12-02 14:34:41'),
(221, 17, 26, 'estandar', 'disponible', '2024-12-02 14:34:41'),
(222, 17, 27, 'estandar', 'disponible', '2024-12-02 14:34:41'),
(223, 17, 28, 'estandar', 'disponible', '2024-12-02 14:34:41'),
(224, 17, 29, 'estandar', 'disponible', '2024-12-02 14:34:41'),
(225, 17, 30, 'estandar', 'disponible', '2024-12-02 14:34:41');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `buses`
--

CREATE TABLE `buses` (
  `id` int(11) NOT NULL,
  `numero` varchar(20) NOT NULL,
  `capacidad` int(11) NOT NULL,
  `modelo` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `conductor_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `buses`
--

INSERT INTO `buses` (`id`, `numero`, `capacidad`, `modelo`, `created_at`, `conductor_id`) VALUES
(1, 'B001', 40, 'Mercedes-Benz Sprinter', '2024-11-22 04:59:13', 1),
(2, 'B002', 50, 'Volvo 9700', '2024-11-22 04:59:13', 2),
(3, 'B003', 30, 'Scania Touring', '2024-11-22 04:59:13', 3),
(15, 'B007', 35, 'GT-2003', '2024-12-01 15:54:57', 1),
(17, 'B006', 30, 'Ferrari', '2024-12-02 14:34:41', 2);

--
-- Disparadores `buses`
--
DELIMITER $$
CREATE TRIGGER `after_delete_buses` AFTER DELETE ON `buses` FOR EACH ROW BEGIN
    INSERT INTO reportes (accion, tabla_afectada, descripcion)
    VALUES 
    (
        'DELETE', 
        'buses', 
        CONCAT('Se eliminó el bus con ID ', OLD.id, ', modelo: ', OLD.modelo, ', matrícula: ', OLD.numero)
    );
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `after_insert_buses` AFTER INSERT ON `buses` FOR EACH ROW BEGIN
    INSERT INTO reportes (accion, tabla_afectada, descripcion)
    VALUES 
    (
        'INSERT', 
        'buses', 
        CONCAT('Se creó un nuevo bus con ID ', NEW.id, ', modelo: ', NEW.modelo, ', matrícula: ', NEW.numero)
    );
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `after_update_buses` AFTER UPDATE ON `buses` FOR EACH ROW BEGIN
    INSERT INTO reportes (accion, tabla_afectada, descripcion)
    VALUES 
    (
        'UPDATE', 
        'buses', 
        CONCAT(
            'Se actualizó el bus con ID ', OLD.id, 
            '. Modelo antes: ', OLD.modelo, ', ahora: ', NEW.modelo, 
            '. Matrícula antes: ', OLD.numero, ', ahora: ', NEW.numero
        )
    );
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `conductores`
--

CREATE TABLE `conductores` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `licencia` varchar(20) NOT NULL,
  `telefono` varchar(15) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `conductores`
--

INSERT INTO `conductores` (`id`, `nombre`, `licencia`, `telefono`, `created_at`) VALUES
(1, 'Juan Pérez', 'LIC-001', '555-1234', '2024-11-23 02:31:42'),
(2, 'María Gómez', 'LIC-002', '555-5678', '2024-11-23 02:31:42'),
(3, 'Carlos Apaza', 'LIC-003', '555-8765', '2024-11-23 02:31:42');

--
-- Disparadores `conductores`
--
DELIMITER $$
CREATE TRIGGER `after_delete_conductores` AFTER DELETE ON `conductores` FOR EACH ROW BEGIN
    INSERT INTO reportes (accion, tabla_afectada, descripcion)
    VALUES 
    (
        'DELETE', 
        'conductores', 
        CONCAT('Se eliminó el conductor con ID ', OLD.id, ', nombre: ', OLD.nombre, ', licencia: ', OLD.licencia)
    );
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `after_insert_conductores` AFTER INSERT ON `conductores` FOR EACH ROW BEGIN
    INSERT INTO reportes (accion, tabla_afectada, descripcion)
    VALUES 
    (
        'INSERT', 
        'conductores', 
        CONCAT('Se creó un nuevo conductor con ID ', NEW.id, ', nombre: ', NEW.nombre, ', licencia: ', NEW.licencia)
    );
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `after_update_conductores` AFTER UPDATE ON `conductores` FOR EACH ROW BEGIN
    INSERT INTO reportes (accion, tabla_afectada, descripcion)
    VALUES 
    (
        'UPDATE', 
        'conductores', 
        CONCAT(
            'Se actualizó el conductor con ID ', OLD.id, 
            '. Nombre antes: ', OLD.nombre, ', ahora: ', NEW.nombre, 
            '. Licencia antes: ', OLD.licencia, ', ahora: ', NEW.licencia
        )
    );
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `destinos`
--

CREATE TABLE `destinos` (
  `id` int(11) NOT NULL,
  `nombre_ciudad` varchar(100) NOT NULL,
  `costo` decimal(10,2) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `destinos`
--

INSERT INTO `destinos` (`id`, `nombre_ciudad`, `costo`, `descripcion`, `created_at`) VALUES
(1, 'Lima', 120.00, 'La capital de Perú, famosa por su gastronomía y cultura vibrante.', '2024-11-19 07:48:43'),
(2, 'Cusco', 150.00, 'Antigua capital del Imperio Inca, hogar de Machu Picchu y rica en historia.', '2024-11-19 07:48:43'),
(3, 'Arequipa', 90.00, 'Conocida como la Ciudad Blanca, famosa por su arquitectura colonial y el Cañón del Colca.', '2024-11-19 07:48:43'),
(4, 'Trujillo', 80.00, 'Ciudad de la Eterna Primavera, famosa por sus festivales y la cultura Moche.', '2024-11-19 07:48:43'),
(5, 'Iquitos', 200.00, 'La ciudad más grande del mundo inaccesible por carretera, ubicada en la selva amazónica.', '2024-11-19 07:48:43'),
(6, 'Puno', 130.00, 'Famosa por el Lago Titicaca, el lago navegable más alto del mundo.', '2024-11-19 07:48:00'),
(7, 'Tarapoto', 110.00, 'Conocida por su clima cálido y su acceso a la selva peruana.', '2024-11-19 07:48:43'),
(8, 'Chiclayo', 95.00, 'Famosa por su rica historia precolombina y su deliciosa gastronomía.', '2024-11-19 07:48:43'),
(9, 'Huancayo', 85.00, 'Conocida por su cultura andina y su hermoso paisaje montañoso.', '2024-11-19 07:48:43'),
(10, 'Cajamarca', 140.00, 'Famosa por su historia inca y su hermoso entorno natural.', '2024-11-19 07:48:43');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalle_viaje`
--

CREATE TABLE `detalle_viaje` (
  `id` int(11) NOT NULL,
  `viaje_id` int(11) DEFAULT NULL,
  `usuario_id` int(11) DEFAULT NULL,
  `asiento` int(11) NOT NULL,
  `categoria` enum('vip','estandar','economico') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `detalle_viaje`
--

INSERT INTO `detalle_viaje` (`id`, `viaje_id`, `usuario_id`, `asiento`, `categoria`, `created_at`) VALUES
(217, 134, 5, 5, 'vip', '2024-12-01 20:09:37'),
(218, 134, 5, 6, 'vip', '2024-12-01 20:09:37'),
(241, 147, 5, 1, 'vip', '2024-12-01 22:36:09'),
(242, 147, 5, 2, 'vip', '2024-12-01 22:36:09'),
(247, 150, 6, 1, 'vip', '2024-12-02 15:02:56'),
(248, 150, 6, 2, 'vip', '2024-12-02 15:02:56'),
(254, 153, 29, 2, 'vip', '2024-12-02 15:54:06'),
(255, 153, 29, 1, 'vip', '2024-12-02 15:54:06');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pagos`
--

CREATE TABLE `pagos` (
  `id` int(11) NOT NULL,
  `trip_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `payment_method` varchar(50) NOT NULL,
  `card_number` varchar(20) DEFAULT NULL,
  `expiry_date` varchar(7) DEFAULT NULL,
  `cvv` varchar(4) DEFAULT NULL,
  `yape_number` varchar(20) DEFAULT NULL,
  `plin_number` varchar(20) DEFAULT NULL,
  `paypal_email` varchar(100) DEFAULT NULL,
  `total` decimal(10,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `pagos`
--

INSERT INTO `pagos` (`id`, `trip_id`, `user_id`, `payment_method`, `card_number`, `expiry_date`, `cvv`, `yape_number`, `plin_number`, `paypal_email`, `total`, `created_at`) VALUES
(101, 134, 5, '', '', '', '', '', '', '', 150.00, '2024-12-01 20:09:37'),
(113, 147, 5, 'yape', '', '', '', '123321123', '', '', 180.00, '2024-12-01 22:36:09'),
(116, 150, 6, 'yape', '', '', '', '789654123', '', '', 180.00, '2024-12-02 15:02:56'),
(119, 153, 29, 'plin', '', '', '', '', '123321', '', 115.00, '2024-12-02 15:54:06');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `reportes`
--

CREATE TABLE `reportes` (
  `id_reportes` int(11) NOT NULL,
  `accion` varchar(50) NOT NULL,
  `tabla_afectada` varchar(50) NOT NULL,
  `descripcion` text NOT NULL,
  `fecha` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `reportes`
--

INSERT INTO `reportes` (`id_reportes`, `accion`, `tabla_afectada`, `descripcion`, `fecha`) VALUES
(1, 'INSERT', 'usuarios', 'Se creó un nuevo usuario con ID 29, nombre: Angel, email: angel@gmail.com', '2024-12-02 15:53:38'),
(2, 'INSERT', 'viajes', 'Se creó un nuevo viaje con ID 153, origen: Arequipa, destino: Huancayo', '2024-12-02 15:54:06');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `es_admin` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `telefono` varchar(15) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `nombre`, `email`, `password`, `es_admin`, `created_at`, `telefono`) VALUES
(5, 'Jhosep', 'jhosepchangana@gmail.com', '$2y$10$oi9SyR3rwQF5cvZtnXS0..eQBE7Mn6unJsWBKMxCcvZaUpfD0q9b.', 1, '2024-11-25 05:52:04', '951287236'),
(6, 'Max', 'max@gmail.com', '$2y$10$1OXdV1U2jjBBWYj9x3K9n.3ZIWNl3j3oQFLQfOr4cVpBH8uYrNbg.', 0, '2024-11-25 06:22:39', '987456621'),
(29, 'Angel', 'angel@gmail.com', '$2y$10$6Z/HGXAHONTPaLNpiWZqiO4CelL85FaONiNq/UXR0zFplW0/Y5EOy', 0, '2024-12-02 15:53:38', '');

--
-- Disparadores `usuarios`
--
DELIMITER $$
CREATE TRIGGER `after_delete_usuario` AFTER DELETE ON `usuarios` FOR EACH ROW BEGIN
    INSERT INTO reportes (accion, tabla_afectada, descripcion)
    VALUES 
    (
        'DELETE', 
        'usuarios', 
        CONCAT('Se eliminó el usuario con ID ', OLD.id, ', nombre: ', OLD.nombre, ', email: ', OLD.email)
    );
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `after_insert_usuario` AFTER INSERT ON `usuarios` FOR EACH ROW BEGIN
    INSERT INTO reportes (accion, tabla_afectada, descripcion)
    VALUES 
    (
        'INSERT', 
        'usuarios', 
        CONCAT('Se creó un nuevo usuario con ID ', NEW.id, ', nombre: ', NEW.nombre, ', email: ', NEW.email)
    );
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `after_update_usuario` AFTER UPDATE ON `usuarios` FOR EACH ROW BEGIN
    INSERT INTO reportes (accion, tabla_afectada, descripcion)
    VALUES 
    (
        'UPDATE', 
        'usuarios', 
        CONCAT(
            'Se actualizó el usuario con ID ', OLD.id, 
            '. Nombre antes: ', OLD.nombre, ', ahora: ', NEW.nombre, 
            '. Email antes: ', OLD.email, ', ahora: ', NEW.email
        )
    );
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `viajes`
--

CREATE TABLE `viajes` (
  `id` int(11) NOT NULL,
  `origen` varchar(100) NOT NULL,
  `destino` varchar(100) NOT NULL,
  `fecha_salida` datetime NOT NULL,
  `fecha_llegada` datetime NOT NULL,
  `bus_id` int(11) DEFAULT NULL,
  `conductor_id` int(11) DEFAULT NULL,
  `precio` decimal(10,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `user_id` int(11) DEFAULT NULL,
  `estado` varchar(255) NOT NULL DEFAULT 'En espera'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `viajes`
--

INSERT INTO `viajes` (`id`, `origen`, `destino`, `fecha_salida`, `fecha_llegada`, `bus_id`, `conductor_id`, `precio`, `created_at`, `user_id`, `estado`) VALUES
(134, 'Trujillo', 'Lima', '2024-12-02 15:09:00', '2024-12-13 15:09:00', 1, 1, 150.00, '2024-12-01 20:09:37', 5, 'Cancelado'),
(135, 'Cusco', 'Iquitos', '2024-12-18 15:28:00', '2025-01-03 15:28:00', 2, 2, 230.00, '2024-12-01 20:28:44', 5, 'Confirmado'),
(147, 'Arequipa', 'Cusco', '2024-12-06 00:00:00', '2024-12-28 00:00:00', 3, 3, 180.00, '2024-12-01 22:36:09', 5, 'Confirmado'),
(150, 'Arequipa', 'Cusco', '2024-12-20 00:00:00', '2024-12-27 00:00:00', 17, 2, 180.00, '2024-12-02 15:02:56', 6, 'En Espera'),
(153, 'Arequipa', 'Huancayo', '2024-12-07 00:00:00', '2024-12-18 00:00:00', 3, 3, 115.00, '2024-12-02 15:54:06', 29, 'En Espera');

--
-- Disparadores `viajes`
--
DELIMITER $$
CREATE TRIGGER `after_delete_viaje` AFTER DELETE ON `viajes` FOR EACH ROW BEGIN
    INSERT INTO reportes (accion, tabla_afectada, descripcion)
    VALUES 
    (
        'DELETE', 
        'viajes', 
        CONCAT('Se eliminó el viaje con ID ', OLD.id, ', origen: ', OLD.origen, ', destino: ', OLD.destino)
    );
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `after_insert_viaje` AFTER INSERT ON `viajes` FOR EACH ROW BEGIN
    INSERT INTO reportes (accion, tabla_afectada, descripcion)
    VALUES 
    (
        'INSERT', 
        'viajes', 
        CONCAT('Se creó un nuevo viaje con ID ', NEW.id, ', origen: ', NEW.origen, ', destino: ', NEW.destino)
    );
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `after_update_viaje` AFTER UPDATE ON `viajes` FOR EACH ROW BEGIN
    -- Verifica si el estado ha cambiado
    IF OLD.estado <> NEW.estado THEN
        INSERT INTO reportes (accion, tabla_afectada, descripcion)
        VALUES 
        (
            'UPDATE', 
            'viajes', 
            CONCAT(
                'Se actualizó el estado del viaje con ID ', OLD.id, 
                '. Estado antes: ', OLD.estado, ', ahora: ', NEW.estado
            )
        );
    END IF;
END
$$
DELIMITER ;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `asientos`
--
ALTER TABLE `asientos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `bus_id` (`bus_id`);

--
-- Indices de la tabla `buses`
--
ALTER TABLE `buses`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `numero` (`numero`);

--
-- Indices de la tabla `conductores`
--
ALTER TABLE `conductores`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `licencia` (`licencia`);

--
-- Indices de la tabla `destinos`
--
ALTER TABLE `destinos`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `detalle_viaje`
--
ALTER TABLE `detalle_viaje`
  ADD PRIMARY KEY (`id`),
  ADD KEY `viaje_id` (`viaje_id`),
  ADD KEY `usuario_id` (`usuario_id`);

--
-- Indices de la tabla `pagos`
--
ALTER TABLE `pagos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_user` (`user_id`),
  ADD KEY `fk_trip` (`trip_id`);

--
-- Indices de la tabla `reportes`
--
ALTER TABLE `reportes`
  ADD PRIMARY KEY (`id_reportes`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indices de la tabla `viajes`
--
ALTER TABLE `viajes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `bus_id` (`bus_id`),
  ADD KEY `conductor_id` (`conductor_id`),
  ADD KEY `fk_user_id` (`user_id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `asientos`
--
ALTER TABLE `asientos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=226;

--
-- AUTO_INCREMENT de la tabla `buses`
--
ALTER TABLE `buses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT de la tabla `conductores`
--
ALTER TABLE `conductores`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT de la tabla `destinos`
--
ALTER TABLE `destinos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `detalle_viaje`
--
ALTER TABLE `detalle_viaje`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=256;

--
-- AUTO_INCREMENT de la tabla `pagos`
--
ALTER TABLE `pagos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=120;

--
-- AUTO_INCREMENT de la tabla `reportes`
--
ALTER TABLE `reportes`
  MODIFY `id_reportes` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT de la tabla `viajes`
--
ALTER TABLE `viajes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=154;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `asientos`
--
ALTER TABLE `asientos`
  ADD CONSTRAINT `asientos_ibfk_1` FOREIGN KEY (`bus_id`) REFERENCES `buses` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `detalle_viaje`
--
ALTER TABLE `detalle_viaje`
  ADD CONSTRAINT `detalle_viaje_ibfk_1` FOREIGN KEY (`viaje_id`) REFERENCES `viajes` (`id`),
  ADD CONSTRAINT `detalle_viaje_ibfk_2` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`);

--
-- Filtros para la tabla `pagos`
--
ALTER TABLE `pagos`
  ADD CONSTRAINT `fk_trip` FOREIGN KEY (`trip_id`) REFERENCES `viajes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_user` FOREIGN KEY (`user_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `viajes`
--
ALTER TABLE `viajes`
  ADD CONSTRAINT `fk_user_id` FOREIGN KEY (`user_id`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `viajes_ibfk_1` FOREIGN KEY (`bus_id`) REFERENCES `buses` (`id`),
  ADD CONSTRAINT `viajes_ibfk_2` FOREIGN KEY (`conductor_id`) REFERENCES `conductores` (`id`);

DELIMITER $$
--
-- Eventos
--
CREATE DEFINER=`root`@`localhost` EVENT `exportar_y_limpiar_event` ON SCHEDULE EVERY 3 MINUTE STARTS '2024-12-01 18:16:53' ON COMPLETION NOT PRESERVE ENABLE DO BEGIN
    CALL exportar_y_limpiar();
END$$

DELIMITER ;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

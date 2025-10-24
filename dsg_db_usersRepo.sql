-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 24-10-2025 a las 04:14:33
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `dsg_db_users`
--
CREATE DATABASE IF NOT EXISTS `dsg_db_users` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `dsg_db_users`;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `bitacora`
--

CREATE TABLE `bitacora` (
  `id` int(11) NOT NULL,
  `idUsuario` int(11) DEFAULT NULL,
  `registro` varchar(512) NOT NULL,
  `ruta` text NOT NULL,
  `fecha` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `bitacora`
--



-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `modulo`
--

CREATE TABLE `modulo` (
  `id` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

--
-- Volcado de datos para la tabla `modulo`
--

INSERT INTO `modulo` (`id`, `nombre`) VALUES
(1, 'bitacora'),
(2, 'roles'),
(3, 'usuarios'),
(4, 'areas'),
(5, 'asistencias'),
(6, 'categorias'),
(7, 'departamentos'),
(9, 'medidas'),
(10, 'notificaciones'),
(11, 'tareas'),
(12, 'trabajadores'),
(13, 'reporteasistencias'),
(14, 'articulos'),
(15, 'ajustes'),
(16, 'movimientos'),
(17, 'notasentrega'),
(18, 'estadisticasasistencias'),
(19, 'cargos'),
(20, 'turnos');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `notificacion`
--

CREATE TABLE `notificacion` (
  `id` int(11) NOT NULL,
  `idUsuario` int(11) NOT NULL,
  `titulo` varchar(50) NOT NULL,
  `descripcion` text NOT NULL,
  `fechaCreacion` datetime NOT NULL DEFAULT current_timestamp(),
  `estado` tinyint(4) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `permiso`
--

CREATE TABLE `permiso` (
  `id` int(11) NOT NULL,
  `idRol` int(11) NOT NULL,
  `idModulo` int(11) NOT NULL,
  `consultar` tinyint(1) NOT NULL DEFAULT 0,
  `registrar` tinyint(1) NOT NULL DEFAULT 0,
  `actualizar` tinyint(1) NOT NULL DEFAULT 0,
  `eliminar` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `permiso`
--

INSERT INTO `permiso` (`id`, `idRol`, `idModulo`, `consultar`, `registrar`, `actualizar`, `eliminar`) VALUES
(1, 1, 1, 1, 1, 1, 1),
(2, 1, 2, 1, 1, 1, 1),
(3, 1, 3, 1, 1, 1, 1),
(22, 1, 4, 1, 1, 1, 1),
(23, 1, 5, 1, 1, 1, 1),
(24, 1, 6, 1, 1, 1, 1),
(25, 1, 7, 1, 1, 1, 1),
(27, 1, 9, 1, 1, 1, 1),
(28, 1, 10, 1, 1, 1, 1),
(29, 1, 11, 1, 1, 1, 1),
(30, 1, 12, 1, 1, 1, 1),
(31, 8, 1, 0, 0, 0, 0),
(32, 8, 2, 0, 0, 0, 0),
(33, 8, 3, 0, 0, 0, 0),
(34, 8, 4, 0, 0, 0, 0),
(35, 8, 5, 0, 0, 0, 0),
(36, 8, 6, 0, 0, 0, 0),
(37, 8, 7, 0, 0, 0, 0),
(39, 8, 9, 0, 0, 0, 0),
(40, 8, 10, 0, 0, 0, 0),
(41, 8, 11, 0, 0, 0, 0),
(42, 8, 12, 0, 0, 0, 0),
(43, 8, 13, 0, 0, 0, 0),
(44, 1, 13, 1, 1, 1, 1),
(45, 1, 14, 1, 1, 1, 1),
(46, 1, 15, 1, 1, 1, 1),
(47, 1, 16, 1, 1, 1, 1),
(48, 1, 17, 1, 1, 1, 1),
(49, 1, 18, 1, 1, 1, 1),
(50, 9, 1, 0, 0, 0, 0),
(51, 9, 2, 0, 0, 0, 0),
(52, 9, 3, 0, 0, 0, 0),
(53, 9, 4, 0, 0, 0, 0),
(54, 9, 5, 1, 1, 1, 1),
(55, 9, 6, 0, 0, 0, 0),
(56, 9, 7, 0, 0, 0, 0),
(57, 9, 9, 0, 0, 0, 0),
(58, 9, 10, 0, 0, 0, 0),
(59, 9, 11, 1, 1, 1, 1),
(60, 9, 12, 1, 1, 1, 1),
(61, 9, 13, 1, 1, 1, 1),
(62, 9, 14, 1, 1, 1, 1),
(63, 9, 15, 1, 1, 1, 1),
(64, 9, 16, 1, 1, 1, 1),
(65, 9, 17, 0, 0, 0, 0),
(66, 9, 18, 1, 1, 1, 1),
(67, 8, 14, 0, 0, 0, 0),
(68, 8, 15, 0, 0, 0, 0),
(69, 8, 16, 0, 0, 0, 0),
(70, 8, 17, 0, 0, 0, 0),
(71, 8, 18, 0, 0, 0, 0),
(89, 11, 1, 0, 0, 0, 0),
(90, 11, 2, 0, 0, 0, 0),
(91, 11, 3, 1, 1, 1, 1),
(92, 11, 4, 1, 1, 1, 1),
(93, 11, 5, 0, 0, 0, 0),
(94, 11, 6, 0, 0, 0, 0),
(95, 11, 7, 0, 0, 0, 0),
(96, 11, 9, 0, 0, 0, 0),
(97, 11, 10, 0, 0, 0, 0),
(98, 11, 11, 0, 0, 0, 0),
(99, 11, 12, 0, 0, 0, 0),
(100, 11, 13, 0, 0, 0, 0),
(101, 11, 14, 0, 0, 0, 0),
(102, 11, 15, 0, 0, 0, 0),
(103, 11, 16, 0, 0, 0, 0),
(104, 11, 17, 0, 0, 0, 0),
(105, 11, 18, 0, 0, 0, 0),
(106, 12, 1, 0, 0, 0, 0),
(107, 12, 2, 0, 0, 0, 0),
(108, 12, 3, 0, 0, 0, 0),
(109, 12, 4, 0, 0, 0, 0),
(110, 12, 5, 0, 0, 0, 0),
(111, 12, 6, 0, 0, 0, 0),
(112, 12, 7, 0, 0, 0, 0),
(113, 12, 9, 0, 0, 0, 0),
(114, 12, 10, 0, 0, 0, 0),
(115, 12, 11, 0, 0, 0, 0),
(116, 12, 12, 0, 0, 0, 0),
(117, 12, 13, 0, 0, 0, 0),
(118, 12, 14, 0, 0, 0, 0),
(119, 12, 15, 0, 0, 0, 0),
(120, 12, 16, 0, 0, 0, 0),
(121, 12, 17, 0, 0, 0, 0),
(122, 12, 18, 0, 0, 0, 0),
(123, 13, 1, 1, 1, 1, 1),
(124, 13, 2, 1, 1, 1, 1),
(125, 13, 3, 1, 1, 1, 1),
(126, 13, 4, 1, 1, 1, 1),
(127, 13, 5, 1, 1, 1, 1),
(128, 13, 6, 1, 1, 1, 1),
(129, 13, 7, 1, 1, 1, 1),
(130, 13, 9, 1, 1, 1, 1),
(131, 13, 10, 1, 1, 1, 1),
(132, 13, 11, 1, 1, 1, 1),
(133, 13, 12, 1, 1, 1, 1),
(134, 13, 13, 1, 1, 1, 1),
(135, 13, 14, 1, 1, 1, 1),
(136, 13, 15, 1, 1, 1, 1),
(137, 13, 16, 1, 1, 1, 1),
(138, 13, 17, 1, 1, 1, 1),
(139, 13, 18, 1, 1, 1, 1),
(140, 1, 19, 1, 1, 1, 1),
(141, 1, 20, 1, 1, 1, 1),
(142, 14, 1, 0, 0, 0, 0),
(143, 14, 2, 0, 0, 0, 0),
(144, 14, 3, 0, 0, 0, 0),
(145, 14, 4, 0, 0, 0, 0),
(146, 14, 5, 1, 0, 0, 0),
(147, 14, 6, 0, 0, 0, 0),
(148, 14, 7, 0, 0, 0, 0),
(149, 14, 9, 0, 0, 0, 0),
(150, 14, 10, 0, 0, 0, 0),
(151, 14, 11, 0, 0, 0, 0),
(152, 14, 12, 0, 0, 0, 0),
(153, 14, 13, 1, 0, 0, 0),
(154, 14, 14, 0, 0, 0, 0),
(155, 14, 15, 0, 0, 0, 0),
(156, 14, 16, 0, 0, 0, 0),
(157, 14, 17, 0, 0, 0, 0),
(158, 14, 18, 1, 0, 0, 0),
(159, 14, 19, 0, 0, 0, 0),
(160, 14, 20, 0, 0, 0, 0),
(161, 15, 1, 1, 1, 1, 0),
(162, 15, 2, 1, 1, 1, 0),
(163, 15, 3, 1, 1, 1, 0),
(164, 15, 4, 1, 1, 1, 0),
(165, 15, 5, 1, 1, 1, 0),
(166, 15, 6, 1, 1, 1, 0),
(167, 15, 7, 1, 1, 1, 0),
(168, 15, 9, 1, 1, 1, 0),
(169, 15, 10, 1, 1, 1, 0),
(170, 15, 11, 1, 1, 1, 0),
(171, 15, 12, 1, 1, 1, 0),
(172, 15, 13, 1, 1, 1, 0),
(173, 15, 14, 1, 1, 1, 0),
(174, 15, 15, 1, 1, 1, 0),
(175, 15, 16, 1, 1, 1, 0),
(176, 15, 17, 1, 1, 1, 0),
(177, 15, 18, 1, 1, 1, 0),
(178, 15, 19, 1, 1, 1, 0),
(179, 15, 20, 1, 1, 1, 0),
(180, 16, 1, 1, 1, 0, 1),
(181, 16, 2, 1, 1, 0, 1),
(182, 16, 3, 1, 1, 0, 1),
(183, 16, 4, 1, 1, 0, 1),
(184, 16, 5, 1, 1, 0, 1),
(185, 16, 6, 1, 1, 0, 1),
(186, 16, 7, 1, 1, 0, 1),
(187, 16, 9, 1, 1, 0, 1),
(188, 16, 10, 1, 1, 0, 1),
(189, 16, 11, 1, 1, 0, 1),
(190, 16, 12, 1, 1, 0, 1),
(191, 16, 13, 1, 1, 0, 1),
(192, 16, 14, 1, 1, 0, 1),
(193, 16, 15, 1, 1, 0, 1),
(194, 16, 16, 1, 1, 0, 1),
(195, 16, 17, 1, 1, 0, 1),
(196, 16, 18, 1, 1, 0, 1),
(197, 16, 19, 1, 1, 0, 1),
(198, 16, 20, 1, 1, 0, 1),
(199, 17, 1, 1, 1, 0, 0),
(200, 17, 2, 1, 1, 0, 0),
(201, 17, 3, 1, 1, 0, 0),
(202, 17, 4, 1, 1, 0, 0),
(203, 17, 5, 1, 1, 0, 0),
(204, 17, 6, 1, 1, 0, 0),
(205, 17, 7, 1, 1, 0, 0),
(206, 17, 9, 1, 1, 0, 0),
(207, 17, 10, 1, 1, 0, 0),
(208, 17, 11, 1, 1, 0, 0),
(209, 17, 12, 1, 1, 0, 0),
(210, 17, 13, 1, 1, 0, 0),
(211, 17, 14, 1, 1, 0, 0),
(212, 17, 15, 1, 1, 0, 0),
(213, 17, 16, 1, 1, 0, 0),
(214, 17, 17, 1, 1, 0, 0),
(215, 17, 18, 1, 1, 0, 0),
(216, 17, 19, 1, 1, 0, 0),
(217, 17, 20, 1, 1, 0, 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `rol`
--

CREATE TABLE `rol` (
  `id` int(11) NOT NULL,
  `nombre` varchar(20) NOT NULL,
  `descripcion` varchar(200) DEFAULT '',
  `estado` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

--
-- Volcado de datos para la tabla `rol`
--

INSERT INTO `rol` (`id`, `nombre`, `descripcion`, `estado`) VALUES
(1, 'Superusuario', 'Control total del sistema', 1),
(8, 'administrador', '', 1),
(9, 'Usuario', 'Usuario comun', 1),
(11, 'xavier', 'perro', 0),
(12, 'xavier', '', 0),
(13, 'xavier', '', 0),
(14, 'prueba de Permisos', '', 1),
(15, 'sin eliminar', '', 1),
(16, 'sin actualizar', '', 1),
(17, 'sin eliminar actuali', '', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuario`
--

CREATE TABLE `usuario` (
  `id` int(11) NOT NULL,
  `cedula` varchar(9) DEFAULT NULL,
  `idRol` int(11) NOT NULL,
  `correo` varchar(200) NOT NULL,
  `clave` text NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `apellido` varchar(50) NOT NULL,
  `estado` tinyint(1) NOT NULL DEFAULT 1,
  `token` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

--
-- Volcado de datos para la tabla `usuario`
--

INSERT INTO `usuario` (`id`, `cedula`, `idRol`, `correo`, `clave`, `nombre`, `apellido`, `estado`, `token`) VALUES
(1, '00000001', 1, 'admin@dsg.com', '$2y$10$UxGlmjwVYDiPeJ/mz/aeru9taZtf7MKIQCUmxm6ffp3duvxcjsVty', 'Admin', 'Dsg', 1, NULL),
(27, '0777777', 1, 'algo@esto.chauX', '$2y$10$.bb2n9fjxRS0qFj6QcDVMuaEVYqJELF7L/1tjTsVOEHPMCcyg2uYe', 'probando nuevo formulario', 'queso                  ', 0, NULL),
(30, '2720544', 8, 'admin@dsg.com1', '$2y$10$4tQ1VbPeoWWgBIu9ys148uOn/MBrTKHBTW5d785I/nU5qyydSu5g2', 'xavier', 'sanchez', 1, NULL);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `bitacora`
--
ALTER TABLE `bitacora`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idUsuario` (`idUsuario`);

--
-- Indices de la tabla `modulo`
--
ALTER TABLE `modulo`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `notificacion`
--
ALTER TABLE `notificacion`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idUsuario` (`idUsuario`);

--
-- Indices de la tabla `permiso`
--
ALTER TABLE `permiso`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `idRol_2` (`idRol`,`idModulo`),
  ADD KEY `idRol` (`idRol`),
  ADD KEY `idModulo` (`idModulo`);

--
-- Indices de la tabla `rol`
--
ALTER TABLE `rol`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `usuario`
--
ALTER TABLE `usuario`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `correo` (`correo`),
  ADD UNIQUE KEY `cedula` (`cedula`),
  ADD KEY `idRol` (`idRol`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `bitacora`
--
ALTER TABLE `bitacora`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=920;

--
-- AUTO_INCREMENT de la tabla `modulo`
--
ALTER TABLE `modulo`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT de la tabla `notificacion`
--
ALTER TABLE `notificacion`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `permiso`
--
ALTER TABLE `permiso`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=693;

--
-- AUTO_INCREMENT de la tabla `rol`
--
ALTER TABLE `rol`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;

--
-- AUTO_INCREMENT de la tabla `usuario`
--
ALTER TABLE `usuario`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=139;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `bitacora`
--
ALTER TABLE `bitacora`
  ADD CONSTRAINT `ibfk_bitacora_1` FOREIGN KEY (`idUsuario`) REFERENCES `usuario` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Filtros para la tabla `notificacion`
--
ALTER TABLE `notificacion`
  ADD CONSTRAINT `notificacion_ibfk_1` FOREIGN KEY (`idUsuario`) REFERENCES `usuario` (`id`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `permiso`
--
ALTER TABLE `permiso`
  ADD CONSTRAINT `permiso_ibfk_1` FOREIGN KEY (`idRol`) REFERENCES `rol` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `permiso_ibfk_2` FOREIGN KEY (`idModulo`) REFERENCES `modulo` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `usuario`
--
ALTER TABLE `usuario`
  ADD CONSTRAINT `usuario_ibfk_1` FOREIGN KEY (`idRol`) REFERENCES `rol` (`id`) ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

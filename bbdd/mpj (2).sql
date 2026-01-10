-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 10-01-2026 a las 07:10:21
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
-- Base de datos: `mpj`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `carrito`
--

CREATE TABLE `carrito` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `creado_en` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `carrito_producto`
--

CREATE TABLE `carrito_producto` (
  `id` int(11) NOT NULL,
  `carrito_id` int(11) DEFAULT NULL,
  `producto_id` int(11) DEFAULT NULL,
  `cantidad` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `categoria`
--

CREATE TABLE `categoria` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `descripcion` longtext DEFAULT NULL,
  `slug` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `categoria`
--

INSERT INTO `categoria` (`id`, `nombre`, `descripcion`, `slug`) VALUES
(1, 'Camisetas', 'Camisetas MPJ de manga corta y larga', 'camisetas'),
(2, 'Sudaderas', 'Sudaderas MPJ con y sin capucha', 'sudaderas'),
(3, 'Pantalones', 'Pantalones MPJ cómodos y versátiles', 'pantalones'),
(4, 'Abrigos', 'Abrigos y chaquetas MPJ', 'abrigos'),
(5, 'Zapatillas', 'Zapatillas y calzado MPJ', 'zapatillas'),
(7, 'Complementos', 'complementos de joyeria y todo', 'complementos'),
(8, 'Accesorios', NULL, 'accesorios'),
(9, 'Sombreros', NULL, 'sombrero'),
(10, 'Invierno', NULL, 'invierno'),
(11, 'Verano', NULL, 'verano');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `comentario`
--

CREATE TABLE `comentario` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `producto_id` int(11) NOT NULL,
  `texto` longtext NOT NULL,
  `fecha` datetime NOT NULL,
  `valoracion` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `direccion`
--

CREATE TABLE `direccion` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `calle` varchar(100) NOT NULL,
  `ciudad` varchar(50) NOT NULL,
  `cp` varchar(10) NOT NULL,
  `provincia` varchar(50) NOT NULL,
  `pais` varchar(50) NOT NULL,
  `tipo` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `direccion`
--

INSERT INTO `direccion` (`id`, `usuario_id`, `calle`, `ciudad`, `cp`, `provincia`, `pais`, `tipo`) VALUES
(1, 17, 'calle sol', 'madrid', '28001', 'madrid', 'españa', 'envio'),
(2, 3, 'Ponzano', 'Madrid', '28010', 'Madrid', 'España', 'envio'),
(3, 18, 'Ramirez Tome, 42, 18A', 'Madrid', '28038', 'Madrid', 'Rumania', 'envio'),
(4, 23, 'Ponzano', 'Madrid', '28943', 'Madrid', 'España', 'envio'),
(5, 28, 'calle prueba 2', 'murcia', '34543', 'murcia', 'españa', 'envio'),
(6, 28, 'calle hostias', 'madrid', '289876', 'madrid', 'España', 'casa'),
(7, 29, 'calle peñazo 3 bj 2', 'Soria', '4444444444', 'Soria', '', 'envio'),
(8, 29, 'Calle hostias 23 bj 4', 'madrid ', '28034', 'Madrid', 'España', 'casa'),
(9, 30, 'calle soledad 1', 'madrid', '28009', 'madrid', 'España', ''),
(10, 30, 'calle castro', 'madrid', '28009', 'madrid', 'España', '');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `doctrine_migration_versions`
--

CREATE TABLE `doctrine_migration_versions` (
  `version` varchar(191) NOT NULL,
  `executed_at` datetime DEFAULT NULL,
  `execution_time` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `doctrine_migration_versions`
--

INSERT INTO `doctrine_migration_versions` (`version`, `executed_at`, `execution_time`) VALUES
('DoctrineMigrations\\Version20251111193201', NULL, NULL),
('DoctrineMigrations\\Version20260105131617', NULL, NULL),
('DoctrineMigrations\\Version20260108201236', '2026-01-08 21:19:19', 13),
('DoctrineMigrations\\Version20260108201547', '2026-01-08 21:20:52', 5);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `lineas_pedido`
--

CREATE TABLE `lineas_pedido` (
  `id` int(11) NOT NULL,
  `pedido_id` int(11) NOT NULL,
  `producto_id` int(11) NOT NULL,
  `talla` varchar(10) NOT NULL,
  `color` varchar(30) NOT NULL,
  `cantidad` int(11) NOT NULL,
  `precio_unitario` decimal(10,2) NOT NULL,
  `subtotal` decimal(10,2) NOT NULL,
  `imagen` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `lineas_pedido`
--

INSERT INTO `lineas_pedido` (`id`, `pedido_id`, `producto_id`, `talla`, `color`, `cantidad`, `precio_unitario`, `subtotal`, `imagen`) VALUES
(6, 12, 5, '41', 'Azul Marino', 2, 79.99, 159.98, 'zapatillas-azules.png'),
(7, 13, 5, '41', 'Azul Marino', 2, 79.99, 159.98, 'zapatillas-azules.png'),
(8, 14, 5, '44', 'Blanco', 1, 79.99, 79.99, 'zapatillas-blancas.png'),
(9, 15, 4, 'M', 'Verde', 1, 89.99, 89.99, 'abrigo-verde.png'),
(10, 16, 4, 'XL', 'Azul Marino', 1, 89.99, 89.99, 'abrigo-azul.png'),
(11, 17, 4, 'XL', 'Negro', 1, 89.99, 89.99, 'abrigo-negro.png'),
(12, 18, 4, 'M', 'Negro', 1, 89.99, 89.99, 'abrigo-negro.png'),
(13, 19, 3, 'XL', 'Gris', 1, 29.99, 29.99, 'pantalon-gris.png'),
(14, 20, 4, 'L', 'Azul Marino', 1, 89.99, 89.99, 'abrigo-azul.png'),
(15, 20, 3, 'M', 'Azul marino', 1, 29.99, 29.99, 'pantalon-azul-marino.png'),
(17, 22, 5, '42', 'Negro', 1, 79.99, 79.99, 'zapatillas-negras.png'),
(19, 24, 5, '42', 'Azul Marino', 1, 79.99, 79.99, 'zapatillas-azules.png'),
(21, 26, 11, 'Niño', 'Marron', 1, 35.98, 35.98, 'var_6961c7821031d.png'),
(22, 27, 10, 'Unica', 'Azul', 1, 16.99, 16.99, 'var_696145391b80d.png'),
(23, 28, 2, 'XL', 'Negro', 1, 39.99, 39.99, 'sudadera-negra.png'),
(24, 28, 5, '42', 'Negro', 2, 79.99, 159.98, 'zapatillas-negras.png'),
(26, 30, 13, 'M', 'Rosa', 1, 20.99, 20.99, 'var_6961cf9c782d9.jpg');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `messenger_messages`
--

CREATE TABLE `messenger_messages` (
  `id` bigint(20) NOT NULL,
  `body` longtext NOT NULL,
  `headers` longtext NOT NULL,
  `queue_name` varchar(190) NOT NULL,
  `created_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `available_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `delivered_at` datetime DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `metodo_pago`
--

CREATE TABLE `metodo_pago` (
  `id` int(11) NOT NULL,
  `tipo` varchar(50) NOT NULL,
  `proveedor` varchar(50) DEFAULT NULL,
  `datos` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pedidos`
--

CREATE TABLE `pedidos` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `fecha` datetime NOT NULL,
  `estado` varchar(30) NOT NULL,
  `total` decimal(10,2) NOT NULL,
  `direccion_id` int(11) DEFAULT NULL,
  `metodo_pago_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `pedidos`
--

INSERT INTO `pedidos` (`id`, `usuario_id`, `fecha`, `estado`, `total`, `direccion_id`, `metodo_pago_id`) VALUES
(12, 17, '2026-01-08 21:55:34', 'pendiente_pago', 159.98, 1, NULL),
(13, 17, '2026-01-08 22:01:29', 'pagado', 159.98, 1, NULL),
(14, 17, '2026-01-08 22:07:46', 'pagado', 79.99, 1, NULL),
(15, 17, '2026-01-08 22:09:07', 'pagado', 89.99, 1, NULL),
(16, 17, '2026-01-08 22:15:56', 'pagado', 89.99, 1, NULL),
(17, 3, '2026-01-08 22:20:03', 'pagado', 89.99, 2, NULL),
(18, 3, '2026-01-08 22:20:58', 'pendiente_pago', 89.99, 2, NULL),
(19, 3, '2026-01-08 22:37:05', 'pagado', 29.99, 2, NULL),
(20, 18, '2026-01-08 22:57:17', 'pagado', 119.98, 3, NULL),
(21, 23, '2026-01-08 23:27:11', 'pagado', 7.99, 4, NULL),
(22, 23, '2026-01-08 23:30:51', 'pagado', 79.99, 4, NULL),
(23, 23, '2026-01-08 23:37:19', 'pagado', 39.95, 4, NULL),
(24, 17, '2026-01-09 16:01:48', 'pendiente_pago', 79.99, 1, NULL),
(25, 28, '2026-01-09 16:47:05', 'pagado', 7.99, 5, NULL),
(26, 23, '2026-01-10 04:29:35', 'pagado', 35.98, 4, NULL),
(27, 23, '2026-01-10 04:31:07', 'pagado', 16.99, 4, NULL),
(28, 29, '2026-01-10 05:14:13', 'pagado', 199.97, 7, NULL),
(30, 30, '2026-01-10 07:08:31', 'pagado', 20.99, 9, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pedido_producto`
--

CREATE TABLE `pedido_producto` (
  `id` int(11) NOT NULL,
  `pedido_id` int(11) NOT NULL,
  `producto_id` int(11) NOT NULL,
  `cantidad` int(11) NOT NULL,
  `precio_unitario` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productos`
--

CREATE TABLE `productos` (
  `id` int(11) NOT NULL,
  `nombre` varchar(150) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `precio` decimal(10,2) NOT NULL,
  `imagen` varchar(255) DEFAULT NULL,
  `categoria_id` int(11) DEFAULT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `productos`
--

INSERT INTO `productos` (`id`, `nombre`, `descripcion`, `precio`, `imagen`, `categoria_id`, `activo`) VALUES
(1, 'Camiseta MPJ Básica', 'Camiseta de algodón con logo MPJ. Corte regular.', 19.99, 'camiseta-negra.png', 1, 1),
(2, 'Sudadera MPJ Essential', 'Sudadera con capucha, interior afelpado y logo MPJ.', 39.99, 'sudadera-azul-marino.png\r\n', 2, 1),
(3, 'Jogger MPJ Comfort', 'Pantalón jogger cómodo, cintura elástica y logo MPJ.', 29.99, 'pantalon-gris.png', 3, 1),
(4, 'Abrigo MPJ', 'Abrigo acolchado MPJ para invierno.', 89.99, 'abrigo-verde.png', 4, 1),
(5, 'Zapatillas MPJ', 'Zapatillas urbanas MPJ.', 79.99, 'zapatillas-negras.png', 5, 1),
(10, 'Collar', 'Collar de perlas', 16.99, 'prod_6961450e20481.png', 8, 1),
(11, 'Sombrerazo', 'Sombrero grande', 35.98, 'prod_6961c7581c418.jpg', 9, 1),
(12, 'Guantes Invierno', 'Guantes Invierno', 20.99, 'prod_6961cdd919ebd.jpg', 10, 1),
(13, 'Bikini', 'Bikini verano', 20.99, 'prod_6961cf779813e.jpg', 11, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `producto_variacion`
--

CREATE TABLE `producto_variacion` (
  `id` int(11) NOT NULL,
  `producto_id` int(11) NOT NULL,
  `talla` varchar(50) NOT NULL,
  `color` varchar(50) NOT NULL,
  `stock` int(11) NOT NULL,
  `imagen` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `producto_variacion`
--

INSERT INTO `producto_variacion` (`id`, `producto_id`, `talla`, `color`, `stock`, `imagen`) VALUES
(1, 1, 'S', 'Blanco', 19, 'camiseta-blanca.png'),
(2, 1, 'M', 'Blanco', 10, 'camiseta-blanca.png'),
(3, 1, 'L', 'Blanco', 0, 'camiseta-blanca.png'),
(4, 1, 'XL', 'Blanco', 6, 'camiseta-blanca.png'),
(5, 1, 'S', 'Negro', 8, 'camiseta-negra.png'),
(6, 1, 'M', 'Negro', 0, 'camiseta-negra.png'),
(7, 1, 'L', 'Negro', 7, 'camiseta-negra.png'),
(8, 1, 'XL', 'Negro', 4, 'camiseta-negra.png'),
(9, 1, 'S', 'Gris', 5, 'camiseta-gris.png'),
(10, 1, 'M', 'Gris', 9, 'camiseta-gris.png'),
(11, 1, 'L', 'Gris', 3, 'camiseta-gris.png'),
(12, 1, 'XL', 'Gris', 0, 'camiseta-gris.png'),
(13, 1, 'S', 'Azul marino', 10, 'camiseta-azul-marino.png'),
(14, 1, 'M', 'Azul marino', 7, 'camiseta-azul-marino.png'),
(15, 1, 'L', 'Azul marino', 0, 'camiseta-azul-marino.png'),
(16, 1, 'XL', 'Azul marino', 2, 'camiseta-azul-marino.png'),
(17, 2, 'S', 'Negro', 6, 'sudadera-negra.png'),
(18, 2, 'M', 'Negro', 4, 'sudadera-negra.png'),
(19, 2, 'L', 'Negro', 0, 'sudadera-negra.png'),
(20, 2, 'XL', 'Negro', 1, 'sudadera-negra.png'),
(21, 2, 'S', 'Blanco', 3, 'sudadera-blanca.png'),
(22, 2, 'M', 'Blanco', 0, 'sudadera-blanca.png'),
(23, 2, 'L', 'Blanco', 5, 'sudadera-blanca.png'),
(24, 2, 'XL', 'Blanco', 1, 'sudadera-blanca.png'),
(25, 2, 'S', 'Gris', 8, 'sudadera-gris.png'),
(26, 2, 'M', 'Gris', 7, 'sudadera-gris.png'),
(27, 2, 'L', 'Gris', 4, 'sudadera-gris.png'),
(28, 2, 'XL', 'Gris', 0, 'sudadera-gris.png'),
(29, 2, 'S', 'Azul marino', 2, 'sudadera-azul-marino.png'),
(30, 2, 'M', 'Azul marino', 5, 'sudadera-azul-marino.png'),
(31, 2, 'L', 'Azul marino', 0, 'sudadera-azul-marino.png'),
(32, 2, 'XL', 'Azul marino', 3, 'sudadera-azul-marino.png'),
(33, 3, 'S', 'Negro', 7, 'pantalon-negro.png'),
(34, 3, 'M', 'Negro', 0, 'pantalon-negro.png'),
(35, 3, 'L', 'Negro', 5, 'pantalon-negro.png'),
(36, 3, 'XL', 'Negro', 3, 'pantalon-negro.png'),
(37, 3, 'S', 'Gris', 0, 'pantalon-gris.png'),
(38, 3, 'M', 'Gris', 6, 'pantalon-gris.png'),
(39, 3, 'L', 'Gris', 4, 'pantalon-gris.png'),
(40, 3, 'XL', 'Gris', 2, 'pantalon-gris.png'),
(41, 3, 'S', 'Beige', 3, 'pantalon-beige.png'),
(42, 3, 'M', 'Beige', 4, 'pantalon-beige.png'),
(43, 3, 'L', 'Beige', 0, 'pantalon-beige.png'),
(44, 3, 'XL', 'Beige', 1, 'pantalon-beige.png'),
(45, 3, 'S', 'Azul marino', 5, 'pantalon-azul-marino.png'),
(46, 3, 'M', 'Azul marino', 3, 'pantalon-azul-marino.png'),
(47, 3, 'L', 'Azul marino', 2, 'pantalon-azul-marino.png'),
(48, 3, 'XL', 'Azul marino', 0, 'pantalon-azul-marino.png'),
(49, 4, 'M', 'Negro', 3, 'abrigo-negro.png'),
(50, 4, 'L', 'Beige', 2, 'abrigo-beige.png'),
(53, 5, '40', 'Blanco', 7, 'zapatillas-blancas.png'),
(54, 5, '41', 'Negro', 4, 'zapatillas-negras.png'),
(57, 4, 'S', 'Negro', 4, 'abrigo-negro.png'),
(59, 4, 'L', 'Negro', 0, 'abrigo-negro.png'),
(60, 4, 'XL', 'Negro', 1, 'abrigo-negro.png'),
(61, 4, 'S', 'Beige', 3, 'abrigo-beige.png'),
(62, 4, 'M', 'Beige', 0, 'abrigo-beige.png'),
(64, 4, 'XL', 'Beige', 1, 'abrigo-beige.png'),
(65, 4, 'S', 'Verde', 1, 'abrigo-verde.png'),
(66, 4, 'M', 'Verde', 2, 'abrigo-verde.png'),
(67, 4, 'L', 'Verde', 0, 'abrigo-verde.png'),
(68, 4, 'XL', 'Verde', 2, 'abrigo-verde.png'),
(69, 4, 'S', 'Azul Marino', 0, 'abrigo-azul.png'),
(70, 4, 'M', 'Azul Marino', 2, 'abrigo-azul.png'),
(71, 4, 'L', 'Azul Marino', 1, 'abrigo-azul.png'),
(72, 4, 'XL', 'Azul Marino', 3, 'abrigo-azul.png'),
(74, 5, '41', 'Blanco', 2, 'zapatillas-blancas.png'),
(75, 5, '42', 'Blanco', 0, 'zapatillas-blancas.png'),
(76, 5, '43', 'Blanco', 4, 'zapatillas-blancas.png'),
(77, 5, '44', 'Blanco', 1, 'zapatillas-blancas.png'),
(78, 5, '45', 'Blanco', 2, 'zapatillas-blancas.png'),
(79, 5, '40', 'Negro', 1, 'zapatillas-negras.png'),
(81, 5, '42', 'Negro', 1, 'zapatillas-negras.png'),
(82, 5, '43', 'Negro', 2, 'zapatillas-negras.png'),
(83, 5, '44', 'Negro', 1, 'zapatillas-negras.png'),
(84, 5, '45', 'Negro', 0, 'zapatillas-negras.png'),
(85, 5, '40', 'Gris', 2, 'zapatillas-grises.png'),
(86, 5, '41', 'Gris', 1, 'zapatillas-grises.png'),
(87, 5, '42', 'Gris', 0, 'zapatillas-grises.png'),
(88, 5, '43', 'Gris', 2, 'zapatillas-grises.png'),
(89, 5, '44', 'Gris', 3, 'zapatillas-grises.png'),
(90, 5, '45', 'Gris', 1, 'zapatillas-grises.png'),
(91, 5, '40', 'Azul Marino', 0, 'zapatillas-azules.png'),
(92, 5, '41', 'Azul Marino', 2, 'zapatillas-azules.png'),
(93, 5, '42', 'Azul Marino', 2, 'zapatillas-azules.png'),
(94, 5, '43', 'Azul Marino', 1, 'zapatillas-azules.png'),
(95, 5, '44', 'Azul Marino', 0, 'zapatillas-azules.png'),
(96, 5, '45', 'Azul Marino', 2, 'zapatillas-azules.png'),
(101, 10, 'Unica', 'Azul', 10, 'var_696145391b80d.png'),
(102, 11, 'Niño', 'Marron', 9, 'var_6961c7821031d.png'),
(104, 13, 'S', 'Beige', 2, 'var_6961cf88dca2a.jpg'),
(105, 13, 'M', 'Rosa', 0, 'var_6961cf9c782d9.jpg');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `reset_password_request`
--

CREATE TABLE `reset_password_request` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `selector` varchar(20) NOT NULL,
  `hashed_token` varchar(100) NOT NULL,
  `requested_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `expires_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `reset_password_request`
--

INSERT INTO `reset_password_request` (`id`, `user_id`, `selector`, `hashed_token`, `requested_at`, `expires_at`) VALUES
(1, 5, 'mzsls3XBmLzq4kT2GuO0', '0Cwh1XgCMBk6JjbT2YHcMXPOw8iN5yJWl/wJVrz8Kfk=', '2026-01-07 19:23:39', '2026-01-07 20:23:39'),
(2, 5, 'sxA8omYuMOwsV7pGSMKx', 'x8trVVIpEUIi2iElWS27XLdCJtwEB+zhG/304yVONz0=', '2026-01-07 20:36:26', '2026-01-07 21:36:26'),
(3, 4, 'TM5laRpeoGDHaIrzY0FE', 'mjYhaEZtuMPOiQVfysyMns1OaTqun1SWqIfY9p1YEfA=', '2026-01-07 20:38:29', '2026-01-07 21:38:29'),
(13, 3, '9VcFmNpdTc8Sf7MUl0SN', '36lrmhQO9XRuhXUo+DhRCHzMlSlQvtBdfltZTShG5vo=', '2026-01-07 21:34:20', '2026-01-07 22:34:20'),
(15, 4, 'RROUBAoOEUjqXFwzXfpy', '8ly+PkDYZhZbanSvc5i6DST1GdLscdZQhJjUssTvK+I=', '2026-01-07 21:40:53', '2026-01-07 22:40:53'),
(17, 6, 'OG1omPddQnA2VGuD39k5', 'mNz7ZAmK7FfordbNPS9jiguYBX4ETV7voWq9j3vnIJY=', '2026-01-07 22:00:27', '2026-01-07 23:00:27'),
(20, 3, 'NOP7O2qq2QosRY9LqHRK', 'vCuHsNy9lIHf7QjtQtIX2hRfjTwA2JMTRMkPIFL+mIw=', '2026-01-08 17:20:15', '2026-01-08 18:20:15');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tarjeta`
--

CREATE TABLE `tarjeta` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `numero` varchar(20) NOT NULL,
  `caducidad` date NOT NULL,
  `cvv` varchar(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `tarjeta`
--

INSERT INTO `tarjeta` (`id`, `user_id`, `numero`, `caducidad`, `cvv`) VALUES
(1, 23, '1212121212121212', '2027-11-01', '121'),
(3, 30, '2345432347786754', '2028-12-01', '344');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `nombre` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `telefono` varchar(11) NOT NULL,
  `roles` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`roles`))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `nombre`, `email`, `password`, `telefono`, `roles`) VALUES
(3, 'Mikel', 'mikel140805@gmail.com', '$2y$13$WZnnFdJPFidTc384g1MID.QqjrC7j/bkIstTtQCDTSKVek0ND7idG', '620081606', '[\"ROLE_USER\"]'),
(4, 'thor', 'thor@gmail.com', '$2y$13$L.GCScwbjIV.iSiCHwZHFuvbwbjZ4DGKASSlQbou8oNLqW7LMacbG', '647453842', '[\"ROLE_USER\"]'),
(5, 'olvidar contra', 'fijecod975@gavrom.com', '$2y$13$3qU3sh6XnJ4C0fOgUp0KX.uDsTMPnNCeOiv/yUiGWyekvF/uO3W96', '657845367', '[\"ROLE_USER\"]'),
(6, 'resetear prueba', 'hanepa1990@gavrom.com', '$2y$13$67aCP6C0KJZF/qS/oznLne6KXGb6z4BLpgM0VBHegJ4ZClqC1yD4O', '675845672', '[\"ROLE_USER\"]'),
(7, 'reseteo prueba ', 'lapad56964@gavrom.com', '$2y$13$pvAVkYhHzL9xciGUfGDzNu4X10cTMfX9xdaxwN81Qga9qXruIzGUi', '6754323456', '[\"ROLE_USER\"]'),
(8, 'susana', 'favijat741@emaxasp.com', '$2y$13$k7UpL1IRzuTQixy8UDZgi.BUWiBbTWnuX0I.1rlx7uwc7Vecgo1kK', '657487253', '[\"ROLE_USER\"]'),
(9, 'resetear prueba 2', 'reniteb683@gavrom.com', '$2y$13$E3c89Y95gA3cAku5sGt.yO9L5sWL3TzFsutWVn7vDsAGZoy38z4iK', '657654567', '[\"ROLE_USER\"]'),
(10, 'reseteo 3', 'xacamos949@emaxasp.com', '$2y$13$49/zRY1AFBv1t2l1rNusx.mPePhoSe1C8sC35ayPfDOc0q2hHgciO', '654321234', '[\"ROLE_USER\"]'),
(11, 'reseteo 4', 'tojic85793@gavrom.com', '$2y$13$tia8uwUXV9lz9Dz1nL7G9.XwixNpw48mL9andjV2UTXyFNmvuoOpO', '657890987', '[\"ROLE_USER\"]'),
(12, 'cliente final', 'tojihi5100@imfaya.com', '$2y$13$PgtMvfKxcyrrrcPWNINlt.08A3q5Np8lyjkkHGRvAb1xXVPSVC7.a', '654321566', '[\"ROLE_USER\"]'),
(13, 'cliente final f', 'cipetex289@gopicta.com', '$2y$13$09918h1honLFJimB1kiEQe6B1ItS7Yt4u.J1fCGMJ9J/9hClti9P.', '675456789', '[\"ROLE_USER\"]'),
(15, 'cliente final form', 'sakaxa1192@imfaya.com', '$2y$13$k8spvN.X4susdabbwYQVAet4F2NUlUTKjMDb8zB5fFOkrU9sX1fJa', '678457678', '[\"ROLE_USER\"]'),
(16, 'cliente final form 2', 'waviy38044@imfaya.com', '$2y$13$6UoPQnvtZDW/XDJh6SPEX.2.hJkW3JRD.Y22qLiG/hPJClafAzPqW', '657489567', '[\"ROLE_USER\"]'),
(17, 'registro prueba', 'viceca1811@imfaya.com', '$2y$13$3u2frUQ4BnHwSw/69EIMVOg7n1GOXEdy5WbH4Lifb7BtId/u7WBfq', '654734234', '[\"ROLE_USER\"]'),
(18, 'Jorge Dominguez', 'domi@gmail.com', '$2y$13$wEOZ.J5GVYl8bshetuXkw.AZNW5Z7dyopskPO/IlW5yaDrDN3sR/2', '666777666', '[\"ROLE_USER\"]'),
(20, 'Administrador', 'admin@mpj.com', '$2y$10$wH7lQH1wZ2m2pG0p0fZzOe4M4f9E9sN5uXz0n6Vf6x1rC3kzJm6Zy\r\n', '600000000', '[\"ROLE_ADMIN\"]'),
(23, 'Admin', 'admin@mpjWear.com', '$2y$13$5hi1iDF7t/8FE08B.DKcSuZ1keM4pmA.yhUYhz6KfN6xvjEXQ56Xy', '600010000', '[\"ROLE_ADMIN\"]'),
(24, 'clienta final', 'xorig59985@imfaya.com', '$2y$13$arxGUhOvfNuc3H1pKwz1QuKfqHN7uB7jtiJZayIpCoaEG4Jg3XEt2', '6123456752', '[\"ROLE_USER\"]'),
(25, 'cliente prueba registro final', 'hodib50118@imfaya.com', '$2y$13$NNFiywMSpisXbGPxjyMdZODyPtjeA2eEy1ZnIcn/5ft6kJQuzqIry', '675482345', '[\"ROLE_USER\"]'),
(28, 'cliente registro check', 'cipahew730@imfaya.com', '$2y$13$NivUJQ8PF8Mf3DNEP6qofuZpg4ocBQDkAdC3S29FRY832Eg5RQxXK', '675482349', '[\"ROLE_USER\"]'),
(29, 'Prueba final', 'cacale7825@imfaya.com', '$2y$13$dDWsoBnKCjMlyzfjUDlNCeqYuTstHanXG9HZRqsuu1GGbSYyg10lW', '654321345', '[\"ROLE_USER\"]'),
(30, 'registro final', 'woyopir555@gopicta.com', '$2y$13$QB0vshh7MOf69RhfCzGRbO4iOAOGdSPjA2Xpcv6By9pMFOkNw9H0u', '675432123', '[\"ROLE_USER\"]');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `carrito`
--
ALTER TABLE `carrito`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_77E6BED5DB38439E` (`usuario_id`);

--
-- Indices de la tabla `carrito_producto`
--
ALTER TABLE `carrito_producto`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_62C02DC2DE2CF6E7` (`carrito_id`),
  ADD KEY `IDX_62C02DC27645698E` (`producto_id`);

--
-- Indices de la tabla `categoria`
--
ALTER TABLE `categoria`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UNIQ_4E10122D989D9B62` (`slug`);

--
-- Indices de la tabla `comentario`
--
ALTER TABLE `comentario`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_comentario_producto` (`producto_id`),
  ADD KEY `IDX_4B91E702DB38439E` (`usuario_id`);

--
-- Indices de la tabla `direccion`
--
ALTER TABLE `direccion`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_direccion_usuario` (`usuario_id`);

--
-- Indices de la tabla `doctrine_migration_versions`
--
ALTER TABLE `doctrine_migration_versions`
  ADD PRIMARY KEY (`version`);

--
-- Indices de la tabla `lineas_pedido`
--
ALTER TABLE `lineas_pedido`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_linea_pedido_pedido` (`pedido_id`),
  ADD KEY `fk_linea_pedido_producto` (`producto_id`);

--
-- Indices de la tabla `messenger_messages`
--
ALTER TABLE `messenger_messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_75EA56E0FB7336F0` (`queue_name`),
  ADD KEY `IDX_75EA56E0E3BD61CE` (`available_at`),
  ADD KEY `IDX_75EA56E016BA31DB` (`delivered_at`);

--
-- Indices de la tabla `metodo_pago`
--
ALTER TABLE `metodo_pago`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `pedidos`
--
ALTER TABLE `pedidos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_pedidos_usuario` (`usuario_id`),
  ADD KEY `fk_pedidos_direccion` (`direccion_id`),
  ADD KEY `fk_pedidos_metodo_pago` (`metodo_pago_id`);

--
-- Indices de la tabla `pedido_producto`
--
ALTER TABLE `pedido_producto`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_pedido_producto_pedido` (`pedido_id`),
  ADD KEY `fk_pedido_producto_producto` (`producto_id`);

--
-- Indices de la tabla `productos`
--
ALTER TABLE `productos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_productos_categoria` (`categoria_id`);

--
-- Indices de la tabla `producto_variacion`
--
ALTER TABLE `producto_variacion`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_59C4E9E67645698E` (`producto_id`);

--
-- Indices de la tabla `reset_password_request`
--
ALTER TABLE `reset_password_request`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_7CE748AA76ED395` (`user_id`);

--
-- Indices de la tabla `tarjeta`
--
ALTER TABLE `tarjeta`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_TARJETA_USER` (`user_id`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `telefono` (`telefono`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `carrito`
--
ALTER TABLE `carrito`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `carrito_producto`
--
ALTER TABLE `carrito_producto`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `categoria`
--
ALTER TABLE `categoria`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT de la tabla `comentario`
--
ALTER TABLE `comentario`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `direccion`
--
ALTER TABLE `direccion`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `lineas_pedido`
--
ALTER TABLE `lineas_pedido`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT de la tabla `messenger_messages`
--
ALTER TABLE `messenger_messages`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `metodo_pago`
--
ALTER TABLE `metodo_pago`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `pedidos`
--
ALTER TABLE `pedidos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT de la tabla `pedido_producto`
--
ALTER TABLE `pedido_producto`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `productos`
--
ALTER TABLE `productos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT de la tabla `producto_variacion`
--
ALTER TABLE `producto_variacion`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=106;

--
-- AUTO_INCREMENT de la tabla `reset_password_request`
--
ALTER TABLE `reset_password_request`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT de la tabla `tarjeta`
--
ALTER TABLE `tarjeta`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `carrito`
--
ALTER TABLE `carrito`
  ADD CONSTRAINT `fk_carrito_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `carrito_producto`
--
ALTER TABLE `carrito_producto`
  ADD CONSTRAINT `FK_62C02DC27645698E` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`),
  ADD CONSTRAINT `FK_62C02DC2DE2CF6E7` FOREIGN KEY (`carrito_id`) REFERENCES `carrito` (`id`);

--
-- Filtros para la tabla `comentario`
--
ALTER TABLE `comentario`
  ADD CONSTRAINT `FK_4B91E7027645698E` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `direccion`
--
ALTER TABLE `direccion`
  ADD CONSTRAINT `fk_direccion_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `lineas_pedido`
--
ALTER TABLE `lineas_pedido`
  ADD CONSTRAINT `fk_linea_pedido_pedido` FOREIGN KEY (`pedido_id`) REFERENCES `pedidos` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_linea_pedido_producto` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`);

--
-- Filtros para la tabla `pedidos`
--
ALTER TABLE `pedidos`
  ADD CONSTRAINT `fk_pedidos_direccion` FOREIGN KEY (`direccion_id`) REFERENCES `direccion` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_pedidos_metodo_pago` FOREIGN KEY (`metodo_pago_id`) REFERENCES `metodo_pago` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_pedidos_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `pedido_producto`
--
ALTER TABLE `pedido_producto`
  ADD CONSTRAINT `fk_pedido_producto_pedido` FOREIGN KEY (`pedido_id`) REFERENCES `pedidos` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_pedido_producto_producto` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `productos`
--
ALTER TABLE `productos`
  ADD CONSTRAINT `fk_productos_categoria` FOREIGN KEY (`categoria_id`) REFERENCES `categoria` (`id`) ON DELETE SET NULL;

--
-- Filtros para la tabla `producto_variacion`
--
ALTER TABLE `producto_variacion`
  ADD CONSTRAINT `FK_59C4E9E67645698E` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`);

--
-- Filtros para la tabla `reset_password_request`
--
ALTER TABLE `reset_password_request`
  ADD CONSTRAINT `FK_7CE748AA76ED395` FOREIGN KEY (`user_id`) REFERENCES `usuarios` (`id`);

--
-- Filtros para la tabla `tarjeta`
--
ALTER TABLE `tarjeta`
  ADD CONSTRAINT `FK_TARJETA_USER` FOREIGN KEY (`user_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

-- phpMyAdmin SQL Dump
-- version 4.7.7
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 18-12-2024 a las 03:42:57
-- Versión del servidor: 10.1.30-MariaDB
-- Versión de PHP: 7.2.2

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `compu_click`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ajuste_stock`
--

CREATE TABLE `ajuste_stock` (
  `cod_ajuste` int(11) NOT NULL,
  `fecha` date NOT NULL,
  `estado_asjus` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `cod_usuario` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Volcado de datos para la tabla `ajuste_stock`
--

INSERT INTO `ajuste_stock` (`cod_ajuste`, `fecha`, `estado_asjus`, `cod_usuario`) VALUES
(1, '2024-12-17', 'ANULADO', 1),
(2, '2024-12-17', 'ANULADO', 1),
(3, '2024-12-17', 'ANULADO', 1),
(4, '2024-12-17', 'ACTIVO', 1);

--
-- Disparadores `ajuste_stock`
--
DELIMITER $$
CREATE TRIGGER `ajuste_stock_anular` AFTER UPDATE ON `ajuste_stock` FOR EACH ROW IF NEW.estado_asjus = 'ANULADO' THEN
UPDATE insumos 
join deta_ajuste c on c.cod_ajuste = NEW.cod_ajuste
SET insumos.cantidad = insumos.cantidad - c.cantidad
WHERE  insumos.cod_insumos = c.cod_insumos and c.tipo =  'ENTRADA';

UPDATE insumos 
join deta_ajuste c on c.cod_ajuste = NEW.cod_ajuste
SET insumos.cantidad = insumos.cantidad + c.cantidad
WHERE  insumos.cod_insumos = c.cod_insumos and c.tipo =  'SALIDA';
END IF
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `apertura_cierre`
--

CREATE TABLE `apertura_cierre` (
  `nro_aper_cierre` int(11) NOT NULL,
  `apertura_fecha` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `monto_apertura` int(11) NOT NULL,
  `total_efectivo` int(11) DEFAULT NULL,
  `total_tarjeta` int(11) DEFAULT NULL,
  `cheque` int(11) DEFAULT NULL,
  `cierre_fecha` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `monto_cierre` int(11) DEFAULT NULL,
  `aper_cier_estado` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `cod_caja` int(11) NOT NULL,
  `cod_usuario` int(11) NOT NULL,
  `cod_sucursal` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `banco`
--

CREATE TABLE `banco` (
  `cod_banco` int(11) NOT NULL,
  `descrip_banco` varchar(100) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `caja`
--

CREATE TABLE `caja` (
  `cod_caja` int(11) NOT NULL,
  `nro_expedicion` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `estado_caja` varchar(20) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cargo`
--

CREATE TABLE `cargo` (
  `cod_cargo` int(11) NOT NULL,
  `cargo_descripcion` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `estado_cargo` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Volcado de datos para la tabla `cargo`
--

INSERT INTO `cargo` (`cod_cargo`, `cargo_descripcion`, `estado_cargo`) VALUES
(1, 'CARGO 1', 'ACTIVO');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cheque`
--

CREATE TABLE `cheque` (
  `cod_cheque` int(11) NOT NULL,
  `cod_cuenta` int(11) NOT NULL,
  `nro_inicial` int(11) NOT NULL,
  `nro_fianl` int(11) NOT NULL,
  `tipo_cheque` varchar(50) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ciudad`
--

CREATE TABLE `ciudad` (
  `cod_ciudad` int(11) NOT NULL,
  `nombre_ciud` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `estado_ciud` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Volcado de datos para la tabla `ciudad`
--

INSERT INTO `ciudad` (`cod_ciudad`, `nombre_ciud`, `estado_ciud`) VALUES
(1, 'CIUDAD 1', 'ACTIVO');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cliente`
--

CREATE TABLE `cliente` (
  `cod_cliente` int(11) NOT NULL,
  `nombres_cli` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `ci_cliente` int(11) NOT NULL,
  `ruc` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  `fecha_nacimiento` date NOT NULL,
  `telefono` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `direccion` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `cod_ciu` int(11) NOT NULL,
  `estado_cli` varchar(20) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cliente_1`
--

CREATE TABLE `cliente_1` (
  `cod_cliente` int(11) NOT NULL,
  `nombre_cliente` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `ci_cliente` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  `ruc` varchar(12) COLLATE utf8_unicode_ci NOT NULL,
  `telefono` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `estado_cliente` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `cod_ciudad` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Volcado de datos para la tabla `cliente_1`
--

INSERT INTO `cliente_1` (`cod_cliente`, `nombre_cliente`, `ci_cliente`, `ruc`, `telefono`, `estado_cliente`, `cod_ciudad`) VALUES
(1, 'Luis Guzman', '2222', '5431313', '0971120712', 'ACTIVO', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cobros`
--

CREATE TABLE `cobros` (
  `cod_cobros` int(11) NOT NULL,
  `fecha_cobro` date NOT NULL,
  `monto` int(11) NOT NULL,
  `tipo_comprobante` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `estado_cobro` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `cod_formo_cobro` int(11) NOT NULL,
  `cod_usuario` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cobro_cheque`
--

CREATE TABLE `cobro_cheque` (
  `cod_cobro_cheque` int(11) NOT NULL,
  `monto_cheque` int(11) NOT NULL,
  `cod_formo_cobro` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cobro_tarjeta`
--

CREATE TABLE `cobro_tarjeta` (
  `cod_cobro_tarjeta` int(11) NOT NULL,
  `monto_tarjeta` int(11) NOT NULL,
  `cod_formo_cobro` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `color`
--

CREATE TABLE `color` (
  `cod_color` int(11) NOT NULL,
  `descripcion` varchar(100) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `compra`
--

CREATE TABLE `compra` (
  `cod_registro` int(11) NOT NULL,
  `fecha_compra` date NOT NULL,
  `cod_orden` int(11) DEFAULT NULL,
  `cod_proveedor` int(11) NOT NULL,
  `cod_usuario` int(11) NOT NULL,
  `nro_factura` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `timbrado` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `estado_registro` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `fecha_vencimiento_timbrado` date DEFAULT NULL,
  `condicion` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Volcado de datos para la tabla `compra`
--

INSERT INTO `compra` (`cod_registro`, `fecha_compra`, `cod_orden`, `cod_proveedor`, `cod_usuario`, `nro_factura`, `timbrado`, `estado_registro`, `fecha_vencimiento_timbrado`, `condicion`) VALUES
(1, '2024-12-17', 1, 1, 1, '001-001-0021222322', '12312', 'ACTIVO', '2024-12-17', 'CONTADO'),
(2, '2024-12-17', 1, 1, 1, '001-001-0021222322', '444444', 'ANULADO', '2024-12-17', 'CONTADO'),
(3, '2024-12-17', 1, 1, 1, '001-001-00212223', '12312', 'ACTIVO', '2024-12-17', 'CONTADO'),
(4, '2024-12-17', 2, 1, 1, '001-001-0021222322', '12312', 'ANULADO', '2024-12-17', 'CONTADO'),
(5, '2024-12-17', 3, 1, 1, '001-001-111111', '12312', 'ACTIVO', '2024-12-17', 'CONTADO'),
(6, '2024-12-17', 4, 1, 1, '2342344', '12312', 'ACTIVO', '2024-12-17', 'CONTADO');

--
-- Disparadores `compra`
--
DELIMITER $$
CREATE TRIGGER `anular_compra` AFTER UPDATE ON `compra` FOR EACH ROW IF NEW.estado_registro = 'ANULADO' THEN
UPDATE insumos 
join detalle_compra c on c.cod_compra = NEW.cod_registro
SET insumos.cantidad = insumos.cantidad - c.cantidad
WHERE  insumos.cod_insumos = c.cod_insumos;
END IF
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cuentarecibircabecera`
--

CREATE TABLE `cuentarecibircabecera` (
  `idcuentarecibircabecera` int(11) NOT NULL,
  `idventacabecera` int(11) NOT NULL,
  `estado` varchar(50) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cuenta_cobrar`
--

CREATE TABLE `cuenta_cobrar` (
  `cod_cuenta_cobrar` int(11) NOT NULL,
  `cod_cliente` int(11) NOT NULL,
  `monto` int(11) NOT NULL,
  `saldo` int(11) NOT NULL,
  `plazo` int(11) NOT NULL,
  `cantidad` int(11) NOT NULL,
  `fecha_vto` date NOT NULL,
  `estado_cobrar` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `cod_cobros` int(11) NOT NULL,
  `cod_usuario` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cuenta_pagar`
--

CREATE TABLE `cuenta_pagar` (
  `cod_cuenta` int(11) NOT NULL,
  `cod_compra` int(11) NOT NULL,
  `estado` char(20) COLLATE utf8_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `deposito`
--

CREATE TABLE `deposito` (
  `cod_deposito` int(11) NOT NULL,
  `dep_descripcion` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `estado_dep` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `cod_sucursal` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `descuento`
--

CREATE TABLE `descuento` (
  `cod_descuentos` int(11) NOT NULL,
  `fecha_inicio` date NOT NULL,
  `fecha_fin` date NOT NULL,
  `estado_descuentos` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `cod_usuario` int(11) NOT NULL,
  `cod_sucursal` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `descuento_servicio`
--

CREATE TABLE `descuento_servicio` (
  `cod_descuentos` int(11) NOT NULL,
  `cod_tiposervicios` int(11) NOT NULL,
  `costo` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalle_compra`
--

CREATE TABLE `detalle_compra` (
  `cod_compra` int(11) NOT NULL,
  `cod_insumos` int(11) NOT NULL,
  `cantidad` int(11) DEFAULT NULL,
  `costo` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Volcado de datos para la tabla `detalle_compra`
--

INSERT INTO `detalle_compra` (`cod_compra`, `cod_insumos`, `cantidad`, `costo`) VALUES
(1, 1, 7, 450000),
(1, 2, 8, 120000),
(2, 1, 7, 450000),
(2, 2, 8, 120000),
(3, 1, 7, 450000),
(3, 2, 8, 120000),
(4, 1, 1, 65000),
(4, 2, 1, 43000),
(5, 1, 1, 78000),
(5, 2, 1, 150000),
(6, 2, 13, 43000);

--
-- Disparadores `detalle_compra`
--
DELIMITER $$
CREATE TRIGGER `insert_compra` AFTER INSERT ON `detalle_compra` FOR EACH ROW UPDATE insumos i set i.cantidad =  i.cantidad + 1
WHERE i.cod_insumos =  NEW.cod_insumos
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalle_insumo`
--

CREATE TABLE `detalle_insumo` (
  `cod_registro_insumos` int(11) NOT NULL,
  `cod_insumo` int(11) NOT NULL,
  `cantidad` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalle_registro_ser`
--

CREATE TABLE `detalle_registro_ser` (
  `cod_registro_servicio` int(11) NOT NULL,
  `cod_tiposervicios` int(11) NOT NULL,
  `cantidad` int(11) DEFAULT NULL,
  `precio_unitario` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalle_tarjeta`
--

CREATE TABLE `detalle_tarjeta` (
  `cod_tarjetas` int(11) NOT NULL,
  `cod_cobros` int(11) NOT NULL,
  `monto` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `deta_ajuste`
--

CREATE TABLE `deta_ajuste` (
  `cod_ajuste` int(11) NOT NULL,
  `cod_insumos` int(11) NOT NULL,
  `cantidad` int(11) NOT NULL,
  `tipo` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Volcado de datos para la tabla `deta_ajuste`
--

INSERT INTO `deta_ajuste` (`cod_ajuste`, `cod_insumos`, `cantidad`, `tipo`) VALUES
(1, 1, 1, 'ENTRADA'),
(1, 2, 22, 'ENTRADA'),
(1, 3, 2, 'ENTRADA'),
(2, 1, 19, 'ENTRADA'),
(2, 2, 80, 'ENTRADA'),
(2, 3, 100, 'ENTRADA'),
(3, 1, 100, 'SALIDA'),
(3, 2, 1, 'SALIDA'),
(4, 1, 100, 'ENTRADA'),
(4, 2, 1, 'SALIDA');

--
-- Disparadores `deta_ajuste`
--
DELIMITER $$
CREATE TRIGGER `ajuste_stock` AFTER INSERT ON `deta_ajuste` FOR EACH ROW IF NEW.tipo = 'ENTRADA' THEN
UPDATE insumos 
SET insumos.cantidad = insumos.cantidad + NEW.cantidad
WHERE  insumos.cod_insumos = NEW.cod_insumos;
ELSEIF NEW.tipo = 'SALIDA' THEN
UPDATE insumos 
SET insumos.cantidad = insumos.cantidad - NEW.cantidad
WHERE  insumos.cod_insumos = NEW.cod_insumos;
END IF
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `deta_cheque`
--

CREATE TABLE `deta_cheque` (
  `cod_cobros` int(11) NOT NULL,
  `cod_cheque` int(11) NOT NULL,
  `cod_cuenta` int(11) NOT NULL,
  `monto` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `deta_cobros`
--

CREATE TABLE `deta_cobros` (
  `cod_cobros` int(11) NOT NULL,
  `cod_factura` int(11) NOT NULL,
  `cod_vehiculo` int(11) NOT NULL,
  `cantidad` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `deta_cred_compra`
--

CREATE TABLE `deta_cred_compra` (
  `cod_cred_compra` int(11) NOT NULL,
  `cod_insumos` int(11) NOT NULL,
  `cantidad` int(11) NOT NULL,
  `prec_uni` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `deta_debito_compra`
--

CREATE TABLE `deta_debito_compra` (
  `cod_debi_compra` int(11) NOT NULL,
  `cod_insumos` int(11) NOT NULL,
  `cantidad` int(11) NOT NULL,
  `precio_debito` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `deta_factura`
--

CREATE TABLE `deta_factura` (
  `cod_factura` int(11) NOT NULL,
  `cod_vehiculo` int(11) NOT NULL,
  `cantidad` int(11) NOT NULL,
  `precio_unitario` int(11) DEFAULT NULL,
  `total_fac` int(11) DEFAULT NULL,
  `cod_tiposervicios` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `deta_forma_cobro`
--

CREATE TABLE `deta_forma_cobro` (
  `cod_forma_cobro` int(11) NOT NULL,
  `efectivo_total` int(11) NOT NULL,
  `tarjeta_total` int(11) NOT NULL,
  `cheque_total` int(11) NOT NULL,
  `total_general` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `deta_nota`
--

CREATE TABLE `deta_nota` (
  `cod_nota` int(11) NOT NULL,
  `cod_insumo` int(11) NOT NULL,
  `cantidad` int(11) NOT NULL,
  `costo` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Volcado de datos para la tabla `deta_nota`
--

INSERT INTO `deta_nota` (`cod_nota`, `cod_insumo`, `cantidad`, `costo`) VALUES
(3, 1, 7, 0),
(3, 2, 8, 0),
(4, 1, 7, 450000),
(4, 2, 8, 120000),
(5, 1, 1, 78000);

--
-- Disparadores `deta_nota`
--
DELIMITER $$
CREATE TRIGGER `insert_nota_compra` AFTER INSERT ON `deta_nota` FOR EACH ROW UPDATE insumos i SET i.cantidad = i.cantidad - NEW.cantidad
WHERE i.cod_insumos = NEW.cod_insumo
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `deta_pedido_venta`
--

CREATE TABLE `deta_pedido_venta` (
  `cod_pedido` int(11) NOT NULL,
  `cod_vehiculo` int(11) NOT NULL,
  `cantidad` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `det_factura`
--

CREATE TABLE `det_factura` (
  `cod_registro` int(11) NOT NULL,
  `cod_insumos` int(11) NOT NULL,
  `cantidad` int(11) NOT NULL,
  `precio_unitario` int(11) DEFAULT NULL,
  `total` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `det_nota_compra`
--

CREATE TABLE `det_nota_compra` (
  `cod_remision_comp` int(11) NOT NULL,
  `cod_insumos` int(11) NOT NULL,
  `cantidad` int(11) NOT NULL,
  `precio_unitario_remision` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `det_nota_remision`
--

CREATE TABLE `det_nota_remision` (
  `cod_remision` int(11) NOT NULL,
  `cod_insumos` int(11) NOT NULL,
  `cantidad` int(11) DEFAULT NULL,
  `cantidad_factura` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Volcado de datos para la tabla `det_nota_remision`
--

INSERT INTO `det_nota_remision` (`cod_remision`, `cod_insumos`, `cantidad`, `cantidad_factura`) VALUES
(1, 1, 4, 7),
(2, 1, 6, 7),
(2, 2, 6, 8);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `det_orden`
--

CREATE TABLE `det_orden` (
  `cod_orden` int(11) NOT NULL,
  `cod_producto` int(11) NOT NULL,
  `cantidad` int(11) NOT NULL,
  `prec_uni` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Volcado de datos para la tabla `det_orden`
--

INSERT INTO `det_orden` (`cod_orden`, `cod_producto`, `cantidad`, `prec_uni`) VALUES
(1, 1, 7, 450000),
(1, 2, 8, 120000),
(2, 1, 1, 65000),
(2, 2, 1, 43000),
(3, 1, 1, 78000),
(3, 2, 1, 150000),
(4, 2, 13, 43000);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `det_pedido_compra`
--

CREATE TABLE `det_pedido_compra` (
  `cod_pedido_compra` int(11) NOT NULL,
  `cod_insumos` int(11) NOT NULL,
  `cantidad` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Volcado de datos para la tabla `det_pedido_compra`
--

INSERT INTO `det_pedido_compra` (`cod_pedido_compra`, `cod_insumos`, `cantidad`) VALUES
(1, 1, 1),
(1, 2, 1),
(2, 1, 1),
(2, 2, 1),
(3, 2, 13),
(4, 1, 13);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `det_presupuesto`
--

CREATE TABLE `det_presupuesto` (
  `cod_presupuesto` int(11) NOT NULL,
  `cod_insumos` int(11) NOT NULL,
  `cantidad` int(11) NOT NULL,
  `precio_unit` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Volcado de datos para la tabla `det_presupuesto`
--

INSERT INTO `det_presupuesto` (`cod_presupuesto`, `cod_insumos`, `cantidad`, `precio_unit`) VALUES
(1, 1, 7, 450000),
(1, 2, 8, 120000),
(2, 1, 1, 65000),
(2, 2, 1, 43000),
(3, 2, 13, 43000),
(4, 1, 13, 33000);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `det_presupuesto_servicio`
--

CREATE TABLE `det_presupuesto_servicio` (
  `cod_presupuesto_servicio` int(11) NOT NULL,
  `cod_tiposervicios` int(11) NOT NULL,
  `cod_insumos` int(11) NOT NULL,
  `cantidad_insumo` int(11) NOT NULL,
  `precio_unitario_ser` int(11) NOT NULL,
  `precio_insumo` int(11) DEFAULT NULL,
  `cod_promocion` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `det_remision`
--

CREATE TABLE `det_remision` (
  `cod_remision` int(11) NOT NULL,
  `cod_insumos` int(11) NOT NULL,
  `cantidad` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `det_solicitud`
--

CREATE TABLE `det_solicitud` (
  `cod_servicio_solicitud` int(11) NOT NULL,
  `cod_tiposervicios` int(11) NOT NULL,
  `cod_equipo` int(11) NOT NULL,
  `cantidad` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `det_titular`
--

CREATE TABLE `det_titular` (
  `cod_titular` int(11) NOT NULL,
  `descripcion` varchar(50) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `diagnosticos_detalle`
--

CREATE TABLE `diagnosticos_detalle` (
  `id_diagnostico` int(11) NOT NULL,
  `id_tipo_servicio` int(11) NOT NULL,
  `id_insumo` int(11) NOT NULL,
  `cantidad` int(11) NOT NULL,
  `observacion` varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `entidad_emisora`
--

CREATE TABLE `entidad_emisora` (
  `cod_entidad_emisora` int(11) NOT NULL,
  `entidad_descrip` varchar(100) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `equipo`
--

CREATE TABLE `equipo` (
  `cod_equipo` int(11) NOT NULL,
  `estado` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `id_tipo_equipo` int(11) NOT NULL,
  `id_color` int(11) NOT NULL,
  `id_modelo` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `factura`
--

CREATE TABLE `factura` (
  `cod_factura` int(11) NOT NULL,
  `fecha_venta` date NOT NULL,
  `intervalo` int(11) NOT NULL,
  `cantidad_cuotas` int(11) NOT NULL,
  `estado_factura` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `cod_pedido` int(11) NOT NULL,
  `cod_cliente` int(11) NOT NULL,
  `cod_usuario` int(11) NOT NULL,
  `cod_sucursal` int(11) NOT NULL,
  `cod_caja` int(11) NOT NULL,
  `tipo_pago` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `nro_timbrado` int(11) NOT NULL,
  `cod_promocion` int(11) DEFAULT NULL,
  `cod_descuentos` int(11) DEFAULT NULL,
  `nro_factura` char(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `nro_aper_cierre` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `forma_cobro`
--

CREATE TABLE `forma_cobro` (
  `cod_formo_cobro` int(11) NOT NULL,
  `descrip_pago` varchar(50) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `funcionario`
--

CREATE TABLE `funcionario` (
  `cod_funcionario` int(11) NOT NULL,
  `nombre_funcionario` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `ci_funcionario` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  `telefono_fun` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `direccion_fun` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `estado_fun` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `cod_cargo` int(11) NOT NULL,
  `cod_sucursal` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Volcado de datos para la tabla `funcionario`
--

INSERT INTO `funcionario` (`cod_funcionario`, `nombre_funcionario`, `ci_funcionario`, `telefono_fun`, `direccion_fun`, `estado_fun`, `cod_cargo`, `cod_sucursal`) VALUES
(1, 'NICOLAS OJEDA', '123', '098123', 'DIREC', 'ACTIVO', 1, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `impuesto`
--

CREATE TABLE `impuesto` (
  `tipo_iva` int(11) NOT NULL,
  `descripcion` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Volcado de datos para la tabla `impuesto`
--

INSERT INTO `impuesto` (`cod_impuesto`, `descripcion`) VALUES
(1, 'I.V.A. 10%'),
(2, 'I.V.A. 5%'),
(3, 'EXENTO');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `insumos`
--

CREATE TABLE `insumos` (
  `cod_insumos` int(11) NOT NULL,
  `descripcion` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `cod_marca_insumos` int(11) NOT NULL,
  `tipo_iva` int(11) NOT NULL,
  `cantidad` int(11) DEFAULT NULL,
  `estado_insumos` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `costo` int(11) DEFAULT NULL,
  `precio` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Volcado de datos para la tabla `insumos`
--

INSERT INTO `insumos` (`cod_insumos`, `descripcion`, `cod_marca_insumos`, `tipo_iva`, `cantidad`, `estado_insumos`, `costo`, `precio`) VALUES
(1, 'INSUMO 1', 1, 10, 100, 'ACTIVO', 33000, 150000),
(2, 'INSUMO 2', 1, 5, 1, 'ACTIVO', 43000, 178000),
(3, 'INSUMO 3', 1, 5, 0, 'ACTIVO', 13000, 138000),
(4, 'INSUMO 7', 1, 10, 11, 'ACTIVO', 120000, 790000);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `libro_compras`
--

CREATE TABLE `libro_compras` (
  `cod_libro_com` int(11) NOT NULL,
  `total` int(11) NOT NULL,
  `id_compra` int(11) NOT NULL,
  `iva5` int(11) DEFAULT NULL,
  `iva10` int(11) DEFAULT NULL,
  `exenta` int(11) DEFAULT NULL,
  `gravada5` int(11) DEFAULT NULL,
  `gravada10` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Volcado de datos para la tabla `libro_compras`
--

INSERT INTO `libro_compras` (`cod_libro_com`, `total`, `id_compra`, `iva5`, `iva10`, `exenta`, `gravada5`, `gravada10`) VALUES
(1, 195714, 2, 45714, 150000, 0, 914286, 3062727),
(2, 4110000, 3, 45714, 150000, 0, 914286, 3062727),
(3, 108000, 4, 2048, 3095, 0, 40952, 61091),
(4, 228000, 5, 7143, 3714, 0, 142857, 64364),
(5, 559000, 6, 26619, 0, 0, 532381, -50818);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `libro_venta`
--

CREATE TABLE `libro_venta` (
  `cod_libro` int(11) NOT NULL,
  `fecha` date NOT NULL,
  `monto` int(11) NOT NULL,
  `cod_factura` int(11) NOT NULL,
  `iva5` int(11) DEFAULT NULL,
  `iva10` int(11) DEFAULT NULL,
  `exenta` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `marca_insumos`
--

CREATE TABLE `marca_insumos` (
  `cod_marca_insumos` int(11) NOT NULL,
  `descripcion_marca` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `estado_marca_insumos` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Volcado de datos para la tabla `marca_insumos`
--

INSERT INTO `marca_insumos` (`cod_marca_insumos`, `descripcion_marca`, `estado_marca_insumos`) VALUES
(1, 'MARCA 1', 'ACTIVO');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `modelo`
--

CREATE TABLE `modelo` (
  `id_modelo` int(11) NOT NULL,
  `descripcion` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `estado` varchar(50) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `motivo`
--

CREATE TABLE `motivo` (
  `cod_motivo` int(11) NOT NULL,
  `descripcion` varchar(100) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `nota_compra`
--

CREATE TABLE `nota_compra` (
  `cod_nota_compra` int(11) NOT NULL,
  `estado_cre` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `tipo` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `fecha_cred` date NOT NULL,
  `cod_registro` int(11) NOT NULL,
  `cod_usuario` int(11) DEFAULT NULL,
  `timbrado` int(11) DEFAULT NULL,
  `fecha_vencimiento` date DEFAULT NULL,
  `motivo` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `nro_factura` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Volcado de datos para la tabla `nota_compra`
--

INSERT INTO `nota_compra` (`cod_nota_compra`, `estado_cre`, `tipo`, `fecha_cred`, `cod_registro`, `cod_usuario`, `timbrado`, `fecha_vencimiento`, `motivo`, `nro_factura`) VALUES
(1, 'ACTIVO', 'CREDITO', '2024-12-17', 1, 1, 12312, '2024-12-17', '', NULL),
(2, 'ACTIVO', 'CREDITO', '2024-12-17', 1, 1, 12312, '2024-12-17', 'aaaa', NULL),
(3, 'ANULADO', 'CREDITO', '2024-12-17', 1, 1, 12312, '2024-12-17', 'aaaa', NULL),
(4, 'ACTIVO', 'CREDITO', '2024-12-17', 1, 1, 12312, '2024-12-17', 'aaaa', '001-001-0021222322'),
(5, 'ACTIVO', 'CREDITO', '2024-12-17', 5, 1, 12312, '2024-12-17', '', '001-001-0021222322');

--
-- Disparadores `nota_compra`
--
DELIMITER $$
CREATE TRIGGER `anular_nota_compra` AFTER UPDATE ON `nota_compra` FOR EACH ROW IF NEW.estado_cre = 'ANULADO' THEN
UPDATE insumos 
join deta_nota c on c.cod_nota = NEW.cod_nota_compra
SET insumos.cantidad = insumos.cantidad + c.cantidad
WHERE  insumos.cod_insumos = c.cod_insumo;
END IF
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `nota_credito_vent`
--

CREATE TABLE `nota_credito_vent` (
  `cod_nota` int(11) NOT NULL,
  `fecha_credito` date NOT NULL,
  `estado_cred_venta` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `cod_usuario` int(11) NOT NULL,
  `cod_factura` int(11) NOT NULL,
  `tipo_venta` varchar(100) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `nota_debito_compra`
--

CREATE TABLE `nota_debito_compra` (
  `cod_debi_compra` int(11) NOT NULL,
  `fecha_debi` date NOT NULL,
  `tipo_deb` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `estado_debito` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `cod_registro` int(11) NOT NULL,
  `cod_usuario` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `nota_debito_vent`
--

CREATE TABLE `nota_debito_vent` (
  `cod_debito_vent` int(11) NOT NULL,
  `fecha_debito` date NOT NULL,
  `estado_debt_vent` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `cod_usuario` int(11) NOT NULL,
  `cod_factura` int(11) NOT NULL,
  `tipo_debito` varchar(100) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `nota_remision`
--

CREATE TABLE `nota_remision` (
  `cod_remision` int(11) NOT NULL,
  `fecha` date NOT NULL,
  `direccion` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `punto_salida` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `punt_llegada` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `estado_remision` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `cod_usuario` int(11) NOT NULL,
  `cod_factura` int(11) NOT NULL,
  `cod_cliente` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `nota_remision_compra`
--

CREATE TABLE `nota_remision_compra` (
  `cod_remision_comp` int(11) NOT NULL,
  `fecha` date NOT NULL,
  `punto_salida` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `punto_llegada` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `estado_remision_com` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `cod_usuario` int(11) NOT NULL,
  `cod_registro` int(11) NOT NULL,
  `vehiculo` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `motivo` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `chofer` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `nro_nota` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `timbrado` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `vencimiento` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Volcado de datos para la tabla `nota_remision_compra`
--

INSERT INTO `nota_remision_compra` (`cod_remision_comp`, `fecha`, `punto_salida`, `punto_llegada`, `estado_remision_com`, `cod_usuario`, `cod_registro`, `vehiculo`, `motivo`, `chofer`, `nro_nota`, `timbrado`, `vencimiento`) VALUES
(1, '2024-12-17', 'ITAUGUA', 'CAPIATA', 'ANULADO', 1, 1, 'SCANIA', 'MOTIVO ', 'jose', '001-002-124423', '12312', '2024-12-17'),
(2, '2024-12-17', 'ITAUGUA', 'CAPIATA', 'ACTIVO', 1, 1, 'SCANIA', 'MOTIVO ', 'jose', '001-002-1234423', '444444', '2024-12-17');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `orden_compra`
--

CREATE TABLE `orden_compra` (
  `cod_orden` int(11) NOT NULL,
  `oc_fecha_emision` date NOT NULL,
  `oc_estado` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `cod_presupuesto` int(11) DEFAULT NULL,
  `cod_usuario` int(11) NOT NULL,
  `cod_proveedor` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Volcado de datos para la tabla `orden_compra`
--

INSERT INTO `orden_compra` (`cod_orden`, `oc_fecha_emision`, `oc_estado`, `cod_presupuesto`, `cod_usuario`, `cod_proveedor`) VALUES
(1, '2024-12-17', 'UTILIZADO', 1, 1, 1),
(2, '2024-12-17', 'UTILIZADO', 2, 1, 1),
(3, '2024-12-17', 'UTILIZADO', 2, 1, 1),
(4, '2024-12-17', 'UTILIZADO', 3, 1, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `orden_servicio_cabecera`
--

CREATE TABLE `orden_servicio_cabecera` (
  `cod_orden_ser` int(11) NOT NULL,
  `fecha_emision_orden` date NOT NULL,
  `estado_orden_ser` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `cod_usuario` int(11) NOT NULL,
  `cod_cliente` int(11) NOT NULL,
  `cod_presupuesto_servicio` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pedido`
--

CREATE TABLE `pedido` (
  `cod_pedido` int(11) NOT NULL,
  `fecha_emision` date NOT NULL,
  `estado_pedido` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `cod_cliente` int(11) NOT NULL,
  `cod_sucursal` int(11) NOT NULL,
  `cod_usuario` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pedido_compra`
--

CREATE TABLE `pedido_compra` (
  `cod_pedido_compra` int(11) NOT NULL,
  `fecha_pedido` date NOT NULL,
  `estado_pedido_compra` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `cod_usuario` int(11) NOT NULL,
  `cod_sucursal` int(11) NOT NULL,
  `cod_proveedor` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Volcado de datos para la tabla `pedido_compra`
--

INSERT INTO `pedido_compra` (`cod_pedido_compra`, `fecha_pedido`, `estado_pedido_compra`, `cod_usuario`, `cod_sucursal`, `cod_proveedor`) VALUES
(1, '2024-12-17', 'ANULADO', 1, 1, 1),
(2, '2024-12-17', 'UTILIZADO', 1, 1, 1),
(3, '2024-12-17', 'UTILIZADO', 1, 1, 1),
(4, '2024-12-17', 'UTILIZADO', 1, 1, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `permiso`
--

CREATE TABLE `permiso` (
  `cod_permiso` int(11) NOT NULL,
  `permiso_descrip` varchar(50) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Volcado de datos para la tabla `permiso`
--

INSERT INTO `permiso` (`cod_permiso`, `permiso_descrip`) VALUES
(1, 'PERMISO 1');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `presupuesto`
--

CREATE TABLE `presupuesto` (
  `cod_presupuesto` int(11) NOT NULL,
  `fecha_presupuesto` date NOT NULL,
  `fecha_vencimiento` date NOT NULL,
  `estado_presupuesto` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `cod_pedido_compra` int(11) DEFAULT NULL,
  `cod_proveedor` int(11) NOT NULL,
  `cod_sucursal` int(11) NOT NULL,
  `cod_usuario` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Volcado de datos para la tabla `presupuesto`
--

INSERT INTO `presupuesto` (`cod_presupuesto`, `fecha_presupuesto`, `fecha_vencimiento`, `estado_presupuesto`, `cod_pedido_compra`, `cod_proveedor`, `cod_sucursal`, `cod_usuario`) VALUES
(1, '2024-12-17', '2024-12-17', 'ANULADO', 2, 1, 1, 1),
(2, '2024-12-17', '2024-12-17', 'UTILIZADO', 2, 1, 1, 1),
(3, '2024-12-17', '2024-12-17', 'UTILIZADO', 3, 1, 1, 1),
(4, '2024-12-17', '2024-12-17', 'PENDIENTE', 4, 1, 1, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `presupuesto_servicio`
--

CREATE TABLE `presupuesto_servicio` (
  `cod_presupuesto_servicio` int(11) NOT NULL,
  `fecha_emision_pre` date NOT NULL,
  `fecha_ven_servicio` date NOT NULL,
  `estado_pre_servicio` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `cod_usuario` int(11) NOT NULL,
  `cod_cliente` int(11) NOT NULL,
  `cod_sucursal` int(11) NOT NULL,
  `id_diagnostico` int(11) NOT NULL,
  `cod_equipo` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `promociones_cabecera`
--

CREATE TABLE `promociones_cabecera` (
  `cod_promocion` int(11) NOT NULL,
  `descripcion_promocion` char(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `fecha_inicio` date DEFAULT NULL,
  `fecha_fin` date DEFAULT NULL,
  `cod_usuario` int(11) DEFAULT NULL,
  `estado_promocion` char(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  `cod_sucursal` int(11) DEFAULT NULL,
  `cod_tiposervicios` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `promociones_detalle`
--

CREATE TABLE `promociones_detalle` (
  `id_promocion` int(11) NOT NULL,
  `id_insumo` int(11) NOT NULL,
  `cantidad` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `promo_servicio`
--

CREATE TABLE `promo_servicio` (
  `cod_promocion` int(11) NOT NULL,
  `cod_tiposervicios` int(11) NOT NULL,
  `plazo` int(11) DEFAULT NULL,
  `costo` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `proveedor`
--

CREATE TABLE `proveedor` (
  `cod_proveedor` int(11) NOT NULL,
  `pro_razonsocial` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `pro_ruc` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `pro_telef` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `estado_proveedor` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `cod_ciudad` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Volcado de datos para la tabla `proveedor`
--

INSERT INTO `proveedor` (`cod_proveedor`, `pro_razonsocial`, `pro_ruc`, `pro_telef`, `estado_proveedor`, `cod_ciudad`) VALUES
(1, 'PROVEEDOR 1', '123', '9999', 'ACTIVO', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `recaudaciones_depositar`
--

CREATE TABLE `recaudaciones_depositar` (
  `cod_recaudaciones` int(11) NOT NULL,
  `total_efectivo` int(11) NOT NULL,
  `total_cheque` int(11) NOT NULL,
  `nro_aper_cierre` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `recepcion_cabecera`
--

CREATE TABLE `recepcion_cabecera` (
  `cod_recepcion` int(11) NOT NULL,
  `cod_cliente` int(11) DEFAULT NULL,
  `cod_sucursal` int(11) DEFAULT NULL,
  `cod_usuario` int(11) DEFAULT NULL,
  `observacion` char(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `fecha_recepcion` date DEFAULT NULL,
  `recepcion_estado` char(10) COLLATE utf8_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `reclamos_clientes`
--

CREATE TABLE `reclamos_clientes` (
  `cod_reclamos` int(11) NOT NULL,
  `descripcion_reclamos` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `causa` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `fecha_reclamo` date NOT NULL,
  `solucion` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `cod_usuario` int(11) NOT NULL,
  `cod_cliente` int(11) NOT NULL,
  `cod_sucursal` int(11) NOT NULL,
  `cod_orden_ser` int(11) NOT NULL,
  `estado_reclamo` varchar(20) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `registro_insumos_utilizados`
--

CREATE TABLE `registro_insumos_utilizados` (
  `cod_registro_insumos` int(11) NOT NULL,
  `fecha_registro` date DEFAULT NULL,
  `cod_usuario` int(11) DEFAULT NULL,
  `cod_sucursal` int(11) DEFAULT NULL,
  `estado_insumo` char(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  `cod_tiposervicios` int(11) NOT NULL,
  `cod_orden_ser` int(11) NOT NULL,
  `cod_insumos` int(11) NOT NULL,
  `cantidad` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `registro_servicio`
--

CREATE TABLE `registro_servicio` (
  `cod_registro_servicio` int(11) NOT NULL,
  `fecha` date DEFAULT NULL,
  `descripcion` char(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  `cod_usuario` int(11) DEFAULT NULL,
  `cod_sucursal` int(11) DEFAULT NULL,
  `cod_orden_ser` int(11) DEFAULT NULL,
  `cod_cliente` int(11) DEFAULT NULL,
  `estado` char(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  `cod_vehiculo` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `servicios`
--

CREATE TABLE `servicios` (
  `cod_tiposervicios` int(11) NOT NULL,
  `tiposervicios` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `costo` int(11) NOT NULL,
  `estado_servicios` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `tipo_impuesto_servicio` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `solicitud_servicio`
--

CREATE TABLE `solicitud_servicio` (
  `cod_servicio_solicitud` int(11) NOT NULL,
  `fecha_emision` date DEFAULT NULL,
  `estado_solicitud` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `cod_usuario` int(11) DEFAULT NULL,
  `cod_cliente` int(11) NOT NULL,
  `cod_sucursal` int(11) DEFAULT NULL,
  `fecha_finalizacion` date DEFAULT NULL,
  `descripcion` char(100) COLLATE utf8_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `stock`
--

CREATE TABLE `stock` (
  `cod_deposito` int(11) NOT NULL,
  `cod_insumos` int(11) NOT NULL,
  `cantidad_minima` int(11) NOT NULL,
  `cantidad_maxima` int(11) NOT NULL,
  `cantidad_actual` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sucursal`
--

CREATE TABLE `sucursal` (
  `cod_sucursal` int(11) NOT NULL,
  `nombre_sucur` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `estado_sucur` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `cod_ciudad` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Volcado de datos para la tabla `sucursal`
--

INSERT INTO `sucursal` (`cod_sucursal`, `nombre_sucur`, `estado_sucur`, `cod_ciudad`) VALUES
(1, 'SUCURSAL 1', 'ACTIVO', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tarjetas`
--

CREATE TABLE `tarjetas` (
  `cod_tarjetas` int(11) NOT NULL,
  `tipo_tarjeta` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `estado_tarjeta` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `cod_entidad_emisora` int(11) NOT NULL,
  `cod_cuenta` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `timbrado`
--

CREATE TABLE `timbrado` (
  `nro_timbrado` int(11) NOT NULL,
  `fecha_inicio` date NOT NULL,
  `fecha_fin` date NOT NULL,
  `estado_timbrado` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `nro_timbrado_datos` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tipo_cuenta`
--

CREATE TABLE `tipo_cuenta` (
  `cod_cuenta` int(11) NOT NULL,
  `nro_cuenta` int(11) NOT NULL,
  `descrip_cuenta` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `cod_titular` int(11) NOT NULL,
  `cod_banco` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tipo_equipo`
--

CREATE TABLE `tipo_equipo` (
  `cod_tipo` int(11) NOT NULL,
  `nombre_tipo` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `estado` char(20) COLLATE utf8_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `titular`
--

CREATE TABLE `titular` (
  `cod_titular` int(11) NOT NULL,
  `t_nombre` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `t_apellido` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `t_ci` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `t_telefono` varchar(100) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuario`
--

CREATE TABLE `usuario` (
  `cod_usuario` int(11) NOT NULL,
  `usuario_alias` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `usu_clave` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `estado_usu` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `cod_sucursal` int(11) NOT NULL,
  `cod_funcionario` int(11) NOT NULL,
  `cod_permiso` int(11) NOT NULL,
  `nro_timbrado` int(11) DEFAULT NULL,
  `intentos` int(11) DEFAULT '0',
  `limite` int(11) DEFAULT '3'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Volcado de datos para la tabla `usuario`
--

INSERT INTO `usuario` (`cod_usuario`, `usuario_alias`, `usu_clave`, `estado_usu`, `cod_sucursal`, `cod_funcionario`, `cod_permiso`, `nro_timbrado`, `intentos`, `limite`) VALUES
(1, 'nico', '202cb962ac59075b964b07152d234b70', 'ACTIVO', 1, 1, 1, 123, 0, 3);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `v_usuario`
--

CREATE TABLE `v_usuario` (
  `cod_usuario` int(11) DEFAULT NULL,
  `usuario_alias` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `usu_clave` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `estado_usu` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `cod_funcionario` int(11) DEFAULT NULL,
  `nombre_funcionario` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `cod_permiso` int(11) DEFAULT NULL,
  `permiso_descrip` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `cod_sucursal` int(11) DEFAULT NULL,
  `nombre_sucur` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `nro_timbrado` int(11) DEFAULT NULL,
  `nro_timbrado_datos` int(11) DEFAULT NULL,
  `funcionario` text COLLATE utf8_unicode_ci,
  `permiso` text COLLATE utf8_unicode_ci,
  `permisos` text COLLATE utf8_unicode_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `ajuste_stock`
--
ALTER TABLE `ajuste_stock`
  ADD PRIMARY KEY (`cod_ajuste`),
  ADD KEY `usuario_ajuste_stock_fk` (`cod_usuario`);

--
-- Indices de la tabla `apertura_cierre`
--
ALTER TABLE `apertura_cierre`
  ADD PRIMARY KEY (`nro_aper_cierre`),
  ADD KEY `caja_apertura_cierre_fk` (`cod_caja`);

--
-- Indices de la tabla `banco`
--
ALTER TABLE `banco`
  ADD PRIMARY KEY (`cod_banco`);

--
-- Indices de la tabla `caja`
--
ALTER TABLE `caja`
  ADD PRIMARY KEY (`cod_caja`);

--
-- Indices de la tabla `cargo`
--
ALTER TABLE `cargo`
  ADD PRIMARY KEY (`cod_cargo`);

--
-- Indices de la tabla `cheque`
--
ALTER TABLE `cheque`
  ADD PRIMARY KEY (`cod_cheque`,`cod_cuenta`),
  ADD KEY `tipo_cuenta_cheque_fk` (`cod_cuenta`);

--
-- Indices de la tabla `ciudad`
--
ALTER TABLE `ciudad`
  ADD PRIMARY KEY (`cod_ciudad`);

--
-- Indices de la tabla `cliente`
--
ALTER TABLE `cliente`
  ADD PRIMARY KEY (`cod_cliente`);

--
-- Indices de la tabla `cliente_1`
--
ALTER TABLE `cliente_1`
  ADD PRIMARY KEY (`cod_cliente`),
  ADD KEY `ciudad_cliente_fk` (`cod_ciudad`);

--
-- Indices de la tabla `cobros`
--
ALTER TABLE `cobros`
  ADD PRIMARY KEY (`cod_cobros`);

--
-- Indices de la tabla `cobro_cheque`
--
ALTER TABLE `cobro_cheque`
  ADD PRIMARY KEY (`cod_cobro_cheque`);

--
-- Indices de la tabla `cobro_tarjeta`
--
ALTER TABLE `cobro_tarjeta`
  ADD PRIMARY KEY (`cod_cobro_tarjeta`);

--
-- Indices de la tabla `color`
--
ALTER TABLE `color`
  ADD PRIMARY KEY (`cod_color`);

--
-- Indices de la tabla `compra`
--
ALTER TABLE `compra`
  ADD PRIMARY KEY (`cod_registro`);

--
-- Indices de la tabla `cuentarecibircabecera`
--
ALTER TABLE `cuentarecibircabecera`
  ADD PRIMARY KEY (`idcuentarecibircabecera`,`idventacabecera`);

--
-- Indices de la tabla `cuenta_cobrar`
--
ALTER TABLE `cuenta_cobrar`
  ADD PRIMARY KEY (`cod_cuenta_cobrar`),
  ADD KEY `cobros_cuenta_cobrar_fk` (`cod_cobros`),
  ADD KEY `cliente_1_cuenta_cobrar_fk` (`cod_cliente`);

--
-- Indices de la tabla `cuenta_pagar`
--
ALTER TABLE `cuenta_pagar`
  ADD PRIMARY KEY (`cod_cuenta`),
  ADD KEY `cuenta_pagar_cod_compra_fkey` (`cod_compra`);

--
-- Indices de la tabla `deposito`
--
ALTER TABLE `deposito`
  ADD PRIMARY KEY (`cod_deposito`);

--
-- Indices de la tabla `descuento`
--
ALTER TABLE `descuento`
  ADD PRIMARY KEY (`cod_descuentos`);

--
-- Indices de la tabla `descuento_servicio`
--
ALTER TABLE `descuento_servicio`
  ADD PRIMARY KEY (`cod_descuentos`,`cod_tiposervicios`),
  ADD KEY `servicios_descuento_servicio_fk` (`cod_tiposervicios`);

--
-- Indices de la tabla `detalle_compra`
--
ALTER TABLE `detalle_compra`
  ADD PRIMARY KEY (`cod_compra`,`cod_insumos`);

--
-- Indices de la tabla `detalle_insumo`
--
ALTER TABLE `detalle_insumo`
  ADD PRIMARY KEY (`cod_registro_insumos`,`cod_insumo`);

--
-- Indices de la tabla `detalle_registro_ser`
--
ALTER TABLE `detalle_registro_ser`
  ADD PRIMARY KEY (`cod_registro_servicio`,`cod_tiposervicios`);

--
-- Indices de la tabla `detalle_tarjeta`
--
ALTER TABLE `detalle_tarjeta`
  ADD PRIMARY KEY (`cod_tarjetas`,`cod_cobros`),
  ADD KEY `cobros_detalle_tarjeta_fk` (`cod_cobros`);

--
-- Indices de la tabla `deta_ajuste`
--
ALTER TABLE `deta_ajuste`
  ADD PRIMARY KEY (`cod_ajuste`,`cod_insumos`),
  ADD KEY `insumos_deta_ajuste_fk` (`cod_insumos`);

--
-- Indices de la tabla `deta_cheque`
--
ALTER TABLE `deta_cheque`
  ADD PRIMARY KEY (`cod_cobros`,`cod_cheque`,`cod_cuenta`),
  ADD KEY `cheque_deta_cheque_cobro_fk` (`cod_cheque`,`cod_cuenta`);

--
-- Indices de la tabla `deta_cobros`
--
ALTER TABLE `deta_cobros`
  ADD PRIMARY KEY (`cod_cobros`,`cod_factura`,`cod_vehiculo`),
  ADD KEY `deta_factura_deta_cobros_fk` (`cod_factura`,`cod_vehiculo`);

--
-- Indices de la tabla `deta_cred_compra`
--
ALTER TABLE `deta_cred_compra`
  ADD PRIMARY KEY (`cod_cred_compra`,`cod_insumos`),
  ADD KEY `insumos_deta_cred_compra_fk` (`cod_insumos`);

--
-- Indices de la tabla `deta_debito_compra`
--
ALTER TABLE `deta_debito_compra`
  ADD PRIMARY KEY (`cod_debi_compra`,`cod_insumos`),
  ADD KEY `insumos_deta_debito_compra_fk` (`cod_insumos`);

--
-- Indices de la tabla `deta_factura`
--
ALTER TABLE `deta_factura`
  ADD PRIMARY KEY (`cod_factura`,`cod_vehiculo`);

--
-- Indices de la tabla `deta_forma_cobro`
--
ALTER TABLE `deta_forma_cobro`
  ADD PRIMARY KEY (`cod_forma_cobro`);

--
-- Indices de la tabla `deta_nota`
--
ALTER TABLE `deta_nota`
  ADD PRIMARY KEY (`cod_nota`,`cod_insumo`);

--
-- Indices de la tabla `deta_pedido_venta`
--
ALTER TABLE `deta_pedido_venta`
  ADD PRIMARY KEY (`cod_pedido`,`cod_vehiculo`);

--
-- Indices de la tabla `det_factura`
--
ALTER TABLE `det_factura`
  ADD PRIMARY KEY (`cod_registro`);

--
-- Indices de la tabla `det_nota_compra`
--
ALTER TABLE `det_nota_compra`
  ADD PRIMARY KEY (`cod_remision_comp`,`cod_insumos`),
  ADD KEY `insumos_det_nota_compra_fk` (`cod_insumos`);

--
-- Indices de la tabla `det_nota_remision`
--
ALTER TABLE `det_nota_remision`
  ADD PRIMARY KEY (`cod_remision`,`cod_insumos`) USING BTREE;

--
-- Indices de la tabla `det_orden`
--
ALTER TABLE `det_orden`
  ADD PRIMARY KEY (`cod_orden`,`cod_producto`),
  ADD KEY `producto_det_orden_fk` (`cod_producto`);

--
-- Indices de la tabla `det_pedido_compra`
--
ALTER TABLE `det_pedido_compra`
  ADD PRIMARY KEY (`cod_pedido_compra`,`cod_insumos`),
  ADD KEY `insumos_det_pedido_compra_fk` (`cod_insumos`);

--
-- Indices de la tabla `det_presupuesto`
--
ALTER TABLE `det_presupuesto`
  ADD PRIMARY KEY (`cod_presupuesto`,`cod_insumos`),
  ADD KEY `insumos_det_presupuesto_fk` (`cod_insumos`);

--
-- Indices de la tabla `det_presupuesto_servicio`
--
ALTER TABLE `det_presupuesto_servicio`
  ADD PRIMARY KEY (`cod_presupuesto_servicio`,`cod_tiposervicios`,`cod_insumos`);

--
-- Indices de la tabla `det_remision`
--
ALTER TABLE `det_remision`
  ADD PRIMARY KEY (`cod_remision`,`cod_insumos`);

--
-- Indices de la tabla `det_solicitud`
--
ALTER TABLE `det_solicitud`
  ADD PRIMARY KEY (`cod_servicio_solicitud`,`cod_tiposervicios`),
  ADD KEY `equipo_det_solicitud_fk` (`cod_equipo`);

--
-- Indices de la tabla `det_titular`
--
ALTER TABLE `det_titular`
  ADD PRIMARY KEY (`cod_titular`);

--
-- Indices de la tabla `diagnosticos_detalle`
--
ALTER TABLE `diagnosticos_detalle`
  ADD PRIMARY KEY (`id_diagnostico`),
  ADD KEY `diagnosticos_detalle_id_tipo_servicio_fkey` (`id_tipo_servicio`),
  ADD KEY `diagnosticos_detalle_id_insumo_fkey` (`id_insumo`);

--
-- Indices de la tabla `entidad_emisora`
--
ALTER TABLE `entidad_emisora`
  ADD PRIMARY KEY (`cod_entidad_emisora`);

--
-- Indices de la tabla `equipo`
--
ALTER TABLE `equipo`
  ADD PRIMARY KEY (`cod_equipo`),
  ADD KEY `tipo_vehiculo_producto_vehiculo_fk` (`id_tipo_equipo`),
  ADD KEY `color_vehiculo_fk` (`id_color`);

--
-- Indices de la tabla `factura`
--
ALTER TABLE `factura`
  ADD PRIMARY KEY (`cod_factura`),
  ADD KEY `cliente_factura_fk` (`cod_cliente`),
  ADD KEY `timbrado_fk` (`nro_timbrado`);

--
-- Indices de la tabla `forma_cobro`
--
ALTER TABLE `forma_cobro`
  ADD PRIMARY KEY (`cod_formo_cobro`);

--
-- Indices de la tabla `funcionario`
--
ALTER TABLE `funcionario`
  ADD PRIMARY KEY (`cod_funcionario`),
  ADD KEY `cargo_funcionario_fk` (`cod_cargo`);

--
-- Indices de la tabla `impuesto`
--
ALTER TABLE `impuesto`
  ADD PRIMARY KEY (`cod_impuesto`);

--
-- Indices de la tabla `insumos`
--
ALTER TABLE `insumos`
  ADD PRIMARY KEY (`cod_insumos`),
  ADD KEY `marca_insumos_insumos_fk` (`cod_marca_insumos`);

--
-- Indices de la tabla `libro_compras`
--
ALTER TABLE `libro_compras`
  ADD PRIMARY KEY (`cod_libro_com`);

--
-- Indices de la tabla `libro_venta`
--
ALTER TABLE `libro_venta`
  ADD PRIMARY KEY (`cod_libro`);

--
-- Indices de la tabla `marca_insumos`
--
ALTER TABLE `marca_insumos`
  ADD PRIMARY KEY (`cod_marca_insumos`);

--
-- Indices de la tabla `motivo`
--
ALTER TABLE `motivo`
  ADD PRIMARY KEY (`cod_motivo`);

--
-- Indices de la tabla `nota_compra`
--
ALTER TABLE `nota_compra`
  ADD PRIMARY KEY (`cod_nota_compra`),
  ADD KEY `registro_compra_nota_credito_compra_fk` (`cod_registro`);

--
-- Indices de la tabla `nota_credito_vent`
--
ALTER TABLE `nota_credito_vent`
  ADD PRIMARY KEY (`cod_nota`);

--
-- Indices de la tabla `nota_debito_compra`
--
ALTER TABLE `nota_debito_compra`
  ADD PRIMARY KEY (`cod_debi_compra`),
  ADD KEY `registro_compra_nota_debito_compra_fk` (`cod_registro`);

--
-- Indices de la tabla `nota_debito_vent`
--
ALTER TABLE `nota_debito_vent`
  ADD PRIMARY KEY (`cod_debito_vent`);

--
-- Indices de la tabla `nota_remision`
--
ALTER TABLE `nota_remision`
  ADD KEY `fk_cliente_1` (`cod_cliente`);

--
-- Indices de la tabla `nota_remision_compra`
--
ALTER TABLE `nota_remision_compra`
  ADD PRIMARY KEY (`cod_remision_comp`),
  ADD KEY `registro_compra_nota_remision_compra_fk` (`cod_registro`);

--
-- Indices de la tabla `orden_compra`
--
ALTER TABLE `orden_compra`
  ADD PRIMARY KEY (`cod_orden`),
  ADD KEY `proveedor_orden_compra_fk` (`cod_proveedor`);

--
-- Indices de la tabla `orden_servicio_cabecera`
--
ALTER TABLE `orden_servicio_cabecera`
  ADD PRIMARY KEY (`cod_orden_ser`),
  ADD KEY `cliente_1_orden_servicio_fk` (`cod_cliente`),
  ADD KEY `presupuesto_servicio_orden_servicio_fk` (`cod_presupuesto_servicio`);

--
-- Indices de la tabla `pedido`
--
ALTER TABLE `pedido`
  ADD PRIMARY KEY (`cod_pedido`),
  ADD KEY `cliente_pedido_fk` (`cod_cliente`);

--
-- Indices de la tabla `pedido_compra`
--
ALTER TABLE `pedido_compra`
  ADD PRIMARY KEY (`cod_pedido_compra`);

--
-- Indices de la tabla `permiso`
--
ALTER TABLE `permiso`
  ADD PRIMARY KEY (`cod_permiso`);

--
-- Indices de la tabla `presupuesto`
--
ALTER TABLE `presupuesto`
  ADD PRIMARY KEY (`cod_presupuesto`),
  ADD KEY `proveedor_presupuesto_fk` (`cod_proveedor`);

--
-- Indices de la tabla `presupuesto_servicio`
--
ALTER TABLE `presupuesto_servicio`
  ADD PRIMARY KEY (`cod_presupuesto_servicio`),
  ADD KEY `cliente_1_presupuesto_servicio_fk` (`cod_cliente`);

--
-- Indices de la tabla `promociones_cabecera`
--
ALTER TABLE `promociones_cabecera`
  ADD PRIMARY KEY (`cod_promocion`),
  ADD KEY `fk_promociones_cabecera_servicios` (`cod_tiposervicios`);

--
-- Indices de la tabla `promociones_detalle`
--
ALTER TABLE `promociones_detalle`
  ADD PRIMARY KEY (`id_promocion`,`id_insumo`),
  ADD KEY `fk__insumos` (`id_insumo`);

--
-- Indices de la tabla `promo_servicio`
--
ALTER TABLE `promo_servicio`
  ADD PRIMARY KEY (`cod_promocion`);

--
-- Indices de la tabla `proveedor`
--
ALTER TABLE `proveedor`
  ADD PRIMARY KEY (`cod_proveedor`),
  ADD KEY `ciudad_proveedor_fk` (`cod_ciudad`);

--
-- Indices de la tabla `recaudaciones_depositar`
--
ALTER TABLE `recaudaciones_depositar`
  ADD PRIMARY KEY (`cod_recaudaciones`);

--
-- Indices de la tabla `recepcion_cabecera`
--
ALTER TABLE `recepcion_cabecera`
  ADD PRIMARY KEY (`cod_recepcion`);

--
-- Indices de la tabla `reclamos_clientes`
--
ALTER TABLE `reclamos_clientes`
  ADD PRIMARY KEY (`cod_reclamos`),
  ADD KEY `cliente_reclamos_clientes_fk` (`cod_cliente`),
  ADD KEY `orden_servicio_reclamos_clientes_fk` (`cod_orden_ser`);

--
-- Indices de la tabla `registro_insumos_utilizados`
--
ALTER TABLE `registro_insumos_utilizados`
  ADD PRIMARY KEY (`cod_registro_insumos`),
  ADD KEY `servicios_registro_insumos_utilizados_fk` (`cod_tiposervicios`),
  ADD KEY `insumos_registro_insumos_utilizados_fk` (`cod_insumos`),
  ADD KEY `orden_servicio_registro_insumos_utilizados_fk` (`cod_orden_ser`);

--
-- Indices de la tabla `registro_servicio`
--
ALTER TABLE `registro_servicio`
  ADD PRIMARY KEY (`cod_registro_servicio`);

--
-- Indices de la tabla `servicios`
--
ALTER TABLE `servicios`
  ADD PRIMARY KEY (`cod_tiposervicios`);

--
-- Indices de la tabla `solicitud_servicio`
--
ALTER TABLE `solicitud_servicio`
  ADD PRIMARY KEY (`cod_servicio_solicitud`),
  ADD KEY `cliente_solicitud_servicio_fk` (`cod_cliente`);

--
-- Indices de la tabla `stock`
--
ALTER TABLE `stock`
  ADD PRIMARY KEY (`cod_deposito`,`cod_insumos`),
  ADD KEY `insumos_stock_fk` (`cod_insumos`);

--
-- Indices de la tabla `sucursal`
--
ALTER TABLE `sucursal`
  ADD PRIMARY KEY (`cod_sucursal`),
  ADD KEY `ciudad_sucursal_fk` (`cod_ciudad`);

--
-- Indices de la tabla `tarjetas`
--
ALTER TABLE `tarjetas`
  ADD PRIMARY KEY (`cod_tarjetas`),
  ADD KEY `entidad_emisora_tarjetas_fk` (`cod_entidad_emisora`);

--
-- Indices de la tabla `timbrado`
--
ALTER TABLE `timbrado`
  ADD PRIMARY KEY (`nro_timbrado`);

--
-- Indices de la tabla `tipo_cuenta`
--
ALTER TABLE `tipo_cuenta`
  ADD PRIMARY KEY (`cod_cuenta`),
  ADD KEY `banco_tipo_cuenta_fk` (`cod_banco`);

--
-- Indices de la tabla `tipo_equipo`
--
ALTER TABLE `tipo_equipo`
  ADD PRIMARY KEY (`cod_tipo`);

--
-- Indices de la tabla `titular`
--
ALTER TABLE `titular`
  ADD PRIMARY KEY (`cod_titular`);

--
-- Indices de la tabla `usuario`
--
ALTER TABLE `usuario`
  ADD PRIMARY KEY (`cod_usuario`),
  ADD KEY `funcionario_usuario_fk` (`cod_funcionario`),
  ADD KEY `permiso_usuario_fk` (`cod_permiso`);

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `ajuste_stock`
--
ALTER TABLE `ajuste_stock`
  ADD CONSTRAINT `usuario_ajuste_stock_fk` FOREIGN KEY (`cod_usuario`) REFERENCES `usuario` (`cod_usuario`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Filtros para la tabla `apertura_cierre`
--
ALTER TABLE `apertura_cierre`
  ADD CONSTRAINT `caja_apertura_cierre_fk` FOREIGN KEY (`cod_caja`) REFERENCES `caja` (`cod_caja`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Filtros para la tabla `cheque`
--
ALTER TABLE `cheque`
  ADD CONSTRAINT `tipo_cuenta_cheque_fk` FOREIGN KEY (`cod_cuenta`) REFERENCES `tipo_cuenta` (`cod_cuenta`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Filtros para la tabla `cliente_1`
--
ALTER TABLE `cliente_1`
  ADD CONSTRAINT `ciudad_cliente_fk` FOREIGN KEY (`cod_ciudad`) REFERENCES `ciudad` (`cod_ciudad`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Filtros para la tabla `cuenta_cobrar`
--
ALTER TABLE `cuenta_cobrar`
  ADD CONSTRAINT `cliente_1_cuenta_cobrar_fk` FOREIGN KEY (`cod_cliente`) REFERENCES `cliente_1` (`cod_cliente`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `cobros_cuenta_cobrar_fk` FOREIGN KEY (`cod_cobros`) REFERENCES `cobros` (`cod_cobros`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Filtros para la tabla `cuenta_pagar`
--
ALTER TABLE `cuenta_pagar`
  ADD CONSTRAINT `cuenta_pagar_cod_compra_fkey` FOREIGN KEY (`cod_compra`) REFERENCES `compra` (`cod_registro`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Filtros para la tabla `descuento_servicio`
--
ALTER TABLE `descuento_servicio`
  ADD CONSTRAINT `descuentos_descuento_servicio_fk` FOREIGN KEY (`cod_descuentos`) REFERENCES `servicios` (`cod_tiposervicios`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `servicios_descuento_servicio_fk` FOREIGN KEY (`cod_tiposervicios`) REFERENCES `servicios` (`cod_tiposervicios`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Filtros para la tabla `detalle_compra`
--
ALTER TABLE `detalle_compra`
  ADD CONSTRAINT `registro_compra_det_compra_fk` FOREIGN KEY (`cod_compra`) REFERENCES `compra` (`cod_registro`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Filtros para la tabla `detalle_tarjeta`
--
ALTER TABLE `detalle_tarjeta`
  ADD CONSTRAINT `cobros_detalle_tarjeta_fk` FOREIGN KEY (`cod_cobros`) REFERENCES `cobros` (`cod_cobros`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `tarjetas_detalle_tarjeta_fk` FOREIGN KEY (`cod_tarjetas`) REFERENCES `tarjetas` (`cod_tarjetas`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Filtros para la tabla `deta_ajuste`
--
ALTER TABLE `deta_ajuste`
  ADD CONSTRAINT `ajuste_stock_deta_ajuste_fk` FOREIGN KEY (`cod_ajuste`) REFERENCES `ajuste_stock` (`cod_ajuste`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `insumos_deta_ajuste_fk` FOREIGN KEY (`cod_insumos`) REFERENCES `insumos` (`cod_insumos`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Filtros para la tabla `deta_cheque`
--
ALTER TABLE `deta_cheque`
  ADD CONSTRAINT `cheque_deta_cheque_cobro_fk` FOREIGN KEY (`cod_cheque`,`cod_cuenta`) REFERENCES `cheque` (`cod_cheque`, `cod_cuenta`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `cobros_deta_cheque_fk` FOREIGN KEY (`cod_cobros`) REFERENCES `cobros` (`cod_cobros`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Filtros para la tabla `deta_cobros`
--
ALTER TABLE `deta_cobros`
  ADD CONSTRAINT `cobros_deta_cobros_fk` FOREIGN KEY (`cod_cobros`) REFERENCES `cobros` (`cod_cobros`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `deta_factura_deta_cobros_fk` FOREIGN KEY (`cod_factura`,`cod_vehiculo`) REFERENCES `deta_factura` (`cod_factura`, `cod_vehiculo`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `factura_deta_cobros_fk` FOREIGN KEY (`cod_factura`) REFERENCES `factura` (`cod_factura`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Filtros para la tabla `deta_cred_compra`
--
ALTER TABLE `deta_cred_compra`
  ADD CONSTRAINT `insumos_deta_cred_compra_fk` FOREIGN KEY (`cod_insumos`) REFERENCES `insumos` (`cod_insumos`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `nota_credito_compra_deta_cred_compra_fk` FOREIGN KEY (`cod_cred_compra`) REFERENCES `nota_compra` (`cod_nota_compra`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Filtros para la tabla `deta_debito_compra`
--
ALTER TABLE `deta_debito_compra`
  ADD CONSTRAINT `insumos_deta_debito_compra_fk` FOREIGN KEY (`cod_insumos`) REFERENCES `insumos` (`cod_insumos`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `nota_debito_compra_deta_debito_compra_fk` FOREIGN KEY (`cod_debi_compra`) REFERENCES `nota_debito_compra` (`cod_debi_compra`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Filtros para la tabla `deta_pedido_venta`
--
ALTER TABLE `deta_pedido_venta`
  ADD CONSTRAINT `pedido_deta_pedido_venta_fk` FOREIGN KEY (`cod_pedido`) REFERENCES `pedido` (`cod_pedido`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Filtros para la tabla `det_nota_compra`
--
ALTER TABLE `det_nota_compra`
  ADD CONSTRAINT `insumos_det_nota_compra_fk` FOREIGN KEY (`cod_insumos`) REFERENCES `insumos` (`cod_insumos`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `nota_remision_compra_det_nota_compra_fk` FOREIGN KEY (`cod_remision_comp`) REFERENCES `nota_remision_compra` (`cod_remision_comp`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Filtros para la tabla `det_orden`
--
ALTER TABLE `det_orden`
  ADD CONSTRAINT `producto_det_orden_fk` FOREIGN KEY (`cod_producto`) REFERENCES `producto` (`cod_producto`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `orden_compra_det_orden_fk` FOREIGN KEY (`cod_orden`) REFERENCES `orden_compra` (`cod_orden`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Filtros para la tabla `det_pedido_compra`
--
ALTER TABLE `det_pedido_compra`
  ADD CONSTRAINT `insumos_det_pedido_compra_fk` FOREIGN KEY (`cod_insumos`) REFERENCES `insumos` (`cod_insumos`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `pedido_compra_det_pedido_compra_fk` FOREIGN KEY (`cod_pedido_compra`) REFERENCES `pedido_compra` (`cod_pedido_compra`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Filtros para la tabla `det_presupuesto`
--
ALTER TABLE `det_presupuesto`
  ADD CONSTRAINT `insumos_det_presupuesto_fk` FOREIGN KEY (`cod_insumos`) REFERENCES `insumos` (`cod_insumos`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `presupuesto_det_presupuesto_fk` FOREIGN KEY (`cod_presupuesto`) REFERENCES `presupuesto` (`cod_presupuesto`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Filtros para la tabla `det_solicitud`
--
ALTER TABLE `det_solicitud`
  ADD CONSTRAINT `equipo_det_solicitud_fk` FOREIGN KEY (`cod_equipo`) REFERENCES `equipo` (`cod_equipo`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `solicitud_servicio_det_solicitud_fk1` FOREIGN KEY (`cod_servicio_solicitud`) REFERENCES `solicitud_servicio` (`cod_servicio_solicitud`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Filtros para la tabla `det_titular`
--
ALTER TABLE `det_titular`
  ADD CONSTRAINT `titular_det_titular_fk` FOREIGN KEY (`cod_titular`) REFERENCES `titular` (`cod_titular`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Filtros para la tabla `diagnosticos_detalle`
--
ALTER TABLE `diagnosticos_detalle`
  ADD CONSTRAINT `diagnosticos_detalle_id_insumo_fkey` FOREIGN KEY (`id_insumo`) REFERENCES `insumos` (`cod_insumos`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `diagnosticos_detalle_id_tipo_servicio_fkey` FOREIGN KEY (`id_tipo_servicio`) REFERENCES `servicios` (`cod_tiposervicios`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Filtros para la tabla `equipo`
--
ALTER TABLE `equipo`
  ADD CONSTRAINT `color_vehiculo_fk` FOREIGN KEY (`id_color`) REFERENCES `color` (`cod_color`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `tipo_vehiculo_producto_vehiculo_fk` FOREIGN KEY (`id_tipo_equipo`) REFERENCES `tipo_equipo` (`cod_tipo`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Filtros para la tabla `factura`
--
ALTER TABLE `factura`
  ADD CONSTRAINT `cliente_factura_fk` FOREIGN KEY (`cod_cliente`) REFERENCES `cliente_1` (`cod_cliente`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `timbrado_fk` FOREIGN KEY (`nro_timbrado`) REFERENCES `timbrado` (`nro_timbrado`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Filtros para la tabla `funcionario`
--
ALTER TABLE `funcionario`
  ADD CONSTRAINT `cargo_funcionario_fk` FOREIGN KEY (`cod_cargo`) REFERENCES `cargo` (`cod_cargo`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Filtros para la tabla `insumos`
--
ALTER TABLE `insumos`
  ADD CONSTRAINT `marca_insumos_insumos_fk` FOREIGN KEY (`cod_marca_insumos`) REFERENCES `marca_insumos` (`cod_marca_insumos`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Filtros para la tabla `nota_compra`
--
ALTER TABLE `nota_compra`
  ADD CONSTRAINT `registro_compra_nota_credito_compra_fk` FOREIGN KEY (`cod_registro`) REFERENCES `compra` (`cod_registro`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Filtros para la tabla `nota_debito_compra`
--
ALTER TABLE `nota_debito_compra`
  ADD CONSTRAINT `registro_compra_nota_debito_compra_fk` FOREIGN KEY (`cod_registro`) REFERENCES `compra` (`cod_registro`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Filtros para la tabla `nota_remision`
--
ALTER TABLE `nota_remision`
  ADD CONSTRAINT `fk_cliente_1` FOREIGN KEY (`cod_cliente`) REFERENCES `cliente_1` (`cod_cliente`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Filtros para la tabla `nota_remision_compra`
--
ALTER TABLE `nota_remision_compra`
  ADD CONSTRAINT `registro_compra_nota_remision_compra_fk` FOREIGN KEY (`cod_registro`) REFERENCES `compra` (`cod_registro`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Filtros para la tabla `orden_compra`
--
ALTER TABLE `orden_compra`
  ADD CONSTRAINT `proveedor_orden_compra_fk` FOREIGN KEY (`cod_proveedor`) REFERENCES `proveedor` (`cod_proveedor`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Filtros para la tabla `orden_servicio_cabecera`
--
ALTER TABLE `orden_servicio_cabecera`
  ADD CONSTRAINT `cliente_1_orden_servicio_fk` FOREIGN KEY (`cod_cliente`) REFERENCES `cliente_1` (`cod_cliente`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `presupuesto_servicio_orden_servicio_fk` FOREIGN KEY (`cod_presupuesto_servicio`) REFERENCES `presupuesto_servicio` (`cod_presupuesto_servicio`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Filtros para la tabla `pedido`
--
ALTER TABLE `pedido`
  ADD CONSTRAINT `cliente_pedido_fk` FOREIGN KEY (`cod_cliente`) REFERENCES `cliente_1` (`cod_cliente`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Filtros para la tabla `presupuesto`
--
ALTER TABLE `presupuesto`
  ADD CONSTRAINT `proveedor_presupuesto_fk` FOREIGN KEY (`cod_proveedor`) REFERENCES `proveedor` (`cod_proveedor`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Filtros para la tabla `presupuesto_servicio`
--
ALTER TABLE `presupuesto_servicio`
  ADD CONSTRAINT `cliente_1_presupuesto_servicio_fk` FOREIGN KEY (`cod_cliente`) REFERENCES `cliente_1` (`cod_cliente`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Filtros para la tabla `promociones_cabecera`
--
ALTER TABLE `promociones_cabecera`
  ADD CONSTRAINT `fk_promociones_cabecera_servicios` FOREIGN KEY (`cod_tiposervicios`) REFERENCES `servicios` (`cod_tiposervicios`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Filtros para la tabla `promociones_detalle`
--
ALTER TABLE `promociones_detalle`
  ADD CONSTRAINT `fk__insumos` FOREIGN KEY (`id_insumo`) REFERENCES `insumos` (`cod_insumos`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk__promociones_cabecera` FOREIGN KEY (`id_promocion`) REFERENCES `promociones_cabecera` (`cod_promocion`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Filtros para la tabla `proveedor`
--
ALTER TABLE `proveedor`
  ADD CONSTRAINT `ciudad_proveedor_fk` FOREIGN KEY (`cod_ciudad`) REFERENCES `ciudad` (`cod_ciudad`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Filtros para la tabla `reclamos_clientes`
--
ALTER TABLE `reclamos_clientes`
  ADD CONSTRAINT `cliente_reclamos_clientes_fk` FOREIGN KEY (`cod_cliente`) REFERENCES `cliente_1` (`cod_cliente`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `orden_servicio_reclamos_clientes_fk` FOREIGN KEY (`cod_orden_ser`) REFERENCES `orden_servicio_cabecera` (`cod_orden_ser`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Filtros para la tabla `registro_insumos_utilizados`
--
ALTER TABLE `registro_insumos_utilizados`
  ADD CONSTRAINT `insumos_registro_insumos_utilizados_fk` FOREIGN KEY (`cod_insumos`) REFERENCES `insumos` (`cod_insumos`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `orden_servicio_registro_insumos_utilizados_fk` FOREIGN KEY (`cod_orden_ser`) REFERENCES `orden_servicio_cabecera` (`cod_orden_ser`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `servicios_registro_insumos_utilizados_fk` FOREIGN KEY (`cod_tiposervicios`) REFERENCES `servicios` (`cod_tiposervicios`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Filtros para la tabla `solicitud_servicio`
--
ALTER TABLE `solicitud_servicio`
  ADD CONSTRAINT `cliente_solicitud_servicio_fk` FOREIGN KEY (`cod_cliente`) REFERENCES `cliente_1` (`cod_cliente`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Filtros para la tabla `stock`
--
ALTER TABLE `stock`
  ADD CONSTRAINT `deposito_stock_fk` FOREIGN KEY (`cod_deposito`) REFERENCES `deposito` (`cod_deposito`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `insumos_stock_fk` FOREIGN KEY (`cod_insumos`) REFERENCES `insumos` (`cod_insumos`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Filtros para la tabla `sucursal`
--
ALTER TABLE `sucursal`
  ADD CONSTRAINT `ciudad_sucursal_fk` FOREIGN KEY (`cod_ciudad`) REFERENCES `ciudad` (`cod_ciudad`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Filtros para la tabla `tarjetas`
--
ALTER TABLE `tarjetas`
  ADD CONSTRAINT `entidad_emisora_tarjetas_fk` FOREIGN KEY (`cod_entidad_emisora`) REFERENCES `entidad_emisora` (`cod_entidad_emisora`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Filtros para la tabla `tipo_cuenta`
--
ALTER TABLE `tipo_cuenta`
  ADD CONSTRAINT `banco_tipo_cuenta_fk` FOREIGN KEY (`cod_banco`) REFERENCES `banco` (`cod_banco`) ON DELETE NO ACTION ON UPDATE NO ACTION;

-- --------------------------------------------------------
--
-- Estructura de tabla para la tabla `proyectos`
--
CREATE TABLE `proyectos` (
  `cod_proyecto` int(11) NOT NULL,
  `nombre` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `descripcion` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `precio` decimal(10,2) NOT NULL,
  `cod_marca` int(11) NOT NULL,
  `estado` varchar(20) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Índices de la tabla `proyectos`
--
ALTER TABLE `proyectos`
  ADD PRIMARY KEY (`cod_proyecto`),
  ADD KEY `proyectos_marca_fk` (`cod_marca`);

--
-- AUTO_INCREMENT de la tabla `proyectos`
--
ALTER TABLE `proyectos`
  MODIFY `cod_proyecto` int(11) NOT NULL AUTO_INCREMENT;

--
-- Filtros para la tabla `proyectos`
--
ALTER TABLE `proyectos`
  ADD CONSTRAINT `proyectos_marca_fk` FOREIGN KEY (`cod_marca`) REFERENCES `marcas` (`cod_marca`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Filtros para la tabla `usuario`
--
ALTER TABLE `usuario`
  ADD CONSTRAINT `funcionario_usuario_fk` FOREIGN KEY (`cod_funcionario`) REFERENCES `funcionario` (`cod_funcionario`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `permiso_usuario_fk` FOREIGN KEY (`cod_permiso`) REFERENCES `permiso` (`cod_permiso`) ON DELETE NO ACTION ON UPDATE NO ACTION;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

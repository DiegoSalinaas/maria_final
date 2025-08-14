-- Tabla: servicios
CREATE TABLE `servicios` (
  `id_servicio` INT NOT NULL AUTO_INCREMENT,
  `id_cliente` INT NOT NULL,
  `ci_cliente` VARCHAR(20),
  `telefono_cliente` VARCHAR(20),
  `email_cliente` VARCHAR(100),
  `fecha_servicio` DATE NOT NULL,
  `tecnico` VARCHAR(100) NOT NULL,
  `estado` VARCHAR(20) NOT NULL,
  `observaciones` TEXT,
  `total` DECIMAL(10,2),
  `created_at` DATETIME,
  PRIMARY KEY (`id_servicio`)
);

-- Tabla: servicio_detalles
CREATE TABLE `servicio_detalles` (
  `id_servicio_detalle` INT NOT NULL AUTO_INCREMENT,
  `id_servicio` INT NOT NULL,
  `tipo_servicio` VARCHAR(100),
  `descripcion` TEXT,
  `producto_relacionado` VARCHAR(100),
  `cantidad` INT,
  `precio_unitario` DECIMAL(10,2),
  `subtotal` DECIMAL(10,2),
  `observaciones` TEXT,
  PRIMARY KEY (`id_servicio_detalle`),
  CONSTRAINT `servicio_detalles_servicio_fk` FOREIGN KEY (`id_servicio`) REFERENCES `servicios` (`id_servicio`) ON DELETE CASCADE
);

-- Tabla: presupuestos (CABECERA)
CREATE TABLE `presupuestos` (
  `id_presupuesto` INT NOT NULL AUTO_INCREMENT,
  `fecha_emision` DATE,
  `fecha_vencimiento` DATE,
  `id_cliente` INT,
  `estado` VARCHAR(20),
  `observaciones` TEXT,
  `subtotal_servicios` DECIMAL(10,2),
  `subtotal_insumos` DECIMAL(10,2),
  `total` DECIMAL(10,2),
  `created_at` DATETIME,
  PRIMARY KEY (`id_presupuesto`)
);

-- Tabla: presupuesto_servicios (DETALLE 1)
CREATE TABLE `presupuesto_servicios` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `id_presupuesto` INT NOT NULL,
  `tipo_servicio` VARCHAR(100),
  `descripcion` TEXT,
  `cantidad` INT,
  `precio_unitario` DECIMAL(10,2),
  `descuento` DECIMAL(10,2),
  `total_linea` DECIMAL(10,2),
  PRIMARY KEY (`id`),
  CONSTRAINT `pres_serv_presupuesto_fk` FOREIGN KEY (`id_presupuesto`) REFERENCES `presupuestos` (`id_presupuesto`) ON DELETE CASCADE
);

-- Tabla: presupuesto_insumos (DETALLE 2)
CREATE TABLE `presupuesto_insumos` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `id_presupuesto` INT NOT NULL,
  `descripcion` TEXT,
  `marca` VARCHAR(100),
  `modelo` VARCHAR(100),
  `cantidad` INT,
  `precio_unitario` DECIMAL(10,2),
  `total_linea` DECIMAL(10,2),
  PRIMARY KEY (`id`),
  CONSTRAINT `pres_ins_presupuesto_fk` FOREIGN KEY (`id_presupuesto`) REFERENCES `presupuestos` (`id_presupuesto`) ON DELETE CASCADE
);

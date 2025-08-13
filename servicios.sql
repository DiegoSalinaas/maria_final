-- Tabla: servicios
CREATE TABLE `servicios` (
  `id_servicio` INT NOT NULL AUTO_INCREMENT,
  `id_cliente` INT NOT NULL,
  `id_equipo` INT NOT NULL,
  `fecha_servicio` DATE NOT NULL,
  `estado` VARCHAR(20) NOT NULL,
  `tecnico` VARCHAR(100) NOT NULL,
  `observaciones` TEXT,
  `total` DECIMAL(10,2),
  `created_at` DATETIME,
  PRIMARY KEY (`id_servicio`)
);

-- Tabla: servicio_detalles
CREATE TABLE `servicio_detalles` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `id_servicio` INT NOT NULL,
  `descripcion` TEXT,
  `costo` DECIMAL(10,2),
  `estado` VARCHAR(20),
  `fecha_realizada` DATE,
  PRIMARY KEY (`id`),
  CONSTRAINT `servicio_detalles_servicio_fk` FOREIGN KEY (`id_servicio`) REFERENCES `servicios` (`id_servicio`) ON DELETE CASCADE
);

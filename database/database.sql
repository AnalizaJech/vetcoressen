-- CREATE DATABASE IF NOT EXISTS vetcoressen CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
-- USE vetcoressen;

-- Desactivar llaves foráneas para poder hacer drop limpio
SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS `kardex_movimientos`;
DROP TABLE IF EXISTS `venta_detalles`;
DROP TABLE IF EXISTS `ventas`;
DROP TABLE IF EXISTS `prescripciones`;
DROP TABLE IF EXISTS `historias_clinicas`;
DROP TABLE IF EXISTS `citas`;
DROP TABLE IF EXISTS `mascotas`;
DROP TABLE IF EXISTS `clientes`;
DROP TABLE IF EXISTS `sessions`;
DROP TABLE IF EXISTS `password_reset_tokens`;
DROP TABLE IF EXISTS `users`;
DROP TABLE IF EXISTS `sucursales`;
DROP TABLE IF EXISTS `clinicas`;
DROP TABLE IF EXISTS `ubigeo_distritos`;
DROP TABLE IF EXISTS `ubigeo_provincias`;
DROP TABLE IF EXISTS `ubigeo_departamentos`;
SET FOREIGN_KEY_CHECKS = 1;

-- 1. Clínicas (Tenant Principal)
CREATE TABLE `clinicas` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `nombre` VARCHAR(255) NOT NULL,
  `ruc` VARCHAR(11) UNIQUE NULL,
  `razon_social` VARCHAR(255) NULL,
  `direccion` VARCHAR(255) NULL,
  `telefono` VARCHAR(20) NULL,
  `email` VARCHAR(255) NULL,
  `logo` VARCHAR(255) NULL,
  `sitio_web` VARCHAR(255) NULL,
  `activo` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  `deleted_at` TIMESTAMP NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. Sucursales
CREATE TABLE `sucursales` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `clinica_id` BIGINT UNSIGNED NOT NULL,
  `nombre` VARCHAR(255) NOT NULL,
  `direccion` VARCHAR(255) NULL,
  `telefono` VARCHAR(20) NULL,
  `email` VARCHAR(255) NULL,
  `codigo_ubigeo` VARCHAR(6) NULL,
  `principal` TINYINT(1) NOT NULL DEFAULT 0,
  `activo` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  `deleted_at` TIMESTAMP NULL DEFAULT NULL,
  CONSTRAINT `fk_sucursales_clinica` FOREIGN KEY (`clinica_id`) REFERENCES `clinicas` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. Ubigeo Departamentos
CREATE TABLE `ubigeo_departamentos` (
  `id` CHAR(2) PRIMARY KEY,
  `nombre` VARCHAR(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. Ubigeo Provincias
CREATE TABLE `ubigeo_provincias` (
  `id` CHAR(4) PRIMARY KEY,
  `nombre` VARCHAR(100) NOT NULL,
  `departamento_id` CHAR(2) NOT NULL,
  CONSTRAINT `fk_provincias_departamento` FOREIGN KEY (`departamento_id`) REFERENCES `ubigeo_departamentos` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 5. Ubigeo Distritos
CREATE TABLE `ubigeo_distritos` (
  `id` CHAR(6) PRIMARY KEY,
  `nombre` VARCHAR(100) NOT NULL,
  `provincia_id` CHAR(4) NOT NULL,
  `departamento_id` CHAR(2) NOT NULL,
  CONSTRAINT `fk_distritos_provincia` FOREIGN KEY (`provincia_id`) REFERENCES `ubigeo_provincias` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_distritos_departamento` FOREIGN KEY (`departamento_id`) REFERENCES `ubigeo_departamentos` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 6. Usuarios
CREATE TABLE `users` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `clinica_id` BIGINT UNSIGNED NULL,
  `sucursal_id` BIGINT UNSIGNED NULL,
  `name` VARCHAR(255) NOT NULL,
  `email` VARCHAR(255) UNIQUE NOT NULL,
  `email_verified_at` TIMESTAMP NULL DEFAULT NULL,
  `password` VARCHAR(255) NOT NULL,
  `telefono` VARCHAR(20) NULL,
  `dni` VARCHAR(8) NULL,
  `avatar` VARCHAR(255) NULL,
  `activo` TINYINT(1) NOT NULL DEFAULT 1,
  `remember_token` VARCHAR(100) NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  `deleted_at` TIMESTAMP NULL DEFAULT NULL,
  CONSTRAINT `fk_users_clinica` FOREIGN KEY (`clinica_id`) REFERENCES `clinicas` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_users_sucursal` FOREIGN KEY (`sucursal_id`) REFERENCES `sucursales` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 7. Clientes
CREATE TABLE `clientes` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `clinica_id` BIGINT UNSIGNED NOT NULL,
  `tipo_documento` ENUM('DNI', 'RUC', 'CE', 'PASAPORTE') NOT NULL DEFAULT 'DNI',
  `numero_documento` VARCHAR(15) NOT NULL,
  `nombres` VARCHAR(100) NOT NULL,
  `apellidos` VARCHAR(100) NULL,
  `email` VARCHAR(100) NULL,
  `telefono` VARCHAR(20) NULL,
  `direccion` VARCHAR(255) NULL,
  `codigo_ubigeo` VARCHAR(6) NULL,
  `distrito_id` CHAR(6) NULL,
  `activo` TINYINT(1) NOT NULL DEFAULT 1,
  `notas` TEXT NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  `deleted_at` TIMESTAMP NULL DEFAULT NULL,
  CONSTRAINT `fk_clientes_clinica` FOREIGN KEY (`clinica_id`) REFERENCES `clinicas` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_clientes_distrito` FOREIGN KEY (`distrito_id`) REFERENCES `ubigeo_distritos` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 8. Mascotas
CREATE TABLE `mascotas` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `clinica_id` BIGINT UNSIGNED NOT NULL,
  `cliente_id` BIGINT UNSIGNED NOT NULL,
  `nombre` VARCHAR(100) NOT NULL,
  `especie` VARCHAR(50) NOT NULL,
  `raza` VARCHAR(100) NULL,
  `sexo` ENUM('M', 'H') NOT NULL COMMENT 'M = Macho, H = Hembra',
  `color` VARCHAR(50) NULL,
  `fecha_nacimiento` DATE NULL,
  `peso_actual` DECIMAL(8,2) NULL COMMENT 'Peso en kg',
  `foto` VARCHAR(255) NULL,
  `esterilizado` TINYINT(1) NOT NULL DEFAULT 0,
  `fallecido` TINYINT(1) NOT NULL DEFAULT 0,
  `notas_medicas` TEXT NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  `deleted_at` TIMESTAMP NULL DEFAULT NULL,
  CONSTRAINT `fk_mascotas_clinica` FOREIGN KEY (`clinica_id`) REFERENCES `clinicas` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_mascotas_cliente` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 9. Citas
CREATE TABLE `citas` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `clinica_id` BIGINT UNSIGNED NOT NULL,
  `cliente_id` BIGINT UNSIGNED NOT NULL,
  `mascota_id` BIGINT UNSIGNED NOT NULL,
  `veterinario_id` BIGINT UNSIGNED NULL,
  `fecha_hora` DATETIME NOT NULL,
  `motivo` VARCHAR(150) NOT NULL,
  `estado` ENUM('PENDIENTE', 'CONFIRMADA', 'EN_PROGRESO', 'COMPLETADA', 'CANCELADA', 'NO_ASISTIO') NOT NULL DEFAULT 'PENDIENTE',
  `notas` TEXT NULL,
  `notificado_sms` TINYINT(1) NOT NULL DEFAULT 0,
  `notificado_whatsapp` TINYINT(1) NOT NULL DEFAULT 0,
  `notificado_email` TINYINT(1) NOT NULL DEFAULT 0,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  `deleted_at` TIMESTAMP NULL DEFAULT NULL,
  CONSTRAINT `fk_citas_clinica` FOREIGN KEY (`clinica_id`) REFERENCES `clinicas` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_citas_cliente` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_citas_mascota` FOREIGN KEY (`mascota_id`) REFERENCES `mascotas` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_citas_veterinario` FOREIGN KEY (`veterinario_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 10. Historias Clínicas
CREATE TABLE `historias_clinicas` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `clinica_id` BIGINT UNSIGNED NOT NULL,
  `mascota_id` BIGINT UNSIGNED NOT NULL,
  `veterinario_id` BIGINT UNSIGNED NULL,
  `cita_id` BIGINT UNSIGNED NULL,
  `fecha` DATETIME NOT NULL,
  `motivo_consulta` VARCHAR(255) NOT NULL,
  `peso` DECIMAL(8,2) NULL,
  `temperatura` DECIMAL(4,1) NULL,
  `frecuencia_cardiaca` INT NULL,
  `frecuencia_respiratoria` INT NULL,
  `anamnesis` TEXT NULL,
  `diagnostico_presuntivo` TEXT NULL,
  `tratamiento_indicaciones` TEXT NULL,
  `proxima_cita_recomendada` DATE NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  `deleted_at` TIMESTAMP NULL DEFAULT NULL,
  CONSTRAINT `fk_historias_clinica` FOREIGN KEY (`clinica_id`) REFERENCES `clinicas` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_historias_mascota` FOREIGN KEY (`mascota_id`) REFERENCES `mascotas` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_historias_veterinario` FOREIGN KEY (`veterinario_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_historias_cita` FOREIGN KEY (`cita_id`) REFERENCES `citas` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 11. Productos y Servicios
CREATE TABLE `productos` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `clinica_id` BIGINT UNSIGNED NOT NULL,
  `tipo` ENUM('PRODUCTO', 'SERVICIO') NOT NULL DEFAULT 'PRODUCTO',
  `categoria` VARCHAR(100) NULL,
  `nombre` VARCHAR(150) NOT NULL,
  `codigo_barras` VARCHAR(100) NULL,
  `precio_venta` DECIMAL(10,2) NOT NULL,
  `costo_compra` DECIMAL(10,2) NULL,
  `afecto_igv` TINYINT(1) NOT NULL DEFAULT 1,
  `stock_actual` INT NOT NULL DEFAULT 0,
  `stock_minimo` INT NOT NULL DEFAULT 0,
  `activo` TINYINT(1) NOT NULL DEFAULT 1,
  `notas` TEXT NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  `deleted_at` TIMESTAMP NULL DEFAULT NULL,
  CONSTRAINT `fk_productos_clinica` FOREIGN KEY (`clinica_id`) REFERENCES `clinicas` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 12. Prescripciones Médicas
CREATE TABLE `prescripciones` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `clinica_id` BIGINT UNSIGNED NOT NULL,
  `historia_clinica_id` BIGINT UNSIGNED NOT NULL,
  `producto_id` BIGINT UNSIGNED NULL,
  `medicamento` VARCHAR(150) NOT NULL,
  `dosis` VARCHAR(100) NOT NULL,
  `frecuencia` VARCHAR(100) NULL,
  `duracion` VARCHAR(100) NULL,
  `via_administracion` VARCHAR(50) NOT NULL DEFAULT 'ORAL',
  `duracion_dias` INT NULL,
  `indicaciones` TEXT NULL,
  `cantidad_dispensada` INT NOT NULL DEFAULT 0,
  `dispensado` TINYINT(1) NOT NULL DEFAULT 0,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  CONSTRAINT `fk_prescripciones_clinica` FOREIGN KEY (`clinica_id`) REFERENCES `clinicas` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_prescripciones_historia` FOREIGN KEY (`historia_clinica_id`) REFERENCES `historias_clinicas` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_prescripciones_producto` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 13. Ventas (Facturación)
CREATE TABLE `ventas` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `clinica_id` BIGINT UNSIGNED NOT NULL,
  `cliente_id` BIGINT UNSIGNED NULL,
  `cajero_id` BIGINT UNSIGNED NOT NULL,
  `tipo_comprobante` ENUM('TICKET', 'BOLETA', 'FACTURA') NOT NULL DEFAULT 'TICKET',
  `subtotal` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `igv` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `total` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `metodo_pago` ENUM('EFECTIVO', 'TARJETA', 'TRANSFERENCIA', 'YAPE_PLIN') NOT NULL DEFAULT 'EFECTIVO',
  `estado` ENUM('PAGADO', 'ANULADO') NOT NULL DEFAULT 'PAGADO',
  `notas` TEXT NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  `deleted_at` TIMESTAMP NULL DEFAULT NULL,
  CONSTRAINT `fk_ventas_clinica` FOREIGN KEY (`clinica_id`) REFERENCES `clinicas` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_ventas_cliente` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_ventas_cajero` FOREIGN KEY (`cajero_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 14. Detalle de Ventas
CREATE TABLE `venta_detalles` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `venta_id` BIGINT UNSIGNED NOT NULL,
  `producto_id` BIGINT UNSIGNED NULL,
  `descripcion` VARCHAR(150) NOT NULL,
  `cantidad` INT NOT NULL DEFAULT 1,
  `precio_unitario` DECIMAL(10,2) NOT NULL,
  `afecto_igv` TINYINT(1) NOT NULL DEFAULT 1,
  `subtotal` DECIMAL(10,2) NOT NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  CONSTRAINT `fk_detalles_venta` FOREIGN KEY (`venta_id`) REFERENCES `ventas` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_detalles_producto` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 15. Kardex Movimientos (Inventario Inmutable)
CREATE TABLE `kardex_movimientos` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `clinica_id` BIGINT UNSIGNED NOT NULL,
  `producto_id` BIGINT UNSIGNED NOT NULL,
  `usuario_id` BIGINT UNSIGNED NOT NULL,
  `tipo` ENUM('ENTRADA_COMPRA', 'ENTRADA_AJUSTE', 'SALIDA_VENTA', 'SALIDA_DISPENSACION', 'SALIDA_AJUSTE') NOT NULL,
  `cantidad` INT NOT NULL COMMENT 'Positivo entradas, Negativo salidas',
  `costo_unitario` DECIMAL(10,2) NULL,
  `lote` VARCHAR(50) NULL,
  `fecha_vencimiento` DATE NULL,
  `documento_referencia` VARCHAR(50) NULL,
  `stock_anterior` INT NOT NULL,
  `stock_posterior` INT NOT NULL,
  `referencia_tipo` VARCHAR(50) NULL,
  `referencia_id` BIGINT UNSIGNED NULL,
  `notas` TEXT NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  CONSTRAINT `fk_kardex_clinica` FOREIGN KEY (`clinica_id`) REFERENCES `clinicas` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_kardex_producto` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_kardex_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

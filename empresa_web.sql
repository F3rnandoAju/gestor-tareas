-- importar en phpMyAdmin (exportar/ejecutar)
CREATE DATABASE IF NOT EXISTS empresa_web CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE empresa_web;

CREATE TABLE IF NOT EXISTS tareas (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  titulo VARCHAR(191) NOT NULL,
  descripcion TEXT,
  estado ENUM('pendiente','en progreso','completada') NOT NULL DEFAULT 'pendiente',
  fecha_limite DATE NULL,
  creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  actualizado_en TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Carwash seed data for local / Docker development
-- Database: carwash (see 01-schema.sql)
--
-- После импорта схемы:
--   php yii utility/init-rbac
--   php yii migrate --interactive=0

USE `carwash`;

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

SET FOREIGN_KEY_CHECKS = 1;

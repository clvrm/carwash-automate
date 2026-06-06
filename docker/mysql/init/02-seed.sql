-- Carwash seed data for local / Docker development
-- Database: carwash (see 01-schema.sql)

USE `carwash`;

-- =============================================================================
-- RBAC (roles & permissions)
-- =============================================================================
-- Roles and permissions are NOT seeded here.
-- After the database is up, initialize RBAC from the Yii console:
--
--   php yii utility/init-rbac
--
-- This creates roles: owner, manager, admin, washer
-- and permissions (perm_edit_pricelist, perm_create_edit_orders, etc.)
-- with owner as the parent of all permissions.
--
-- The auth_assignment INSERT at the bottom of this file requires the `owner`
-- role to exist (created by init-rbac). Run init-rbac before applying it, or
-- include it in your container entrypoint after schema + base seed.
-- =============================================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- -----------------------------------------------------------------------------
-- Demo user (password: demo123)
-- bcrypt hash generated with Yii2/password_hash (cost 13)
-- -----------------------------------------------------------------------------
INSERT INTO `users` (
  `id`, `guid`, `auth_token`, `status`,
  `firstname`, `lastname`, `patronymic`, `avatar`, `phone`, `phone_verified`,
  `email`, `password_hash`, `lang_id`, `updated_at`, `created_at`
) VALUES (
  1,
  'a1b2c3d4-e5f6-7890-abcd-ef1234567890',
  NULL,
  2,
  'Demo',
  'User',
  NULL,
  NULL,
  NULL,
  b'0',
  'demo@carwash.local',
  '$2y$13$YZuD8Zqx2QLK87aVwxXjjOfjb8//pUL5dI/FfS5EqHn5feaW8tDnG',
  1,
  CURRENT_TIMESTAMP,
  CURRENT_TIMESTAMP
);

-- -----------------------------------------------------------------------------
-- Demo carwash
-- -----------------------------------------------------------------------------
INSERT INTO `carwash` (
  `id`, `owner_id`, `city_id`, `name`, `avatar`, `address`,
  `timezone`, `geo_lat`, `geo_long`, `created_at`
) VALUES (
  1,
  NULL,
  NULL,
  'Demo Carwash',
  NULL,
  'Demo Street 1',
  3,
  NULL,
  NULL,
  CURRENT_TIMESTAMP
);

-- -----------------------------------------------------------------------------
-- Demo personal (owner)
-- -----------------------------------------------------------------------------
INSERT INTO `personal` (
  `id`, `user_id`, `carwash_id`, `firebase_token`,
  `is_approved`, `post`, `salary_type`, `salary`,
  `updated_at`, `created_at`
) VALUES (
  1,
  1,
  1,
  NULL,
  1,
  10,
  0,
  NULL,
  CURRENT_TIMESTAMP,
  CURRENT_TIMESTAMP
);

UPDATE `carwash` SET `owner_id` = 1 WHERE `id` = 1;

-- Default carwash settings (mirrors Carwash::createDefaultSettings())
INSERT INTO `carwash_settings` (
  `id`, `carwash_id`, `post_count`, `online_record`, `only_subscribers`,
  `subscriber_code`, `can_record_blacklist`, `checkout_time`, `dense_record`,
  `max_recording_range`, `average_duration`, `until_last_client`,
  `staff_delay_time`, `service_time_multiplier`, `updated_at`, `created_at`
) VALUES (
  1, 1, 3, 1, 0, 'AV0000001', 1, 0, 0, 7, 0, 0, 0, 0,
  CURRENT_TIMESTAMP, CURRENT_TIMESTAMP
);

INSERT INTO `carwash_schedule` (
  `id`, `carwash_id`,
  `monday_start`, `monday_end`, `tuesday_start`, `tuesday_end`,
  `wednesday_start`, `wednesday_end`, `thursday_start`, `thursday_end`,
  `friday_start`, `friday_end`, `saturday_start`, `saturday_end`,
  `sunday_start`, `sunday_end`,
  `is_work_monday`, `is_work_tuesday`, `is_work_wednesday`, `is_work_thursday`,
  `is_work_friday`, `is_work_saturday`, `is_work_sunday`,
  `updated_at`, `created_at`
) VALUES (
  1, 1,
  '09:00:00', '21:00:00', '09:00:00', '21:00:00',
  '09:00:00', '21:00:00', '09:00:00', '21:00:00',
  '09:00:00', '21:00:00', '09:00:00', '21:00:00',
  '09:00:00', '21:00:00',
  b'1', b'1', b'1', b'1', b'1', b'1', b'1',
  CURRENT_TIMESTAMP, CURRENT_TIMESTAMP
);

INSERT INTO `carwash_comfort` (
  `id`, `carwash_id`,
  `pay_cash`, `pay_online`, `pay_terminal`, `pay_invoice`,
  `cf_ATM`, `cf_postomat`, `cf_cafe`, `cf_toilet`, `cf_shop`,
  `cf_rest_zone`, `cf_coffee`, `cf_TV`, `cf_videocam`, `created_at`
) VALUES (
  1, 1,
  b'1', b'0', b'0', b'0',
  b'0', b'0', b'0', b'1', b'0',
  b'0', b'0', b'0', b'0', CURRENT_TIMESTAMP
);

-- -----------------------------------------------------------------------------
-- Car brands & models (minimal reference data)
-- -----------------------------------------------------------------------------
INSERT INTO `car_brands` (`id`, `carwash_id`, `title`, `icon`, `synonyms`, `created_at`) VALUES
(1, NULL, 'Toyota', NULL, NULL, CURRENT_TIMESTAMP),
(2, NULL, 'BMW', NULL, NULL, CURRENT_TIMESTAMP),
(3, NULL, 'LADA (ВАЗ)', NULL, NULL, CURRENT_TIMESTAMP);

INSERT INTO `car_models` (`id`, `car_brand_id`, `carwash_id`, `title`, `synonyms`, `created_at`) VALUES
(1, 1, NULL, 'Camry', NULL, CURRENT_TIMESTAMP),
(2, 1, NULL, 'Corolla', NULL, CURRENT_TIMESTAMP),
(3, 2, NULL, '3 серии', NULL, CURRENT_TIMESTAMP),
(4, 2, NULL, 'X5', NULL, CURRENT_TIMESTAMP),
(5, 3, NULL, 'Granta', NULL, CURRENT_TIMESTAMP),
(6, 3, NULL, 'Vesta', NULL, CURRENT_TIMESTAMP);

-- -----------------------------------------------------------------------------
-- Owner role assignment
-- Requires `owner` in auth_item. Run `php yii utility/init-rbac` for the full
-- role/permission tree; the bootstrap row below lets docker init apply
-- auth_assignment before the first console run (init-rbac skips existing roles).
-- -----------------------------------------------------------------------------
INSERT IGNORE INTO `auth_item` (`name`, `type`, `description`, `created_at`, `updated_at`)
VALUES ('owner', 1, 'Владелец автомойки', UNIX_TIMESTAMP(), UNIX_TIMESTAMP());

INSERT INTO `auth_assignment` (`item_name`, `personal_id`, `created_at`)
VALUES ('owner', 1, UNIX_TIMESTAMP());

SET FOREIGN_KEY_CHECKS = 1;

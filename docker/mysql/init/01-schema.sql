-- Carwash Yii2 schema (MySQL 8)
-- Generated from models/ar/ ActiveRecord definitions
-- 39 tables covering all application AR models

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

CREATE DATABASE IF NOT EXISTS `carwash` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `carwash`;

-- Table: auth_rule
CREATE TABLE `auth_rule` (
  `name` varchar(64) NOT NULL,
  `data` blob DEFAULT NULL,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: auth_item
CREATE TABLE `auth_item` (
  `name` varchar(64) NOT NULL,
  `type` smallint(6) NOT NULL,
  `description` text DEFAULT NULL,
  `rule_name` varchar(64) DEFAULT NULL,
  `data` blob DEFAULT NULL,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: auth_item_child
CREATE TABLE `auth_item_child` (
  `parent` varchar(64) NOT NULL,
  `child` varchar(64) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: auth_assignment
CREATE TABLE `auth_assignment` (
  `item_name` varchar(64) NOT NULL,
  `personal_id` int(11) NOT NULL,
  `created_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: users
CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `guid` varchar(64) NOT NULL,
  `auth_token` varchar(48) DEFAULT NULL,
  `status` int(11) NOT NULL DEFAULT 0,
  `firstname` varchar(128) DEFAULT NULL,
  `lastname` varchar(128) DEFAULT NULL,
  `patronymic` varchar(128) DEFAULT NULL,
  `avatar` varchar(300) DEFAULT NULL,
  `phone` varchar(32) DEFAULT NULL,
  `phone_verified` bit(1) DEFAULT b'0',
  `email` varchar(64) DEFAULT NULL,
  `password_hash` varchar(255) DEFAULT NULL COMMENT 'bcrypt hash (Yii2 security)',
  `lang_id` int(11) DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: cities
CREATE TABLE `cities` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: carwash
CREATE TABLE `carwash` (
  `id` int(11) NOT NULL,
  `owner_id` int(11) DEFAULT NULL,
  `city_id` int(11) DEFAULT NULL,
  `name` varchar(128) NOT NULL,
  `avatar` varchar(512) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `timezone` tinyint(3) UNSIGNED DEFAULT NULL,
  `geo_lat` varchar(16) DEFAULT NULL,
  `geo_long` varchar(16) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: personal
CREATE TABLE `personal` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `carwash_id` int(11) NOT NULL,
  `firebase_token` varchar(256) DEFAULT NULL COMMENT 'Токен для отправки уведомлений',
  `is_approved` tinyint(3) UNSIGNED NOT NULL DEFAULT 0,
  `post` tinyint(3) UNSIGNED DEFAULT NULL,
  `salary_type` tinyint(3) UNSIGNED DEFAULT NULL,
  `salary` decimal(10,2) DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: carwash_settings
CREATE TABLE `carwash_settings` (
  `id` int(11) NOT NULL,
  `carwash_id` int(11) NOT NULL,
  `post_count` tinyint(4) DEFAULT NULL COMMENT 'Количество постов',
  `online_record` tinyint(4) DEFAULT NULL COMMENT 'Онлайн запись',
  `only_subscribers` tinyint(4) DEFAULT NULL COMMENT 'Только для подписчиков',
  `subscriber_code` varchar(32) DEFAULT NULL COMMENT 'Код для подписчиков',
  `can_record_blacklist` tinyint(4) DEFAULT NULL COMMENT 'Запись клиентов из черного списка',
  `checkout_time` tinyint(4) DEFAULT NULL COMMENT 'Время въезда-выезда',
  `dense_record` tinyint(4) DEFAULT NULL COMMENT 'Плотная запись',
  `max_recording_range` tinyint(4) DEFAULT 7 COMMENT 'Максимальное количество дней для записи наперед',
  `average_duration` tinyint(4) DEFAULT NULL COMMENT 'Средняя длительность',
  `until_last_client` tinyint(4) DEFAULT NULL COMMENT 'До последнего клиента',
  `staff_delay_time` tinyint(4) DEFAULT NULL COMMENT 'Время задержки персонала',
  `service_time_multiplier` tinyint(4) DEFAULT NULL COMMENT 'Множитель времени услуг',
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: carwash_schedule
CREATE TABLE `carwash_schedule` (
  `id` int(11) NOT NULL,
  `carwash_id` int(11) DEFAULT NULL,
  `monday_start` time DEFAULT NULL,
  `monday_end` time DEFAULT NULL,
  `tuesday_start` time DEFAULT NULL,
  `tuesday_end` time DEFAULT NULL,
  `wednesday_start` time DEFAULT NULL,
  `wednesday_end` time DEFAULT NULL,
  `thursday_start` time DEFAULT NULL,
  `thursday_end` time DEFAULT NULL,
  `friday_start` time DEFAULT NULL,
  `friday_end` time DEFAULT NULL,
  `saturday_start` time DEFAULT NULL,
  `saturday_end` time DEFAULT NULL,
  `sunday_start` time DEFAULT NULL,
  `sunday_end` time DEFAULT NULL,
  `is_work_monday` bit(1) DEFAULT NULL,
  `is_work_tuesday` bit(1) DEFAULT NULL,
  `is_work_wednesday` bit(1) DEFAULT NULL,
  `is_work_thursday` bit(1) DEFAULT NULL,
  `is_work_friday` bit(1) DEFAULT NULL,
  `is_work_saturday` bit(1) DEFAULT NULL,
  `is_work_sunday` bit(1) DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: carwash_comfort
CREATE TABLE `carwash_comfort` (
  `id` int(11) NOT NULL,
  `carwash_id` int(11) NOT NULL,
  `pay_cash` bit(1) DEFAULT b'0',
  `pay_online` bit(1) DEFAULT b'0',
  `pay_terminal` bit(1) DEFAULT b'0',
  `pay_invoice` bit(1) DEFAULT b'0',
  `cf_ATM` bit(1) DEFAULT b'0',
  `cf_postomat` bit(1) DEFAULT b'0',
  `cf_cafe` bit(1) DEFAULT b'0',
  `cf_toilet` bit(1) DEFAULT b'0',
  `cf_shop` bit(1) DEFAULT b'0',
  `cf_rest_zone` bit(1) DEFAULT b'0',
  `cf_coffee` bit(1) DEFAULT b'0',
  `cf_TV` bit(1) DEFAULT b'0',
  `cf_videocam` bit(1) DEFAULT b'0',
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: carwash_contacts
CREATE TABLE `carwash_contacts` (
  `id` int(11) NOT NULL,
  `carwash_id` int(11) NOT NULL,
  `phone_1` varchar(32) DEFAULT NULL,
  `phone_2` varchar(32) DEFAULT NULL,
  `phone_3` varchar(32) DEFAULT NULL,
  `site` varchar(128) DEFAULT NULL,
  `email` varchar(128) DEFAULT NULL,
  `vk` varchar(64) DEFAULT NULL,
  `facebook` varchar(64) DEFAULT NULL,
  `instagram` varchar(64) DEFAULT NULL,
  `telegram` varchar(64) DEFAULT NULL,
  `whatsapp` varchar(64) DEFAULT NULL,
  `viber` varchar(64) DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: car_brands
CREATE TABLE `car_brands` (
  `id` int(11) NOT NULL,
  `carwash_id` int(11) DEFAULT NULL,
  `title` varchar(128) NOT NULL,
  `icon` varchar(255) DEFAULT NULL,
  `synonyms` varchar(1024) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: car_models
CREATE TABLE `car_models` (
  `id` int(11) NOT NULL,
  `car_brand_id` int(11) NOT NULL,
  `carwash_id` int(11) DEFAULT NULL,
  `title` varchar(128) NOT NULL,
  `synonyms` varchar(1024) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: carwash_blacklist
CREATE TABLE `carwash_blacklist` (
  `id` int(11) NOT NULL,
  `carwash_id` int(11) NOT NULL,
  `client_id` int(11) NOT NULL,
  `car_number` varchar(16) NOT NULL,
  `car_region` varchar(8) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: carwash_images
CREATE TABLE `carwash_images` (
  `id` int(11) NOT NULL,
  `carwash_id` int(11) NOT NULL,
  `image` varchar(512) NOT NULL,
  `alt` varchar(255) DEFAULT NULL,
  `position` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: carwash_sales
CREATE TABLE `carwash_sales` (
  `id` int(11) NOT NULL,
  `carwash_id` int(11) NOT NULL,
  `name` varchar(128) NOT NULL,
  `description` varchar(512) DEFAULT NULL,
  `start_at` date NOT NULL,
  `end_at` date NOT NULL,
  `only_subscribers` tinyint(4) DEFAULT NULL COMMENT 'Для подписчиков / всех',
  `for_service_type` tinyint(4) DEFAULT NULL COMMENT 'комплекс / услуга',
  `sale_type` tinyint(4) DEFAULT NULL COMMENT 'Процент / скидка',
  `sale` smallint(5) UNSIGNED DEFAULT NULL,
  `rounding_to` smallint(6) DEFAULT NULL COMMENT 'Округлять до? Нет / 10 / 100',
  `sum_up_discount` tinyint(4) DEFAULT NULL COMMENT 'Суммировать скидку',
  `apply_greater` tinyint(4) DEFAULT NULL COMMENT 'Применять бо''льшую скидку?',
  `position` int(11) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: carwash_sales_item
CREATE TABLE `carwash_sales_item` (
  `id` int(11) NOT NULL,
  `sale_id` int(11) NOT NULL,
  `complex_id` int(11) DEFAULT NULL,
  `service_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: advertising
CREATE TABLE `advertising` (
  `id` int(11) NOT NULL,
  `carwash_id` int(11) NOT NULL,
  `type` varchar(16) NOT NULL DEFAULT 'client-lk',
  `site` varchar(128) DEFAULT NULL,
  `phone` varchar(32) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `text` text DEFAULT NULL,
  `banner` varchar(255) DEFAULT NULL,
  `status` int(11) NOT NULL DEFAULT 0,
  `views` int(11) NOT NULL DEFAULT 0,
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Рассылка рекламы';

-- Table: clients
CREATE TABLE `clients` (
  `id` int(11) NOT NULL,
  `guid` varchar(64) NOT NULL,
  `carwash_id` int(11) NOT NULL,
  `is_subscribed` bit(1) NOT NULL DEFAULT b'0',
  `full_name` varchar(128) DEFAULT NULL,
  `phone` varchar(32) DEFAULT NULL,
  `email` varchar(128) DEFAULT NULL,
  `reputation` int(11) NOT NULL DEFAULT 0,
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: materials
CREATE TABLE `materials` (
  `id` int(11) NOT NULL,
  `carwash_id` int(11) NOT NULL,
  `is_detailing` tinyint(3) UNSIGNED DEFAULT 0,
  `name` varchar(255) DEFAULT NULL,
  `price` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: services
CREATE TABLE `services` (
  `id` int(11) NOT NULL,
  `carwash_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `is_detailing` bit(1) NOT NULL DEFAULT b'0',
  `type_1_price` int(11) DEFAULT NULL,
  `type_1_time` int(11) DEFAULT NULL,
  `type_2_price` int(11) DEFAULT NULL,
  `type_2_time` int(11) DEFAULT NULL,
  `type_3_price` int(11) DEFAULT NULL,
  `type_3_time` int(11) DEFAULT NULL,
  `type_4_price` int(11) DEFAULT NULL,
  `type_4_time` int(11) DEFAULT NULL,
  `type_5_price` int(11) DEFAULT NULL,
  `type_5_time` int(11) DEFAULT NULL,
  `position` int(11) NOT NULL DEFAULT 1,
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Услуги';

-- Table: service_materials
CREATE TABLE `service_materials` (
  `id` int(11) NOT NULL,
  `service_id` int(11) NOT NULL,
  `material_id` int(11) NOT NULL,
  `price` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: complexes
CREATE TABLE `complexes` (
  `id` int(11) NOT NULL,
  `carwash_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `is_detailing` bit(1) NOT NULL DEFAULT b'0',
  `type_1_price` int(11) DEFAULT NULL,
  `type_1_time` int(11) DEFAULT NULL,
  `type_2_price` int(11) DEFAULT NULL,
  `type_2_time` int(11) DEFAULT NULL,
  `type_3_price` int(11) DEFAULT NULL,
  `type_3_time` int(11) DEFAULT NULL,
  `type_4_price` int(11) DEFAULT NULL,
  `type_4_time` int(11) DEFAULT NULL,
  `type_5_price` int(11) DEFAULT NULL,
  `type_5_time` int(11) DEFAULT NULL,
  `position` int(11) NOT NULL DEFAULT 1,
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Комплексы';

-- Table: complex_services
CREATE TABLE `complex_services` (
  `id` int(11) NOT NULL,
  `complex_id` int(11) DEFAULT NULL,
  `service_id` int(11) DEFAULT NULL,
  `position` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: complex_materials
CREATE TABLE `complex_materials` (
  `id` int(11) NOT NULL,
  `complex_id` int(11) NOT NULL,
  `material_id` int(11) NOT NULL,
  `price` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: orders
CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `carwash_id` int(11) NOT NULL,
  `client_id` int(11) DEFAULT NULL,
  `personal_id` int(11) DEFAULT NULL COMMENT 'Менеджер',
  `personal_fullname` varchar(255) DEFAULT NULL COMMENT 'После архивирования заказа, указываем фио',
  `date` date NOT NULL,
  `post` tinyint(3) UNSIGNED DEFAULT NULL,
  `start_time` smallint(6) DEFAULT NULL,
  `end_time` smallint(6) DEFAULT NULL,
  `car_type` int(11) DEFAULT NULL,
  `car_number` varchar(50) DEFAULT NULL,
  `car_region` smallint(6) DEFAULT NULL,
  `color` varchar(64) DEFAULT NULL,
  `car_brand_id` int(11) DEFAULT NULL,
  `car_model_id` int(11) DEFAULT NULL,
  `client_fullname` varchar(128) DEFAULT NULL,
  `client_phone` varchar(32) DEFAULT NULL,
  `total_price` int(11) DEFAULT NULL,
  `sale` int(11) DEFAULT NULL,
  `work_time` int(11) DEFAULT NULL,
  `status` int(11) DEFAULT NULL,
  `admin_comment` text DEFAULT NULL,
  `client_comment` text DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: order_service
CREATE TABLE `order_service` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `order_id` int(11) NOT NULL,
  `entity_id` int(11) DEFAULT NULL COMMENT 'id услуги / комплекса / материала исходя из типа',
  `is_detailing` bit(1) DEFAULT b'0',
  `type` tinyint(4) DEFAULT NULL COMMENT 'Услуга / комплекс / материалы',
  `name` varchar(255) NOT NULL,
  `price` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: chat
CREATE TABLE `chat` (
  `id` int(11) NOT NULL,
  `carwash_id` int(11) DEFAULT NULL,
  `client_id` int(11) DEFAULT NULL,
  `order_id` int(11) DEFAULT NULL,
  `car_number` varchar(50) DEFAULT NULL,
  `car_region` varchar(50) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: chat_messages
CREATE TABLE `chat_messages` (
  `id` int(11) NOT NULL,
  `chat_id` int(11) NOT NULL,
  `client_id` int(11) DEFAULT NULL,
  `personal_id` int(11) DEFAULT NULL,
  `is_viewed` bit(1) NOT NULL DEFAULT b'0',
  `text` text NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: tickets
CREATE TABLE `tickets` (
  `id` int(11) NOT NULL,
  `carwash_id` int(11) NOT NULL,
  `personal_id` int(11) DEFAULT NULL,
  `text` varchar(8000) NOT NULL,
  `is_closed` bit(1) NOT NULL DEFAULT b'0',
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: ticket_messages
CREATE TABLE `ticket_messages` (
  `id` int(11) NOT NULL,
  `ticket_id` int(11) NOT NULL,
  `personal_id` int(11) DEFAULT NULL,
  `admin_id` int(11) DEFAULT NULL,
  `text` text NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: personal_log
CREATE TABLE `personal_log` (
  `id` bigint(20) NOT NULL,
  `personal_id` int(11) NOT NULL,
  `type` tinyint(4) DEFAULT 1 COMMENT '1 - общее, 2 - личное, 3 - информационное',
  `event` varchar(32) NOT NULL,
  `text` mediumtext DEFAULT NULL,
  `logged_at` timestamp NULL DEFAULT current_timestamp(),
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: personal_notification
CREATE TABLE `personal_notification` (
  `id` int(11) NOT NULL,
  `personal_id` int(11) NOT NULL,
  `type` varchar(32) NOT NULL,
  `key` varchar(128) NOT NULL,
  `value` varchar(4096) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: partner_store
CREATE TABLE `partner_store` (
  `id` int(11) NOT NULL,
  `title` varchar(128) NOT NULL,
  `short_text` varchar(512) DEFAULT NULL,
  `type` varchar(32) NOT NULL COMMENT 'Оборудование / Материалы / Всё',
  `link` varchar(255) NOT NULL,
  `logo` varchar(255) DEFAULT NULL,
  `position` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: partner_store_items
CREATE TABLE `partner_store_items` (
  `id` int(11) NOT NULL,
  `partner_store_id` int(11) NOT NULL,
  `type` varchar(32) NOT NULL COMMENT 'Материал / Оборудование',
  `title` varchar(50) NOT NULL,
  `price` int(11) DEFAULT NULL,
  `link` varchar(50) DEFAULT NULL,
  `position` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: documentation_category
CREATE TABLE `documentation_category` (
  `id` int(11) NOT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `title` varchar(128) DEFAULT NULL,
  `position` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: documentation
CREATE TABLE `documentation` (
  `id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `html_id` varchar(50) DEFAULT NULL,
  `category_name` varchar(64) NOT NULL,
  `title` varchar(128) NOT NULL,
  `text` text DEFAULT NULL,
  `video` varchar(2048) DEFAULT NULL,
  `position` int(11) DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: event_log
CREATE TABLE `event_log` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `personal_id` int(11) DEFAULT NULL,
  `event` varchar(255) DEFAULT NULL,
  `type` char(12) DEFAULT NULL COMMENT 'error / info / debug / system',
  `data` mediumtext DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Indexes and auto-increment
ALTER TABLE `auth_rule` ADD PRIMARY KEY (`name`);
ALTER TABLE `auth_item` ADD PRIMARY KEY (`name`),
  ADD KEY `rule_name` (`rule_name`),
  ADD KEY `idx-auth_item-type` (`type`);
ALTER TABLE `auth_item_child` ADD PRIMARY KEY (`parent`,`child`),
  ADD KEY `child` (`child`);
ALTER TABLE `auth_assignment` ADD PRIMARY KEY (`item_name`,`personal_id`),
  ADD KEY `idx-auth_assignment-user_id` (`personal_id`);
ALTER TABLE `users` ADD PRIMARY KEY (`id`);
ALTER TABLE `users` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `cities` ADD PRIMARY KEY (`id`);
ALTER TABLE `cities` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `carwash` ADD PRIMARY KEY (`id`),
  ADD KEY `owner_id` (`owner_id`),
  ADD KEY `city_id` (`city_id`);
ALTER TABLE `carwash` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `personal` ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `carwash_id` (`carwash_id`);
ALTER TABLE `personal` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `carwash_settings` ADD PRIMARY KEY (`id`),
  ADD KEY `carwash_id` (`carwash_id`);
ALTER TABLE `carwash_settings` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `carwash_schedule` ADD PRIMARY KEY (`id`),
  ADD KEY `carwash_id` (`carwash_id`);
ALTER TABLE `carwash_schedule` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `carwash_comfort` ADD PRIMARY KEY (`id`),
  ADD KEY `carwash_id` (`carwash_id`);
ALTER TABLE `carwash_comfort` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `carwash_contacts` ADD PRIMARY KEY (`id`),
  ADD KEY `carwash_id` (`carwash_id`);
ALTER TABLE `carwash_contacts` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `car_brands` ADD PRIMARY KEY (`id`),
  ADD KEY `carwash_id` (`carwash_id`);
ALTER TABLE `car_brands` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `car_models` ADD PRIMARY KEY (`id`),
  ADD KEY `carwash_id` (`carwash_id`),
  ADD KEY `car_brand_id` (`car_brand_id`);
ALTER TABLE `car_models` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `carwash_blacklist` ADD PRIMARY KEY (`id`),
  ADD KEY `carwash_id` (`carwash_id`),
  ADD KEY `client_id` (`client_id`);
ALTER TABLE `carwash_blacklist` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `carwash_images` ADD PRIMARY KEY (`id`),
  ADD KEY `carwash_id` (`carwash_id`);
ALTER TABLE `carwash_images` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `carwash_sales` ADD PRIMARY KEY (`id`),
  ADD KEY `carwash_id` (`carwash_id`);
ALTER TABLE `carwash_sales` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `carwash_sales_item` ADD PRIMARY KEY (`id`),
  ADD KEY `sale_id_complex_id_service_id` (`sale_id`,`complex_id`,`service_id`),
  ADD KEY `FK_carwash_sales_item_complexes` (`complex_id`),
  ADD KEY `FK_carwash_sales_item_services` (`service_id`);
ALTER TABLE `carwash_sales_item` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `advertising` ADD PRIMARY KEY (`id`),
  ADD KEY `carwash_id` (`carwash_id`);
ALTER TABLE `advertising` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `clients` ADD PRIMARY KEY (`id`),
  ADD KEY `carwash_id` (`carwash_id`);
ALTER TABLE `clients` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `materials` ADD PRIMARY KEY (`id`),
  ADD KEY `carwash_id` (`carwash_id`);
ALTER TABLE `materials` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `services` ADD PRIMARY KEY (`id`),
  ADD KEY `carwash_id` (`carwash_id`);
ALTER TABLE `services` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `service_materials` ADD PRIMARY KEY (`id`),
  ADD KEY `service_id` (`service_id`),
  ADD KEY `material_id` (`material_id`);
ALTER TABLE `service_materials` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `complexes` ADD PRIMARY KEY (`id`),
  ADD KEY `carwash_id` (`carwash_id`);
ALTER TABLE `complexes` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `complex_services` ADD PRIMARY KEY (`id`),
  ADD KEY `complex_id` (`complex_id`),
  ADD KEY `service_id` (`service_id`);
ALTER TABLE `complex_services` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `complex_materials` ADD PRIMARY KEY (`id`),
  ADD KEY `complex_id` (`complex_id`),
  ADD KEY `material_id` (`material_id`);
ALTER TABLE `complex_materials` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `orders` ADD PRIMARY KEY (`id`),
  ADD KEY `carwash_id` (`carwash_id`),
  ADD KEY `client_id` (`client_id`),
  ADD KEY `personal_id` (`personal_id`),
  ADD KEY `car_model_id` (`car_model_id`),
  ADD KEY `car_brand_id` (`car_brand_id`);
ALTER TABLE `orders` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `order_service` ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`);
ALTER TABLE `order_service` MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;
ALTER TABLE `chat` ADD PRIMARY KEY (`id`),
  ADD KEY `carwash_id` (`carwash_id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `client_id` (`client_id`);
ALTER TABLE `chat` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `chat_messages` ADD PRIMARY KEY (`id`),
  ADD KEY `chat_id` (`chat_id`),
  ADD KEY `client_id` (`client_id`),
  ADD KEY `personal_id` (`personal_id`);
ALTER TABLE `chat_messages` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `tickets` ADD PRIMARY KEY (`id`),
  ADD KEY `carwash_id` (`carwash_id`),
  ADD KEY `personal_id` (`personal_id`);
ALTER TABLE `tickets` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `ticket_messages` ADD PRIMARY KEY (`id`),
  ADD KEY `ticket_id` (`ticket_id`),
  ADD KEY `personal_id` (`personal_id`),
  ADD KEY `admin_id` (`admin_id`);
ALTER TABLE `ticket_messages` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `personal_log` ADD PRIMARY KEY (`id`),
  ADD KEY `personal_id` (`personal_id`);
ALTER TABLE `personal_log` MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;
ALTER TABLE `personal_notification` ADD PRIMARY KEY (`id`),
  ADD KEY `person_id` (`personal_id`);
ALTER TABLE `personal_notification` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `partner_store` ADD PRIMARY KEY (`id`);
ALTER TABLE `partner_store` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `partner_store_items` ADD PRIMARY KEY (`id`),
  ADD KEY `store_id` (`partner_store_id`);
ALTER TABLE `partner_store_items` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `documentation_category` ADD PRIMARY KEY (`id`),
  ADD KEY `parent_id` (`parent_id`);
ALTER TABLE `documentation_category` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `documentation` ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `html_id` (`html_id`),
  ADD KEY `category_id` (`category_id`);
ALTER TABLE `documentation` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `event_log` ADD PRIMARY KEY (`id`),
  ADD KEY `FK_event_log_users` (`user_id`),
  ADD KEY `FK_event_log_personal` (`personal_id`);
ALTER TABLE `event_log` MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

-- Foreign keys
ALTER TABLE `auth_item` ADD CONSTRAINT `auth_item_ibfk_1` FOREIGN KEY (`rule_name`) REFERENCES `auth_rule` (`name`) ON DELETE SET NULL ON UPDATE CASCADE;
ALTER TABLE `auth_item_child` ADD CONSTRAINT `auth_item_child_ibfk_1` FOREIGN KEY (`parent`) REFERENCES `auth_item` (`name`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `auth_item_child_ibfk_2` FOREIGN KEY (`child`) REFERENCES `auth_item` (`name`) ON DELETE CASCADE ON UPDATE CASCADE;
ALTER TABLE `auth_assignment` ADD CONSTRAINT `FK_auth_assignment_personal` FOREIGN KEY (`personal_id`) REFERENCES `personal` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `auth_assignment_ibfk_1` FOREIGN KEY (`item_name`) REFERENCES `auth_item` (`name`) ON DELETE CASCADE ON UPDATE CASCADE;
ALTER TABLE `carwash` ADD CONSTRAINT `FK_carwash_cities` FOREIGN KEY (`city_id`) REFERENCES `cities` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_carwash_personal` FOREIGN KEY (`owner_id`) REFERENCES `personal` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;
ALTER TABLE `personal` ADD CONSTRAINT `FK_personal_carwash` FOREIGN KEY (`carwash_id`) REFERENCES `carwash` (`id`),
  ADD CONSTRAINT `FK_personal_users` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
ALTER TABLE `carwash_settings` ADD CONSTRAINT `FK_carwash_settings_carwash` FOREIGN KEY (`carwash_id`) REFERENCES `carwash` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
ALTER TABLE `carwash_schedule` ADD CONSTRAINT `FK_carwash_schedule_carwash` FOREIGN KEY (`carwash_id`) REFERENCES `carwash` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
ALTER TABLE `carwash_comfort` ADD CONSTRAINT `FK_carwash_comfort_carwash` FOREIGN KEY (`carwash_id`) REFERENCES `carwash` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
ALTER TABLE `carwash_contacts` ADD CONSTRAINT `FK_carwash_contacts_carwash` FOREIGN KEY (`carwash_id`) REFERENCES `carwash` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
ALTER TABLE `car_brands` ADD CONSTRAINT `FK_car_brands_carwash` FOREIGN KEY (`carwash_id`) REFERENCES `carwash` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
ALTER TABLE `car_models` ADD CONSTRAINT `FK_car_models_car_brands` FOREIGN KEY (`car_brand_id`) REFERENCES `car_brands` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_car_models_carwash` FOREIGN KEY (`carwash_id`) REFERENCES `carwash` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
ALTER TABLE `carwash_blacklist` ADD CONSTRAINT `FK_carwash_blacklist_carwash` FOREIGN KEY (`carwash_id`) REFERENCES `carwash` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_carwash_blacklist_clients` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
ALTER TABLE `carwash_images` ADD CONSTRAINT `FK__carwash` FOREIGN KEY (`carwash_id`) REFERENCES `carwash` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
ALTER TABLE `carwash_sales` ADD CONSTRAINT `FK_carwash_sales_carwash` FOREIGN KEY (`carwash_id`) REFERENCES `carwash` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
ALTER TABLE `carwash_sales_item` ADD CONSTRAINT `FK_carwash_sales_item_carwash_sales` FOREIGN KEY (`sale_id`) REFERENCES `carwash_sales` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_carwash_sales_item_complexes` FOREIGN KEY (`complex_id`) REFERENCES `complexes` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_carwash_sales_item_services` FOREIGN KEY (`service_id`) REFERENCES `services` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
ALTER TABLE `advertising` ADD CONSTRAINT `FK_advertising_carwash` FOREIGN KEY (`carwash_id`) REFERENCES `carwash` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
ALTER TABLE `clients` ADD CONSTRAINT `FK_clients_carwash` FOREIGN KEY (`carwash_id`) REFERENCES `carwash` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
ALTER TABLE `materials` ADD CONSTRAINT `FK_materials_carwash` FOREIGN KEY (`carwash_id`) REFERENCES `carwash` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
ALTER TABLE `services` ADD CONSTRAINT `FK_services_carwash` FOREIGN KEY (`carwash_id`) REFERENCES `carwash` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
ALTER TABLE `service_materials` ADD CONSTRAINT `FK_service_materials_materials` FOREIGN KEY (`material_id`) REFERENCES `materials` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_service_materials_services` FOREIGN KEY (`service_id`) REFERENCES `services` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
ALTER TABLE `complexes` ADD CONSTRAINT `FK_complexes_carwash` FOREIGN KEY (`carwash_id`) REFERENCES `carwash` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
ALTER TABLE `complex_services` ADD CONSTRAINT `FK_complex_services_complexes` FOREIGN KEY (`complex_id`) REFERENCES `complexes` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_complex_services_services` FOREIGN KEY (`service_id`) REFERENCES `services` (`id`) ON UPDATE CASCADE;
ALTER TABLE `complex_materials` ADD CONSTRAINT `FK_complex_materials_complexes` FOREIGN KEY (`complex_id`) REFERENCES `complexes` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_complex_materials_materials` FOREIGN KEY (`material_id`) REFERENCES `materials` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
ALTER TABLE `orders` ADD CONSTRAINT `FK_orders_car_brands` FOREIGN KEY (`car_brand_id`) REFERENCES `car_brands` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_orders_car_models` FOREIGN KEY (`car_model_id`) REFERENCES `car_models` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_orders_carwash` FOREIGN KEY (`carwash_id`) REFERENCES `carwash` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_orders_clients` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_orders_personal` FOREIGN KEY (`personal_id`) REFERENCES `personal` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;
ALTER TABLE `order_service` ADD CONSTRAINT `FK__orders` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
ALTER TABLE `chat` ADD CONSTRAINT `FK_chat_carwash` FOREIGN KEY (`carwash_id`) REFERENCES `carwash` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_chat_clients` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_chat_orders` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
ALTER TABLE `chat_messages` ADD CONSTRAINT `FK_chat_messages_chat` FOREIGN KEY (`chat_id`) REFERENCES `chat` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_chat_messages_clients` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_chat_messages_personal` FOREIGN KEY (`personal_id`) REFERENCES `personal` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;
ALTER TABLE `tickets` ADD CONSTRAINT `FK_tickets_carwash` FOREIGN KEY (`carwash_id`) REFERENCES `carwash` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_tickets_personal` FOREIGN KEY (`personal_id`) REFERENCES `personal` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;
-- admin_id column kept for TicketMessages model; admins table is outside models/ar scope
ALTER TABLE `ticket_messages` ADD CONSTRAINT `FK_ticket_messages_personal` FOREIGN KEY (`personal_id`) REFERENCES `personal` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_ticket_messages_tickets` FOREIGN KEY (`ticket_id`) REFERENCES `tickets` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
ALTER TABLE `personal_log` ADD CONSTRAINT `FK_personal_log_personal` FOREIGN KEY (`personal_id`) REFERENCES `personal` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
ALTER TABLE `personal_notification` ADD CONSTRAINT `FK_personal_notification_personal` FOREIGN KEY (`personal_id`) REFERENCES `personal` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
ALTER TABLE `partner_store_items` ADD CONSTRAINT `FK_partner_store_items_partner_store` FOREIGN KEY (`partner_store_id`) REFERENCES `partner_store` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
ALTER TABLE `documentation_category` ADD CONSTRAINT `FK_documentation_category_documentation_category` FOREIGN KEY (`parent_id`) REFERENCES `documentation_category` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
ALTER TABLE `documentation` ADD CONSTRAINT `FK_documentation_documentation_category` FOREIGN KEY (`category_id`) REFERENCES `documentation_category` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
ALTER TABLE `event_log` ADD CONSTRAINT `FK_event_log_personal` FOREIGN KEY (`personal_id`) REFERENCES `personal` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_event_log_users` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

SET FOREIGN_KEY_CHECKS = 1;

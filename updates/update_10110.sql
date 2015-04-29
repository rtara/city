ALTER TABLE `prefix_city`
    ADD `city_tariff_id` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0',
    ADD `city_date_tariff_end` DATETIME DEFAULT NULL;


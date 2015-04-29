ALTER TABLE `prefix_city`
    ADD `city_count_subscribe` INT(11) UNSIGNED NOT NULL DEFAULT '0',
    ADD `city_fields` TEXT,
    ADD `city_about` TEXT,
    ADD `city_about_source` TEXT,
    ADD `topic_id_last` INTEGER(11) UNSIGNED NOT NULL DEFAULT '0',
    ADD `city_date_activation` DATETIME DEFAULT NULL;
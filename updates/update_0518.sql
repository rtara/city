ALTER TABLE `prefix_city`
    ADD `city_count_favourite` int(11) unsigned NOT NULL DEFAULT '0',
    ADD `city_file_name` varchar(50) DEFAULT NULL,
    ADD `city_type` enum('base') DEFAULT 'base',
    ADD `city_skype` varchar(50) DEFAULT NULL,
    ADD `city_icq` varchar(12) DEFAULT NULL,
    ADD `city_contact_info` varchar(255) DEFAULT NULL,
    CHANGE  `city_boss`  `city_contact_name` VARCHAR(50);
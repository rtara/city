CREATE TABLE IF NOT EXISTS `prefix_city` (
  `city_id` INTEGER(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `blog_id` INTEGER(11) UNSIGNED NOT NULL,
  `user_owner_id` INTEGER(11) UNSIGNED NOT NULL,
  `city_url` VARCHAR(200) NOT NULL DEFAULT '',
  `city_name` VARCHAR(255) NOT NULL DEFAULT '' COLLATE utf8_general_ci,
  `city_name_legal` VARCHAR(255) DEFAULT NULL,
  `city_description` TEXT NOT NULL,
  `city_tags` VARCHAR(255) NOT NULL DEFAULT '',
  `city_country` VARCHAR(30) DEFAULT NULL,
  `city_city` VARCHAR(30) DEFAULT NULL,
  `city_address` VARCHAR(100) DEFAULT NULL,
  `city_site` VARCHAR(100) DEFAULT NULL,
  `city_phone` VARCHAR(50) DEFAULT NULL,
  `city_fax` VARCHAR(50) DEFAULT NULL,
  `city_skype` varchar(50) DEFAULT NULL,
  `city_icq` varchar(12) DEFAULT NULL,
  `city_contact_name` VARCHAR(50) DEFAULT NULL,
  `city_contact_info` varchar(255) DEFAULT NULL,
  `city_date_basis` DATETIME DEFAULT NULL,
  `city_vacancies` TEXT DEFAULT NULL,
  `city_email` VARCHAR(50) DEFAULT NULL,
  `city_logo` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0',
  `city_logo_type` VARCHAR(5) DEFAULT NULL,
  `city_date_add` DATETIME NOT NULL,
  `city_date_edit` DATETIME DEFAULT NULL,
  `city_date_activation` DATETIME DEFAULT NULL,
  `city_rating` FLOAT(9,3) NOT NULL DEFAULT '0.000',
  `city_count_workers` INTEGER(11) UNSIGNED NOT NULL DEFAULT '0',
  `city_count_vote` INTEGER(11) UNSIGNED NOT NULL DEFAULT '0',
  `city_count_feedback` INTEGER(11) UNSIGNED NOT NULL DEFAULT '0',
  `city_count_favourite` INT(11) UNSIGNED NOT NULL DEFAULT '0',
  `city_count_subscribe` INT(11) UNSIGNED NOT NULL DEFAULT '0',
  `city_latitude` varchar(255) default NULL,
  `city_longitude` varchar(255) default NULL,
  `city_type` enum('base') DEFAULT 'base',
  `city_file_name` varchar(50) DEFAULT NULL,
  `city_active` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0',
  `city_prefs` TEXT,
  `city_fields` TEXT,
  `city_about` TEXT,
  `city_about_source` TEXT,
  `topic_id_last` INTEGER(11) UNSIGNED NOT NULL DEFAULT '0',
  `city_tariff_id` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0',
  `city_date_tariff_end` DATETIME DEFAULT NULL,

  PRIMARY KEY (`city_id`),
   KEY user_owner_id (user_owner_id),
   KEY blog_id (blog_id),
   KEY city_url (city_url),
   KEY city_name (city_name),
   KEY city_type (city_type),
  CONSTRAINT prefix_city_fk FOREIGN KEY (user_owner_id) REFERENCES prefix_user (user_id) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT prefix_city_fk1 FOREIGN KEY (blog_id) REFERENCES prefix_blog (blog_id) ON DELETE CASCADE ON UPDATE CASCADE
)ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `prefix_city_tag` (
  `city_tag_id` INTEGER(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `city_id` INTEGER(11) UNSIGNED NOT NULL,
  `user_id` INTEGER(11) UNSIGNED NOT NULL,
  `city_tag_text` VARCHAR(50) COLLATE utf8_general_ci NOT NULL DEFAULT '',
  PRIMARY KEY (`city_tag_id`),
  KEY `city_id` (`city_id`),
  KEY `user_id` (`user_id`),
  KEY `city_tag_text` (`city_tag_text`),
  CONSTRAINT `prefix_city_tag_fk` FOREIGN KEY (`city_id`) REFERENCES `prefix_city` (`city_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `prefix_city_tag_fk1` FOREIGN KEY (`user_id`) REFERENCES `prefix_user` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE
)ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `prefix_city_feedback_read` (
  `city_id` INTEGER(11) UNSIGNED NOT NULL,
  `user_id` INTEGER(11) UNSIGNED NOT NULL,
  `date_read` DATETIME NOT NULL,
  `feedback_count_last` INTEGER(10) UNSIGNED NOT NULL DEFAULT '0',
  `feedback_id_last` INTEGER(11) NOT NULL DEFAULT '0',
  UNIQUE KEY `city_id_user_id` (`city_id`, `user_id`),
  KEY `city_id` (`city_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `prefix_city_feedback_read_fk` FOREIGN KEY (`city_id`) REFERENCES `prefix_city` (`city_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `prefix_city_feedback_read_fk1` FOREIGN KEY (`user_id`) REFERENCES `prefix_user` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE
)ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `prefix_city_staff` (
  `city_id` int(11) unsigned NOT NULL,
  `staff_name` varchar(100) NOT NULL,
  `staff_position` varchar(100) DEFAULT NULL,
  `user_id` int(11) unsigned DEFAULT NULL,
  KEY `city_id` (`city_id`),
  CONSTRAINT `prefix_city_staff_fk` FOREIGN KEY (`city_id`) REFERENCES `prefix_city` (`city_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `prefix_city_photo` (
	`id` INT(11) NOT NULL AUTO_INCREMENT,
	`city_id` INT(11) UNSIGNED NULL DEFAULT NULL,
	`path` VARCHAR(255) NOT NULL,
	`description` TEXT NULL,
	`target_tmp` VARCHAR(40) NULL DEFAULT NULL,
	PRIMARY KEY (`id`),
	INDEX `city_id` (`city_id`),
	INDEX `target_tmp` (`target_tmp`),
	CONSTRAINT `prefix_city_photo_fk_1` FOREIGN KEY (`city_id`) REFERENCES `prefix_city` (`city_id`) ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;


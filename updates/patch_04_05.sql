Alter table prefix_city
MODIFY city_description TEXT NOT NULL,
ADD  city_count_favourite INT(11) UNSIGNED NOT NULL DEFAULT '0',
ADD  city_latitude varchar(255) collate utf8_bin default NULL,
ADD  city_longitude varchar(255) collate utf8_bin default NULL;

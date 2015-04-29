<?php
/**
 *	Module "City"
 *	Author: Grebenkin Anton
 *	Contact e-mail: 4freework@gmail.com
 */

/**
 * Конфиг плагина "city" - Компании
 */
Config::Set('acl.vote.city.rating', -1); 		// порог рейтинга при котором юзер может голосовать за компанию
Config::Set('acl.create.feedback.rating', -5);		// порог рейтинга при котором юзер может добавлять отзыв
Config::Set('acl.create.city.rating', 0);		// порог рейтинга при котором пользователь может создать компанию

Config::Set('module.city.per_page', 20); 		// количество компаний на странице
Config::Set('module.city.on_block', 10);			// количество отображаемое в блоке
Config::Set('module.city.use_jobs', true);		// используется ли плагин Работа
Config::Set('module.city.description_len', 255); // количество символов описание в компаний
Config::Set('module.city.about_len', 3000);       // количество символов в поле о компаний
Config::Set('module.city.new_time', 60*60*24*7); // сколько времени компания считается новой

Config::Set('module.city.topic_on_index', true);	// выводить все новые топики из блогов компаний на главную
Config::Set('module.city.feedbacks_in_stream', false); // выводить отзывы компаний в прямой эфир
Config::Set('module.city.use_category', false);      // Вместо тэгов выводить категории.
Config::Set('module.city.prefix', 'city'); 	// префикс для компаний (ссылка в URL)
Config::Set('module.city.image_web_path', '___path.root.web______path.uploads.images___/city/'); // путь до логотипов компании
Config::Set('module.city.use_activate', false);			// используется премодерация компаний
Config::Set('module.city.use_convert_url', false);		// используется ли автоматическое создание URL по названию компании
Config::Set('module.city.allow_file_ext', array('doc','docx','xls','xlsx', 'pdf'));		// типы файлов которые позволяем загружать
Config::Set('module.city.types', array('base'));	// Можно задавать типы компаний и для них делать различный набор полей.

// Тип карты. Возможные значения:
// openstreet#map (Open Street Map);
// yandex#map (схема)
// yandex#publicMap (народная карта); - по умолчанию;
// google#map (Google.Схема)
// mail#map (Mail.Схема);
Config::Set('module.city.map.use', true);				// позволять ли компаниям указывать координаты на карте
Config::Set('module.city.map.type', "yandex#publicMap"); // тип отображения карты
Config::Set('module.city.map.edit_zoom', 9); 			// зум при редактировании компании
Config::Set('module.city.map.view_zoom', 15); 			// зум при отображении карты в профиле
Config::Set('module.city.map.scroll', false); 			// разрешать ли менять размер карты скролом мышки
Config::Set('module.city.map.center', "[37.5607,55.8672]"); // центр карты
Config::Set('module.city.map.geocode', true);			// Использовать ли геокодирование (ищет по адресу местонахождение компании при редактировании)


// настройки загрузки логотипа
$config['module']['image']['city_logo']['jpg_quality']		= 100;
$config['module']['image']['city_logo']['watermark_use']		= false;
$config['module']['image']['city_logo']['round_corner']		= false;
Config::Set('module.city.logo_size', array(100,48,24,0)); // Список размеров логотипов компаний. 0 - исходный размер


/**
 * Настройки фотографий
 */
Config::Set('module.city.photo.jpg_quality', 100);        // настройка модуля Image, качество обработки фото
Config::Set('module.city.photo.photo_max_size', 6*1024);  // максимально допустимый размер фото, Kb
Config::Set('module.city.photo.count_photos_min', 0);     // минимальное количество фоток
Config::Set('module.city.photo.count_photos_max', 30);    // максимальное количество фоток
Config::Set('module.city.photo.per_page', 21);            // число фоток при загрузке
Config::Set('module.city.photo.size', array(             // список размеров превью, которые необходимо делать при загрузке фото
	array(
		'w' => 1000,
		'h' => null,
		'crop' => false,
	),
	array(
		'w' => 300,
		'h' => null,
		'crop' => false,
	),
	array(
		'w' => 100,
		'h' => 65,
		'crop' => true,
	),
	array(
		'w' => 50,
		'h' => 50,
		'crop' => true,
	)
));
/** Список тарифов
 * нулевой тариф всегда считается бесплатным и без времени
 * доступные права.
 * 'branding' возможность брендировать страницу
 * 'photo' возможность загружать фотографии в альбом компании
 * 'widgets' возможность пользоваться виджетами
 */
Config::Set('module.city.tariffs', array(
	array(
		'id' => 0,
		'title' => 'Free',
		'rights' => array('photo'), // доступные действия
	),
	array(
		'id' => 1,
		'title' => 'Бизнес',
		'rights' => array('branding','photo','widgets'),
	),
));

Config::Set('module.city.tariff_periods', array(30,90,365));  // варианты продления тарифа, количество дней

// Настройки авторизации твиттера https://dev.twitter.com/apps
Config::Set('module.city.twitter.oauth_access_token', ''); //access token
Config::Set('module.city.twitter.oauth_access_token_secret', ''); // access token secret
Config::Set('module.city.twitter.consumer_key', ''); //consumer key
Config::Set('module.city.twitter.consumer_secret', ''); // consumer secret



// настройки руотинга
Config::Set('router.page.city', 'PluginCity_ActionCity');
Config::Set('router.page.cityes', 'PluginCity_ActionCityes');

// если поменяли префикс то подменяем ссылки действий
if (Config::Get('module.city.prefix') != 'city'){
	Config::Set('router.rewrite.city', '___module.city.prefix___');
}
//Config::Set('router.rewrite.cityes', 'НаКакойУРЛПоменять');
// описание таблиц компаний
Config::Set('db.table.city', 			'___db.table.prefix___city');
Config::Set('db.table.city_tag', 		'___db.table.prefix___city_tag');
Config::Set('db.table.city_feedback_read', '___db.table.prefix___city_feedback_read');
Config::Set('db.table.city_photo', 		'___db.table.prefix___city_photo');
Config::Set('db.table.staff', 		'___db.table.prefix___city_staff');

// Блоки которые будут на всех страницах компаний
Config::Set('block.plugin_cityes', array(
    'action' => array(
       'cityes'
    ),
    'blocks' => array(
        'right' => array(
            /*'stream'=>array('priority'=>100),*/
			'feedbacks' => array('params' => array('plugin' => 'city'), 'priority' => 4),
            'tags' => array('params' => array('plugin' => 'city'), 'priority' => 5),
            'tagsCountry' => array('params' => array('plugin' => 'city'), 'priority' => 2),
            'tagsCity' => array('params' => array('plugin' => 'city'), 'priority' => 1),
        )
    ),
    'clear' => false,
));

Config::Set('block.city_widgets', array(
	'action' => array(
		'city' => array('{topic}','{profile}','{blog}','{vacancies}','{feedbacks}','{fans}',)
	),
	'blocks' => array(
		'right' => array(
			'widgets' => array('params' => array('plugin' => 'city'), 'priority' => 100),
		)
	),
	'clear' => false,
));
Config::Set('block.plugin_city', array(
	'action' => array(
		'city'
	),
	'blocks' => array(
		'right' => array(
			'cityinfo' => array('params' => array('plugin' => 'city'), 'priority' => 110),
		)
	),
	'clear' => false,
));

?>
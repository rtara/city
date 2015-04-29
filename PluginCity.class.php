<?php
/**
*	Plugin "City"
*	Author: Grebenkin Anton
*	Contact e-mail: 4freework@gmail.com
*/
if (!class_exists('Plugin')) {
	die('Hacking attemp!');
}

class PluginCity extends Plugin {

	public function __construct() {
		if (class_exists('MobileDetect') && MobileDetect::IsMobileTemplate()) {
			// удаляем переопределение шаблонов и классов экшенов
			unset($this->aInherits['template']);
			unset($this->aInherits['action']);
		}
	}


	protected $aInherits=array(
		'action' => array('ActionSearch','ActionProfile'),
		'module' => array('ModuleTopic','ModuleBlog','ModuleComment','ModuleGeo','ModuleSubscribe','ModuleACL'),
		'entity' => array('ModuleTopic_EntityTopic','ModuleBlog_EntityBlog'),
		'mapper' => array('ModuleBlog_MapperBlog'),
	);
	/*
	 * Активация плагина Компании.
	 * Создание таблиц в базе данных при их отсутствии.
	 */
	public function Activate() {
		$this->addEnumType('prefix_blog','blog_type','city');
		$this->addEnumType('prefix_comment','target_type','city');
		$this->addEnumType('prefix_comment_online','target_type','city');
		$this->addEnumType('prefix_favourite','target_type','city');
		$this->addEnumType('prefix_vote','target_type','city');
		$this->ExportSQL(dirname(__FILE__).'/updates/install.sql');
		return true;
	}

	/**
	 * Инициализация плагина Компании
	 */
	public function Init() {
		$this->Geo_AddTargetType('city');
		$this->Subscribe_AddTargetType('city_new_topic');
		$this->Subscribe_AddTargetType('city_new_feedback');
		$this->Viewer_AppendStyle(Plugin::GetTemplateWebPath(__CLASS__).'css/style.css');
		$this->Viewer_AppendScript(Plugin::GetTemplateWebPath(__CLASS__).'js/city.js');
		$this->Viewer_AppendScript(Plugin::GetTemplateWebPath(__CLASS__).'js/photo.js');
		$this->Viewer_AddMenu('main',Plugin::GetTemplatePath(__CLASS__).'menu.main.tpl');
	}
}

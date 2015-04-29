<?php
class Plugincity_HookCity extends Hook {
	public function RegisterHook() {
		$this->AddHook('topic_add_before', 'TopicBeforeAdd', __CLASS__, -5);
		$this->AddHook('topic_add_after', 'TopicAfterAdd', __CLASS__, -5);
		$this->AddHook('topic_edit_after', 'TopicAfterEdit', __CLASS__, -5);

		$this->AddHook('template_main_menu_item', 'tplMainMenu', __CLASS__, 2);
		$this->AddHook('template_menu_profile_created_item', 'tplMenuCreatedProfile', __CLASS__, -5);
		$this->AddHook('template_menu_profile_favourite_item', 'tplMenuFavProfile', __CLASS__, -5);

		$this->AddHook('template_profile_whois_activity_item', 'tplProfileWhois', __CLASS__, -5);
		$this->AddHook('template_menu_blog', 'tplMenuBlog', __CLASS__, -5);
		$this->AddHook('template_search_result_item', 'tplSearchItem', __CLASS__, -5);
		$this->AddHook('template_search_result', 'tplSearch', __CLASS__, -5);
		$this->AddHook('template_menu_create_item', 'tplCreateItem', __CLASS__, -5);
		$this->AddHook('template_menu_create_item_select', 'tplCreateItemSelect', __CLASS__, -5);
		$this->AddHook('action_shutdown_actionindex_after', 'ActionShutdown', __CLASS__, -5);
		if (Config::Get('module.city.feedbacks_in_stream'))
			$this->AddHook('template_block_stream_nav_item', 'tplBlockStreamItem', __CLASS__, -5);

	}




	/**
	 * Добавляет элемент в меню "Создать"
	 * @param $aVars
	 * @return mixed
	 */
	public function tplCreateItem($aVars) {
		return $this->Viewer_Fetch(Plugin::GetTemplatePath(__CLASS__).'/inject.menu.create.content.tpl');
	}
	/**
	 * Добавляет элемент в меню выбора "Создать"
	 * @param $aVars
	 * @return mixed
	 */
	public function tplCreateItemSelect($aVars) {
		if ($aVars["sMenuItemSelect"]=='city')
			return $this->Lang_Get('plugin.city.city_menu_create');
	}
	/**
	 * Вкладка поиска компании
	 * @param $aVars
	 * @return mixed
	 */
	public function tplSearchItem($aVars) {
		if ($aVars["sType"]=='cityes')
			return $this->Lang_Get('plugin.city.search_results_count_cityes');
	}
	/**
	 * Отображение результатов поиска
	 * @param $aVars
	 * @return mixed
	 */
	public function tplSearch($aVars) {
		if ($aVars["sType"]=='cityes')
			return $this->Viewer_Fetch(Plugin::GetTemplatePath(__CLASS__).'/city_list.tpl');
	}

	/**
	 * Выводит сразу на главную топики компаний, если устрановлена настройка.
	 * @param $aVars
	 * @return mixed
	 */
	public function TopicBeforeAdd($aVars) {
		if ($aVars["oBlog"]->getType()=='city'){
			// Топики компаний сразу попадают на главную
			if (Config::Get('module.city.topic_on_index')){
				$aVars["oTopic"]->setPublishIndex(1);
			}
		}
	}
	/**
	 * Отправляет подписчикам компании уведомления о новом топике
	 * @param $aVars
	 * @return mixed
	 */
	public function TopicAfterAdd($aVars) {
		if ($aVars["oBlog"]->getType()=='city' and $aVars["oTopic"]->getPublish()){
			$this->PluginCity_City_SendSubscribeNewTopic($aVars["oTopic"]);
			$oCity = $this->PluginCity_City_GetCityByBlogId($aVars["oTopic"]->getBlogId());
			$oCity->setTopicIdLast($aVars["oTopic"]->getId());
			$this->PluginCity_City_UpdateCity($oCity);
		}
	}

	/**
	 * Регулирует последний топик компании после редактирования
	 * @param $aVars
	 * @return mixed
	 */
	public function TopicAfterEdit($aVars) {
		if ($aVars["oBlog"]->getType()=='city'){
			$this->PluginCity_City_SendSubscribeNewTopic($aVars["oTopic"]);
			$oCity = $this->PluginCity_City_GetCityByBlogId($aVars["oTopic"]->getBlogId());
			if ($oTopic = $this->Topic_GetLastBlogTopic($oCity->getBlogId())){
				$oCity->setTopicIdLast($oTopic->getId());
			} else{
				$oCity->setTopicIdLast(0);
			}
			$this->PluginCity_City_UpdateCity($oCity);
		}
	}
	/**
	 * Добавляет "Компании" в главное меню
	 * @param $aVars
	 * @return mixed
	 */
	public function tplMainMenu($aVars) {
		return $this->Viewer_Fetch(Plugin::GetTemplatePath(__CLASS__).'/inject.header_top.tpl');
	}
	/**
	 * Добавляет в публикации профиля меню с отзывами
	 * @param $aVars
	 * @return mixed
	 */
	public function tplMenuCreatedProfile($aVars) {
		return $this->Viewer_Fetch(Plugin::GetTemplatePath(__CLASS__).'/inject.menu.profile_created.tpl');
	}

	/**
	 * Добавляет в избранное профиля меню с компаниями
	 * @param $aVars
	 * @return mixed
	 */
	public function tplMenuFavProfile($aVars) {
		return $this->Viewer_Fetch(Plugin::GetTemplatePath(__CLASS__).'/inject.menu.profile_favourite.tpl');
	}

	/**
	 * Добавляет переменные с количеством топиков
	 * @param $aVars
	 * @return mixed
	 */
	public function ActionShutdown($aVars) {

		$iCountTopicsCollectiveNew=$this->Topic_GetCountTopicsCollectiveNew();
		$iCountTopicsPersonalNew=$this->Topic_GetCountTopicsPersonalNew();
		$iCountTopicsCityNew=$this->Topic_GetCountTopicsCorporativeNew();
		$iCountTopicsNew=$iCountTopicsCollectiveNew+$iCountTopicsPersonalNew+$iCountTopicsCityNew;

		$this->Viewer_Assign('iCountTopicsCityNew',$iCountTopicsCityNew);
		$this->Viewer_Assign('iCountTopicsPersonalNew',$iCountTopicsPersonalNew);
		$this->Viewer_Assign('iCountTopicsNew',$iCountTopicsNew);
	}

	/**
	 * Добавляет данные в профиль пользователя по созданным компаниям и отношения к компаниям
	 * @param $aVars
	 * @return mixed
	 */
	public function tplProfileWhois($aVars) {
		$aCityEmployee = $this->PluginCity_City_GetCityesByUser($aVars["oUserProfile"]->getId());
		$aResult=$this->PluginCity_City_GetFavouriteCityesByUserId($aVars["oUserProfile"]->getId(),1,100);
		$aCityAdmirer=$aResult['collection'];
		$this->Viewer_Assign('aCityEmployee',$aCityEmployee);
		$this->Viewer_Assign('aCityAdmirer',$aCityAdmirer);
		return $this->Viewer_Fetch(Plugin::GetTemplatePath(__CLASS__).'/inject.whois.tpl');
	}

	/**
	 * Добавляет в меню блогов "Корпоративные" топики
	 * @param $aVars
	 * @return mixed
	 */
	public function tplMenuBlog($aVars) {
		return $this->Viewer_Fetch(Plugin::GetTemplatePath(__CLASS__).'/inject.menu.blog.tpl');
	}

	public function tplBlockStreamItem($aVars) {
		return $this->Viewer_Fetch(Plugin::GetTemplatePath(__CLASS__).'/inject.stream_feedback.tpl');
	}
}
?>
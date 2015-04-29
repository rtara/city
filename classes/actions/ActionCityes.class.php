<?php
/**
 *	Module "City"
 *	Author: Grebenkin Anton
 *	Contact e-mail: 4freework@gmail.com
 */

class PluginCity_ActionCityes extends ActionPlugin {
	/**
	 * Главное меню
	 */
	protected $sMenuHeadItemSelect='city';
	/**
	 * Меню
	 */
	protected $sMenuItemSelect='city';
	/**
	 * Субменю
	 */
	protected $sMenuSubItemSelect='all';
	protected $oUserCurrent=null;
	protected $aUserCity=null;
	protected $aTypes;

	public function Init() {
		$this->SetDefaultEvent('');
		$this->oUserCurrent = $this->User_GetUserCurrent();
		$this->aTypes = Config::Get('module.city.types');
	}

	/**
	 * Регистрация событий
	 */
	protected function RegisterEvent() {
		$this->AddEvent('city','EventShowCityesByCity');
		$this->AddEvent('country','EventShowCityesByCountry');
		$this->AddEvent('tag','EventShowCityesByTag');
		$this->AddEvent('blog','EventShowCorporativeBlogs');
		$this->AddEventPreg('/^my$/i','/^(page([1-9]\d{0,5}))?$/i','EventShowCityes');
		$this->AddEventPreg('/^new$/i','/^(page([1-9]\d{0,5}))?$/i','EventShowCityes');
		$this->AddEventPreg('/^moderation$/i','/^(page([1-9]\d{0,5}))?$/i','EventShowCityes');
		$this->AddEventPreg('/^fav$/i','/^(page([1-9]\d{0,5}))?$/i','EventShowFavCityes');

		$this->AddEventPreg('/^(page([1-9]\d{0,5}))?$/i','EventShowCityes');
		$this->AddEventPreg('/^[\w\-\_]+$/i','/^(page([1-9]\d{0,5}))?$/i','EventShowCityesByType');
	}

	/**
	 * Показывает список компаний по рейтингу
	 *
	 */
	protected function EventShowCityes() {
		$this->sMenuItemSelect='cityes';
		$this->sMenuSubItemSelect='all';
		/**
		 * Сортировка
		 */
		$sOrder='city_rating';
		if (getRequest('order')) {
			$sOrder=(string)getRequest('order');
		}
		$sOrderWay='desc';
		if (getRequest('order_way')) {
			$sOrderWay=(string)getRequest('order_way');
		}
		$aOrder = array();
		if ($sOrder!='city_rating' or $sOrderWay!='desc') {
			$aOrder = array('order'=>$sOrder,'order_way'=>$sOrderWay);
		}

		/**
		 * Ограничения
		 */
		$aFilter = array();

		$sUrl = '';
		if (in_array($this->sCurrentEvent, array('my','new','moderation'))){
			$sUrl = $this->sCurrentEvent;
			$this->sMenuSubItemSelect = $this->sCurrentEvent;
			$sPage=$this->GetParam(0);
		}
		if ($this->sCurrentEvent == 'my'){
			if (!$this->oUserCurrent) {
				return parent::EventNotFound();
			}
			$Ids = $this->Blog_GetBlogUsersByUserId($this->oUserCurrent->getId(), array(ModuleBlog::BLOG_USER_ROLE_USER,ModuleBlog::BLOG_USER_ROLE_MODERATOR,ModuleBlog::BLOG_USER_ROLE_ADMINISTRATOR), true);
			$Ids = array_merge($this->Blog_GetBlogsByOwnerId($this->oUserCurrent->getId(),true), $Ids);
			$aFilter['blog_id'] = $Ids;
			$aFilter['all'] = 1; // чтобы фильтр показывал все компании, даже не активные
			$sHeader = $this->Lang_Get('plugin.city.cityes_my');
		} elseif($this->sCurrentEvent == 'new'){
			$sHeader = $this->Lang_Get('plugin.city.cityes_new');
			$aFilter['new_time'] = Config::Get('module.city.new_time');
		} elseif($this->sCurrentEvent == 'moderation'){
			if (!$this->oUserCurrent or !$this->oUserCurrent->isAdministrator()) {
				return parent::EventNotFound();
			}
			$sHeader = $this->Lang_Get('plugin.city.cityes_moderation');
			$aFilter['active'] = 0;
		}else{
			$sHeader = $this->Lang_Get('plugin.city.cityes');
			$sPage=$this->sCurrentEvent;
		}

		if (preg_match("/^page(\d+)$/i",$sPage,$aMatch)) {
			$iPage=$aMatch[1];
		} else {
			$iPage=1;
		}

		/**
		 * Получаем список компаний
		 */
		$aResult=$this->PluginCity_City_GetCityesByFilter($aFilter,array($sOrder=>$sOrderWay),$iPage,Config::Get('module.city.per_page'),array('owner'=>array(),'topic_last'=>array('blog'=>array(),'user'=>array())));
		$aCity=$aResult['collection'];
		$aPaging=$this->Viewer_MakePaging($aResult['count'],$iPage,Config::Get('module.city.per_page'),Config::Get('pagination.pages.count'),Router::GetPath('cityes').$sUrl,$aOrder);
		/**
		 * Загружаем переменные в шаблон
		 */

		$this->Viewer_Assign('sHeaderName',$sHeader);
		$this->Viewer_Assign('aPaging',$aPaging);
		$this->Viewer_Assign("aCity",$aCity);

		$this->Viewer_Assign("bCityesUseOrder",true);
		$this->Viewer_Assign("sCityesOrder",htmlspecialchars($sOrder));
		$this->Viewer_Assign("sCityesOrderWay",htmlspecialchars($sOrderWay));
		$this->Viewer_Assign("sCityesOrderWayNext",htmlspecialchars($sOrderWay=='desc' ? 'asc' : 'desc'));

		$this->Viewer_AddHtmlTitle($sHeader);
		$this->SetTemplateAction('cityes');
	}

	/**
	 * Показывает список компаний по типу
	 *
	 * @return unknown
	 */
	protected function EventShowCityesByType() {
		$this->sMenuItemSelect='cityes';
		$this->sMenuSubItemSelect='all';

		$sType=$this->sCurrentEvent;
		$sPage=$this->GetParam(0);
		if (preg_match("/^page(\d+)$/i",$sPage,$aMatch)) {
			$iPage=$aMatch[1];
		} else {
			$iPage=1;
		}
		// если такого типа не существует
		if (!in_array($sType,$this->aTypes)) {
			return parent::EventNotFound();
		}
		/**
		 * Получаем список компаний
		 */
		$aResult=$this->PluginCity_City_GetCityesByFilter(array('type' => $sType),array(),$iPage,Config::Get('module.city.per_page'));
		$aCity=$aResult['collection'];
		$aPaging=$this->Viewer_MakePaging($aResult['count'],$iPage,Config::Get('module.city.per_page'),Config::Get('pagination.pages.count'),Router::GetPath('cityes').$sType);
		/**
		 * Загружаем переменные в шаблон
		 */
		$this->Viewer_Assign('aPaging',$aPaging);
		$this->Viewer_Assign("aCity",$aCity);
		$this->Viewer_AddHtmlTitle($this->Lang_Get('plugin.city.cityes'));
		$this->SetTemplateAction('cityes');
	}

	/**
	 * Отображение избранных компаний пользователя
	 *
	 * @return unknown
	 */
	protected function EventShowFavCityes() {
		$this->sMenuItemSelect='cityes';
		$this->sMenuSubItemSelect='fav';
		/**
		 * Проверяем авторизован ли пользователь
		 */
		if (!$this->User_IsAuthorization()) {
			$this->Message_AddErrorSingle($this->Lang_Get('not_access'),$this->Lang_Get('error'));
			return Router::Action('error');
		}
		$sPage=$this->GetParam(0);
		if (preg_match("/^page(\d+)$/i",$sPage,$aMatch)) {
			$iPage=$aMatch[1];
		} else {
			$iPage=1;
		}
		/**
		 * Получаем список компаний
		 */
		$aResult=$this->PluginCity_City_GetFavouriteCityesByUserId($this->oUserCurrent->getId(),$iPage,Config::Get('module.city.per_page'));
		$aCity=$aResult['collection'];
		$aPaging=$this->Viewer_MakePaging($aResult['count'],$iPage,Config::Get('module.city.per_page'),Config::Get('pagination.pages.count'),Router::GetPath('cityes').'fav');
		/**
		 * Загружаем переменные в шаблон
		 */
		$this->Viewer_Assign('aPaging',$aPaging);
		$this->Viewer_Assign("aCity",$aCity);
		$sHeader = $this->Lang_Get('plugin.city.cityes_fav');
		$this->Viewer_AddHtmlTitle($sHeader);
		$this->Viewer_Assign('sHeaderName',$sHeader);
		$this->Viewer_Assign("bCityesUseOrder",false);
		$this->SetTemplateAction('cityes');
	}

	/**
	 * Отображение компаний по стране
	 *
	 * @return unknown
	 */
	protected function EventShowCityesByCountry() {
		/**
		 * Страна существует?
		 */
		if (!($oCountry=$this->Geo_GetCountryById($this->getParam(0)))) {
			return parent::EventNotFound();
		}
		/**
		 * Передан ли номер страницы
		 */
		$sPage=$this->GetParam(1);
		if (preg_match("/^page(\d+)$/i",$sPage,$aMatch)) {
			$iPage=$aMatch[1];
		} else {
			$iPage=1;
		}

		$aResult=$this->Geo_GetTargets(array('country_id'=>$oCountry->getId(),'target_type'=>'city'),$iPage,Config::Get('module.city.per_page'));
		$aCityId=array();
		foreach($aResult['collection'] as $oTarget) {
			$aCityId[]=$oTarget->getTargetId();
		}

		$aCity = $this->PluginCity_City_GetCityesAdditionalData($aCityId);

		$aPaging=$this->Viewer_MakePaging($aResult['count'],$iPage,Config::Get('module.city.per_page'),Config::Get('pagination.pages.count'),Router::GetPath('cityes').$this->sCurrentEvent.'/'.$oCountry->getId());
		/**
		 * Загружаем переменные в шаблон
		 */
		if ($aCity) {
			$this->Viewer_Assign('aPaging',$aPaging);
		}
		$sHeader = $this->Lang_Get('plugin.city.city_cityes_header_bycountry').$oCountry->getName();
		$this->Viewer_Assign('sHeaderName',$sHeader);
		$this->Viewer_Assign("aCity",$aCity);
		$this->Viewer_AddHtmlTitle($sHeader);
		$this->SetTemplateAction('cityes');
	}

	/**
	 * Отображение компаний по городу
	 *
	 * @return unknown
	 */
	protected function EventShowCityesByCity() {
		/**
		 * Страна существует?
		 */
		if (!($oCity=$this->Geo_GetCityById($this->getParam(0)))) {
			return parent::EventNotFound();
		}
		/**
		 * Передан ли номер страницы
		 */
		$sPage=$this->GetParam(1);
		if (preg_match("/^page(\d+)$/i",$sPage,$aMatch)) {
			$iPage=$aMatch[1];
		} else {
			$iPage=1;
		}

		$aResult=$this->Geo_GetTargets(array('city_id'=>$oCity->getId(),'target_type'=>'city'),$iPage,Config::Get('module.city.per_page'));
		$aCityId=array();
		foreach($aResult['collection'] as $oTarget) {
			$aCityId[]=$oTarget->getTargetId();
		}

		$aCity = $this->PluginCity_City_GetCityesAdditionalData($aCityId);

		$aPaging=$this->Viewer_MakePaging($aResult['count'],$iPage,Config::Get('module.city.per_page'),Config::Get('pagination.pages.count'),Router::GetPath('cityes').$this->sCurrentEvent.'/'.$oCity->getId());
		/**
		 * Загружаем переменные в шаблон
		 */
		if ($aCity) {
			$this->Viewer_Assign('aPaging',$aPaging);
		}

		$sHeader = $this->Lang_Get('plugin.city.city_cityes_header_bycity').$oCity->getName();
		$this->Viewer_Assign('sHeaderName',$sHeader);
		$this->Viewer_Assign('aPaging',$aPaging);
		$this->Viewer_Assign("aCity",$aCity);
		$this->Viewer_AddHtmlTitle($sHeader);
		$this->SetTemplateAction('cityes');
	}

	protected function EventShowCityesByTag() {
		/**
		 * Получаем тег из УРЛа
		 */
		$sTag=urldecode($this->getParam(0));

		/**
		 * Передан ли номер страницы
		 */
		$sPage=$this->GetParam(1);
		if (preg_match("/^page(\d+)$/i",$sPage,$aMatch)) {
			$iPage=$aMatch[1];
		} else {
			$iPage=1;
		}
		/**
		 * Получаем список компаний
		 */
		$aResult=$this->PluginCity_City_GetCityesByTag($sTag,$iPage,Config::Get('module.city.per_page'));
		$aCityes=$aResult['collection'];

		$aPaging=$this->Viewer_MakePaging($aResult['count'],$iPage,Config::Get('module.city.per_page'),Config::Get('pagination.pages.count'),Router::GetPath('cityes').'tag/'.htmlspecialchars($sTag));
		// Если используем тэги, а не категории
		if (!Config::Get('module.city.use_category')){
			$aTags=$this->PluginCity_City_GetCityTags(70);
			$this->Tools_MakeCloud($aTags);
			$this->Viewer_Assign("aTags",$aTags);
		}
		$this->Viewer_Assign('aPaging',$aPaging);
		$this->Viewer_Assign('aCity',$aCityes);
		$this->Viewer_Assign('sTag',$sTag);
		$this->Viewer_AddHtmlTitle($sTag);
		$this->Viewer_AddHtmlTitle($this->Lang_Get('plugin.city.city_header_tagfind'));
		$this->SetTemplateAction('tags');
	}

	/**
	 * Отображает топики корпоративных блогов
	 *
	 * @return unknown
	 */
	protected function EventShowCorporativeBlogs() {
		$this->sMenuHeadItemSelect='blog';
		$this->Viewer_AddBlock('right','cityes',array('plugin'=>'city'));
		$sCityUrl=$this->sCurrentEvent;
		$sPage=$this->GetParam(0);
		$this->sMenuItemSelect='cityes';

		/**
		 * Передан ли номер страницы
		 */
		if (preg_match("/^page(\d+)$/i",$sPage,$aMatch)) {
			$iPage=$aMatch[1];
		} else {
			$iPage=1;
		}

		$aResult=$this->Topic_GetCorporativeTopics($iPage,Config::Get('module.topic.per_page'));
		$aTopics=$aResult['collection'];
		$aPaging=$this->Viewer_MakePaging($aResult['count'],$iPage,Config::Get('module.topic.per_page'),4,Router::GetPath('cityes').$sCityUrl);

		/**
		 * Загружаем переменные в шаблон
		 */
		$this->Viewer_Assign('aPaging',$aPaging);
		$this->Viewer_Assign('aTopics',$aTopics);
		$this->Viewer_AddHtmlTitle($this->Lang_Get('plugin.city.city_title_view_blogs'));
		$this->SetTemplateAction('blogs');
	}

	public function EventShutdown() {

		if ($this->oUserCurrent){
			// Проверяем может ли пользователь добавить компанию, нужно для меню
			$bCanAddCity=$this->ACL_CanCreateCity($this->oUserCurrent);
			$this->Viewer_Assign('bCanAddCity',$bCanAddCity);
			$this->Viewer_AddBlock('right','cityadd', array('plugin' => 'city'), 110);
			// по сути нужно переделать в функцию, чтобы получать компании которые нельзя показывать, т.е. не активированные.
			$this->Viewer_Assign('iCountFavCityes',$this->PluginCity_City_GetCountFavCityesByUser($this->oUserCurrent->getId()));

			//при завершении обновляем значение переменной компаний пользователя
			$this->aUserCity = $this->PluginCity_City_GetCityesByUser($this->oUserCurrent->getId(),true);
			$this->Viewer_Assign('aUserCity',$this->aUserCity);
			// если только одна компания то выводим прямую ссылку на нее и количество заявок
			if (count($this->aUserCity) == 1){
				$oUserCity = reset($this->aUserCity);
				$this->Viewer_Assign('oUserCity',$oUserCity);
				$this->Viewer_Assign('iCountTender',$this->PluginCity_City_GetCountTender($oUserCity));
			}
			if (Config::Get('module.city.use_activate') and $this->oUserCurrent->isAdministrator())
				$this->Viewer_Assign('iCountModeration',$this->PluginCity_City_GetCountCityesByFilter(array('active' => 0)));
		}
		$this->Viewer_Assign('sMenuHeadItemSelect',$this->sMenuHeadItemSelect);
		$this->Viewer_Assign('sMenuItemSelect',$this->sMenuItemSelect);
		$this->Viewer_Assign('sMenuSubItemSelect',$this->sMenuSubItemSelect);
		$this->Viewer_Assign('sCityTemplatePath',$this->getTemplatePathPlugin());
		$this->Viewer_Assign('iCountCityes',$this->PluginCity_City_GetCountCityesByFilter());
		$this->Viewer_Assign('iCountNewCityes',$this->PluginCity_City_GetCountCityesByFilter(array('new_time' => Config::Get('module.city.new_time'))));

		/**
		 * Подсчитываем новые топики
		 */
		$iCountTopicsCollectiveNew=$this->Topic_GetCountTopicsCollectiveNew();
		$iCountTopicsPersonalNew=$this->Topic_GetCountTopicsPersonalNew();
		$iCountTopicsCityNew=$this->Topic_GetCountTopicsCorporativeNew();
		$iCountTopicsNew=$iCountTopicsCollectiveNew+$iCountTopicsPersonalNew+$iCountTopicsCityNew;
		$this->Viewer_Assign('iCountTopicsCollectiveNew',$iCountTopicsCollectiveNew);
		$this->Viewer_Assign('iCountTopicsPersonalNew',$iCountTopicsPersonalNew);
		$this->Viewer_Assign('iCountTopicsCityNew',$iCountTopicsCityNew);
		$this->Viewer_Assign('iCountTopicsNew',$iCountTopicsNew);
	}

}
?>
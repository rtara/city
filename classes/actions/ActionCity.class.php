<?php
/**
 *	Module "City"
 *	Author: Grebenkin Anton
 *	Contact e-mail: 4freework@gmail.com
 */

class PluginCity_ActionCity extends ActionPlugin {
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
	/**
	 * Текущий пользователь
	 */
	protected $oUserCurrent=null;
	protected $aUserCity=null;
	protected $aBadCityUrl = array('new','good','bad','edit','add','admin','vacancies','feedbacks','blog','page','rss','city','test','user','my','fav','ajax','activate','deactivate','settings');
	protected $aTypes;

	/**
	 * Инициализация
	 */
	public function Init() {
		$this->SetDefaultEvent('');
		$this->oUserCurrent = $this->User_GetUserCurrent();
		$this->aTypes = Config::Get('module.city.types');

		/**
		 * Загружаем в шаблон JS текстовки
		 */
		$this->Lang_AddLangJs(array(
			'plugin.city.city_photo_photo_delete','plugin.city.city_photo_mark_as_preview','plugin.city.city_photo_photo_delete_confirm',
			'plugin.city.city_photo_is_preview','plugin.city.city_photo_upload_choose'
		));
	}

	/**
	 * Регистрация событий
	 */
	protected function RegisterEvent() {
		$this->AddEvent('add','EventAddCity');
		$this->AddEvent('edit','EventEditCity');
		//$this->AddEvent('admin','EventAdminCity');
		$this->AddEvent('delete','EventDelete');
		$this->AddEvent('test','EventTest');
		$this->AddEvent('activate','EventActivate');
		$this->AddEvent('deactivate','EventActivate');

		// настройки
		$this->AddEventPreg('/^settings$/i','/^repair$/i','EventSettingsRepair');
		$this->AddEventPreg('/^settings$/i','/^convert$/i','EventSettingsConvert');
		$this->AddEventPreg('/^settings$/i','/^convertgeo$/i','EventSettingsConvertGeo');
		$this->AddEventPreg('/^settings$/i','/^update$/i','EventSettingsUpdate');
		$this->AddEvent('settings','EventSettings');

		//$this->AddEventPreg('/^user$/i','/^[\w\-\_]+$/i','/^feedbacks$/i','/^(page([1-9]\d{0,5}))?$/i','EventShowUserFeedbacks');
		$this->AddEventPreg('/^(page([1-9]\d{0,5}))?$/i','EventShowCityes');

		$this->AddEventPreg('/^[\w\-\_]+$/i','/^(\d+)\.html$/i', array('EventShowTopic','topic'));
		$this->AddEventPreg('/^[\w\-\_]+$/i','/^$/i', array('EventShowCityProfile','profile'));
		$this->AddEventPreg('/^[\w\-\_]+$/i','/^blog$/i','/^(\d+)\.html$/i', array('EventShowTopic','topic'));
		$this->AddEventPreg('/^[\w\-\_]+$/i','/^blog$/', array('EventShowCityBlog','blog'));
		$this->AddEventPreg('/^[\w\-\_]+$/i','/^vacancies$/', array('EventShowCityVacancies','vacancies'));
		$this->AddEventPreg('/^[\w\-\_]+$/i','/^feedbacks$/', array('EventShowCityFeedbacks','feedbacks'));
		$this->AddEventPreg('/^[\w\-\_]+$/i','/^fans$/i','/^(page([1-9]\d{0,5}))?$/i', array('EventShowCityFans','fans'));
		$this->AddEventPreg('/^[\w\-\_]+$/i','/^rss$/','RssCityBlog');

		$this->AddEventPreg('/^ajax$/i','/^joinleavecity$/','AjaxCityBlogJoinLeave');
		$this->AddEventPreg('/^ajax$/i','/^streamfeedback$/','AjaxStreamFeedback');
		$this->AddEventPreg('/^ajax$/i','/^addfeedback$/','AjaxAddFeedback');
		$this->AddEventPreg('/^ajax$/i','/^responsefeedback$/','AjaxResponseFeedback');
		$this->AddEventPreg('/^ajax$/i','/^feedbackbad$/','AjaxFeedbackBad');
		$this->AddEventPreg('/^ajax$/i','/^votecity$/','AjaxVoteCity');
		$this->AddEventPreg('/^ajax$/i','/^favourite$/','AjaxFavouriteCity');
		$this->AddEventPreg('/^ajax$/i','/^autocompleter$/i','/^tag$/','AjaxAutocompleterTag');
		$this->AddEventPreg('/^ajax$/i','/^autocompleter$/i','/^city$/','AjaxAutocompleterCity');
		$this->AddEventPreg('/^ajax$/i','/^subscribe-toggle$/','AjaxSubscribeToggle');
		// работа с виджетами
		$this->AddEventPreg('/^ajax$/i','/^update-twitter$/','AjaxUpdateTwitterAccount'); // Сохраняет значения виджета twitter
		$this->AddEventPreg('/^ajax$/i','/^update-fb$/','AjaxUpdateFbAccount'); // Сохраняет значения виджета facebook
		$this->AddEventPreg('/^ajax$/i','/^update-vk$/','AjaxUpdateVkAccount'); // Сохраняет значения виджета вконтакте
		$this->AddEventPreg('/^ajax$/i','/^switch-widget-visible$/','AjaxSwitchWidgetVisible');// Изменяет отображение виджета
		// работа с фото
		$this->AddEventPreg('/^ajax$/i','/^photo-delete$/','AjaxPhotoDelete');// Удаление изображения
		$this->AddEventPreg('/^ajax$/i','/^photo-upload$/','AjaxPhotoUpload'); // Загрузка изображения
		$this->AddEventPreg('/^ajax$/i','/^photo-get-more$/','AjaxPhotoGetMore');// Загрузка изображения на сервер
		$this->AddEventPreg('/^ajax$/i','/^photo-set-description$/','AjaxPhotoSetDescription'); // Установка описания к фото

		$this->AddEventPreg('/^ajax$/i','/^tariff-update$/','AjaxUpdateTariff'); // Обновление тарифа


	}


	/**********************************************************************************
	 ************************ РЕАЛИЗАЦИЯ ЭКШЕНА ***************************************
	 **********************************************************************************
	 */

	/**
	 * Создает новую компанию
	 *
	 * @return unknown
	 */
	protected function EventAddCity() {
		$this->sMenuSubItemSelect='add';
		$sType = $this->GetParam(0);
		/**
		 * Проверяем авторизован ли пользователь
		 */
		if (!$this->User_IsAuthorization()) {
			$this->Message_AddErrorSingle($this->Lang_Get('not_access'),$this->Lang_Get('error'));
			return Router::Action('error');
		}
		/**
		 * Проверяем хватает ли рейтинга пользователю чтобы зарегистрировать компанию
		 */
		if (!$this->ACL_CanCreateCity($this->oUserCurrent) and !$this->oUserCurrent->isAdministrator()) {
			$this->Message_AddErrorSingle($this->Lang_Get('plugin.city.city_error_lowrating'),$this->Lang_Get('error'));
			return Router::Action('error');
		}

		$aCountries = $this->Geo_GetCountries(array(),array('sort'=>'asc'),1,300);
		$this->Viewer_Assign('aGeoCountries',$aCountries['collection']);
		$this->Viewer_AddHtmlTitle($this->Lang_Get('plugin.city.city_title_add'));

		$aCategories=$this->PluginCity_City_GetCityTags(30);
		//$aSelected = $this->PluginCity_City_GetCityTagsByCityId($oCity->getId());
		$this->Viewer_Assign('aCategories',$aCategories);
		$this->Viewer_Assign('aSelected',array());
		/**
		 * Если нажали кнопку "Зарегистрировать"
		 */
		if (isPost('submit_add_city')) {
			$oCity = Engine::GetEntity('PluginCity_City_City');
			$oCity->_setValidateScenario('add');
			$oCity->setOwnerId($this->oUserCurrent->getId());
			$oCity->setName(getRequest('city_name'));
			if ($sType == '' or !in_array($sType,$this->aTypes))
				$oCity->setType('base');
			else
				$oCity->setType($sType);
			/**
			 * Проверяем URL компании, с заменой всех пробельных символов на "_"
			 */
			if (Config::Get('module.city.use_convert_url')){
				$cityUrl = func_translit_url((string)getRequest('city_name'));
			} else{
				$cityUrl = (string)getRequest('city_url');
			}

			$cityUrl=preg_replace("/\s+/",'_',$cityUrl);
			$_REQUEST['city_url']= mb_strtolower($cityUrl);

			$oCity->setUrl((string)getRequest('city_url'));

			$sText=$this->Text_Parser(getRequest('city_description'));
			$oCity->setDescription($sText);
			if (!$this->oUserCurrent->isAdministrator() and Config::Get('module.city.use_category') and is_array(getRequest('category')))
				$oCity->setTags(implode(",", getRequest('category')));
			else
				$oCity->setTags(getRequest('city_tags'));
			/**
			 * Определяем гео-объект
			 */
			if (getRequest('geo_city')) {
				$oGeoObject=$this->Geo_GetGeoObject('city',getRequest('geo_city'));
			} elseif (getRequest('geo_country')) {
				$oGeoObject=$this->Geo_GetGeoObject('country',getRequest('geo_country'));
			} else {
				$oGeoObject=null;
			}
			// Если вдруг будут ошибки заполняем города и регионы, чтобы не слетали
			if ($oGeoObject) {
				$this->Viewer_Assign('oGeoTarget',$oGeoObject);
				if ($oGeoObject->getCountryId()) {
					$aRegions=$this->Geo_GetRegions(array('country_id'=>$oGeoObject->getCountryId()),array('sort'=>'asc'),1,500);
					$this->Viewer_Assign('aGeoRegions',$aRegions['collection']);
				}
				if ($oGeoObject->getRegionId()) {
					$aCities=$this->Geo_GetCities(array('region_id'=>$oGeoObject->getRegionId()),array('sort'=>'asc'),1,500);
					$this->Viewer_Assign('aGeoCities',$aCities['collection']);
				}
			}

			if ($oGeoObject) {
				if ($oCountry=$oGeoObject->getCountry()) {
					$oCity->setCountry($oCountry->getName());
				} else {
					$oCity->setCountry(null);
				}

				if ($oCity=$oGeoObject->getCity()) {
					$oCity->setCity($oCity->getName());
				} else {
					$oCity->setCity(null);
				}
			} else {
				$oCity->setCountry(null);
				$oCity->setCity(null);
			}
			$oCity->setDateAdd(date("Y-m-d H:i:s"));


			if (!$this->checkCityFields($oCity)){
				return false;
			}
			/**
			 * Создаём компанию
			 */
			if ($this->PluginCity_City_AddCity($oCity)) {
				$this->PluginCity_City_UpdateCityTags($oCity);
				if ($oGeoObject)
					$this->Geo_CreateTarget($oGeoObject,'city',$oCity->getId());
				func_header_location(Router::GetPath('city').'edit/'.$oCity->getId().'/profile/');
			} else {
				$this->Message_AddError($this->Lang_Get('system_error'));
			}
		}


		$this->Viewer_AddBlock('right','cityes',array('plugin'=>'city'));
		if ($sType == '' or !in_array($sType,$this->aTypes))
			$this->SetTemplateAction('add');
		else
			$this->SetTemplateAction('add_'.$sType);
	}

	/**
	 * Редактирование компании
	 *
	 * @return unknown
	 */
	protected function EventEditCity() {
		$sPage=$this->GetParam(1);
		/**
		 * Меню
		 */
		$this->sMenuSubItemSelect='';
		$this->sMenuItemSelect=$sPage;
		/**
		 * Проверям авторизован ли пользователь
		 */
		if (!$this->User_IsAuthorization()) {
			$this->Message_AddErrorSingle($this->Lang_Get('not_access'),$this->Lang_Get('error'));
			return Router::Action('error');
		}
		/**
		 * Проверяем передан ли в УРЛе ИД компании
		 */
		$sCityId=$this->GetParam(0);
		if (!$oCity=$this->PluginCity_City_GetCityById($sCityId)) {
			return parent::EventNotFound();
		}


		/**
		 * Явлется ли авторизованный пользователь хозяином компании, либо ее администратором
		 */
		if (!$this->ACL_CanEditCity($this->oUserCurrent,$oCity)) {
			return parent::EventNotFound();
		}
		switch ($sPage){
			case 'profile':
				$this->EditCityProfile($oCity);
				break;
			case 'contacts':
				$this->EditCityContacts($oCity);
				break;
			case 'admin':
				$this->EditCityAdmin($oCity);
				break;
			case 'branding':
				$this->EditCityBranding($oCity);
				break;
			case 'widgets':
				$this->EditCityWidgets($oCity);
				break;
			case 'photo':
				$this->EditCityPhoto($oCity);
				break;
			default:
				return parent::EventNotFound();
		}

		$this->Viewer_Assign('oCity',$oCity);

		$this->Viewer_AddMenu('city_edit',Plugin::GetTemplatePath('city').'menu.city_edit.tpl');
		$this->Viewer_AddBlock('right','cityes',array('plugin'=>'city'));
		$this->Viewer_AddHtmlTitle($oCity->getName());
		$this->Viewer_AddHtmlTitle($this->Lang_Get('plugin.city.city_title_edit'));
	}

	protected function EditCityProfile(PluginCity_ModuleCity_EntityCity $oCity){
		$aStaff = $this->PluginCity_CityStaff_GetCityStaffItemsByCityId($oCity->getId(),array('#cache' => array("city_staff_{$oCity->getId()}",array(""))));
		$aCategories=$this->PluginCity_City_GetCityTags(30);
		$aSelected = $this->PluginCity_City_GetCityTagsByCityId($oCity->getId());
		$this->Viewer_Assign('aStaff',$aStaff);
		$this->Viewer_Assign('aCategories',$aCategories);
		$this->Viewer_Assign('aSelected',$aSelected);
		$this->SetTemplateAction('edit');

		if (isPost('submit_edit_city') and $this->FillProfile($oCity)) {

			if ($this->PluginCity_City_UpdateCity($oCity)) {
				$this->PluginCity_City_UpdateCityTags($oCity);
				$this->Message_AddNoticeSingle($this->Lang_Get('plugin.city.city_notice_edit_profile'),$this->Lang_Get('attention'));
			} else {
				$this->Message_AddErrorSingle($this->Lang_Get('system_error'));
			}
		}
	}

	protected function EditCityContacts(PluginCity_ModuleCity_EntityCity $oCity){
		$this->SetTemplateAction('edit_contacts');
		/**
		 * Выбор города и страны
		 */
		$oGeoTarget=$this->Geo_GetTargetByTarget('city',$oCity->getId());
		$this->Viewer_Assign('oGeoTarget',$oGeoTarget);
		$aCountries=$this->Geo_GetCountries(array(),array('sort'=>'asc'),1,300);
		$this->Viewer_Assign('aGeoCountries',$aCountries['collection']);
		if ($oGeoTarget) {
			if ($oGeoTarget->getCountryId()) {
				$aRegions=$this->Geo_GetRegions(array('country_id'=>$oGeoTarget->getCountryId()),array('sort'=>'asc'),1,500);
				$this->Viewer_Assign('aGeoRegions',$aRegions['collection']);
			}
			if ($oGeoTarget->getRegionId()) {
				$aCities=$this->Geo_GetCities(array('region_id'=>$oGeoTarget->getRegionId()),array('sort'=>'asc'),1,500);
				$this->Viewer_Assign('aGeoCities',$aCities['collection']);
			}
		}


		if (isPost('submit_edit_city') and $this->FillContacts($oCity)) {
			if ($this->PluginCity_City_UpdateCity($oCity)) {
				$this->Message_AddNoticeSingle($this->Lang_Get('plugin.city.city_notice_edit_profile'),$this->Lang_Get('attention'));
			} else {
				$this->Message_AddErrorSingle($this->Lang_Get('system_error'));
			}
		}
	}

	protected function EditCityBranding(PluginCity_ModuleCity_EntityCity $oCity){
		$this->SetTemplateAction('edit_branding');


		if (isPost('submit_edit_city')) {

			if (isset($_FILES['background']) and is_uploaded_file($_FILES['background']['tmp_name'])) {
				$this->PluginCity_Content_DeleteBackground($oCity);
				if (!$this->PluginCity_Content_UploadBackground($_FILES['background'], $oCity))
					return false;
			}

			if ($oCity->getBrandImage() && getRequest('use_branding')){
				$oCity->setUseBrandImage(1);
			} else{
				$oCity->setUseBrandImage(0);
			}
			// если цвет введен верно, ставим его если нет, ставим цвет по умолчанию
			if (func_check(getRequest('bg_color'), 'text', 7, 7)) {
				$oCity->setBackgroundColor(getRequest('bg_color'));
			} else {
				$oCity->setBackgroundColor('#fbfcfc');
			}

			if ($this->PluginCity_City_UpdateCityPrefs($oCity)) {
				func_header_location($oCity->getUrlFull());
				$this->Message_AddNoticeSingle($this->Lang_Get('plugin.city.city_notice_edit_profile'),$this->Lang_Get('attention'));
			} else {
				$this->Message_AddErrorSingle($this->Lang_Get('system_error'));
			}
		}
	}

	protected function EditCityWidgets(PluginCity_ModuleCity_EntityCity $oCity){
		$this->SetTemplateAction('edit_widgets');
		$oCity->setDateEdit(date("Y-m-d H:i:s"));
		$oCity->getBrandImage() && getRequest('use_branding') ? $oCity->setUseBrandImage(1) : $oCity->setUseBrandImage(0);
		$_REQUEST['screen_name']= $oCity->getTwitterScreenName();
		$_REQUEST['facebook_url']= $oCity->getFbUrl();
		$_REQUEST['vk_url']= $oCity->getVkUrl();
	}

	protected function EditCityPhoto(PluginCity_ModuleCity_EntityCity $oCity){
		$this->SetTemplateAction('edit_photo');
		$_REQUEST['city_id']=$oCity->getId();
		/**
		 * Проверяем отправлена ли форма с данными(хотяб одна кнопка)
		 */
		if (isPost('submit_edit_city')) {
			$aPhotos = $oCity->getPhotos();
			if (!($oPhotoMain=$this->PluginCity_Content_GetCityPhotoById(getRequestStr('main_photo')) and $oPhotoMain->getCityId()==$oCity->getId())) {
				if (isset($aPhotos[0])){
					$oPhotoMain=$aPhotos[0];
				}
			}
			if (count($aPhotos)>0)
				$oCity->setMainPhotoId($oPhotoMain->getId());
			else
				$oCity->setMainPhotoId(0);
			$oCity->setPhotoCount(count($aPhotos));
			$this->PluginCity_City_UpdateCityPrefs($oCity);
		} else {
			$_REQUEST['city_id']=$oCity->getId();
			$_REQUEST['main_photo']=$oCity->getMainPhotoId();
		}
		$this->Viewer_Assign('aPhotos', $this->PluginCity_Content_GetPhotosByCityId($oCity->getId()));
	}

	/**
	 * Администрирование компании
	 *
	 * @return unknown
	 */
	protected function EditCityAdmin(PluginCity_ModuleCity_EntityCity $oCity) {
		$oBlog = $this->Blog_GetBlogById($oCity->getBlogId());
		/**
		 * Обрабатываем сохранение формы
		 */
		if (isPost('submit_city_admin')) {
			$aUserRank=getRequest('user_rank',array());
			if (!is_array($aUserRank)) {
				$aUserRank=array();
			}
			foreach ($aUserRank as $sUserId => $sRank) {
				if (!($oBlogUser=$this->Blog_GetBlogUserByBlogIdAndUserId($oBlog->getId(),$sUserId))) {
					$this->Message_AddError($this->Lang_Get('system_error'),$this->Lang_Get('error'));
					break;
				}

				switch ($sRank) {
					case 'administrator':
						$oBlogUser->setUserRole(ModuleBlog::BLOG_USER_ROLE_ADMINISTRATOR);
						break;
					case 'moderator':
						$oBlogUser->setUserRole(ModuleBlog::BLOG_USER_ROLE_MODERATOR);
						break;
					case 'employee':
						$oBlogUser->setUserRole(ModuleBlog::BLOG_USER_ROLE_USER);
						break;
					case 'reject':
						$this->Talk_SendTalk($this->Lang_Get('plugin.city.city_notice_talk_reject_title'), $this->Lang_Get('plugin.city.city_notice_talk_reject_text').$oCity->getName()." ".$this->Lang_Get('plugin.city.city_notice_talk_reject_title'),$this->oUserCurrent,$this->User_GetUserById($oBlogUser->getUserId()),false);
						$oBlog->setCountUser($oBlog->getCountUser()-1);
						$this->Blog_DeleteRelationBlogUser($oBlogUser);
						break;
					default:
						$oBlogUser->setUserRole(ModuleBlog::BLOG_USER_ROLE_GUEST);
				}
				$this->Blog_UpdateRelationBlogUser($oBlogUser);
				$this->Message_AddNoticeSingle($this->Lang_Get('plugin.city.city_notice_edit_users'));
			}
			$this->Cache_Clean(Zend_Cache::CLEANING_MODE_MATCHING_TAG,array('city_update',"city_update_{$oCity->getId()}"));
			$oBlog->setUrl('city_city_'.$oBlog->getUrl());
			$this->Blog_UpdateBlog($oBlog);
		}

		/**
		 * Получаем список подписчиков блога
		 */
		$aBlogUsers=$this->Blog_GetBlogUsersByBlogId(
			$oBlog->getId(),
			array(
				ModuleBlog::BLOG_USER_ROLE_GUEST,
				ModuleBlog::BLOG_USER_ROLE_USER,
				ModuleBlog::BLOG_USER_ROLE_MODERATOR,
				ModuleBlog::BLOG_USER_ROLE_ADMINISTRATOR
			)
		);

		$this->Viewer_Assign('oBlog',$oBlog);
		$this->Viewer_Assign('aBlogUsers',$aBlogUsers['collection']);

		$this->Viewer_Assign('BLOG_USER_ROLE_GUEST', ModuleBlog::BLOG_USER_ROLE_GUEST);
		$this->Viewer_Assign('BLOG_USER_ROLE_USER', ModuleBlog::BLOG_USER_ROLE_USER);
		$this->Viewer_Assign('BLOG_USER_ROLE_MODERATOR', ModuleBlog::BLOG_USER_ROLE_MODERATOR);
		$this->Viewer_Assign('BLOG_USER_ROLE_ADMINISTRATOR', ModuleBlog::BLOG_USER_ROLE_ADMINISTRATOR);

		if (isPost('submit_city_own')) {
			if ($oBlog->getOwnerId() == $this->oUserCurrent->getId() or $this->oUserCurrent->isAdministrator()){
				$sUsers=getRequest('own_user');
				$aUsers=explode(',',$sUsers);
				//берем первого пользователя
				$sUser=$aUsers[0];
				if ($oUser=$this->User_GetUserByLogin($sUser)){
					$this->PluginCity_City_ReplaceCityOwner($oBlog, $oUser);
					$this->Cache_Delete("city_{$oCity->getId()}");
					$this->Message_AddNoticeSingle($this->Lang_Get('plugin.city.city_notice_edit_users_owner'));
					func_header_location($oCity->getUrlFull());
				} else {
					$this->Message_AddError($this->Lang_Get('talk_create_users_error_not_found').' «'.htmlspecialchars($sUser).'»',$this->Lang_Get('error'));
				}
				$_REQUEST['talk_users']	= $sUser;
				$this->Cache_Clean(Zend_Cache::CLEANING_MODE_MATCHING_TAG,array('city_update',"city_update_{$oCity->getId()}"));
			} else{
				$this->Message_AddErrorSingle($this->Lang_Get('not_access'),$this->Lang_Get('error'));
			}
		}

		$this->Viewer_AddHtmlTitle($oCity->getName());
		$this->Viewer_AddHtmlTitle($this->Lang_Get('plugin.city.city_title_edit_users'));
		$this->Viewer_Assign('oCity',$oCity);
		/**
		 * Устанавливаем шалон для вывода
		 */
		$this->SetTemplateAction('edit_admin');
	}


	/**
	 * Заполняется страница профиля
	 *
	 * @param $oCity
	 */
	protected function FillProfile(PluginCity_ModuleCity_EntityCity $oCity) {
		$oCity->_setValidateScenario('edit');
		/**
		 * Удалить логотип
		 */
		if (isset($_REQUEST['logo_delete'])) {
			$this->PluginCity_Content_DeleteLogo($oCity);
			$oCity->setLogo(0);
			$oCity->setLogoType(null);
		}
		/*
		 * Загрузка файла
		 */
		if (is_uploaded_file($_FILES['city_file']['tmp_name'])) {
			$this->PluginCity_Content_DeleteFile($oCity);
			$this->PluginCity_Content_UploadFile($_FILES['city_file'], $oCity);
		}
		/*
		 * Удаление файла
		 */
		if (isset($_REQUEST['file_delete'])) {
			$this->PluginCity_Content_DeleteFile($oCity);
			$oCity->setFileName(null);
		}

		$oCity->setName(strip_tags(getRequest('city_name')));
		$oCity->setLegalName(strip_tags(getRequest('city_name_legal')));
		$oCity->setDescription(strip_tags(getRequest('city_description')));
		$oCity->setAboutSource(getRequest('city_about'));
		$oCity->setAbout($this->Text_Parser(getRequest('city_about')));
		/**
		 * Проверяем дату основания
		 */
		if (func_check(getRequest('city_basis_day'), 'id', 1, 2) and func_check(getRequest('city_basis_month'), 'id', 1, 2) and func_check(getRequest('city_basis_year'), 'id', 4, 4)) {
			$oCity->setDateBasis(date("Y-m-d H:i:s", mktime(0, 0, 0, getRequest('city_basis_month'), getRequest('city_basis_day'), getRequest('city_basis_year'))));
		} else {
			$oCity->setDateBasis(null);
		}
		$oCity->setVacancies($this->Text_Parser(getRequest('city_vacancies')));

		if (!$this->oUserCurrent->isAdministrator() and Config::Get('module.city.use_category') and is_array(getRequest('category')))
			$oCity->setTags(implode(",", getRequest('category')));
		else
			$oCity->setTags(getRequest('city_tags'));

		$oCity->setCountWorkers(getRequest('city_count_workers'));

		// Заполняем руководство
		$this->PluginCity_CityStaff_DeleteStaffByCityId($oCity->getId());
		$aStaffs=getRequest('staff',array());
		foreach ($aStaffs as $oStaff){
			$oStaff = Engine::GetEntity('PluginCity_ModuleCityStaff_EntityCityStaff',$oStaff);
			// если данные заполнены не верно, ошибку не выводим и данные не сохраняем.
			if (func_check($oStaff->getStaffName(),'text',2,100) and func_check($oStaff->getStaffPosition(),'text',2,100))
				$oStaff->Add();
		}

		/*
			 * Загрузка логотипа
			 */
		if (isset($_FILES['logo']) and is_uploaded_file($_FILES['logo']['tmp_name'])) {
			$this->PluginCity_Content_DeleteLogo($oCity);
			if (!$this->PluginCity_Content_UploadLogo($_FILES['logo'], $oCity))
				return false;
		}
		if (!is_array(getRequest('category')) and Config::Get('module.city.use_category') and !$this->oUserCurrent->isAdministrator()){
			$this->Message_AddError($this->Lang_Get('plugin.city.city_error_edit_tags'),$this->Lang_Get('error'));
			return false;
		}
		if (!$this->checkCityFields($oCity)) {
			return false;
		}

		$oCity->setDateEdit(date("Y-m-d H:i:s"));
		return true;

	}

	/**
	 * Заполняется страница с контактами
	 *
	 * @param $oCity
	 */
	protected function FillContacts(PluginCity_ModuleCity_EntityCity $oCity) {
		$oCity->_setValidateScenario('contacts');
		$oCity->setSite(getRequest('city_site'));
		$oCity->setEmail(getRequest('city_email'));
		$oCity->setPhone(getRequest('city_phone'));
		$oCity->setFax(getRequest('city_fax'));
		$oCity->setSkype(getRequest('city_skype'));
		$oCity->setIcq(getRequest('city_icq'));
		$oCity->setContactName(getRequest('city_contact_name'));
		$oCity->setContactInfo(getRequest('city_contact_info'));
		$oCity->setLatitude(getRequest('map_marker_latitude'));
		$oCity->setLongitude(getRequest('map_marker_longitude'));
		$oCity->setAddress(getRequest('city_address'));
		/**
		 * Определяем гео-объект
		 */
		if (getRequest('geo_city')) {
			$oGeoObject=$this->Geo_GetGeoObject('city',getRequest('geo_city'));
		} elseif (getRequest('geo_country')) {
			$oGeoObject=$this->Geo_GetGeoObject('country',getRequest('geo_country'));
		} else {
			$oGeoObject=null;
		}

		if ($oGeoObject) {
			$this->Geo_CreateTarget($oGeoObject,'city',$oCity->getId());
			if ($oCountry=$oGeoObject->getCountry()) {
				$oCity->setCountry($oCountry->getName());
			} else {
				$oCity->setCountry(null);
			}
			if ($oCity=$oGeoObject->getCity()) {
				$oCity->setCity($oCity->getName());
			} else {
				$oCity->setCity(null);
			}
		} else {
			$this->Geo_DeleteTargetsByTarget('city',$oCity->getId());
			$oCity->setCountry(null);
			$oCity->setCity(null);
		}

		if (!$this->checkCityFields($oCity)) {
			return false;
		}

		$oCity->setDateEdit(date("Y-m-d H:i:s"));
		return true;
	}


	/**
	 * Показать профиль компании
	 * @return unknown
	 */
	protected function EventShowCityProfile() {
		$sCityUrl=$this->sCurrentEvent;
		$this->sMenuItemSelect='index';
		$this->sMenuSubItemSelect='index';

		/**
		 * Проверяем есть ли компания с таким УРЛ
		 */
		if (!($oCity=$this->PluginCity_City_GetCityByUrl($sCityUrl))) {
			return parent::EventNotFound();
		}

		if (!$this->ACL_CanViewCity($this->oUserCurrent,$oCity)) {
			$this->Message_AddErrorSingle($this->Lang_Get('plugin.city.city_error_not_active'),$this->Lang_Get('error'));
			return Router::Action('error');
		}

		// Получаем данные о сотрудниках компании
		$aCityEmpl=$this->Blog_GetBlogUsersByBlogId(
			$oCity->getBlogId(),
			array(
				ModuleBlog::BLOG_USER_ROLE_USER,
				ModuleBlog::BLOG_USER_ROLE_MODERATOR,
				ModuleBlog::BLOG_USER_ROLE_ADMINISTRATOR
			)
		);
		$this->Viewer_Assign('aCityEmpl',$aCityEmpl['collection']);
		/**
		 * Выставляем SEO данные
		 */
		$sTextSeo=preg_replace("/<.*>/Ui",' ',$oCity->getDescription());
		$this->Viewer_SetHtmlDescription(func_text_words($sTextSeo,20));
		$this->Viewer_SetHtmlKeywords($oCity->getTags());
		$aStaff = $this->PluginCity_CityStaff_GetCityStaffItemsByCityId($oCity->getId(),array('#cache' => array("city_staff_{$oCity->getId()}",array(""))));
		$this->Viewer_Assign('aStaff',$aStaff);
		/**
		 * Загружаем переменные в шаблон
		 */
		$this->Viewer_AddHtmlTitle($oCity->getName());
		$this->Viewer_AddHtmlTitle($this->Lang_Get('plugin.city.city_title_view_profile'));
		$this->CityHeaderVariables($oCity);
		$this->SetTemplateAction('city_profile');
	}

	/**
	 * Отображает блог компании
	 *
	 * @return unknown
	 */
	protected function EventShowCityBlog() {
		$sCityUrl=$this->sCurrentEvent;
		$sPage=$this->GetParam(1);
		$this->sMenuItemSelect = 'blog';
		$this->sMenuSubItemSelect = 'good';
		$this->sMenuSubCityUrl = Router::GetPath('city').$sCityUrl;
		/**
		 * Проверяем есть ли компания с таким УРЛ
		 */
		if (!($oCity=$this->PluginCity_City_GetCityByUrl($sCityUrl))) {
			return parent::EventNotFound();
		}

		if (!$this->ACL_CanViewCity($this->oUserCurrent,$oCity)) {
			$this->Message_AddErrorSingle($this->Lang_Get('plugin.city.city_error_not_active'),$this->Lang_Get('error'));
			return Router::Action('error');
		}

		/**
		 * Передан ли номер страницы
		 */
		if (preg_match("/^page(\d+)$/i",$sPage,$aMatch)) {
			$iPage=$aMatch[1];
		} else {
			$iPage=1;
		}
		/**
		 * Получаем список топиков
		 */
		$aResult=$this->Topic_GetTopicsByBlog($oCity->getBlog(),$iPage,Config::Get('module.topic.per_page'));
		$aTopics=$aResult['collection'];
		$aPaging=$this->Viewer_MakePaging($aResult['count'],$iPage,Config::Get('module.topic.per_page'),4,Router::GetPath('city').$sCityUrl.'/blog');

		/**
		 * Выставляем SEO данные
		 */
		$sTextSeo=preg_replace("/<.*>/Ui",' ',$oCity->getDescription());
		$this->Viewer_SetHtmlDescription(func_text_words($sTextSeo,20));
		/**
		 * Загружаем переменные в шаблон
		 */
		$this->CityHeaderVariables($oCity);
		$this->Viewer_Assign('aPaging',$aPaging);
		$this->Viewer_Assign('aTopics',$aTopics);
		$this->Viewer_Assign('oBlog',$oCity->getBlog());
		$this->Viewer_AddHtmlTitle($this->Lang_Get('plugin.city.city_blog_prefix').' '.$oCity->getName());
		$this->SetTemplateAction('city_blog');
	}

	/**
	 * Отображает вакансии компании
	 *
	 * @return unknown
	 */
	protected function EventShowCityVacancies() {
		$sCityUrl=$this->sCurrentEvent;
		$this->sMenuItemSelect='vacancies';
		$this->sMenuSubItemSelect='';

		/**
		 * Проверяем есть ли компания с таким УРЛ
		 */
		if (!($oCity=$this->PluginCity_City_GetCityByUrl($sCityUrl))) {
			return parent::EventNotFound();
		}

		if (!$this->ACL_CanViewCity($this->oUserCurrent,$oCity)) {
			$this->Message_AddErrorSingle($this->Lang_Get('plugin.city.city_error_not_active'),$this->Lang_Get('error'));
			return Router::Action('error');
		}

		$aVacancies = array();
		if ($this->PluginCity_City_isActivePlugin('job') and Config::Get('module.city.use_jobs')){
			$aResult = $this->PluginJob_Job_GetVacanciesByCityId(1,10,$oCity->getId());
			$aVacancies=$aResult['collection'];
		}
		$this->Viewer_Assign('aVacancies',$aVacancies);

		$this->CityHeaderVariables($oCity);
		$this->Viewer_Assign('sJobTemplatePath',Plugin::GetTemplatePath('job'));
		$this->SetTemplateAction('city_vacancies');
	}


	/**
	 * Отображает отзывы о компании
	 *
	 * @return unknown
	 */
	protected function EventShowCityFeedbacks() {
		$sCityUrl=$this->sCurrentEvent;
		$this->sMenuItemSelect='feedbacks';

		/**
		 * Проверяем есть ли компания с таким УРЛ
		 */
		if (!($oCity=$this->PluginCity_City_GetCityByUrl($sCityUrl))) {
			return parent::EventNotFound();
		}

		if (!$this->ACL_CanViewCity($this->oUserCurrent,$oCity)) {
			$this->Message_AddErrorSingle($this->Lang_Get('plugin.city.city_error_not_active'),$this->Lang_Get('error'));
			return Router::Action('error');
		}
		$this->Viewer_AppendScript($this->getTemplatePathPlugin().'js/feedbacks.js');
		$aReturn=$this->Comment_GetCommentsByTargetId($oCity->getId(),'city');
		$iMaxIdComment=$aReturn['iMaxIdComment'];
		$aComments=$aReturn['comments'];

		$dDate=date("Y-m-d H:i:s");
		if ($this->oUserCurrent) {
			if ($oFeebackRead=$this->PluginCity_City_GetFeedbackRead($oCity->getId(),$this->oUserCurrent->getId())) {
				$dDate=$oFeebackRead->getDateRead();
			}
		}
		$oCity->setDateRead($dDate);
		/**
		 * Отмечаем дату прочтения отзывов
		 */
		if ($this->oUserCurrent) {
			$oFeebackRead= Engine::GetEntity('PluginCity_City_CityFeedbackRead');
			$oFeebackRead->setCityId($oCity->getId());
			$oFeebackRead->setUserId($this->oUserCurrent->getId());
			$oFeebackRead->setFeedbackCountLast($oCity->getCountFeedback());
			$oFeebackRead->setFeedbackIdLast($iMaxIdComment);
			$oFeebackRead->setDateRead(date("Y-m-d H:i:s"));
			$this->PluginCity_City_SetFeedbackRead($oFeebackRead);
		}
		$this->Viewer_Assign('aComments',$aComments);
		$this->Viewer_Assign('iMaxIdComment',$iMaxIdComment);
		$this->CityHeaderVariables($oCity);
		$this->Viewer_AddHtmlTitle($this->Lang_Get('plugin.city.city_feedbacks_title').' '.$oCity->getName());
		$this->SetTemplateAction('city_feedbacks');
	}

	/**
	 * Отображает фанов компании
	 *
	 * @return unknown
	 */
	protected function EventShowCityFans() {
		$sPage=$this->GetParam(1);
		if (preg_match("/^page(\d+)$/i",$sPage,$aMatch)) {
			$iPage=$aMatch[1];
		} else {
			$iPage=1;
		}
		$sCityUrl=$this->sCurrentEvent;
		$this->sMenuItemSelect='fans';

		/**
		 * Проверяем есть ли компания с таким УРЛ
		 */
		if (!($oCity=$this->PluginCity_City_GetCityByUrl($sCityUrl))) {
			return parent::EventNotFound();
		}
		if (!$this->ACL_CanViewCity($this->oUserCurrent,$oCity)) {
			$this->Message_AddErrorSingle($this->Lang_Get('plugin.city.city_error_not_active'),$this->Lang_Get('error'));
			return Router::Action('error');
		}
		$aFansId = $this->PluginCity_City_GetFavouriteUsersByTargerId($oCity->getId(),'city',$iPage,Config::Get('module.city.per_page'));
		$aFans = $this->User_GetUsersAdditionalData($aFansId);
		$aPaging=$this->Viewer_MakePaging($oCity->getCountFavourite(),$iPage,Config::Get('module.city.per_page'),Config::Get('pagination.pages.count'),Router::GetPath('city').$sCityUrl.'/fans');
		/**
		 * Загружаем переменные в шаблон
		 */
		$this->Viewer_Assign('aPaging',$aPaging);
		$this->Viewer_Assign('aFans',$aFans);
		$this->Viewer_AddHtmlTitle($oCity->getName());
		$this->Viewer_AddHtmlTitle($this->Lang_Get('plugin.city.city_title_view_fans'));
		$this->CityHeaderVariables($oCity);
		$this->SetTemplateAction('city_fans');
	}



	/**
	 * Отображает топик из корпоративного блога
	 *
	 * @return unknown
	 */
	protected function EventShowTopic() {
		$sCityUrl=$this->sCurrentEvent;
		$iTopicId=$this->GetParamEventMatch(1,1);
		/**
		 * Меню
		 */
		$this->sMenuItemSelect='blog';
		$this->sMenuSubItemSelect='';
		if (!($oCity=$this->PluginCity_City_GetCityByUrl($sCityUrl))) {
			return parent::EventNotFound();
		}

		if (!$this->ACL_CanViewCity($this->oUserCurrent,$oCity)) {
			$this->Message_AddErrorSingle($this->Lang_Get('plugin.city.city_error_not_active'),$this->Lang_Get('error'));
			return Router::Action('error');
		}
		/**
		 * Проверяем есть ли такой топик
		 */
		if (!($oTopic=$this->Topic_GetTopicById($iTopicId,null,-1))) {
			return parent::EventNotFound();
		}
		/**
		 * Проверяем права на просмотр топика
		 */
		if (!$oTopic->getPublish() and (!$this->oUserCurrent or ($this->oUserCurrent->getId()!=$oTopic->getUserId() and !$this->oUserCurrent->isAdministrator()))) {
			return parent::EventNotFound();
		}

		/**
		 * Обрабатываем добавление коммента
		 */
		$this->SubmitComment($oTopic);
		/**
		 * Достаём комменты к топику
		 */
		$aReturn=$this->Comment_GetCommentsByTargetId($oTopic->getId(),'topic');
		$iMaxIdComment=$aReturn['iMaxIdComment'];
		$aComments=$aReturn['comments'];
		$aCommentsNew=array();
		foreach ($aComments as $oCom) {
			$array=$oCom->_getData();
			$array['obj']=$oCom;
			$aCommentsNew[]=$array;
		}
		/**
		 * Проверяем находится ли топик в избранном у текущего юзера
		 */
		$bInFavourite=false;
		if ($this->oUserCurrent) {
			if ($this->Topic_GetFavouriteTopic($oTopic->getId(),$this->oUserCurrent->getId())) {
				$bInFavourite=true;
			}
		}
		/**
		 * Получаем дату прочтения топика
		 */
		$dDate=date("Y-m-d H:i:s");
		$iCommentLastTopicRead=0;
		if ($this->oUserCurrent) {
			if ($oTopicRead=$this->Topic_GetTopicRead($oTopic->getId(),$this->oUserCurrent->getId())) {
				$dDate=$oTopicRead->getDateRead();
				$iCommentLastTopicRead=$oTopicRead->getCommentIdLast();
			}
		}
		/**
		 * Отмечаем дату прочтения топика
		 */
		if ($this->oUserCurrent) {
			$oTopicRead= Engine::GetEntity('Topic_TopicRead');
			$oTopicRead->setTopicId($oTopic->getId());
			$oTopicRead->setUserId($this->oUserCurrent->getId());
			$oTopicRead->setCommentCountLast($oTopic->getCountComment());
			$oTopicRead->setCommentIdLast($iMaxIdComment);
			$oTopicRead->setDateRead(date("Y-m-d H:i:s"));
			$this->Topic_SetTopicRead($oTopicRead);
		}
		/**
		 * Вызов хуков
		 */
		$this->Hook_Run('topic_show',array("oTopic"=>$oTopic));
		/**
		 * Выставляем SEO данные
		 */
		$sTextSeo=preg_replace("/<.*>/Ui",' ',$oTopic->getText());
		$this->Viewer_SetHtmlDescription(func_text_words($sTextSeo,20));
		$this->Viewer_SetHtmlKeywords($oTopic->getTags());
		/**
		 * Загружаем переменные в шаблон
		 */
		$this->CityHeaderVariables($oCity);
		$this->Viewer_Assign('bInFavourite',$bInFavourite);
		$this->Viewer_Assign('dDateTopicRead',$dDate);
		$this->Viewer_Assign('iCommentLastTopicRead',$iCommentLastTopicRead);
		$this->Viewer_Assign('oTopic',$oTopic);
		$this->Viewer_Assign('aComments',$aComments);
		$this->Viewer_Assign('aCommentsNew',$aCommentsNew);
		$this->Viewer_Assign('iMaxIdComment',$iMaxIdComment);
		$this->Viewer_AddHtmlTitle($oTopic->getBlogTitle());
		$this->Viewer_AddHtmlTitle($oTopic->getTitle());
		$this->SetTemplateAction('topic');
	}

	/**
	 * Cписок компаний по рейтингу
	 *
	 * @return unknown
	 */
	protected function EventShowCityes() {
		// Обработка этого события перенесена в другой экшн, поэтому туда перенаправляем.
		func_header_location(Router::GetPath('cityes'));
	}

	/**
	 * Выводит список отзывов которые написал пользователь
	 *
	 * @return unknown
	 */
	protected function EventShowUserFeedbacks() {
		$this->Viewer_ClearBlocksAll();
		$this->Viewer_AddBlock('right','actions/ActionProfile/sidebar.tpl');
		/**
		 * Получаем логин из УРЛа
		 */
		$sUserLogin=$this->GetParam(0);
		/**
		 * Проверяем есть ли такой юзер
		 */
		if (!($oUserProfile=$this->User_GetUserByLogin($sUserLogin))) {
			return parent::EventNotFound();
		}
		/**
		 * Передан ли номер страницы
		 */
		$iPage=$this->GetParamEventMatch(2,2) ? $this->GetParamEventMatch(2,2) : 1;
		/**
		 * Получаем список комментов
		 */
		$aResult=$this->Comment_GetCommentsByUserId($oUserProfile->getId(),'city',$iPage,Config::Get('module.comment.per_page'));
		$aComments=$aResult['collection'];
		$aPaging=$this->Viewer_MakePaging($aResult['count'],$iPage,Config::Get('module.comment.per_page'),4,Router::GetPath('city').'user/'.$oUserProfile->getLogin().'/feedbacks');

		$iCountFeedbackUser=$this->Comment_GetCountCommentsByUserId($oUserProfile->getId(),'city');
		$this->Viewer_Assign('iCountFeedbackUser',$iCountFeedbackUser);
		/**
		 * Загружаем переменные в шаблон
		 */
		$this->Viewer_Assign('aPaging',$aPaging);
		$this->Viewer_Assign('aComments',$aComments);
		$this->Viewer_Assign('oUserProfile',$oUserProfile);
		$this->Viewer_AddHtmlTitle($this->Lang_Get('user_menu_publication').' '.$oUserProfile->getLogin());
		$this->Viewer_AddHtmlTitle($this->Lang_Get('user_menu_publication_comment'));
		/**
		 * Устанавливаем шаблон вывода
		 */
		$this->SetTemplateAction('user_feedbacks');
	}

	/**
	 * Удаление компании
	 *
	 * @return unknown
	 */
	protected function EventDelete() {
		/**
		 * Получаем идентификатор компании из УРЛ и проверяем существует ли он
		 */
		$sCityId=$this->GetParam(0);
		if (!$oCity=$this->PluginCity_City_GetCityById($sCityId)) {
			return parent::EventNotFound();
		}

		if (!$this->oUserCurrent or !$this->oUserCurrent->isAdministrator()) {
			return parent::EventNotFound();
		}
		/**
		 * Удаляем компанию
		 */
		$this->PluginCity_City_DeleteCity($oCity->getId());
		func_header_location(Router::GetPath('city'));
	}

	/**
	 * Активация/деактивация компании
	 *
	 * @return unknown
	 */
	protected function EventActivate() {
		if (!$this->oUserCurrent or !$this->oUserCurrent->isAdministrator()) {
			return parent::EventNotFound();
		}
		/**
		 * Получаем идентификатор компании из УРЛ и проверяем существует ли он
		 */
		$sCityId=$this->GetParam(0);
		if (!$oCity=$this->PluginCity_City_GetCityById($sCityId)) {
			return parent::EventNotFound();
		}
		if ($this->sCurrentEvent == 'activate'){
			$oCity->setActive(1);
		} else{
			$oCity->setActive(0);
		}
		$this->PluginCity_City_UpdateCity($oCity);
		func_header_location($oCity->getUrlFull());
	}

	/**
	 * Тествое событие, используется для отладки
	 *
	 * @return unknown
	 */
	protected function EventTest() {
		if (!$this->User_IsAuthorization()) {
			$this->Message_AddErrorSingle($this->Lang_Get('not_access'),$this->Lang_Get('error'));
			return parent::EventNotFound();
		}
		if (!$this->oUserCurrent->isAdministrator()) {
			return parent::EventNotFound();
		}
		$this->Cache_Clean();
		$this->SetTemplateAction('test');
	}
	/*
	 * Настройки и админка плагина
	 */
	protected function EventSettings() {
		if (!$this->User_IsAuthorization() or !$this->oUserCurrent->isAdministrator()) {
			$this->Message_AddErrorSingle($this->Lang_Get('not_access'),$this->Lang_Get('error'));
			return parent::EventNotFound();
		}

		$this->SetTemplateAction('settings');
	}
	/**
	 * Конвертирование базы
	 *
	 * @return unknown
	 */
	protected function EventSettingsConvert() {
		if (!$this->oUserCurrent or !$this->oUserCurrent->isAdministrator()) {
			return parent::EventNotFound();
		}
		if (isPost('submit_convert')) {
			list($bResult,$aErrors) = array_values($this->PluginCity_Update_Convert());
			if(!$bResult) {
				foreach($aErrors as $sError) $this->Message_AddError($sError,$this->Lang_Get('error'));
			} else{
				$this->Message_AddNoticeSingle($this->Lang_Get('plugin.city.update_success'));
			}
		}
		$this->SetTemplateAction('settings_convert');
	}

	/**
	 * Восстановление битых урл компаний
	 *
	 * @return unknown
	 */
	protected function EventSettingsRepair() {
		if (!$this->oUserCurrent or !$this->oUserCurrent->isAdministrator()) {
			return parent::EventNotFound();
		}
		if (isPost('submit_repair')) {
			if(!$this->PluginCity_Update_RepairUrl()) {
				$this->Message_AddError($this->Lang_Get('plugin.city.update_error'),$this->Lang_Get('error'));
			} else{
				$this->Message_AddNoticeSingle($this->Lang_Get('plugin.city.update_success'));
			}
		}
		$this->SetTemplateAction('settings_repair');
	}

	/**
	 * Обновление до новой версии
	 *
	 * @return unknown
	 */
	protected function EventSettingsUpdate() {
		if (!$this->oUserCurrent or !$this->oUserCurrent->isAdministrator()) {
			return parent::EventNotFound();
		}
		$sVersion=$this->GetParam(1);
		switch ($sVersion){
			case '0518':
				$this->Viewer_Assign('sHeader',$this->Lang_Get('plugin.city.city_settings_update_plugin_0518'));
				break;
			case '10109':
				$this->Viewer_Assign('sHeader',$this->Lang_Get('plugin.city.city_settings_update_plugin_10109'));
				break;
			case '10110':
				$this->Viewer_Assign('sHeader',$this->Lang_Get('plugin.city.city_settings_update_plugin_10110'));
				break;
		}

		if (isPost('submit_update')) {
			if(!$this->PluginCity_Update_UpdateToVersion($sVersion)) {
				$this->Message_AddError($this->Lang_Get('plugin.city.update_error'),$this->Lang_Get('error'));
			} else{
				$this->Message_AddNoticeSingle($this->Lang_Get('plugin.city.update_success'));
				$this->Cache_Clean();
			}
		}
		$this->SetTemplateAction('settings_update');
	}

	/**
	 * Конвертация городов и стран в Geo объекты
	 *
	 * @return unknown
	 */
	protected function EventSettingsConvertGeo() {
		if (!$this->oUserCurrent or !$this->oUserCurrent->isAdministrator()) {
			return parent::EventNotFound();
		}
		if (isPost('submit_convert')) {
			if ($this->PluginCity_Update_ConvertGeo())
				$this->Message_AddNoticeSingle($this->Lang_Get('plugin.city.update_success'));
		}
		$this->SetTemplateAction('settings_convertgeo');
	}


	/**
	 * Обработка добавление комментария к топику
	 *
	 * @param unknown_type $oTopic
	 * @return unknown
	 */
	protected function SubmitComment($oTopic) {
		/**
		 * Если нажали кнопку "Отправить"
		 */
		if (isPost('submit_comment')) {
			$this->Security_ValidateSendForm();
			/**
			 * Проверяем авторизованл ли пользователь
			 */
			if (!$this->oUserCurrent) {
				$this->Message_AddErrorSingle($this->Lang_Get('not_access'),$this->Lang_Get('error'));
				return Router::Action('error');
			}
			/**
			 * Проверяем разрешено ли постить комменты
			 */
			if (!$this->ACL_CanPostComment($this->oUserCurrent) and !$this->oUserCurrent->isAdministrator()) {
				$this->Message_AddError($this->Lang_Get('topic_comment_acl'),$this->Lang_Get('error'));
				return false;
			}
			/**
			 * Проверяем разрешено ли постить комменты по времени
			 */
			if (!$this->ACL_CanPostCommentTime($this->oUserCurrent) and !$this->oUserCurrent->isAdministrator()) {
				$this->Message_AddError($this->Lang_Get('topic_comment_limit'),$this->Lang_Get('error'));
				return false;
			}
			/**
			 * Проверяем запрет на добавления коммента автором топика
			 */
			if ($oTopic->getForbidComment()) {
				$this->Message_AddError($this->Lang_Get('topic_comment_notallow'),$this->Lang_Get('error'));
				return false;
			}
			/**
			 * Проверяем текст комментария
			 */
			$sText=$this->Text_Parser(getRequest('comment_text'));
			if (!func_check($sText,'text',2,10000)) {
				$this->Message_AddError($this->Lang_Get('topic_comment_add_text_error'),$this->Lang_Get('error'));
				return false;
			}
			/**
			 * Проверям на какой коммент отвечаем
			 */
			$sParentId=getRequest('reply',0);
			if (!func_check($sParentId,'id')) {
				$this->Message_AddError($this->Lang_Get('system_error'),$this->Lang_Get('error'));
				return false;
			}
			$oCommentParent=null;
			if ($sParentId!=0) {
				/**
				 * Проверяем существует ли комментарий на который отвечаем
				 */
				if (!($oCommentParent=$this->Comment_GetCommentById($sParentId))) {
					return false;
				}
				/**
				 * Проверяем из одного топика ли новый коммент и тот на который отвечаем
				 */
				if ($oCommentParent->getTopicId()!=$oTopic->getId()) {
					return false;
				}
			} else {
				/**
				 * Корневой комментарий
				 */
				$sParentId=null;
			}
			/**
			 * Проверка на дублирующий коммент
			 */
			if ($this->Comment_GetCommentUnique($oTopic->getId(),$this->oUserCurrent->getId(),$sParentId,md5($sText))) {
				$this->Message_AddError($this->Lang_Get('topic_comment_spam'),$this->Lang_Get('error'));
				return false;
			}
			/**
			 * Создаём коммент
			 */
			$oCommentNew=new CommentEntity_TopicComment();
			$oCommentNew->setTopicId($oTopic->getId());
			$oCommentNew->setUserId($this->oUserCurrent->getId());
			$oCommentNew->setText($sText);
			$oCommentNew->setDate(date("Y-m-d H:i:s"));
			$oCommentNew->setUserIp(func_getIp());
			$oCommentNew->setPid($sParentId);
			$oCommentNew->setTextHash(md5($sText));
			/**
			 * Добавляем коммент
			 */
			$this->Hook_Run('comment_add_before', array('oCommentNew'=>$oCommentNew,'oCommentParent'=>$oCommentParent,'oTopic'=>$oTopic));
			if ($this->Comment_AddComment($oCommentNew)) {
				$this->Hook_Run('comment_add_after', array('oCommentNew'=>$oCommentNew,'oCommentParent'=>$oCommentParent,'oTopic'=>$oTopic));
				if ($oTopic->getPublish()) {
					/**
					 * Добавляем коммент в прямой эфир если топик не в черновиках
					 */
					$oTopicCommentOnline=new CommentEntity_TopicCommentOnline();
					$oTopicCommentOnline->setTopicId($oCommentNew->getTopicId());
					$oTopicCommentOnline->setCommentId($oCommentNew->getId());
					$this->Comment_AddTopicCommentOnline($oTopicCommentOnline);
				}
				/**
				 * Сохраняем дату последнего коммента для юзера
				 */
				$this->oUserCurrent->setDateCommentLast(date("Y-m-d H:i:s"));
				$this->User_Update($this->oUserCurrent);
				/**
				 * Список емайлов на которые не нужно отправлять уведомление
				 */
				$aExcludeMail=array($this->oUserCurrent->getMail());
				/**
				 * Отправляем уведомление тому на чей коммент ответили
				 */
				if ($oCommentParent and $oCommentParent->getUserId()!=$oTopic->getUserId() and $oCommentNew->getUserId()!=$oCommentParent->getUserId()) {
					$oUserAuthorComment=$oCommentParent->getUser();
					$aExcludeMail[]=$oUserAuthorComment->getMail();
					$this->Notify_SendCommentReplyToAuthorParentComment($oUserAuthorComment,$oTopic,$oCommentNew,$this->oUserCurrent);
				}
				/**
				 * Отправка уведомления автору топика
				 */
				$this->Subscribe_Send('topic_new_comment',$oTopic->getId(),'notify.comment_new.tpl',$this->Lang_Get('notify_subject_comment_new'),array(
					'oTopic' => $oTopic,
					'oComment' => $oCommentNew,
					'oUserComment' => $this->oUserCurrent,
				),$aExcludeMail);
				/**
				 * Добавляем событие в ленту
				 */
				$this->Stream_write($oCommentNew->getUserId(), 'add_comment', $oCommentNew->getId(), $oTopic->getPublish() && $oTopic->getBlog()->getType()!='close');
			} else {
				$this->Message_AddErrorSingle($this->Lang_Get('system_error'),$this->Lang_Get('error'));
				return false;
			}
		}
	}


	/**
	 * Проверка полей компании
	 *
	 * @return unknown
	 */
	protected function checkCityFields($oCity=null) {
		$bOk=true;
		if (!$oCity->_Validate()) {
			foreach($oCity->_getValidateErrors() as $sFieldKey => $aErrors) {
				foreach ($aErrors as $sError)
					$this->Message_AddError($sError,$this->Lang_Get('error'));
			}
			$bOk=false;
		}

		/**
		 * Проверяем есть ли уже компания с таким наименованием
		 */
		if ($oCityExists=$this->PluginCity_City_GetCityByName(getRequest('city_name'))) {
			if (!$oCity->getId() or $oCity->getId()!=$oCityExists->getId()) {
				$this->Message_AddError($this->Lang_Get('plugin.city.city_error_add_name_exsist'),$this->Lang_Get('error'));
				$bOk=false;
			}
		}

		/* Проверка URL
		   * Проверка только в том случае если создаём новую компанию, т.к при редактировании URL нельзя менять
		   */
		if (!$oCity->getId()) {
			/**
			 * Проверяем есть ли уже компания с таким url
			 */
			if ($this->PluginCity_City_GetCityExist(getRequest('city_url'))) {
				$this->Message_AddError($this->Lang_Get('plugin.city.city_error_add_url_exsist'),$this->Lang_Get('error'));
				$bOk=false;
			}
			/*
			 * Проверяем на допустимую длинну URL
			 */
			if (!func_check(getRequest('city_url'),'login',4,50)) {
				$this->Message_AddError($this->Lang_Get('plugin.city.city_error_add_url_text'),$this->Lang_Get('error'));
				$bOk=false;
			}
			/**
			 * Проверяем на плохие УРЛы
			 */
			if (in_array(getRequest('city_url'),$this->aBadCityUrl) or preg_match("/^page(\d+)$/i",getRequest('city_url'),$aMatch) or strpos(getRequest('city_url'),'city') !== false) {
				$this->Message_AddError($this->Lang_Get('plugin.city.city_error_add_url_bad').': '.join(',',$this->aBadCityUrl),$this->Lang_Get('error'));
				$bOk=false;
			}
		}
		return $bOk;
	}


	protected function RssCityBlog() {
		$sBlogUrl=$this->sCurrentEvent;
		if (!$sBlogUrl or !($oBlog=$this->Blog_GetBlogByUrl('city_'.$sBlogUrl))) {
			return parent::EventNotFound();
		}else{
			$aResult=$this->Topic_GetTopicsByBlog($oBlog,1,Config::Get('module.topic.per_page')*2,'good');
		}
		$aTopics=$aResult['collection'];

		$aChannel['title']=Config::Get('path.root.web');
		$aChannel['link']=Config::Get('path.root.web');
		$aChannel['description']=Config::Get('path.root.web').' / '.$oBlog->getTitle().' / RSS channel';
		$aChannel['language']='ru';
		$aChannel['managingEditor']=Config::Get('general.rss_editor_mail');
		$aChannel['generator']=Config::Get('path.root.web');

		$topics=array();
		foreach ($aTopics as $oTopic){
			$item['title']=$oTopic->getTitle();
			$item['guid']=$oTopic->getUrl();
			$item['link']=$oTopic->getUrl();
			$item['description']=$oTopic->getTextShort();
			$item['pubDate']=$oTopic->getDateAdd();
			$item['author']=$oTopic->getUserLogin();
			$item['category']=htmlspecialchars($oTopic->getTags());
			$topics[]=$item;
		}
		header('Content-Type: application/rss+xml; charset=utf-8');
		$this->Viewer_Assign('aChannel',$aChannel);
		$this->Viewer_Assign('aItems',$topics);
		$this->SetTemplateAction('city_rss');
	}


	/**
	 * Обработка добавление отзыва к компании
	 *
	 * @return bool
	 */
	protected function AjaxAddFeedback() {
		$this->Viewer_SetResponseAjax('json');
		/**
		 * Проверям авторизован ли пользователь
		 */
		if (!$this->User_IsAuthorization()) {
			$this->Message_AddErrorSingle($this->Lang_Get('need_authorization'),$this->Lang_Get('error'));
			return;
		}
		/**
		 * Проверяем компанию
		 */
		if (!($oCity=$this->PluginCity_City_GetCityById(getRequest('cmt_target_id')))) {
			$this->Message_AddErrorSingle($this->Lang_Get('system_error'),$this->Lang_Get('error'));
			return;
		}

		/**
		 * Проверяем разрешено ли постить отзывы
		 */
		if (!$this->ACL_CanPostFeedback($this->oUserCurrent) and !$this->oUserCurrent->isAdministrator()) {
			$this->Message_AddErrorSingle($this->Lang_Get('plugin.city.city_feedback_acl'),$this->Lang_Get('error'));
			return;
		}

		/**
		 * Проверяем текст отзыва
		 */
		$sText=$this->Text_Parser(getRequest('comment_text'));
		if (!func_check($sText,'text',2,3000)) {
			$this->Message_AddErrorSingle($this->Lang_Get('plugin.city.city_feedback_add_text_error'),$this->Lang_Get('error'));
			return;
		}
		/**
		 * Проверям на какой отзыв отвечаем
		 */
		$sParentId=(int)getRequest('reply');
		if (!func_check($sParentId,'id')) {
			$this->Message_AddErrorSingle($this->Lang_Get('system_error'),$this->Lang_Get('error'));
			return;
		}
		$oCommentParent=null;
		if ($sParentId!=0) {
			/**
			 * Проверяем существует ли отзыв на который отвечаем
			 */
			if (!($oCommentParent=$this->Comment_GetCommentById($sParentId))) {
				$this->Message_AddErrorSingle($this->Lang_Get('system_error'),$this->Lang_Get('error'));
				return;
			}
			/**
			 * Проверяем из одной ли компании новый отзыв и тот на который отвечаем
			 */
			if ($oCommentParent->getTargetId()!=$oCity->getId()) {
				$this->Message_AddErrorSingle($this->Lang_Get('system_error'),$this->Lang_Get('error'));
				return;
			}
		} else {
			/**
			 * Корневой отзыв
			 */
			$sParentId=null;
		}
		/**
		 * Проверка на дублирующий отзыв
		 */
		if ($this->Comment_GetCommentUnique($oCity->getId(),'city',$this->oUserCurrent->getId(),$sParentId,md5($sText))) {
			$this->Message_AddErrorSingle($this->Lang_Get('topic_comment_spam'),$this->Lang_Get('error'));
			return;
		}
		/**
		 * Создаём отзыв
		 */
		$oCommentNew = Engine::GetEntity('Comment');
		$oCommentNew->setTargetId($oCity->getId());
		$oCommentNew->setTargetType('city');
		$oCommentNew->setTargetParentId($oCity->getBlogId());
		$oCommentNew->setUserId($this->oUserCurrent->getId());
		$oCommentNew->setText($sText);
		$oCommentNew->setDate(date("Y-m-d H:i:s"));
		$oCommentNew->setUserIp(func_getIp());
		$oCommentNew->setPid($sParentId);
		$oCommentNew->setTextHash(md5($sText));
		$oCommentNew->setPublish(1);

		/**
		 * Добавляем отзыв
		 */
		//$this->Hook_Run('feedback_add_before', array('oCommentNew'=>$oCommentNew,'oCommentParent'=>$oCommentParent,'oCity'=>$oCity));
		if ($this->Comment_AddComment($oCommentNew)) {
			//$this->Hook_Run('feedback_add_after', array('oCommentNew'=>$oCommentNew,'oCommentParent'=>$oCommentParent,'oCity'=>$oCity));
			$this->PluginCity_City_increaseCityCountFeedbacks($oCity->getId());
			$this->Viewer_AssignAjax('sCommentId',$oCommentNew->getId());

			//if ($oTopic->getActive()) {
			// Добавляем отзыв в прямой эфир если компания активна
			$oCommentOnline=Engine::GetEntity('Comment_CommentOnline');
			$oCommentOnline->setTargetId($oCommentNew->getTargetId());
			$oCommentOnline->setTargetType($oCommentNew->getTargetType());
			$oCommentOnline->setTargetParentId($oCity->getBlogId());
			$oCommentOnline->setCommentId($oCommentNew->getId());

			$this->Comment_AddCommentOnline($oCommentOnline);

			/**
			 * Отправка уведомления подписанным на новые отзывы
			 */
			$aExcludeMail=array($this->oUserCurrent->getMail());
			$this->Subscribe_Send('city_new_feedback', $oCity->getId(), 'notify.city_new_feedback.tpl', $this->Lang_Get('plugin.city.notify_subject_feedback_new').' '.$oCity->getName(), array(
				'oComment' => $oCommentNew,
				'oCity' => $oCity,
				'oUserComment' => $this->oUserCurrent,
			), $aExcludeMail,'PluginCity');
			//}
			/**
			 * Сохраняем дату последнего коммента для юзера
			 */
			$this->oUserCurrent->setDateCommentLast(date("Y-m-d H:i:s"));
			$this->User_Update($this->oUserCurrent);
		} else {
			$this->Message_AddErrorSingle($this->Lang_Get('system_error'),$this->Lang_Get('error'));
		}
	}


	/**
	 * Автоподставновка тегов (видов деятельности)
	 *
	 */
	protected function AjaxAutocompleterTag() {
		$this->Viewer_SetResponseAjax('json');
		if (!($sValue=getRequest('value',null,'post'))) {
			return ;
		}

		$aItems=array();
		$aTags=$this->PluginCity_City_GetCityTagsByLike($sValue,10);
		foreach ($aTags as $oTag) {
			$aItems[]=$oTag->getText();
		}
		$this->Viewer_AssignAjax('aItems',$aItems);
	}

	protected function AjaxAutocompleterCity() {
		$this->Viewer_SetResponseAjax('json');
		if (!($sValue=getRequest('value',null,'post'))) {
			return ;
		}
		$aItems=array();
		$aCityes=$this->PluginCity_City_GetCityesByFilter(array('name_like' => $sValue),array(),1,10,array());
		foreach ($aCityes['collection'] as $oCity) {
			$oCity->setLogo($oCity->getLogoPath(24));
			$aItems[]=$oCity->_getData();
		}
		$this->Viewer_AssignAjax('aItems',$aItems);
	}

	/**
	 * Обработка входа\выхода из компании
	 *
	 */
	protected function AjaxCityBlogJoinLeave() {
		$this->Viewer_SetResponseAjax('json');
		if (!$this->oUserCurrent) {
			$this->Message_AddErrorSingle($this->Lang_Get('need_authorization'),$this->Lang_Get('error'));
			return;
		}

		$idBlog=getRequest('idBlog',null,'post');
		if (!($oBlog=$this->Blog_GetBlogById($idBlog))) {
			$this->Message_AddErrorSingle($this->Lang_Get('system_error'),$this->Lang_Get('error'));
			return;
		}

		if (!in_array($oBlog->getType(),array('city'))) {
			$this->Message_AddErrorSingle($this->Lang_Get('blog_join_error_invite'),$this->Lang_Get('error'));
			return;
		}

		$oBlogUser=$this->Blog_GetBlogUserByBlogIdAndUserId($oBlog->getId(),$this->oUserCurrent->getId());
		if (!$oBlogUser) {
			if ($oBlog->getOwnerId()!=$this->oUserCurrent->getId()) {
				/**
				 * Присоединяем юзера к блогу
				 */
				$bResult=false;
				if($oBlogUser) {
					$oBlogUser->setUserRole(ModuleBlog::BLOG_USER_ROLE_GUEST);
					$bResult = $this->Blog_UpdateRelationBlogUser($oBlogUser);
				} elseif($oBlog->getType()=='city') {
					$oBlogUserNew=Engine::GetEntity('Blog_BlogUser');
					$oBlogUserNew->setBlogId($oBlog->getId());
					$oBlogUserNew->setUserId($this->oUserCurrent->getId());
					$oBlogUserNew->setUserRole(ModuleBlog::BLOG_USER_ROLE_GUEST);
					$bResult = $this->Blog_AddRelationBlogUser($oBlogUserNew);
				}
				if ($bResult) {
					$this->Message_AddNoticeSingle($this->Lang_Get('plugin.city.city_notice_join'),$this->Lang_Get('attention'));
					$this->Viewer_AssignAjax('bState',true);
					/**
					 * Увеличиваем число читателей блога
					 */
					$oBlog->setCountUser($oBlog->getCountUser()+1);
					$oBlog->setUrl('city_city_'.$oBlog->getUrl());
					$this->Blog_UpdateBlog($oBlog);
					$this->Viewer_AssignAjax('iCountUser',$oBlog->getCountUser());
					/**
					 * Добавляем событие в ленту
					 */
					$this->Stream_write($this->oUserCurrent->getId(), 'join_blog', $oBlog->getId());
				} else {
					$sMsg=($oBlog->getType()=='close')
						? $this->Lang_Get('blog_join_error_invite')
						: $this->Lang_Get('system_error');
					$this->Message_AddErrorSingle($sMsg,$this->Lang_Get('error'));
					return;
				}
			} else {
				$this->Message_AddErrorSingle($this->Lang_Get('plugin.city.city_notice_join_owner'),$this->Lang_Get('attention'));
				return;
			}
		}
		if ($oBlogUser) {
			/**
			 * Покидаем блог
			 */
			if ($this->Blog_DeleteRelationBlogUser($oBlogUser)) {
				$this->Message_AddNoticeSingle($this->Lang_Get('plugin.city.city_notice_leave'),$this->Lang_Get('attention'));
				$this->Viewer_AssignAjax('bState',false);
				/**
				 * Уменьшаем число читателей блога
				 */
				$oBlog->setCountUser($oBlog->getCountUser()-1);
				$oBlog->setUrl('city_city_'.$oBlog->getUrl());
				$this->Blog_UpdateBlog($oBlog);
				$this->Viewer_AssignAjax('iCountUser',$oBlog->getCountUser());

			} else {
				$this->Message_AddErrorSingle($this->Lang_Get('system_error'),$this->Lang_Get('error'));
				return;
			}
		}
	}

	/**
	 * Обработка получения последних отзывов
	 *
	 */
	protected function AjaxStreamFeedback() {
		$this->Viewer_SetResponseAjax('json');
		if ($aComments=$this->Comment_GetCommentsOnline('city',Config::Get('block.stream.row'))) {
			$oViewer=$this->Viewer_GetLocalViewer();
			$oViewer->Assign('aComments',$aComments);
			$sTextResult=$oViewer->Fetch(Plugin::GetTemplatePath(__CLASS__)."blocks/block.stream_feedback.tpl");
			$this->Viewer_AssignAjax('sText',$sTextResult);
		} else {
			$this->Message_AddErrorSingle($this->Lang_Get('block_stream_feedbacks_no'),$this->Lang_Get('attention'));
			return;
		}
	}

	/**
	 * Получение новых отзывов
	 *
	 */
	protected function AjaxResponseFeedback() {
		$this->Viewer_SetResponseAjax('json');

		if (!$this->oUserCurrent) {
			$this->Message_AddErrorSingle($this->Lang_Get('need_authorization'),$this->Lang_Get('error'));
			return;
		}

		$idTopic=getRequest('idTarget',null,'post');
		if (!($oCity=$this->PluginCity_City_GetCityById($idTopic))) {
			$this->Message_AddErrorSingle($this->Lang_Get('system_error'),$this->Lang_Get('error'));
			return;
		}

		$idCommentLast=getRequest('idCommentLast',null,'post');
		$selfIdComment=getRequest('selfIdComment',null,'post');
		$aComments=array();

		if (getRequest('bUsePaging',null,'post') and $selfIdComment) {
			if ($oComment=$this->Comment_GetCommentById($selfIdComment) and $oComment->getTargetId()==$oCity->getId() and $oComment->getTargetType()=='city') {
				$oViewerLocal=$this->Viewer_GetLocalViewer();
				$oViewerLocal->Assign('oUserCurrent',$this->oUserCurrent);
				$oViewerLocal->Assign('bOneComment',true);

				$oViewerLocal->Assign('oComment',$oComment);
				$sText=$oViewerLocal->Fetch(Plugin::GetTemplatePath('city')."feedback.tpl");
				$aCmt=array();
				$aCmt[]=array(
					'html' => $sText,
					'obj'  => $oComment,
				);
			} else {
				$aCmt=array();
			}
			$aReturn['comments']=$aCmt;
			$aReturn['iMaxIdComment']=$selfIdComment;
		} else {
			$aReturn=$this->Comment_GetCommentsNewByTargetId($oCity->getId(),'city',$idCommentLast);
		}
		$iMaxIdComment=$aReturn['iMaxIdComment'];

		$oFeebackRead= Engine::GetEntity('PluginCity_City_CityFeedbackRead');
		$oFeebackRead->setCityId($oCity->getId());
		$oFeebackRead->setUserId($this->oUserCurrent->getId());
		$oFeebackRead->setFeedbackCountLast($oCity->getCountFeedback());
		$oFeebackRead->setFeedbackIdLast($iMaxIdComment);
		$oFeebackRead->setDateRead(date("Y-m-d H:i:s"));
		$this->PluginCity_City_SetFeedbackRead($oFeebackRead);

		$aCmts=$aReturn['comments'];
		if ($aCmts and is_array($aCmts)) {
			foreach ($aCmts as $aCmt) {
				$aComments[]=array(
					'html' => $aCmt['html'],
					'idParent' => $aCmt['obj']->getPid(),
					'id' => $aCmt['obj']->getId(),
				);
			}
		}

		$this->Viewer_AssignAjax('iMaxIdComment',$iMaxIdComment);
		$this->Viewer_AssignAjax('aComments',$aComments);
	}

	/**
	 * Скрытие отзыва
	 *
	 */
	protected function AjaxFeedbackBad() {
		$this->Viewer_SetResponseAjax('json');
		if (!$this->oUserCurrent) {
			$this->Message_AddErrorSingle($this->Lang_Get('need_authorization'),$this->Lang_Get('error'));
			return;
		}
		$idComment = getRequest('idComment',null,'post');
		if ($oComment = $this->Comment_GetCommentById($idComment)) {
			$oCity = $this->PluginCity_City_GetCityById($oComment->getTargetId());
			$oBlog = $oCity->getBlog();
			if ($oBlog->getUserIsAdministrator() or $oBlog->getUserIsModerator() or $this->oUserCurrent->isAdministrator()) {
				($oComment->getRating() == 0 ? $oComment->setRating(-100) : $oComment->setRating(0));
				if ($this->Comment_UpdateCommentRating($oComment)) {
					$bState=(bool)$oComment->isBad();
					if ($bState) {
						$this->Message_AddNoticeSingle($this->Lang_Get('plugin.city.city_notice_feedback_colapce'),$this->Lang_Get('attention'));
						$this->Viewer_AssignAjax('bState',$bState);
						$this->Viewer_AssignAjax('sTextToggle',$this->Lang_Get('plugin.city.feedback_show'));
					} else {
						$this->Message_AddNoticeSingle($this->Lang_Get('plugin.city.city_notice_feedback_expand'),$this->Lang_Get('attention'));
						$this->Viewer_AssignAjax('bState',$bState);
						$this->Viewer_AssignAjax('sTextToggle',$this->Lang_Get('plugin.city.feedback_hide'));
					}
				} else {
					$this->Message_AddErrorSingle($this->Lang_Get('plugin.city.city_error_feedback_edit'),$this->Lang_Get('error'));
					return;
				}
			} else {
				$this->Message_AddErrorSingle($this->Lang_Get('not_access'),$this->Lang_Get('error'));
				return;
			}
		} else {
			$this->Message_AddErrorSingle($this->Lang_Get('plugin.city.city_error_feedback_notfound'),$this->Lang_Get('error'));
			return;
		}
		// Чистим кэши
		if(Config::Get('sys.cache.solid')){
			$this->Cache_Clean(Zend_Cache::CLEANING_MODE_MATCHING_TAG,array("comment_update"));
		}
		$this->Cache_Clean(Zend_Cache::CLEANING_MODE_MATCHING_TAG,array("comment_update_status_city"));
	}

	/**
	 * Голосование за компанию
	 *
	 */
	protected function AjaxVoteCity() {
		$this->Viewer_SetResponseAjax('json');
		$idCity = getRequest('idCity',null,'post');
		if (!$this->oUserCurrent) {
			$this->Message_AddErrorSingle($this->Lang_Get('need_authorization'),$this->Lang_Get('error'));
			return;
		}

		if (!($oCity=$this->PluginCity_City_GetCityById($idCity))) {
			$this->Message_AddErrorSingle($this->Lang_Get('system_error'),$this->Lang_Get('error'));
			return;
		}

		if ($oCity->getOwnerId()==$this->oUserCurrent->getId()) {
			$this->Message_AddErrorSingle($this->Lang_Get('plugin.city.city_notice_voted_owner'),$this->Lang_Get('attention'));
			return;
		}

		if ($oCityVote=$this->Vote_GetVote($oCity->getId(),'city',$this->oUserCurrent->getId())) {
			$this->Message_AddErrorSingle($this->Lang_Get('plugin.city.city_notice_voted'),$this->Lang_Get('attention'));
			return;
		}

		$iValue=getRequest('value',null,'post');
		if (!in_array($iValue,array('1','-1','0'))) {
			$this->Message_AddErrorSingle($this->Lang_Get('plugin.city.city_vote_values'),$this->Lang_Get('attention'));
			return;
		}

		if (!$this->ACL_CanVoteCity($this->oUserCurrent,$oCity)) {
			$this->Message_AddErrorSingle($this->Lang_Get('plugin.city.city_vote_acl'),$this->Lang_Get('attention'));
			return;
		}
		$oCityVote=Engine::GetEntity('Vote');
		$oCityVote->setTargetId($oCity->getId());
		$oCityVote->setTargetType('city');
		$oCityVote->setVoterId($this->oUserCurrent->getId());
		$oCityVote->setDirection($iValue);
		$oCityVote->setDate(date("Y-m-d H:i:s"));

		$iVal=0;
		if ($iValue!=0) {
			$iVal=(float)$this->PluginCity_City_VoteCity($this->oUserCurrent,$oCity,$iValue);
		}
		$oCityVote->setValue($iVal);
		$oCity->setCountVote($oCity->getCountVote()+1);

		if ($this->Vote_AddVote($oCityVote) and $this->PluginCity_City_UpdateCity($oCity)) {
			if ($iValue)
				$this->Message_AddNoticeSingle($this->Lang_Get('plugin.city.city_vote_ok'),$this->Lang_Get('attention'));

			$this->Viewer_AssignAjax('iRating',$oCity->getRating());
			$this->Viewer_AssignAjax('iCountVote',$oCity->getCountVote());

			//$this->Stream_write($oTopicVote->getVoterId(), 'vote_topic', $oTopic->getId());
		} else {
			$this->Message_AddErrorSingle($this->Lang_Get('plugin.city.city_vote_after'),$this->Lang_Get('error'));
			return;
		}
	}

	/**
	 * Обработка избранного - компаний
	 *
	 */
	protected function AjaxFavouriteCity() {
		$this->Viewer_SetResponseAjax('json');
		if (!$this->oUserCurrent) {
			$this->Message_AddErrorSingle($this->Lang_Get('need_authorization'),$this->Lang_Get('error'));
			return;
		}

		$iType=getRequest('type',null,'post');
		if (!in_array($iType,array('1','0'))) {
			$this->Message_AddErrorSingle($this->Lang_Get('system_error'),$this->Lang_Get('error'));
			return;
		}
		if (!($oCity=$this->PluginCity_City_GetCityById(getRequest('idCity',null,'post')))) {
			$this->Message_AddErrorSingle($this->Lang_Get('system_error'),$this->Lang_Get('error'));
			return;
		}

		$oFavouriteCity=$this->Favourite_GetFavourite($oCity->getId(),'city',$this->oUserCurrent->getId());
		if (!$oFavouriteCity and $iType) {
			$oFavouriteCityNew=Engine::GetEntity('Favourite',
				array(
					'target_id'      => $oCity->getId(),
					'user_id'        => $this->oUserCurrent->getId(),
					'target_type'    => 'city',
					'target_publish' => '1'
				)
			);
			$oCity->setCountFavourite($oCity->getCountFavourite()+1);
			if ($this->Favourite_AddFavourite($oFavouriteCityNew) and $this->PluginCity_City_UpdateCity($oCity)) {
				$this->Message_AddNoticeSingle($this->Lang_Get('plugin.city.city_favourite_add_ok'),$this->Lang_Get('attention'));
				$this->Viewer_AssignAjax('bState',true);
				$this->Viewer_AssignAjax('iCount', $oCity->getCountFavourite());
				$this->Cache_Clean(Zend_Cache::CLEANING_MODE_MATCHING_TAG,array("favourite_city_change"));
			} else {
				$this->Message_AddErrorSingle($this->Lang_Get('system_error'),$this->Lang_Get('error'));
				return;
			}
		}
		if (!$oFavouriteCity and !$iType) {
			$this->Message_AddErrorSingle($this->Lang_Get('plugin.city.city_favourite_add_no'),$this->Lang_Get('error'));
			return;
		}
		if ($oFavouriteCity and $iType) {
			$this->Message_AddErrorSingle($this->Lang_Get('plugin.city.city_favourite_add_already'),$this->Lang_Get('error'));
			return;
		}
		if ($oFavouriteCity and !$iType) {
			$oCity->setCountFavourite($oCity->getCountFavourite()-1);
			if ($this->Favourite_DeleteFavourite($oFavouriteCity) and $this->PluginCity_City_UpdateCity($oCity)) {
				$this->Message_AddNoticeSingle($this->Lang_Get('plugin.city.city_favourite_del_ok'),$this->Lang_Get('attention'));
				$this->Viewer_AssignAjax('bState',false);
				$this->Viewer_AssignAjax('iCount', $oCity->getCountFavourite());
				$this->Cache_Clean(Zend_Cache::CLEANING_MODE_MATCHING_TAG,array("favourite_city_change"));
			} else {
				$this->Message_AddErrorSingle($this->Lang_Get('system_error'),$this->Lang_Get('error'));
				return;
			}
		}
	}

	/**
	 * Обработка увеличения/уменьшения числа подписчиков компании
	 *
	 */
	protected function AjaxSubscribeToggle() {
		$this->Viewer_SetResponseAjax('json');
		$iValue=getRequest('iValue',null,'post');
		$idCity=getRequest('idCity',null,'post');
		if (!($oCity=$this->PluginCity_City_GetCityById($idCity))) {
			$this->Message_AddErrorSingle($this->Lang_Get('system_error'),$this->Lang_Get('error'));
			return;
		}
		// если подписывается увеличиваем число подписчиков
		if ($iValue ==1)
			$this->PluginCity_City_increaseCityCountSubscribe($oCity->getId());
		else
			$this->PluginCity_City_decreaseCityCountSubscribe($oCity->getId());
	}


	protected function AjaxUpdateTwitterAccount() {
		$this->Viewer_SetResponseAjax('json');
		$screenName = getRequest('screenName',null,'post');
		$idCity = getRequest('idCity',null,'post');
		if (!$this->oUserCurrent) {
			$this->Message_AddErrorSingle($this->Lang_Get('need_authorization'),$this->Lang_Get('error'));
			return;
		}
		if (!($oCity=$this->PluginCity_City_GetCityById($idCity))) {
			$this->Message_AddErrorSingle($this->Lang_Get('system_error'),$this->Lang_Get('error'));
			return;
		}

		if (!$this->ACL_CanEditCity($this->oUserCurrent, $oCity)) {
			$this->Message_AddErrorSingle($this->Lang_Get('system_error'),$this->Lang_Get('attention'));
			return;
		}
		if ($screenName == '') {
			$oCity->setTwitterScreenName('');
			$this->PluginCity_City_UpdateCityPrefs($oCity);
			$this->Message_AddNoticeSingle($this->Lang_Get('plugin.city.widget_notice_ok'),$this->Lang_Get('attention'));
			return;
		}

		if (!$this->PluginCity_City_CheckAndSaveTwitterAccount($oCity, $screenName)) {
			$this->Message_AddErrorSingle($this->Lang_Get('plugin.city.widget_error_value'),$this->Lang_Get('error'));
			return;
		}
		$this->Message_AddNoticeSingle($this->Lang_Get('plugin.city.widget_notice_ok'),$this->Lang_Get('attention'));
	}

	protected function AjaxUpdateFbAccount() {
		$this->Viewer_SetResponseAjax('json');
		$fbUrl = getRequest('fbUrl',null,'post');
		$idCity = getRequest('idCity',null,'post');
		if (!$this->oUserCurrent) {
			$this->Message_AddErrorSingle($this->Lang_Get('need_authorization'),$this->Lang_Get('error'));
			return;
		}
		if (!($oCity=$this->PluginCity_City_GetCityById($idCity))) {
			$this->Message_AddErrorSingle($this->Lang_Get('system_error'),$this->Lang_Get('error'));
			return;
		}

		if (!$this->ACL_CanEditCity($this->oUserCurrent, $oCity)) {
			$this->Message_AddErrorSingle($this->Lang_Get('system_error'),$this->Lang_Get('attention'));
			return;
		}

		if ($fbUrl == '') {
			$oCity->setFbUrl('');
			$this->PluginCity_City_UpdateCityPrefs($oCity);
			$this->Message_AddNoticeSingle($this->Lang_Get('plugin.city.widget_notice_ok'),$this->Lang_Get('attention'));
			return;
		}

		if (!$this->PluginCity_City_CheckAndSaveFBAccount($oCity,$fbUrl)) {
			$this->Message_AddErrorSingle($this->Lang_Get('plugin.city.widget_error_value'),$this->Lang_Get('error'));
			return;
		}
		$this->Message_AddNoticeSingle($this->Lang_Get('plugin.city.widget_notice_ok'),$this->Lang_Get('attention'));
	}

	protected function AjaxUpdateVkAccount() {
		$this->Viewer_SetResponseAjax('json');
		$vkUrl = getRequest('vkUrl',null,'post');
		$idCity = getRequest('idCity',null,'post');
		if (!$this->oUserCurrent) {
			$this->Message_AddErrorSingle($this->Lang_Get('need_authorization'),$this->Lang_Get('error'));
			return;
		}
		if (!($oCity=$this->PluginCity_City_GetCityById($idCity))) {
			$this->Message_AddErrorSingle($this->Lang_Get('system_error'),$this->Lang_Get('error'));
			return;
		}

		if (!$this->ACL_CanEditCity($this->oUserCurrent, $oCity)) {
			$this->Message_AddErrorSingle($this->Lang_Get('system_error'),$this->Lang_Get('attention'));
			return;
		}

		if ($vkUrl == '') {
			$oCity->setVkUrl('');
			$oCity->setVkId('');
			$this->PluginCity_City_UpdateCityPrefs($oCity);
			$this->Message_AddNoticeSingle($this->Lang_Get('plugin.city.widget_notice_ok'),$this->Lang_Get('attention'));
			return;
		}

		if (!$this->PluginCity_City_CheckAndSaveVkAccount($oCity,$vkUrl)) {
			$this->Message_AddErrorSingle($this->Lang_Get('plugin.city.widget_error_value'),$this->Lang_Get('error'));
			return;
		}
		$this->Message_AddNoticeSingle($this->Lang_Get('plugin.city.widget_notice_ok'),$this->Lang_Get('attention'));
	}

	protected function AjaxSwitchWidgetVisible(){
		$this->Viewer_SetResponseAjax('json');
		if (!getRequest('widgetName')) {
			$this->Message_AddError($this->Lang_Get('system_error'),$this->Lang_Get('error'));
		}

		$widgetName = getRequest('widgetName',null,'post');
		$idCity = getRequest('idCity',null,'post');
		$iVisible = getRequest('iVisible',null,'post');
		if (!$this->oUserCurrent) {
			$this->Message_AddErrorSingle($this->Lang_Get('need_authorization'),$this->Lang_Get('error'));
			return;
		}
		if (!($oCity=$this->PluginCity_City_GetCityById($idCity))) {
			$this->Message_AddErrorSingle($this->Lang_Get('system_error'),$this->Lang_Get('error'));
			return;
		}

		if (!$this->ACL_CanEditCity($this->oUserCurrent, $oCity)) {
			$this->Message_AddErrorSingle($this->Lang_Get('system_error'),$this->Lang_Get('attention'));
			return;
		}

		if (!in_array($widgetName,array('twitter','fb','vk'))) {
			$this->Message_AddErrorSingle($this->Lang_Get('system_error'),$this->Lang_Get('attention'));
			return;
		}
		$oCity->setWidgetVisible($widgetName,$iVisible);
		if (!$this->PluginCity_City_UpdateCityPrefs($oCity)){
			$this->Message_AddErrorSingle($this->Lang_Get('plugin.city.widget_error_visible'),$this->Lang_Get('error'));
			return;
		}
		$this->Message_AddNoticeSingle($this->Lang_Get('plugin.city.widget_notice_visible_ok'),$this->Lang_Get('attention'));


	}


	/**
	 * AJAX подгрузка следующих фото
	 *
	 */
	protected function AjaxPhotoGetMore() {
		/**
		 * Устанавливаем формат Ajax ответа
		 */
		$this->Viewer_SetResponseAjax('json');
		/**
		 * Существует ли топик
		 */
		$oCity = $this->PluginCity_City_GetCityById(getRequestStr('city_id'));
		if (!$oCity || !getRequest('last_id')) {
			$this->Message_AddError($this->Lang_Get('system_error'), $this->Lang_Get('error'));
			return false;
		}
		/**
		 * Получаем список фото
		 */
		$aPhotos = $oCity->getPhotos(getRequestStr('last_id'), Config::Get('module.city.photo.per_page'));
		$aResult = array();
		if (count($aPhotos)) {
			/**
			 * Формируем данные для ajax ответа
			 */
			foreach($aPhotos as $oPhoto) {
				$aResult[] = array('id' => $oPhoto->getId(), 'path_thumb' => $oPhoto->getWebPath('50crop'), 'path' => $oPhoto->getWebPath(), 'description' => $oPhoto->getDescription());
			}
			$this->Viewer_AssignAjax('photos', $aResult);
		}
		$this->Viewer_AssignAjax('bHaveNext', count($aPhotos)==Config::Get('module.city.photo.per_page'));
	}
	/**
	 * AJAX удаление фото
	 *
	 */
	protected function AjaxPhotoDelete() {
		/**
		 * Устанавливаем формат Ajax ответа
		 */
		$this->Viewer_SetResponseAjax('json');
		/**
		 * Проверяем авторизован ли юзер
		 */
		if (!$this->User_IsAuthorization()) {
			$this->Message_AddErrorSingle($this->Lang_Get('not_access'),$this->Lang_Get('error'));
			return Router::Action('error');
		}
		/**
		 * Поиск фото по id
		 */
		$oPhoto = $this->PluginCity_Content_GetCityPhotoById(getRequestStr('id'));
		if ($oPhoto) {
			if ($oPhoto->getCityId()) {
				/**
				 * Проверяем права на топик
				 */
				$oCity=$this->PluginCity_City_GetCityById($oPhoto->getCityId());
				if ($oCity and $this->ACL_CanEditCity($this->oUserCurrent,$oCity)) {
					//if ($oCity->getPhotoCount()>1) {
						$this->PluginCity_Content_DeleteCityPhoto($oPhoto);
						/**
						 * Если удаляем главную фотку топика, то её необходимо сменить
						 */
						if ($oPhoto->getId()==$oCity->getPMainPhotoId()) {
							$aPhotos = $oCity->getPhotos(0,1);
							if ($oCity->getPhotoCount() == 1)
								$oCity->setMainPhotoId(0);
							else
								$oCity->setMainPhotoId($aPhotos[0]->getId());
						}
						$oCity->setPhotoCount($oCity->getPhotoCount()-1);
						$this->PluginCity_City_UpdateCityPrefs($oCity);
						$this->Message_AddNotice($this->Lang_Get('plugin.city.city_photo_photo_deleted'), $this->Lang_Get('attention'));
					//} else {
					//	$this->Message_AddError($this->Lang_Get('plugin.city.city_photo_photo_deleted_error_last'), $this->Lang_Get('error'));
					//}
					return;
				}
			} else {
				$this->PluginCity_Content_DeleteCityPhoto($oPhoto);
				$this->Message_AddNotice($this->Lang_Get('plugin.city.city_photo_photo_deleted'), $this->Lang_Get('attention'));
				return;
			}
		}
		$this->Message_AddError($this->Lang_Get('system_error'), $this->Lang_Get('error'));
	}
	/**
	 * AJAX установка описания фото
	 *
	 */
	protected function AjaxPhotoSetDescription() {
		/**
		 * Устанавливаем формат Ajax ответа
		 */
		$this->Viewer_SetResponseAjax('json');
		/**
		 * Проверяем авторизован ли юзер
		 */
		if (!$this->User_IsAuthorization()) {
			$this->Message_AddErrorSingle($this->Lang_Get('not_access'),$this->Lang_Get('error'));
			return Router::Action('error');
		}
		/**
		 * Поиск фото по id
		 */
		$oPhoto = $this->PluginCity_Content_GetCityPhotoById(getRequestStr('id'));
		if ($oPhoto) {
			if ($oPhoto->getCityId()) {
				// проверяем права на топик
				if ($oCity=$this->PluginCity_City_GetCityById($oPhoto->getCityId()) and $this->ACL_CanEditCity($this->oUserCurrent,$oCity)) {
					$oPhoto->setDescription(htmlspecialchars(strip_tags(getRequestStr('text'))));
					$this->PluginCity_Content_UpdateCityPhoto($oPhoto);
				}
			} else {
				$oPhoto->setDescription(htmlspecialchars(strip_tags(getRequestStr('text'))));
				$this->PluginCity_Content_UpdateCityPhoto($oPhoto);
			}
		}
	}


	/**
	 * AJAX загрузка фоток
	 *
	 * @return unknown
	 */
	protected function AjaxPhotoUpload() {
		/**
		 * Устанавливаем формат Ajax ответа
		 * В зависимости от типа загрузчика устанавливается тип ответа
		 */
		if (getRequest('is_iframe')) {
			$this->Viewer_SetResponseAjax('jsonIframe', false);
		} else {
			$this->Viewer_SetResponseAjax('json');
		}
		/**
		 * Проверяем авторизован ли юзер
		 */
		if (!$this->User_IsAuthorization()) {
			$this->Message_AddErrorSingle($this->Lang_Get('not_access'),$this->Lang_Get('error'));
			return Router::Action('error');
		}
		/**
		 * Файл был загружен?
		 */
		if (!isset($_FILES['Filedata']['tmp_name'])) {
			$this->Message_AddError($this->Lang_Get('system_error'), $this->Lang_Get('error'));
			return false;
		}

		$iCityId = getRequestStr('city_id');
		$sTargetId = null;
		$iCountPhotos = 0;
		// Если от сервера не пришёл id компании, то пытаемся определить временный код для новой компании. Если и его нет. то это ошибка
		if (!$iCityId) {
			$sTargetId = empty($_COOKIE['ls_photo_target_tmp']) ? getRequestStr('ls_photo_target_tmp') : $_COOKIE['ls_photo_target_tmp'];
			if (!$sTargetId) {
				$this->Message_AddError($this->Lang_Get('system_error'), $this->Lang_Get('error'));
				return false;
			}
			$iCountPhotos = $this->PluginCity_Content_GetCountPhotosByTargetTmp($sTargetId);
		} else {
			/**
			 * Загрузка фото к уже существующему топику
			 */
			$oCity = $this->PluginCity_City_GetCityById($iCityId);
			if (!$oCity or !$this->ACL_CanEditCity($this->oUserCurrent,$oCity)) {
				$this->Message_AddError($this->Lang_Get('system_error'), $this->Lang_Get('error'));
				return false;
			}
			$iCountPhotos = $this->PluginCity_Content_GetCountPhotosByCityId($iCityId);
		}
		/**
		 * Максимальное количество фото в топике
		 */
		if ($iCountPhotos >= Config::Get('module.city.photo.count_photos_max')) {
			$this->Message_AddError($this->Lang_Get('plugin.city.city_photo_error_too_much_photos', array('MAX' => Config::Get('module.city.photo.count_photos_max'))), $this->Lang_Get('error'));
			return false;
		}
		/**
		 * Максимальный размер фото
		 */
		if (filesize($_FILES['Filedata']['tmp_name']) > Config::Get('module.city.photo.photo_max_size')*1024) {
			$this->Message_AddError($this->Lang_Get('plugin.city.city_photo_error_bad_filesize', array('MAX' => Config::Get('module.city.photo.photo_max_size'))), $this->Lang_Get('error'));
			return false;
		}
		/**
		 * Загружаем файл
		 */
		$sFile = $this->PluginCity_Content_UploadCityPhoto($_FILES['Filedata']);
		if ($sFile) {
			/**
			 * Создаем фото
			 */
			$oPhoto = Engine::GetEntity('PluginCity_ModuleContent_EntityPhoto');
			$oPhoto->setPath($sFile);
			if ($iCityId) {
				$oPhoto->setCityId($iCityId);
			} else {
				$oPhoto->setTargetTmp($sTargetId);
			}
			if ($oPhoto = $this->PluginCity_Content_AddCityPhoto($oPhoto)) {
				/**
				 * Если топик уже существует (редактирование), то обновляем число фоток в нём
				 */
				if (isset($oCity)) {
					$oCity->setPhotoCount($oCity->getPhotoCount()+1);
					$this->PluginCity_City_UpdateCityPrefs($oCity);
				}

				$this->Viewer_AssignAjax('file', $oPhoto->getWebPath('100crop'));
				$this->Viewer_AssignAjax('id', $oPhoto->getId());
				$this->Message_AddNotice($this->Lang_Get('plugin.city.city_photo_photo_added'), $this->Lang_Get('attention'));
			} else {
				$this->Message_AddError($this->Lang_Get('system_error'), $this->Lang_Get('error'));
			}
		} else {
			$this->Message_AddError($this->Lang_Get('system_error'), $this->Lang_Get('error'));
		}
	}

	/**
	 * Обновление тарифа компании
	 *
	 * @return bool
	 */
	protected function AjaxUpdateTariff() {
		$this->Viewer_SetResponseAjax('json');
		/**
		 * Проверям авторизован ли пользователь
		 */
		if (!$this->User_IsAuthorization()) {
			$this->Message_AddErrorSingle($this->Lang_Get('need_authorization'),$this->Lang_Get('error'));
			return;
		}

		/**
		 * Проверяем разрешено ли менять тариф
		 */
		if (!$this->oUserCurrent->isAdministrator()) {
			$this->Message_AddErrorSingle($this->Lang_Get('system_error'),$this->Lang_Get('error'));
			return;
		}


		$idCity =  getRequest('city_id');
		$idTariff = getRequest('tariff_id');
		$sPeriod = getRequest('tariff_period');
		/**
		 * Проверяем компанию
		 */
		if (!($oCity=$this->PluginCity_City_GetCityById($idCity))) {
			$this->Message_AddErrorSingle($this->Lang_Get('system_error'),$this->Lang_Get('error'));
			return;
		}

		if (!in_array($sPeriod,Config::Get('module.city.tariff_periods'))) {
			$this->Message_AddErrorSingle($this->Lang_Get('system_error'),$this->Lang_Get('error'));
			return;
		}

		if (!$this->PluginCity_Pay_GetTariff($idTariff)) {
			$this->Message_AddErrorSingle($this->Lang_Get('system_error'),$this->Lang_Get('error'));
			return;
		}

		$oCity->setTariffId($idTariff);
		if ($oCity->getDateTariffEnd())
			$oCity->setDateTariffEnd(date("Y-m-d H:i:s", strtotime($oCity->getDateTariffEnd())+60*60*24*$sPeriod));
		else
			$oCity->setDateTariffEnd(date("Y-m-d H:i:s", time()+60*60*24*$sPeriod));
		// бесплатный тариф без времени
		if ($idTariff == 0){
			$oCity->setDateTariffEnd(null);
		}

		if ($this->PluginCity_City_UpdateCity($oCity)) {
			$this->Viewer_AssignAjax('sDate',$oCity->getDateTariffEnd());
			$this->Message_AddNoticeSingle($this->Lang_Get('plugin.city.tariff_notice_ok'),$this->Lang_Get('attention'));
		} else {
			$this->Message_AddErrorSingle($this->Lang_Get('system_error'),$this->Lang_Get('error'));
			return;
		}
	}

	/**
	 * Заполняются переменные вывода которые требуются в шапке компаний
	 *
	 * @param $oCity
	 */
	public function CityHeaderVariables($oCity){
		$iCountTender = 0;
		if ($this->oUserCurrent and $oCity)
			$iCountTender = $this->PluginCity_City_GetCountTender($oCity);

		if ($this->oUserCurrent){
			$aAllowBlogs = $this->Blog_GetBlogsAllowByUser($this->oUserCurrent);
			$this->Viewer_Assign('bCanWriteBlog',isset($aAllowBlogs[$oCity->getBlogId()]));
		}
		$iCountCityTopicNew = $this->Topic_GetCountTopicsNewByCity($oCity);
		$iCountVacancies = 0;
		if ($this->PluginCity_City_isActivePlugin('job') and Config::Get('module.city.use_jobs')){
			// пока такая заглушка, в работе нет пока функции подсчета количества вакансий по компании.
			$aResult = $this->PluginJob_Job_GetVacanciesByCityId(1,1,$oCity->getId());
			$iCountVacancies = $aResult['count'];
		}elseif ($oCity->getVacancies()){
			$iCountVacancies = 1;
		}
		$this->Viewer_AddMenu('city',Plugin::GetTemplatePath('city').'menu.city.tpl');
		$this->Viewer_Assign('iCountCityTopicNew',$iCountCityTopicNew);
		$this->Viewer_Assign('iCountVacancies',$iCountVacancies);

		$this->Viewer_Assign('iCountTender',$iCountTender);

		$this->Viewer_Assign('oCity',$oCity);
		// блок компаний в этом городе, показываем только для бесплатных тарифов
		if ($oCity->getTariffId()==0)
			$this->Viewer_AddBlock('right','cityesInCity',array($oCity,'plugin'=>'city',));
		$this->Viewer_AddBlock('right','cityblog',array($oCity,'plugin'=>'city',));

	}

	/**
	 * При завершении экшена загружаем в шаблон необходимые переменные
	 *
	 */
	public function EventShutdown() {
		if ($this->oUserCurrent){
			// Проверяем может ли пользователь добавить компанию, нужно для меню
			$bCanAddCity=$this->ACL_CanCreateCity($this->oUserCurrent);
			$this->Viewer_Assign('bCanAddCity',$bCanAddCity);
			// по сути нужно переделать в функцию, чтобы получать компании которые нельзя показывать, т.е. не активированные.
			$this->Viewer_Assign('iCountFavCityes',$this->PluginCity_City_GetCountFavCityesByUser($this->oUserCurrent->getId()));
			//при завершении обновляем значение переменной компаний пользователя
			$this->aUserCity = $this->PluginCity_City_GetCityesByUser($this->oUserCurrent->getId(),true);
			$this->Viewer_Assign('aUserCity',$this->aUserCity);
			// если только одна компания то выводим прямую ссылку на нее и количество заявок
			if (count($this->aUserCity) == 1){
				$oUserCity = reset($this->aUserCity);
				$this->Viewer_Assign('oUserCity',$oUserCity);
				//$this->Viewer_Assign('iCountTender',$this->PluginCity_City_GetCountTender($oUserCity));
			}
			if (Config::Get('module.city.use_activate') and $this->oUserCurrent->isAdministrator())
				$this->Viewer_Assign('iCountModeration',$this->PluginCity_City_GetCountCityesByFilter(array('active' => 0)));
		}

		/**
		 * Загружаем переменные в шаблон
		 */
		$this->Viewer_Assign('sCityTemplatePath',$this->getTemplatePathPlugin()); //`$aTemplatePathPlugin['city']`
		$this->Viewer_Assign('sMenuHeadItemSelect',$this->sMenuHeadItemSelect);
		$this->Viewer_Assign('sMenuItemSelect',$this->sMenuItemSelect);
		$this->Viewer_Assign('sMenuSubItemSelect',$this->sMenuSubItemSelect);

		$this->Viewer_Assign('iCountNewCityes',$this->PluginCity_City_GetCountCityesByFilter(array('new_time' => Config::Get('module.city.new_time'))));
		$this->Viewer_Assign('iCountCityes',$this->PluginCity_City_GetCountCityesByFilter());
	}
}
?>

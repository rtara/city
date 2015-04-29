<?php
/**
 *	Module "City"
 *	Author: Grebenkin Anton
 *	Contact e-mail: 4freework@gmail.com
 */

class PluginCity_ModuleCity_EntityCity extends Entity {

	/**
	 * Массив объектов(не всегда) для дополнительных данных компании
	 *
	 * @var array
	 */
	protected $aPrefs=null;

	public function Init() {
		parent::Init();
		$this->aValidateRules[]=array('city_name','string','max'=>255,'min'=>2,'allowEmpty'=>false,'label'=>$this->Lang_Get('plugin.city.city_add_name'),'on'=>array('add','edit'));
		$this->aValidateRules[]=array('city_description','string','max'=>Config::Get('module.city.description_len'),'min'=>10,'allowEmpty'=>false,'label'=>$this->Lang_Get('plugin.city.city_add_description'),'on'=>array('add','edit'));
		$this->aValidateRules[]=array('city_about','string','max'=>Config::Get('module.city.about_len'),'min'=>10,'allowEmpty'=>true,'label'=>$this->Lang_Get('plugin.city.city_edit_about_title'),'on'=>array('add','edit'));

		$this->aValidateRules[]=array('city_tags','tags','count'=>15,'label'=>$this->Lang_Get('plugin.city.city_add_tags'),'allowEmpty'=>false,'on'=>array('add','edit'));
		$this->aValidateRules[]=array('city_vacancies','string','max'=>70000,'min'=>10,'allowEmpty'=>true,'label'=>$this->Lang_Get('plugin.city.city_edit_vacancy'),'on'=>array('edit'));

		$this->aValidateRules[]=array('city_email','email','label'=>$this->Lang_Get('plugin.city.city_edit_email'),'allowEmpty'=>true,'on'=>array('contacts'));
		$this->aValidateRules[]=array('city_address','string','max'=>100,'min'=>2,'allowEmpty'=>true,'label'=>$this->Lang_Get('plugin.city.city_edit_address'),'on'=>array('contacts'));
		$this->aValidateRules[]=array('city_site','string','max'=>100,'min'=>4,'allowEmpty'=>true,'label'=>$this->Lang_Get('plugin.city.city_edit_site'),'on'=>array('contacts'));
		$this->aValidateRules[]=array('city_phone','string','max'=>50,'min'=>4,'allowEmpty'=>true,'label'=>$this->Lang_Get('plugin.city.city_edit_phone'),'on'=>array('contacts'));
		$this->aValidateRules[]=array('city_fax','string','max'=>50,'min'=>4,'allowEmpty'=>true,'label'=>$this->Lang_Get('plugin.city.city_edit_fax'),'on'=>array('contacts'));
		$this->aValidateRules[]=array('city_skype','string','max'=>50,'min'=>4,'allowEmpty'=>true,'label'=>$this->Lang_Get('plugin.city.city_edit_skype'),'on'=>array('contacts'));
		$this->aValidateRules[]=array('city_icq','string','max'=>14,'min'=>4,'allowEmpty'=>true,'label'=>$this->Lang_Get('plugin.city.city_edit_icq'),'on'=>array('contacts'));
		$this->aValidateRules[]=array('city_contact_name','string','max'=>50,'min'=>4,'allowEmpty'=>true,'label'=>$this->Lang_Get('plugin.city.city_edit_contact_name'),'on'=>array('contacts'));
		$this->aValidateRules[]=array('city_contact_info','string','max'=>255,'min'=>5,'allowEmpty'=>true,'label'=>$this->Lang_Get('plugin.city.city_edit_contact_info'),'on'=>array('contacts'));
		$this->aValidateRules[]=array('city_latitude','string','max'=>20,'min'=>1,'allowEmpty'=>true,'on'=>array('contacts'));
		$this->aValidateRules[]=array('city_longitude','string','max'=>20,'min'=>1,'allowEmpty'=>true,'on'=>array('contacts'));

	}

	public function getId() {
		return $this->_getDataOne('city_id');
	}
	public function getBlogId() {
		return $this->_getDataOne('blog_id');
	}
	public function getOwnerId() {
		return $this->_getDataOne('user_owner_id');
	}
	public function getType() {
		return $this->_getDataOne('city_type');
	}
	public function getUrl() {
		return $this->_getDataOne('city_url');
	}
	public function getUrlFull() {
		return Router::GetPath('city').$this->getUrl();
	}
	public function getName() {
		return $this->_getDataOne('city_name');
	}
	public function getLegalName() {
		return $this->_getDataOne('city_name_legal');
	}
	public function getDescription() {
		return $this->_getDataOne('city_description');
	}
	public function getDateBasis() {
		return $this->_getDataOne('city_date_basis');
	}
	public function getDateAdd() {
		return $this->_getDataOne('city_date_add');
	}
	public function getDateEdit() {
		return $this->_getDataOne('city_date_edit');
	}
	public function getRating() {
        return number_format(round($this->_getDataOne('city_rating'),2), 2, '.', '');
    }
	public function getTags() {
		return $this->_getDataOne('city_tags');
	}
	public function getTagsLink() {
		$aTags=explode(',',$this->getTags());
		foreach ($aTags as $key => $value) {
			$aTags[$key]='<a href="'.Router::GetPath('cityes').'tag/'.htmlspecialchars($value).'/" class="smalltags">'.htmlspecialchars($value).'</a>';
		}
		return trim(join(', ',$aTags));
	}
	public function getVacancies() {
		return $this->_getDataOne('city_vacancies');
	}
	public function getLogo() {
		return $this->_getDataOne('city_logo');
	}
	public function getLogoType() {
		return $this->_getDataOne('city_logo_type');
	}
	public function getLogoPath($iSize=48) {
		if ($this->getLogo()){
			if ($iSize==0)
				return Config::Get('module.city.image_web_path').$this->getId()."/logo_city_{$this->getUrl()}".'.'.$this->getLogoType();
			else
				return Config::Get('module.city.image_web_path').$this->getId()."/logo_city_{$this->getUrl()}_".$iSize.'x'.$iSize.'.'.$this->getLogoType();
		} else {
			return Config::Get('path.static.skin').'/images/avatar_blog_'.$iSize.'x'.$iSize.'.png';
		}
	}
	public function getIsFavourite()
	{
		return $this->_getDataOne('city_is_favourite');
	}
	public function getCountFavourite()
	{
		return $this->_getDataOne('city_count_favourite');
	}
	public function getCountFeedback() {
		return $this->_getDataOne('city_count_feedback');
	}
	public function getCountVote() {
		return $this->_getDataOne('city_count_vote');
	}
	public function getCountWorkers() {
		return $this->_getDataOne('city_count_workers');
	}
	public function getCountSubscribe() {
		return $this->_getDataOne('city_count_subscribe');
	}
	public function getUserIsJoin() {
		return $this->_getDataOne('user_is_join');
	}
	public function getUserIsAdministrator() {
		return $this->_getDataOne('user_is_administrator');
	}
	public function getUserIsModerator() {
		return $this->_getDataOne('user_is_moderator');
	}

	public function getDateRead() {
		return $this->_getDataOne('date_read');
	}
	public function getActive() {
		return $this->_getDataOne('city_active');
	}
	public function getFileName() {
		return $this->_getDataOne('city_file_name');
	}
	public function getFilePath() {
		return Config::Get('path.root.web').Config::Get('path.uploads.root').'/city/'.$this->getId().'/'.$this->getFileName();
	}
	public function getSite($bHtml=false) {
		if ($bHtml) {
			if (strpos($this->_getDataOne('city_site'),'http://')!==0) {
				return 'http://'.$this->_getDataOne('city_site');
			}
		}
		return $this->_getDataOne('city_site');
	}
	public function getEmail() {
		return $this->_getDataOne('city_email');
	}
	public function getPhone() {
		return $this->_getDataOne('city_phone');
	}
	public function getFax() {
		return $this->_getDataOne('city_fax');
	}
	public function getContactName() {
		return $this->_getDataOne('city_contact_name');
	}
	public function getCountry() {
		return $this->_getDataOne('city_country');
	}
	public function getCity() {
		return $this->_getDataOne('city_city');
	}
	public function getAddress() {
		return $this->_getDataOne('city_address');
	}
	public function getLatitude() {
		return $this->_getDataOne('city_latitude');
	}
	public function getLongitude() {
		return $this->_getDataOne('city_longitude');
	}
	public function getSkype() {
		return $this->_getDataOne('city_skype');
	}
	public function getSkypeLink() {
		if ($this->getSkype())
			return str_replace('{*}', $this->getSkype(), '<a href="skype:{*}">{*}</a>');
		return null;
	}
	public function getIcq() {
		return $this->_getDataOne('city_icq');
	}
	public function getContactInfo() {
		return $this->_getDataOne('city_contact_info');
	}
	public function getAbout() {
		return $this->_getDataOne('city_about');
	}
	public function getAboutSource() {
		return $this->_getDataOne('city_about_source');
	}
	public function getTopicIdLast() {
		return $this->_getDataOne('topic_id_last');
	}
	public function getTopicLast() {
		return $this->_getDataOne('topic_last');
	}
	public function getGeoTarget() {
		return $this->_getDataOne('geo_target');
	}

	/**
	 * Возвращает объект подписки на новые отзывы к компании
	 *
	 * @return ModuleSubscribe_EntitySubscribe|null
	 */
	public function getSubscribeNewFeedback() {
		if (!($oUserCurrent=$this->User_GetUserCurrent())) {
			return null;
		}
		return $this->Subscribe_GetSubscribeByTargetAndMail('city_new_feedback',$this->getId(),$oUserCurrent->getMail());
	}

	public function getSubscribeNewTopic() {
		if (!($oUserCurrent=$this->User_GetUserCurrent())) {
			return null;
		}
		return $this->Subscribe_GetSubscribeByTargetAndMail('city_new_topic',$this->getId(),$oUserCurrent->getMail());
	}

	public function getTariffId() {
		return $this->_getDataOne('city_tariff_id');
	}

	public function getTariff() {
		return $this->PluginCity_Pay_GetTariff($this->getTariffId());
	}

	public function IsAllowTariff($sValue) {
		return in_array($sValue,$this->getTariff()->getRights());
	}
	public function getDateTariffEnd() {
		return $this->_getDataOne('city_date_tariff_end');
	}


	/*  SET   */


    public function setId($data) {
        $this->_aData['city_id']=$data;
    }
    public function setOwnerId($data) {
        $this->_aData['user_owner_id']=$data;
    }
    public function setType($data) {
        $this->_aData['city_type']=$data;
    }
    public function setUrl($data) {
        $this->_aData['city_url']=$data;
    }
    public function setName($data) {
        $this->_aData['city_name']=$data;
    }
    public function setLegalName($data) {
        $this->_aData['city_name_legal']=$data;
    }
    public function setDescription($data) {
        $this->_aData['city_description']=$data;
    }
    public function setSite($data) {
        $this->_aData['city_site']=$data;
    }
 	public function setEmail($data) {
        $this->_aData['city_email']=$data;
    }
	public function setPhone($data) {
        $this->_aData['city_phone']=$data;
    }
	public function setFax($data) {
        $this->_aData['city_fax']=$data;
    }
	public function setContactName($data) {
        $this->_aData['city_contact_name']=$data;
    }
	public function setDateBasis($data) {
        $this->_aData['city_date_basis']=$data;
    }
    public function setDateAdd($data) {
        $this->_aData['city_date_add']=$data;
    }   
    public function setDateEdit($data) {
        $this->_aData['city_date_edit']=$data;
    } 
    public function setRating($data) {
        $this->_aData['city_rating']=$data;
    }
	public function setTags($data) {
        $this->_aData['city_tags']=$data;
    }
	public function setCountry($data) {
        $this->_aData['city_country']=$data;
    }
	public function setCity($data) {
        $this->_aData['city_city']=$data;
    }
	public function setAddress($data) {
        $this->_aData['city_address']=$data;
    }
	public function setBlogId($data) {
        $this->_aData['blog_id']=$data;
    }
	public function setVacancies($data) {
        $this->_aData['city_vacancies']=$data;
    }
    public function setIsFavourite($data) {
        $this->_aData['city_is_favourite']=$data;
    }
    public function setCountFavourite($data) {
        $this->_aData['city_count_favourite']=$data;
    }
	public function setCountVote($data) {
        $this->_aData['city_count_vote']=$data;
    }
	public function setCountFeedback($data) {
        $this->_aData['city_count_feedback']=$data;
    }
    public function setCountWorkers($data) {
        $this->_aData['city_count_workers']=$data;
    }
	public function setCountSubscribe($data) {
		$this->_aData['city_count_subscribe']=$data;
	}
    public function setLogo($data) {
        $this->_aData['city_logo']=$data;
    }
    public function setLogoType($data) {
        $this->_aData['city_logo_type']=$data;
    }

    public function setUserIsJoin($data) {
        $this->_aData['user_is_join']=$data;
    }
    public function setUserIsAdministrator($data) {
        $this->_aData['user_is_administrator']=$data;
    }
    public function setUserIsModerator($data) {
        $this->_aData['user_is_moderator']=$data;
    }

    public function setDateRead($data) {
        $this->_aData['date_read']=$data;
    }
    public function setActive($data) {
        $this->_aData['city_active']=$data;
    }
    public function setLatitude($data) {
        $this->_aData['city_latitude']=$data;
    }
    public function setLongitude($data) {
        $this->_aData['city_longitude']=$data;
    }

    public function setFileName($data) {
        $this->_aData['city_file_name']=$data;
    }


    public function setSkype($data) {
        $this->_aData['city_skype']=$data;
    }
    public function setIcq($data) {
        $this->_aData['city_icq']=$data;
    }
    public function setContactInfo($data) {
        $this->_aData['city_contact_info']=$data;
    }
	public function setAbout($data) {
		$this->_aData['city_about']=$data;
	}
	public function setAboutSource($data) {
		$this->_aData['city_about_source']=$data;
	}
	public function setTopicIdLast($data) {
		$this->_aData['topic_id_last']=$data;
	}
	public function setTopicLast($data) {
		$this->_aData['topic_last']=$data;
	}
	public function setGeoTarget($data) {
		$this->_aData['geo_target']=$data;
	}
	public function setTariffId($data) {
		$this->_aData['city_tariff_id']=$data;
	}
	public function setDateTariffEnd($data) {
		return $this->_aData['city_date_tariff_end']=$data;
	}

	/**
	 * Возвращает фотографии из компании
	 *
	 * @param int|null $iFromId	ID с которого начинать  выборку
	 * @param int|null $iCount	Количество
	 * @return array
	 */
	public function getPhotos($iFromId = null, $iCount = null) {
		return $this->PluginCity_Content_GetPhotosByCityId($this->getId(), $iFromId, $iCount);
	}
	/**
	 * Возвращает количество фотографий в компании
	 *
	 * @return int|null
	 */
	public function getPhotoCount() {
		return $this->getPrefsValue('count_photo');
	}
	/**
	 * Возвращает ID главной фото в компании
	 *
	 * @return int|null
	 */
	public function getMainPhotoId() {
		return $this->getPrefsValue('main_photo_id');
	}
	/**
	 * Устанавливает ID главной фото в компании
	 *
	 * @param int $data
	 */
	public function setMainPhotoId($data) {
		$this->setPrefsValue('main_photo_id',$data);
	}
	/**
	 * Устанавливает количество фотографий в компании
	 *
	 * @param int $data
	 */
	public function setPhotoCount($data) {
		$this->setPrefsValue('count_photo',$data);
	}



	// Расширенные данные для компаний

	public function getUseBrandImage() {
		if ($this->getPrefsValue('use_bi') == 1)
			return true;
		return false;
	}

	public function getBrandImage() {
		return $this->getPrefsValue('brand_image');
	}

	public function getBackgroundColor() {
		return $this->getPrefsValue('bg_color');
	}

	public function getBrandImagePreview() {
		if ($this->getBrandImage()){
			$aPathInfo=pathinfo($this->getBrandImage());
			return $aPathInfo['dirname'].'/'.$aPathInfo['filename'].'_preview.'.$aPathInfo['extension'];
		}
		return null;
	}

	public function getTwitterScreenName() {
		return $this->getPrefsValue('w_tw_sn');
	}
	public function getFbUrl() {
		return $this->getPrefsValue('w_fb_url');
	}
	public function getVkUrl() {
		return $this->getPrefsValue('w_vk_url');
	}
	public function getVkId() {
		return $this->getPrefsValue('w_vk_id');
	}

	public function setUseBrandImage($data) {
		$this->setPrefsValue('use_bi',$data);
	}
	public function setBackgroundColor($data) {
		$this->setPrefsValue('bg_color',$data);
	}

	public function setBrandImage($data) {
		$this->setPrefsValue('brand_image',$data);
	}

	public function setTwitterScreenName($data) {
		$this->setPrefsValue('w_tw_sn',$data);
	}

	public function setFbUrl($data) {
		$this->setPrefsValue('w_fb_url',$data);
	}

	public function setVkUrl($data) {
		$this->setPrefsValue('w_vk_url',$data);
	}

	public function setVkId($data) {
		$this->setPrefsValue('w_vk_id',$data);
	}

	public function getWidgetVisible($WidgetName) {
		return $this->getPrefsValue('w_visible_'.$WidgetName);
	}
	public function setWidgetVisible($WidgetName,$data) {
		$this->setPrefsValue('w_visible_'.$WidgetName,$data);
	}


	/**
	 * Возвращает сериализованные строку дополнительный данных топика
	 *
	 * @return string
	 */
	public function getPrefs() {
		return $this->_getDataOne('city_prefs') ? $this->_getDataOne('city_prefs') : serialize('');
	}

	/**
	 * Извлекает сериализованные данные топика
	 */
	protected function extractPrefs() {
		if (is_null($this->aPrefs)) {
			$this->aPrefs=@unserialize($this->getPrefs());
		}
	}

	/**
	 * Устанавливает значение нужного параметра
	 *
	 * @param string $sName	Название параметра/данных
	 * @param mixed $data	Данные
	 */
	protected function setPrefsValue($sName,$data) {
		$this->extractPrefs();
		$this->aPrefs[$sName]=$data;
		$this->setPrefs($this->aPrefs);
	}
	/**
	 * Извлекает значение параметра
	 *
	 * @param string $sName	Название параметра
	 * @return null|mixed
	 */
	protected function getPrefsValue($sName) {
		$this->extractPrefs();
		if (isset($this->aPrefs[$sName])) {
			return $this->aPrefs[$sName];
		}
		return null;
	}

	/**
	 * Устанавливает сериализованную строчку дополнительных данных
	 *
	 * @param string $data
	 */
	public function setPrefs($data) {
		$this->_aData['city_prefs']=serialize($data);
	}

}
?>
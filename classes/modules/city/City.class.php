<?php
/**
 *	Module "City"
 *	Author: Grebenkin Anton
 *	Contact e-mail: 4freework@gmail.com
 */
class PluginCity_ModuleCity extends Module {
	protected $oMapper;
	protected $oUserCurrent=null;

	/**
	 * Инициализация
	 */
	public function Init() {
		$this->oMapper=Engine::GetMapper(__CLASS__);
		$this->oUserCurrent = $this->User_GetUserCurrent();
		$this->oMapper->SetUserCurrent($this->oUserCurrent);
	}

	/**
	 * Получает дополнительные данные(объекты) для компаний по их ID
	 *
	 * @param $aCityId ид компании
	 * @param null $aAllowData
	 * @return array массив компаний
	 */
	public function GetCityesAdditionalData($aCityId,$aAllowData=null) {
		if (is_null($aAllowData)) {
			$aAllowData=array('owner'=>array(),'blog'=>array('owner'=>array(),'relation_user'),'vote','favourite','geo_target','topic_last'=>array('blog'=>array(),'user'=>array()));
		}
		func_array_simpleflip($aAllowData);
		if (!is_array($aCityId)) {
			$aCityId=array($aCityId);
		}
		$aCityes=$this->GetCityesByArrayId($aCityId);
		/**
		 * Формируем ID дополнительных данных, которые нужно получить
		 */
		$aUserId=array();
		$aBlogId=array();
		$aTopicId=array();
		$aPhotoMainId=array();
		foreach ($aCityes as $oCity) {
			if (isset($aAllowData['owner'])) {
				$aUserId[]=$oCity->getOwnerId();
			}
			if (isset($aAllowData['blog'])) {
				$aBlogId[]=$oCity->getBlogId();
			}
			if (isset($aAllowData['topic_last'])) {
				$aTopicId[]=$oCity->getTopicIdLast();
			}
			if ($oCity->getMainPhotoId())	{
				$aPhotoMainId[]=$oCity->getMainPhotoId();
			}
		}
		$aGeoTargets=array();
		if (isset($aAllowData['geo_target'])) {
			$aGeoTargets=$this->Geo_GetCityTargetsByArray($aCityId);
			//$aGeoTargets=$this->Geo_GetTargetsByTargetArray('city',$aCityId);
		}
		/**
		 * Получаем дополнительные данные
		 */
		$aUsers=isset($aAllowData['owner']) && is_array($aAllowData['owner']) ? $this->User_GetUsersAdditionalData($aUserId,$aAllowData['owner']) : $this->User_GetUsersAdditionalData($aUserId);

		$aBlogs = array();
		if (isset($aAllowData['blog'])) {
			$aBlogs=isset($aAllowData['blog']) && is_array($aAllowData['blog']) ? $this->Blog_GetBlogsAdditionalData($aBlogId,$aAllowData['blog']) : $this->Blog_GetBlogsAdditionalData($aBlogId);
		}
		$aTopics = array();
		if (isset($aAllowData['topic_last'])) {
			$aTopics=isset($aAllowData['topic_last']) && is_array($aAllowData['topic_last']) ? $this->Topic_GetTopicsAdditionalData($aTopicId,$aAllowData['topic_last']) : $this->Topic_GetTopicsAdditionalData($aTopicId,array('blog'=>array(),'user'=>array()));
		}

		$aFavouriteComapanies=array();
		if (isset($aAllowData['favourite']) and $this->oUserCurrent) {
			$aFavouriteComapanies=$this->Favourite_GetFavouritesByArray($aCityId,'city',$this->oUserCurrent->getId());
		}

		$aCityVote=array();
		if (isset($aAllowData['vote']) and $this->oUserCurrent) {
			$aCityVote=$this->Vote_GetVoteByArray($aCityId,'city',$this->oUserCurrent->getId());
		}
		$aMainPhotos=$this->PluginCity_Content_GetCityPhotosByArrayId($aPhotoMainId);
		/**
		 * Добавляем данные к результату - списку топиков
		 */
		foreach ($aCityes as $oCity) {
			if (isset($aUsers[$oCity->getOwnerId()])) {
				$oCity->setOwner($aUsers[$oCity->getOwnerId()]);
			} else {
				$oCity->setOwner(null);
			}
			if (isset($aBlogs[$oCity->getBlogId()])) {
				$oCity->setBlog($aBlogs[$oCity->getBlogId()]);
				$oCity->setUserIsJoin($oCity->getBlog()->getUserIsJoin());
				$oCity->setUserIsAdministrator($oCity->getBlog()->getUserIsAdministrator());
				$oCity->setUserIsModerator($oCity->getBlog()->getUserIsModerator());
			} else {
				$oCity->setBlog(null);
			}

			if (isset($aTopics[$oCity->getTopicIdLast()])) {
				$oCity->setTopicLast($aTopics[$oCity->getTopicIdLast()]);
			} else {
				$oCity->setTopicLast(null);
			}

			if (isset($aFavouriteComapanies[$oCity->getId()])) {
				$oCity->setIsFavourite(true);
			} else {
				$oCity->setIsFavourite(false);
			}
			if (isset($aCityVote[$oCity->getId()])) {
				$oCity->setVote($aCityVote[$oCity->getId()]);
			} else {
				$oCity->setVote(null);
			}

			if (isset($aGeoTargets[$oCity->getId()])) {
				$aTargets=$aGeoTargets[$oCity->getId()];
				$oCity->setGeoTarget(isset($aTargets[0]) ? $aTargets[0] : null);
			} else {
				$oCity->setGeoTarget(null);
			}

			if (isset($aMainPhotos[$oCity->getMainPhotoId()])) {
				$oCity->setMainPhoto($aMainPhotos[$oCity->getMainPhotoId()]);
			} else {
				$oCity->setMainPhoto(null);
			}

		}
		return $aCityes;
	}

	/**
	 * Получает массив компании по массиву id компаний
	 *
	 * @param $aCityId
	 * @return array
	 */
	public function GetCityesByArrayId($aCityId) {
		if (!$aCityId) {
			return array();
		}
		if (!is_array($aCityId)) {
			$aCityId=array($aCityId);
		}
		$aCityId=array_unique($aCityId);
		$aCityes=array();
		$aCityIdNotNeedQuery=array();
		/**
		 * Делаем мульти-запрос к кешу
		 */
		$aCacheKeys = func_build_cache_keys($aCityId,'city_');
		if (false !== ($data = $this->Cache_Get($aCacheKeys))) {
			/**
			 * проверяем что досталось из кеша
			 */
			foreach ($aCacheKeys as $sValue => $sKey ) {
				if (array_key_exists($sKey,$data)) {
					if ($data[$sKey]) {
						$aCityes[$data[$sKey]->getId()]=$data[$sKey];
					} else {
						$aCityIdNotNeedQuery[]=$sValue;
					}
				}
			}
		}
		/**
		 * Смотрим каких юзеров не было в кеше и делаем запрос в БД
		 */
		$aCityIdNeedQuery=array_diff($aCityId,array_keys($aCityes));
		$aCityIdNeedQuery=array_diff($aCityIdNeedQuery,$aCityIdNotNeedQuery);
		$aCityIdNeedStore=$aCityIdNeedQuery;
		if ($data = $this->oMapper->GetCityesByArrayId($aCityIdNeedQuery)) {
			foreach ($data as $oCity) {
				/**
				 * Добавляем к результату и сохраняем в кеш
				 */
				$aCityes[$oCity->getId()]=$oCity;
				$this->Cache_Set($oCity, "city_{$oCity->getId()}", array(), 60*60*24*4);
				$aCityIdNeedStore=array_diff($aCityIdNeedStore,array($oCity->getId()));
			}
		}
		/**
		 * Сохраняем в кеш запросы не вернувшие результата
		 */
		foreach ($aCityIdNeedStore as $sId) {
			$this->Cache_Set(null, "city_{$sId}", array(), 60*60*24*4);
		}
		/**
		 * Сортируем результат согласно входящему массиву
		 */
		return func_array_sort_by_keys($aCityes,$aCityId);
	}

	/**
	 * Получает компании по фильтру
	 *
	 * @param $aFilter фильтр
	 * @param $aOrder сортировка
	 * @param $iCurrPage текущая страница
	 * @param $iPerPage количество записей на странице
	 * @param null $aAllowData доступные данные
	 * @return array
	 */
	public function GetCityesByFilter($aFilter,$aOrder,$iCurrPage=1,$iPerPage=100,$aAllowData=null) {
		// если используется активация, то показываем только активированные
		if (Config::Get('module.city.use_activate') and !isset($aFilter['active']) and !isset($aFilter['all']))
			$aFilter['active'] = 1;
		$sKey="cityes_filter_".serialize($aFilter).serialize($aOrder)."_{$iCurrPage}_{$iPerPage}";
		if (false === ($data = $this->Cache_Get($sKey))) {
			$data = array('collection'=>$this->oMapper->GetCityesByFilter($aFilter,$aOrder,$iCount,$iCurrPage,$iPerPage),'count'=>$iCount);
			$this->Cache_Set($data, $sKey, array("city_update","city_new"), 60*60*24*2);
		}
		$data['collection']=$this->GetCityesAdditionalData($data['collection'],$aAllowData);
		return $data;
	}

	/**
	 * Получает комании в которых работает пользователь
	 *
	 * @param $sUserId ИД пользователя
	 * @param bool $bWithOwner включать создателя компании, или только работники
	 * @return mixed
	 */
	public function GetCityesByUser($sUserId,$bWithOwner=false) {
		$Ids = $this->Blog_GetBlogUsersByUserId($sUserId,array(ModuleBlog::BLOG_USER_ROLE_USER,ModuleBlog::BLOG_USER_ROLE_MODERATOR,ModuleBlog::BLOG_USER_ROLE_ADMINISTRATOR), true);
		if ($bWithOwner){ // Добавляем еще хозяев компаний
			$Ids = array_merge($this->Blog_GetBlogsByOwnerId($sUserId,true), $Ids);
		}
		$aCityes = $this->GetCityesByFilter(array('blog_id' => $Ids),array(),1,1000);
		return $aCityes['collection'];
	}

	/*
	 * Получает список компаний по тегу
	 */
	public function GetCityesByTag($sTag,$iPage,$iPerPage) {
		if (false === ($data = $this->Cache_Get("city_tag_{$sTag}_{$iPage}_{$iPerPage}"))) {
			$data = array('collection'=>$this->oMapper->GetCityesByTag($sTag,$iCount,$iPage,$iPerPage),'count'=>$iCount);
			$this->Cache_Set($data, "city_tag_{$sTag}_{$iPage}_{$iPerPage}", array('city_update','city_new'), 60*60*24);
		}
		$data['collection'] = $this->GetCityesByArrayId($data['collection']);
		return $data;
	}


	/**
	 * Получить компанию по идентификатору
	 *
	 * @param $sCityId
	 * @return null
	 */
	public function GetCityById($sCityId) {
		$aCityes = $this->GetCityesAdditionalData($sCityId,array('owner'=>array(),'blog'=>array('owner'=>array(),'relation_user'),'vote','favourite','geo_target'));
		if (isset($aCityes[$sCityId])) {
			return $aCityes[$sCityId];
		}
		return null;
	}

	/**
	 * Получить компанию по id блога
	 *
	 * @param $sBlogId
	 * @return PluginCity_ModuleCity_EntityCity
	 */
	public function GetCityByBlogId($sBlogId) {
		if (false === ($id = $this->Cache_Get("city_blog_{$sBlogId}"))) {
			if ($id = $this->oMapper->GetCityByBlogId($sBlogId)) {
				$this->Cache_Set($id, "city_blog_{$sBlogId}", array("city_update_{$id}",'city_new'), 60*60*24);
			} else {
				$this->Cache_Set(null, "city_blog_{$sBlogId}", array('city_update','city_new'), 60*60*24);
			}
		}
		return $this->GetCityById($id);
	}

	/**
	 * Получить компанию по алиасу
	 *
	 * @param $sCityUrl
	 * @return PluginCity_ModuleCity_EntityCity
	 */
	public function GetCityByUrl($sCityUrl) {
		if (false === ($id = $this->Cache_Get("city_url_{$sCityUrl}"))) {
			if ($id = $this->oMapper->GetCityByUrl($sCityUrl)) {
				$this->Cache_Set($id, "city_url_{$sCityUrl}", array("city_update_{$id}",'city_new'), 60*60*24);
			} else {
				$this->Cache_Set(null, "city_url_{$sCityUrl}", array('city_update','city_new'), 60*60*24);
			}
		}
		return $this->GetCityById($id);
	}

	public function GetCityExist($sCityUrl) {
		if ($this->GetCityByUrl($sCityUrl))
			return true;
		return false;
	}

	/**
	 * Получить компанию по названию
	 *
	 * @param $sName
	 * @return PluginCity_ModuleCity_EntityCity
	 */
	public function GetCityByName($sName) {
		if (false === ($id = $this->Cache_Get("city_name_{$sName}"))) {
			if ($id = $this->oMapper->GetCityByName($sName)) {
				$this->Cache_Set($id, "city_name_{$sName}", array("city_update_{$id}",'city_new'), 60*60*24);
			} else {
				$this->Cache_Set(null, "city_name_{$sName}", array('city_update','city_new'), 60*60*24);
			}
		}
		return $this->GetCityById($id);
	}

	/*
	 * Получает список тегов компаний по первым буквам тега
	 */
	public function GetCityTagsByLike($sTag,$iLimit) {
		if (false === ($data = $this->Cache_Get("city_tag_like_{$sTag}_{$iLimit}"))) {
			$data = $this->oMapper->GetCityTagsByLike($sTag,$iLimit);
			$this->Cache_Set($data, "city_tag_like_{$sTag}_{$iLimit}", array("city_update","city_new"), 60*60*24);
		}
		return $data;
	}

	/*
	 * Получает список тегов компаний
	 */
	public function GetCityTags($iLimit) {
		if (false === ($data = $this->Cache_Get("citytag_{$iLimit}"))) {
			$data = $this->oMapper->GetCityTags($iLimit);
			$this->Cache_Set($data, "citytag_{$iLimit}", array('city_update','city_new'), 60*60*24);
		}
		return $data;
	}

	/**
	 * Получает теги компании по id
	 *
	 * @param $iCityId
	 * @return mixed
	 */
	public function GetCityTagsByCityId($iCityId) {
		if (false === ($data = $this->Cache_Get("city_category_{$iCityId}"))) {
			$data = $this->oMapper->GetCityTagsByCityId($iCityId);
			$this->Cache_Set($data, "city_category_{$iCityId}", array('city_update','city_new'), 60*60*24);
		}
		return $data;
	}

	/**
	 * Количество компаний по фильтру
	 *
	 * @param array() $aFilter
	 * @return int
	 */
	public function GetCountCityesByFilter($aFilter = array()) {
		if (Config::Get('module.city.use_activate') and !isset($aFilter['active']))
			$aFilter['active'] = 1;
		$s=serialize($aFilter);
		if (false === ($data = $this->Cache_Get("city_count_{$s}"))) {
			$data = $this->oMapper->GetCountCityesByFilter($aFilter);
			$this->Cache_Set($data, "city_count_{$s}", array('city_update','city_new'), 60*60*24);
		}
		return 	$data;
	}

	/**
	 * Получает количество избранных пользователем компаний
	 *
	 * @param $sUserId
	 * @return int
	 */
	public function GetCountFavCityesByUser($sUserId) {
		$aFavCityes = $this->GetFavouriteCityesByUserId($sUserId,1,100);
		return 	$aFavCityes['count'];
	}

	/**
	 * Количество заявок которые поступили на вступление в компанию
	 *
	 * @param $oCity
	 * @return int
	 */
	public function GetCountTender($oCity) {
		if (false === ($data = $this->Cache_Get("city_{$oCity->getId()}_tender_count"))) {
			$aBlogUsers = $this->Blog_GetBlogUsersByBlogId($oCity->getBlogId(), array(ModuleBlog::BLOG_USER_ROLE_GUEST));
			$data = $aBlogUsers['count'];
			$this->Cache_Set($data, "city_{$oCity->getId()}_tender_count}", array("blog_relation_change_blog_{$oCity->getBlogId()}"), 60*60*24);
		}
		return $data;
	}


	/**
	 * Добавляет компанию
	 *
	 * @param PluginCity_ModuleCity_EntityCity $oCity
	 * @return bool|PluginCity_ModuleCity_EntityCity
	 */
	public function AddCity(PluginCity_ModuleCity_EntityCity $oCity) {
		// Создаем блог компании
		if ($oBlog = $this->CreateCityBlog($oCity)){
			$oCity->setBlogId($oBlog->getId());

			// Создаем саму компанию
			if ($sId=$this->oMapper->AddCity($oCity)) {
				$oCity->setId($sId);
				// разбираем и добавляем теги.
				$aTags=explode(',',$oCity->getTags());
				foreach ($aTags as $sTag) {
					$oTag=Engine::GetEntity('PluginCity_City_CityTag');//new ModuleCity_EntityCityTag();
					$oTag->setCityId($oCity->getId());
					$oTag->setUserId($oCity->getOwnerId());
					$oTag->setText(trim($sTag));
					$this->oMapper->AddCityTag($oTag);
				}

				//чистим зависимые кеши
				$this->Cache_Clean(Zend_Cache::CLEANING_MODE_MATCHING_TAG,array('city_new'));
				return $oCity;
			}
		}
		return false;
	}

	/**
	 * Удаление компании
	 * Если тип таблиц в БД InnoDB, то удалятся всё связи по компании.
	 *
	 * @param $sCityId
	 * @return mixed
	 */
	public function DeleteCity($sCityId) {
		$oCity = $this->GetCityById($sCityId);
		$this->Blog_DeleteBlog($oCity->getBlogId());
		$this->Comment_DeleteCommentByTargetId($sCityId,'city');
		$this->Comment_DeleteCommentOnlineByTargetId($sCityId,'city');
		$this->Favourite_DeleteFavouriteByTargetId($sCityId,'city');
		$this->Geo_DeleteTargetsByTarget('city',$sCityId);
		$this->DeleteCityTagsByCityId($oCity->getId());

		//чистим зависимые кеши
		$this->Cache_Clean(Zend_Cache::CLEANING_MODE_MATCHING_TAG,array('city_update',"city_update_{$oCity->getId()}"));
		$this->Cache_Delete("city_{$sCityId}");
		return $this->oMapper->DeleteCity($sCityId);
	}

	/**
	 * Удаление тегов компании
	 *
	 * @param $sCityId
	 * @return mixed
	 */
	public function DeleteCityTagsByCityId($sCityId) {
		return $this->oMapper->DeleteCityTagsByCityId($sCityId);
	}

	/**
	 * Обновляет компанию
	 *
	 * @param PluginCity_ModuleCity_EntityCity $oCity
	 * @return bool
	 */
	public function UpdateCity(PluginCity_ModuleCity_EntityCity $oCity) {
		if ($this->oMapper->UpdateCity($oCity)) {
			$this->UpdateCityBlog($oCity);
			//чистим зависимые кеши
			$this->Cache_Clean(Zend_Cache::CLEANING_MODE_MATCHING_TAG,array('city_update',"city_update_{$oCity->getId()}"));
			$this->Cache_Delete("city_{$oCity->getId()}");
			return true;
		}
		return false;
	}

	/**
	 * Обновляет теги компании
	 * @param PluginCity_ModuleCity_EntityCity $oCity
	 * @return bool
	 */
	public function UpdateCityTags(PluginCity_ModuleCity_EntityCity $oCity) {
		// разбираем и добавляем теги.
		$aTags = explode(',', $oCity->getTags());
		$this->DeleteCityTagsByCityId($oCity->getId());
		foreach ($aTags as $sTag) {
			$oTag = Engine::GetEntity('PluginCity_City_CityTag'); //ModuleCity_EntityCityTag();
			$oTag->setCityId($oCity->getId());
			$oTag->setUserId($oCity->getOwnerId());
			$oTag->setText($sTag);
			$this->oMapper->AddCityTag($oTag);
		}
		return true;
	}

	/**
	 * Обновляет настройки компании
	 *
	 * @param PluginCity_ModuleCity_EntityCity $oCity
	 * @return bool
	 */
	public function UpdateCityPrefs(PluginCity_ModuleCity_EntityCity $oCity) {
		if ($this->oMapper->UpdateCityPrefs($oCity)) {
			//чистим зависимые кеши
			//$this->Cache_Clean(Zend_Cache::CLEANING_MODE_MATCHING_TAG,array('city_update',"city_update_{$oCity->getId()}"));
			$this->Cache_Delete("city_{$oCity->getId()}");
			return true;
		}
		return false;
	}

	/*
    * Обновляет описание и заголовок корпоративного блога
    */
	public function UpdateCityBlog(PluginCity_ModuleCity_EntityCity $oCity) {
		$oBlog = $this->Blog_GetBlogById($oCity->getBlogId());
		$oBlog->setTitle($this->Lang_Get('plugin.city.city_blog_prefix').' '.$oCity->getName());
		$oBlog->setDescription($oCity->getDescription());
		$oBlog->setUrl('city_city_'.$oCity->GetUrl()); // Для того чтобы не терялся префикс при апдейте
		$oBlog->setAvatar($oCity->getLogoPath(100));
		return $this->Blog_UpdateBlog($oBlog);
	}
	/*
	 * Создаёт корпоративный блог
	 */
	public function CreateCityBlog(PluginCity_ModuleCity_EntityCity $oCity) {
		$oBlog = Engine::GetEntity('Blog');//BlogEntity_Blog();
		$oBlog->setOwnerId($oCity->getOwnerId());
		$oBlog->setTitle($this->Lang_Get('plugin.city.city_blog_prefix').' '.$oCity->getName());
		$oBlog->setType('city');
		$oBlog->setDescription($oCity->getDescription());
		$oBlog->setDateAdd(date("Y-m-d H:i:s"));
		$oBlog->setLimitRatingTopic(-100);
		$oBlog->setUrl('city_city_'.$oCity->GetUrl());	// city_ вырезается в getUrl() поэтому при создании пишем 2 раза..
		$oBlog->setAvatar(null);
		return $this->Blog_AddBlog($oBlog);
	}


	/*
	 * Увеличивает число отзывов о компании
	 */
	public function increaseCityCountFeedbacks($sCityId) {
		$this->Cache_Clean(Zend_Cache::CLEANING_MODE_MATCHING_TAG,array('city_update',"city_update_{$sCityId}"));
		$this->Cache_Delete("city_{$sCityId}");
		return $this->oMapper->increaseCityCountFeedbacks($sCityId);
	}

	/**
	 * Увеличивает число подписчиков на блог компании
	 *
	 * @param $sCityId
	 * @return mixed
	 */
	public function increaseCityCountSubscribe($sCityId) {
		$this->Cache_Clean(Zend_Cache::CLEANING_MODE_MATCHING_TAG,array('city_update',"city_update_{$sCityId}"));
		$this->Cache_Delete("city_{$sCityId}");
		return $this->oMapper->increaseCityCountSubscribe($sCityId);
	}

	/**
	 * Уменьшает число подписчиков на блог компании
	 *
	 * @param $sCityId
	 * @return mixed
	 */
	public function decreaseCityCountSubscribe($sCityId) {
		$this->Cache_Clean(Zend_Cache::CLEANING_MODE_MATCHING_TAG,array('city_update',"city_update_{$sCityId}"));
		$this->Cache_Delete("city_{$sCityId}");
		return $this->oMapper->decreaseCityCountSubscribe($sCityId);
	}

	/*
	 * Получает дату прочтения отзывов компании юзером
	 */
	public function GetFeedbackRead($sCityId,$sUserId) {
		return $this->oMapper->GetFeedbackRead($sCityId,$sUserId);
	}

	/*
	 * Обновляем/устанавливаем дату прочтения отзыва, если читаем его первый раз то добавляем
	 */
	public function SetFeedbackRead(PluginCity_ModuleCity_EntityCityFeedbackRead $oFeedbackRead) {
		if ($this->GetFeedbackRead($oFeedbackRead->getCityId(),$oFeedbackRead->getUserId())) {
			$this->oMapper->UpdateFeedbackRead($oFeedbackRead);
		} else {
			$this->oMapper->AddFeedbackRead($oFeedbackRead);
		}
		return true;
	}


	/**
	 * Расчет рейтинга и силы при голосовании за компанию
	 *
	 * @param ModuleUser_EntityUser $oUser
	 * @param PluginCity_ModuleCity_EntityCity $oCity
	 * @param int $iValue
	 * @return int
	 */
	public function VoteCity(ModuleUser_EntityUser $oUser, PluginCity_ModuleCity_EntityCity $oCity, $iValue) {
		/**
		 * Устанавливаем рейтинг блога, используя логарифмическое распределение
		 */
		$skill=$oUser->getSkill();
		$iMinSize=1.13;
		$iMaxSize=15;
		$iSizeRange=$iMaxSize-$iMinSize;
		$iMinCount=log(0+1);
		$iMaxCount=log(500+1);
		$iCountRange=$iMaxCount-$iMinCount;
		if ($iCountRange==0) {
			$iCountRange=1;
		}
		if ($skill>50 and $skill<200) {
			$skill_new=$skill/20;
		} elseif ($skill>=200) {
			$skill_new=$skill/10;
		} else {
			$skill_new=$skill/50;
		}
		$iDelta=$iMinSize+(log($skill_new+1)-$iMinCount)*($iSizeRange/$iCountRange);
		/**
		 * Сохраняем рейтинг
		 */
		$oCity->setRating($oCity->getRating()+$iValue*$iDelta);
		return $iDelta;
	}

	/**
	 * Изменяет владельца компании
	 *
	 * @param ModuleBlog_EntityBlog $oBlog
	 * @param ModuleUser_EntityUser $oUser
	 * @return bool
	 */
	public function ReplaceCityOwner(ModuleBlog_EntityBlog $oBlog, ModuleUser_EntityUser $oUser) {
		$oBlog->setOwnerId($oUser->getId());
		$this->oMapper->UpdateCityBlogOwner($oBlog);
		$this->oMapper->UpdateCityOwner($oBlog);
		// Если пользователь был в уже в компании.
		$oBlogUser=$this->Blog_GetBlogUserByBlogIdAndUserId($oBlog->getId(),$oUser->getId());
		if ($oBlogUser){
			$this->Blog_DeleteRelationBlogUser($oBlogUser);
		}
		$oBlog->setUrl('city_city_'.$oBlog->getUrl());
		$this->Blog_UpdateBlog($oBlog);
		return true;
	}

	/**
	 * Проверяет является ли пользователь сотрудником компании
	 *
	 * @param $sUserId
	 * @param $oCity
	 * @return bool
	 */
	public function GetUserIsEmployee($sUserId,$oCity) {
		$aUserCityes = $this->GetCityesByUser($sUserId, true);
		if (isset($aUserCityes[$oCity->getId()])) {
			return true;
		}
		return false;
	}

	/**
	 * Получает неактивные блоги компаний
	 *
	 * @return array
	 */
	public function GetInaccessibleCityBlogs() {
		if ($this->oUserCurrent && $this->oUserCurrent->isAdministrator()) {
			return array();
		}
		return $aCloseBlogs = $this->oMapper->GetCityInactiveBlogs();
	}

	/**
	 * Получает активные блоги компаний
	 *
	 * @return array
	 */
	public function GetCityActiveBlogs() {
		if (false === ($data = $this->Cache_Get("city_active_blogs"))) {
			$data = $this->oMapper->GetCityActiveBlogs();
			$this->Cache_Set($data, "city_active_blogs", array('city_update','city_new'), 60*60*24);
		}
		return $data;
	}

	/**
	 * Проверяет активирова ли плагин
	 *
	 * @param $sPluginName
	 * @return bool
	 */
	public function isActivePlugin($sPluginName) {
		$aPluginList = array_keys($this->oEngine->GetPlugins());
		if (in_array(strtolower($sPluginName),$aPluginList)){
			return true;
		}
		return false;
	}

	/**
	 * Получает список пользователей из избранного
	 *
	 * @param  string $sTargetId
	 * @param  string $sTargetType
	 * @param  int $iCurrPage
	 * @param  int $iPerPage
	 * @return array
	 */
	public function GetFavouriteUsersByTargerId($sTargetId,$sTargetType,$iCurrPage,$iPerPage) {
		if (false === ($data = $this->Cache_Get("{$sTargetType}_favourite_target_{$sTargetId}_{$iCurrPage}_{$iPerPage}"))) {
			$data = $this->oMapper->GetFavouriteUsersByTargerId($sTargetId,$sTargetType,$iCurrPage,$iPerPage);
			$this->Cache_Set($data, "{$sTargetType}_favourite_target_{$sTargetId}_{$iCurrPage}_{$iPerPage}", array("favourite_{$sTargetType}_change"), 60*60*24);
		}
		return $data;
	}

	/**
	 * Получает избранные компании пользователя
	 *
	 * @param $iUserId
	 * @param $iCurrPage
	 * @param $iPerPage
	 * @return array
	 */
	public function GetFavouriteCityesByUserId($iUserId,$iCurrPage,$iPerPage) {
		$data = $this->Favourite_GetFavouritesByUserId($iUserId,'city',$iCurrPage,$iPerPage);
		return $this->GetCityesByFilter(array('city_id' => $data['collection']),array(),$iCurrPage,$iPerPage);
	}

	/**
	 * Отсылает уведомления о новом топике подписчикам
	 *
	 * @param $oTopic
	 */
	public function SendSubscribeNewTopic($oTopic){
		$oCity = $oCity=$this->GetCityByBlogId($oTopic->getBlogId());
		// Отсылаем только если компания активна
		if ($oCity) {
			// получаем дополнительные данные топика
			$oTopic=$this->Topic_GetTopicById($oTopic->getId());
			$aExcludeMail=array($this->oUserCurrent->getMail());
			$this->Subscribe_Send('city_new_topic', $oCity->getId(), 'notify.city_new_topic.tpl', $this->Lang_Get('plugin.city.notify_subject_topic_new').' '.$oCity->getName(), array(
				'oTopic' => $oTopic,
				'oCity' => $oCity,
			), $aExcludeMail,'PluginCity');
		}
	}

	public function GetJSON($url){
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$data=curl_exec($ch);
		curl_close($ch);
		return json_decode($data,true);
	}

	public function CheckAndSaveTwitterAccount(PluginCity_ModuleCity_EntityCity $oCity, $screenName) {
		$screenName = strip_tags($screenName);
		if ($this->PluginCity_Oauth_CheckTwitterName($screenName)) {
			$oCity->setTwitterScreenName($screenName);
			$this->UpdateCityPrefs($oCity);
			return true;
		}
		return false;
	}


	public function CheckAndSaveFBAccount(PluginCity_ModuleCity_EntityCity $oCity,$fbUrl) {
		$data = $this->GetJSON('http://graph.facebook.com/'.strip_tags($fbUrl));
		if (isset($data['link'])){
			$oCity->setFbUrl($data['link']);
			$this->UpdateCityPrefs($oCity);
			return true;
		}
		return false;
	}

	public function CheckAndSaveVkAccount(PluginCity_ModuleCity_EntityCity $oCity,$vkUrl) {
		$data = $this->GetJSON('http://api.vk.com/method/groups.getById?gid='.strip_tags($vkUrl));
		if (isset($data['response']['0']['screen_name'])){
			$response = $data['response']['0'];
			$oCity->setVkUrl('http://vk.com/'.$response['screen_name']);
			$oCity->setVkId($response['gid']);
			$this->UpdateCityPrefs($oCity);
			return true;
		}
		return false;
	}

}
?>
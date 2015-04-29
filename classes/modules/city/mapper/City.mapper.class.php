<?php
/**
 *	Module "City"
 *	Author: Grebenkin Anton
 *	Contact e-mail: 4freework@gmail.com
 */

class PluginCity_ModuleCity_MapperCity extends Mapper {
	protected $oUserCurrent=null;

	public function SetUserCurrent($oUserCurrent) {
		$this->oUserCurrent=$oUserCurrent;
	}

	/* КОМПАНИИ */

	public function AddCity(PluginCity_ModuleCity_EntityCity $oCity) {
		$sql = "INSERT INTO ".Config::Get('db.table.city')."
			(user_owner_id,
			city_url,
			city_name,
			city_description,
			city_tags,
			city_country,
			city_city,
			city_date_add,
			city_vacancies,
			city_prefs,
			city_type,
			blog_id
			)
			VALUES(?d,  ?,	?,	?,	?,	?, ?, ?, ?, ?, ?, ?d)
		";
		if ($iId=$this->oDb->query($sql,$oCity->getOwnerId(),$oCity->getUrl(),$oCity->getName(),$oCity->getDescription(),$oCity->getTags(),$oCity->getCountry(),$oCity->getCity(),$oCity->getDateAdd(),'','',$oCity->getType(),$oCity->getBlogId())) {
			return $iId;
		}
		return false;
	}

	public function UpdateCity(PluginCity_ModuleCity_EntityCity $oCity) {
		$sql = "UPDATE ".Config::Get('db.table.city')."
			SET
				city_name= ?,
				city_name_legal= ?,
				city_description= ?,
				city_site= ?,
				city_email= ?,
				city_phone= ?,
				city_fax= ?,
				city_contact_name= ?,
				city_date_basis= ?,
				city_date_edit= ?,
				city_tags= ?,
				city_country= ?,
				city_city= ?,
				city_address= ?,
				city_count_workers= ?,
				city_rating= ?f,
				city_count_vote = ?d,
				city_logo=	?d,
				city_logo_type=	?,
				city_vacancies=	?,
				city_active=	?d,
				city_latitude = ?,
				city_longitude = ?,
				city_count_favourite= ?d,
				city_file_name = ?,
				city_skype= ?,
				city_icq = ?,
				city_contact_info = ?,
				city_about = ?,
				city_about_source = ?,
				topic_id_last = ?d,
				city_prefs = ?,
				city_tariff_id = ?d,
				city_date_tariff_end = ?
			WHERE
				city_id = ?d
		";
		if ($this->oDb->query($sql,$oCity->getName(),$oCity->getLegalName(),$oCity->getDescription(),
			$oCity->getSite(),$oCity->getEmail(),$oCity->getPhone(),$oCity->getFax(),$oCity->getContactName(),$oCity->getDateBasis(),$oCity->getDateEdit(),
			$oCity->getTags(),$oCity->getCountry(),$oCity->getCity(),$oCity->getAddress(),$oCity->getCountWorkers(),$oCity->getRating(),
			$oCity->getCountVote(),$oCity->getLogo(),$oCity->getLogoType(),$oCity->getVacancies(),$oCity->getActive(),
			$oCity->getLatitude(),$oCity->getLongitude(),$oCity->getCountFavourite(),$oCity->getFileName(),$oCity->getSkype(),$oCity->getIcq(),
			$oCity->getContactInfo(),$oCity->getAbout(),$oCity->getAboutSource(),$oCity->getTopicIdLast(),$oCity->getPrefs(),$oCity->getTariffId(),$oCity->getDateTariffEnd(),
			$oCity->getId())) {
			return true;
		}
		return false;
	}

	public function UpdateCityPrefs(PluginCity_ModuleCity_EntityCity $oCity) {
		$sql = "UPDATE ".Config::Get('db.table.city')."
			SET
				city_prefs = ?
			WHERE
				city_id = ?d
		";
		if ($this->oDb->query($sql,$oCity->getPrefs(),
			$oCity->getId())) {
			return true;
		}
		return false;
	}


	public function GetCityByName($sName) {
		$sql = "SELECT city_id FROM ".Config::Get('db.table.city')." WHERE city_name = ? ";
		if ($aRow=$this->oDb->selectRow($sql,$sName)) {
			return $aRow['city_id'];
		}
		return null;
	}

	public function GetCityByBlogId($sId) {
		$sql = "SELECT city_id FROM ".Config::Get('db.table.city')."  WHERE blog_id = ?d ";
		if ($aRow=$this->oDb->selectRow($sql,$sId)) {
			return $aRow['city_id'];
		}
		return null;
	}

	/*
	 * Возвращает id компании по заданному алиасу
	 */
	public function GetCityByUrl($sUrl, $bOnlyActive = true) {
		$sql = "SELECT
				city_id
			FROM
				".Config::Get('db.table.city')."
			WHERE
				LOWER(city_url) = ?
				";
		if ($aRow=$this->oDb->selectRow($sql,$sUrl)) {
			return $aRow['city_id'];
		}
		return null;
	}

	public function GetCityTags($iLimit) {
		$sql = "SELECT
			tt.city_tag_text,
			count(tt.city_tag_text)	as count
			FROM
				".Config::Get('db.table.city_tag')." as tt,
				".Config::Get('db.table.city')." as t
			WHERE
				t.city_id=tt.city_id
			GROUP BY
				tt.city_tag_text
			ORDER BY
				count desc
			LIMIT 0, ?d
				";
		$aReturn=array();
		$aReturnSort=array();
		if ($aRows=$this->oDb->select($sql,$iLimit)) {
			foreach ($aRows as $aRow) {
				$aReturn[$aRow['city_tag_text']]=$aRow;
			}
			ksort($aReturn);
			foreach ($aReturn as $aRow) {
				$aReturnSort[]= Engine::GetEntity('PluginCity_City_CityTag', $aRow);//new PluginCity_ModuleCity_EntityCityTag($aRow);
			}
		}
		return $aReturnSort;
	}

	public function GetCityTagsByCityId($iCityId) {
		$sql = "SELECT
			city_tag_text
			FROM
				".Config::Get('db.table.city_tag')."
			WHERE
				city_id = ?d
				";
		$aReturn=array();
		if ($aRows=$this->oDb->select($sql,$iCityId)) {
			foreach ($aRows as $aRow) {
				$aReturn[]=$aRow['city_tag_text'];
			}

		}
		return $aReturn;
	}

	public function GetCityTagsByLike($sTag,$iLimit) {
		$sTag=mb_strtolower($sTag,"UTF-8");
		$sql = "SELECT
				city_tag_text
			FROM
				".Config::Get('db.table.city_tag')."
			WHERE
				city_tag_text LIKE ?
			GROUP BY
				city_tag_text
			LIMIT 0, ?d
				";
		$aReturn=array();
		if ($aRows=$this->oDb->select($sql,$sTag.'%',$iLimit)) {
			foreach ($aRows as $aRow) {
				$aReturn[]= Engine::GetEntity('PluginCity_City_CityTag', $aRow);//new PluginCity_ModuleCity_EntityCityTag($aRow);
			}
		}
		return $aReturn;
	}

	public function GetCityesByArrayId($aArrayId) {
		if (!is_array($aArrayId) or count($aArrayId)==0) {
			return array();
		}

		$sql = "SELECT
					*
				FROM
					".Config::Get('db.table.city')." as c
				WHERE
				city_id IN(?a)
				ORDER BY FIELD(city_id,?a) ";
		$aCityes=array();
		if ($aRows=$this->oDb->select($sql,$aArrayId,$aArrayId)) {
			foreach ($aRows as $oCity) {
				$aCityes[]=Engine::GetEntity('PluginCity_City_City', $oCity);
			}
		}
		return $aCityes;
	}

	public function GetCityesByTag($sTag,&$iCount,$iCurrPage,$iPerPage) {
		$sWhere = $this->MakeActivateFilter();
		$sql = "SELECT
			c.city_id
			FROM (
					SELECT
						city_id
					FROM
						".Config::Get('db.table.city_tag')."
					WHERE
						city_tag_text = ?
				) as ct
				JOIN ".Config::Get('db.table.city')." AS c ON ct.city_id = c.city_id
				".$sWhere."
				ORDER BY city_rating DESC
                LIMIT ?d, ?d";
		$aCityes=array();
		if ($aRows=$this->oDb->selectPage($iCount,$sql,$sTag,($iCurrPage-1)*$iPerPage, $iPerPage)) {
			foreach ($aRows as $aCity) {
				$aCityes[]= $aCity['city_id'];
			}
		}
		return $aCityes;
	}

	public function GetCityesByFilter($aFilter,$aOrder,&$iCount,$iCurrPage,$iPerPage) {
		$aOrderAllow=array('city_id','city_name','city_date_add','city_rating','city_count_feedback','city_count_favourite');
		$sOrder='';
		foreach ($aOrder as $key=>$value) {
			if (!in_array($key,$aOrderAllow)) {
				unset($aOrder[$key]);
			} elseif (in_array($value,array('asc','desc'))) {
				$sOrder.=" {$key} {$value},";
			}
		}
		$sOrder=trim($sOrder,',');
		if ($sOrder=='') {
			$sOrder=' city_rating desc ';
		}
		$sWhere = $this->buildFilter($aFilter);
		$sql = "SELECT
					city_id
				FROM
					".Config::Get('db.table.city')."
				WHERE
					".$sWhere."
				ORDER by {$sOrder}
				LIMIT ?d, ?d ;
					";
		$aResult=array();
		if ($aRows=$this->oDb->selectPage($iCount,$sql,
			($iCurrPage-1)*$iPerPage, $iPerPage
		)) {
			foreach ($aRows as $aRow) {
				$aResult[]=$aRow['city_id'];
			}
		}
		return $aResult;
	}
	public function GetCountCityesByFilter($aFilter) {
		$sWhere = $this->buildFilter($aFilter);
		$sql = "SELECT count(city_id) as count
				FROM
					".Config::Get('db.table.city')."
				WHERE ".$sWhere;
		if ($aRow=$this->oDb->selectRow($sql)) {
			return $aRow['count'];
		}
		return false;
	}

	public function GetCountCityesByTag($sTag) {
		$sql = "SELECT
					count(city_id) as count
				FROM
					".Config::Get('db.table.city_tag')."
				WHERE
					city_tag_text = ? ;
					";
		if ($aRow=$this->oDb->selectRow($sql,$sTag)) {
			return $aRow['count'];
		}
		return false;
	}
	protected function buildFilter($aFilter) {
		$sWhere = '1=1 ';
		if(isset($aFilter['active']))
			$sWhere .= ' AND city_active = '.$aFilter['active'];
		if(isset($aFilter['type']))
			$sWhere .= " AND city_type = '".$aFilter['type']."'";
		if(isset($aFilter['name_like']))
			$sWhere .= " AND LOWER(city_name) LIKE '%{$aFilter['name_like']}%'";
		if(isset($aFilter['blog_id']))
			$sWhere .= " AND blog_id IN ('".join("', '",$aFilter['blog_id'])."')";
		if(isset($aFilter['city_id']))
			$sWhere .= " AND city_id IN ('".join("', '",$aFilter['city_id'])."')";
		if(isset($aFilter['new_time']))
			$sWhere .= ' AND city_date_add >= "'.date("Y-m-d H:00:00",time()-$aFilter['new_time']).'"';
		if(isset($aFilter['city_type']))
			$sWhere .= " AND city_type IN ('".join("', '",$aFilter['city_type'])."')";
		return $sWhere;
	}

	/* СВЯЗЬ ПОЛЬЗОВАТЕЛЕЙ С КОМПАНИЕЙ */

	public function UpdateCityOwner(ModuleBlog_EntityBlog $oBlog) {
		$sql = "UPDATE ".Config::Get('db.table.city')."
			SET
				user_owner_id = ?d
			WHERE
				blog_id = ?d
		";
		if ($this->oDb->query($sql,$oBlog->getOwnerId(),$oBlog->getId())) {
			return true;
		}
		return false;
	}

	public function UpdateCityBlogOwner(ModuleBlog_EntityBlog $oBlog) {
		$sql = "UPDATE ".Config::Get('db.table.blog')."
			SET
				user_owner_id = ?d
			WHERE
				blog_id = ?d
		";
		if ($this->oDb->query($sql,$oBlog->getOwnerId(),$oBlog->getId())) {
			return true;
		}
		return false;
	}

	/* ОТЗЫВЫ */

	public function increaseCityCountFeedbacks($sCityId) {
		$sql = "UPDATE ".Config::Get('db.table.city')."
			SET
				city_count_feedback=city_count_feedback+1
			WHERE
				city_id = ?
		";
		if ($this->oDb->query($sql,$sCityId)) {
			return true;
		}
		return false;
	}

	public function UpdateFeedbackRead(PluginCity_ModuleCity_EntityCityFeedbackRead $oFeedbackRead) {
		$sql = "UPDATE ".Config::Get('db.table.city_feedback_read')."
			SET
				feedback_count_last = ? ,
				feedback_id_last = ? ,
				date_read = ?
			WHERE
				city_id = ?
				AND
				user_id = ?
		";
		return $this->oDb->query($sql,$oFeedbackRead->getFeedbackCountLast(),$oFeedbackRead->getFeedbackIdLast(),$oFeedbackRead->getDateRead(),$oFeedbackRead->getCityId(),$oFeedbackRead->getUserId());
	}

	public function AddFeedbackRead(PluginCity_ModuleCity_EntityCityFeedbackRead $oFeedbackRead) {
		$sql = "INSERT INTO ".Config::Get('db.table.city_feedback_read')."
			SET
				feedback_count_last = ? ,
				feedback_id_last = ? ,
				date_read = ? ,
				city_id = ? ,
				user_id = ?
		";
		return $this->oDb->query($sql,$oFeedbackRead->getFeedbackCountLast(),$oFeedbackRead->getFeedbackIdLast(),$oFeedbackRead->getDateRead(),$oFeedbackRead->getCityId(),$oFeedbackRead->getUserId());
	}

	public function GetFeedbackRead($sCityId,$sUserId) {
		$sql = "SELECT
					*
				FROM
					".Config::Get('db.table.city_feedback_read')."
				WHERE
					city_id = ?d
					AND
					user_id = ?d
				;
					";
		if ($aRow=$this->oDb->selectRow($sql,$sCityId,$sUserId)) {
			return Engine::GetEntity('PluginCity_City_CityFeedbackRead', $aRow);//new PluginCity_ModuleCity_EntityCityFeedbackRead($aRow);
		}
		return false;
	}

	/* ТЭГИ */

	public function AddCityTag(PluginCity_ModuleCity_EntityCityTag $oCityTag) {
		$sql = "INSERT INTO ".Config::Get('db.table.city_tag')."
			(city_id,
			user_id,
			city_tag_text
			)
			VALUES(?d,  ?d,	?)
		";
		if ($iId=$this->oDb->query($sql,$oCityTag->getCityId(),$oCityTag->getUserId(),$oCityTag->getText()))
		{
			return $iId;
		}
		return false;
	}

	public function DeleteCityTagsByCityId($sCityId) {
		$sql = "DELETE FROM ".Config::Get('db.table.city_tag')."
			WHERE
				city_id = ?d
		";
		if ($this->oDb->query($sql,$sCityId)) {
			return true;
		}
		return false;
	}

	public function DeleteCity($sCityId) {
		$sql = "DELETE FROM ".Config::Get('db.table.city')."
			WHERE
				city_id = ?d
		";
		if ($this->oDb->query($sql,$sCityId)) {
			return true;
		}
		return false;
	}

	public function MakeActivateFilter() {
		$iCurrentUserId=-1;
		if (is_object($this->oUserCurrent)) {
			$iCurrentUserId=$this->oUserCurrent->getId();
		}
		if (Config::Get('module.city.use_activate')) {
			if (!$this->oUserCurrent or ($this->oUserCurrent and !$this->oUserCurrent->isAdministrator())){
				return " AND (city_active = 1 OR user_owner_id = ".$iCurrentUserId.')';
			} //'#where' => array('c.city_active = ?d OR c.user_owner_id = ?' => array(1,$iCurrentUserId)); // Для ORM фильтр
		}
		return "";
	}

	public function GetCityInactiveBlogs() {
		$aReturn=array();
		if (Config::Get('module.city.use_activate')) {
			$sql = "SELECT blog_id
					FROM ".Config::Get('db.table.city')."
					WHERE city_active = 0;
				   ";

			if ($aRows=$this->oDb->select($sql)) {
				foreach ($aRows as $aRow) {
					$aReturn[]=$aRow['blog_id'];
				}
			}
		}
		return $aReturn;
	}

	public function GetCityActiveBlogs() {
		$sWhere = "1=1 ";
		if (Config::Get('module.city.use_activate')) {
			$sWhere .= " AND city_active = 1";
		}

		$sql = "SELECT blog_id
				FROM ".Config::Get('db.table.city')."
				WHERE ".$sWhere;
		$aReturn=array();
		if ($aRows=$this->oDb->select($sql)) {
			foreach ($aRows as $aRow) {
				$aReturn[]=$aRow['blog_id'];
			}
		}
		return $aReturn;
	}

	/*
	* Получает всех пользоватлей которым понравилась компания
	*/
	public function GetFavouriteUsersByTargerId($sTargetId,$sTargetType,$iCurrPage,$iPerPage) {
		$sql = "
			SELECT user_id
			FROM ".Config::Get('db.table.favourite')."
			WHERE
					target_id = ?
				AND
					target_publish = 1
				AND
					target_type = ?
            ORDER BY user_id DESC
            LIMIT ?d, ?d ";

		$aFavourites=array();
		if ($aRows=$this->oDb->selectPage($iCount,
			$sql,
			$sTargetId,
			$sTargetType,
			($iCurrPage-1)*$iPerPage,
			$iPerPage
		)) {
			foreach ($aRows as $aFavourite) {
				$aFavourites[]=$aFavourite['user_id'];
			}
		}
		return $aFavourites;
	}

	/*
		 * Получает компании которые понравились пользователю
		 */
	public function GetFavouriteCityesByUserId(&$iCount,$sUserId,$iCurrPage,$iPerPage) {
		$sWhere = "1=1 ".$this->MakeActivateFilter();
		$sql = "
			SELECT c.*
			FROM ".Config::Get('db.table.favourite')." as f,".Config::Get('db.table.city')." as c
			WHERE ".$sWhere."
				AND	f.user_id = ? AND f.target_type = 'city' AND f.target_id = c.city_id
            ORDER BY c.city_rating DESC
            LIMIT ?d, ?d ";
		$aReturn=array();
		if ($aRows=$this->oDb->selectPage($iCount,
			$sql,
			$sUserId,
			($iCurrPage-1)*$iPerPage,
			$iPerPage
		)) {
			foreach ($aRows as $aRow) {
				$aReturn[]= Engine::GetEntity('PluginCity_City_City', $aRow);
			}
		}
		return $aReturn;
	}

	public function increaseCityCountSubscribe($sCityId) {
		$sql = "UPDATE ".Config::Get('db.table.city')."
			SET
				city_count_subscribe = city_count_subscribe + 1
			WHERE
				city_id = ?d
		";
		if ($this->oDb->query($sql,$sCityId)) {
			return true;
		}
		return false;
	}

	public function decreaseCityCountSubscribe($sCityId) {
		$sql = "UPDATE ".Config::Get('db.table.city')."
			SET
				city_count_subscribe = city_count_subscribe - 1
			WHERE
				city_id = ?d
		";
		if ($this->oDb->query($sql,$sCityId)) {
			return true;
		}
		return false;
	}


}

?>
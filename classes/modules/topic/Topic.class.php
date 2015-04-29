<?php
class PluginCity_ModuleTopic extends PluginCity_Inherit_ModuleTopic {

	/*
 * Получает список топиков из компании
 */
	public function GetCorporativeTopics($iPage,$iPerPage) {
		$aFilter=array(
			/*		'blog_type' => array(
				'city',
			),*/
			'topic_publish' => 1,
		);

		if (Config::Get('module.city.use_activate')) {
			if (!$this->oUserCurrent or ($this->oUserCurrent and !$this->oUserCurrent->isAdministrator())){
				$aActiveCityBlogs = $this->PluginCity_City_GetCityActiveBlogs();
				if(count($aActiveCityBlogs))
					$aFilter['blog_type']['city'] = $aActiveCityBlogs;
				else
					return array('collection'=>array(),'count'=>0);
			}else{
				$aFilter['blog_type'][] = 'city';
			}
		} else {
			$aFilter['blog_type'][] = 'city';
		}
		return $this->Topic_GetTopicsByFilter($aFilter,$iPage,$iPerPage);
	}

	/*
	 * Получает число новых топиков в корпоративных блогах
	 */
	public function GetCountTopicsCorporativeNew() {
		$sDate=date("Y-m-d H:00:00",time()-Config::Get('module.topic.new_time'));
		$aFilter=array(
			/*'blog_type' => array(
				'city',
			),*/
			'topic_publish' => 1,
			'topic_new' => $sDate,
		);
		if (!Config::Get('module.city.use_activate') or ($this->oUserCurrent and $this->oUserCurrent->isAdministrator())) {
			$aFilter['blog_type'][] = 'city';
		} else {
			$aActiveCityBlogs = $this->PluginCity_City_GetCityActiveBlogs();
			if(count($aActiveCityBlogs))
				$aFilter['blog_type']['city'] = $aActiveCityBlogs;
			else
				return 0;
		}
		return $this->GetCountTopicsByFilter($aFilter);
	}


	public function GetTopicsLast($iCount) {		
		$aFilter=array(
			'blog_type' => array(
				'personal',
				'open'
			),
			'topic_publish' => 1,			
		);
		/**
		 * Если пользователь авторизирован, то добавляем в выдачу
		 * закрытые блоги в которых он состоит
		 */
		if ($this->oUserCurrent) {
			$aOpenBlogs = $this->Blog_GetAccessibleBlogsByUser($this->oUserCurrent);
			if(count($aOpenBlogs)) $aFilter['blog_type']['close'] = $aOpenBlogs;
		}
		if (!Config::Get('module.city.use_activate') or ($this->oUserCurrent and $this->oUserCurrent->isAdministrator())) {
			$aFilter['blog_type'][] = 'city';
		} else {
			$aActiveCityBlogs = $this->PluginCity_City_GetCityActiveBlogs();
			if(count($aActiveCityBlogs)) $aFilter['blog_type']['city'] = $aActiveCityBlogs;
		}
		$aReturn=$this->GetTopicsByFilter($aFilter,1,$iCount);
		if (isset($aReturn['collection'])) {
			return $aReturn['collection'];
		}
		return false;
	}
	
	public function GetTopicsGood($iPage,$iPerPage,$bAddAccessible=true) {
		$aFilter=array(
			'blog_type' => array(
				'personal',
				'open',
			),
			'topic_publish' => 1,
			'topic_rating'  => array(
				'value' => Config::Get('module.blog.index_good'),
				'type'  => 'top',
				'publish_index'  => 1,
			)
		);
		if (!Config::Get('module.city.use_activate') or ($this->oUserCurrent and $this->oUserCurrent->isAdministrator())) {
			$aFilter['blog_type'][] = 'city';
		} else {
			$aActiveCityBlogs = $this->PluginCity_City_GetCityActiveBlogs();
			if(count($aActiveCityBlogs)) $aFilter['blog_type']['city'] = $aActiveCityBlogs;
		}
		/**
		 * Если пользователь авторизирован, то добавляем в выдачу
		 * закрытые блоги в которых он состоит
		 */
		if($this->oUserCurrent && $bAddAccessible) {
			$aOpenBlogs = $this->Blog_GetAccessibleBlogsByUser($this->oUserCurrent);
			if(count($aOpenBlogs)) $aFilter['blog_type']['close'] = $aOpenBlogs;
		}
		
		return $this->GetTopicsByFilter($aFilter,$iPage,$iPerPage);
	}
	
	/**
	 * Список топиков из блога
	 *
	 * @param unknown_type $oBlog
	 * @param unknown_type $iPage
	 * @param unknown_type $iPerPage
	 * @param unknown_type $sShowType
	 * @return unknown
	 */
	public function GetTopicsByBlog($oBlog,$iPage,$iPerPage,$sShowType='good',$sPeriod=null) {
		if (is_numeric($sPeriod)) {
			// количество последних секунд
			$sPeriod=date("Y-m-d H:00:00",time()-$sPeriod);
		}
		$aFilter=array(
			'topic_publish' => 1,
			'blog_id' => $oBlog->getId(),
		);
		if ($sPeriod) {
			$aFilter['topic_date_more'] = $sPeriod;
		}
		switch ($sShowType) {
			case 'good':
				$aFilter['topic_rating']=array(
					'value' => Config::Get('module.blog.collective_good'),
					'type'  => 'top',
				);			
				break;	
			case 'bad':
				$aFilter['topic_rating']=array(
					'value' => Config::Get('module.blog.collective_good'),
					'type'  => 'down',
				);			
				break;
			case 'new':
				$aFilter['topic_new']=date("Y-m-d H:00:00",time()-Config::Get('module.topic.new_time'));
				break;
			case 'newall':
				// нет доп фильтра
				break;
			case 'discussed':
				$aFilter['order']=array('t.topic_count_comment desc','t.topic_id desc');
				break;
			case 'top':
				$aFilter['order']=array('t.topic_rating desc','t.topic_id desc');
				break;
			default:
				break;
		}
		if ($oBlog->getType() != 'city')
			$aFilter['blog_type'][] = $oBlog->getType();
		if (!Config::Get('module.city.use_activate') or ($this->oUserCurrent and $this->oUserCurrent->isAdministrator())) {
			$aFilter['blog_type'][] = 'city';
		} else {
			$aActiveCityBlogs = $this->PluginCity_City_GetCityActiveBlogs();
			if(count($aActiveCityBlogs)) $aFilter['blog_type']['city'] = $aActiveCityBlogs;
		}
		/**
		 * Если пользователь авторизирован, то добавляем в выдачу
		 * закрытые блоги в которых он состоит
		 */
		if($this->oUserCurrent) {
			$aOpenBlogs = $this->Blog_GetAccessibleBlogsByUser($this->oUserCurrent);
			if(count($aOpenBlogs)) $aFilter['blog_type']['close'] = $aOpenBlogs;
		}
		return $this->GetTopicsByFilter($aFilter,$iPage,$iPerPage);
	}
	
	public function GetTopicsNew($iPage,$iPerPage,$bAddAccessible=true) {
		$sDate=date("Y-m-d H:00:00",time()-Config::Get('module.topic.new_time'));
		$aFilter=array(
			'blog_type' => array(
				'personal',
				'open',
			),
			'topic_publish' => 1,
			'topic_new' => $sDate,
		);
		if (!Config::Get('module.city.use_activate') or ($this->oUserCurrent and $this->oUserCurrent->isAdministrator())) {
			$aFilter['blog_type'][] = 'city';
		} else {
			$aActiveCityBlogs = $this->PluginCity_City_GetCityActiveBlogs();
			if(count($aActiveCityBlogs)) $aFilter['blog_type']['city'] = $aActiveCityBlogs;
		}
		/**
		 * Если пользователь авторизирован, то добавляем в выдачу
		 * закрытые блоги в которых он состоит
		 */
		if($this->oUserCurrent && $bAddAccessible) {
			$aOpenBlogs = $this->Blog_GetAccessibleBlogsByUser($this->oUserCurrent);
			if(count($aOpenBlogs)) $aFilter['blog_type']['close'] = $aOpenBlogs;
		}			
		return $this->GetTopicsByFilter($aFilter,$iPage,$iPerPage);
	}

	/**
	 * Получает список топиков по юзеру
	 *
	 * @param unknown_type $sUserId
	 * @param unknown_type $iPublish
	 * @param unknown_type $iPage
	 * @param unknown_type $iPerPage
	 * @return unknown
	 */
	public function GetTopicsPersonalByUser($sUserId,$iPublish,$iPage,$iPerPage) {
		$aFilter=array(
			'topic_publish' => $iPublish,
			'user_id' => $sUserId,
			'blog_type' => array('open','personal'),
		);
		/**
		 * Если пользователь смотрит свой профиль, то добавляем в выдачу
		 * закрытые блоги в которых он состоит
		 */
		if($this->oUserCurrent && $this->oUserCurrent->getId()==$sUserId) {
			$aFilter['blog_type'][]='close';
		}
		if (!Config::Get('module.city.use_activate') or ($this->oUserCurrent and $this->oUserCurrent->isAdministrator())) {
			$aFilter['blog_type'][] = 'city';
		} else {
			$aActiveCityBlogs = $this->PluginCity_City_GetCityActiveBlogs();
			if(count($aActiveCityBlogs)) $aFilter['blog_type']['city'] = $aActiveCityBlogs;
		}
		return $this->GetTopicsByFilter($aFilter,$iPage,$iPerPage);
	}

	/**
	 * Возвращает количество топиков которые создал юзер
	 *
	 * @param unknown_type $sUserId
	 * @param unknown_type $iPublish
	 * @return unknown
	 */
	public function GetCountTopicsPersonalByUser($sUserId,$iPublish) {
		$aFilter=array(
			'topic_publish' => $iPublish,
			'user_id' => $sUserId,
			'blog_type' => array('open','personal'),
		);
		/**
		 * Если пользователь смотрит свой профиль, то добавляем в выдачу
		 * закрытые блоги в которых он состоит
		 */
		if($this->oUserCurrent && $this->oUserCurrent->getId()==$sUserId) {
			$aFilter['blog_type'][]='close';
		}
		if (!Config::Get('module.city.use_activate') or ($this->oUserCurrent and $this->oUserCurrent->isAdministrator())) {
			$aFilter['blog_type'][] = 'city';
		} else {
			$aActiveCityBlogs = $this->PluginCity_City_GetCityActiveBlogs();
			if(count($aActiveCityBlogs)) $aFilter['blog_type']['city'] = $aActiveCityBlogs;
		}
		$s=serialize($aFilter);
		if (false === ($data = $this->Cache_Get("topic_count_user_{$s}"))) {
			$data = $this->oMapperTopic->GetCountTopics($aFilter);
			$this->Cache_Set($data, "topic_count_user_{$s}", array("topic_update_user_{$sUserId}"), 60*60*24);
		}
		return 	$data;
	}

	/**
	 * Получает число новых топиков в корпоративных блогах
	 *
	 * @return unknown
	 */
	public function GetCountTopicsNewByCity($oCity) {
		$sDate=date("Y-m-d H:00:00",time()-Config::Get('module.topic.new_time'));
		$aFilter=array(
			'blog_id' => $oCity->getBlogId(),
			'topic_publish' => 1,
			'topic_new' => $sDate,
		);
		if (!Config::Get('module.city.use_activate') or ($this->oUserCurrent and $this->oUserCurrent->isAdministrator())) {
			$aFilter['blog_type'][] = 'city';
		} else {
			$aActiveCityBlogs = $this->PluginCity_City_GetCityActiveBlogs();
			if(count($aActiveCityBlogs))
				$aFilter['blog_type']['city'] = $aActiveCityBlogs;
			else
				return 0;
		}
		return $this->GetCountTopicsByFilter($aFilter);
	}

	public function GetTopicsNewAll($iPage,$iPerPage,$bAddAccessible=true) {
		$aFilter=array(
			'blog_type' => array(
				'personal',
				'open',
				'city'
			),
			'topic_publish' => 1,
		);
		/**
		 * Если пользователь авторизирован, то добавляем в выдачу
		 * закрытые блоги в которых он состоит
		 */
		if($this->oUserCurrent && $bAddAccessible) {
			$aOpenBlogs = $this->Blog_GetAccessibleBlogsByUser($this->oUserCurrent);
			if(count($aOpenBlogs)) $aFilter['blog_type']['close'] = $aOpenBlogs;
		}
		return $this->GetTopicsByFilter($aFilter,$iPage,$iPerPage);
	}

	/**
	 * Получает список ВСЕХ обсуждаемых топиков
	 *
	 * @param  int    $iPage	Номер страницы
	 * @param  int    $iPerPage	Количество элементов на страницу
	 * @param  int|string   $sPeriod	Период в виде секунд или конкретной даты
	 * @param  bool   $bAddAccessible Указывает на необходимость добавить в выдачу топики,
	 *                                из блогов доступных пользователю. При указании false,
	 *                                в выдачу будут переданы только топики из общедоступных блогов.
	 * @return array
	 */
	public function GetTopicsDiscussed($iPage,$iPerPage,$sPeriod=null,$bAddAccessible=true) {
		if (is_numeric($sPeriod)) {
			// количество последних секунд
			$sPeriod=date("Y-m-d H:00:00",time()-$sPeriod);
		}

		$aFilter=array(
			'blog_type' => array(
				'personal',
				'open',
				'city'
			),
			'topic_publish' => 1
		);
		if ($sPeriod) {
			$aFilter['topic_date_more'] = $sPeriod;
		}
		$aFilter['order']=' t.topic_count_comment desc, t.topic_id desc ';
		/**
		 * Если пользователь авторизирован, то добавляем в выдачу
		 * закрытые блоги в которых он состоит
		 */
		if($this->oUserCurrent && $bAddAccessible) {
			$aOpenBlogs = $this->Blog_GetAccessibleBlogsByUser($this->oUserCurrent);
			if(count($aOpenBlogs)) $aFilter['blog_type']['close'] = $aOpenBlogs;
		}
		return $this->GetTopicsByFilter($aFilter,$iPage,$iPerPage);
	}

	/**
	 * Получает список ВСЕХ рейтинговых топиков
	 *
	 * @param  int    $iPage	Номер страницы
	 * @param  int    $iPerPage	Количество элементов на страницу
	 * @param  int|string   $sPeriod	Период в виде секунд или конкретной даты
	 * @param  bool   $bAddAccessible Указывает на необходимость добавить в выдачу топики,
	 *                                из блогов доступных пользователю. При указании false,
	 *                                в выдачу будут переданы только топики из общедоступных блогов.
	 * @return array
	 */
	public function GetTopicsTop($iPage,$iPerPage,$sPeriod=null,$bAddAccessible=true) {
		if (is_numeric($sPeriod)) {
			// количество последних секунд
			$sPeriod=date("Y-m-d H:00:00",time()-$sPeriod);
		}

		$aFilter=array(
			'blog_type' => array(
				'personal',
				'open',
				'city'
			),
			'topic_publish' => 1
		);
		if ($sPeriod) {
			$aFilter['topic_date_more'] = $sPeriod;
		}
		$aFilter['order']=array('t.topic_rating desc','t.topic_id desc');
		/**
		 * Если пользователь авторизирован, то добавляем в выдачу
		 * закрытые блоги в которых он состоит
		 */
		if($this->oUserCurrent && $bAddAccessible) {
			$aOpenBlogs = $this->Blog_GetAccessibleBlogsByUser($this->oUserCurrent);
			if(count($aOpenBlogs)) $aFilter['blog_type']['close'] = $aOpenBlogs;
		}
		return $this->GetTopicsByFilter($aFilter,$iPage,$iPerPage);
	}


	/**
	 * Список топиков из коллективных блогов
	 *
	 * @param int $iPage	Номер страницы
	 * @param int $iPerPage	Количество элементов на страницу
	 * @param string $sShowType	Тип выборки топиков
	 * @param string $sPeriod	Период в виде секунд или конкретной даты
	 * @return array
	 */
	public function GetTopicsCollective($iPage,$iPerPage,$sShowType='good',$sPeriod=null) {
		if (is_numeric($sPeriod)) {
			// количество последних секунд
			$sPeriod=date("Y-m-d H:00:00",time()-$sPeriod);
		}
		$aFilter=array(
			'blog_type' => array(
				'open', 'city'
			),
			'topic_publish' => 1,
		);
		if ($sPeriod) {
			$aFilter['topic_date_more'] = $sPeriod;
		}
		switch ($sShowType) {
			case 'good':
				$aFilter['topic_rating']=array(
					'value' => Config::Get('module.blog.collective_good'),
					'type'  => 'top',
				);
				break;
			case 'bad':
				$aFilter['topic_rating']=array(
					'value' => Config::Get('module.blog.collective_good'),
					'type'  => 'down',
				);
				break;
			case 'new':
				$aFilter['topic_new']=date("Y-m-d H:00:00",time()-Config::Get('module.topic.new_time'));
				break;
			case 'newall':
				// нет доп фильтра
				break;
			case 'discussed':
				$aFilter['order']=array('t.topic_count_comment desc','t.topic_id desc');
				break;
			case 'top':
				$aFilter['order']=array('t.topic_rating desc','t.topic_id desc');
				break;
			default:
				break;
		}
		/**
		 * Если пользователь авторизирован, то добавляем в выдачу
		 * закрытые блоги в которых он состоит
		 */
		if($this->oUserCurrent) {
			$aOpenBlogs = $this->Blog_GetAccessibleBlogsByUser($this->oUserCurrent);
			if(count($aOpenBlogs)) $aFilter['blog_type']['close'] = $aOpenBlogs;
		}
		return $this->GetTopicsByFilter($aFilter,$iPage,$iPerPage);
	}

	public function GetLastBlogTopic($iBlogId) {
		// порядок можно не задавать сортирует по умолчанию по дате добаления
		$aFilter=array(
			'blog_type' => array(
				'city'
			),
			'topic_publish' => 1,
			'blog_id' => $iBlogId
		);

		$aReturn=$this->GetTopicsByFilter($aFilter,1,1);
		if (isset($aReturn['collection'])) {
			return array_shift($aReturn['collection']);
		}
		return false;
	}
}

?>
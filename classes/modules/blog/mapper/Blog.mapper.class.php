<?php
class PluginCity_ModuleBlog_MapperBlog extends PluginCity_Inherit_ModuleBlog_MapperBlog {

	public function GetBlogsRatingJoin($sUserId,$iLimit) {		
		$sql = "SELECT 
					b.*													
				FROM 
					".Config::Get('db.table.blog_user')." as bu,
					".Config::Get('db.table.blog')." as b	
				WHERE 	
					bu.user_id = ?d
					AND
					bu.blog_id = b.blog_id
					AND				
					b.blog_type NOT IN ('personal', 'city')
				ORDER by b.blog_rating desc
				LIMIT 0, ?d 
				;	
					";		
		$aReturn=array();
		if ($aRows=$this->oDb->select($sql,$sUserId,$iLimit)) {
			foreach ($aRows as $aRow) {
				$aReturn[]=Engine::GetEntity('Blog',$aRow);
			}
		}
		return $aReturn;
	}


	/**
	 * Получает список блогов по фильтру
	 *
	 * @param array $aFilter	Фильтр выборки
	 * @param array $aOrder		Сортировка
	 * @param int $iCount		Возвращает общее количество элментов
	 * @param int $iCurrPage	Номер текущей страницы
	 * @param int $iPerPage		Количество элементов на одну страницу
	 * @return array
	 */
	public function GetBlogsByFilter($aFilter,$aOrder,&$iCount,$iCurrPage,$iPerPage) {
		$aOrderAllow=array('blog_id','blog_title','blog_rating','blog_count_user','blog_count_topic');
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
			$sOrder=' blog_id desc ';
		}

		if (isset($aFilter['exclude_type']) and !is_array($aFilter['exclude_type'])) {
			$aFilter['exclude_type']= array($aFilter['exclude_type'],'city');//array($aFilter['exclude_type']);
		}

		if (isset($aFilter['type']) and !is_array($aFilter['type'])) {
			$aFilter['type']=array($aFilter['type']);
		}

		$sql = "SELECT
					blog_id
				FROM
					".Config::Get('db.table.blog')."
				WHERE
					1 = 1
					{ AND blog_id = ?d }
					{ AND user_owner_id = ?d }
					{ AND blog_type IN (?a) }
					{ AND blog_type not IN (?a) }
					{ AND blog_url = ? }
					{ AND blog_title LIKE ? }
				ORDER by {$sOrder}
				LIMIT ?d, ?d ;
					";
		$aResult=array();
		if ($aRows=$this->oDb->selectPage($iCount,$sql,
			isset($aFilter['id']) ? $aFilter['id'] : DBSIMPLE_SKIP,
			isset($aFilter['user_owner_id']) ? $aFilter['user_owner_id'] : DBSIMPLE_SKIP,
			(isset($aFilter['type']) and count($aFilter['type']) ) ? $aFilter['type'] : DBSIMPLE_SKIP,
			(isset($aFilter['exclude_type']) and count($aFilter['exclude_type']) ) ? $aFilter['exclude_type'] : DBSIMPLE_SKIP,
			isset($aFilter['url']) ? $aFilter['url'] : DBSIMPLE_SKIP,
			isset($aFilter['title']) ? $aFilter['title'] : DBSIMPLE_SKIP,
			($iCurrPage-1)*$iPerPage, $iPerPage
		)) {
			foreach ($aRows as $aRow) {
				$aResult[]=$aRow['blog_id'];
			}
		}
		return $aResult;
	}
}
?>
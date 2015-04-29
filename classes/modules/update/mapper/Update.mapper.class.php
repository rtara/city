<?php
/**
 *	Module "City"
 *	Author: Grebenkin Anton
 *	Contact e-mail: 4freework@gmail.com
 */

class PluginCity_ModuleUpdate_MapperUpdate extends Mapper {
	protected $oUserCurrent=null;

	public function SetUserCurrent($oUserCurrent) {
		$this->oUserCurrent=$oUserCurrent;
	}

	/**
	 * Конвертирует данные таблицы компаний
	 * @return unknown_type
	 */
	public function Convert03to04(){
		$sPrefix = Config::Get('db.table.prefix');
		$aErrors = array();
		/**
		 * Переводим в одну таблицу комментарии
		 */
		$sCommentIdMaxQuery = "SELECT MAX( comment_id ) AS max_id FROM {$sPrefix}comment";
		/**
		 * Получаем максимальный идентификатор комментариев к топикам
		 */
		if(!$aResults = mysql_query($sCommentIdMaxQuery) ){
			$aErrors[] = $this->Lang('error_table_select',array('table'=>'comments'));
		} else {
			$aRow=mysql_fetch_row($aResults);
			$iMaxId = $aRow[0]+1;

			$sTalkCommentSelect = "SELECT * FROM {$sPrefix}city_feedback";
			if(!$aResults = mysql_query($sTalkCommentSelect)){
				$aErrors[] = $this->Lang('error_table_select', array('table'=>'city_feedback'));
			} else {
				$iAutoIncrement = $iMaxId;
				while($aRow = mysql_fetch_array($aResults, MYSQL_ASSOC)) {
					$aRow['feedback_id']+=$iMaxId;
					/**
					 * Выбираем максимальный айдишник
					 */
					$iAutoIncrement = ($aRow['feedback_id']>$iAutoIncrement)
						? $aRow['feedback_id']
						: $iAutoIncrement;

					$aRow['feedback_pid']= is_int($aRow['feedback_pid']) ? $aRow['feedback_id']+$iMaxId : "NULL";
					$sQuery = "INSERT INTO `{$sPrefix}comment`
								SET
									comment_id = '{$aRow['feedback_id']}',
									comment_pid = {$aRow['feedback_pid']},
									target_id = '{$aRow['city_id']}',
									target_type = 'city',
									target_parent_id = '0',
									user_id = '{$aRow['user_id']}',
									comment_text = '".mysql_real_escape_string($aRow['feedback_text'])."',
									comment_text_hash = '".md5($aRow['feedback_text'])."',
									comment_date = '{$aRow['feedback_date']}',
									comment_user_ip = '{$aRow['feedback_user_ip']}',
									comment_rating = '".(($aRow['feedback_bad'] == 1)? -100: 0)."',
									comment_count_vote = '0',
									comment_delete = '{$aRow['feedback_delete']}',
									comment_publish = '1' ";
					if(!mysql_query($sQuery)) $aErrors[] = mysql_error();
				}
				$iAutoIncrement++;
				/**
				 * Устанавливаем в таблице новое значение авто инкремента
				 */
				@mysql_query("ALTER {$sPrefix}comment AUTO_INCREMENT={$iAutoIncrement}");
				mysql_free_result($aResults);
			}
		}
		/**
		 * Обновляем количество отзывов
		 */
		$sFeedbackSql = "SELECT feedback_id FROM {$sPrefix}city_feedback";
		if($aResults = mysql_query($sFeedbackSql)){
			while($aRow = mysql_fetch_assoc($aResults)) {
				$sFeedbackCountSql = "SELECT count(comment_id) as c FROM {$sPrefix}comment WHERE `target_id`={$aRow['feedback_id']} AND `target_type`='city'";
				if($aResultsCount = mysql_query($sFeedbackCountSql) and $aRowCount = mysql_fetch_assoc($aResultsCount)){
					mysql_query("UPDATE {$sPrefix}city SET city_count_feedback = {$aRowCount['c']} WHERE feedback_id = {$aRow['feedback_id']} ");
				}
			}
		}

		// Обновляем роли
		$sTable=$sPrefix.'city_user';
		mysql_query("UPDATE {$sTable} SET city_user_role = 0 WHERE city_user_role = 1 ");  //поклонник
		mysql_query("UPDATE {$sTable} SET city_user_role = 1 WHERE city_user_role = 2 ");  //сотрудник
		mysql_query("UPDATE {$sTable} SET city_user_role = 2 WHERE city_user_role = 99 "); //модератор
		mysql_query("UPDATE {$sTable} SET city_user_role = 4 WHERE city_user_role = 100 ");//администратор

		$sCityRoleSql = "SELECT c.city_id, c.blog_id, cu.city_user_role, cu.user_id FROM {$sPrefix}city_user as cu LEFT JOIN {$sPrefix}city as c on c.city_id=cu.city_id WHERE c.user_owner_id <> cu.user_id";
		if(!$aResults = mysql_query($sCityRoleSql)){
			$aErrors[] = $this->Lang('error_table_select', array('table'=>'city_user'));
		} else {
			while($aRow = mysql_fetch_array($aResults, MYSQL_ASSOC)) {
				$sQuery = "INSERT INTO `{$sPrefix}blog_user`
							SET
								blog_id = '{$aRow['blog_id']}',
								user_id = '{$aRow['user_id']}',
								user_role = '{$aRow['city_user_role']}'
							";
				if(!mysql_query($sQuery)) $aErrors[] = mysql_error();
			}
			mysql_free_result($aResults);
		}

		if(count($aErrors)==0) {
			return array('result'=>true,'errors'=>null);
		}
		return array('result'=>false,'errors'=>$aErrors);
	}

	public function RepairUrl(){
		$sql = "SELECT
					c.city_url, c.blog_id
				FROM
					".Config::Get('db.table.city')." as c
				WHERE

				(1=1)";
		if ($aRows=$this->oDb->select($sql)) {
			foreach ($aRows as $oRow) {
				$sQuery = "UPDATE ".Config::Get('db.table.blog')."
								SET
									blog_url = 'city_{$oRow['city_url']}'
								WHERE
									blog_id = {$oRow['blog_id']} and blog_type = 'city' ";
				$this->oDb->query($sQuery);
			}
		}
		return true;
	}
	/*
		 * Перевод старого голосования в новые таблицы
		 */
	public function ConvertVote(){
		$sPrefix = Config::Get('db.table.prefix');
		$sql = "SELECT * FROM {$sPrefix}city_vote WHERE 1=1";

		if ($aRows=$this->oDb->select($sql)) {
			foreach ($aRows as $oRow) {
				$sQuery = "INSERT INTO ".Config::Get('db.table.vote')."
                        SET
								target_id = '{$oRow['city_id']}',
								target_type = 'city',
								user_voter_id = '{$oRow['user_voter_id']}',
								vote_direction = '".(($oRow['vote_delta']>=0)?1:-1)."',
								vote_value = '{$oRow['vote_delta']}',
								vote_date = '".date("Y-m-d H:i:s")."'";
				$this->oDb->query($sQuery);
			}
		}
		return true;
	}

	public function GetCityesForConvertGeo() {
		$sQuery = "SELECT * FROM " . Config::Get('db.table.city') . " WHERE
					(`city_country`  IS NOT NULL and `city_country`<>'') or
					(`city_city`  IS NOT NULL and `city_city`<>'')";
		return $this->oDb->select($sQuery);
	}

	public function GetCountryForConvertGeo($aRow) {
		$iCityId = $aRow['city_id'];
		if (!$aRow['city_country']) {
			$sQuery2 = "UPDATE " . Config::Get('db.table.city') . " SET city_country=null, city_city=null WHERE city_id= ? ";
			$this->oDb->query($sQuery2, $iCityId);
		}
		$sCountry = mysql_real_escape_string($aRow['city_country']);
		// Обновляем страну
		$sQuery2="SELECT id, name_ru FROM ".Config::Get('db.table.geo_country')." WHERE name_ru='{$sCountry}' or name_en='{$sCountry}' LIMIT 0,1";
		$aResults2 = $this->oDb->select($sQuery2, $iCityId);
		if($aRow2 = current($aResults2)) {
			return $aRow2['id'];
		}else {
			$sQuery2 = "UPDATE " . Config::Get('db.table.city') . " SET city_country=null, city_city=null WHERE city_id= ? ";
			$this->oDb->query($sQuery2, $iCityId);
		}
		return false;
	}

	public function GetCityForConvertGeo($aRow, $iCountryId) {
		$sCity = mysql_real_escape_string((string)$aRow['city_city']);

		// Обновляем город
		if ($sCity) {
			$sQuery2="SELECT id, name_ru FROM ".Config::Get('db.table.geo_city')." WHERE country_id='{$iCountryId}' and (name_ru='{$sCity}' or name_en='{$sCity}') LIMIT 0,1";
			$aResults2 = $this->oDb->select($sQuery2);
			if($aRow2 = current($aResults2)) {
				return $aRow2['id'];
			}
		}
		return false;
	}

}

?>
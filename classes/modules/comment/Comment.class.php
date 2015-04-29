<?php

/**
 * Модуль для работы с комментариями
 *
 */

class PluginCity_ModuleComment extends PluginCity_Inherit_ModuleComment {

	public function GetTemplateCommentByTarget($iTargetId,$sTargetType) {
		if ($sTargetType == 'city'){
			return Plugin::GetTemplatePath('city').'feedback.tpl';
		} else {
			return "comment.tpl";
		}
	}
	/**
	 * Получает дополнительные данные(объекты) для комментов по их ID
	 *
	 */
	public function GetCommentsAdditionalData($aCommentId,$aAllowData=null) {
		if (is_null($aAllowData)) {
			$aAllowData=array('vote','target','favourite','user'=>array());
		}
		func_array_simpleflip($aAllowData);
		if (!is_array($aCommentId)) {
			$aCommentId=array($aCommentId);
		}
		/**
		 * Получаем комменты
		 */
		$aComments=$this->GetCommentsByArrayId($aCommentId);
		/**
		 * Формируем ID дополнительных данных, которые нужно получить
		 */
		$aUserId=array();	
		$aTargetId=array('topic'=>array(),'talk'=>array(),'city'=>array());
		foreach ($aComments as $oComment) {
			if (isset($aAllowData['user'])) {
				$aUserId[]=$oComment->getUserId();
			}
			if (isset($aAllowData['target'])) {
				$aTargetId[$oComment->getTargetType()][]=$oComment->getTargetId();
			}			
		}
		
		/**
		 * Получаем дополнительные данные
		 */
		$aUsers=isset($aAllowData['user']) && is_array($aAllowData['user']) ? $this->User_GetUsersAdditionalData($aUserId,$aAllowData['user']) : $this->User_GetUsersAdditionalData($aUserId);
		/**
		 * В зависимости от типа target_type достаем данные
		 */
		$aTargets=array();
		//$aTargets['topic']=isset($aAllowData['target']) && is_array($aAllowData['target']) ? $this->Topic_GetTopicsAdditionalData($aTargetId['topic'],$aAllowData['target']) : $this->Topic_GetTopicsAdditionalData($aTargetId['topic']);
		$aTargets['topic']=$this->Topic_GetTopicsAdditionalData($aTargetId['topic'],array('blog'=>array('owner'=>array())));
		$aTargets['city']=$this->PluginCity_City_GetCityesAdditionalData($aTargetId['city'],array('blog'=>array('owner'=>array(),'relation_user')));
		$aVote=array();
		if (isset($aAllowData['vote']) and $this->oUserCurrent) {
			$aVote=$this->Vote_GetVoteByArray($aCommentId,'comment',$this->oUserCurrent->getId());			
		}
		if (isset($aAllowData['favourite']) and $this->oUserCurrent) {
			$aFavouriteComments=$this->Favourite_GetFavouritesByArray($aCommentId,'comment',$this->oUserCurrent->getId());	
		}
		/**
		 * Добавляем данные к результату
		 */
		foreach ($aComments as $oComment) {
			if (isset($aUsers[$oComment->getUserId()])) {
				$oComment->setUser($aUsers[$oComment->getUserId()]);
			} else {
				$oComment->setUser(null); // или $oComment->setUser(new UserEntity_User());
			} 
			if (isset($aTargets[$oComment->getTargetType()][$oComment->getTargetId()])) {
				$oComment->setTarget($aTargets[$oComment->getTargetType()][$oComment->getTargetId()]); 
			} else { 
				$oComment->setTarget(null); 
			}
			if (isset($aVote[$oComment->getId()])) {
				$oComment->setVote($aVote[$oComment->getId()]);				
			} else {
				$oComment->setVote(null);
			}
			if (isset($aFavouriteComments[$oComment->getId()])) {
				$oComment->setIsFavourite(true);
			} else {
				$oComment->setIsFavourite(false);
			}						
		}
		return $aComments;
	}

}
?>
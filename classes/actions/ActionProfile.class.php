<?php

class PluginCity_ActionProfile extends PluginCity_Inherit_ActionProfile {

	protected function RegisterEvent() {
		parent::RegisterEvent();
		$this->AddEventPreg('/^.+$/i','/^favourites$/i','/^city/i','/^(page([1-9]\d{0,5}))?$/i','EventFavouriteCity');
		$this->AddEventPreg('/^.+$/i','/^created/i','/^feedbacks/i','/^(page([1-9]\d{0,5}))?$/i','EventFeedbacks');
	}

	protected function EventFeedbacks() {
		if (!$this->CheckUserProfile()) {
			return parent::EventNotFound();
		}
		$this->sMenuSubItemSelect = 'feedbacks';
		/**
		 * Передан ли номер страницы
		 */
		$iPage=$this->GetParamEventMatch(2,2) ? $this->GetParamEventMatch(2,2) : 1;
		/**
		 * Получаем список комментов
		 */
		$aResult=$this->Comment_GetCommentsByUserId($this->oUserProfile->getId(),'city',$iPage,Config::Get('module.comment.per_page'));
		$aComments=$aResult['collection'];
		$aPaging=$this->Viewer_MakePaging($aResult['count'],$iPage,Config::Get('module.comment.per_page'),Config::Get('pagination.pages.count'),$this->oUserProfile->getUserWebPath().'created/feedbacks');

		$iCountFeedbackUser=$this->Comment_GetCountCommentsByUserId($this->oUserProfile->getId(),'city');
		$this->Viewer_Assign('iCountFeedbackUser',$iCountFeedbackUser);
		/**
		 * Загружаем переменные в шаблон
		 */
		$this->Viewer_Assign('aPaging',$aPaging);
		$this->Viewer_Assign('aComments',$aComments);
		$this->Viewer_AddHtmlTitle($this->Lang_Get('user_menu_publication').' '.$this->oUserProfile->getLogin());
		$this->Viewer_AddHtmlTitle($this->Lang_Get('user_menu_publication_comment'));
		/**
		 * Устанавливаем шаблон вывода
		 */
		$this->SetTemplateAction('created_feedbacks');

	}

	protected function EventFavouriteCity(){
		if (!$this->CheckUserProfile()) {
			return parent::EventNotFound();
		}
		$this->sMenuHeadItemSelect = 'city';
		$this->sMenuSubItemSelect = 'city';
		/**
		 * Передан ли номер страницы
		 */
		$iPage=$this->GetParamEventMatch(2,2) ? $this->GetParamEventMatch(2,2) : 1;
		/**
		 * Получаем список избранных топиков
		 */
		$aResult=$this->PluginCity_City_GetFavouriteCityesByUserId($this->oUserProfile->getId(),$iPage,Config::Get('module.city.per_page'));
		$aCity=$aResult['collection'];

		/**
		 * Формируем постраничность
		 */
		$aPaging=$this->Viewer_MakePaging($aResult['count'],$iPage, Config::Get('module.city.per_page'),Config::Get('pagination.pages.count'),$this->oUserProfile->getUserWebPath().'favourites/city');
		/**
		 * Загружаем переменные в шаблон
		 */
		$this->Viewer_Assign('aPaging',$aPaging);
		$this->Viewer_Assign('aCity',$aCity);
		$this->Viewer_AddHtmlTitle($this->Lang_Get('user_menu_profile').' '.$this->oUserProfile->getLogin());
		$this->Viewer_AddHtmlTitle($this->Lang_Get('user_menu_profile_favourites'));
		/**
		 * Устанавливаем шаблон вывода
		 */
		$this->SetTemplateAction('favorites_city');
	}



	public function EventShutdown() {
		parent::EventShutdown();
		if (!$this->oUserProfile){
			return ;
		}

		$iCountTopicUser=$this->Topic_GetCountTopicsPersonalByUser($this->oUserProfile->getId(),1);
		$iCountCommentUser=$this->Comment_GetCountCommentsByUserId($this->oUserProfile->getId(),'topic');
		$iCountNoteUser=$this->User_GetCountUserNotesByUserId($this->oUserProfile->getId());
		$iCountFeedbackUser=$this->Comment_GetCountCommentsByUserId($this->oUserProfile->getId(),'city');
		$iCountTopicFavourite=$this->Topic_GetCountTopicsFavouriteByUserId($this->oUserProfile->getId());
		$iCountCommentFavourite=$this->Comment_GetCountCommentsFavouriteByUserId($this->oUserProfile->getId());
		$iCountCityFavourite=$this->PluginCity_City_GetCountFavCityesByUser($this->oUserProfile->getId());
		$this->Viewer_Assign('iCountCityFavourite',$iCountCityFavourite);
		$this->Viewer_Assign('iCountFeedbackUser',$iCountFeedbackUser);
		$this->Viewer_Assign('iCountCreated',(($this->oUserCurrent and $this->oUserCurrent->getId()==$this->oUserProfile->getId()) ? $iCountNoteUser : 0) +$iCountTopicUser+$iCountCommentUser+$iCountFeedbackUser);
		$this->Viewer_Assign('iCountFavourite',$iCountCommentFavourite+$iCountTopicFavourite+$iCountCityFavourite);

	}
}
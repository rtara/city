<?php
/**
 * ACL(Access Control List)
 * Модуль для разруливания ограничений по карме/рейтингу юзера
 *
 */
class PluginCity_ModuleACL extends PluginCity_Inherit_ModuleACL {

	/*
	 * Проверяет может ли пользователь создавать компании
	 */
	public function CanCreateCity(ModuleUser_EntityUser $oUser) {
		if ($oUser->getRating()>=Config::Get('acl.create.city.rating') or $oUser->isAdministrator()) {
			return true;
		}
		return false;
	}

	/*
     * Проверяет может ли пользователь редактировать компании
    */
	public function CanEditCity(ModuleUser_EntityUser $oUser, PluginCity_ModuleCity_EntityCity $oCity) {
		if ($oCity->getOwnerId()==$oUser->getId() or $oUser->isAdministrator() or $oCity->getUserIsAdministrator()) {
			return true;
		}
		return false;
	}
	/*
	 * Проверяет может ли пользователь голосовать за конкретный блог
	 */
	public function CanVoteCity(ModuleUser_EntityUser $oUser, PluginCity_ModuleCity_EntityCity $oCity) {
		if ($oUser->getRating()>=Config::Get('acl.vote.city.rating') or $oUser->isAdministrator()) {
			return true;
		}
		return false;
	}

	/*
	 * Проверяет может ли пользователь писать отзывы
	 */
	public function CanPostFeedback(ModuleUser_EntityUser $oUser) {
		if ($oUser->getRating()>=Config::Get('acl.create.feedback.rating') or $oUser->isAdministrator()) {
			return true;
		}
		return false;
	}

	/*
	 * Проверяет можно ли просматривать компанию
	 */
	public function CanViewCity($oUser, PluginCity_ModuleCity_EntityCity $oCity) {
		if (Config::Get('module.city.use_activate') and !$oCity->getActive()) {
			if (!$oUser or ($oCity->getOwnerId() != $oUser->getId() and !$oUser->isAdministrator()))
				return false;
		}
		return true;
	}
}
?>
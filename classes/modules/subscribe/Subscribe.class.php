<?php

/**
 * Модуль для работы с подписками
 *
 */

class PluginCity_ModuleSubscribe extends PluginCity_Inherit_ModuleSubscribe {
	/**
	 * Проверка объекта подписки с типом "city_new_topic"
	 *
	 * @param int $iTargetId	ID объявления
	 * @param int $iStatus	Статус
	 * @return bool
	 */
	public function CheckTargetCityNewTopic($iTargetId,$iStatus) {
		if ($oAd=$this->PluginCity_City_GetCityById($iTargetId)) {
			return true;
		}
		return false;
	}

	/**
	 * Проверка объекта подписки с типом "city_new_feedback"
	 *
	 * @param int $iTargetId	ID объявления
	 * @param int $iStatus	Статус
	 * @return bool
	 */
	public function CheckTargetCityNewFeedback($iTargetId,$iStatus) {
		if ($oAd=$this->PluginCity_City_GetCityById($iTargetId)) {
			return true;
		}
		return false;
	}
}
?>
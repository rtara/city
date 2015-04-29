<?php
/**
 *	Module "City"
 *	Author: Grebenkin Anton
 *	Contact e-mail: 4freework@gmail.com
 */

/**
 * Обработка блока компании в городе
 *
 */
class PluginCity_BlockCityesInCity extends Block {

	public function Exec(){
		$oCity = $this->GetParam(0);
		if ($oCity->getGeoTarget()) {
			$aResult = $this->Geo_GetCityTargets(array('city_id' => $oCity->getGeoTarget()->getCityId(), 'target_type' => 'city'), 1, Config::Get('module.city.on_block'));
			$aCityId = array();
			foreach ($aResult['collection'] as $oTarget) {
				$aCityId[] = $oTarget->getTargetId();
			}
			$aCity = $this->PluginCity_City_GetCityesByFilter(array('city_id'=>$aCityId),array(),1,Config::Get('module.city.on_block'),array('owner'=>array()));

			/**
			 * Загружаем переменные в шаблон
			 */
			$this->Viewer_Assign("aCity", $aCity['collection']);
			$this->Viewer_Assign("sCity", $oCity->getCity());
		}

	}
}
?>
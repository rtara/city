<?php
/**
 *	Module "City"
 *	Author: Grebenkin Anton
 *	Contact e-mail: 4freework@gmail.com
 */

/**
 * Обработка блока компаний
 *
 */
class PluginCity_BlockCityes extends Block {
    
    public function Exec() {
        /**
		 * Получаем список блогов
		 */
		$aResult = $this->PluginCity_City_GetCityesByFilter(array(),array(),1,Config::Get('module.city.on_block'));
		$aCity=$aResult['collection'];
		/**
		 * Загружаем переменные в шаблон
		 */
		$this->Viewer_Assign("aCity", $aCity);

    }
}
?>
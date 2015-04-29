<?php
/**
 *	Module "City"
 *	Author: Grebenkin Anton
 *	Contact e-mail: 4freework@gmail.com
 */
/**
 * Обработка блока компании
 *
 */
class PluginCity_BlockCityinfo extends Block {
    
    public function Exec() {
	    $aTariffs = $this->PluginCity_Pay_GetTariffs();
	    $this->Viewer_Assign('aTariffs',$aTariffs);
	    //Действия не нужны все в шаблоне
    }
}
?>
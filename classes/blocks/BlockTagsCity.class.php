<?php
/**
 *	Module "City"
 *	Author: Grebenkin Anton
 *	Contact e-mail: 4freework@gmail.com
 */
/**
 * Обрабатывает блок облака тегов городов компаний
 */
class PluginCity_BlockTagsCity extends Block {

	public function Exec() {
		/**
		 * Получаем города
		 */
		$aCities=$this->Geo_GetGroupCitiesByTargetType('city',30);
		/**
		 * Формируем облако тегов
		 */
		$this->Tools_MakeCloud($aCities);
		/**
		 * Выводим в шаблон
		 */
		$this->Viewer_Assign("aCities",$aCities);
	}
}
?>
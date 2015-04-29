<?php
/**
 *	Module "City"
 *	Author: Grebenkin Anton
 *	Contact e-mail: 4freework@gmail.com
 */
/**
 * Обрабатывает блок облака тегов городов компаний
 */
class PluginCity_BlockTagsCountry extends Block {

	public function Exec() {
		/**
		 * Получаем страны
		 */
		$aCountries=$this->Geo_GetGroupCountriesByTargetType('city',30);
		/**
		 * Формируем облако тегов
		 */
		$this->Tools_MakeCloud($aCountries);
		/**
		 * Выводим в шаблон
		 */
		$this->Viewer_Assign("aCountryList",$aCountries);
	}

}
?>
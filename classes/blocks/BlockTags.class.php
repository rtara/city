<?php
/**
 *	Module "City"
 *	Author: Grebenkin Anton
 *	Contact e-mail: 4freework@gmail.com
 */
/**
 * Обрабатывает блок облака видов деятельности
 *
 */
class PluginCity_BlockTags extends Block {


	public function Exec() {
		/**
		 * Получаем города
		 */
		$aTags=$this->oEngine->PluginCity_City_GetCityTags(30);
		$oViewer=$this->Viewer_GetLocalViewer();
		$oViewer->Assign("aTags",$aTags);
		if (Config::Get('module.city.use_category')){
			$sTag=urldecode(Router::GetParam(0));
			$oViewer->Assign("sTag",$sTag);
			$sTextResult=$oViewer->Fetch(Plugin::GetTemplatePath(__CLASS__)."blocks/block.tags_categories.tpl");
		} else {
			$this->Tools_MakeCloud($aTags);
			$sTextResult=$oViewer->Fetch(Plugin::GetTemplatePath(__CLASS__)."blocks/block.tags_tags.tpl");
		}


		$this->Viewer_Assign('sCityTags',$sTextResult);
		//$this->Viewer_Assign("aTags",$aTags);
	}
}
?>
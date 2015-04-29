<?php

//$sDirRoot=dirname(dirname(dirname(__FILE__)));
$sDirRoot = dirname(realpath((dirname(__FILE__)) . "/../../../"));
set_include_path(get_include_path().PATH_SEPARATOR.$sDirRoot);
chdir($sDirRoot);

require_once($sDirRoot."/config/loader.php");
require_once($sDirRoot."/engine/classes/Cron.class.php");

class UpdateTarrifsCron extends Cron {

	public function Client() {
		//обновляем компании у которых закончилась платная подписка
		$aCityesArrayId = $this->PluginCity_Pay_GetCityesTariffEnding();
		if(empty($aCityesArrayId)) {
			$this->Log("No cityes are found, for update");
		}elseif ($this->PluginCity_Pay_UpdateCityesTariffsByArrayId($aCityesArrayId)){
			$this->Log("Tariff updated: ".count($aCityesArrayId));
			foreach ($aCityesArrayId as $Id) {
				$this->Cache_Delete("city_{$Id}");
			}
			$this->Cache_Clean(Zend_Cache::CLEANING_MODE_MATCHING_TAG,array('city_update'));
		}
	}
}
/**
 * Создаем объект крон-процесса
 */
$app = new UpdateTarrifsCron();
print $app->Exec();
?>
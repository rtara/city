<?php
/**
 *	Module "City"
 *	Author: Grebenkin Anton
 *	Contact e-mail: 4freework@gmail.com
 */
class PluginCity_ModuleUpdate extends Module {
	protected $oMapper;
	protected $oUserCurrent=null;

	/**
	 * Инициализация
	 */
	public function Init() {
		$this->oMapper=Engine::GetMapper(__CLASS__);
		$this->oMapper->SetUserCurrent($this->User_GetUserCurrent());
		$this->oUserCurrent = $this->User_GetUserCurrent();
	}

	public function Convert(){
		return $this->oMapper->Convert03to04();
	}

	public function RepairUrl(){
		return $this->oMapper->RepairUrl();
	}

	public function UpdateToVersion($sVersion){
		switch ($sVersion){
			case '0518':
				return $this->Update0518();
				break;
			case '10109':
				return $this->Update10109();
				break;
			case '10110':
				return $this->Database_ExportSQL(Config::Get('path.root.server').'/plugins/city/updates/update_10110.sql');
				break;
		}
	}

	public function Update0518(){
		$this->oMapper->ConvertVote();
		return $this->Database_ExportSQL(Config::Get('path.root.server').'/plugins/city/updates/update_0518.sql');
	}

	public function Update10109(){
		return $this->Database_ExportSQL(Config::Get('path.root.server').'/plugins/city/updates/update_10109.sql');
	}

	public function ConvertGeo(){
		$aRows = $this->oMapper->GetCityesForConvertGeo();
		foreach ($aRows as $aRow) {
			$iCountryId = $this->oMapper->GetCountryForConvertGeo($aRow);
			$iCityId = 0;
			if ($iCountryId)
				$iCityId = $this->oMapper->GetCityForConvertGeo($aRow,$iCountryId);

			if ($iCityId) {
				$oGeoObject=$this->Geo_GetGeoObject('city',$iCityId);
			} elseif ($iCountryId) {
				$oGeoObject=$this->Geo_GetGeoObject('country',$iCountryId);
			} else {
				$oGeoObject=null;
			}

			if ($oGeoObject) {
				$oCity = $this->PluginCity_City_GetCityById($aRow['city_id']);
				$this->Geo_CreateTarget($oGeoObject,'city',$oCity->getId());
				if ($oCountry=$oGeoObject->getCountry()) {
					$oCity->setCountry($oCountry->getName());
				} else {
					$oCity->setCountry(null);
				}

				if ($oCity=$oGeoObject->getCity()) {
					$oCity->setCity($oCity->getName());
				} else {
					$oCity->setCity(null);
				}
				$this->PluginCity_City_UpdateCity($oCity);
			}
		}
		return true;
	}

}
?>
<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Gran
 * Date: 04.02.13
 * Time: 13:55
 * To change this template use File | Settings | File Templates.
 */
class PluginCity_ModulePay extends Module {
	protected $oMapper;
	protected $oUserCurrent=null;
	protected $aTariffs;
	/**
	 * Инициализация
	 */
	public function Init() {
		$this->oMapper=Engine::GetMapper(__CLASS__);
		$this->oUserCurrent = $this->User_GetUserCurrent();
		$this->aTariffs = $this->GetTariffs();
		//$this->oMapper->SetUserCurrent($this->oUserCurrent);
	}

	public function GetTariffs(){
		$aResult = array();
		foreach (Config::Get('module.city.tariffs') as $oTariff){
			$aResult[$oTariff['id']] = Engine::GetEntity('PluginCity_ModulePay_EntityTariff',$oTariff);
		}
		return $aResult;
	}
	public function GetTariff($idTariff){
		if (isset($this->aTariffs[$idTariff]))
			return $this->aTariffs[$idTariff];
		return null;
	}

	/**
	 * Обновляет тарифы компаний по списку id
	 * @param $aCityId
	 * @param int $idTariff
	 * @param null $sDate
	 * @return bool
	 */
	public function UpdateCityesTariffsByArrayId($aCityId,$idTariff=0,$sDate=null) {
		$this->oMapper->UpdateCityesTariffsByArrayId($aCityId,$idTariff,$sDate);
		return true;
	}

	/**
	 * выбирает id компаний у которых закончилась подписка
	 * @param int $iDays
	 * @return mixed
	 */
	public function GetCityesTariffEnding($iDays = 0) {
		return $this->oMapper->GetCityesTariffEnding($iDays);
	}

}

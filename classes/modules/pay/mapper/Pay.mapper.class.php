<?php
	/**
	 *	Module "City"
	 *	Author: Grebenkin Anton
	 *	Contact e-mail: 4freework@gmail.com
	 */

class PluginCity_ModulePay_MapperPay extends Mapper {
	public function GetCityesTariffEnding($iDays) {
		$sCurrDate = time();
		$sWhere = "1=1 ";
		$sql = "SELECT c.city_id
				FROM
					".Config::Get('db.table.city')." as c
				WHERE ".$sWhere." AND c.city_date_tariff_end < ?s AND c.city_tariff_id > 0
				 ";
		$aReturn=array();
		if ($aRows=$this->oDb->select($sql,date("Y-m-d H:i:s", $sCurrDate + 60*60*24*$iDays))) {
			foreach ($aRows as $aRow) {
				$aReturn[]= $aRow['city_id'];
			}
		}
		return $aReturn;
	}

	public function UpdateCityesTariffsByArrayId($aCityId,$idTariff,$sDate) {
		$sql = "UPDATE ".Config::Get('db.table.city')."
			SET
				city_tariff = ?d,
				city_date_tariff_end = ?
			WHERE
				city_id IN(?a)
		";
		if ($this->oDb->query($sql,$idTariff,$sDate,$aCityId)) {
			return true;
		}
		return false;
	}
}
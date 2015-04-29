<?php
/**
 *	Module "City"
 *	Author: Grebenkin Anton
 *	Contact e-mail: 4freework@gmail.com
 */
class PluginCity_ModuleCityStaff extends ModuleORM {
    protected $oMapperStaff;
	/**
	 * Инициализация модуля
	 */
	public function Init() {
		parent::Init();
        $this->oMapperStaff=Engine::GetMapper(__CLASS__);
	}

	/**
	 * Удаления руководство по id компании
	 * @param $sCityId
	 */
	public function DeleteStaffByCityId($sCityId) {
	    $this->Cache_Delete("city_staff_{$sCityId}");
        return $this->oMapperStaff->DeleteStaffByCityId($sCityId);
    }
}

?>
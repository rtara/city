<?php
/**
 *	Module "City"
 *	Author: Grebenkin Anton
 *	Contact e-mail: 4freework@gmail.com
 */

class PluginCity_ModuleCityStaff_MapperCityStaff extends MapperORM {


    public function DeleteStaffByCityId($sCityId) {
        $sql = "DELETE FROM ".Config::Get('db.table.staff')."
			WHERE
				city_id = ?d
		";
        if ($this->oDb->query($sql,$sCityId)) {
            return true;
        }
        return false;
    }
}
?>
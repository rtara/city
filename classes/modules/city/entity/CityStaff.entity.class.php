<?php
/**
 *	Module "City"
 *	Author: Grebenkin Anton
 *	Contact e-mail: 4freework@gmail.com
 */

class PluginCity_ModuleCityStaff_EntityCityStaff extends Entity
{
	public function getCityId() {
		return $this->_getDataOne('city_id');
	}
	public function getUserId() {
		return $this->_getDataOne('user_id');
	}
	public function getName() {
		return $this->_getDataOne('staff_name');
	}
	public function getPosition() {
		return $this->_getDataOne('staff_position');
	}

  
    
    public function setCityId($data) {
        $this->_aData['city_id']=$data;
    }
	public function setUserId($data) {
        $this->_aData['user_id']=$data;
    }
    public function setName($data) {
        $this->_aData['staff_name']=$data;
    }
    public function setPosition($data) {
        $this->_aData['staff_position']=$data;
    }
}
?>
<?php
/**
 *	Module "City"
 *	Author: Grebenkin Anton
 *	Contact e-mail: 4freework@gmail.com
 */

class PluginCity_ModuleCity_EntityCityTag extends Entity
{
	public function getId() {
		return $this->_getDataOne('city_tag_id');
	}
	public function getCityId() {
		return $this->_getDataOne('city_id');
	}
	public function getUserId() {
		return $this->_getDataOne('user_id');
	}
	public function getText() {
		return $this->_getDataOne('city_tag_text');
	}

	public function getCount() {
		return $this->_getDataOne('count');
	}
	public function getSize() {
		return $this->_getDataOne('size');
	}

  
    
	public function setId($data) {
        $this->_aData['city_tag_id']=$data;
    }
    public function setCityId($data) {
        $this->_aData['city_id']=$data;
    }
    public function setUserId($data) {
        $this->_aData['user_id']=$data;
    }
    public function setText($data) {
        $this->_aData['city_tag_text']=$data;
    }
    
	public function setSize($data) {
        $this->_aData['size']=$data;
    }
}
?>
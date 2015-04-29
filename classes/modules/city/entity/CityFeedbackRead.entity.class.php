<?php
/**
*	Module "City"
*	Author: Grebenkin Anton 
*	Contact e-mail: 4freework@gmail.com
*/

class PluginCity_ModuleCity_EntityCityFeedbackRead extends Entity
{
	public function getCityId() {
		return $this->_getDataOne('city_id');
	}
	public function getUserId() {
		return $this->_getDataOne('user_id');
	}
	public function getDateRead() {
		return $this->_getDataOne('date_read');
	}
	public function getFeedbackCountLast() {
		return $this->_getDataOne('feedback_count_last');
	}
	public function getFeedbackIdLast() {
		return $this->_getDataOne('feedback_id_last');
	}

    
    
	public function setCityId($data) {
        $this->_aData['city_id']=$data;
    }
    public function setUserId($data) {
        $this->_aData['user_id']=$data;
    }
    public function setDateRead($data) {
        $this->_aData['date_read']=$data;
    }
    public function setFeedbackCountLast($data) {
        $this->_aData['feedback_count_last']=$data;
    }
    public function setFeedbackIdLast($data) {
        $this->_aData['feedback_id_last']=$data;
    }
}
?>
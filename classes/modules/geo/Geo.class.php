<?php
class PluginCity_ModuleGeo extends PluginCity_Inherit_ModuleGeo {
	public function CheckTargetCity(){
		return true;
	}


	public function GetCityesByFilter($aFilter,$aOrder,$iCurrPage,$iPerPage,$aAllowData=null) {
		// если используется активация, то показываем только активированные
		if (Config::Get('module.city.use_activate') and !isset($aFilter['active']))
			$aFilter['active'] = 1;
		$sKey="cityes_filter_".serialize($aFilter).serialize($aOrder)."_{$iCurrPage}_{$iPerPage}";
		if (false === ($data = $this->Cache_Get($sKey))) {
			$data = array('collection'=>$this->oMapper->GetCityesByFilter($aFilter,$aOrder,$iCount,$iCurrPage,$iPerPage),'count'=>$iCount);
			$this->Cache_Set($data, $sKey, array("city_update","city_new"), 60*60*24*2);
		}
		$data['collection']=$this->GetCityesAdditionalData($data['collection'],$aAllowData);
		return $data;
	}

	public function GetCityTargets($aFilter,$iCurrPage,$iPerPage) {
		$sKey="cityes_targets_".serialize($aFilter)."_{$iCurrPage}_{$iPerPage}";
		if (false === ($data = $this->Cache_Get($sKey))) {
			$data = array('collection'=>$this->oMapper->GetTargets($aFilter,$iCount,$iCurrPage,$iPerPage),'count'=>$iCount);
			$this->Cache_Set($data, $sKey, array("city_update","city_new"), 60*60*24);
		}
		return $data;
	}


	public function GetCityTargetsByArray($aTargetId) {
		if (!is_array($aTargetId)) {
			$aTargetId=array($aTargetId);
		}
		if (!count($aTargetId)) {
			return array();
		}
		$aResult=array();
		$aTargets=$this->GetCityTargets(array('target_type'=>'city','target_id'=>$aTargetId),1,count($aTargetId));
		if ($aTargets['count']) {
			foreach($aTargets['collection'] as $oTarget) {
				$aResult[$oTarget->getTargetId()][]=$oTarget;
			}
		}
		return $aResult;
	}
}
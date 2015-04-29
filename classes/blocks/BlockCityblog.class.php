<?php
/**
 *	Module "City"
 *	Author: Grebenkin Anton
 *	Contact e-mail: 4freework@gmail.com
 */
/**
 * Обработка блока компаний действий
 *
 */
class PluginCity_BlockCityblog extends Block {
    
    public function Exec() {
	    $oCity = $this->GetParam(0);
	    if ($oCity) {
		    $aResult = $this->Topic_GetTopicsByBlog($oCity->getBlog(), 1, 5);
		    $aTopics = $aResult['collection'];
		    $this->Viewer_Assign('aBlockCityBlog', $aTopics);
	    }
    }
}
?>
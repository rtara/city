<?php
/**
 *	Module "City"
 *	Author: Grebenkin Anton
 *	Contact e-mail: 4freework@gmail.com
 */

/**
 * Блок отзывов
 */
class PluginCity_BlockFeedbacks extends Block {
    public function Exec() {
	    $aFeedbacks=$this->Comment_GetCommentsOnline('city',Config::Get('block.stream.row'));
		$this->Viewer_Assign('aFeedbacks',$aFeedbacks);
    }
}
?>
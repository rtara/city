<?php
/**
 * Модуль для работы с блогами
 *
 */

class PluginCity_ModuleBlog extends PluginCity_Inherit_ModuleBlog {
	public function GetInaccessibleBlogsByUser($oUser=null) {
		$aCloseBlogs = parent::GetInaccessibleBlogsByUser($oUser);
		$aCloseInactiveBlogs = $this->PluginCity_City_GetInaccessibleCityBlogs();
		return array_merge($aCloseBlogs,$aCloseInactiveBlogs);
	}

    public function GetBlogsAllowByUser($oUser) {
        $aAllowBlogsUser = parent::GetBlogsAllowByUser($oUser);
        //если используется активация то отдаем только блоги активированных компаний
        if (Config::Get('module.city.use_activate') and !$oUser->isAdministrator()) {
            $aInaccessible = $this->GetInaccessibleBlogsByUser($oUser);
            $aReturn = array();
            foreach ($aAllowBlogsUser as $aRow) {
                if ($aRow->getType() <> 'city' or !in_array($aRow->getId(),$aInaccessible))
                    $aReturn[$aRow->getId()]=$aRow;
            }
            return $aReturn;
        }
        return $aAllowBlogsUser;
    }
}
?>
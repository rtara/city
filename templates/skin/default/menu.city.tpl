<ul class="nav nav-pills">
			<li {if $sMenuItemSelect=='blog'}class="active"{/if}>
				<a href="{$oCity->getUrlFull()}/blog/">{$aLang.plugin.city.city_menu_blog} </a>
			   {if $iCountCityTopicNew>0}+{$iCountCityTopicNew}{/if}
			</li>
			
			<li {if $sMenuItemSelect=='vacancies'}class="active"{/if}>
				<a href="{$oCity->getUrlFull()}/vacancies/">{$aLang.plugin.city.city_menu_vacancies}</a>
				{if $iCountVacancies>0}({$iCountVacancies}){/if}
			</li>
			
			<li {if $sMenuItemSelect=='feedbacks'}class="active"{/if}>
				<a href="{$oCity->getUrlFull()}/feedbacks/">{$aLang.plugin.city.city_menu_feedbacks}</a>
				{if $oCity->getCountFeedback()>0}({$oCity->getCountFeedback()}){/if}
			</li>
             <li {if $sMenuItemSelect=='fans'}class="active"{/if}>
                <a href="{$oCity->getUrlFull()}/fans/">{$aLang.plugin.city.city_menu_fans}</a>
				 {if $oCity->getCountFavourite()>0}({$oCity->getCountFavourite()}){/if}
            </li>
	{if $iCountTender>0 and $oUserCurrent and ($oUserCurrent->getId()==$oCity->getOwnerId() or $oUserCurrent->isAdministrator() or ($oCity->getUserIsAdministrator()) )}
            <li>
                <a href="{router page='city'}edit/{$oCity->getId()}/admin/">{$aLang.plugin.city.city_menu_tenders} ({$iCountTender})</a>
            </li>
	{/if}
</ul>

<ul class="nav nav-filter">
	
	<li {if $sMenuItemSelect=='index'}class="active"{/if}>
		<a href="{$oCompany->getUrlFull()}/">{$aLang.plugin.company.company_menu_profile}</a>
	</li>
	
	<li {if $sMenuItemSelect=='blog'}class="active"{/if}>
		<a href="{$oCompany->getUrlFull()}/blog/">{$aLang.plugin.company.company_menu_blog} {if $iCountCompanyTopicNew>0}+{$iCountCompanyTopicNew}{/if}</a>
	</li>
	<li {if $sMenuItemSelect=='vacancies'}class="active"{/if}>
		<a href="{$oCompany->getUrlFull()}/vacancies/">{$aLang.plugin.company.company_menu_vacancies}{if $iCountVacancies>0}({$iCountVacancies}){/if}</a>
	</li>
	<li {if $sMenuItemSelect=='feedbacks'}class="active"{/if}>
		<a href="{$oCompany->getUrlFull()}/feedbacks/">{$aLang.plugin.company.company_menu_feedbacks} {if $oCompany->getCountFeedback()>0}({$oCompany->getCountFeedback()}){/if}</a>
	</li>
	<li {if $sMenuItemSelect=='fans'}class="active"{/if}>
	    <a href="{$oCompany->getUrlFull()}/fans/">{$aLang.plugin.company.company_menu_fans} {if $oCompany->getCountFavourite()>0}({$oCompany->getCountFavourite()}){/if}</a>
	</li>
	{if $iCountTender>0 and $oUserCurrent and ($oUserCurrent->getId()==$oCompany->getOwnerId() or $oUserCurrent->isAdministrator() or ($oCompany->getUserIsAdministrator()) )}
        <li>
            <a href="{router page='city'}edit/{$oCompany->getId()}/admin/">{$aLang.plugin.company.company_menu_tenders} ({$iCountTender})</a>
        </li>
	{/if}
</ul>

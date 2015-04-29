<h2 class="page-header">{$aLang.plugin.city.cityes}</h2>


<ul class="nav nav nav-filter">
	<li {if $sMenuSubItemSelect=='all'}class="active"{/if}>
		<a href="{router page='cityes'}">{$aLang.plugin.city.city_menu_main_all} ({$iCountCityes})</a>
	</li>

	<li {if $sMenuSubItemSelect=='new'}class="active"{/if}>
		<a href="{router page='cityes'}new/">{$aLang.plugin.city.city_menu_main_new} {if $iCountNewCityes>0} +{$iCountNewCityes}{/if}</a>
	</li>

{if $oUserCurrent}
	<li {if $sMenuSubItemSelect=='fav'}class="active"{/if}>
		<a href="{router page='cityes'}fav/">{$aLang.plugin.city.city_menu_main_fav} {if $iCountFavCityes>0}({$iCountFavCityes}){/if}</a>
	</li>


	{if $aUserCity}
		{if $oUserCity}
			<li {if $sMenuSubItemSelect=='index' and $oCity and $oCity->getId() == $oUserCity->getId()}class="active"{/if}>
				<a href="{$oUserCity->getUrlFull()}/" class="nav-part-left">{$oUserCity->getName()}</a>
				{if $iCountTender > 0}<a href="{router page='city'}edit/{$oUserCity->getId()}/admin/" class="nav-part-right">+{$iCountTender}</a>{/if}
			</li>
		{else}
			<li {if $sMenuSubItemSelect=='my'}class="active"{/if}><a href="{router page='cityes'}my/">{$aLang.plugin.city.city_menu_main_my} ({$aUserCity|@count})</a></li>
		{/if}
	{/if}
	{if $oUserCurrent->isAdministrator() && $oConfig->GetValue('module.city.use_activate')}
    <li {if $sMenuSubItemSelect=='moderation'}class="active"{/if}><a href="{router page='cityes'}moderation/">{$aLang.plugin.city.city_menu_main_moderation} {if $iCountModeration>0}({$iCountModeration}){/if}</a></li>
	{/if}
{/if}
</ul>

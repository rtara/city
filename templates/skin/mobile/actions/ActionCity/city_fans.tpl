
{include file="header.tpl" menu='main'}

{include file="`$aTemplatePathPlugin['city']`header.city.tpl"}

<h2 class="page-header">{$aLang.plugin.city.city_menu_fans}</h2>
{if $aFans}
{include file='user_list.tpl' aUsersList=$aFans}

{else}
	{$aLang.user_empty}
{/if}


{include file='footer.tpl'}


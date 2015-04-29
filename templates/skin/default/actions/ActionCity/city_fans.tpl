{if $oCity->IsAllowTariff('branding') && $oCity->getUseBrandImage()}
{include file="`$aTemplatePathPlugin['city']`header_branding.tpl" menu='main'}
	{else}
{include file="header.tpl" menu='main'}
{/if}
{include file="`$aTemplatePathPlugin['city']`header.city.tpl"}

<h2 class="page-header">{$aLang.plugin.city.city_menu_fans}</h2>
{if $aFans}
{include file='user_list.tpl' aUsersList=$aFans}

{else}
	{$aLang.user_empty}
{/if}

{if $oCity->IsAllowTariff('branding') && $oCity->getUseBrandImage()}
{include file="`$aTemplatePathPlugin['city']`footer_branding.tpl"}
	{else}
{include file='footer.tpl'}
{/if}

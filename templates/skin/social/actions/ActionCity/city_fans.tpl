{if $oCity->IsAllowTariff('branding') && $oCity->getUseBrandImage()}
{include file="`$aTemplatePathPlugin['city']`header_branding.tpl"}
	{else}
{include file="header.tpl"}
{/if}
{include file="`$aTemplatePathPlugin['city']`header.city.tpl"}

{if $aFans}
	{include file='user_list.tpl' aUsersList=$aFans}
{else}
	{$aLang.user_empty}
{/if}

{include file='footer.tpl'}

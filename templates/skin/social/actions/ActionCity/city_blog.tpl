{if $oCity->IsAllowTariff('branding') && $oCity->getUseBrandImage()}
{include file="`$aTemplatePathPlugin['city']`header_branding.tpl"}
	{else}
{include file="header.tpl"}
{/if}
{include file="`$aTemplatePathPlugin['city']`header.city.tpl"}

{include file='topic_list.tpl'}

{include file='footer.tpl'}


{if $oCity->IsAllowTariff('branding') && $oCity->getUseBrandImage()}
{include file="`$aTemplatePathPlugin['city']`header_branding.tpl" menu='main'}
	{else}
{include file="header.tpl" menu='main'}
{/if}
{include file="`$aTemplatePathPlugin['city']`header.city.tpl"}

{include file='topic_list.tpl'}

{if $oCity->IsAllowTariff('branding') && $oCity->getUseBrandImage()}
{include file="`$aTemplatePathPlugin['city']`footer_branding.tpl"}
	{else}
{include file='footer.tpl'}
{/if}


{if $oCity->IsAllowTariff('branding') && $oCity->getUseBrandImage()}
	{include file="`$aTemplatePathPlugin['city']`header_branding.tpl" menu='main'}
{else}
	{include file="header.tpl" menu='main'}
{/if}
{include file="`$aTemplatePathPlugin['city']`header.city.tpl"}
{assign var="oUserOwner" value=$oCity->getOwner()}

{assign var="sCityEditTemplateName" value="`$aTemplatePathPlugin['city']`city_profile_`$oCity->getType()`.tpl"}
{include file=$sCityEditTemplateName}


{if $oCity->IsAllowTariff('branding') && $oCity->getUseBrandImage()}
{include file="`$aTemplatePathPlugin['city']`footer_branding.tpl"}
	{else}
{include file='footer.tpl'}
{/if}



{include file="header.tpl" menu='main'}

{include file="`$aTemplatePathPlugin['city']`header.city.tpl"}
{assign var="oUserOwner" value=$oCity->getOwner()}

{assign var="sCityEditTemplateName" value="`$aTemplatePathPlugin['city']`city_profile_`$oCity->getType()`.tpl"}
{include file=$sCityEditTemplateName}


{include file='footer.tpl'}




{include file='header.tpl'}

{assign var="sCityEditTemplateName" value="`$aTemplatePathPlugin['city']`city_edit_`$oCity->getType()`.tpl"}
{include file=$sCityEditTemplateName}

{include file='footer.tpl'}

{include file='header.tpl' menu="main"}

{assign var="sCityEditTemplateName" value="`$aTemplatePathPlugin['city']`city_edit_`$oCity->getType()`.tpl"}
{include file=$sCityEditTemplateName}

{include file='footer.tpl'}

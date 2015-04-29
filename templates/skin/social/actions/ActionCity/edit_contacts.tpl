{include file='header.tpl'}

{assign var="sCityEditTemplateName" value="`$aTemplatePathPlugin['city']`city_edit_contacts_`$oCity->getType()`.tpl"}
{include file=$sCityEditTemplateName}

{include file='footer.tpl'}

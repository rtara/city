{if $oCity->IsAllowTariff('branding') && $oCity->getUseBrandImage()}
{include file="`$aTemplatePathPlugin['city']`header_branding.tpl"}
	{else}
{include file="header.tpl"}
{/if}
{include file="`$aTemplatePathPlugin['city']`header.city.tpl"}


{if $aVacancies}
	{include file="$sJobTemplatePath/vacancy_list.tpl" aVacancies=$aVacancies}
{else}
	{if $oCity->getVacancies()}
		{$oCity->getVacancies()}
	{else}
		<div class="notice-empty">{$aLang.city_vacancies_empty}</div>
	{/if}
{/if}


{include file='footer.tpl'}

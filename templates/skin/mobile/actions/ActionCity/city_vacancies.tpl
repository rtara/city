
{include file="header.tpl" menu='main'}

{include file="`$aTemplatePathPlugin['city']`header.city.tpl"}


	<article class="topic">
	<header class="topic-header">
		<h1 class="topic-title">{$aLang.plugin.city.city_vacancies_title}</h1>
	</header>
	<div class="topic-content text">
	{if $aVacancies}
	{include file="$sJobTemplatePath/vacancy_list.tpl" aVacancies=$aVacancies}
		{else}
		{if $oCity->getVacancies()}
			<div class="content">
				{$oCity->getVacancies()}
			</div>
			{else}
			{$aLang.city_vacancies_empty}
		{/if}
	{/if}
	</div>
	<footer class="topic-footer">
	</footer>
	</article>


{include file='footer.tpl'}


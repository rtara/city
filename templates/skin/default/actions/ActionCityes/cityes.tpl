{include file='header.tpl' menu='main'}
<script type="text/javascript">
    jQuery(document).ready(function($) {
        ls.city.autocomplete($("#input-city"), aRouter['city']+'ajax/autocompleter/city/');
    } );
</script>
<h2 class="page-header">
    {if $sHeaderName}
        {$sHeaderName}
    {else}
        {$aLang.plugin.city.city_cityes_header}
	{/if}
    </h2>

<form action="{router page='search'}cityes/" method="GET" class="search-item">
	<div class="search-input-wrapper">
		<input id="input-city" type="text" placeholder="{$aLang.plugin.city.city_cityes_find_text}" autocomplete="off" name="q" value="" class="input-text">
		<input type="submit" value="" class="input-submit" />
	</div>
</form>
{router page='cityes' assign=sCityesRootPage}
{include file="`$aTemplatePathPlugin['city']`cityes_table_list.tpl" aCity=$aCity sCityesRootPage=$sCityesRootPage bCityesUseOrder=$bCityesUseOrder}

{include file='paging.tpl' aPaging=$aPaging}
{include file='footer.tpl'}
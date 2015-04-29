{include file='header.tpl' menu='main'}


<script type="text/javascript">
    jQuery(document).ready(function($) {
        ls.city.autocomplete($("#input-city"), aRouter['city']+'ajax/autocompleter/city/');
    } );
</script>


<form action="{router page='search'}cityes/" method="GET" class="search-item">
	<div class="search search-item">
		<input id="input-city" type="text" placeholder="{$aLang.plugin.city.city_cityes_find_text}" autocomplete="off" name="q" value="" class="input-text">
		<input type="submit" value="" class="icon-search input-submit" />
	</div>
</form>


{include file="`$aTemplatePathPlugin['city']`cityes_table_list.tpl" aCity=$aCity}

{include file='paging.tpl' aPaging=$aPaging}
{include file='footer.tpl'}
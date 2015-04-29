{include file='header.tpl' menu='main'}

{if $aTags}
<ul class="tag-cloud">
	{foreach from=$aTags item=oTag}
        <li><a class="tag-size-{$oTag->getSize()}" href="{router page='cityes'}tag/{$oTag->getText()|escape:'url'}/">{$oTag->getText()|escape:'html'}</a></li>
	{/foreach}
</ul>
<form action="" method="GET" class="js-city-tag-search-form search-tags">
    <input type="text" name="tag" placeholder="{$aLang.block_tags_search}" value="{$sTag|escape:'html'}" class="input-text input-width-full autocomplete-city-tags js-city-tag-search" />
</form>
{/if}


{include file="`$aTemplatePathPlugin['city']`cityes_table_list.tpl" aCity=$aCity}

{include file='paging.tpl' aPaging=$aPaging}
{include file='footer.tpl'}
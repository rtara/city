{if $aCities && count($aCities)>0}
<section class="block">
	<header class="block-header">
		<h3>{$aLang.block_city_tags}</h3>
	</header>


	<div class="block-content">
		<ul class="tag-cloud">
			{foreach from=$aCities item=oCity}
				<li><a class="tag-size-{$oCity->getSize()}" href="{router page='cityes'}city/{$oCity->getId()}/">{$oCity->getName()|escape:'html'}</a></li>
			{/foreach}
		</ul>
	</div>
</section>
{/if}

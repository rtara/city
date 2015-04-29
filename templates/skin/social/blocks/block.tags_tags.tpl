<section class="block block-type-tags">
	<header class="block-header sep">
		<h3>{$aLang.plugin.city.block_city_tags}</h3>
	</header>


	<div class="block-content">

		<div class="js-block-tags-content" data-type="all">
		{if $aTags}
			<ul class="tag-cloud">
				{foreach from=$aTags item=oTag}
					<li><a class="tag-size-{$oTag->getSize()}" href="{router page='cityes'}tag/{$oTag->getText()|escape:'url'}/">{$oTag->getText()|escape:'html'}</a></li>
				{/foreach}
			</ul>
			{else}
			<div class="notice-empty">{$aLang.block_tags_empty}</div>
		{/if}
		</div>

	</div>
</section>

{if $aBlockCityBlog}
<div class="block block-type-blogs" id="block_city_blog">
	<header class="block-header sep">
		<h3>{$aLang.plugin.city.block_city_blog}</h3>
	</header>

	<div class="block-content">
			{foreach from=$aBlockCityBlog item=oTopic}
					{strip}
						<a href="{$oTopic->getUrl()}">{$oTopic->getTitle()|escape:'html'}</a>
					{/strip}<br>
					<span class="note mb-10">{date_format date=$oTopic->getDate() format='j F Y, H:i'}</span>
			{/foreach}
		<footer>
			<a href="{$oCity->getUrlFull()}/blog/">{$aLang.plugin.city.block_city_blog_all}</a>
		</footer>
	</div>
</div>
{/if}

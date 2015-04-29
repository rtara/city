<div class="block block-type-blogs" id="block_cityes">
	<header class="block-header sep">
		<h3>{$aLang.plugin.city.block_city}</h3>
	</header>


    <div class="block-content">
        <div class="js-block-blogs-content">
            <ul class="item-list">
			{foreach from=$aCity item=oCity}
				<li>
					{strip}
						<img src="{$oCity->getLogoPath(24)}" alt="" width="20" class="avatar"/><a href="{$oCity->getUrlFull()}/">{$oCity->getName()|escape:'html'}</a>
					{/strip}

                    <p>{$aLang.blog_rating}: <strong>{$oCity->getRating()}</strong></p>
				</li>
			{/foreach}
			</ul>
		</div>


		<footer>
			<a href="{router page='cityes'}">{$aLang.plugin.city.block_city_allcityes}</a>
		</footer>
	</div>
</div>

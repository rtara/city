<section class="block block-type-blogs">
    <header class="block-header sep">
        <h3>{$aLang.plugin.city.block_city_category}</h3>
    </header>

    <div class="block-content">
        <div class="js-block-genres-content">
            <ul class="block-blog-list">
			{foreach from=$aTags item=oTag}
                <li>
					{strip}
                        <a href="{router page='cityes'}tag/{$oTag->getText()|escape:'url'}/" {if $sTag == $oTag->getText()} class="active"{/if}>{$oTag->getText()|escape:'html'}</a>
					{/strip}

                    <strong>{$oTag->getCount()}</strong>
                </li>
			{/foreach}
            </ul>
        </div>

        <footer>
            <a href="{router page='cityes'}">{$aLang.plugin.city.block_city_allcityes}</a>
        </footer>
    </div>
</section>
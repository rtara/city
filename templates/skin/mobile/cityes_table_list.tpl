	<ul class="blog-list">
		{foreach from=$aCity item=oCity}
			{assign var="oUserOwner" value=$oCity->getOwner()}
			{assign var="oTopicLast" value=$oCity->getTopicLast()}

			<li>
				<a href="{$oCity->getUrlFull()}">
					<img src="{$oCity->getLogoPath(48)}" width="48" height="48" alt="avatar" class="avatar" />
				</a>

				<h3><a href="{$oCity->getUrlFull()}">{$oCity->getName()|escape:'html'}</a></h3>

				<p>
					{$aLang.plugin.city.city_cityes_rating}: {$oCity->getRating()}
					{if $oTopicLast}
						<br>{$aLang.plugin.city.city_topic_last}: <a href="{$oTopicLast->getUrl()}">{$oTopicLast->getTitle()}</a>
					{/if}
				</p>
			</li>
		{/foreach}
	</ul>

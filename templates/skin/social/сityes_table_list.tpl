{foreach from=$aCity item=oCity}
	{assign var="oUserOwner" value=$oCity->getOwner()}
	{assign var="oTopicLast" value=$oCity->getTopicLast()}
	<div class="blog-item">
		<a href="{$oCity->getUrlFull()}">
			<img src="{$oCity->getLogoPath(100)}" width="100" height="100" alt="avatar" class="avatar" />
		</a>
		<dl>
			<dt>{$aLang.blog_name}:</dt>
			<dd>
				{if $oConfig->GetValue('module.city.use_activate')}
					{if !$oCity->getActive()}<i class="icon-warning-sign" alt="{$aLang.plugin.city.city_cityes_need_activate}"></i>{/if}
				{/if}
				<a href="{$oCity->getUrlFull()}/" class="blog-header">{$oCity->getName()|escape:'html'}</a>

			</dd>
		</dl>
		<dl>
			<dt>{$aLang.blog_description}:</dt>
			<dd>{$oCity->getDescription()|strip_tags|trim|truncate:250:'...'}</dd>
		</dl>
		{if $oTopicLast}
        <dl>
            <dt>{$aLang.plugin.city.city_topic_last}:</dt>
            <dd><a href="{$oTopicLast->getUrl()}">{$oTopicLast->getTitle()}</a></dd>
        </dl>
		{/if}
		<dl>
			<dt>{$aLang.plugin.city.city_menu_fans}:</dt>
			<dd>{$oCity->getCountFavourite()}</dd>
		</dl>
		<dl>
			<dt>{$aLang.plugin.city.city_cityes_feedbacks}:</dt>
			<dd>{$oCity->getCountFeedback()}</dd>
		</dl>
		<dl>
			<dt>{$aLang.blogs_rating}:</dt>
			<dd><strong>{$oCity->getRating()}</strong></dd>
		</dl>
	</div>
{/foreach}
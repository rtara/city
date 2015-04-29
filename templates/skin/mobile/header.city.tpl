{assign var="oVote" value=$oCity->getVote()}
{assign var="oUserOwner" value=$oCity->getOwner()}
<div class="city">
	<header class="city-header">
        <img src="{$oCity->getLogoPath(48)}" alt="{$oCity->getName()|escape:'html'}" title="{$oCity->getName()|escape:'html'}" class="img">
	<h2>{$oCity->getName()|escape:'html'}</h2>
        <p>
		{$aLang.plugin.city.city_rating}: <span id="vote_total_city_alt_{$oCity->getId()}">{$oCity->getRating()}</span>
        </p>
	</header>
    <ul class="actions clearfix">
        <li><a href="{$oCity->getUrlFull()}/rss/" class="icon-rss"></a></li>
	{if $oUserCurrent and ($oUserCurrent->getId()==$oCity->getOwnerId() or $oUserCurrent->isAdministrator() or ($oCity->getUserIsAdministrator()) )}
		<li><a href="{router page='city'}edit/{$oCity->getId()}/" title="{$aLang.plugin.city.city_edit}" class="icon-edit"></a></li>
	{/if}
	{if $oUserCurrent and $oUserCurrent->isAdministrator()}
        <li><a href="#"
               title="{$aLang.plugin.city.city_delete}" class="icon-delete"
               onclick='
                       return confirm("{$aLang.plugin.city.city_notice_delete_city}") ? window.location="{router page='city'}delete/{$oCity->getId()}/?security_ls_key={$LIVESTREET_SECURITY_KEY}" : false;'></a></li>
		{if $oConfig->GetValue('module.city.use_activate')}
            <li><a href="{router page='city'}{if $oCity->getActive()}deactivate{else}activate{/if}/{$oCity->getId()}/?security_ls_key={$LIVESTREET_SECURITY_KEY}" class="edit">
                <i class="icon-synio-actions-draft"></i>{if $oCity->getActive()}{$aLang.plugin.city.city_deactivate}{else}{$aLang.plugin.city.city_activate}{/if}</a></li>
		{/if}
	{/if}
	{if $bCanWriteBlog}
        <li><a href="{router page='topic'}add/?blog_id={$oCity->getBlogId()}" title="{$aLang.plugin.city.city_add_topic}" class="icon-edit"></a></li>
	{/if}
	{if $oUserCurrent && $oUserCurrent->getId() != $oCity->getOwnerId()}
        <li id="vote_total_blog_{$oCity->getId()}" class="vote-result
				vote-no-rating

				{if $oVote || ($oUserCurrent && $oUserOwner->getId() == $oUserCurrent->getId())}
					{if $oCity->getRating() > 0}
						vote-count-positive
					{elseif $oCity->getRating() < 0}
						vote-count-negative
					{elseif $oCity->getRating() == 0}
						vote-count-zero
					{/if}
				{/if}

				{if $oVote}
					voted

					{if $oVote->getDirection() > 0}
						voted-up
					{elseif $oVote->getDirection() < 0}
						voted-down
					{/if}
				{/if}"

			{if $oUserCurrent && !$oVote}
            onclick="ls.tools.slide($('#vote_area_city_{$oCity->getId()}'), $(this));"
			{/if}

            title="{$aLang.blog_vote_count}: {$oCity->getCountVote()}">
        </li>
	{/if}
    </ul>
    <div id="vote_area_city_{$oCity->getId()}" class="vote">
        <div class="vote-item vote-up" onclick="return ls.vote.vote({$oCity->getId()},this,1,'city');"><i></i></div>
        <div class="vote-item vote-down" onclick="return ls.vote.vote({$oCity->getId()},this,-1,'city');"><i></i></div>
    </div>
</div>
<div class="menu">
{include file="`$aTemplatePathPlugin['city']`menu.city.tpl"}
</div>

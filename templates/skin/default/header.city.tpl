{assign var="oVote" value=$oCity->getVote()}
<div class="city">
	<div id="vote_area_city_{$oCity->getId()}" class="vote-topic
															{if $oCity->getRating() > 0}
																vote-count-positive
															{elseif $oCity->getRating() < 0}
																vote-count-negative
															{elseif $oCity->getRating() == 0}
																vote-count-zero
															{/if}

															{if $oVote}
																voted

																{if $oVote->getDirection() > 0}
																	voted-up
																{elseif $oVote->getDirection() < 0}
																	voted-down
																{/if}
															{else}
																not-voted
															{/if}

															{if ($oUserCurrent && $oCity->getOwnerId() == $oUserCurrent->getId())}
																vote-nobuttons
															{/if}">
		<a href="#" class="vote-item vote-down" onclick="return ls.vote.vote({$oCity->getId()},this,-1,'city');"><span><i></i></span></a>
		<div class="vote-item vote-count" title="{$aLang.plugin.city.city_vote_count}: {$oCity->getCountVote()}"><span id="vote_total_city_{$oCity->getId()}">{if $oCity->getRating() > 0}+{/if}{$oCity->getRating()}</span></div>
		<a href="#" class="vote-item vote-up" onclick="return ls.vote.vote({$oCity->getId()},this,1,'city');"><span><i></i></span></a>
	</div>

{if $oUserCurrent}
	<a href="#" onclick="return ls.favourite.toggle({$oCity->getId()},this,'city');" class="favourite {if $oUserCurrent && $oCity->getIsFavourite()}active{/if}" title="{$aLang.plugin.city.city_button_like}"></a>
{/if}
	<div class="city-header">
	<h1><a href="{$oCity->getUrlFull()}/"><img src="{$oCity->getLogoPath(100)}" width="100" height="100" alt="{$oCity->getName()|escape:'html'}" title="{$oCity->getName()|escape:'html'}"></a>
		<a href="{$oCity->getUrlFull()}/" class="city_name">{$oCity->getName()|escape:'html'}</a>
	{if $oCity->getLegalName()} ({$oCity->getLegalName()|escape:'html'}){/if}</h1>

		<ul class="actions">
			<li><a href="{$oCity->getUrlFull()}/rss/" class="rss"></a></li>
		{if $oUserCurrent and ($oUserCurrent->getId()==$oCity->getOwnerId() or $oUserCurrent->isAdministrator() or ($oCity->getUserIsAdministrator()) )}
			<li><a href="{router page='city'}edit/{$oCity->getId()}/profile/" title="{$aLang.plugin.city.city_edit}" class="edit"><i class="icon-synio-actions-edit"></i>{$aLang.plugin.city.city_edit}</a></li>
		{/if}
		{if $oUserCurrent and $oUserCurrent->isAdministrator()}
			<li><a href="#"
			       title="{$aLang.plugin.city.city_delete}" class="delete"
			       onclick='
                   return confirm("{$aLang.plugin.city.city_notice_delete_city}") ? window.location="{router page='city'}delete/{$oCity->getId()}/?security_ls_key={$LIVESTREET_SECURITY_KEY}" : false;'><i class="icon-synio-actions-delete"></i>{$aLang.plugin.city.city_delete}</a></li>
			{if $oConfig->GetValue('module.city.use_activate')}
			<li><a href="{router page='city'}{if $oCity->getActive()}deactivate{else}activate{/if}/{$oCity->getId()}/?security_ls_key={$LIVESTREET_SECURITY_KEY}" class="edit">
				<i class="icon-synio-actions-draft"></i>{if $oCity->getActive()}{$aLang.plugin.city.city_deactivate}{else}{$aLang.plugin.city.city_activate}{/if}</a></li>
			{/if}
		{/if}
		{if $bCanWriteBlog}
			<li><a href="{router page='topic'}add/?blog_id={$oCity->getBlogId()}" title="{$aLang.plugin.city.city_add_topic}" class="edit"><i class="icon-synio-actions-edit"></i>{$aLang.plugin.city.city_add_topic}</a></li>
		{/if}
		</ul>
		<div class="menu">
		{include file="`$aTemplatePathPlugin['city']`menu.city.tpl"}
		</div>
	</div>
</div>


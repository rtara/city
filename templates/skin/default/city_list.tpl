{if count($aCityes)>0}
<div >
	{foreach from=$aCityes item=oCity}
	<article class="topic">
	<header class="topic-header">
		<div class="topic-content text">
            <h2 class="header-table"><a href="{$oCity->getUrlFull()}/">{$oCity->getName()|escape:'html'}</a></h2>

            <a href="{$oCity->getUrlFull()}/"><img src="{$oCity->getLogoPath(100)}" width="100" height="100" alt="{$oCity->getName()|escape:'html'}" title="{$oCity->getName()|escape:'html'}" class="logotype fl-l"></a>

	        {$oCity->getDescription()}

            <ul class="tags">
		        {$oCity->getTagsLink()}
            </ul>

		</div>
	</header>

	<footer class="topic-footer">

		<ul class="topic-info">
			<li class="topic-info-date">{date_format date=$oCity->getDateAdd()}</li>

			<li class="topic-info-comments">
				{if $oCity->getCountFeedback()>0}
	      			<a href="{$oCity->getUrlFull()}/feedbacks/#feedbacks" title="{$aLang.plugin.city.city_feedbacks_read_feedback}"> <i class="icon-synio-comments-green-filled"></i>{$oCity->getCountFeedback()}</a>
	      		{else}
	      			<a href="{$oCity->getUrlFull()}/feedbacks/#feedbacks" title="{$aLang.plugin.city.city_feedbacks_write_feedback}"><i class="icon-synio-comments-blue"></i>{$aLang.plugin.city.city_feedbacks_write_feedback}</a>
	      		{/if}
			</li>

            {if ($oCity->getGeoTarget() && ($oCity->getCountry()|| $oCity->getCity()))}
                <li class="topic-info-author">

	                {if $oCity->getCountry()}
                        <a href="{router page='cityes'}country/{$oCity->getGeoTarget()->getCountryId()}/">{$oCity->getCountry()|escape:'html'}</a>{/if}{if $oCity->getGeoTarget()->getCityId()},{/if}
	                {if $oCity->getCity()}
                        <a href="{router page='cityes'}city/{$oCity->getGeoTarget()->getCityId()}/">{$oCity->getCity()|escape:'html'}</a>{/if}
                </li>
            {/if}

{strip}
            <li class="topic-info-vote">
                <div id="vote_area_city_{$oCity->getId()}" class="vote-topic {if $oCity->getRating() > 0}
																			vote-count-positive
																		{elseif $oCity->getRating() < 0}
																			vote-count-negative
																		{elseif $oCity->getRating() == 0}
																			vote-count-zero
																		{/if} vote-not-self vote-nobuttons">
	                <div class="vote-item vote-count">
						<span id="vote_total_city_{$oCity->getId()}">
							{if $oCity->getRating() > 0}+{/if}{$oCity->getRating()}
                        </span>
                    </div>
                </div>
            </li>
{/strip}
		</ul>
		</footer>
	</article>
	{/foreach}	
	
    {include file='paging.tpl' aPaging=$aPaging}			
</div>
{else}
{$aLang.plugin.city.city_notfound_cityes}
{/if}		
	

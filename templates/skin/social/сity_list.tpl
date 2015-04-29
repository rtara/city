{if count($aCityes)>0}
	{foreach from=$aCityes item=oCity}
	<article class="topic">
	<header class="topic-header"><div class="topic-content text">
		<h2>
            <a href="{$oCity->getUrlFull()}" class="title">{$oCity->getName()|escape:'html'}</a></h2>
  		    <a href="{$oCity->getUrlFull()}"><img src="{$oCity->getLogoPath(48)}" alt="" style='vertical-align:top; margin-right: 5px'/></a>

			{$oCity->getDescription()}
		</div>
	</header>

	<footer class="topic-footer">
		<ul class="tags">
			{$oCity->getTagsLink()}
		</ul>

		<ul class="topic-info">
			<li class="voting {if $oCity->getRating()>0}positive{elseif $oCity->getRating()<0}negative{/if}">
				<span class="total" title="{$aLang.topic_vote_count}: {$oCity->getCountVote()}">{$oCity->getRating()}</span>
			</li>
			<li class="topic-info-date">{date_format date=$oCity->getDateAdd()}</li>

			<li class="topic-info-comments">
				{if $oCity->getCountFeedback()>0}
	      			<a href="{$oCity->getUrlFull()}/feedbacks/#feedbacks" title="{$aLang.plugin.city.city_feedbacks_read_feedback}"><span>{$oCity->getCountFeedback()}</span></a>
	      		{else}
	      			<a href="{$oCity->getUrlFull()}/feedbacks/#feedbacks" title="{$aLang.plugin.city.city_feedbacks_write_feedback}"><span class="red">{$aLang.plugin.city.city_feedbacks_write_feedback}</span></a>
	      		{/if}
			</li>



            {if ($oCity->getCountry()|| $oCity->getCity())}
                <li>
                        {if $oCity->getCountry()}
                            <a href="{router page='city'}country/{$oCity->getCountry()|escape:'html'}/">{$oCity->getCountry()|escape:'html'}</a>
                        {/if}
                        {if $oCity->getCity()}
                            , <a href="{router page='city'}city/{$oCity->getCity()|escape:'html'}/">{$oCity->getCity()|escape:'html'}</a>
                        {/if}
                </li>
            {/if}
		</ul>
		</footer>
	</article>
	{/foreach}	
	
    {include file='paging.tpl' aPaging=$aPaging}			
	
{else}
{$aLang.plugin.city.city_notfound_cityes}
{/if}		
	

<table class="table table-blogs">
{if $bCityesUseOrder}
	<thead>
	<tr>
		<th class="cell-name cell-tab">
			<div class="cell-tab-inner {if $sCityesOrder=='city_name'}active{/if}"><a href="{$sCityesRootPage}?order=city_name&order_way={if $sCityesOrder=='city_name'}{$sCityesOrderWayNext}{else}{$sCityesOrderWay}{/if}" {if $sCityesOrder=='city_name'}class="{$sCityesOrderWay}"{/if}><span>{$aLang.blogs_title}</span></a></div>
		</th>
		<th class="cell-readers cell-tab">
            <div class="cell-tab-inner {if $sCityesOrder=='city_count_favourite'}active{/if}"><a href="{$sCityesRootPage}?order=city_count_favourite&order_way={if $sCityesOrder=='city_count_favourite'}{$sCityesOrderWayNext}{else}{$sCityesOrderWay}{/if}" {if $sCityesOrder=='city_count_favourite'}class="{$sCityesOrderWay}"{/if}><span>{$aLang.plugin.city.city_menu_fans}</span></a></div>
        </th>
		<th class="cell-readers cell-tab">
            <div class="cell-tab-inner {if $sCityesOrder=='city_count_feedback'}active{/if}"><a href="{$sCityesRootPage}?order=city_count_feedback&order_way={if $sCityesOrder=='city_count_feedback'}{$sCityesOrderWayNext}{else}{$sCityesOrderWay}{/if}" {if $sCityesOrder=='city_count_feedback'}class="{$sCityesOrderWay}"{/if}><span>{$aLang.plugin.city.city_cityes_feedbacks}</span></a></div>
        </th>
		<th class="cell-rating cell-tab align-center">
            <div class="cell-tab-inner {if $sCityesOrder=='city_rating'}active{/if}"><a href="{$sCityesRootPage}?order=city_rating&order_way={if $sCityesOrder=='city_rating'}{$sCityesOrderWayNext}{else}{$sCityesOrderWay}{/if}" {if $sCityesOrder=='city_rating'}class="{$sCityesOrderWay}"{/if}><span>{$aLang.plugin.city.city_cityes_rating}</span></a></div>
		</th>
	</tr>
	</thead>
{else}
    <thead>
    <tr>
        <th class="cell-name cell-tab">
            <div class="cell-tab-inner"><span>{$aLang.blogs_title}</span></div>
        </th>
        <th class="cell-readers cell-tab">
            <div class="cell-tab-inner"><span>{$aLang.plugin.city.city_menu_fans}</span></div>
        </th>
        <th class="cell-readers cell-tab">
            <div class="cell-tab-inner"><span>{$aLang.plugin.city.city_cityes_feedbacks}</span></div>
        </th>
        <th class="cell-rating cell-tab align-center">
            <div class="cell-tab-inner active"><span>{$aLang.plugin.city.city_cityes_rating}</span></div>
        </th>
    </tr>
    </thead>
{/if}
	{foreach from=$aCity item=oCity}
		{assign var="oUserOwner" value=$oCity->getOwner()}
		{assign var="oTopicLast" value=$oCity->getTopicLast()}
	<tr>
		<td >

				{if $oConfig->GetValue('module.city.use_activate')}
					{if !$oCity->getActive()}<img src='{cfg name="path.static.skin"}/images/error.png' alt='{$aLang.plugin.city.city_cityes_need_activate}'>{/if}
				{/if}
			<a href="{$oCity->getUrlFull()}/"><img src="{$oCity->getLogoPath(100)}" alt="{$oCity->getName()|escape:'html'}" class="list_logotype"/></a>


                <a href="{$oCity->getUrlFull()}/" class="city_name" >{$oCity->getName()|escape:'html'}</a><br>
	            <span class="city_description">{$oCity->getDescription()|escape:'html'}</span>
	            {if $oTopicLast}
		            <br>{$aLang.plugin.city.city_topic_last}: <a href="{$oTopicLast->getUrl()}">{$oTopicLast->getTitle()}</a>
	            {/if}

		</td>
		<td class="cell-readers align-center"><strong>{$oCity->getCountFavourite()}</strong></td>
		<td class="cell-readers align-center"><strong>{$oCity->getCountFeedback()}</strong></td>
		<td class="cell-rating align-center">{$oCity->getRating()}</td>
	</tr>
	{/foreach}
	</tbody>
</table>
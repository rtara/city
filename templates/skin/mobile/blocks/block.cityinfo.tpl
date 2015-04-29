{if $oCity}
<div class="block">
    <header class="block-header">
        <h3>{$aLang.plugin.city.block_cityinfo}</h3>
    </header>

<span class="note">
{$oCity->getCountSubscribe()} {$oCity->getCountSubscribe()|declension:$aLang.plugin.city.subscribe_count_declension:'russian'}<br>
{$oCity->getCountFeedback()} {$oCity->getCountFeedback()|declension:$aLang.plugin.city.feedback_declension:'russian'}<br>
{$oCity->getBlog()->getCountTopic()} {$oCity->getBlog()->getCountTopic()|declension:$aLang.topic_declension:'russian'}
</span><br>

{if $oUserCurrent}
	{assign var="oTarif" value=$oCity->getTariff()}
{if $oUserCurrent->getId()!=$oCity->getOwnerId()}
	{assign var="oSubscribeCity" value=$oCity->getSubscribeNewTopic()}
    <div class="subscribe">
        <ul>

        <li class="margin-bottom-10"><input type="button" id="subscribeCity" title="{$aLang.plugin.city.subscribe_city_subscribe_button_title}" class="button{if $oSubscribeCity && $oSubscribeCity->getStatus()} hidden{/if}" onclick="ls.subscribe.toggle('city_new_topic','{$oCity->getId()}','',1); return false;" value="{$aLang.plugin.city.subscribe_city_subscribe_button_text}">
        <input type="button" id="unsubscribeCity" title="{$aLang.plugin.city.subscribe_city_unsubscribe_button_title}" class="button{if !$oSubscribeCity or !$oSubscribeCity->getStatus()} hidden{/if}" onclick="ls.subscribe.toggle('city_new_topic','{$oCity->getId()}','',0); return false;" value="{$aLang.plugin.city.subscribe_city_subscribed_button_text}">
        </li>
        <li>
            <input {if $oCity->getUserIsJoin()}checked="checked"{/if} type="checkbox" id="city_join" class="input-checkbox" onchange="ls.city.toggleJoin(this,{$oCity->getBlogId()}); return false;">
            <label for="city_join">{$aLang.plugin.city.city_button_worker}</label>
        </li>
        </ul>
    </div>

{elseif $oUserCurrent->getId()==$oCity->getOwnerId()}

	{$aLang.plugin.city.tariff_title} {$oTarif->getTitle()}<br>
	{if $oTarif->getId() != 0}
        <span class="note"> {$aLang.plugin.city.tariff_active_to} {date_format date=$oCity->getDateTariffEnd() format='j F Y'}</span>
	{/if}
{/if}


	{if $oUserCurrent->isAdministrator()}
    <header class="block-header">
        <h3>{$aLang.plugin.city.tariff_change}</h3>
    </header>
    <form action="" method="POST" id="tariff_form" enctype="multipart/form-data" class="wrapper-content" >
        <input type="hidden" name="security_ls_key" value="{$LIVESTREET_SECURITY_KEY}" />
        <input type="hidden" name="city_id" value="{$oCity->getId()}" />
		{foreach from=$aTariffs item=oTariffFromList}
            <label><input type="radio" name="tariff_id" value="{$oTariffFromList->getId()}"
			              {if $oTariffFromList->getId() == $oTarif->getId()}checked{/if}/> {$oTariffFromList->getTitle()}</label>
		{/foreach}
        <span id="tariff_end_date" class="note">
		{if $oTarif->getId() != 0}
             {$aLang.plugin.city.tariff_active_to} {date_format date=$oCity->getDateTariffEnd() format='j F Y'}
		{/if}
        </span>
        <select name="tariff_period" id="tariff_period" class="input-text input-width-100">
			{foreach from=$oConfig->GetValue('module.city.tariff_periods') item=sPeriod}
                <option value="{$sPeriod}">{$sPeriod} {$sPeriod|declension:$aLang.plugin.city.tariff_period_declension:'russian'}</option>
			{/foreach}
        </select><br><br>
        <button class="button" onclick="ls.city.updateTariff('tariff_form'); return false;">{$aLang.plugin.city.tariff_change_button}</button>
    </form>
	{/if}
{/if}
</div>
{/if}

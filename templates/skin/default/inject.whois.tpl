{if $aCityEmployee}
<li>
	<span class="val">{$aLang.plugin.city.city_is_work}:</span>
	<strong>
		{foreach from=$aCityEmployee item=oCityEmploye name=city_user}
			<a href="{router page='city'}{$oCityEmploye->getCityUrl()}/">{$oCityEmploye->getCityName()|escape:'html'}</a>{if !$smarty.foreach.city_user.last}, {/if}
		{/foreach}
	</strong>
</li>
{/if}

{if $aCityAdmirer}
<li>
	<span>{$aLang.plugin.city.city_is_like}:</span>
	<strong>
		{foreach from=$aCityAdmirer item=oCityAdmirer name=city_user}
			<a href="{router page='city'}{$oCityAdmirer->getCityUrl()}/">{$oCityAdmirer->getCityName()|escape:'html'}</a>{if !$smarty.foreach.city_user.last}, {/if}
		{/foreach}
	</strong>
</li>
{/if}


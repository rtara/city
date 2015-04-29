{if $aCityEmployee}
<tr>
	<td class="cell-label">{$aLang.plugin.city.city_is_work}:</td>
	<td>
		{foreach from=$aCityEmployee item=oCityEmploye name=city_user}
			<a href="{router page='city'}{$oCityEmploye->getCityUrl()}/">{$oCityEmploye->getCityName()|escape:'html'}</a>{if !$smarty.foreach.city_user.last}, {/if}
		{/foreach}
	</td>
</li>
{/if}

{if $aCityAdmirer}
<tr>
	<td class="cell-label">{$aLang.plugin.city.city_is_like}:</td>
	<td>
		{foreach from=$aCityAdmirer item=oCityAdmirer name=city_user}
			<a href="{router page='city'}{$oCityAdmirer->getCityUrl()}/">{$oCityAdmirer->getCityName()|escape:'html'}</a>{if !$smarty.foreach.city_user.last}, {/if}
		{/foreach}
	</td>
</li>
{/if}


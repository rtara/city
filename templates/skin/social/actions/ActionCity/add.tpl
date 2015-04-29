{include file='header.tpl' menu='create'}


<script type="text/javascript">
	jQuery(document).ready(function ($) {
		ls.lang.load({lang_load name="geo_select_city,geo_select_region"});
		ls.geo.initSelect();
		ls.autocomplete.add($(".autocomplete-tags"), aRouter['city']+'ajax/autocompleter/tag/', true);
		$("#city_description").charCount({
			allowed: {$oConfig->GetValue('module.city.description_len')} ,
			warning: 20
		});
	});
</script>


<form action="" method="POST" id="thisform" enctype="multipart/form-data" class="wrapper-content">
	<input type="hidden" name="security_ls_key" value="{$LIVESTREET_SECURITY_KEY}" />
	<label for="city_name">{$aLang.plugin.city.city_add_name}:</label>
	<input type="text" id="city_name" name="city_name" value="{$_aRequest.city_name}" class="input-text input-width-full" /><br />
	<span class="note">{$aLang.plugin.city.city_add_name_note}</span><br />

	{if !$oConfig->GetValue('module.city.use_convert_url')}
		<p>
			<label for="city_url">{$aLang.plugin.city.city_add_url}:</label>
			<span class="note">{router page='city'}</span><input type="text" id="city_url" name="city_url" value="{$_aRequest.city_url}" class="input-text input-width-200" {if $_aRequest.city_id}disabled{/if} /><br />
			<span class="note">{$aLang.plugin.city.city_add_url_note}</span>
		</p>
	{/if}

	<p class="counter-wrapper">
		<label for="city_url">{$aLang.plugin.city.city_add_description}:</label>
		<textarea name="city_description" id="city_description" rows="5"
			 class="input-text input-width-full">{$_aRequest.city_description}</textarea>
		<span class="note">{$aLang.plugin.city.city_add_description_note}</span>
	</p>

{if $oUserCurrent && !$oUserCurrent->isAdministrator() && $oConfig->GetValue('module.city.use_category')}
    <p>
        <label for="category">{$aLang.plugin.city.city_add_tags}:</label>
		{foreach from=$aCategories item=oCategory}
            <label><input {if in_array($oCategory->getText(), $aSelected)}checked{/if} type="checkbox" id="category" name="category[]" value="{$oCategory->getText()}" class="input-checkbox" /> {$oCategory->getText()}</label>
		{/foreach}
    </p>
	{else}
    <p>
        <label for="city_tags">{$aLang.plugin.city.city_add_tags}:</label>
        <input type="text" id="city_tags" name="city_tags" value="{$_aRequest.city_tags|escape:'html'}" class="autocomplete-tags input-text input-width-full" /><br />
        <span class="note">{$aLang.plugin.city.city_add_tags_note}</span>
    </p>
{/if}


   <input type="hidden" name="city_id" id="city_id" value="0" />


	<div class="js-geo-select">
		<label for="" style="margin-bottom: 7px">{$aLang.plugin.city.city_add_place}:</label>

		<p style="margin-bottom: 15px">
			<select class="js-geo-country input-width-200" name="geo_country">
				<option value="">{$aLang.geo_select_country}</option>
			{if $aGeoCountries}
				{foreach from=$aGeoCountries item=oGeoCountry}
					<option value="{$oGeoCountry->getId()}"
					        {if $oGeoTarget and $oGeoTarget->getCountryId()==$oGeoCountry->getId()}selected="selected"{/if}>{$oGeoCountry->getName()}</option>
				{/foreach}
			{/if}
			</select>
		</p>

		<p style="margin-bottom: 15px">
			<select class="js-geo-region input-width-200" name="geo_region"
			        {if !$oGeoTarget or !$oGeoTarget->getCountryId()}style="display:none;"{/if}>
				<option value="">{$aLang.geo_select_region}</option>
			{if $aGeoRegions}
				{foreach from=$aGeoRegions item=oGeoRegion}
					<option value="{$oGeoRegion->getId()}"
					        {if $oGeoTarget and $oGeoTarget->getRegionId()==$oGeoRegion->getId()}selected="selected"{/if}>{$oGeoRegion->getName()}</option>
				{/foreach}
			{/if}
			</select>
		</p>

		<p>
			<select class="js-geo-city input-width-200" name="geo_city"
			        {if !$oGeoTarget or !$oGeoTarget->getRegionId()}style="display:none;"{/if}>
				<option value="">{$aLang.geo_select_city}</option>
			{if $aGeoCities}
				{foreach from=$aGeoCities item=oGeoCity}
					<option value="{$oGeoCity->getId()}"
					        {if $_aRequest['geo_city']==$oGeoCity->getId()}selected="selected"{/if}>{$oGeoCity->getName()}</option>
				{/foreach}
			{/if}
			</select>
		</p>
	</div>
	<br />

	<button type="submit" name="submit_add_city" id="submit_add_city" class="button button-primary">{$aLang.plugin.city.city_add_submit}</button>
</form>


{include file='footer.tpl'}

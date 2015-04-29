<script type="text/javascript">
	jQuery(document).ready(function ($) {
		ls.lang.load({lang_load name="geo_select_city,geo_select_region"});
		ls.geo.initSelect();
		$("#city_contact_info").charCount({
			allowed: 255,
			warning: 20
		});
	});

</script>



	<h2 class="page-header">{$aLang.plugin.city.city_edit_header}: <a href="{$oCity->getUrlFull()}/"
	                                                                        class="blog_headline_group">{$oCity->getName()}</a>
	</h2>
{include file="`$aTemplatePathPlugin['city']`menu.city_edit.tpl"}
	<form action="" method="POST" id="thisform" enctype="multipart/form-data" class="wrapper-content">
		<input type="hidden" name="security_ls_key" value="{$LIVESTREET_SECURITY_KEY}"/>

		<p>
			<label for="city_site">{$aLang.plugin.city.city_edit_site}:</label>
			<input type="text" id="city_site" name="city_site" value="{$oCity->getSite()|escape:'html'}"
			       class="input-text input-width-full"/><br/>
			<span class="note">{$aLang.plugin.city.city_edit_site_note}</span><br/>
		</p>

		<p>
			<label for="city_email">{$aLang.plugin.city.city_edit_email}:</label>
			<input type="text" id="city_email" name="city_email" value="{$oCity->getEmail()|escape:'html'}"
			       class="input-text input-width-full"/><br/>
			<span class="note">{$aLang.plugin.city.city_edit_email_note}</span><br/>
		</p>

		<p>
			<label for="city_phone">{$aLang.plugin.city.city_edit_phone}:</label>
			<input type="text" id="city_phone" name="city_phone" value="{$oCity->getPhone()|escape:'html'}"
			       class="input-text input-width-full"/><br/>
			<span class="note">{$aLang.plugin.city.city_edit_phone_note}</span><br/>
		</p>

		<p>
			<label for="city_fax">{$aLang.plugin.city.city_edit_fax}:</label>
			<input type="text" id="city_fax" name="city_fax" value="{$oCity->getFax()|escape:'html'}"
			       class="input-text input-width-full"/><br/>
			<span class="note">{$aLang.plugin.city.city_edit_fax_note}</span><br/>
		</p>

		<p>
			<label for="city_skype">{$aLang.plugin.city.city_edit_skype}:</label>
			<input type="text" id="city_skype" name="city_skype" value="{$oCity->getSkype()|escape:'html'}"
			       class="input-text input-width-200"/><br/>
		</p>

		<p>
			<label for="city_icq">{$aLang.plugin.city.city_edit_icq}:</label>
			<input type="text" id="city_icq" name="city_icq" value="{$oCity->getIcq()|escape:'html'}"
			       class="input-text input-width-200"/><br/>
		</p>

		<p>
			<label for="city_contact_name">{$aLang.plugin.city.city_edit_contact_name}:</label>
			<input type="text" id="city_contact_name" name="city_contact_name"
			       value="{$oCity->getContactName()|escape:'html'}" class="input-text input-width-full"/><br/>
			<span class="note">{$aLang.plugin.city.city_edit_contact_name_note}</span><br/>
		</p>

			<div>
			<label for="city_contact_info">{$aLang.plugin.city.city_edit_contact_info}:</label>
			<textarea name="city_contact_info" id="city_contact_info" rows="3"
			          class="input-text input-width-full">{$oCity->getContactInfo()|escape:'html'}</textarea>
			</div>


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

			<p style="margin-bottom: 15px">
				<select class="js-geo-city input-width-200" name="geo_city"
				        {if !$oGeoTarget or !$oGeoTarget->getRegionId()}style="display:none;"{/if}>
					<option value="">{$aLang.geo_select_city}</option>
				{if $aGeoCities}
					{foreach from=$aGeoCities item=oGeoCity}
						<option value="{$oGeoCity->getId()}"
						        {if $oGeoTarget and $oGeoTarget->getCityId()==$oGeoCity->getId()}selected="selected"{/if}>{$oGeoCity->getName()}</option>
					{/foreach}
				{/if}
				</select>
			</p>
		</div>

		<input type="hidden" name="city_id" id="city_id" value="{$oCity->getid()}"/>

		<p>

			<label for="city_address">{$aLang.plugin.city.city_edit_address}:</label>
			<input type="text" id="city_address" name="city_address"
			       value="{$oCity->getAddress()|escape:'html'}" class="input-text input-width-full"/><br/>
			<span class="note">{$aLang.plugin.city.city_add_place_note}</span><br/>
		</p>

	{if $oConfig->GetValue('module.city.map.use')}
		{include file="`$aTemplatePathPlugin['city']`inject.map_edit.tpl"}
	{/if}
		<button type="submit" name="submit_edit_city" id="submit_edit_city" class="button button-primary">{$aLang.plugin.city.city_edit_submit}</button>
	</form>

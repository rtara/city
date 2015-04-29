{include file='editor.tpl'}

<script language="javascript" type="text/javascript">
	jQuery(document).ready(function($) {
		ls.autocomplete.add($(".autocomplete-tags"), aRouter['city']+'ajax/autocompleter/tag/', true);

		$("#city_description").charCount({
			allowed: {$oConfig->GetValue('module.city.description_len')} ,
			warning: 20
		});
	} );
</script>

<div class="city">

	<h2 class="page-header">{$aLang.plugin.city.city_edit_header}: <a href="{$oCity->getUrlFull()}/"  class="blog_headline_group">{$oCity->getName()}</a></h2>
	{include file="`$aTemplatePathPlugin['city']`menu.city_edit.tpl"}

	<form action="" method="POST" id="thisform" enctype="multipart/form-data" class="wrapper-content">

		<input type="hidden" name="security_ls_key" value="{$LIVESTREET_SECURITY_KEY}" />
		<label for="city_name">{$aLang.plugin.city.city_add_name}:</label>
		<input type="text" id="city_name" name="city_name" value="{$oCity->getName()|escape:'html'}" class="input-text input-width-full" /><br />
		<span class="note">{$aLang.plugin.city.city_add_name_note}</span><br />

		<p>
			<label for="city_name_legal">{$aLang.plugin.city.city_edit_legalname}:</label>
			<input type="text" id="city_name_legal" name="city_name_legal" value="{$oCity->getLegalName()|escape:'html'}" class="input-text input-width-full" /><br />
			<span class="note">{$aLang.plugin.city.city_edit_legalname_note}</span><br />
		</p>

		<p>
			<label for="city_url">{$aLang.plugin.city.city_add_url}:</label>
			<input type="text" id="city_url" name="city_url" value="{$oCity->getUrl()|escape:'html'}" class="input-text input-width-full" {if $oCity->getId()}disabled{/if} /><br />
			<span class="note">{$aLang.plugin.city.city_add_url_note}</span><br />
		</p>

		<div>
			<label for="city_description">{$aLang.plugin.city.city_add_description}:</label>
			<textarea name="city_description" id="city_description" rows="3"
					   class="input-width-full">{$oCity->getDescription()|escape:'html'}</textarea>
			<span class="note">{$aLang.plugin.city.city_add_description_note}</span><br />
		</div>

        <div>
        <label for="city_about">{$aLang.plugin.city.city_edit_about_title}:</label>
        <textarea name="city_about" id="city_about" rows="5"
                  class="mce-editor markitup-editor input-width-full">{$oCity->getAboutSource()|escape:'html'}</textarea>
        <br />
        </div>
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
                <input type="text" id="city_tags" name="city_tags" value="{$oCity->getTags()|escape:'html'}" class="autocomplete-tags input-text input-width-full" /><br />
                <span class="note">{$aLang.plugin.city.city_add_tags_note}</span>
            </p>
		{/if}
		<p>
			<span class="form">{$aLang.plugin.city.city_edit_datebasis}: </span><br />
			<select name="city_basis_day" class="w70">
				<option value="">{$aLang.date_day}</option>
			{section name=date_day start=1 loop=32 step=1}
				<option value="{$smarty.section.date_day.index}" {if $smarty.section.date_day.index==$oCity->getDateBasis()|date_format:"%d"}selected{/if}>{$smarty.section.date_day.index}</option>
			{/section}
			</select>
			<select name="city_basis_month" class="w100">
				<option value="">{$aLang.date_month}</option>
			{section name=date_month start=1 loop=13 step=1}
				<option value="{$smarty.section.date_month.index}" {if $smarty.section.date_month.index==$oCity->getDateBasis()|date_format:"%m"}selected{/if}>{$aLang.month_array[$smarty.section.date_month.index][0]}</option>
			{/section}
			</select>

			<select name="city_basis_year" class="w70">
				<option value="">{$aLang.date_year}</option>
			{section name=date_year start=1910 loop={$smarty.now|date_format:"%Y"}+1 step=1}
				<option value="{$smarty.section.date_year.index}" {if $smarty.section.date_year.index==$oCity->getDateBasis()|date_format:"%Y"}selected{/if}>{$smarty.section.date_year.index}</option>
			{/section}
			</select><br />
			<span class="note">{$aLang.plugin.city.city_edit_datebasis_note}</span><br />

		</p>
		<p>
			<label for="city_count_workers">{$aLang.plugin.city.city_edit_countworkers}:</label><br />
			<select id="city_count_workers" name="city_count_workers" class="w100">
				<option value="0">{$aLang.plugin.city.city_info_count_workers_unknown}</option>
				<option value="1" {if 1==$oCity->getCountWorkers()}selected{/if}>{$aLang.plugin.city.city_info_count_workers_1}</option>
				<option value="10" {if 10==$oCity->getCountWorkers()}selected{/if}>{$aLang.plugin.city.city_info_count_workers_10}</option>
				<option value="30" {if 30==$oCity->getCountWorkers()}selected{/if}>{$aLang.plugin.city.city_info_count_workers_30}</option>
				<option value="50" {if 50==$oCity->getCountWorkers()}selected{/if}>{$aLang.plugin.city.city_info_count_workers_50}</option>
				<option value="100" {if 100==$oCity->getCountWorkers()}selected{/if}>{$aLang.plugin.city.city_info_count_workers_100}</option>
				<option value="200" {if 200==$oCity->getCountWorkers()}selected{/if}>{$aLang.plugin.city.city_info_count_workers_200}</option>
				<option value="500" {if 500==$oCity->getCountWorkers()}selected{/if}>{$aLang.plugin.city.city_info_count_workers_500}</option>
				<option value="1000" {if 1000==$oCity->getCountWorkers()}selected{/if}>{$aLang.plugin.city.city_info_count_workers_1000}</option>
				<option value="9999" {if 9999==$oCity->getCountWorkers()}selected{/if}>{$aLang.plugin.city.city_info_count_workers_more}</option>
			</select><br />
			<span class="note">{$aLang.plugin.city.city_edit_countworkers_note}</span><br />
		</p>

		<h4>{$aLang.plugin.city.city_info_staff}</h4>
	{assign var="form_id" value="1"}
		<div id="staff-forms-holder" class="staff-forms-holder">
			<div class="row">
				<div class="th name">{$aLang.plugin.city.city_edit_staff_fio}</div>
				<div class="th position">{$aLang.plugin.city.city_edit_staff_pos}</div>
				<div class="th delete"></div>
			</div>
		{foreach from=$aStaff item=oStaff}
			<div class="row staff-form" id="staff-form-{$form_id}">
				<div class="td name">
					<input type="text" value="{$oStaff->getStaffName()}" name="staff[{$form_id}][staff_name]" class="input-text input-width-100">
				</div>
				<div class="td position">
					<input type="text" value="{$oStaff->getStaffPosition()}" name="staff[{$form_id}][staff_position]" class="input-text input-width-100">
				</div>
				<div class="td delete"><a href="#" onclick="return ls.city.closeStaff({$form_id});" class="dotted">{$aLang.plugin.city.city_edit_staff_delete}</a>
					<input type="hidden" name="staff[{$form_id}][city_id]" value="{$oCity->getId()}" />
				</div>
			</div>
			{assign var="form_id" value="`$form_id+1`"}
		{/foreach}
		</div>
		<a href="#" class="add-staff-form dotted" onclick="return ls.city.addStaff();">{$aLang.plugin.city.city_edit_staff_add}</a><br /><br />
	{if !$oConfig->GetValue('module.city.use_jobs')}
		<label for="city_vacancies">{$aLang.plugin.city.city_edit_vacancy}:</label>
		<textarea name="city_vacancies" id="city_vacancies" rows="20" class="mce-editor markitup-editor input-width-full">{$oCity->getVacancies()|escape:'html'}</textarea>
		{if !$oConfig->GetValue('view.tinymce')}
		{include file='tags_help.tpl' sTagsTargetId="city_vacancies"}
			<br />
			<br />
		{/if}
	{/if}
		<p>
            <span class="form">{$aLang.plugin.city.city_edit_logo}:</span><br>
		{if $oCity->getLogo()}
			{foreach from=$oConfig->GetValue('module.city.logo_size') item=iSize}
				{if $iSize}<img src="{$oCity->getLogoPath({$iSize})}">{/if}
			{/foreach}
			<label for="logo_delete">
			<input type="checkbox" id="logo_delete" name="logo_delete" value="on" class="input-checkbox">


			{$aLang.blog_create_avatar_delete}</label>
		{/if}
            <input type="file" name="logo" ><br><br>
		</p>
		<p>
			<span class="form">{$aLang.plugin.city.city_edit_file}:</span><br>

		{if $oCity->getFileName()}
			<a href="{$oCity->getFilePath()}" title="{$aLang.plugin.city.city_info_file}">{$oCity->getFileName()}</a>
			<label for="file_delete">
			<input type="checkbox" id="file_delete" name="file_delete" value="on" class="input-checkbox">{$aLang.blog_create_avatar_delete}</label>
		{/if}
            <input type="file" name="city_file"><br>
		</p>

		<button type="submit" name="submit_edit_city" id="submit_edit_city" class="button button-primary fl-r">{$aLang.plugin.city.city_edit_submit}</button>
		<br>
	</form>
	<div class="row staff-form hidden" id="staff-form-form_id">
		<fieldset class="staff">
			<div class="td name"><input type="text" value="" name="staff[form_id][staff_name]" class="input-text input-width-100"></div>
			<div class="td position"><input type="text" value="" name="staff[form_id][staff_position]" class="input-text input-width-100"></div>
			<div class="td delete">
				<input type="hidden" name="staff[form_id][city_id]" value="{$oCity->getId()}" />
				<a class="dotted" href="#" onclick="return ls.city.closeStaff('form_id');">{$aLang.plugin.city.city_edit_staff_delete}</a>
			</div>
		</fieldset>
	</div>
</div>
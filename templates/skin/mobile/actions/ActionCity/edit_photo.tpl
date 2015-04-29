{include file='header.tpl' menu="main"}

<h2 class="page-header">{$aLang.plugin.city.city_edit_header}: <a href="{$oCity->getUrlFull()}/"  class="blog_headline_group">{$oCity->getName()}</a></h2>
{include file="`$aTemplatePathPlugin['city']`menu.city_edit.tpl"}


<script type="text/javascript">
    jQuery(function($){
        if (jQuery.browser.flash) {
            ls.cityphoto.initSwfUpload({
                post_params: { 'city_id': {json var=$_aRequest.city_id} }
            });
        }
    });
</script>
<form id="photoset-upload-form" method="POST" enctype="multipart/form-data" onsubmit="return false;" class="modal modal-image-upload">
    <header class="modal-header">
        <h3>{$aLang.uploadimg}</h3>
        <a href="#" class="close jqmClose"></a>
    </header>

    <div id="city-photo-upload-input" class="city-photo-upload-input modal-content">
        <label for="photo-upload-file">{$aLang.plugin.city.city_photo_choose_image}:</label>
        <input type="file" id="photo-upload-file" name="Filedata" /><br><br>

        <button type="submit" class="button button-primary" onclick="ls.cityphoto.upload();">{$aLang.plugin.city.city_photo_upload_choose}</button>
        <button type="submit" class="button" onclick="ls.cityphoto.closeForm();">{$aLang.plugin.city.city_photo_upload_close}</button>

        <input type="hidden" name="is_iframe" value="true" />
        <input type="hidden" name="city_id" value="{$_aRequest.city_id}" />
    </div>
</form>

<form action="" method="POST" enctype="multipart/form-data" id="form-topic-add" class="wrapper-content">
    <input type="hidden" name="security_ls_key" value="{$LIVESTREET_SECURITY_KEY}" />
    <div class="city-photo-upload">
        <h2>{$aLang.plugin.city.city_photo_upload_title}</h2>

        <div class="city-photo-upload-rules">
		{$aLang.plugin.city.city_photo_upload_rules|ls_lang:"SIZE%%`$oConfig->get('module.city.photo.photo_max_size')`":"COUNT%%`$oConfig->get('module.city.photo.count_photos_max')`"}
        </div>

        <input type="hidden" name="main_photo" id="main_photo" value="{$_aRequest.main_photo}" />

        <ul id="swfu_images">
		{if count($aPhotos)}
			{foreach from=$aPhotos item=oPhoto}
				{if $_aRequest.main_photo && $_aRequest.main_photo == $oPhoto->getId()}
					{assign var=bIsMainPhoto value=true}
				{/if}

                <li id="photo_{$oPhoto->getId()}" {if $bIsMainPhoto}class="marked-as-preview"{/if}>
                    <img src="{$oPhoto->getWebPath('100crop')}" alt="image" />
                    <textarea onBlur="ls.cityphoto.setPreviewDescription({$oPhoto->getId()}, this.value)">{$oPhoto->getDescription()}</textarea><br />
                    <a href="javascript:ls.cityphoto.deletePhoto({$oPhoto->getId()})" class="image-delete">{$aLang.plugin.city.city_photo_photo_delete}</a>
						<span id="photo_preview_state_{$oPhoto->getId()}" class="photo-preview-state">
							{if $bIsMainPhoto}
								{$aLang.plugin.city.city_photo_is_preview}
								{else}
                                <a href="javascript:ls.cityphoto.setPreview({$oPhoto->getId()})" class="mark-as-preview">{$aLang.plugin.city.city_photo_mark_as_preview}</a>
							{/if}
                        </span>
                </li>

				{assign var=bIsMainPhoto value=false}
			{/foreach}
		{/if}
        </ul>

        <a href="javascript:ls.cityphoto.showForm()" id="photo-start-upload">{$aLang.plugin.city.city_photo_upload_choose}</a>
    </div>


    <button type="submit" name="submit_edit_city" id="submit_edit_city" class="button button-primary fl-r">{$aLang.plugin.city.city_edit_submit}</button>
    <br>
</form>


{include file='footer.tpl'}


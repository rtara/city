{include file='header.tpl' menu="main"}
<script type='text/javascript' src="{$aTemplateWebPathPlugin['city']}libs/colorpicker/jquery.minicolors.js"></script>
<link rel='stylesheet' type='text/css' href="{$aTemplateWebPathPlugin['city']}libs/colorpicker/jquery.minicolors.css" />
<script type="text/javascript">
    jQuery(document).ready(function($){
    $('INPUT.minicolors').minicolors({
        animationSpeed: 100,
        animationEasing: 'swing',
        change: null,
        control: 'hue',
        defaultValue: '#fbfcfc',
        inline: false,
        letterCase: 'lowercase',
        opacity: false,
        position: 'default',
        swatchPosition: 'left',
        textfield: true,
        theme: 'default'
    });
    });
</script>

<h2 class="page-header">{$aLang.plugin.city.city_edit_header}: <a href="{$oCity->getUrlFull()}/"  class="blog_headline_group">{$oCity->getName()}</a></h2>
{include file="`$aTemplatePathPlugin['city']`menu.city_edit.tpl"}

<form action="" method="POST" id="thisform" enctype="multipart/form-data" class="wrapper-content"  style="display: inline-block; width: 95%">
    <div >
        <input type="hidden" name="security_ls_key" value="{$LIVESTREET_SECURITY_KEY}"/>

        <div class="fl-l" style="width: 49%">
            <label><input type="radio" name="use_branding" value="0"
			              {if !$oCity->getUseBrandImage()}checked{/if}/>{$aLang.plugin.city.city_edit_without_background}</label>
        </div>
        <div class="fl-l" style="width: 49%">
            <label for="background">
	            <input type="radio" name="use_branding" value="1"  {if $oCity->getUseBrandImage()}checked{/if}/>{$aLang.plugin.city.city_edit_background}:</label>
		{if $oCity->getBrandImage()}<img src="{$oCity->getBrandImagePreview()}" height="100px">{/if}<br>
            <input id="background" type="file" name="background"><br><br>
            <label for="bg-color">{$aLang.plugin.city.city_edit_background_color}:</label>
            <input id="bg-color" name="bg_color" value="{$oCity->getBackgroundColor()}" class="minicolors minicolors-input">
        </div>
    </div>
    <button type="submit" name="submit_edit_city" id="submit_edit_city"
            class="button button-primary fl-r">{$aLang.plugin.city.city_edit_submit}</button>
    <br>
</form>

{include file='footer.tpl'}


<script type="text/javascript" src="{cfg name='path.root.engine_lib'}/external/prettyPhoto/js/prettyPhoto.js"></script>
<link rel='stylesheet' type='text/css' href="{cfg name='path.root.engine_lib'}/external/prettyPhoto/css/prettyPhoto.css" />
<script type="text/javascript">
    jQuery(document).ready(function($) {
        $('.photoset-image').prettyPhoto({
            social_tools:'',
            show_title: false,
            slideshow:false,
            deeplinking: false
        });
    });
</script>

{assign var=oMainPhoto value=$oCity->getMainPhoto()}

<div class="city-images">
    <h2>{$aLang.plugin.city.city_photo_header} ({$oCity->getPhotoCount()} {$oCity->getPhotoCount()|declension:$aLang.plugin.city.city_photo_count_images})</h2>

<div class="fl-l" style="padding-right: 10px">
{if $oMainPhoto}
    <a class="photoset-image" href="{$oMainPhoto->getWebPath(1000)}" rel="[photoset]" title="{$oMainPhoto->getDescription()}"><img src="{$oMainPhoto->getWebPath(300)}" title="{$oMainPhoto->getDescription()}" id="photoset-main-image-{$oCity->getId()}" /></a>
{/if}
</div>
    <ul id="city-images">
	{assign var=aPhotos value=$oCity->getPhotos(0, $oConfig->get('module.city.photo.per_page'))}
	{if count($aPhotos)}
		{foreach from=$aPhotos item=oPhoto}
			{if !$oMainPhoto or $oMainPhoto->getId() != $oPhoto->getId() }
            <li><a class="photoset-image" href="{$oPhoto->getWebPath(1000)}" rel="[photoset]"  title="{$oPhoto->getDescription()}"><img src="{$oPhoto->getWebPath('50crop')}" alt="{$oPhoto->getDescription()}" /></a></li>
			{assign var=iLastPhotoId value=$oPhoto->getId()}
			{/if}
		{/foreach}
	{/if}
        <script type="text/javascript">
            ls.cityphoto.idLast='{$iLastPhotoId}';
        </script>
    </ul>


{if count($aPhotos)<$oCity->getPhotoCount()}
    <a href="javascript:ls.cityphoto.getMore({$oCity->getId()})" id="city-photo-more" class="city-photo-more">{$aLang.plugin.city.city_photo_show_more} &darr;</a>
{/if}
</div>
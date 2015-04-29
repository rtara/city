{if $oCity->IsAllowTariff('widgets') && ($oCity->getWidgetVisible('twitter') || $oCity->getWidgetVisible('vk') || $oCity->getWidgetVisible('fb'))}

<div class="block">
{if $oCity->getWidgetVisible('twitter') && $oCity->getTwitterScreenName()}
    <a href="https://twitter.com/{$oCity->getTwitterScreenName()}" class="twitter-follow-button" data-show-count="true" data-lang="ru" >@{$oCity->getTwitterScreenName()}</a>
    <script>!function(d,s,id){ var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){ js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>
    <br>
{/if}
{if $oCity->getWidgetVisible('vk') && $oCity->getVkId()}
    <script type="text/javascript" src="//vk.com/js/api/openapi.js?78"></script>
    <!-- VK Widget -->
    <div id="vk_groups"></div>
    <script type="text/javascript">
        VK.Widgets.Group("vk_groups", { mode: 0, width: "240", height: "290"}, {$oCity->getVkId()});
    </script>
	<br>
{/if}
{if $oCity->getWidgetVisible('fb') && $oCity->getFbUrl()}
    <iframe src="http://www.facebook.com/plugins/likebox.php?href={$oCity->getFbUrl()}&width=240&height=315&colorscheme=light&show_faces=true&stream=false&header=false" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:240px; height:315px;" allowTransparency="true"></iframe>
{/if}
</div>
{/if}
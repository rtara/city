{include file='header.tpl' menu="main"}

<h2 class="page-header">{$aLang.plugin.city.city_edit_header}: <a href="{$oCity->getUrlFull()}/"  class="blog_headline_group">{$oCity->getName()}</a></h2>
{include file="`$aTemplatePathPlugin['city']`menu.city_edit.tpl"}

<form action="" method="POST" id="thisform" enctype="multipart/form-data" >
    <input type="hidden" name="security_ls_key" value="{$LIVESTREET_SECURITY_KEY}" />
    <input type="hidden" name="city_id" id="city_id" value="{$oCity->getId()}" />
    <table class="table table-blogs">
        <thead>
        <tr >
            <th class="cell-name cell-tab"><div class="cell-tab-inner">{$aLang.plugin.city.city_widget_name}</div></th>
            <th width="10%"  class="cell-name cell-tab align-center"><div class="cell-tab-inner align-center">{$aLang.plugin.city.city_widget_use}</div></th>
        </tr>
        </thead>
        {if $oConfig->GetValue('module.city.twitter.consumer_secret') != ''}
        <tr>
            <td class="username"><span><a href="#" class="link-dotted" onclick="ls.city.toggleWidget('twitter'); return false;">{$aLang.plugin.city.widget_twitter_name}</a></span>
            <div id="widget-twitter" style="display: none">
                <label for="screen_name">{$aLang.plugin.city.widget_twitter_edit_screen_name}:</label>
                <input type="text" id="screen_name" name="screen_name" value="{$_aRequest.screen_name}" class="input-text input-width-200" />
	            <button class="button " onclick="ls.city.updateTwitter($('#screen_name').val()); return false;">{$aLang.plugin.city.widget_submit}</button>
	            <br>
                <span class="note">{$aLang.plugin.city.widget_twitter_edit_screen_name_note}</span><br>
            </div>

            </td>
            <td class="align-center" align="center"><input type="checkbox" name="visible[]" id='visible-twitter' onClick="ls.city.switchWidgetVisible('twitter');" {if $oCity->getWidgetVisible('twitter')}checked{/if}/></td>
        </tr>
        {elseif $oUserCurrent && $oUserCurrent->isAdministrator()}
        <tr><td>Twitter. Чтобы можно было задавать виджет Твитера заполните настройки в Конфиге</td></tr>
        {/if}
        <tr>
            <td class="username"><span><a href="#" class="link-dotted" onclick="ls.city.toggleWidget('vk'); return false;">{$aLang.plugin.city.widget_vk_name}</a></span>
                <div id="widget-vk" style="display: none">
                    <label for="vk-url">{$aLang.plugin.city.widget_vk_edit_url}:</label>
                    <input type="text" id="vk-url" name="vk_url" value="{$_aRequest.vk_url}" class="input-text input-width-200" />
                    <button class="button " onclick="ls.city.updateVk($('#vk-url').val()); return false;">{$aLang.plugin.city.widget_submit}</button>
                    <br>
                    <span class="note">{$aLang.plugin.city.widget_vk_edit_url_note}</span><br />
                </div>

            </td>
            <td class="align-center" align="center"><input type="checkbox" name="visible[]" id='visible-vk' onClick="ls.city.switchWidgetVisible('vk');" {if $oCity->getWidgetVisible('vk')}checked{/if}/></td>
        </tr>

        <tr>
            <td class="username"><span><a href="#" class="link-dotted" onclick="ls.city.toggleWidget('facebook'); return false;">{$aLang.plugin.city.widget_facebook_name}</a></span>
                <div id="widget-facebook" style="display: none">
                    <label for="facebook-url">{$aLang.plugin.city.widget_facebook_edit_url}:</label>
                    <input type="text" id="facebook-url" name="facebook_url" value="{$_aRequest.facebook_url}" class="input-text input-width-200" />
                    <button class="button " onclick="ls.city.updateFb($('#facebook-url').val()); return false;">{$aLang.plugin.city.widget_submit}</button>
                    <br>
                    <span class="note">{$aLang.plugin.city.widget_facebook_edit_url_note}</span><br />
                </div>

            </td>
            <td class="align-center" align="center"><input type="checkbox" name="visible[]" id='visible-fb' onClick="ls.city.switchWidgetVisible('fb');" {if $oCity->getWidgetVisible('fb')}checked{/if}/></td>
        </tr>

    </table>
    <br>
</form>

{include file='footer.tpl'}


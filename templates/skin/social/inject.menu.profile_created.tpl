<li {if $sMenuSubItemSelect=='feedbacks'}class="active"{/if}>
    <a href="{$oUserProfile->getUserWebPath()}created/feedbacks/">{$aLang.plugin.city.city_pub_feedback}  {if $iCountFeedbackUser}({$iCountFeedbackUser}){/if}</a>
</li>
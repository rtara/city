<li {if $sMenuSubItemSelect=='city'}class="active"{/if}>
    <a href="{$oUserProfile->getUserWebPath()}favourites/city/">{$aLang.plugin.city.user_menu_profile_favourites_city}  {if $iCountCityFavourite} ({$iCountCityFavourite}) {/if}</a>
</li>
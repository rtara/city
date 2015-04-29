
<ul class="nav nav-pills">
    <li {if $sMenuItemSelect=='profile'}class="active"{/if}>
        <a href="{router page='city'}edit/{$oCity->getId()}/profile/">{$aLang.plugin.city.city_menu_profile}</a>
    </li>
    <li {if $sMenuItemSelect=='contacts'}class="active"{/if}>
        <a href="{router page='city'}edit/{$oCity->getId()}/contacts/">{$aLang.plugin.city.city_menu_contacts}</a>
    </li>
    <li {if $sMenuItemSelect=='admin'}class="active"{/if}>
        <a href="{router page='city'}edit/{$oCity->getId()}/admin/">{$aLang.plugin.city.city_menu_users}</a>
    </li>
{if $oCity->IsAllowTariff('photo')}
    <li {if $sMenuItemSelect=='photo'}class="active"{/if}>
        <a href="{router page='city'}edit/{$oCity->getId()}/photo/">{$aLang.plugin.city.city_menu_photo}</a>
    </li>
{/if}
{if $oCity->IsAllowTariff('branding')}
    <li {if $sMenuItemSelect=='branding'}class="active"{/if}>
        <a href="{router page='city'}edit/{$oCity->getId()}/branding/">{$aLang.plugin.city.city_menu_branding}</a>
    </li>
{/if}
{if $oCity->IsAllowTariff('widgets')}
    <li {if $sMenuItemSelect=='widgets'}class="active"{/if}>
        <a href="{router page='city'}edit/{$oCity->getId()}/widgets/">{$aLang.plugin.city.city_menu_widgets}</a>
    </li>
{/if}
</ul>
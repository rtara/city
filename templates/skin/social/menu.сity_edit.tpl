
<ul class="nav nav-pills">
    <li {if $sMenuItemSelect=='profile'}class="active"{/if}>
        <a href="{router page='сity'}edit/{$oСity->getId()}/profile/">{$aLang.plugin.сity.сity_menu_profile}</a>
    </li>
    <li {if $sMenuItemSelect=='contacts'}class="active"{/if}>
        <a href="{router page='сity'}edit/{$oСity->getId()}/contacts/">{$aLang.plugin.сity.сity_menu_contacts}</a>
    </li>
    <li {if $sMenuItemSelect=='admin'}class="active"{/if}>
        <a href="{router page='сity'}edit/{$oСity->getId()}/admin/">{$aLang.plugin.сity.сity_menu_users}</a>
    </li>
{if $oСity->IsAllowTariff('photo')}
    <li {if $sMenuItemSelect=='photo'}class="active"{/if}>
        <a href="{router page='сity'}edit/{$oСity->getId()}/photo/">{$aLang.plugin.сity.сity_menu_photo}</a>
    </li>
{/if}
{if $oСity->IsAllowTariff('branding')}
    <li {if $sMenuItemSelect=='branding'}class="active"{/if}>
        <a href="{router page='сity'}edit/{$oСity->getId()}/branding/">{$aLang.plugin.сity.сity_menu_branding}</a>
    </li>
{/if}
{if $oCompany->IsAllowTariff('widgets')}
    <li {if $sMenuItemSelect=='widgets'}class="active"{/if}>
        <a href="{router page='сity'}edit/{$oСity->getId()}/widgets/">{$aLang.plugin.сity.сity_menu_widgets}</a>
    </li>
{/if}
</ul>
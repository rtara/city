{include file='header.tpl' showWhiteBack=true menu='main'}
<h2 class="page-header">{$aLang.plugin.city.city_settings_header}</h2>
<div class="topic-content text">


<h5>{$aLang.plugin.city.city_settings_header_new_versions}</h5>
<ul>
    <li><a href="{router page='city'}settings/convert/">{$aLang.plugin.city.city_settings_convert}</a></li>
	<small class="note"> {$aLang.plugin.city.city_settings_convert_note}</small></li>
    <li><a href="{router page='city'}settings/update/0518/">{$aLang.plugin.city.city_settings_update_plugin_0518}</a></li>
	<small class="note"> {$aLang.plugin.city.city_settings_update_plugin_0518_note}</small></li>
    <li><a href="{router page='city'}settings/update/10109/">{$aLang.plugin.city.city_settings_update_plugin_10109}</a></li>
    <small class="note"> {$aLang.plugin.city.city_settings_update_plugin_10109_note}</small></li>
    <li><a href="{router page='city'}settings/update/10110/">{$aLang.plugin.city.city_settings_update_plugin_10110}</a></li>
    <small class="note"> {$aLang.plugin.city.city_settings_update_plugin_10110_note}</small></li>
	<li><a href="{router page='city'}settings/convertgeo/">{$aLang.plugin.city.city_settings_convert_geo}</a></li>
	<small class="note"> {$aLang.plugin.city.city_settings_convert_geo_note}</small></li>

</ul>
</p>
<p>
<h5>{$aLang.plugin.city.city_settings_header_tools}</h5>
<p>
<ul>
	<li><a href="{router page='city'}settings/repair/">{$aLang.plugin.city.city_settings_repair_url}</a>
	<small class="note"> {$aLang.plugin.city.city_settings_repair_url_note}</small></li>
</ul>
</p>
</div>
{include file='footer.tpl'}
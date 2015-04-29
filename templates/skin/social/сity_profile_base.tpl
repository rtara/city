<h2 class="header-table">{$aLang.plugin.city.city_info_description}</h2>
{$oCity->getAbout()}
<div class="city-profile-dotted-list">
	{if $oCity->getTags()}
		<dl>
			<dt>{$aLang.plugin.city.city_info_tags}:</dt>
			<dd>{$oCity->getTagsLink()}</dd>
		</dl>
	{/if}
	{if $oCity->getCountWorkers()!=0}
		<dl>
			<dt>{$aLang.plugin.city.city_info_countworkers}:</dt>
			<dd>
				{if 0==$oCity->getCountWorkers()}{$aLang.plugin.city.city_info_count_workers_unknown}{/if}
				{if 1==$oCity->getCountWorkers()}{$aLang.plugin.city.city_info_count_workers_1}{/if}
				{if 10==$oCity->getCountWorkers()}{$aLang.plugin.city.city_info_count_workers_10}{/if}
				{if 30==$oCity->getCountWorkers()}{$aLang.plugin.city.city_info_count_workers_30}{/if}
				{if 50==$oCity->getCountWorkers()}{$aLang.plugin.city.city_info_count_workers_50}{/if}
				{if 100==$oCity->getCountWorkers()}{$aLang.plugin.city.city_info_count_workers_100}{/if}
				{if 200==$oCity->getCountWorkers()}{$aLang.plugin.city.city_info_count_workers_200}{/if}
				{if 500==$oCity->getCountWorkers()}{$aLang.plugin.city.city_info_count_workers_500}{/if}
				{if 1000==$oCity->getCountWorkers()}{$aLang.plugin.city.city_info_count_workers_1000}{/if}
				{if 9999==$oCity->getCountWorkers()}{$aLang.plugin.city.city_info_count_workers_more}{/if}
			</dd>
		</dl>
	{/if}
	{if $oCity->getDateBasis()}
		<dl>
			<dt>{$aLang.plugin.city.city_info_datebasis}:</dt>
			<dd>{date_format date=$oCity->getDateBasis() format="j F Y"}</dd>
		</dl>
	{/if}
		<dl>
			<dt>{$aLang.plugin.city.city_info_date_add}:</dt>
			<dd>{date_format date=$oCity->getDateAdd() format="j F Y"}</dd>
		</dl>
		<dl>
			<dt>{$aLang.plugin.city.city_info_owner}:</dt>
			<dd>{assign var="oUser" value=$oCity->getOwner()}
				<div class="user-avatar">
					<a href="{$oUser->getUserWebPath()}"><img src="{$oUser->getProfileAvatarPath(24)}" alt="avatar" /></a>
					<a href="{$oUser->getUserWebPath()}">{$oUser->getLogin()}</a>
				</div>
			</dd>
		</dl>
		<dl>
			<dt>{$aLang.plugin.city.city_info_employes} ({count($aCityEmpl)}):</dt>
			<dd>{if $aCityEmpl}

				{foreach from=$aCityEmpl item=oBlogUser}
					{assign var="oUser" value=$oBlogUser->getUser()}
					<div class="user-avatar">
						<a href="{$oUser->getUserWebPath()}"><img src="{$oUser->getProfileAvatarPath(24)}" alt="avatar" /></a>
						<a href="{$oUser->getUserWebPath()}">{$oUser->getLogin()}</a>
					</div>
				{/foreach}

				{else}
				{$aLang.plugin.city.city_info_employes_empty}
			{/if}
			</dd>
		</dl>
	{if $oCity->getFileName()}
		<dl>
			<dt>{$aLang.plugin.city.city_info_file}:</dt>
			<dd>
				<a href="{$oCity->getFilePath()}" >{$oCity->getFileName()}</a>
			</dd>
		</dl>
	{/if}
	{if $aStaff}
		<dl>
			<dt>{$aLang.plugin.city.city_info_staff}:</dt>
			<dd>
				{if $aStaff}
					{foreach from=$aStaff item=oStaff}
						<div>{$oStaff->getStaffName()} <span class="staff-position">({$oStaff->getStaffPosition()})</span></div>
					{/foreach}
				{/if}
			</dd>
		</dl>
	{/if}
</div>




<h2 class="header-table">{$aLang.plugin.city.city_menu_contacts}</h2>

<div class="city-profile-dotted-list">
	{if $oCity->getSite()}
		<dl>
			<dt>{$aLang.plugin.city.city_info_site}:</dt>
			<dd><noindex><a href="{$oCity->getSite(true)|escape:'html'}" rel="nofollow" target="_blank">{$oCity->getSite()}</a></noindex> </dd>
		</dl>
	{/if}
	{if $oCity->getEmail() and $oUserCurrent}
		<dl>
			<dt>{$aLang.plugin.city.city_info_email}:</dt>
			<dd>{if $oUserCurrent}{$oCity->getEmail()|escape:'html'}{else}{$aLang.plugin.city.city_email_hide}{/if}</dd>
		</dl>
	{/if}
	{if $oCity->getPhone()}
		<dl>
			<dt>{$aLang.plugin.city.city_info_phone}:</dt>
			<dd>{$oCity->getPhone()|escape:'html'}</dd>
		</dl>
	{/if}
	{if $oCity->getFax()}
		<dl>
			<dt>{$aLang.plugin.city.city_info_fax}:</dt>
			<dd>{$oCity->getFax()|escape:'html'}</dd>
		</dl>
	{/if}
	{if $oCity->getSkype()}
		<dl>
			<dt>{$aLang.plugin.city.city_info_skype}:</dt>
			<dd>{$oCity->getSkypeLink()}</dd>
		</dl>
	{/if}
	{if $oCity->getIcq()}
		<dl>
			<dt>{$aLang.plugin.city.city_info_icq}:</dt>
			<dd>{$oCity->getIcq()|escape:'html'}</dd>
		</dl>
	{/if}
	{if $oCity->getContactName()}
		<dl>
			<dt>{$aLang.plugin.city.city_info_contact_name}:</dt>
			<dd>{$oCity->getContactName()|escape:'html'}</dd>
		</dl>
	{/if}
	{if $oCity->getContactInfo()}
		<dl>
			<dt>{$aLang.plugin.city.city_info_contact_info}:</dt>
			<dd>{$oCity->getContactInfo()|nl2br}</dd>
		</dl>
	{/if}
	{if ($oCity->getGeoTarget())}
		<dl>
			<dt>
				{$aLang.plugin.city.city_info_place}:
			</dt>
			<dd>
				{if $oCity->getCountry()}
					<a href="{router page='cityes'}country/{$oCity->getGeoTarget()->getCountryId()}/">{$oCity->getCountry()|escape:'html'}</a>{/if}{if $oCity->getGeoTarget()->getCityId()},{/if}
				{if $oCity->getCity()}
					 <a href="{router page='cityes'}city/{$oCity->getGeoTarget()->getCityId()}/">{$oCity->getCity()|escape:'html'}</a>{/if}{if $oCity->getAddress()},{/if}
				{if $oCity->getAddress()} {$oCity->getAddress()|escape:'html'}{/if}
				{if ($oCity->getLongitude() != '')}<br/>
					<a href="#" class="dotted" id="toggle" onclick="return true;">{$aLang.plugin.city.city_info_show_map}</a>
				{/if}
			</dd>
			<div id="YMapsCity" class="map hidden"></div>
		</dl>
	{/if}
</div>


{if $oConfig->GetValue('module.city.map.use')}
	{include file="`$aTemplatePathPlugin['city']`inject.map.tpl"}
{/if}

{if $oCity->IsAllowTariff('photo') && $oCity->getPhotoCount()}
	{include file="`$aTemplatePathPlugin['city']`inject.photos.tpl"}
{/if}

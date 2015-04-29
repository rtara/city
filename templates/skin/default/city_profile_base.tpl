
<div class="wrapper">
	<div >
		<h2 class="header-table">{$aLang.plugin.city.city_info_description}</h2>
		<ul class="city-profile-dotted-list">
		{if $oCity->getAbout()}
			<p>{$oCity->getAbout()}</p><br />
			{*<li>
				<span>{$aLang.plugin.city.city_info_description}:</span>
				<strong>{$oCity->getDescription()|nl2br}</strong>
			</li>*}
		{/if}
		{if $oCity->getTags()}
			<li>
				<span>{$aLang.plugin.city.city_info_tags}:</span>
				<strong>{$oCity->getTagsLink()}</strong>
			</li>
		{/if}
		{if $oCity->getCountWorkers()!=0}
			<li>
				<span>{$aLang.plugin.city.city_info_countworkers}:</span>
				<strong>
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
				</strong>
			</li>
		{/if}
		{if $oCity->getDateBasis()}
			<li>
				<span>{$aLang.plugin.city.city_info_datebasis}:</span>
				<strong>{date_format date=$oCity->getDateBasis() format="j F Y"}</strong>
			</li>
		{/if}
			<li>
				<span>{$aLang.plugin.city.city_info_date_add}:</span>
				<strong>{date_format date=$oCity->getDateAdd() format="j F Y"}</strong>
			</li>
			<li>
				<span>{$aLang.plugin.city.city_info_owner}:</span>
				<strong>{assign var="oUser" value=$oCity->getOwner()}
					<span class="user-avatar">
						<a href="{$oUser->getUserWebPath()}"><img src="{$oUser->getProfileAvatarPath(24)}" alt="avatar" /></a>
						<a href="{$oUser->getUserWebPath()}">{$oUser->getLogin()}</a></span>
				</strong>
			</li>
			{if count($aCityEmpl) > 0}
			<li>
				<span>{$aLang.plugin.city.city_info_employes}({count($aCityEmpl)}):</span>
				<strong>
					{foreach from=$aCityEmpl item=oBlogUser}
						{assign var="oUser" value=$oBlogUser->getUser()}
						<span class="user-avatar">
							<a href="{$oUser->getUserWebPath()}"><img src="{$oUser->getProfileAvatarPath(24)}" alt="avatar" /></a>
							<a href="{$oUser->getUserWebPath()}">{$oUser->getLogin()}</a>
						</span>
					{/foreach}
				</strong>
			</li>
			{/if}
		{if $oCity->getFileName()}
			<li>
				<span>{$aLang.plugin.city.city_info_file}:</span>
				<strong>
					<a href="{$oCity->getFilePath()}" >{$oCity->getFileName()}</a></strong>
			</li>
		{/if}
		{if $aStaff}
			<li>
				<span>{$aLang.plugin.city.city_info_staff}:</span>
				<strong>{if $aStaff}

					{foreach from=$aStaff item=oStaff}
						<span class="user-avatar">{$oStaff->getStaffName()}<div class="staff-position">{$oStaff->getStaffPosition()}</div></span>
					{/foreach}

				{/if}
				</strong>
			</li>
		{/if}
		</ul>
		<h2 class="header-table">{$aLang.plugin.city.city_menu_contacts}</h2>
		<ul class="city-profile-dotted-list">
		{if $oCity->getSite()}
			<li>
				<span>{$aLang.plugin.city.city_info_site}:</span>
				<strong><noindex> <a href="{$oCity->getSite(true)|escape:'html'}" target="_blank">{$oCity->getSite()}</a></noindex> </strong>
			</li>
		{/if}
		{if $oCity->getEmail() and $oUserCurrent}
			<li>
				<span>{$aLang.plugin.city.city_info_email}:</span>
				<strong>{if $oUserCurrent}{$oCity->getEmail()|escape:'html'}{else}{$aLang.plugin.city.city_email_hide}{/if}</strong>
			</li>
		{/if}
		{if $oCity->getPhone()}
			<li>
				<span>{$aLang.plugin.city.city_info_phone}:</span>
				<strong>{$oCity->getPhone()|escape:'html'}</strong>
			</li>
		{/if}
		{if $oCity->getFax()}
			<li>
				<span>{$aLang.plugin.city.city_info_fax}:</span>
				<strong>{$oCity->getFax()|escape:'html'}</strong>
			</li>
		{/if}
		{if $oCity->getSkype()}
			<li>
				<span>{$aLang.plugin.city.city_info_skype}:</span>
				<strong>{$oCity->getSkypeLink()}</strong>
			</li>
		{/if}
		{if $oCity->getIcq()}
			<li>
				<span>{$aLang.plugin.city.city_info_icq}:</span>
				<strong>{$oCity->getIcq()|escape:'html'}</strong>
			</li>
		{/if}
		{if $oCity->getContactName()}
			<li>
				<span>{$aLang.plugin.city.city_info_contact_name}:</span>
				<strong>{$oCity->getContactName()|escape:'html'}</strong>
			</li>
		{/if}
		{if $oCity->getContactInfo()}
			<li>
				<span>{$aLang.plugin.city.city_info_contact_info}:</span>
				<strong>{$oCity->getContactInfo()|nl2br}</strong>
			</li>
		{/if}
		{if ($oCity->getGeoTarget())}
			<li>
				<span>
					{$aLang.plugin.city.city_info_place}:
				</span>
				<strong>
					{if $oCity->getCountry()}
						<a href="{router page='cityes'}country/{$oCity->getGeoTarget()->getCountryId()}/">{$oCity->getCountry()|escape:'html'}</a>{/if}{if $oCity->getGeoTarget()->getCityId()},{/if}
					{if $oCity->getCity()}
						 <a href="{router page='cityes'}city/{$oCity->getGeoTarget()->getCityId()}/">{$oCity->getCity()|escape:'html'}</a>{/if}{if $oCity->getAddress()},{/if}
					{if $oCity->getAddress()} {$oCity->getAddress()|escape:'html'}{/if}
					{if ($oCity->getLongitude() != '')}<br/>
						<a href="#" class="dotted" id="toggle" onclick="return true;">{$aLang.plugin.city.city_info_show_map}</a>

					{/if}
				</strong>
				<div id="YMapsCity" class="map hidden"></div>
			</li>
		{/if}


		</ul>

	{if $oConfig->GetValue('module.city.map.use')}
	{include file="`$aTemplatePathPlugin['city']`inject.map.tpl"}
	{/if}
	{if $oCity->IsAllowTariff('photo') && $oCity->getPhotoCount()}
	{include file="`$aTemplatePathPlugin['city']`inject.photos.tpl"}
	{/if}

	</div>
</div>

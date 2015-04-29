{include file='header.tpl' menu="main"}

<h2 class="page-header">{$aLang.plugin.city.city_edit_header}: <a href="{$oCity->getUrlFull()}/"  class="blog_headline_group">{$oCity->getName()}</a></h2>
{include file="`$aTemplatePathPlugin['city']`menu.city_edit.tpl"}

<div class="profile-user">
{if $aBlogUsers}

	<form action="" method="POST" id="thisform" enctype="multipart/form-data">
		<input type="hidden" name="security_ls_key" value="{$LIVESTREET_SECURITY_KEY}" />
		<table class="table">
			<thead>
			<tr>
				<td></td>
				<td width="6%">{$aLang.plugin.city.city_users_admin}</td>
				<td width="10%">{$aLang.plugin.city.city_users_moderator}</td>
				<td width="10%">{$aLang.plugin.city.city_users_employe}</td>
				<td width="21%">{$aLang.plugin.city.city_users_admirer}</td>
				<td width="6%">{$aLang.plugin.city.city_users_reject}</td>

			</tr>
			</thead>
			<tbody>
				{foreach from=$aBlogUsers item=oCityUser}
					{assign var="oUser" value=$oCityUser->getUser()}
				<tr>
					<td class="username"><a href="{router page='profile'}{$oUser->getLogin()}/">{$oUser->getLogin()}</a></td>
					{if $oCityUser->getUserId()==$oUserCurrent->getId()}
						<td colspan="5" align="center">{$aLang.plugin.city.city_users_isadmin}</td>
						{elseif $oCityUser->getUserOwnerId()==$oCityUser->getUserId()}
						<td colspan="5" align="center">{$aLang.plugin.city.city_users_isowner}</td>
						{else}
						<td><input type="radio" name="user_rank[{$oUser->getId()}]"  value="administrator" {if $oCityUser->getIsAdministrator()}checked{/if}/></td>
						<td><input type="radio" name="user_rank[{$oUser->getId()}]"  value="moderator" {if $oCityUser->getIsModerator()}checked{/if}/></td>
						<td><input type="radio" name="user_rank[{$oUser->getId()}]"  value="employee" {if $oCityUser->getUserRole()==$BLOG_USER_ROLE_USER}checked{/if}/></td>
						<td><input type="radio" name="user_rank[{$oUser->getId()}]"  value="reader" {if $oCityUser->getUserRole()==$BLOG_USER_ROLE_GUEST}checked{/if}/></td>
						<td><input type="radio" name="user_rank[{$oUser->getId()}]"  value="reject"/></td>
					{/if}
				</tr>
				{/foreach}
			</tbody>
		</table>
		<p>
        <div class="note">{$aLang.plugin.city.city_users_note}</div>
		<button type="submit" name="submit_city_admin" id="submit_city_admin" class="button">{$aLang.plugin.city.city_users_submit}</button>
		</p>
	</form>
	{else}
	{$aLang.plugin.city.city_users_onlyone}
{/if}

{if $oBlog->getOwnerId()==$oUserCurrent->getId() or $oUserCurrent->isAdministrator()}
	<form action="" method="POST" id="thisform" enctype="multipart/form-data">
		<input type="hidden" name="security_ls_key" value="{$LIVESTREET_SECURITY_KEY}" />
		<p><label for="own_user">{$aLang.plugin.city.city_users_replaceowner}:</label>
			<input type="text" class="input-text input-width-200 autocomplete-users" id="own_user" name="own_user" value="{$_aRequest.own_user}"/>
			<button type="submit" name="submit_city_own" id="submit_city_own" class="button">{$aLang.plugin.city.city_users_replaceowner_submit}</button>
	</form>
{/if}

</div>

{include file='footer.tpl'}


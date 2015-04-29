{include file='header.tpl'}

<h2 class="page-header">{$aLang.plugin.city.city_edit_header}: <a href="{$oCity->getUrlFull()}/"  class="blog_headline_group">{$oCity->getName()}</a></h2>
{include file="`$aTemplatePathPlugin['city']`menu.city_edit.tpl"}

{if $aBlogUsers}

	<form action="" method="POST" id="thisform" enctype="multipart/form-data">
		<input type="hidden" name="security_ls_key" value="{$LIVESTREET_SECURITY_KEY}" />

		<table class="table table-users">
			<thead>
				<tr>
					<th class="cell-name">{$aLang.blog_admin_users}</th>
					<th class="ta-c">{$aLang.blog_admin_users_administrator}</th>
					<th class="ta-c">{$aLang.blog_admin_users_moderator}</th>
					<th class="ta-c">{$aLang.blog_admin_users_reader}</th>
					<th class="ta-c">{$aLang.blog_admin_users_bun}</th>
					<th class="ta-c">{$aLang.plugin.city.city_users_reject}</th>
				</tr>
			</thead>

			<tbody>
				{foreach from=$aBlogUsers item=oCityUser}
					{assign var="oUser" value=$oCityUser->getUser()}

					<tr>
						<td class="cell-name">
							<a href="{$oUser->getUserWebPath()}"><img src="{$oUser->getProfileAvatarPath(24)}" alt="avatar" class="avatar" /></a>
							<a href="{$oUser->getUserWebPath()}">{$oUser->getLogin()}</a>
						</td>

						{if $oCityUser->getUserId()==$oUserCurrent->getId()}
							<td colspan="5" align="center">{$aLang.plugin.city.city_users_isadmin}</td>
						{elseif $oCityUser->getUserOwnerId()==$oCityUser->getUserId()}
							<td colspan="5" align="center">{$aLang.plugin.city.city_users_isowner}</td>
						{else}
							<td class="ta-c"><input type="radio" name="user_rank[{$oUser->getId()}]"  value="administrator" {if $oCityUser->getIsAdministrator()}checked{/if}/></td>
							<td class="ta-c"><input type="radio" name="user_rank[{$oUser->getId()}]"  value="moderator" {if $oCityUser->getIsModerator()}checked{/if}/></td>
							<td class="ta-c"><input type="radio" name="user_rank[{$oUser->getId()}]"  value="employee" {if $oCityUser->getUserRole()==$BLOG_USER_ROLE_USER}checked{/if}/></td>
							<td class="ta-c"><input type="radio" name="user_rank[{$oUser->getId()}]"  value="reader" {if $oCityUser->getUserRole()==$BLOG_USER_ROLE_GUEST}checked{/if}/></td>
							<td class="ta-c"><input type="radio" name="user_rank[{$oUser->getId()}]"  value="reject"/></td>
						{/if}
					</tr>
				{/foreach}
			</tbody>
		</table>

		<button type="submit" name="submit_city_admin" id="submit_city_admin" class="button">{$aLang.plugin.city.city_users_submit}</button>
	</form>
{else}
	{$aLang.plugin.city.city_users_onlyone}
{/if}

<br />
<br />

{if $oBlog->getOwnerId()==$oUserCurrent->getId() or $oUserCurrent->isAdministrator()}
	<form action="" method="POST" id="thisform" enctype="multipart/form-data">
		<input type="hidden" name="security_ls_key" value="{$LIVESTREET_SECURITY_KEY}" />
		<p><label for="own_user">{$aLang.plugin.city.city_users_replaceowner}:</label>
			<input type="text" class="input-text input-width-200 autocomplete-users" id="own_user" name="own_user" value="{$_aRequest.own_user}"/>
			<button type="submit" name="submit_city_own" id="submit_city_own" class="button">{$aLang.plugin.city.city_users_replaceowner_submit}</button>
	</form>
{/if}

{include file='footer.tpl'}


{assign var="sidebarPosition" value='left'}
{include file='header.tpl' menu='people'}

{include file='actions/ActionProfile/profile_top.tpl'}
{include file='menu.profile_favourite.tpl'}

{include file="`$aTemplatePathPlugin['city']`cityes_table_list.tpl" aCity=$aCity}

{include file='paging.tpl' aPaging=$aPaging}
{include file='footer.tpl'}
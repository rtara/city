{include file='header.tpl'}


<h2 class="page-header">{$sHeader}</h2>

<div>
    <form action="" method="POST" id="thisform" enctype="multipart/form-data">
        <input type="hidden" name="security_ls_key" value="{$LIVESTREET_SECURITY_KEY}" />
	    <input type="submit" name="submit_update" class="button button-primary" value="Обновить"></p>
	</form>	
  
</div>


{include file='footer.tpl'}


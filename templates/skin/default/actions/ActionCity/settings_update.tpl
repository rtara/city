{include file='header.tpl' showWhiteBack=true}


<h1>{$sHeader}</h1>
<div>
    <form action="" method="POST" id="thisform" enctype="multipart/form-data">
        <input type="hidden" name="security_ls_key" value="{$LIVESTREET_SECURITY_KEY}" />
	    <input type="submit" name="submit_update" value="Обновить">
	</form>	
  
</div>

{include file='footer.tpl'}


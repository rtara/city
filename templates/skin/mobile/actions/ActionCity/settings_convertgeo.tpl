{include file='header.tpl' showWhiteBack=true}


<h2 class="page-header">Обновление стран и городов на новый формат 1.0</h2>
<div>
    <form action="" method="POST" id="thisform" enctype="multipart/form-data">
       <input type="hidden" name="security_ls_key" value="{$LIVESTREET_SECURITY_KEY}" />
	    <button type="submit" name="submit_convert" id="submit_convert" class="button button-primary">Конвертировать</button>
	</form>	
  
</div>

{include file='footer.tpl'}


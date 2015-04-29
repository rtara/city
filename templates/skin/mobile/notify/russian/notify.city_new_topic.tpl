Компания <a href="{$oCity->getUrlFull()}/blog/">{$oCity->getName()}</a> опубликовала новый топик <b>«<a href="{$oTopic->getUrl()}">{$oTopic->getTitle()|escape:'html'}</a>»</b>

{if $sSubscribeKey}
	<br><br>
	<a href="{router page='subscribe'}unsubscribe/{$sSubscribeKey}/">Отписаться от новых комментариев к этому топику</a>
{/if}

<br><br>
С уважением, администрация сайта <a href="{cfg name='path.root.web'}">{cfg name='view.name'}</a>
Пользователь <a href="{$oUserComment->getUserWebPath()}">{$oUserComment->getLogin()}</a> оставил новый отзыв к компании <b>«<a href="{$oCity->getUrlFull()}/blog/">{$oCity->getName()}</a>»</b>, прочитать его можно перейдя по <a href="{$oCity->getUrlFull()}/feedbacks#comment{$oComment->getId()}">этой ссылке</a><br>
{if $oConfig->GetValue('sys.mail.include_comment')}
	Текст сообщения: <i>{$oComment->getText()}</i>				
{/if}

{if $sSubscribeKey}
	<br><br>
	<a href="{router page='subscribe'}unsubscribe/{$sSubscribeKey}/">Отписаться от новых отзывов к компании</a>
{/if}

<br><br>
С уважением, администрация сайта <a href="{cfg name='path.root.web'}">{cfg name='view.name'}</a>
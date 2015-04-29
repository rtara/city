<ul class="list">
	{foreach from=$aComments item=oComment name="cmt"}
		{assign var="oUser" value=$oComment->getUser()}
		{assign var="oCity" value=$oComment->getTarget()}
		{assign var="oBlog" value=$oCity->getBlog()}
	
		<li {if $smarty.foreach.cmt.iteration % 2 == 1}class="even"{/if}>
			<a href="{$oUser->getUserWebPath()}" class="user">{$oUser->getLogin()}</a> &rarr;
			<span class="stream-comment-icon"></span>
			<a href="{$oCity->getUrlFull()}/feedbacks/#comment{$oComment->getId()}" class="stream-comment">{$oCity->getCityName()|escape:'html'}</a>
			<span> {$oCity->getCityCountFeedback()}</span>
		</li>
	{/foreach}
</ul>


<div class="bottom">
	<a href="{router page='comments'}">{$aLang.block_stream_comments_all}</a> | <a href="{router page='rss'}allcomments/">RSS</a>
</div>
<ul class="latest-list">
{foreach from=$aComments item=oComment name="cmt"}
	{assign var="oUser" value=$oComment->getUser()}
	{assign var="oCity" value=$oComment->getTarget()}

    <li class="js-title-comment" title="{$oComment->getText()|strip_tags|trim|truncate:100:'...'|escape:'html'}">
        <p>
            <a href="{$oUser->getUserWebPath()}" class="author">{$oUser->getLogin()}</a>
            <time datetime="{date_format date=$oComment->getDate() format='c'}" title="{date_format date=$oComment->getDate() format="j F Y, H:i"}">
				{date_format date=$oComment->getDate() hours_back="12" minutes_back="60" now="60" day="day H:i" format="j F Y, H:i"}
            </time>
        </p>
        <a href="{$oCity->getUrlFull()}/feedbacks/#comment{$oComment->getId()}" class="stream-topic">{$oCity->getCityName()|escape:'html'}</a>
        <span class="block-item-comments"><i class="icon-synio-comments-small"></i>{$oCity->getCityCountFeedback()}</span>
    </li>
{/foreach}
</ul>


<footer>
    <a href="{router page='comments'}">{$aLang.block_stream_comments_all}</a> · <a href="{router page='rss'}allcomments/">RSS</a>
</footer>

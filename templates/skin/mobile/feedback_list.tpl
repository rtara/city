<div class="comments comment-list">
{foreach from=$aComments item=oComment}
	{assign var="oUser" value=$oComment->getUser()}
	{assign var="oCity" value=$oComment->getTarget()}

    <section class="comment">
        <ul class="comment-info">
            <li class="comment-author">
                <a href="{$oUser->getUserWebPath()}"><img src="{$oUser->getProfileAvatarPath(24)}" alt="avatar" class="comment-avatar" /></a>
                <a href="{$oUser->getUserWebPath()}">{$oUser->getLogin()}</a>
            </li>
            <li class="comment-date">
                <time datetime="{date_format date=$oComment->getDate() format='c'}">{date_format date=$oComment->getDate() hours_back="12" minutes_back="60" now="60" day="day H:i" format="j F Y, H:i"}</time>
            </li>
            <li class="comment-link">
                <a href="{if $oConfig->GetValue('module.comment.nested_per_page')}{router page='comments'}{else}{$oCity->getUrlFull()}/feedbacks/{/if}{$oComment->getId()}" title="{$aLang.comment_url_notice}"></a>
                    <i class="icon-synio-link"></i>
                </a>
            </li>
        </ul>

        <div class="comment-content">
            <div class="text">
				{if $oComment->isBad()}
	                <div style="color: #aaa;">{$oComment->getText()}	</div>
					{else}
						{$oComment->getText()}
					{/if}
            </div>
        </div>


        <div class="comment-path">
            <a href="{$oCity->getUrlFull()}" class="blog-name">{$oCity->getName()|escape:'html'}</a> &rarr;
            <a href="{$oCity->getUrlFull()}/feedbacks/" class="comment-path-topic">{$aLang.block_stream_feedbacks}</a>
            <a href="{$oCity->getUrlFull()}/feedbacks/" class="comment-path-comments">{$oCity->getCountFeedback()}</a>
        </div>
    </section>
{/foreach}
</div>


{include file='paging.tpl' aPaging=$aPaging}

{assign var="oCity2" value=$oComment->getTarget()}
{assign var="oBlog" value=$oCity2->getBlog()}
{assign var="oUser" value=$oComment->getUser()}
{assign var="oUserOwner" value=$oBlog->getOwner()}


<section id="comment_id_{$oComment->getId()}" class="comment
														{if $oComment->isBad()}
															comment-bad
														{/if}

														{if $oComment->getDelete()}
															comment-deleted
														{elseif $oUserCurrent and $oComment->getUserId() == $oUserCurrent->getId()} 
															comment-self
														{elseif $sDateReadLast <= $oComment->getDate()} 
															comment-new
														{/if}">
	{if !$oComment->getDelete() or $bOneComment or ($oUserCurrent and $oUserCurrent->isAdministrator())}
		<a name="comment{$oComment->getId()}"></a>
		
		
		<a href="{$oUser->getUserWebPath()}"><img src="{$oUser->getProfileAvatarPath(48)}" alt="avatar" class="comment-avatar" /></a>
		
		{if $iAuthorId == $oUser->getId()}<span class="comment-topic-author" title="{if $sAuthorNotice}{$sAuthorNotice}{/if}"></span>{/if}
		
		<ul class="comment-info">
			<li class="comment-author {if $iAuthorId == $oUser->getId()}active{/if}">
				<a href="{$oUser->getUserWebPath()}">{$oUser->getLogin()}</a>
			</li>
			<li class="comment-date">
				<a href="{if $oConfig->GetValue('module.comment.nested_per_page')}{router page='comments'}{else}#comment{/if}{$oComment->getId()}" title="{$aLang.comment_url_notice}">
					<time datetime="{date_format date=$oComment->getDate() format='c'}">{date_format date=$oComment->getDate() hours_back="12" minutes_back="60" now="60" day="day H:i" format="j F Y, H:i"}</time>
				</a>
			</li>

			{if $oUserCurrent and !$bNoCommentFavourites}
				<li class="comment-favourite {if $oComment->getCountFavourite() > 0}has-favs{/if}">
					<div onclick="return ls.favourite.toggle({$oComment->getId()},this,'comment');" class="favourite {if $oComment->getIsFavourite()}active{/if}"></div>
					<span class="favourite-count" id="fav_count_comment_{$oComment->getId()}">{if $oComment->getCountFavourite() > 0}{$oComment->getCountFavourite()}{/if}</span>
				</li>
			{/if}

			
			{if $oComment->getPid()}
				<li class="goto-comment-parent"><a href="#" onclick="ls.comments.goToParentComment({$oComment->getId()},{$oComment->getPid()}); return false;" title="{$aLang.comment_goto_parent}">↑</a></li>
			{/if}
			<li class="goto-comment-child"><a href="#" title="{$aLang.comment_goto_child}">↓</a></li>
			
			


			{if $oComment->getTargetType() != 'talk'}						
				<li id="vote_area_comment_{$oComment->getId()}" class="vote 
																		{if $oComment->getRating() > 0}
																			vote-count-positive
																		{elseif $oComment->getRating() < 0}
																			vote-count-negative
																		{elseif $oComment->getRating() == 0}
																			vote-count-zero
																		{/if} 
																		
																		{if $oVote} 
																			voted 
																			
																			{if $oVote->getDirection() > 0}
																				voted-up
																			{else}
																				voted-down
																			{/if}
																		{/if}">
					<div class="vote-down" onclick="return ls.vote.vote({$oComment->getId()},this,-1,'comment');"></div>
					<div class="vote-up" onclick="return ls.vote.vote({$oComment->getId()},this,1,'comment');"></div>
					<span class="vote-count" id="vote_total_comment_{$oComment->getId()}">{if $oComment->getRating() > 0}+{/if}{$oComment->getRating()}</span>
				</li>
			{/if}
		</ul>
		
		
		<div id="comment_content_id_{$oComment->getId()}" class="comment-content text">
			{$oComment->getText()}
		</div>
			
			
		<ul class="comment-actions">
		{if $oUserCurrent}
			{if !$oComment->getDelete() and !$bAllowNewComment}
				<li><a href="#" onclick="ls.comments.toggleCommentForm({$oComment->getId()}); return false;" class="reply-link">{$aLang.comment_answer}</a></li>
			{/if}

			<li class="folding" {if $bOneComment || !$bComments}style="display: none;"{/if}><a href="#" title="{$aLang.comment_collapse}/{$aLang.comment_expand}" onclick="return false;">{$aLang.comment_fold}</a></li>
				
			{if !$oComment->getDelete() and $oUserCurrent->isAdministrator()}
				<li><a href="#" class="comment-delete" onclick="ls.comments.toggle(this,{$oComment->getId()}); return false;">{$aLang.comment_delete}</a></li>
			{/if}	

			{if $oComment->getDelete() and $oUserCurrent->isAdministrator()}
				<li><a href="#" class="comment-repair" onclick="ls.comments.toggle(this,{$oComment->getId()}); return false;">{$aLang.comment_repair}</a></li>
			{/if}

			{if !$oComment->isBad() and ($oUserOwner->getId() == $oUserCurrent->getId() or $oBlog->getUserIsAdministrator() or $oBlog->getUserIsModerator() or $oUserCurrent->isAdministrator())}
				<li><a href="#" onclick="ls.comments.toggleHide(this,{$oComment->getId()}); return false;">{$aLang.plugin.city.feedback_hide}</a></li>
			{/if}

			{if $oComment->isBad() and ($oUserOwner->getId() == $oUserCurrent->getId() or $oBlog->getUserIsAdministrator() or $oBlog->getUserIsModerator() or $oUserCurrent->isAdministrator())}
				<li><a href="#" onclick="ls.comments.toggleHide(this,{$oComment->getId()}); return false;">{$aLang.plugin.city.feedback_show}</a></li>
			{/if}
		{/if}
			{hook run='comment_action' comment=$oComment}
		</ul>
	{else}				
		{$aLang.comment_was_delete}
	{/if}	
</section>
{add_block group='toolbar' name='toolbar_comment.tpl'
	aPagingCmt=$aPagingCmt
	iTargetId=$iTargetId
	sTargetType=$sTargetType
	iMaxIdComment=$iMaxIdComment
}

{hook run='comment_tree_begin' iTargetId=$iTargetId sTargetType=$sTargetType}

<div class="comments" id="comments">
	<header class="comments-header clearfix">
		<strong><span id="count-comments">{$iCountComment}</span> {$iCountComment|declension:$aLang.plugin.city.feedback_declension:'russian'}</strong>
		<a href="{router page='rss'}comments/{$iTargetId}/" class="rss">RSS</a>
		
		{if $bAllowSubscribe and $oUserCurrent}
			<a href="#" 
				class="comments-subscribe {if $oSubscribeComment and $oSubscribeComment->getStatus()}active{/if}" 
				id="comment_subscribe" 
				onclick="ls.subscribe.toggle('{$sTargetType}_new_feedback','{$iTargetId}','',!jQuery(this).hasClass('active')); return false;"
				title="{$aLang.subscribe_title}">
				{if $oSubscribeComment and $oSubscribeComment->getStatus()}
					{$aLang.comment_unsubscribe}
				{else}
					{$aLang.comment_subscribe}
				{/if}
			</a>
		{/if}
	
		<a name="comments"></a>
	</header>

	{if count($aComments) > 0}	
		{assign var="nesting" value="-1"}
		{foreach from=$aComments item=oComment name=rublist}
			{assign var="cmtlevel" value=$oComment->getLevel()}
			
			{if $cmtlevel>$oConfig->GetValue('module.comment.max_tree')}
				{assign var="cmtlevel" value=$oConfig->GetValue('module.comment.max_tree')}
			{/if}
			
			{if $nesting < $cmtlevel} 
			{elseif $nesting > $cmtlevel}    	
				{section name=closelist1  loop=$nesting-$cmtlevel+1}</div>{/section}
			{elseif not $smarty.foreach.rublist.first}
				</div>
			{/if}
			
			<div class="comment-wrapper" id="comment_wrapper_id_{$oComment->getId()}">
			
			{include file="`$aTemplatePathPlugin['city']`feedback.tpl"}
			{assign var="nesting" value=$cmtlevel}
			{if $smarty.foreach.rublist.last}
				{section name=closelist2 loop=$nesting+1}</div>{/section}    
			{/if}
		{/foreach}
	{else}
		<div class="notice-empty" id="comments_empty">{$aLang.topic_comments_empty}</div>
	{/if}
</div>				
	
	
{include file='comment_paging.tpl' aPagingCmt=$aPagingCmt}

{hook run='comment_tree_end' iTargetId=$iTargetId sTargetType=$sTargetType}

{if $bAllowNewComment}
	{$sNoticeNotAllow}
{else}
	{if $oUserCurrent}
		{include file='editor.tpl' sImgToLoad='form_comment_text' sSettingsTinymce='ls.settings.getTinymceComment()' sSettingsMarkitup='ls.settings.getMarkitupComment()'}

		<h4 class="reply-header" id="comment_id_0">
			<a href="#" class="link-dotted" onclick="ls.comments.toggleCommentForm(0); return false;">{$sNoticeCommentAdd}</a>
		</h4>
		
		<div id="reply" class="reply">		
			<a href="{$oUserCurrent->getUserWebPath()}"><img src="{$oUserCurrent->getProfileAvatarPath(48)}" alt="avatar" class="avatar" /></a>

			<form method="post" id="form_comment" onsubmit="return false;" enctype="multipart/form-data">
				{hook run='form_add_comment_begin'}
				
				<textarea name="comment_text" id="form_comment_text" class="mce-editor markitup-editor input-width-full" placeholder="{$sNoticeCommentAdd}"></textarea>
				
				{hook run='form_add_comment_end'}
				
				<button type="submit" name="submit_comment" 
						id="comment-button-submit" 
						onclick="ls.comments.add('form_comment',{$iTargetId},'{$sTargetType}'); return false;" 
						class="button button-primary">{$aLang.comment_add}</button>
				<button type="button" onclick="ls.comments.preview();" class="button">{$aLang.comment_preview}</button>
				
				<input type="hidden" name="reply" value="0" id="form_comment_reply" />
				<input type="hidden" name="cmt_target_id" value="{$iTargetId}" />
			</form>
		</div>
	{else}
		<div class="system-message-notice">
			{$aLang.comment_unregistered}
		</div>
	{/if}
{/if}	



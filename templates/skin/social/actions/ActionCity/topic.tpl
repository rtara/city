{if $oUserCurrent}
	{if $oCity->IsAllowTariff('branding') && $oCity->getUseBrandImage()}
	{include file="`$aTemplatePathPlugin['city']`header_branding.tpl" menu='main' showUpdateButton=true}
		{else}
	{include file='header.tpl' menu='main' showUpdateButton=true}
	{/if}
	{else}
	{if $oCity->IsAllowTariff('branding') && $oCity->getUseBrandImage()}
	{include file="`$aTemplatePathPlugin['city']`header_branding.tpl" menu='main'}
		{else}
	{include file='header.tpl' menu='main'}
	{/if}
{/if}
            {include file="`$aTemplatePathPlugin['city']`header.city.tpl"}

			{include file='topic.tpl'}			
			{include 
				file='comment_tree.tpl' 	
				iTargetId=$oTopic->getId()
				iAuthorId=$oTopic->getUserId()
				sAuthorNotice=$aLang.topic_author
				sTargetType='topic'
				iCountComment=$oTopic->getCountComment()
				sDateReadLast=$oTopic->getDateRead()
				bAllowNewComment=$oTopic->getForbidComment()
				sNoticeNotAllow=$aLang.topic_comment_notallow
				sNoticeCommentAdd=$aLang.topic_comment_add
				bAllowSubscribe=true
				oSubscribeComment=$oTopic->getSubscribeNewComment()
				aPagingCmt=$aPagingCmt}

{include file='footer.tpl'}

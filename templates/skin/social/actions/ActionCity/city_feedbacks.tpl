{if $oCity->IsAllowTariff('branding') && $oCity->getUseBrandImage()}
{include file="`$aTemplatePathPlugin['city']`header_branding.tpl"}
	{else}
{include file="header.tpl"}
{/if}

{include file="`$aTemplatePathPlugin['city']`header.city.tpl"}

{include
	file="`$aTemplatePathPlugin['city']`feedback_tree.tpl"
	iTargetId=$oCity->getId()
	iAuthorId=$oCity->getOwnerId()
	sAuthorNotice=$aLang.plugin.city.city_owner
	sTargetType='city'
	iCountComment=$oCity->getCountFeedback()
	sDateReadLast=$oCity->getDateRead()
	bAllowNewComment=0
	sNoticeNotAllow=$aLang.plugin.city.city_feedbacks_notallow
	sNoticeCommentAdd=$aLang.plugin.city.city_feedbacks_write_feedback
	bAllowSubscribe=true
	oSubscribeComment=$oCity->getSubscribeNewFeedback()
	aPagingCmt=$aPagingCmt}

{include file='footer.tpl'}

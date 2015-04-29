<div class="block block-type-stream" id="block_feedbacks">
	<header class="block-header sep">
		<h3>{$aLang.plugin.city.block_stream_feedbacks}</h3>
	</header>

	<div class="block-content">
		<ul class="item-list">
		{foreach from=$aFeedbacks item=oComment name="cmt"}
			{assign var="oUser" value=$oComment->getUser()}
			{assign var="oCity" value=$oComment->getTarget()}


			<li class="js-title-comment" title="{$oComment->getText()|strip_tags|trim|truncate:100:'...'}">
				<a href="{$oUser->getUserWebPath()}"><img src="{$oUser->getProfileAvatarPath(48)}" alt="avatar" class="avatar" /></a>

				<a href="{$oUser->getUserWebPath()}" class="author">{$oUser->getLogin()}</a> &rarr;
				<a href="{$oCity->getUrlFull()}" class="blog-name">{$oCity->getName()|escape:'html'}</a>

				<p>
					<time datetime="{date_format date=$oComment->getDate() format='c'}">{date_format date=$oComment->getDate() hours_back="12" minutes_back="60" now="60" day="day H:i" format="j F Y, H:i"}</time> |
					<a href="{$oCity->getUrlFull()}/feedbacks/#comment{$oComment->getId()}">
						{$oCity->getCountFeedback()}&nbsp;{$oCity->getCountFeedback()|declension:$aLang.comment_declension:'russian'}
					</a>
				</p>
			</li>
		{/foreach}
		</ul>
	</div>
</div>

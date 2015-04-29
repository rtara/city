<div class="block block-type-stream" id="block_feedbacks">
	<header class="block-header sep">
		<h3>{$aLang.plugin.city.block_stream_feedbacks}</h3>
	</header>

	<div class="block-content">


		<ul class="latest-list">
		{foreach from=$aFeedbacks item=oComment name="cmt"}
			{assign var="oUser" value=$oComment->getUser()}
			{assign var="oCity" value=$oComment->getTarget()}


			<li class="js-title-comment" title="{$oComment->getText()|strip_tags|trim|truncate:100:'...'}">
				<p>
					<a href="{$oUser->getUserWebPath()}" class="author">{$oUser->getLogin()}</a>
					<time datetime="{date_format date=$oComment->getDate() format='c'}">{date_format date=$oComment->getDate() hours_back="12" minutes_back="60" now="60" day="day H:i" format="j F Y, H:i"}</time>
				</p>
				<a href="{$oCity->getUrlFull()}/feedbacks/#comment{$oComment->getId()}"
				   class="stream-topic">{$oCity->getName()|escape:'html'}</a>
				<span class="block-item-comments"><i
						class="icon-synio-comments-small"></i>{$oCity->getCountFeedback()}</span>
			</li>
		{/foreach}
		</ul>
	</div>
</div>

var ls = ls || {};

ls.comments.options.type.city={url_add: 		aRouter.city+'ajax/addfeedback/',
		url_response: 	aRouter.city+'ajax/responsefeedback/'};


//Скрыть/восстановить комментарий
ls.comments.toggleHide = function(obj, commentId) {
	ls.ajax(aRouter['city']+'ajax/feedbackbad/', { idComment: commentId }, function(result){
		if (!result) {
			ls.msg.error('Error','Please try again later');
		}
		if (result.bStateError) {
			ls.msg.error(null,result.sMsg);
		} else {
			ls.msg.notice(null,result.sMsg);

            $('#comment_id_'+commentId).removeClass('comment-bad');
            if (result.bState) {
                $('#comment_id_'+commentId).addClass('comment-bad');
            }

			$(obj).text(result.sTextToggle);
		}
	}.bind(this));
}
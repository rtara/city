var ls = ls || {};

ls.city = (function ($) {
	this.toggleJoin = function(obj,idBlog) {   
		ls.ajax(aRouter['city']+'ajax/joinleavecity/',{idBlog: idBlog},function(result) {
			if (result.bStateError) {
				ls.msg.error(null, result.sMsg);
			} else {
				obj = $(obj);
				ls.msg.notice(null, result.sMsg);
				obj.removeClass("active");
                obj.text(ls.lang.get('plugin.city.city_work'));
				if (result.bState) {
					obj.addClass("active");
                    obj.text(ls.lang.get('plugin.city.city_dont_work'));
				}
				$('#blog_user_count_'+idBlog).text(result.iCountUser);
			}
		});
	}

    this.autocomplete = function(obj, sPath) {
        obj.autocomplete({
            source: function(request, response) {
                ls.ajax(sPath,{value: request.term},function(data){
                    response(data.aItems);
                });
            },
            select: function( event, ui ) {
                location.href=aRouter['city'] + ui.item.city_url +'/';
            }
        })
            .data( "autocomplete" )._renderItem = function( ul, item ) {
            return $( "<li></li>" )
                .data( "item.autocomplete", item )
                .append( "<a><img src='"+item.city_logo+"' height='20px' style='vertical-align: middle'> " + item.city_name +"</a>" )
                .appendTo( ul );
        };
    }

    this.addStaff = function() {
        var forms = $('.city .staff-form');
        if(forms.length>1) {
            var id = forms[forms.length-2].id.match(/\d+/g)[0];
            id = parseInt(id);
        }else {
            var id = 0;
        }
        id++;
        var form = $('#staff-form-form_id');
        $(form).clone().appendTo('#staff-forms-holder');
        var first_form = $('#staff-form-form_id:first');
        first_form.removeClass('hidden');
        first_form.attr('id', first_form.attr('id').replace(/form_id/,id));
        first_form.html(first_form.html().replace(/form_id/g,id));
        return false;
    }
    this.closeStaff = function(id) {
        $('#staff-form-' + id).remove();
        return false;
    }

	
    this.toggleWidget = function(name) {
        var widget = $('#widget-'+name);
        if (widget.is(':visible')) {
            widget.hide();
        } else {
            widget.show();
        }

        return false;
    };

    this.updateTwitter = function(screenName) {
        idCity = $('#city_id').val();
        var url = aRouter['city']+'ajax/update-twitter/';
        var params = {screenName: screenName, idCity: idCity};
        ls.ajax(url, params, function(result){
            if (result.bStateError) {
                ls.msg.error(null, result.sMsg);
            } else {
                ls.msg.notice(null, result.sMsg);
            }
        });
    };

    this.checkVk = function(vk_url) {
        aUrl = vk_url.split('/');
        screenName = aUrl[aUrl.length-1];
        $.ajax({
            url: 'http://api.vk.com/method/groups.getById?gid='+encodeURIComponent(screenName)+'&callback=jsonpCallback',
            type: 'post',
            dataType: 'jsonp',
            success: function jsonpCallback(data){
                var id = parseInt(data.response[0].gid);
            }
        });
    };

    this.updateVk = function(vk_url) {
        aUrl = vk_url.split('/');
        vkUrl = aUrl[aUrl.length-1];
        idCity = $('#city_id').val();
        var url = aRouter['city']+'ajax/update-vk/';
        var params = {vkUrl: vkUrl, idCity: idCity};
        ls.ajax(url, params, function(result){
            if (result.bStateError) {
                ls.msg.error(null, result.sMsg);
            } else {
                ls.msg.notice(null, result.sMsg);
            }
        });
    };

    this.updateFb = function(fb_url) {
        aUrl = fb_url.split('/');
        fbUrl = aUrl[aUrl.length-1];
        idCity = $('#city_id').val();
        var url = aRouter['city']+'ajax/update-fb/';
        var params = {fbUrl: fbUrl, idCity: idCity};
        ls.ajax(url, params, function(result){
            if (result.bStateError) {
                ls.msg.error(null, result.sMsg);
            } else {
                ls.msg.notice(null, result.sMsg);
            }
        });
    };

    this.switchWidgetVisible = function (widgetName) {
        var idCity = $('#city_id').val();
        var iVisible = $("#visible-"+widgetName).attr("checked")=="checked";

        var url = aRouter['city']+'ajax/switch-widget-visible/';
        var params = {widgetName: widgetName, idCity: idCity, iVisible: iVisible};

        ls.ajax(url, params, function(result){
            if (result.bStateError) {
                ls.msg.error(null, result.sMsg);
            } else {
                ls.msg.notice(null, result.sMsg);
            }
        });

    };


    this.getJSON = function(url){
        var resultJSON = null;
        var http_request = new XMLHttpRequest();
        http_request.open( "GET", url, true );
        http_request.send(null);
        http_request.onreadystatechange = function () {
            if ( http_request.readyState == 4 ) {
                if ( http_request.status == 200 ) {
                    resultJSON = JSON.parse(http_request.responseText);
                    ls.msg.notice(null, 'Успешно получены данные');
                   // return result;
                } else {
                    ls.msg.error(null, 'Неверное значение');
                }
                http_request = null;
            }
        };

    };


    this.updateTariff = function (formObj) {
        formObj = $('#'+formObj);

        var url = aRouter['city']+'ajax/tariff-update/';

        ls.ajax(url, formObj.serializeJSON(), function(result){
            if (result.bStateError) {
                ls.msg.error(null, result.sMsg);
            } else {
                if (result.sDate){
                    $('#tariff_end_date').text(result.sDate);
                    $('#tariff_end_date').removeClass("hidden");
                }else {
                    $('#tariff_end_date').addClass("hidden");
                }
                ls.msg.notice(null, result.sMsg);
            }
        });

    };
	
	
	return this;
}).call(ls.city || {},jQuery);



ls.vote.options.type.city = { url: aRouter['city']+'ajax/votecity/', targetName: 'idCity' };
ls.favourite.options.type.city = { url: aRouter['city']+'ajax/favourite/', targetName: 'idCity' };

ls.hook.add ('ls_template_init_start', function (obj, block) {
    $('.js-city-tag-search-form').submit(function(){
        var val=$(this).find('.js-city-tag-search').val();
        if (val) {
            window.location = aRouter['cityes']+'tag/'+encodeURIComponent(val)+'/';
        }
        return false;
    });
    ls.autocomplete.add($(".autocomplete-city-tags"), aRouter['city']+'ajax/autocompleter/tag/', false);
});


ls.hook.add('ls_favourite_toggle_after',function(idTarget,objFavourite,type,params,result){
    $('#fav_count_'+type+'_'+idTarget).text((result.iCount > 0) ? result.iCount : '');
    
    if (type == 'city' ) {
        if (result.bState) {
            this.objFavourite.text(ls.lang.get('vote_dont_like'));
        } else {
            this.objFavourite.text(ls.lang.get('vote_like'));
        }
    }
});


ls.hook.add ('ls_subscribe_toggle_after', function (sTargetType, iTargetId, sMail, iValue) {
    if (iValue == 1){
        $('#unsubscribeCity').removeClass("hidden");
        $('#subscribeCity').addClass("hidden");
    }else {
        $('#subscribeCity').removeClass("hidden");
        $('#unsubscribeCity').addClass("hidden");
    }
    // обработчиков ошибок нет, т.к. подписка уже прошла, значит все ок
    ls.ajax(aRouter['city']+'ajax/subscribe-toggle/',{idCity: iTargetId, iValue: iValue},function(result) {
    });
});
var ls = ls || {};

ls.cityphoto =( function ($) {
	
	this.idLast=0;
	this.isLoading=false;
	this.swfu=null;
	
	this.initSwfUpload = function(opt) {
		opt=opt || {};
		opt.post_params.ls_photo_target_tmp = $.cookie('ls_photo_target_tmp') ? $.cookie('ls_photo_target_tmp') : 0;
        opt.button_placeholder_id = 'photo-start-upload';
        opt.upload_url = aRouter['city']+"ajax/photo-upload";
        opt.button_text = '<span class="button">'+ls.lang.get('plugin.city.city_photo_upload_choose')+'</span>';
		$(ls.swfupload).unbind('load').bind('load',function() {
            this.swfu = ls.swfupload.init(opt);

            $(this.swfu).bind('eUploadProgress',this.swfHandlerUploadProgress);
            $(this.swfu).bind('eFileDialogComplete',this.swfHandlerFileDialogComplete);
            $(this.swfu).bind('eUploadSuccess',this.swfHandlerUploadSuccess);
            $(this.swfu).bind('eUploadComplete',this.swfHandlerUploadComplete);
		}.bind(this));
		
		ls.swfupload.loadSwf();
	}
	
	this.swfHandlerUploadProgress = function(e, file, bytesLoaded, percent) {
		$('#photo_empty_progress').text(file.name+': '+( percent==100 ? 'resize..' : percent +'%'));
	}
	
	this.swfHandlerFileDialogComplete = function(e, numFilesSelected, numFilesQueued) {
		if (numFilesQueued>0) {
			ls.cityphoto.addPhotoEmpty();
		}
	}
	
	this.swfHandlerUploadSuccess = function(e, file, serverData) {
		ls.cityphoto.addPhoto(jQuery.parseJSON(serverData));
	}
	
	this.swfHandlerUploadComplete = function(e, file, next) {
		if (next>0) {
			ls.cityphoto.addPhotoEmpty();
		}
	}
	
	this.addPhotoEmpty = function() {
		template = '<li id="photo_empty"><img src="'+DIR_STATIC_SKIN + '/images/loader.gif'+'" alt="image" style="margin-left: 35px;margin-top: 20px;" />'
					+'<div id="photo_empty_progress" style="height: 60px;width: 350px;padding: 3px;border: 1px solid #DDDDDD;"></div><br /></li>';
		$('#swfu_images').append(template);
	}
	
	this.addPhoto = function(response) {
		$('#photo_empty').remove();
		if (!response.bStateError) {
			template = '<li id="photo_'+response.id+'"><img src="'+response.file+'" alt="image" />'
						+'<textarea onBlur="ls.cityphoto.setPreviewDescription('+response.id+', this.value)"></textarea><br />'
						+'<a href="javascript:ls.cityphoto.deletePhoto('+response.id+')" class="image-delete">'+ls.lang.get('plugin.city.city_photo_photo_delete')+'</a>'
						+'<span id="photo_preview_state_'+response.id+'" class="photo-preview-state"><a href="javascript:ls.cityphoto.setPreview('+response.id+')" class="mark-as-preview">'+ls.lang.get('plugin.city.city_photo_mark_as_preview')+'</a></span></li>';
			$('#swfu_images').append(template);
			ls.msg.notice(response.sMsgTitle,response.sMsg);
		} else {
			ls.msg.error(response.sMsgTitle,response.sMsg);
		}
		ls.cityphoto.closeForm();
	}

	this.deletePhoto = function(id)
	{
		if (!confirm(ls.lang.get('plugin.city.city_photo_photo_delete_confirm'))) {return;}
		ls.ajax(aRouter['city']+'ajax/photo-delete', {'id':id}, function(response){
			if (!response.bStateError) {
				$('#photo_'+id).remove();
				ls.msg.notice(response.sMsgTitle,response.sMsg);
			} else {
				ls.msg.error(response.sMsgTitle,response.sMsg);
			}
		});
	}

	this.setPreview =function(id)
	{
		$('#main_photo').val(id);

		$('.marked-as-preview').each(function (index, el) {
			$(el).removeClass('marked-as-preview');
			tmpId = $(el).attr('id').slice($(el).attr('id').lastIndexOf('_')+1);
			$('#photo_preview_state_'+tmpId).html('<a href="javascript:ls.cityphoto.setPreview('+tmpId+')" class="mark-as-preview">'+ls.lang.get('plugin.city.city_photo_mark_as_preview')+'</a>');
		});
		$('#photo_'+id).addClass('marked-as-preview');
		$('#photo_preview_state_'+id).html(ls.lang.get('plugin.city.city_photo_is_preview'));
	}

	this.setPreviewDescription = function(id, text)
	{
		ls.ajax(aRouter['city']+'ajax/photo-set-description', {'id':id, 'text':text},  function(result){
			if (!result.bStateError) {

			} else {
				ls.msg.error('Error','Please try again later');
			}
		}
		)
	}

	this.getMore = function(city_id)
	{
		if (this.isLoading) return;
		this.isLoading=true;
				
		ls.ajax(aRouter['city']+'ajax/photo-get-more', {'city_id':city_id, 'last_id':this.idLast}, function(result){
			this.isLoading=false;
			if (!result.bStateError) {
				if (result.photos) {
					$.each(result.photos, function(index, photo) {
						var image = '<li><a class="photoset-image" href="'+photo.path+'" rel="[photoset]" title="'+photo.description+'"><img src="'+photo.path_thumb+'" alt="'+photo.description+'" /></a></li>';
						$('#city-images').append(image);
						this.idLast=photo.id;
						$('.photoset-image').unbind('click');
						$('.photoset-image').prettyPhoto({
							social_tools:'',
							show_title: false,
							slideshow:false,
							deeplinking: false
						});
					}.bind(this));
				}
				if (!result.bHaveNext || !result.photos) {
					$('#city-photo-more').remove();
				}
			} else {
				ls.msg.error('Error','Please try again later');
			}
		}.bind(this));
	}

	this.upload = function()
	{
		ls.cityphoto.addPhotoEmpty();
		ls.ajaxSubmit(aRouter['city']+'ajax/photo-upload/',$('#photoset-upload-form'),function(data){
			if (data.bStateError) {
				$('#photo_empty').remove();
				ls.msg.error(data.sMsgTitle,data.sMsg);
			} else {
				ls.cityphoto.addPhoto(data);
			}
		});
		ls.cityphoto.closeForm();
	}

	this.closeForm = function()
	{
		$('#photoset-upload-form').jqmHide();
	}

	this.showForm = function()
	{
		var $select = $('#photo-start-upload');
		if ($select.length) {
			var pos = $select.offset();
			w = $select.outerWidth();
			h = $select.outerHeight();
			t = pos.top + h - 30  + 'px';
			l = pos.left - 15 + 'px';
			$('#photoset-upload-form').css({'top':t,'left':l});
		}
		$('#photoset-upload-form').show();
	}
	
	this.showMainPhoto = function(id) {
		$('#photoset-main-preview-'+id).css('width',$('#photoset-main-image-'+id).outerWidth());
		$('#photoset-photo-count-'+id).show();
		$('#photoset-photo-desc-'+id).show();
	}
	
	return this;
}).call(ls.cityphoto || {},jQuery);
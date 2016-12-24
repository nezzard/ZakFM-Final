//
var sto;
var sto_crawl;
var sto_keyword;
var stop_crawl = false;
var cum_list_medias_first = true;

//
jQuery(document).ready(function() {
	//
	jQuery('body').on('click', '#cum-warning', function() {
		var date = new Date();
		date.setTime(date.getTime() + (7 * 24 * 60 * 60 * 1000));
		var expires = "; expires=" + date.toGMTString();
		document.cookie = encodeURIComponent('cum-warning-hide')+'=yep'+expires+"; path=/";
	});
	//
	jQuery('body').on('click', '.cum-tag', function() {
		var _href = jQuery(this).attr('href');
		var _type = _href.replace(/#/, '');
		var _id = jQuery(this).attr('data-media-id');

		cum_display_details(_type, _id);

		return false;
	});
	//
	jQuery('body').on('click', '.cum-notice button.notice-dismiss', function() {
		jQuery(this).parent('.cum-notice').slideUp(400, function() {
			jQuery(this).remove();
		});
	});
	//
	jQuery('body').on('change', 'select[name="cum-medias-per-page"]', function() {
		cum_list_medias();
	});
	//
	jQuery('body').on('click', '#cum_medias_not_theme_customise, #cum_medias_not_thumb, #cum_medias_not_related, #cum_medias_not_in_content, #cum_medias_not_in_postmeta, #cum_medias_not_in_usermeta, #cum_medias_not_in_option, #cum_medias_not_in_acf, a.cum-refresh-result', function() {
		cum_list_medias();
	});
	//
	jQuery('body').on('click', 'a.cum-recrawl', function() {
		//
		stop_crawl = false;
		//
		cum_display_notice(cum_tools_translate.relaunch_crawl, 'info', 'cum_crawl_notice');
		//
		change_crawl_status('reset');
	});
	//
	jQuery('body').on('click', 'a.cum-pause', function() {
		//
		cum_display_notice(cum_tools_translate.pause_crawl, 'info', 'cum_crawl_notice');
		change_crawl_status('pause');
	});
	//
	jQuery('body').on('click', 'a.cum-resume', function() {
		//
		stop_crawl = false;
		//
		cum_display_notice(cum_tools_translate.resume_crawl, 'info', 'cum_crawl_notice');
		//
		change_crawl_status('resume');
	});
	//
	jQuery('body').on('keyup', '#cum_medias_keyword', function() {
		clearTimeout(sto_keyword);
		sto_keyword = setTimeout(function() {
			cum_list_medias();
		}, 600);
	});
	//
	jQuery('body').on('click', 'input[name="btn-select-medias"]', function() {
		if (jQuery(this).hasClass('selectall')) {
			jQuery(this).removeClass('selectall');
			jQuery('#fcum').find('.cum-medias-content input[type="checkbox"]').removeAttr('checked');
		}
		else {
			jQuery(this).addClass('selectall');
			jQuery('#fcum').find('.cum-medias-content input[type="checkbox"]').attr('checked', 'checked');
		}
	});
	//
	jQuery('body').on('click', 'input[name="btn-select-filters"]', function() {
		if (jQuery(this).hasClass('selectall')) {
			jQuery(this).removeClass('selectall');
			jQuery('#fcum').find('.cum-filters input[type="checkbox"]').removeAttr('checked');
		}
		else {
			jQuery(this).addClass('selectall');
			jQuery('#fcum').find('.cum-filters input[type="checkbox"]').attr('checked', 'checked');
		}
		cum_list_medias();
	});
	//
    jQuery('body').on('click', '.cum-nav .page-numbers', function() {
        var _str = jQuery(this).attr('href');
        var test = _str.match(/\/([0-9]+)$/);
        if (test && test[1]) { cum_list_medias(test[1]); }
        return false;
    });
	//
	jQuery('#fcum').submit(function() {
		//
		jQuery('input[name="btn-delete-medias"]').attr('disabled', 'disabled');
		jQuery('.result').html(cum_tools_translate.deleting_media_in_progress);
		//
		var params = {};
		params.action = 'cum_do_delete_medias';
		params.cum_do_delete_medias_nonce = jQuery('#cum_do_delete_medias_nonce').val();
		params.media_ids = [];
		jQuery('#fcum').find('.cum-medias-content input[type="checkbox"]:checked').each(function() { params.media_ids.push(jQuery(this).val()); });

		//
	    jQuery.ajax({
	        type: 'POST',
	        'url': ajaxurl,
	        'data': params,
	        'dataType': 'json',
	        'success': function(data) {
				jQuery('input[name="btn-delete-medias"]').removeAttr('disabled');
				if (data.error == 0) {
					//
					jQuery('.result').html(data.message);
					//
					if (data.media_ids_deleted.length > 0) {
						for (var i in data.media_ids_deleted) {
							jQuery('input[type="checkbox"][value="'+data.media_ids_deleted[i]+'"]').parent('label').parent('h3').parent('.cum-media').slideUp(300, function() {
								//
								jQuery(this).remove();

								//
								if (jQuery('.cum-media').length == 0) {
									//
									jQuery('.cum-medias').remove();
									//
									cum_list_medias();
								}
							});
						}
					}
				}
				else {
					cum_display_notice(data.message, 'error');
				}
			}
	    });
		return false;
	});
	//
	get_cum_crawl_medias();
});

//
function cum_list_medias(paged) {
	//
	var params = {};
	params.action = 'cum_list_medias';
	params.cum_medias_per_page = jQuery('#cum-medias-per-page').val();
	params.cum_list_medias_nonce = jQuery('#cum_list_medias_nonce').val();
	params.cum_medias_keyword = jQuery('#cum_medias_keyword').val();
	params.cum_medias_not_theme_customise = (jQuery('#cum_medias_not_theme_customise:checked').length > 0) ? 1 : 0;
	params.cum_medias_not_thumb = (jQuery('#cum_medias_not_thumb:checked').length > 0) ? 1 : 0;
	params.cum_medias_not_related = (jQuery('#cum_medias_not_related:checked').length > 0) ? 1 : 0;
	params.cum_medias_not_in_acf = (jQuery('#cum_medias_not_in_acf:checked').length > 0) ? 1 : 0;
	params.cum_medias_not_in_content = (jQuery('#cum_medias_not_in_content:checked').length > 0) ? 1 : 0;
	params.cum_medias_not_in_postmeta = (jQuery('#cum_medias_not_in_postmeta:checked').length > 0) ? 1 : 0;
	params.cum_medias_not_in_usermeta = (jQuery('#cum_medias_not_in_usermeta:checked').length > 0) ? 1 : 0;
	params.cum_medias_not_in_option = (jQuery('#cum_medias_not_in_option:checked').length > 0) ? 1 : 0;
	params.paged = (paged != undefined) ? paged : 0;
	jQuery('.cum-medias-content, p.cum-buttons').animate({ 'opacity': 0.4 }, 400);

	//
    jQuery.ajax({
        type: 'POST',
        'url': ajaxurl,
        'data': params,
        'dataType': 'json',
        'success': function(data) {
			jQuery('.cum-medias-content, p.cum-buttons').animate({ 'opacity': 1 }, 600);
			jQuery('.cum-medias-content').html(data.message);
			if (data.error == 1) {
				cum_display_notice(data.message, 'error');
			}
		}
    });
	return false;	
}

//
function get_cum_crawl_medias() {
	//
	clearTimeout(sto_crawl);

	//
	if (stop_crawl) { return false; };

	//
	var params = {};
	params.action = 'cum_crawl_medias';
	params.cum_crawl_medias_nonce = jQuery('#cum_crawl_medias_nonce').val();

	//
    jQuery.ajax({
        type: 'POST',
        'url': ajaxurl,
        'data': params,
        'dataType': 'json',
        'success': function(data) {
			if (data.error == 0) {
				if (data && data.complete == 1) {
					//
					stop_crawl = true;
					//
					cum_display_notice(data.message, 'success', 'cum_crawl_notice');
					//
					cum_list_medias();
				}
				else if (data && data.pause == 1) {
					//
					stop_crawl = true;
					//
					cum_display_notice(data.message, 'warning', 'cum_crawl_notice');
				}
				else {
					cum_display_notice(data.message, 'info', 'cum_crawl_notice');
				}
			}
			else {
				cum_display_notice(data.message, 'error');
			}
		},
		'complete': function(data) {
			//
			sto_crawl = setTimeout(get_cum_crawl_medias, 1000);

			//
			if (cum_list_medias_first) {
				cum_list_medias_first = false;
				cum_list_medias();
			}
		}
    });

	return false;	
}

//
function change_crawl_status(_action) {
	//
	var params = {};
	params.action = 'cum_crawl_medias_change_status';
	params.cum_crawl_medias_nonce = jQuery('#cum_crawl_medias_nonce').val();
	if (_action != undefined && _action == 'reset') { params.cum_crawl_medias_reset = 1; }
	if (_action != undefined && _action == 'pause') { params.cum_crawl_medias_pause = 1; }
	if (_action != undefined && _action == 'resume') { params.cum_crawl_medias_resume = 1; }

	//
    jQuery.ajax({
        type: 'POST',
        'url': ajaxurl,
        'data': params,
        'dataType': 'json',
        'success': function(data) {
			if (data.error == 0) {
				if (data.reset) {
					//
					stop_crawl = false;
					//
					get_cum_crawl_medias();
				}
				else if (data.resume) {
					//
					stop_crawl = false;
					//
					get_cum_crawl_medias();
				}
				else if (data.pause) {
					//
					// stop_crawl = true;
				}
			}
			else {
				cum_display_notice(data.message, 'error');
			}
		},
    });

	return false;	
}

function cum_display_details(_type, _id) {
	jQuery('.thickbox.cum-thickbox-launcher').click();

	jQuery('#TB_ajaxContent').html('<p>'+cum_tools_translate.loading+'</p>');

	//
	var params = {};
	params.action = 'cum_get_medias_used_in';
	params.media_id = _id;
	params.type = _type;
	params.cum_get_medias_used_in_nonce = jQuery('#cum_get_medias_used_in_nonce').val();

	//
    jQuery.ajax({
        type: 'POST',
        'url': ajaxurl,
        'data': params,
        'dataType': 'json',
        'success': function(data) {
			jQuery('#TB_ajaxContent').html(data.message);
		}
    });

	return false;	
}

//
function cum_display_notice(_msg, _type, _id) {
	var _type = (_type != undefined) ? _type : 'success';
	if (_id != undefined) {
		if (jQuery('#'+_id).length == 0) {
			var _html = '\
				<div class="cum-notice notice notice-'+_type+' is-dismissible" id="'+_id+'">\
					<p>'+_msg+'</p>\
					<button type="button" class="notice-dismiss"></button>\
				</div>\
			';
			jQuery(_html).insertAfter('#fcum > fieldset:first');
		}
		else {
			jQuery('#'+_id).removeClass('notice-warning notice-error notice-success notice-info');
			jQuery('#'+_id).addClass('notice-'+_type);
			jQuery('#'+_id).find('p:first').html(_msg);
			jQuery('#'+_id).stop().animate({ 'opacity': 0.4 }, 200, function() {
				jQuery('#'+_id).stop().animate({ 'opacity': 1 }, 200);
			});

		}
	}
	else {
		var _html = '\
			<div class="cum-notice notice notice-'+_type+' is-dismissible">\
				<p>'+_msg+'</p>\
				<button type="button" class="notice-dismiss"></button>\
			</div>\
		';
		jQuery(_html).insertAfter('#fcum > fieldset:first');
	}
}
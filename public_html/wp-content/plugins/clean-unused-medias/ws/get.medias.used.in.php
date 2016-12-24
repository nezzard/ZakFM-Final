<?php
	//
	if (!defined('ABSPATH')) exit; // Exit if accessed directly 

	//
	global $wp_query, $wpdb;

	//
	$error = 1;
	$message = __("Error. Parameters missing.", "cum-tools");

	//
	ob_start();

	if (!check_ajax_referer('cum_get_medias_used_in', 'cum_get_medias_used_in_nonce', false)) {
		$message = __("Error. Access denied.", "cum-tools");
		$message = "<p>".$message."</p>";
	}
	else if (empty($media_id) && empty($_POST['type'])) {
		$message = __("Error. Parameters missing.", "cum-tools");
		$message = "<p>".$message."</p>";
	}
	else {
		$error = 0;
		$message = "";
		//
		$media_id = $_POST['media_id'];

		//
		$media = get_post($media_id);

		// search if media URL or ID in content
		$media_full_url = wp_get_attachment_url($media_id);
    	$content_medias = array(basename($media_full_url));
    	foreach($cum_thumb_sizes as $thumb_size) {
			$thumb = wp_get_attachment_image_src($media_id, $thumb_size);
			if (isset($thumb[0]) && !empty($thumb[0])) {
				$thumb_basename = basename($thumb[0]);
				$content_medias[] = $thumb_basename;
			}
    	}

    	//
    	$cum_used_in = get_post_meta($media_id, 'cum_used_in', 1);
    	$cum_used_in = (!is_array($cum_used_in) || empty($cum_used_in)) ? array() : $cum_used_in;

		//
		$message .= "<h3>".$media->post_title."</h3>";

    	//
    	if (cum_is_favicon($media->ID)) { $cum_used_in[] = 'favicon'; }
    	if (cum_is_featured_media($media->ID)) { $cum_used_in[] = 'featured'; }
    	if (cum_is_related_media($media->ID)) { $cum_used_in[] = 'related'; }
        if (cum_is_ACF_media($media->ID)) { $cum_used_in[] = 'ACF'; }
        if (cum_is_media_in_content($media->ID)) { $cum_used_in[] = 'content'; }
        if (cum_is_media_in_postmeta($media->ID)) { $cum_used_in[] = 'postmeta'; }
        if (cum_is_media_in_usermeta($media->ID)) { $cum_used_in[] = 'usermeta'; }
        if (cum_is_media_in_option($media->ID)) { $cum_used_in[] = 'option'; }

        //
        $cum_used_in = array_unique($cum_used_in);

    	//
    	if (sizeof($cum_used_in) > 0) {
			$message .= "<p>".__('This media is:', 'cum-tools')."</p>";
    		$message .= "<ul>";
    		foreach($cum_used_in as $cum_tag) {
    			switch($cum_tag) {
    				case 'favicon':
    					$message .= "<li>".__('used as a favicon', 'cum-tools')."</li>";
    				break;
    				case 'featured':
    					$posts = cum_is_featured_media($media_id);
    					if (sizeof($posts) == 1) {
    						$post = get_post($posts[0]);
	    					$message .= "<li>".sprintf(__('a featured image of <a href="%s" target="_blank">%s</a>', 'cum-tools'), get_edit_post_link($post->ID), $post->post_title)."</li>";
    					}
    					else if (sizeof($posts) > 1) {
    						$message .= "<li>".__('a featured image of:', 'cum-tools');
	    					foreach($posts as $post_id) {
		    					$post = get_post($post_id);

								$message .= "<ul>";    						
		    					$message .= "<li>".sprintf(__('<a href="%s" target="_blank">%s</a>', 'cum-tools'), get_edit_post_link($post->ID), $post->post_title)."</li>";
								$message .= "</ul>";    						
	    					}
	    					$message .= "</li>";
    					}
    				break;
    				case 'related':
    					$post = get_post($media->post_parent);
    					
    					$message .= "<li>".sprintf(__('related to <a href="%s" target="_blank">%s</a>', 'cum-tools'), get_edit_post_link($post->ID), $post->post_title)."</li>";
    				break;
    				case 'ACF':
    					$posts = cum_is_ACF_media($media_id);
    					if (sizeof($posts) == 1) {
    						$post = get_post($posts[0]);
	    					$message .= "<li>".sprintf(__('used in an ACF of <a href="%s" target="_blank">%s</a>', 'cum-tools'), get_edit_post_link($post->ID), $post->post_title)."</li>";
    					}
    					else if (sizeof($posts) > 1) {
    						$message .= "<li>".__('used in an ACF of:', 'cum-tools');
	    					foreach($posts as $post_id) {
		    					$post = get_post($post_id);

								$message .= "<ul>";    						
		    					$message .= "<li>".sprintf(__('<a href="%s" target="_blank">%s</a>', 'cum-tools'), get_edit_post_link($post->ID), $post->post_title)."</li>";
								$message .= "</ul>";    						
	    					}
	    					$message .= "</li>";
    					}
    				break;
    				case 'content':
    					$posts = cum_is_media_in_content($media_id);
    					if (sizeof($posts) == 1) {
    						$post = get_post($posts[0]);
	    					$message .= "<li>".sprintf(__('used in the content of <a href="%s" target="_blank">%s</a>', 'cum-tools'), get_edit_post_link($post->ID), $post->post_title)."</li>";
    					}
    					else if (sizeof($posts) > 1) {
    						$message .= "<li>".__('used in the content of:', 'cum-tools');
	    					foreach($posts as $post_id) {
		    					$post = get_post($post_id);

								$message .= "<ul>";    						
		    					$message .= "<li>".sprintf(__('<a href="%s" target="_blank">%s</a>', 'cum-tools'), get_edit_post_link($post->ID), $post->post_title)."</li>";
								$message .= "</ul>";    						
	    					}
	    					$message .= "</li>";
    					}
    				break;
    				case 'postmeta':
    					$posts = cum_is_media_in_postmeta($media_id);
    					if (sizeof($posts) == 1) {
    						$post = get_post($posts[0]);
	    					$message .= "<li>".sprintf(__('used in the meta of <a href="%s" target="_blank">%s</a>', 'cum-tools'), get_edit_post_link($post->ID), $post->post_title)."</li>";
    					}
    					else if (sizeof($posts) > 1) {
    						$message .= "<li>".__('used in the meta of:', 'cum-tools');
	    					foreach($posts as $post_id) {
		    					$post = get_post($post_id);

								$message .= "<ul>";    						
		    					$message .= "<li>".sprintf(__('<a href="%s" target="_blank">%s</a>', 'cum-tools'), get_edit_post_link($post->ID), $post->post_title)."</li>";
								$message .= "</ul>";    						
	    					}
	    					$message .= "</li>";
    					}
    				break;
    				case 'usermeta':
    					$users = cum_is_media_in_usermeta($media_id);
    					if (sizeof($users) == 1) {
    						$User = new WP_User($users[0]);
	    					$message .= "<li>".sprintf(__('used in meta for user <a href="%s" target="_blank">%s</a>', 'cum-tools'), get_edit_user_link($users[0]), $User->display_name)."</li>";
    					}
    					else if (sizeof($users) > 1) {
    						$message .= "<li>".__('used in meta for the users:', 'cum-tools');
	    					foreach($users as $user_id) {
		    					$User = new WP_User($user_id);

								$message .= "<ul>";    						
		    					$message .= "<li>".sprintf(__('<a href="%s" target="_blank">%s</a>', 'cum-tools'), get_edit_user_link($user_id), $User->display_name)."</li>";
								$message .= "</ul>";    						
	    					}
	    					$message .= "</li>";
    					}
    				break;
    				case 'option':
    					$options = cum_is_media_in_option($media_id);
    					if (sizeof($options) == 1) {
    						$result = $options[0];
	    					$message .= "<li>".sprintf(__('used in the option <strong>`%s`</strong>', 'cum-tools'), $result->option_name)."</li>";
    					}
    					else if (sizeof($options) > 1) {
    						$message .= "<li>".__('used in the options:', 'cum-tools');
	    					foreach($options as $result) {

								$message .= "<ul>";    						
		    					$message .= "<li>".sprintf(__('<strong>`%s`</strong>', 'cum-tools'), $result->option_name)."</li>";
								$message .= "</ul>";    						
	    					}
	    					$message .= "</li>";
    					}
    				break;
    			}
    		}
    		$message .= "</ul>";
    	}
    	else {
			$message .= "<p>".__('This media is used anywhere.', 'cum-tools')."</p>";
    	}
	}

	//
	$debug = ob_get_contents();
	ob_end_clean();

    //
	header("Content-Type: text/javascript");

	//
	$array = array(
		"error" => $error,
  		"message" => $message,
  		"debug" => $debug,
	);

	//
	echo json_encode($array);

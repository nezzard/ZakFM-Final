<?php
	//
	if (!defined('ABSPATH')) exit; // Exit if accessed directly 

	$medias_meta_keys = array(-1);

	// -----------------------------------------------------------------------------------------------------------------------------------------------------------
	// add cron
	// -----------------------------------------------------------------------------------------------------------------------------------------------------------
	add_action('init', 'ini_cron_cum_check_medias_in_content');
	function ini_cron_cum_check_medias_in_content() {
	    if (!wp_next_scheduled('cum_check_medias_in_content')) {
	        wp_schedule_event(time(), 'minutely', 'cum_check_medias_in_content');
	    }
        if (isset($_GET) && isset($_GET['cumcheck'])) { cron_cum_check_medias_in_content(); }
	}

	//
	add_action('cum_check_medias_in_content', 'cron_cum_check_medias_in_content_test');
	function cron_cum_check_medias_in_content_test() {
		if (!cum_get_option('cum_medias_in_content_pause') && !cum_get_option('cum_medias_in_content_completed')) {
			cron_cum_check_medias_in_content();
		}
	}

	//
	function cron_cum_check_medias_in_content() {
        //
        global $wpdb, $wp;

        //
        $cum_medias_to_check = 2;
        $cum_medias_in_content_index = cum_get_option('cum_medias_in_content_index');
        $cum_medias_in_content_index = (empty($cum_medias_in_content_index)) ? 0 : $cum_medias_in_content_index;
        //
        $cum_medias_in_content_ids = cum_get_option('cum_medias_in_content_ids');
        $cum_medias_in_content_ids = (empty($cum_medias_in_content_index)) ? array() : unserialize($cum_medias_in_content_ids);
        $cum_medias_in_postmeta_ids = cum_get_option('cum_medias_in_postmeta_ids');
        $cum_medias_in_postmeta_ids = (empty($cum_medias_in_postmeta_ids)) ? array() : unserialize($cum_medias_in_postmeta_ids);
        $cum_medias_in_usermeta_ids = cum_get_option('cum_medias_in_usermeta_ids');
        $cum_medias_in_usermeta_ids = (empty($cum_medias_in_usermeta_ids)) ? array() : unserialize($cum_medias_in_usermeta_ids);
        $cum_medias_in_option_ids = cum_get_option('cum_medias_in_option_ids');
        $cum_medias_in_option_ids = (empty($cum_medias_in_option_ids)) ? array() : unserialize($cum_medias_in_option_ids);
        //
        $cum_medias_in_content_last_check = cum_get_option('cum_medias_in_content_last_check');
        $cum_medias_in_content_pause = cum_get_option('cum_medias_in_content_pause');
        $cum_medias_in_content_completed = cum_get_option('cum_medias_in_content_completed');
        $cum_medias_in_content_completed_date = cum_get_option('cum_medias_in_content_completed_date');
        $cum_medias_in_content_processing = cum_get_option('cum_medias_in_content_processing');

        //
        if (!empty($cum_medias_in_content_completed)) {
        	return false;
        }

		//
		$cum_post_types = array();
		$cum_get_post_types = get_post_types('', 'names');
		foreach($cum_get_post_types as $cum_post_type) { $cum_post_types[] = "'".$cum_post_type."'"; }
		$cum_thumb_sizes = get_intermediate_image_sizes();

        //
        $sql = "
        	SELECT DISTINCT `ID`
        	FROM `".$wpdb->posts."`
        	WHERE
        		`post_type` = 'attachment'
    		ORDER BY `post_date_gmt` DESC
    	";
        $all_media_ids = $wpdb->get_col($sql);
        $total = sizeof($all_media_ids);

        //
        $sql .= "LIMIT ".$cum_medias_in_content_index.", ".$cum_medias_to_check;
        $media_ids = $wpdb->get_col($sql);

        if (sizeof($media_ids) > 0) {
        	foreach($media_ids as $media_id) {
        		//
        		$cum_used_in = get_post_meta($media_id, 'cum_used_in', 1);
        		$cum_used_in = (!is_array($cum_used_in) || empty($cum_used_in)) ? array() : $cum_used_in;

        		// search if media URL or ID in content
        		$media_full_url = wp_get_attachment_url($media_id);
		    	$content_medias = array(basename($media_full_url));
		    	foreach($cum_thumb_sizes as $thumb_size) {
	    			$thumb = wp_get_attachment_image_src($media_id, $thumb_size);
	    			if (isset($thumb[0]) && !empty($thumb[0])) {
	    				// $content_medias[] = str_replace("/", "\/", $thumb[0]);
	    				$thumb_basename = basename($thumb[0]);
	    				$content_medias[] = $thumb_basename;
	    			}
	        	}
	        	//
		        $sql = "
		        	SELECT DISTINCT `ID`
		        	FROM `".$wpdb->posts."`
		        	WHERE
		        		`post_type` IN (".implode(',', $cum_post_types).")
		        	AND (
		        			`post_content` REGEXP '".implode('|', $content_medias)."'
		        		OR  `post_content` REGEXP 'gallery(.*)ids=(\"|,){1}[ ,0-9]*(".$media_id.")(\"|,){1}'
		        	)
		        	AND `post_status` IN ('publish')
		    		ORDER BY `post_date_gmt` DESC
		        ";
		        $posts_content_medias = $wpdb->get_col($sql);
		        if (sizeof($posts_content_medias) > 0) {
		        	$cum_medias_in_content_ids[] = $media_id;
		        	$cum_used_in[] = 'content';
		        }

		        // search if media URL is in `wp_options`
		        $sql = "
		        	SELECT DISTINCT `option_id`
		        	FROM `".$wpdb->options."`
		        	WHERE
		        		`option_value` REGEXP '".implode('|', $content_medias)."'
			    	AND `option_name` NOT IN ('_transient_jetpack_sitemap')
		        ";
		        $options_medias = $wpdb->get_col($sql);
		        if (sizeof($options_medias) > 0) {
		        	$cum_medias_in_option_ids[] = $media_id;
		        	$cum_used_in[] = 'option';
		        }

		        // search if media URL is in `wp_postmeta`
		        // $sql = "SELECT DISTINCT `meta_value` FROM `".$wpdb->postmeta."` WHERE `meta_key` = '_thumbnail_id' AND `meta_value` = '".$media_id."'";
		        // $featured_media = $wpdb->get_col($sql);
		        // if (sizeof($featured_media) == 0) {
			        $sql = "
			        	SELECT DISTINCT `meta_id`
			        	FROM `".$wpdb->postmeta."`
			        	WHERE
			        		`meta_value` REGEXP '".implode('|', $content_medias)."'
			        	AND `meta_key` NOT IN ('_wp_attached_file', '_wp_attachment_metadata', '_original_filename', '_imagify_data')
			        ";
			        $postmetas_medias = $wpdb->get_col($sql);
			        if (sizeof($postmetas_medias) > 0) {
			        	$cum_medias_in_postmeta_ids[] = $media_id;
			        	$cum_used_in[] = 'postmeta';
			        }
		        // }

		        // search if media URL is in `wp_usermeta`
		        $sql = "
		        	SELECT DISTINCT `umeta_id`
		        	FROM `".$wpdb->usermeta."`
		        	WHERE
		        		`meta_value` REGEXP '".implode('|', $content_medias)."'
		        ";
		        $usermetas_medias = $wpdb->get_col($sql);
		        if (sizeof($usermetas_medias) > 0) {
		        	$cum_medias_in_usermeta_ids[] = $media_id;
		        	$cum_used_in[] = 'usermeta';
		        }

	        	//
	        	update_post_meta($media_id, 'cum_used_in', $cum_used_in);
        	}
        }

        //
        $cum_medias_in_content_index_next = $cum_medias_in_content_index+$cum_medias_to_check;
        if (($cum_medias_in_content_index_next) >= $total) {
        	$cum_medias_in_content_completed = 1;
        	$cum_medias_in_content_completed_date = date("Y-m-d H:i:s");
        }

        //
        cum_update_option('cum_medias_in_content_index', $cum_medias_in_content_index_next);
        cum_update_option('cum_medias_in_content_ids', serialize($cum_medias_in_content_ids));
        cum_update_option('cum_medias_in_option_ids', serialize($cum_medias_in_option_ids));
        cum_update_option('cum_medias_in_postmeta_ids', serialize($cum_medias_in_postmeta_ids));
        cum_update_option('cum_medias_in_usermeta_ids', serialize($cum_medias_in_usermeta_ids));
        cum_update_option('cum_medias_in_content_last_check', date('Y-m-d H:i:s'));
        cum_update_option('cum_medias_in_content_completed', $cum_medias_in_content_completed);
        cum_update_option('cum_medias_in_content_completed_date', $cum_medias_in_content_completed_date);
   }	


	// -----------------------------------------------------------------------------------------------------------------------------------------------------------
	// init constant ACF fields
	// -----------------------------------------------------------------------------------------------------------------------------------------------------------
	function cum_define_medias_meta_keys() {
		global $wpdb, $medias_meta_keys;

    	//
        $sql = "
        	SELECT DISTINCT `post_name`
        	FROM `".$wpdb->posts."`
        	WHERE
        		`post_type` = 'acf-field'
        	AND (`post_content` LIKE '%s:4:\"type\";s:5:\"image\";%' OR `post_content` LIKE '%s:4:\"type\";s:4:\"file\";%')
    	";
        $medias_acf_field_keys = $wpdb->get_col($sql);
    	//
        $sql = "
        	SELECT DISTINCT CONCAT('\'', SUBSTRING(`meta_key`, 2, CHAR_LENGTH(`meta_key`)), '\'') as `meta_key`
        	FROM `".$wpdb->postmeta."`
        	WHERE
        	`meta_value` REGEXP '".implode('|', $medias_acf_field_keys)."'
        ";
        $medias_meta_keys = $wpdb->get_col($sql);
        $medias_meta_keys = (!is_array($medias_meta_keys) || empty($medias_meta_keys)) ? array(-1) : $medias_meta_keys;
	}
	add_action('init', 'cum_define_medias_meta_keys');


	// -----------------------------------------------------------------------------------------------------------------------------------------------------------
	// custom get_option
	// -----------------------------------------------------------------------------------------------------------------------------------------------------------
	function cum_get_option($name) {
		global $wpdb;

		$result = $wpdb->get_col("SELECT `option_value` FROM `".$wpdb->options."` WHERE `option_name` = '".$name."'");
		if (isset($result[0])) {
			return $result[0];
		}
		else {
			return null;
		}
	}


	// -----------------------------------------------------------------------------------------------------------------------------------------------------------
	// custom update_option
	// -----------------------------------------------------------------------------------------------------------------------------------------------------------
	function cum_update_option($name, $value) {
		global $wpdb;

		$check = $wpdb->get_col("SELECT `option_value` FROM `".$wpdb->options."` WHERE `option_name` = '".$name."'");
		if (sizeof($check) == 0) {
	        $wpdb->query("INSERT INTO `".$wpdb->options."` (`option_name`, `option_value`) VALUES ('".$name."', '".$value."')");
		}
		else {
	        $wpdb->query("UPDATE `".$wpdb->options."` SET `option_value` = '".$value."' WHERE `option_name` = '".$name."'");
		}
	}


	// -----------------------------------------------------------------------------------------------------------------------------------------------------------
	// is favicon
	// -----------------------------------------------------------------------------------------------------------------------------------------------------------
	function cum_is_favicon($media_id) {
    	$media_favicon = cum_get_option('site_icon');
    	if (!empty($media_favicon) && $media_favicon == $media_id) { return true; } else { return false; }
	}


	// -----------------------------------------------------------------------------------------------------------------------------------------------------------
	// is featured media
	// -----------------------------------------------------------------------------------------------------------------------------------------------------------
	function cum_is_featured_media($media_id) {
		global $wpdb;

        $sql = "SELECT DISTINCT `post_id` FROM `".$wpdb->postmeta."` WHERE `meta_key` = '_thumbnail_id' AND `meta_value` = '".$media_id."'";
        $featured_post = $wpdb->get_col($sql);
        if (sizeof($featured_post) > 0) { return $featured_post; } else { return false; }
	}


	// -----------------------------------------------------------------------------------------------------------------------------------------------------------
	// is related media
	// -----------------------------------------------------------------------------------------------------------------------------------------------------------
	function cum_is_related_media($media_id) {
		global $wpdb;

        $sql = "SELECT DISTINCT `post_parent` FROM `".$wpdb->posts."` WHERE `ID` = '".$media_id."' AND `post_parent` > 0 AND `post_type` = 'attachment'";
        $post_parent = $wpdb->get_col($sql);
        if (sizeof($post_parent) > 0) { return $post_parent; } else { return false; }
	}


	// -----------------------------------------------------------------------------------------------------------------------------------------------------------
	// is ACF media
	// -----------------------------------------------------------------------------------------------------------------------------------------------------------
	function cum_is_ACF_media($media_id) {
		global $wpdb, $medias_meta_keys;
    	//
        $sql = "
        	SELECT DISTINCT `post_id`
        	FROM `".$wpdb->postmeta."`
        	WHERE
        		`meta_key` IN (".implode(',', $medias_meta_keys).")
        	AND `meta_value` = '".$media_id."'
        ";
        // echo $sql;
        $posts_acf = $wpdb->get_col($sql);
        if (sizeof($posts_acf) > 0) { return $posts_acf; } else { return false; }
	}


	// -----------------------------------------------------------------------------------------------------------------------------------------------------------
	// is media in content
	// -----------------------------------------------------------------------------------------------------------------------------------------------------------
	function cum_is_media_in_content($media_id) {
		global $wpdb;
		//
		$cum_post_types = array();
		$cum_get_post_types = get_post_types('', 'names');
		foreach($cum_get_post_types as $cum_post_type) { $cum_post_types[] = "'".$cum_post_type."'"; }
		//
		$cum_thumb_sizes = get_intermediate_image_sizes();
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
        $sql = "
        	SELECT DISTINCT `ID`
        	FROM `".$wpdb->posts."`
        	WHERE
        		`post_type` IN (".implode(',', $cum_post_types).")
        	AND (
        			`post_content` REGEXP '".implode('|', $content_medias)."'
        		OR  `post_content` REGEXP 'gallery(.*)ids=(\"|,){1}[ ,0-9]*(".$media_id.")(\"|,){1}'
        	)
        	AND `post_status` IN ('publish')
    		ORDER BY `post_date_gmt` DESC
        ";
        $posts_content_medias = $wpdb->get_col($sql);
        if (sizeof($posts_content_medias) > 0) { return $posts_content_medias; } else { return false; }
	}


	// -----------------------------------------------------------------------------------------------------------------------------------------------------------
	// is media used in postmeta
	// -----------------------------------------------------------------------------------------------------------------------------------------------------------
	function cum_is_media_in_postmeta($media_id) {
		global $wpdb;
		//
		$cum_thumb_sizes = get_intermediate_image_sizes();
		//
        // $sql = "SELECT DISTINCT `meta_value` FROM `".$wpdb->postmeta."` WHERE `meta_key` = '_thumbnail_id' AND `meta_value` = '".$media_id."'";
        // $featured_media = $wpdb->get_col($sql);
        // if (sizeof($featured_media) == 0) {
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
	        $sql = "
	        	SELECT DISTINCT `post_id`
	        	FROM `".$wpdb->postmeta."`
	        	WHERE
	        		`meta_value` REGEXP '".implode('|', $content_medias)."'
	        	AND `meta_key` NOT IN ('_wp_attached_file', '_wp_attachment_metadata', '_original_filename', '_imagify_data')
	        ";
	        $postmetas_medias = $wpdb->get_col($sql);
	        if (sizeof($postmetas_medias) > 0) { return $postmetas_medias; } else { return false; }
        // }
        // else {
        // 	return false;
        // }
	}


	// -----------------------------------------------------------------------------------------------------------------------------------------------------------
	// is media used in usermeta
	// -----------------------------------------------------------------------------------------------------------------------------------------------------------
	function cum_is_media_in_usermeta($media_id) {
		global $wpdb;
		//
		$cum_thumb_sizes = get_intermediate_image_sizes();
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
        $sql = "
        	SELECT DISTINCT `user_id`
        	FROM `".$wpdb->usermeta."`
        	WHERE
        		`meta_value` REGEXP '".implode('|', $content_medias)."'
        ";
        $usermetas_medias = $wpdb->get_col($sql);
        if (sizeof($usermetas_medias) > 0) { return $usermetas_medias; } else { return false; }
	}


	// -----------------------------------------------------------------------------------------------------------------------------------------------------------
	// is media used in option
	// -----------------------------------------------------------------------------------------------------------------------------------------------------------
	function cum_is_media_in_option($media_id) {
		global $wpdb;
		//
		$cum_thumb_sizes = get_intermediate_image_sizes();
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
	    $sql = "
	    	SELECT DISTINCT `option_name`, `option_value`
	    	FROM `".$wpdb->options."`
	    	WHERE
	    		`option_value` REGEXP '".implode('|', $content_medias)."'
	    	AND `option_name` NOT IN ('_transient_jetpack_sitemap')
	    ";
	    $options_medias = $wpdb->get_results($sql);
        if (sizeof($options_medias) > 0) { return $options_medias; } else { return false; }
	}


	// -----------------------------------------------------------------------------------------------------------------------------------------------------------
	// add admin js
	// -----------------------------------------------------------------------------------------------------------------------------------------------------------
	function cum_add_admin_js($hook) {
		//
		$screen = get_current_screen();
		//
 		if (isset($screen->id) && in_array($screen->id, array('media_page_cum-tools', 'upload'))) {
	 		wp_enqueue_script('cum_admin_js', WP_PLUGIN_URL.'/clean-unused-medias/admin.js', array('jquery'));
 		}
		//
		$translation_array = array(
			'deleting_media_in_progress' => __('Deleting medias in progress...', 'cum-tools'),
			'relaunch_crawl' => __('Relaunching crawl...', 'cum-tools'),
			'pause_crawl' => __('Stopping crawl...', 'cum-tools'),
			'resume_crawl' => __('Resuming crawl...', 'cum-tools'),
			'crawl_launch' => __('Launching crawl...', 'cum-tools'),
			'loading' => __('Loading...', 'cum-tools'),
		);
		wp_localize_script('cum_admin_js', 'cum_tools_translate', $translation_array);
	}
	add_action('admin_print_scripts', 'cum_add_admin_js');


	// -----------------------------------------------------------------------------------------------------------------------------------------------------------
	// add admin css
	// -----------------------------------------------------------------------------------------------------------------------------------------------------------
	function cum_add_admin_css() {
		$screen = get_current_screen();
 		if (isset($screen->id) && in_array($screen->id, array('media_page_cum-tools', 'upload'))) {
			wp_enqueue_style('cum_admin_css', WP_PLUGIN_URL.'/clean-unused-medias/admin.css');
		}
	}
	add_action('admin_print_styles', 'cum_add_admin_css');


	// -----------------------------------------------------------------------------------------------------------------------------------------------------------
	// add 
	// -----------------------------------------------------------------------------------------------------------------------------------------------------------
	function cum_admin_footer() {
		//
		$screen = get_current_screen();
		//
 		if (isset($screen->id) && $screen->id == 'upload') {
	    //
		add_thickbox();
		//
		wp_nonce_field('cum_get_medias_used_in', 'cum_get_medias_used_in_nonce');
?>
		<div id="cum-popin-details" class="cum-popin"></div>
		<a href="#TB_inline?width=600&height=400&inlineId=cum-popin-details" class="thickbox cum-thickbox-launcher"><span><?php _e('Launch Thickbox', 'cum-tools');?></span></a>
<?php
 		}
	}
	add_action('admin_footer', 'cum_admin_footer');


	// -----------------------------------------------------------------------------------------------------------------------------------------------------------
	// add submenu to medias
	// -----------------------------------------------------------------------------------------------------------------------------------------------------------
	function cum_submenu_add_tools() {
	    add_submenu_page('upload.php', __('CUM tools', 'cum-tools'), __('CUM tools', 'cum-tools'), 'manage_options', 'cum-tools', 'cum_tools');

	}
	add_action('admin_menu', 'cum_submenu_add_tools');


	// -----------------------------------------------------------------------------------------------------------------------------------------------------------
	// add column in media list
	// -----------------------------------------------------------------------------------------------------------------------------------------------------------
	// Add col
	function cum_add_column($cols) {
        $cols["cum-tools"] = __('CUM Tools', 'cum-tools');

        return $cols;
	}
	// Display
	function cum_display_col_content($column_name, $id) {
		//
	    if ($column_name == 'cum-tools') {
	    	//
	    	$cum_used_in = get_post_meta($id, 'cum_used_in', 1);
	    	$cum_used_in = (!is_array($cum_used_in) || empty($cum_used_in)) ? array() : $cum_used_in;
	    	//
	    	if (cum_is_favicon($id)) { $cum_used_in[] = 'favicon'; }
	    	if (cum_is_featured_media($id)) { $cum_used_in[] = 'featured'; }
	    	if (cum_is_related_media($id)) { $cum_used_in[] = 'related'; }
	    	if (cum_is_ACF_media($id)) { $cum_used_in[] = 'ACF'; }
	    	//
	    	sort($cum_used_in);
	    	//
	    	$cum_used_in = array_unique($cum_used_in);
	    	//
	    	if (sizeof($cum_used_in) > 0) {
	    		$cum_used_in_display = array();
	    		foreach($cum_used_in as $cum_tag) {
	    			$cum_used_in_display[] = "<a href=\"#".$cum_tag."\" class=\"cum-tag\" data-media-id=\"".$id."\">#".$cum_tag."</a>";
	    		}
	    		echo implode(" ", $cum_used_in_display);
	    	}
	    	else {
	    		echo "<a href=\"#nope\" class=\"cum-tag\" data-media-id=\"".$id."\">".__('Check it', 'cum-tools')."</a>";    		
	    	}
	    }
	}
	// Hook actions to admin_init
	function cum_hook_add_column() {
	    add_filter('manage_media_columns', 'cum_add_column');
	    add_action('manage_media_custom_column', 'cum_display_col_content', 10, 2);
	}
	add_action('admin_init', 'cum_hook_add_column');


	// -----------------------------------------------------------------------------------------------------------------------------------------------------------
	// submenu cum tools page
	// -----------------------------------------------------------------------------------------------------------------------------------------------------------
	function cum_tools() {
	    global $wpdb;

	    //
		add_thickbox();
?>
		<div id="cum-popin-details" class="cum-popin"></div>
		<a href="#TB_inline?width=600&height=400&inlineId=cum-popin-details" class="thickbox cum-thickbox-launcher"><span><?php _e('Launch Thickbox', 'cum-tools');?></span></a>

	    <div class="wrap">  
<?php
			if (!isset($_COOKIE['cum-warning-hide'])) {
?>
			<div class="notice notice-warning is-dismissible" id="cum-warning">
				<p>
					<?php _e('Delete the medias is irreversible. Take precautions by making a backup and / or by being sure you really want to do it.', 'cum-tools');?>
				</p>
				<button type="button" class="notice-dismiss"></button>
			</div>
<?php
			}
?>


	        <form id="fcum" name="fcum" method="post" action="upload.php?page=cum-tools">  
				<?php wp_nonce_field('cum_get_medias_used_in', 'cum_get_medias_used_in_nonce');?>
				<?php wp_nonce_field('cum_list_medias', 'cum_list_medias_nonce');?>
				<?php wp_nonce_field('cum_do_delete_medias', 'cum_do_delete_medias_nonce');?>
				<?php wp_nonce_field('cum_crawl_medias', 'cum_crawl_medias_nonce');?>
	            <h2><?php _e('Clean Unused Medias', 'cum-tools');?></h2>  
	            <fieldset>
	            	<legend><?php _e("Filters", "cum-tools");?></legend>
		            <div class="cum-filters">
		            	<ul>
		            		<li class="pagination">
		            			<?php _e('Medias per page', 'cum-tools');?>
		            			<select id="cum-medias-per-page" name="cum-medias-per-page">
		            				<!-- <option value="2">2</option> -->
		            				<!-- <option value="6">6</option> -->
		            				<option value="12">12</option>
		            				<option value="48">48</option>
		            				<option value="96">96</option>
		            			</select>
		            		</li>
		            		<li class="keyword">
		            			<input type="text" id="cum_medias_keyword" name="cum_media_keyword" value="" placeholder="<?php _e('Filter with keywords', 'cum-tools');?>" />
		            		</li>
		            		<li class="filters">
								<input type="button" class="button selectall" name="btn-select-filters" value="<?php _e('Check / Uncheck filters', 'cum-tools');?>" />
		            		</li>
		            		<li>
		            			<label><input type="checkbox" id="cum_medias_not_theme_customise" name="cum_medias_not_theme_customise" value="1" checked="checked" /><?php _e("Not the site favicon", "cum-tools");?></label>
		            		</li>
		            		<li>
		            			<label><input type="checkbox" id="cum_medias_not_thumb" name="cum_medias_not_thumb" value="1" checked="checked" /><?php _e("Not a post thumbnail", "cum-tools");?></label>
		            		</li>
		            		<li>
		            			<label><input type="checkbox" id="cum_medias_not_related" name="cum_medias_not_related" value="1" checked="checked" /><?php _e("Not related to a post", "cum-tools");?></label>
		            		</li>
		            		<li>
		            			<label><input type="checkbox" id="cum_medias_not_in_acf" name="cum_medias_not_in_acf" value="1" checked="checked" /><?php _e("Not in ACF fields", "cum-tools");?></label>
		            		</li>
		            		<li>
		            			<label>
		            				<input type="checkbox" id="cum_medias_not_in_content" name="cum_medias_not_in_content" value="1" checked="checked" /><?php _e("Not in post's / page's / custom post type's content", "cum-tools");?> <sup>(1)</sup>
		            			</label>		            			
		            		</li>
		            		<li>
		            			<label>
		            				<input type="checkbox" id="cum_medias_not_in_postmeta" name="cum_medias_not_in_postmeta" value="1" checked="checked" /><?php _e("Not in post's / page's / custom post type's metas", "cum-tools");?> <sup>(1)</sup>
		            			</label>		            			
		            		</li>
		            		<li>
		            			<label>
		            				<input type="checkbox" id="cum_medias_not_in_usermeta" name="cum_medias_not_in_usermeta" value="1" checked="checked" /><?php _e("Not in user's metas", "cum-tools");?> <sup>(1)</sup>
		            			</label>		            			
		            		</li>
		            		<li>
		            			<label>
		            				<input type="checkbox" id="cum_medias_not_in_option" name="cum_medias_not_in_option" value="1" checked="checked" /><?php _e("Not in site's options", "cum-tools");?> <sup>(1)</sup>
		            			</label>		            			
		            		</li>
		            	</ul>
        				<span id="cum-crawling-status"> <sup>(1)</sup> <?php _e("Those features need to crawl all the post's contents, post's metas, user's metas and site's options. It may takes some times.", "cum-tools");?></span>
		            </div>
	            </fieldset>
	            <hr />
	            <div class="cum-medias-content"><?php _e('Loading ...', 'cum-tools');?></div>
		        <hr class="clear" />
	            <p class="cum-buttons clear">
					<input type="button" class="button" name="btn-select-medias" value="<?php _e('Check / Uncheck all', 'cum-tools');?>" />
	                <input type="submit" class="button button-primary" name="btn-delete-medias" value="<?php _e('Delete selected medias', 'cum-tools');?>" />
		            <span class="result"></span>
	            </p>
	        </form>
	    </div>  
<?php
	}


	// -----------------------------------------------------------------------------------------------------------------------------------------------------------
	// ajax : list medias
	// -----------------------------------------------------------------------------------------------------------------------------------------------------------
	function cum_list_medias() {
	    include CUM_PLUGIN_DIR."/ws/list.medias.php";
	    exit;
	}
	add_action('wp_ajax_cum_list_medias', 'cum_list_medias');
	add_action('wp_ajax_nopriv_cum_list_medias', 'cum_list_medias');


	// -----------------------------------------------------------------------------------------------------------------------------------------------------------
	// ajax : delete medias
	// -----------------------------------------------------------------------------------------------------------------------------------------------------------
	function cum_do_delete_medias() {
	    include CUM_PLUGIN_DIR."/ws/do.clean.medias.php";
	    exit;
	}
	add_action('wp_ajax_cum_do_delete_medias', 'cum_do_delete_medias');
	add_action('wp_ajax_nopriv_cum_do_delete_medias', 'cum_do_delete_medias');


	// -----------------------------------------------------------------------------------------------------------------------------------------------------------
	// ajax : crawl medias status
	// -----------------------------------------------------------------------------------------------------------------------------------------------------------
	function cum_crawl_medias() {
	    include CUM_PLUGIN_DIR."/ws/crawl.medias.php";
	    exit;
	}
	add_action('wp_ajax_cum_crawl_medias', 'cum_crawl_medias');
	add_action('wp_ajax_nopriv_cum_crawl_medias', 'cum_crawl_medias');


	// -----------------------------------------------------------------------------------------------------------------------------------------------------------
	// ajax : crawl medias change status
	// -----------------------------------------------------------------------------------------------------------------------------------------------------------
	function cum_crawl_medias_change_status() {
	    include CUM_PLUGIN_DIR."/ws/crawl.change.status.php";
	    exit;
	}
	add_action('wp_ajax_cum_crawl_medias_change_status', 'cum_crawl_medias_change_status');
	add_action('wp_ajax_nopriv_cum_crawl_medias_change_status', 'cum_crawl_medias_change_status');


	// -----------------------------------------------------------------------------------------------------------------------------------------------------------
	// ajax : get medias used in
	// -----------------------------------------------------------------------------------------------------------------------------------------------------------
	function cum_get_medias_used_in() {
	    include CUM_PLUGIN_DIR."/ws/get.medias.used.in.php";
	    exit;
	}
	add_action('wp_ajax_cum_get_medias_used_in', 'cum_get_medias_used_in');
	add_action('wp_ajax_nopriv_cum_get_medias_used_in', 'cum_get_medias_used_in');



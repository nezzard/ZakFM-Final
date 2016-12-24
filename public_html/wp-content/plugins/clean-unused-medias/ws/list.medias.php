<?php
	//
	if (!defined('ABSPATH')) exit; // Exit if accessed directly 

	//
	global $wp_query, $wpdb, $medias_meta_keys;

	//
	$cum_medias_per_page = (!empty($_POST['cum_medias_per_page'])) ? $_POST['cum_medias_per_page'] : 12;
	$medias_used = array(-1);
	$medias_keyword = array();
	$sql_keyword = "";

	//
	if (empty($_POST['paged']) || $_POST['paged'] == 1) {
		$paged = 1;
		$offset = 0;
	}
	else {
		$paged = $_POST['paged'];
		$offset = ($_POST['paged']-1)*$cum_medias_per_page;
	}

	//
	$error = 1;
	$message_error = __("Error. Parameters missing.", "cum-tools");
	$message = "";

	//
	ob_start();

	if (!check_ajax_referer('cum_list_medias', 'cum_list_medias_nonce', false)) {
		$message_error = __("Error. Access denied.", "cum-tools");
	}
	else {
		//
		$error = 0;
		$message_error = "";

		// keyword
		if (!empty($_POST['cum_medias_keyword'])) {
			$medias_keyword = array(-1);
	        //
	        $sql = "
	        	SELECT DISTINCT `ID`
	        	FROM `".$wpdb->posts."`
	        	WHERE
	        		`post_type` = 'attachment'
	        	AND (
	        			`post_title` LIKE '%".$_POST['cum_medias_keyword']."%'
	        		OR	`post_name` LIKE '%".$_POST['cum_medias_keyword']."%'
	        		OR	`post_content` LIKE '%".$_POST['cum_medias_keyword']."%'
	        	)
	    		ORDER BY `post_date_gmt` DESC
	        ";
	        $medias_keyword = array_merge($medias_keyword, $wpdb->get_col($sql));
        	$sql_keyword = " AND `ID` IN (".implode(',', $medias_keyword).")";
		}

		// medias favicon
		$medias_favicon = array();
        if (!empty($_POST['cum_medias_not_theme_customise'])) {
			$media_favicon = cum_get_option('site_icon');
			if (!empty($media_favicon)) $medias_favicon[] = $media_favicon;
		}

        // featured medias
        $featured_medias = array();
        if (!empty($_POST['cum_medias_not_thumb'])) {
	        $sql = "SELECT DISTINCT `meta_value` FROM `".$wpdb->postmeta."` WHERE `meta_key` = '_thumbnail_id'";
	        $featured_medias = $wpdb->get_col($sql);
        }

        // related medias
        $related_medias = array();
        if (!empty($_POST['cum_medias_not_related'])) {
	        $sql = "SELECT DISTINCT `ID` FROM `".$wpdb->posts."` WHERE `post_parent` > 0 AND `post_type` = 'attachment'";
	        $related_medias = $wpdb->get_col($sql);
        }

        // medias in content
        $in_content_medias = array();
        if (!empty($_POST['cum_medias_not_in_content'])) {
	        $in_content_medias = cum_get_option('cum_medias_in_content_ids');
	        $in_content_medias = (empty($in_content_medias)) ? array() : unserialize($in_content_medias);
	    }

        // medias in postmeta
        $in_postmeta_medias = array();
        if (!empty($_POST['cum_medias_not_in_postmeta'])) {
	        $in_postmeta_medias = cum_get_option('cum_medias_in_postmeta_ids');
	        $in_postmeta_medias = (empty($in_postmeta_medias)) ? array() : unserialize($in_postmeta_medias);
	    }

        // medias in usermeta
        $in_usermeta_medias = array();
        if (!empty($_POST['cum_medias_not_in_usermeta'])) {
	        $in_usermeta_medias = cum_get_option('cum_medias_in_usermeta_ids');
	        $in_usermeta_medias = (empty($in_usermeta_medias)) ? array() : unserialize($in_usermeta_medias);
	    }

        // medias in option
        $in_option_medias = array();
        if (!empty($_POST['cum_medias_not_in_option'])) {
	        $in_option_medias = cum_get_option('cum_medias_in_option_ids');
	        $in_option_medias = (empty($in_option_medias)) ? array() : unserialize($in_option_medias);
	    }

        // medias in ACF
        $acf_medias = array();
        if (!empty($_POST['cum_medias_not_in_acf'])) {
        	//
	        $sql = "
	        	SELECT DISTINCT `meta_value`
	        	FROM `".$wpdb->postmeta."`
	        	WHERE
	        		`meta_key` IN (".implode(',', $medias_meta_keys).")
	        	AND `meta_value` REGEXP '[0-9]+'
	        ";
	        // echo $sql;
	        $acf_medias = $wpdb->get_col($sql);
	    }

        //
        $medias_used = array_merge($medias_used, $medias_favicon, $featured_medias, $related_medias, $in_content_medias, $in_postmeta_medias, $in_usermeta_medias, $in_option_medias, $acf_medias);
        $medias_used = array_unique($medias_used);

        //
        $sql = "
        	SELECT DISTINCT `ID`
        	FROM `".$wpdb->posts."`
        	WHERE
        		`post_type` = 'attachment'
        	AND `ID` NOT IN (".implode(',', $medias_used).")
        	".$sql_keyword."
    		ORDER BY `post_date_gmt` DESC
        ";
        $all_medias = $wpdb->get_results($sql);
        $total = sizeof($all_medias);

        //
        $sql .= "LIMIT ".$offset.", ".$cum_medias_per_page;
        $medias = $wpdb->get_results($sql);

        //
        if (sizeof($medias) == 0) {
        	_e('No result matching to filters.', 'cum-tools');
        }
        else {
	        printf(_n('<strong>%s</strong> media found.', '<strong>%s</strong> medias found.', $total, 'cum-tools'), $total);
	        echo "<hr />";
	        echo "
	        	<p>
	        		<input type=\"button\" class=\"button\" id=\"btn-select-medias\" name=\"btn-select-medias\" value=\"".__('Check / Uncheck all', 'cum-tools')."\" />
					<input type=\"submit\" class=\"button button-primary\" name=\"btn-delete-medias\" value=\"".__('Delete selected medias', 'cum-tools')."\" />
		            <span class=\"result\"></span>
				</p>
    		";
	        echo "<hr />";
	        echo "<div class=\"cum-medias\">";
	        //
	        foreach($medias as $media) {
	        	//
	        	$_url = wp_get_attachment_url($attachment->ID);
	        	//
	        	echo "<div class=\"cum-media\">";
	        	$attachment = get_post($media->ID);
				$thumb = wp_get_attachment_image_src($media->ID, array(60, 60));
	        	echo "<a href=\"".$_url."\" target=\"_blank\"><img src=\"".$thumb[0]."\" alt=\"Thumb\" /></a>";
	        	echo "<h3><label for=\"cum_diam_".$media->ID."\"><input type=\"checkbox\" id=\"cum_diam_".$media->ID."\" name=\"cum_diam[]\" value=\"".$media->ID."\" />#".$attachment->ID."</label></h3>";
	        	echo "<p><a href=\"".$_url."\" target=\"_blank\">".$attachment->post_title."</a></p>";
	        	echo "<p><a href=\"".get_permalink($attachment->ID)."\" target=\"_blank\">".__('Media page', 'cum-tools')."</a></p>";
	        	echo "<p>".__('Uploaded on', 'cum-tools')." ".date_i18n(cum_get_option('date_format'), strtotime($attachment->post_date)).", ".date_i18n(cum_get_option('time_format'), strtotime($attachment->post_date))."</a></p>";
	        	//
	        	$cum_used_in = get_post_meta($media->ID, 'cum_used_in', 1);
	        	$cum_used_in = (!is_array($cum_used_in) || empty($cum_used_in)) ? array() : $cum_used_in;
	        	//
	        	if (cum_is_favicon($media->ID)) { $cum_used_in[] = 'favicon'; }
	        	if (cum_is_featured_media($media->ID)) { $cum_used_in[] = 'featured'; }
	        	if (cum_is_related_media($media->ID)) { $cum_used_in[] = 'related'; }
	        	if (cum_is_ACF_media($media->ID)) { $cum_used_in[] = 'ACF'; }
	        	//
	        	sort($cum_used_in);
	        	//
	        	$cum_used_in = array_unique($cum_used_in);
	        	//
	        	if (sizeof($cum_used_in) > 0) {
	        		$cum_used_in_display = array();
	        		echo "<p class=\"cum-tags\">";
	        		foreach($cum_used_in as $cum_tag) {
	        			$cum_used_in_display[] = "<a href=\"#".$cum_tag."\" class=\"cum-tag\" data-media-id=\"".$media->ID."\">#".$cum_tag."</a>";
	        		}
	        		echo implode(" ", $cum_used_in_display);
	        		echo "</p>";
	        	}
	        	else {
	        		echo "<p class=\"cum-tags\">";
	        		echo __('This media is used anywhere.', 'cum-tools')." <a href=\"#nope\" class=\"cum-tag\" data-media-id=\"".$media->ID."\">".__('You can check it again.', 'cum-tools')."</a>";
					echo  "</p>";	
	        	}
		        echo "</div>";
	        }
	        echo "</div>";

			//
			$page_links = paginate_links(array(
	    		'base' => admin_url().'%#%',
				'format' => '/page/%#%',
	    		'prev_text' => __('&laquo; previous', 'cum-tools'),
	    		'next_text' => __('next &raquo;', 'cum-tools'),
	    		'total' => ceil($total/$cum_medias_per_page),
	    		'current' => $paged
			));
			//
			if ($page_links) {
		    	//
		    	echo "<hr class=\"clear\"/>";
				echo "<div class=\"cum-nav\">";
				echo $page_links;
				//
				echo "</div>";
			}
        }
	}

	//
	$message = ob_get_contents();
	ob_end_clean();

    //
	header("Content-Type: text/javascript");

	//
	$array = array(
		"error" => $error,
  		"message" => $message,
  		"message_error" => $message_error,
	);

	//
	echo json_encode($array);


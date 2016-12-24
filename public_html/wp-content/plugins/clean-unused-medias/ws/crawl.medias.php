<?php
	//
	if (!defined('ABSPATH')) exit; // Exit if accessed directly 

	//
	global $wp_query, $wpdb;

	//
	$error = 1;
	$message = __("Error. Parameters missing.", "cum-tools");
	$debug = "";
	$complete = null;
	$pause = null;
	$resume = null;

	//
	ob_start();

	if (!check_ajax_referer('cum_crawl_medias', 'cum_crawl_medias_nonce', false)) {
	// if (true) {
		$message = __("Error. Access denied.", "cum-tools");
	}
	else {
		//
		wp_clear_scheduled_hook('cum_check_medias_in_content');

		//
		$error = 0;

        // get nb total attachments
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
        $cum_medias_in_content_index = cum_get_option('cum_medias_in_content_index');
        $cum_medias_in_content_index = (empty($cum_medias_in_content_index)) ? 0 : $cum_medias_in_content_index;
        $cum_medias_in_content_index_first = $cum_medias_in_content_index;

        //
        $percent = floor($cum_medias_in_content_index/$total*100);
        $percent = ($percent > 100) ? 100 : $percent;

		//
		if (cum_get_option('cum_medias_in_content_processing')) {
			$message = __("The crawl is already processing somewhere else (in WP-Cron or in another session).", "cum-tools");
			$message .= sprintf(__(" <strong>%s&percnt;</strong> (%s/%s) actually treated.", "cum-tools"), $percent, $cum_medias_in_content_index, $total);
			$message .= __(" <a href=\"#cum-refresh\" class=\"cum-refresh-result\">Refresh the results</a> |", "cum-tools");
			$message .= __(" <a href=\"#cum-pause\" class=\"cum-pause\">Stop the crawler</a>", "cum-tools");
		}
		else {
			//			
	        cum_update_option('cum_medias_in_content_processing', 1);

	        //
			if (cum_get_option('cum_medias_in_content_pause') == 1) {
				$message = __("The crawl was stopped.", "cum-tools");
				$message .= sprintf(__(" <strong>%s&percnt;</strong> (%s/%s) actually treated.", "cum-tools"), $percent, $cum_medias_in_content_index, $total);
				$message .= __(" <a href=\"#cum-recrawl\" class=\"cum-recrawl\">Relaunch the crawl</a> or <a href=\"#cum-resume\" class=\"cum-resume\">resume</a> the lastest one</a>.", "cum-tools");
				$message .= "<span class=\"cum-loading-bar\"><span style=\"width: ".$percent."%; background: #ffb900;\"></span></span>";
				$pause = 1;
			}
			else {
				// check if new medias uploaded since last crawl completed
				$cum_medias_in_content_completed_date = cum_get_option('cum_medias_in_content_completed_date');
				if (!empty($cum_medias_in_content_completed_date)) {
			        $sql = "
			        	SELECT DISTINCT `ID`
			        	FROM `".$wpdb->posts."`
			        	WHERE
			        		`post_type` = 'attachment'
			        	AND `post_date_gmt` > '".$cum_medias_in_content_completed_date."'
			    		ORDER BY `post_date_gmt` DESC
			    	";
			        $recent_media_ids = $wpdb->get_col($sql);
			        // if true, then resume
			        if (sizeof($recent_media_ids) > 0) {
        		        cum_update_option('cum_medias_in_content_completed', null);
        		        //
        		        $cum_medias_in_content_index = $total-sizeof($recent_media_ids);
        		        cum_update_option('cum_medias_in_content_index', $cum_medias_in_content_index);
        		    }
				}

				// do it
				cron_cum_check_medias_in_content();

				//
				$cum_medias_in_content_index = cum_get_option('cum_medias_in_content_index');
				$cum_medias_in_content_index = (empty($cum_medias_in_content_index)) ? 0 : $cum_medias_in_content_index;
				$cum_medias_in_content_completed = cum_get_option('cum_medias_in_content_completed');
				$cum_medias_in_content_completed_date = cum_get_option('cum_medias_in_content_completed_date');
				$cum_medias_in_content_completed_date = get_date_from_gmt($cum_medias_in_content_completed_date, "Y-m-d H:i:s");

		        //
		        $percent = floor($cum_medias_in_content_index/$total*100);
		        $percent = ($percent > 100) ? 100 : $percent;

		        //
		        if (!empty($cum_medias_in_content_completed) || $cum_medias_in_content_index >= $total) {
		        	//
					$message = sprintf(
						__("The crawl was completed on %s at %s.", "cum-tools"),
						date_i18n(cum_get_option('date_format'), strtotime($cum_medias_in_content_completed_date)),
						date_i18n(cum_get_option('time_format'), strtotime($cum_medias_in_content_completed_date))
					);
					$message .= sprintf(_n(" %s media treated.", " %s medias treated.", $total, "cum-tools"), $total);
					$message .= __(" <a href=\"#cum-refresh\" class=\"cum-refresh-result\">Refresh the results</a> |", "cum-tools");
					$message .= __(" <a href=\"#cum-recrawl\" class=\"cum-recrawl\">Relaunch the crawl</a>", "cum-tools");
					$message .= "<span class=\"cum-loading-bar\"><span style=\"width: 100%; background: #46b450;\"></span></span>";
		 	        $complete = 1;
		       }
		        else {
    				if (cum_get_option('cum_medias_in_content_pause') == 1) {
				        //
						$message = __("The crawl was stopped.", "cum-tools");
						$message .= sprintf(__(" <strong>%s&percnt;</strong> (%s/%s) actually treated.", "cum-tools"), $percent, $cum_medias_in_content_index, $total);
						$message .= __(" <a href=\"#cum-recrawl\" class=\"cum-recrawl\">Relaunch the crawl</a> or <a href=\"#cum-resume\" class=\"cum-resume\">resume</a> the lastest one</a>.", "cum-tools");
						$message .= "<span class=\"cum-loading-bar\"><span style=\"width: ".$percent."%; background: #ffb900;\"></span></span>";
						$pause = 1;
    				}
    				else {    					
    					//
						$message = sprintf(__("<strong>%s&percnt;</strong> (%s/%s) actually treated.", "cum-tools"), $percent, $cum_medias_in_content_index, $total);
						$message .= __(" <a href=\"#cum-refresh\" class=\"cum-refresh-result\">Refresh the results</a> |", "cum-tools");
						$message .= __(" <a href=\"#cum-pause\" class=\"cum-pause\">Stop the crawler</a>", "cum-tools");
						$message .= "<span class=\"cum-loading-bar\"><span style=\"width: ".$percent."%;\"></span></span>";
    				}
		        }
			}
		}
	}

	//
	$debug = ob_get_contents();
	ob_end_clean();

	//
    cum_update_option('cum_medias_in_content_processing', null);

    //
    if (!wp_next_scheduled('cum_check_medias_in_content')) {
        wp_schedule_event(time(), 'minutely', 'cum_check_medias_in_content');
    }

    //
	header("Content-Type: text/javascript");

	//
	$array = array(
		"error" => $error,
  		"message" => $message,
		"debug"=>$debug,
		"complete"=>$complete,
		"pause"=>$pause,
		"resume"=>$resume,
		"percent"=>$percent,
		"cum_medias_in_content_index"=>$cum_medias_in_content_index,
	);

	//
	echo json_encode($array);


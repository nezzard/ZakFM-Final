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
	$reset = null;

	//
	ob_start();

	if (!check_ajax_referer('cum_crawl_medias', 'cum_crawl_medias_nonce', false)) {
		$message = __("Error. Access denied.", "cum-tools");
	}
	else {
		//
		$error = 0;

		// reset
		if (!empty($_POST['cum_crawl_medias_reset'])) {
			//
	        cum_update_option('cum_medias_in_content_index', null);
	        cum_update_option('cum_medias_in_content_ids', null);
	        cum_update_option('cum_medias_in_postmeta_ids', null);
	        cum_update_option('cum_medias_in_usermeta_ids', null);
	        cum_update_option('cum_medias_in_option_ids', null);
	        cum_update_option('cum_medias_in_content_completed', null);
	        cum_update_option('cum_medias_in_content_pause', null);
	        //
	        $sql = "
	        	DELETE FROM `".$wpdb->postmeta."`
	        	WHERE
	        		`meta_key` = 'cum_used_in'
	    	";
	        $wpdb->query($sql);

	        //
	        $reset = 1;
	        //
			$message = __("Reset crawl.", "cum-tools");
		}

		// pause
		if (!empty($_POST['cum_crawl_medias_pause'])) {
			//
	        cum_update_option('cum_medias_in_content_pause', 1);
	        //
	        $pause = 1;
	        //
			$message = __("Pause crawl.", "cum-tools");
		}

		// resume
		if (!empty($_POST['cum_crawl_medias_resume'])) {
			//
	        cum_update_option('cum_medias_in_content_pause', null);
	        //
	        $resume = 1;
	        //
			$message = __("Resume crawl.", "cum-tools");
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
		"debug"=>$debug,
		"pause"=>$pause,
		"resume"=>$resume,
		"reset"=>$reset,
	);

	//
	echo json_encode($array);


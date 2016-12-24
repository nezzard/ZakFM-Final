<?php
	//
	if (!defined('ABSPATH')) exit; // Exit if accessed directly 

	//
	function cum_install() {
		global $wpdb, $wp, $wp_rewrite;

		//
		update_option("cum_medias_in_content_index", 0);
		update_option("cum_medias_in_content_ids", array());
		update_option("cum_medias_in_postmeta_ids", array());
		update_option("cum_medias_in_usermeta_ids", array());
		update_option("cum_medias_in_option_ids", array());
		update_option("cum_medias_in_content_last_check", null);
		update_option("cum_medias_in_content_pause", null);
		update_option("cum_medias_in_content_completed", null);
		update_option("cum_medias_in_content_completed_date", null);
		update_option("cum_medias_in_content_processing", null);
	}

	//
	function cum_uninstall() {
		global $wpdb;

		// some things to undo
		delete_option("cum_medias_in_content_index");
		delete_option("cum_medias_in_content_ids");
		delete_option("cum_medias_in_postmeta_ids");
		delete_option("cum_medias_in_usermeta_ids");
		delete_option("cum_medias_in_option_ids");
		delete_option("cum_medias_in_content_last_check");
		delete_option("cum_medias_in_content_pause");
		delete_option("cum_medias_in_content_completed");
		delete_option("cum_medias_in_content_completed_date");
		delete_option("cum_medias_in_content_processing");

		//
        $sql = "
        	DELETE FROM `".$wpdb->postmeta."`
        	WHERE
        		`meta_key` = 'cum_used_in'
    	";
        $wpdb->query($sql);

		//
		wp_clear_scheduled_hook('cum_check_medias_in_content');
	}


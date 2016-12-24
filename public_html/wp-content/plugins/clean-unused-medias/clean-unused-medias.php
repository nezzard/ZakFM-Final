<?php
	/*
	Plugin Name: 	Clean Unused Medias
	Version:		1.08
	Date:			2016/10/06
	Plugin URI:		https://xuxu.fr/2016/09/28/supprimer-les-fichiers-non-utilises-sous-wordpress/
	Description:	Clean Unused Medias : Simple way to delete medias not attached to any posts or pages
	Author:			xuxu.fr
	Text Domain:	cum-tools
	Domain Path:	/languages/
	Author URI:		https://xuxu.fr
	*/

	//
	if (!defined('ABSPATH')) exit; // Exit if accessed directly 

	/* +---------------------------------------------------------------------------------------------------+
	   | CONSTANTES
	   +---------------------------------------------------------------------------------------------------+ */
	if (!defined('WP_PLUGIN_DIR')) {
		$cum_plugin_dir = str_replace('clean-unused-medias/', '', dirname(__FILE__));
		define('WP_PLUGIN_DIR', $cum_plugin_dir);
	}
	define('CUM_PLUGIN_DIR', WP_PLUGIN_DIR."/clean-unused-medias");

	/* +---------------------------------------------------------------------------------------------------+
	   | INCLUDES
	   +---------------------------------------------------------------------------------------------------+ */
	require_once CUM_PLUGIN_DIR."/library/includes/compat.php";
	require_once CUM_PLUGIN_DIR."/library/install.php";
	require_once CUM_PLUGIN_DIR."/library/functions.php";

	/* +---------------------------------------------------------------------------------------------------+
	   | REGISTER ACTIVATION
	   +---------------------------------------------------------------------------------------------------+ */
	register_activation_hook(__FILE__, 'cum_install');
	register_deactivation_hook(__FILE__, 'cum_uninstall');


	/* +---------------------------------------------------------------------------------------------------+
	   | TEXT DOMAIN
	   +---------------------------------------------------------------------------------------------------+ */
	function cum_load_textdomain() {
		load_plugin_textdomain('cum-tools', false, dirname(plugin_basename(__FILE__)).'/languages'); 
	}
	add_action('init', 'cum_load_textdomain');

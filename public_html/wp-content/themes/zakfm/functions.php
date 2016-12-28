<?php



require_once('parser.php');
//require_once('socket.php');







function loadModule() {
wp_register_script('parser-js', get_bloginfo( 'template_directory' ).'/js/init.js',array(),NULL,true);
wp_enqueue_script('parser-js');


/*$parserVal = array( 'parserVal' => parseMusic());
wp_localize_script( 'parser-js', 'parserVal', $parserVal );*/
}


add_theme_support( 'post-thumbnails' );


// Register Custom Post Type
function song_post_type() {

	$labels = array(
		'name'                  => _x( 'Пісні', 'Post Type General Name', 'text_domain' ),
		'singular_name'         => _x( 'Пісня', 'Post Type Singular Name', 'text_domain' ),
		'menu_name'             => __( 'Пісні', 'text_domain' ),
		'name_admin_bar'        => __( 'Пісні', 'text_domain' ),
		'archives'              => __( 'Архів пісень', 'text_domain' ),
		'parent_item_colon'     => __( '', 'text_domain' ),
		'all_items'             => __( 'Всі пісні', 'text_domain' ),
		'add_new_item'          => __( 'Додати нову пісню', 'text_domain' ),
		'add_new'               => __( 'Додати нову', 'text_domain' ),
		'new_item'              => __( 'Нова пісня', 'text_domain' ),
		'edit_item'             => __( 'Редагувати пісню', 'text_domain' ),
		'update_item'           => __( 'Оновити пісню', 'text_domain' ),
		'view_item'             => __( 'Переглянути пісню', 'text_domain' ),
		'search_items'          => __( 'Пошук пісні', 'text_domain' ),
		'not_found'             => __( 'Нічого не знайдено', 'text_domain' ),
		'not_found_in_trash'    => __( '', 'text_domain' ),
		'featured_image'        => __( 'Мініатюра', 'text_domain' ),
		'set_featured_image'    => __( 'Встановити мініатюру', 'text_domain' ),
		'remove_featured_image' => __( 'Видалити мініатюру', 'text_domain' ),
		'use_featured_image'    => __( 'Використати як мініатюру', 'text_domain' ),
		'insert_into_item'      => __( '', 'text_domain' ),
		'uploaded_to_this_item' => __( '', 'text_domain' ),
		'items_list'            => __( 'Список пісень', 'text_domain' ),
		'items_list_navigation' => __( '', 'text_domain' ),
		'filter_items_list'     => __( '', 'text_domain' ),
	);
	$args = array(
		'label'                 => __( 'Пісня', 'text_domain' ),
		'description'           => __( 'Перелік всіх пісень', 'text_domain' ),
		'labels'                => $labels,
		'supports'              => array( 'title', 'editor', 'excerpt', 'author', 'thumbnail', 'comments', 'trackbacks', 'revisions', 'custom-fields', 'page-attributes', 'post-formats', ),
		'taxonomies'            => array( 'post_tag' ),
		'hierarchical'          => false,
		'public'                => true,
		'show_ui'               => true,
		'show_in_menu'          => true,
		'show_in_rest' => true,
		'rest_base'          => 'song',
		'rest_controller_class' => 'WP_REST_Posts_Controller',
		'menu_position'         => 5,
		'show_in_admin_bar'     => true,
		'show_in_nav_menus'     => true,
		'can_export'            => true,
		'has_archive'           => true,		
		'exclude_from_search'   => false,
		'publicly_queryable'    => true,
		'capability_type'       => 'page',
	);
	register_post_type( 'song', $args );

}
add_action( 'init', 'song_post_type', 0 );



add_action( 'rest_api_init', function () {
	register_rest_route( 'wp/v2', '/arch', array(
		'methods' => 'POST',
		'callback' => 'my_awesome_funсc',

	) );
} );



add_action( 'rest_api_init', function () {
	register_rest_route( 'wp/v2', '/next/(?P<id>\d+)/(?P<ordernum>\d+)', array(
		'methods' => 'POST',
		'callback' => 'my_awesome_func',
	) );
} );
function my_awesome_funсc($data){


		global $wpdb;
		return $wpdb->insert("wp_playlistarch", array(
		   "song_id" => $data['songid'],
		));

}



add_action( 'rest_api_init', 'slug_register_spaceship' );
function slug_register_spaceship() { 



    register_rest_field( 'song',
        'youtube',
        array(
            'get_callback'    => 'slug_get_spaceship',
            'update_callback' => 'slug_update_spaceship',
            'schema'          => null,
        )
    );

    register_rest_field( 'song',
        'lastfm',
        array(
            'get_callback'    => 'slug_get_spaceship',
            'update_callback' => 'slug_update_spaceship',
            'schema'          => null,
        )
    );

    register_rest_field( 'song',
        'artist',
        array(
            'get_callback'    => 'slug_get_spaceship',
            'update_callback' => 'slug_update_spaceship',
            'schema'          => null,
        )
    );

    register_rest_field( 'song',
        'song',
        array(
            'get_callback'    => 'slug_get_spaceship',
            'update_callback' => 'slug_update_spaceship',
            'schema'          => null,
        )
    );



    register_rest_field( 'song',
        'insertdb',
        array(
            'get_callback'    => 'slug_get_playarch',
            'update_callback' => 'slug_update_playarch',
            'schema'          => null,
        )
    );




}



function test( $data ) {
    return 1;
}

function slug_get_spaceship( $object, $field_name, $request ) {
    return get_post_meta( $object[ 'id' ], $field_name );
}

function slug_update_spaceship( $value, $object, $field_name ) {
    if ( ! $value || ! is_string( $value ) ) {
        return;
    }
    return update_post_meta( $object->ID, $field_name, strip_tags( $value ) );
}






function slug_get_playarch( $object, $field_name, $request ) {
    return get_post_meta( $object[ 'id' ], $field_name );
}

function slug_update_playarch( $value ) {


		global $wpdb;
		return $wpdb->insert("wp_playlistarch", array(
		   "song_id" => $value,
		));

}




/*

add_action( 'rest_api_init', 'ah_register_health_endpoint' );
function ah_register_health_endpoint() {
	// Add deep-thoughts/v1/get-all-post-ids route
	register_rest_route( 'wp/v2', 'song/', array(
		'methods' => 'POST',
		'callback' => 'ah_get_weights',
	) );
}
function ah_get_weights() {
	$health_args = [
		'post_type' => 'song',
		'posts_per_page' => -1,
		'orderby' => 'date',
		'order' => 'ASC',
	];

}*/







?>
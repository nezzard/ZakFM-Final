<?php
date_default_timezone_set('Europe/Kiev');

require_once('parser.php');
//require_once('socket.php');


//Регистрация нового размера миниатюры
add_image_size( 'homepage-thumb', 290, 193, true );
add_image_size( 'homepage-thumb-slide', 710, 442, true );


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
		'supports'              => array( 'title', 'editor', 'excerpt', 'thumbnail', 'comments', 'trackbacks', 'custom-fields', 'page-attributes'),
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
           "date" => date('Y-m-d H:i:s')
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




add_action( 'transition_post_status', function ( $new_status, $old_status, $post )
{

    if( 'publish' == $new_status && 'publish' != $old_status && $post->post_type == 'song' ) {

function true_unset_image_sizes( $sizes) {
    unset( $sizes['thumbnail']); // миниатюра
    unset( $sizes['medium']); // средний
    unset( $sizes['large']); // большой
    unset( $sizes['full']); // оригинал
    return $sizes;
}
 
add_filter('intermediate_image_sizes_advanced', 'true_unset_image_sizes');
    }
}, 10, 3 );





function register_my_menus() {
  register_nav_menus(
    array(
      'header-menu' => __( 'Верхнє меню' ),
      'fotter-menu' => __( 'Нижнє меню' )
    )
  );
}
add_action( 'init', 'register_my_menus' );



function last_song() {
    global $wpdb;

    $song_ids =  $wpdb->get_results('SELECT * FROM wp_playlistarch ORDER BY date DESC LIMIT 6');

    ?>
    
    <h3 class="wrap-tit">
      	Лунало в ефірі
    </h3>
    <div class="arch-songs">
    <?php foreach($song_ids as $id) { ?>   
    <div class="one-song">
        <div class="one-song-in">
        		<?php $youtClass = ""; if(strlen(get_field("youtube", $id->song_id)) > 0)
        		{ $youtClass = "thumb-yout"; }  ?>
						<a href="#" class="one-song-thumb <?php echo $youtClass; ?>" data-youtube="<?php echo get_field('youtube', $id->song_id); ?>">
                <img src="<?php if(has_post_thumbnail( $id->song_id)) { 
									echo get_the_post_thumbnail_url( $id->song_id, 'thumbnail' ); 
              	}else {
              		echo "http://placehold.it/150x150";
              	}
                ?>">              
            </a>

        		
            
            <div class="one-song-descr">
                <div class="one-song-tit">
                        <b><?php the_field('artist', $id->song_id); ?></b> </br><?php the_field('song', $id->song_id); ?>
                </div>
                <div class="one-song-time">
                    Прозвучала: <?php echo $id->date; ?>
                </div>
            </div>
        </div>
    </div>     
    <?php } ?>
    </div>
    
    <?php

}




// Реєстрація слайду
function slider_post_type() {

    $labels = array(
        'name'                  => _x( 'Слайди', 'Post Type General Name', 'text_domain' ),
        'singular_name'         => _x( 'Слайд', 'Post Type Singular Name', 'text_domain' ),
        'menu_name'             => __( 'Слайди', 'text_domain' ),
        'name_admin_bar'        => __( 'Слайд', 'text_domain' ),
        'archives'              => __( 'Архів слайдів', 'text_domain' ),
        'attributes'            => __( '', 'text_domain' ),
        'parent_item_colon'     => __( '', 'text_domain' ),
        'all_items'             => __( 'Всі слайди', 'text_domain' ),
        'add_new_item'          => __( 'Додати новий слайд', 'text_domain' ),
        'add_new'               => __( 'Додати новий', 'text_domain' ),
        'new_item'              => __( 'Новий слайд', 'text_domain' ),
        'edit_item'             => __( 'Редагувати слайд', 'text_domain' ),
        'update_item'           => __( 'Оновити слайд', 'text_domain' ),
        'view_item'             => __( 'Переглянути слайд', 'text_domain' ),
        'view_items'            => __( 'Переглянути слайди', 'text_domain' ),
        'search_items'          => __( 'Пошук слайдів', 'text_domain' ),
        'not_found'             => __( 'Нічого не знайдено', 'text_domain' ),
        'not_found_in_trash'    => __( 'В кошику нічого не знайдено', 'text_domain' ),
        'featured_image'        => __( 'Мініатюра', 'text_domain' ),
        'set_featured_image'    => __( 'Встановити як мініатюру', 'text_domain' ),
        'remove_featured_image' => __( 'Видалити мінатюру', 'text_domain' ),
        'use_featured_image'    => __( 'Використати як мініатюру', 'text_domain' ),
        'insert_into_item'      => __( 'Вставити в слайд', 'text_domain' ),
        'uploaded_to_this_item' => __( 'Завантажені до слайда', 'text_domain' ),
        'items_list'            => __( 'Список слайдів', 'text_domain' ),
        'items_list_navigation' => __( 'Навігація по слайдам', 'text_domain' ),
        'filter_items_list'     => __( 'Фільтрувати список слайдів', 'text_domain' ),
    );
    $args = array(
        'label'                 => __( 'Слайд', 'text_domain' ),
        'description'           => __( 'Слайди на головній', 'text_domain' ),
        'labels'                => $labels,
        'supports'              => array( 'title', 'editor', 'comments'),
        'hierarchical'          => false,
        'public'                => true,
        'show_ui'               => true,
        'show_in_menu'          => true,
        'menu_position'         => 5,
        'menu_icon'             => 'dashicons-images-alt2',
        'show_in_admin_bar'     => true,
        'show_in_nav_menus'     => true,
        'can_export'            => true,
        'has_archive'           => true,        
        'exclude_from_search'   => false,
        'publicly_queryable'    => true,
        'capability_type'       => 'page',
    );
    register_post_type( 'slider', $args );

}
add_action( 'init', 'slider_post_type', 0 );







?>
<?php /* Template Name: Архив */ ?>

<?php
    global $wpdb;


    //$total_query = "SELECT COUNT(id) FROM wp_playlistarch";
    //$total = $wpdb->get_var( $total_query );
   // $items_per_page = 24;
    //$page = isset( $_GET['cpaged'] ) ? abs( (int) $_GET['cpaged'] ) : 1;
    //$offset = ( $page * $items_per_page ) - $items_per_page;

    if(isset($_POST["clearDate"])){
        $_POST = array();
    }
    if(isset($_POST["sortDate"])){
       $song_ids =  $wpdb->get_results('SELECT * FROM wp_playlistarch WHERE date LIKE "'.$_POST["sortDate"].'%" ORDER BY date DESC ');
    }else {
       $song_ids =  $wpdb->get_results('SELECT * FROM wp_playlistarch ORDER BY date DESC');
    }


    $songIDs = $song_ids;
?>

<?php get_header(); ?>
        
    <div class="wrap-in">
        <?php get_sidebar(); ?>    
                    
        	<!-- Левая часть -->
            <div class="cont">                    
                <!-- Останні пісні -->
                <div class="last-song">
                	<h3 class="wrap-tit">
                    	Лунало в ефірі
                    </h3>

          
                    <div class="arch-songs">
                        <div class=" form-control-date"></div>
<div class="input-group date form-control-date">
    <input type="text" class="form-control" value="12-02-2012">
    <div class="input-group-addon">
        <span class="glyphicon glyphicon-th"></span>
    </div>
</div>
                    <?php $orderNum = 1; foreach($songIDs as $id) { ?>   
                    <div class="one-song">
                        <div class="one-song-in">
                            <?php $youtClass = ""; if(strlen(get_field("youtube", $id->song_id)) > 0)
                            { $youtClass = "thumb-yout"; }  ?>
                            <div class="num">1</div>
                            <a href="#" class="one-song-thumb pjax <?php echo $youtClass; ?>" data-youtube="<?php echo get_field('youtube', $id->song_id); ?>">
                                <img src="<?php echo get_the_post_thumbnail_url( $id->song_id, 'thumbnail' ); ?>">
                            </a>
                            <div class="one-song-descr">
                                <div class="one-song-tit">
                                        <b><?php the_field('artist', $id->song_id); ?></b></br><?php the_field('song', $id->song_id); ?>
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
                    /*echo '<div class="wp-pagenavi arch-nav">';

                    echo paginate_links( array(
                        'base' => add_query_arg( 'cpaged', '%#%' ),
                        'format' => '',
                        'show_all'     => False,
                        'end_size'     => 1,
                        'mid_size'     => 2,
                        'type'         => 'plain',
                        'prev_text' => __('&laquo;'),

                        'next_text' => __('&raquo;'),
                        'total' => ceil($total / $items_per_page),
                        'current' => $page
                    ));

                    echo '</div>' */;
                    ?>
                </div>
                <!-- Кінець Останні пісні -->
                    
            </div>
                <!-- Конец Основная часть -->
            
            <!-- Конец Основная часть -->
        </div>
<?php get_footer(); ?>
<?php /* Template Name: Архив */ ?>

<?php







    global $wpdb;

     $total_query = "SELECT COUNT(id) FROM wp_playlistarch";
     $total = $wpdb->get_var( $total_query );
     $items_per_page = 12;
     $page = isset( $_GET['cpaged'] ) ? abs( (int) $_GET['cpaged'] ) : 1;
     $offset = ( $page * $items_per_page ) - $items_per_page;
     $song_ids =  $wpdb->get_results('SELECT * FROM wp_playlistarch ORDER BY date DESC LIMIT '.$items_per_page.' OFFSET '.$offset);

     $songIDs = $song_ids;



     
     	 ?>














<?php 


get_header(); ?>
        
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




<?php 

     foreach($songIDs as $id) {

 ?>


   
    <div class="one-song">
        <div class="one-song-in">
            <a href="#" class="one-song-thumb" data-youtube="<?php echo get_field('youtube'); ?>">
                <img src="<?php echo get_the_post_thumbnail_url( $id->song_id, 'thumbnail' ); ?>">
            </a>
            <div class="one-song-descr">
                <div class="one-song-tit">
                    <a href="#" data-youtube="<?php echo get_field('youtube'); ?>">
                        <b>
                          <?php the_field('artist', $id->song_id); ?>
                        </b>
                        - <?php the_field('song', $id->song_id); ?>
                    </a>
                </div>
                <div class="one-song-time">
                    Прозвучала: <?php echo $id->date; ?>
                </div>
                <div class="show-video">
                    <a href="#" data-youtube="<?php echo get_field('youtube'); ?>">
                        Переглянути відео
                    </a>
                </div>
            </div>
        </div>
    </div>






     
<?php     }?>





           

                        
                        </div>

                        <?php





     echo '<div class="commentPagination">';

     echo paginate_links( array(
        'base' => add_query_arg( 'cpaged', '%#%' ),
        'format' => '',
        'prev_text' => __('&laquo;'),
        'next_text' => __('&raquo;'),
        'total' => ceil($total / $items_per_page),
        'current' => $page
    ));

     echo '</div>';


?>
                    </div>
                    <!-- Конец Останні пісні -->
                    
                </div>
                <!-- Конец Основная часть -->
            	
                
            </main>
            <!-- Конец Основная часть -->
        </div>
<?php get_footer(); ?>
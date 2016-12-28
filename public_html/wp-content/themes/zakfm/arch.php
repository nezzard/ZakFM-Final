<?php /* Template Name: Архив */ ?>

<?php







    global $wpdb;

     $total_query = "SELECT COUNT(id) FROM wp_playlistarch";
     $total = $wpdb->get_var( $total_query );
     $items_per_page = 3;
     $page = isset( $_GET['paged'] ) ? abs( (int) $_GET['paged'] ) : 1;
     $offset = ( $page * $items_per_page ) - $items_per_page;
     $song_ids =  $wpdb->get_results('SELECT * FROM wp_playlistarch ORDER BY "date" DESC LIMIT '.$items_per_page.' OFFSET '.$offset);


     $songIDs;


     foreach($song_ids as $id) {
     echo $i; 
     	$songIDs[] =  $id->song_id.',';
      
     }

     echo '<div class="commentPagination">';

     echo paginate_links( array(
        'base' => add_query_arg( 'paged', '%#%' ),
        'format' => '',
        'prev_text' => __('&laquo;'),
        'next_text' => __('&raquo;'),
        'total' => ceil($total / $items_per_page),
        'current' => $page
    ));

     echo '</div>';


?>


<?php 


get_header(); ?>
        
    	<div class="wrap-in">

            <!-- Основная часть -->
            <main class="main">
            
            	<!-- Сайдбар -->
                <aside class="aside">
                	<div class="one-widget">
                    	<h3 class="wrap-tit">
                        	Наступні композиції
                        </h3>
                        <div class="will-play">
                        	<div class="all-songs">

                                

                                
                            </div>
                        </div>
                    </div>
                    
                    <div class="one-widget">
                    	<div class="social">
                        	<a href="#">
                            	<img src="<?php bloginfo('template_url'); ?>/images/facebook.png" alt=" " />
                            </a>
                            <a href="#">
                            	<img src="<?php bloginfo('template_url'); ?>/images/google-plus.png" alt=" " />
                            </a>
                            <a href="#">
                            	<img src="<?php bloginfo('template_url'); ?>/images/vk.png" alt=" " />
                            </a>
                            <a href="#">
                            	<img src="<?php bloginfo('template_url'); ?>/images/facebook.png" alt=" " />
                            </a>
                        </div>
                    </div>
                    
                    <div class="one-widget rekl">
                    	<a href="#">
                        	<img src="http://placehold.it/290x220">
                        </a>
                    </div>
                    
                    <div class="one-widget chat">
                    	<a href="#">
                        	<img src="<?php bloginfo('template_url'); ?>/images/chat.jpg">
                        </a>
                    </div>
                    
                </aside>
                <!-- Конец Сайдбар -->
            
            	<!-- Левая часть -->
                <div class="cont">

                    
                    <!-- Останні пісні -->
                    <div class="last-song">
                    	<h3 class="wrap-tit">
                        	Лунало в ефірі
                        </h3>
                        <div class="arch-songs">





<?php 
// the query
												$args = array(
													'posts_per_page' => 3,
													'post_type' => 'song',
													'post__in' => $songIDs
												);


$the_query = new WP_Query( $args ); ?>

<?php if ( $the_query->have_posts() ) : ?>

	<!-- pagination here -->

	<!-- the loop -->
	<?php while ( $the_query->have_posts() ) : $the_query->the_post(); ?>
		<h2><?php the_title(); ?></h2>
	<?php endwhile; ?>
	<!-- end of the loop -->

	<!-- pagination here -->

	<?php wp_reset_postdata(); ?>

<?php else : ?>
	<p><?php _e( 'Sorry, no posts matched your criteria.' ); ?></p>
<?php endif; ?>


												<?php

												$args = array(
													'posts_per_page' => 3,
													'post_type' => 'song',
													'post__in' => $songIDs
												);

												$query = new WP_Query( $args );
												// Цикл
												if ( $query->have_posts()) {

													$i = 0;
													while ( $query->have_posts() ) {

														$query->the_post();?>

												    	<div class="one-song">
												    		<div class="one-song-in">
												    	        <a href="#" class="one-song-thumb" data-youtube="<?php echo get_field('youtube'); ?>">
												    	            <img src="<?php bloginfo('template_url'); ?>/images/avat/thumb-4.jpg">
												    	        </a>
												    	        <div class="one-song-descr">
												    	            <div class="one-song-tit">
												    	                <a href="#" data-youtube="<?php echo get_field('youtube'); ?>">
												    	                    <b>
												    	                      <?php the_field('artist'); ?>
												    	                    </b>
												    	                    - <?php the_field('song'); ?>
												    	                </a>
												    	            </div>
												    	            <div class="one-song-time">
												    	                Прозвучала: <?php echo $song_ids[$i]->date; ?>
												    	            </div>
												    	            <div class="show-video">
												    	                <a href="#" data-youtube="<?php echo get_field('youtube'); ?>">
												    	                    Переглянути відео
												    	                </a>
												    	            </div>
												    	        </div>
												    	    </div>
												    	</div>

														<?php
														$i++;
													}
												} else {
													// Постов не найдено
												}
												/* Возвращаем оригинальные данные поста. Сбрасываем $post. */
												wp_reset_postdata();
												?>


           

                        
                        </div>
                        
                        <div class="all-vid">
                        	<a href="#">
                            	<span></span>
                            	<i>Переглянути архів</i>
                            </a>
                        </div>
                        
                    </div>
                    <!-- Конец Останні пісні -->
                    
                </div>
                <!-- Конец Основная часть -->
            	
                
            </main>
            <!-- Конец Основная часть -->
        </div>
<?php get_footer(); ?>
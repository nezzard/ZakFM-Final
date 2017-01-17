<?php 

get_header(); ?>
        
    	<div class="wrap-in">

            <!-- Основная часть -->
            <main class="main">
            
                <?php get_sidebar(); ?>
            
            	<!-- Левая часть -->
                <div class="cont">
                	<!-- Слайдер -->
                    <div class="slider">
                        <div class="bxslider">

                        <?php
                        $the_query = new WP_Query( array( 'post_type' => 'slider', 'posts_per_page' => 4 ) );
                        ?>
                        <?php if ( $the_query->have_posts() ) : ?>
                            <?php while ( $the_query->have_posts() ) : $the_query->the_post(); 

                            if(get_field('вставити_запис') == true){
                                $id = get_field('вставлений_запис')->ID; ?>

                                <div class="one-slide">
                                    <a href="#" class="pjax">
                                    <?php echo get_the_post_thumbnail( $id, 'homepage-thumb-slide' ); ?>
                                    
                                    </a>
                                    <div class="slide-descr">
                                        <div class="slide-tit">
                                            <a href="<?php the_permalink($id); ?>" class="pjax"><?php echo get_field('вставлений_запис')->post_title; ?></a>
                                        </div>
                                        <p>
                                            <?php echo mb_substr( strip_tags(  get_field('вставлений_запис')->post_content), 0, 70 ); ?>...
                                        </p>
                                    </div>
                                </div>
                            <?php } else { ?>     

                            <div class="one-slide">
                            	<a href="#" class="pjax">
                                <?php $image = wp_get_attachment_image_src( get_field('зображення_слайду'), 'homepage-thumb-slide'); ?>
                                
                                	<img src="<?php echo $image[0]; ?>" width="670" height="375" alt=" " />
                                </a>
                                <div class="slide-descr">
                                    <div class="slide-tit">
                                        <a href="<?php the_permalink(); ?>" class="pjax"><?php the_title(); ?></a>
                                    </div>
                                    <p>
                                        <?php echo mb_substr( strip_tags( get_field('опис_слайду')), 0, 70 ); ?>...
                                    </p>
                                </div>
                            </div>
                            <?php } ?>
                            <?php endwhile; ?>
                            <?php wp_reset_postdata(); ?>

                        <?php else : ?>
                            <p><?php _e( 'Sorry, no posts matched your criteria.' ); ?></p>
                        <?php endif; ?>

                        </div>
                    </div>
                    <!-- Конец Слайдер -->

                    <!-- Блок рекламы под слайдером -->
                    <a href="#" class="ads" target="_blank"><img src="http://placehold.it/670x120"></a>
                    
                    <!-- Останні новини -->
                    <div class="last-news">
                    	<h3 class="wrap-tit">
                        	Останні новини
                        </h3>
                        <?php                         
                        $the_query = new WP_Query( array( 'category__in' => 4, 'posts_per_page' => 1 ) );
                        ?>
                        <?php if ( $the_query->have_posts() ) : ?>
                            <?php while ( $the_query->have_posts() ) : $the_query->the_post(); ?>                        
                            <div class="left-news">
                            	<div class="left-news-thumb">
                                	<a href="#" class="pjax">
                                        <?php  the_post_thumbnail('homepage-thumb'); ?>
                                    </a>
                                </div>
                                <div class="left-news-tit">
                                    <a href="<?php the_permalink(); ?>" class="pjax">
                                        <?php the_title(); ?>
                                    </a>
                                </div>
                                <p>
                                    <?php echo mb_substr( strip_tags( get_the_content()), 0, 152 ); ?>...
                                </p>
                                <div class="one-news-info">
                                    <div class="news-data">
                                        <?php the_time("j F") ;?> |
                                    </div>
                                    <div class="news-author">
                                        Ведучий(а) <a href="<?php echo esc_url( get_author_posts_url( $authordata->ID, $authordata->user_nicename ) ); ?>" class="pjax"><?php the_author_meta('first_name'); ?> <?php the_author_meta('last_name'); ?></a>
                                    </div>
                                </div>
                            </div>
                            <?php endwhile; ?>
                            <?php wp_reset_postdata(); ?>

                        <?php else : ?>
                            <p><?php _e( 'Sorry, no posts matched your criteria.' ); ?></p>
                        <?php endif; ?>

                        <div class="right-news">
                            <?php                         
                            $the_query = new WP_Query( array( 'category__in' => 4, 'posts_per_page' => 5, 'offset' => 1 ) );
                            ?>
                            <?php if ( $the_query->have_posts() ) : ?>
                                <?php while ( $the_query->have_posts() ) : $the_query->the_post(); ?>
                                <div class="one-news">
                                    <div class="one-news-thumb">
                                        <a href="<?php echo esc_url( get_author_posts_url( $authordata->ID, $authordata->user_nicename ) ); ?>" class="pjax">
                                            <?php echo get_avatar( get_the_author_meta('user_email'), 50 ); ?>
                                        </a>
                                    </div>
                                    <div class="one-news-descr">
                                        <a href="<?php the_permalink(); ?>" class="pjax "><b><?php the_title(); ?></b></a>
                                        <div class="one-news-info">
                                            <div class="news-data">
                                                <?php the_time("j F") ;?> |
                                            </div>
                                            <div class="news-author">
                                                Ведучий(а) <a href="<?php echo esc_url( get_author_posts_url( $authordata->ID, $authordata->user_nicename ) ); ?>" class="pjax"><?php the_author_meta('first_name'); ?> <?php the_author_meta('last_name'); ?></a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php endwhile; ?>
                                <?php wp_reset_postdata(); ?>

                            <?php else : ?>
                                <p><?php _e( 'Sorry, no posts matched your criteria.' ); ?></p>
                            <?php endif; ?>


                            
                            <div class="all-news">
                            	<a href="<?php echo get_category_link(4); ?>" class="pjax">
                                	Всі новини
                                </a>
                            </div>
                            
                        </div>
                    </div>
                    <!-- Конец Останні новини -->
                    
                    <!-- Останні пісні -->
                    <div class="last-song">

                        <?php last_song(); ?>

                        
                      
                        
                        <div class="all-vid">
                            <a href="/archive/" class="pjax">
                                <span></span>
                                <i>Переглянути архів</i>
                            </a>
                        </div>
                        
                    </div>
                    <!-- Блок рекламы под архивом -->
                    <a href="#" class="ads" target="_blank"><img src="http://placehold.it/670x120"></a>
                    <!-- Конец Останні пісні -->
                    
                </div>
                <!-- Конец Основная часть -->
            	
                
            </main>
            <!-- Конец Основная часть -->
        </div>
<?php get_footer(); ?>
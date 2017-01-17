<?php 


get_header(); ?>
        
    	<div class="wrap-in">

            <!-- Основная часть -->
            <main class="main">
            
                <?php get_sidebar(); ?>
            
            	<!-- Левая часть -->
                <div class="cont">

                    
                    <!-- Останні новини -->
                    <div class="single-news">
                    	<h1 class="post-title"><?php the_title(); ?></h1>


                             <!-- Start the Loop. -->
                             <?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
                        <p class="cat-descr"><b>Опубліковано</b> <?php the_time('M j, Y'); ?> | <em>від <?php the_author(); ?></em></p>


                            <article class="single-page">
                                <?php $image = wp_get_attachment_image_src( get_field('зображення_слайду'), 'homepage-thumb-slide'); ?>
                                <?php if( $image ) { ?>
                                   
                                        <img src="<?php echo  $image[0];?>" class="single-thumb" alt=""> 
                                    
                                <?php } ?>
                                <div class="content">
                                    <?php the_field('опис_слайду'); ?> 
                                </div> 
                                <br class="clear"> 
                            </article>

                            <?php comments_template(); ?>


                             <!-- Остановить Цикл (но есть ключевое слово "else:" - смотрите далее). -->
                             <?php endwhile; else: ?>


                             <p>Sorry, no posts matched your criteria.</p>

                             <!-- ДЕЙСТВИТЕЛЬНО остановить Цикл -->
                             <?php endif; ?>
                           
                            
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
                    <!-- Конец Останні пісні -->
                    
                </div>
                <!-- Конец Основная часть -->
            	
                
            </main>
            <!-- Конец Основная часть -->
    </div>
<?php get_footer(); ?>
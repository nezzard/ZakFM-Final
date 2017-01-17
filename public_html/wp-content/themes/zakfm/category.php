<?php 


get_header(); ?>
        
    	<div class="wrap-in">

            <!-- Основная часть -->
            <main class="main">
            
                <?php get_sidebar(); ?>
            
            	<!-- Левая часть -->
                <div class="cont">

                    
                    <!-- Останні новини -->
                    <div class="last-news">
                    	<h3 class="wrap-tit">
                        	Останні новини
                        </h3>
                             <!-- Start the Loop. -->
                             <?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>

                            <article class="category-single">
                                <?php if( has_post_thumbnail() ) { ?>
                                
                                    <a href="<?php the_permalink(); ?>" class="pjax"> 
                                        <span class="module-a-video-icon-big omc-blog2-icon"></span> 
                                        <img src="<?php the_post_thumbnail_url( 'homepage-thumb' ); ?>" class="cat-thumb" alt=""> 
                                    </a>
                                <?php } ?>
                                <h2 class="cat-tit"> 
                                    <a href="<?php the_permalink(); ?>" class="pjax"><?php the_title(); ?></a>
                                </h2>
                                <p class="cat-descr"><b>Опубліковано</b> <?php the_time('M j, Y'); ?> | <em>від <?php the_author_meta('first_name'); ?> <?php the_author_meta('last_name'); ?></em></p>
                                <div class="cat-content"><?php the_excerpt(); ?>
 
                                    <a href="<?php the_permalink(); ?>" class="pjax"><b>Читати більше</b></a> 
                                    <span class="omc-rarr">→</span>
                                </div> 
                                <br class="clear"> 
                            </article>



                             <!-- Остановить Цикл (но есть ключевое слово "else:" - смотрите далее). -->
                             <?php endwhile; else: ?>


                             <p>Sorry, no posts matched your criteria.</p>

                             <!-- ДЕЙСТВИТЕЛЬНО остановить Цикл -->
                             <?php endif; ?>
                           
                            
                            <?php wp_pagenavi(); ?>
                        
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
<!DOCTYPE html>
<html lang="ru">
  <head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="<?php bloginfo('description'); ?>">
    <meta name="author" content="">

    <link href="https://fonts.googleapis.com/css?family=PT+Sans:400,400i,700&amp;subset=cyrillic" rel="stylesheet">
    <link href="<?php bloginfo('template_url'); ?>/css/reset.css" rel="stylesheet" type="text/css" />
    <link href="<?php bloginfo('template_url'); ?>/css/jquery.bxslider.css" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.6.4/css/bootstrap-datepicker.min.css" rel="stylesheet" />
    <link href="<?php bloginfo('template_url'); ?>/css/boots.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php bloginfo('template_url'); ?>/style.css" rel="stylesheet" type="text/css" />
    <title><?php bloginfo('name'); ?></title>

   

    <?php if(!is_user_logged_in()){
           // header("Location: http://artpixel.com.ua");

    }?>
    <!--[if lt IE 9]>
            <script type="text/javascript" src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
            <script src="http://css3-mediaqueries-js.googlecode.com/files/css3-mediaqueries.js"></script>
    <![endif]-->

    <?php wp_head(); loadModule(); 


    ?>




</head>


<body>
	<div class="wrapper">
    	
        <!-- Шапка -->
        <header>
            
            <div class="logo">
                <a href="/" class="pjax">
                    <img src="<?php bloginfo('template_url'); ?>/images/logo.png" alt=" " />
                </a>
            </div>
            <div class="player">
                <div id="player1" class="aplayer"></div>
            </div>
            
            <div class="navigation">
            	<div class="show-hide">
                	<span></span>
                    <span></span>
                    <span></span>
                </div>

                <?php


                wp_nav_menu( array(
                    'theme_location'  => 'header-menu',
                    'container'       => 'nav', 
                    'menu_class'      => 'menu', 
                    'echo'            => true,
                    'fallback_cb'     => 'wp_page_menu',
                    'items_wrap'      => '<ul id="%1$s" class="%2$s">%3$s</ul>',
                    'depth'           => 0,
                ) );
                ?>

            </div>
            
        </header>
        <!-- Конец Шапка -->
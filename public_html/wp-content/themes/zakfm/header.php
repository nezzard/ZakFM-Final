<!DOCTYPE html>
<html lang="ru">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="<?php bloginfo('description'); ?>">
    <meta name="author" content="">
    <script src="https://cdn.socket.io/socket.io-1.4.5.js"></script>

    <title><?php bloginfo('name'); ?></title>
<script>
  var socket = io('http://zakarpattyafm.com.ua:7080');
  socket.emit('seyGet');
  socket.on('sendSongg', function (data) {
    console.log(data);
    loadPlay(data);
  });




  function loadPlay(data){    


data.sort(function(a, b) {
  return a.key - b.key;
});




jQuery.each( data, function( key, value ) {
    var img;
    if(value.end){
        img = value.end;
    }
    else {
        img = 'http://placehold.it/150x150';
    }
    jQuery('.all-songs').append('<div class="one-song"><div class="one-song-in"><div class="num">'+value.key+'</div><a href="#" data-youtube="'+value.post.youtube[0]+'" class="one-song-thumb"><img class="minithumb" src="'+img+'"></a><div class="one-song-descr"><div class="one-song-tit"><a href="#"><span>'+value.post.artist[0]+'</span>'+value.post.song[0]+'</a></div></div></div></div>');

      
});
        

}
</script>    

    <?php if(!is_user_logged_in()){
            header("Location: http://artpixel.com.ua");

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
                <a href="#">
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
                <nav>
                    <ul>
                        <li><a class="active" href="#">Головна</a></li>
                        <li><a href="#">Новини</a></li>
                        <li><a href="#">Пошта привітань</a></li>
                        <li><a href="#">Архів пісень</a></li>
                        <li><a href="#">Реклама</a></li>
                        <li><a href="#">Контакти</a></li>
                    </ul>
                </nav>
            </div>
            
        </header>
        <!-- Конец Шапка -->
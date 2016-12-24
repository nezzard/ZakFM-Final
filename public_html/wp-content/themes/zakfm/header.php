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
    loadPlay(data);
  });




  function loadPlay(data){    
    
console.log(data[4]);

         /*   jQuery.getJSON("http://zakarpattyafm.com.ua/wp-json/wp/v2/media/"+value['post'][0].featured_media,
                function(data) {
                    dima.push({key: 1, p: 1});
                    jQuery('.all-songs').append('<div class="one-song"><div class="one-song-in"><div class="num">1</div><a href="#" class="one-song-thumb"><img class="minithumb" src="'+data.media_details.sizes.full.source_url+'"></a><div class="one-song-descr"><div class="one-song-tit"><a href="'+value.post[0].youtube[0]+'"><span>'+value.post[0].artist[0]+'</span>'+value.post[0].song[0]+'</a></div></div></div></div>');
             
            }) */


function imm(data){
dima = jQuery.getJSON("http://zakarpattyafm.com.ua/wp-json/wp/v2/media/"+13158)

            console.log(dima);
            var yura;
            ddd = dima.done(function(e){
                return e.source_url;

            })
            console.log(ddd);
}







var dima = new Array();
    jQuery.each( data, function( key, value ) {


jQuery('.all-songs').append('<div class="one-song"><div class="one-song-in"><div class="num">1</div><a href="#" class="one-song-thumb"><img class="minithumb" src="'+imm(value['post'][0].featured_media)+'"></a><div class="one-song-descr"><div class="one-song-tit"><a href="#"><span>'+value.post[0].artist[0]+'</span>'+value.post[0].song[0]+'</a></div></div></div></div>');

      
     });
        
        console.log(dima);

dima.forEach(function(item) {
  console.log(item);
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
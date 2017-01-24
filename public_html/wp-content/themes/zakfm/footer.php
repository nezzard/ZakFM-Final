        <!-- Подвал -->
        <footer>
            <div class="copy">
                © 2016 artpixel.com.ua
            </div>


                <?php
                wp_nav_menu( array(
                    'theme_location'  => 'header-menu',
                    'container'       => 'div', 
                    'container_class' => 'foot-menu', 
                    'echo'            => true,
                    'fallback_cb'     => 'wp_page_menu',
                    'items_wrap'      => '<ul id="%1$s" class="%2$s">%3$s</ul>',
                    'depth'           => 0,
                ) );
                ?>


        </footer>
        <!-- Конец Подвал -->
	</div>

    
	<script src="https://code.jquery.com/jquery-1.10.2.min.js"></script>
  <script src="<?php bloginfo('template_url'); ?>/js/jquery.pjax.js"></script> 

    <script>
    $(document).pjax('.pjax, .menu-item a, .wp-pagenavi a', '.cont', {fragment: '.cont', maxCacheLength: 10000, timeout: 0});


     
    </script>





    
    <!-- bxSlider Javascript file -->


    <script src="https://cdn.socket.io/socket.io-1.4.5.js"></script>

    
<script>
  var socket = io('http://zakarpattyafm.com.ua:7080');
  console.log("dasdads");

  socket.emit('seyGet');
  socket.on('changed', function(changed){
      console.log(2);
    var image = changed.image;
    changed = changed['changed'];
    //console.log(changed);
    jQuery('.aplayer-title').html(changed.song);
    jQuery('.aplayer-author').html('- '+changed.artist);
    jQuery('.aplayer-pic').css('background-image', 'url('+image+')');
  })
  socket.on('sendSongg', function (data) {
    jQuery('.all-songs').html('');
    loadPlay(JSON.parse(data));
        console.log(1);

  });




  function loadPlay(data){    
console.log(1);

data.sort(function(a, b) {
  return a.key - b.key;
});




jQuery.each( data, function( key, value ) {
    //console.log(value);
    var img;
    if(value.end){
        img = value.end;
    }
    else {
        img = 'http://placehold.it/150x150';
    }
    
    key = key+1;
    jQuery('.all-songs').append('<div class="one-song"><div class="one-song-in"><div class="num">'+key+'</div><a href="#" data-youtube="'+value.post.youtube[0]+'" class="one-song-thumb nextP"><img class="minithumb" src="'+img+'"></a><div class="one-song-descr"><div class="one-song-tit"><a href="#"><span>'+value.post.artist[0]+'</span>'+value.post.song[0]+'</a></div></div></div></div>');
    if(typeof(value.post.youtube[0]) !=='undefined'){
        console.log(value);
        jQuery('.nextP').addClass('thumb-yout');

    }
      
});
        

}
</script> 

	<script src="<?php bloginfo('template_url'); ?>/js/jquery.bxslider.min.js" ></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.6.4/js/bootstrap-datepicker.min.js" ></script>
      <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.6.4/locales/bootstrap-datepicker.uk.min.js" ></script>
    <script src="<?php bloginfo('template_url'); ?>/js/jquery.cache.js"></script>

    <script src="http://cdn.bootcss.com/aplayer/1.5.8/APlayer.min.js"></script>
      <script src="<?php bloginfo('template_url'); ?>/js/boots.min.js" ></script>
	<script>



  $('#join').click(function(){
    console.log(111);
    var name = $('#name').val();
    if (name != '') {
      socket.emit('join', name);
      socket.emit('room', 'chat');
      $('#login').detach();
      $('#chat').show();
      $('#msg').focus();
      ready = true;
      $.cookie('chatZakName', name, { expires: 1 });

    }
  });

  if($.cookie('chatZakName')){
   // socket.emit('join', $.cookie('chatZakName'));
    $('#login').css("display", "none");
    $('#chat').css("display", "block");
    ready = true;
  }

  

  $('#send').click(function(){
    if(ready) {
      var msg = $('#msg').val();
      socket.emit('send', msg, $.cookie('chatZakName'));
      $('#msg').val('');
    }
  });

  $('.form-inline').submit(function(e) {
    e.preventDefault();
      if(ready) {
      var msg = $('#msg').val();
      socket.emit('send', msg);
      $('#msg').val('');
    }
  });


    socket.on('chat', function(who, msg){
    console.log(who);
    if(ready) {
      $('#msgs').append('<li><b>' + who + ' написал:</b> ' + msg + '</li>');

    var objDiv = document.getElementById("msgs");
objDiv.scrollTop = objDiv.scrollHeight;
    }
  });




$('#datepicker').on('changeDate', function() {
    $('#my_hidden_input').val(
        $('#datepicker').datepicker('getFormattedDate')
    );

    
});





$('#datepicker').datepicker({
    language: "uk",
     autoclose: true,
     format: "yyyy-mm-dd",
}).on('changeDate', function() {
    window.location.href = "archive/?sortDate="+$('#my_hidden_input').val();
});

  $('.bxslider').bxSlider({
    infiniteLoop: true
  });

$(document).on('pjax:complete', function() {
  $('.bxslider').bxSlider({
    infiniteLoop: true
  });

ymaps.ready(init);

$('#datepicker').datepicker({
    language: "uk",
     autoclose: true,
     format: "yyyy-mm-dd",
     "setDate": new Date(),

}).on('changeDate', function() {
    window.location.href = "archive/?sortDate="+$('#my_hidden_input').val();
});;

});





  </script>


<!-- Modal -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title" id="myModalLabel">Відеокліп до пісні</h4>
        <span data-dismiss="modal" class="closeModal"  aria-hidden="true">&times;</span>
      </div>
      <div class="modal-body">
        <iframe id="youtubeModal" src="" allowfullscreen="true" allowscriptaccess="always" quality="high" bgcolor="#000000" name="playerid" width="530" height="300"></iframe>
      </div>
    </div>
  </div>
</div>



    <?php wp_footer(); ?>


    <script>



        jQuery('body').on('click', '.thumb-yout, .show-video a, .one-song-tit a', function() {
                  var src = 'http://www.youtube.com/embed/'+$(this).data('youtube');

        $('#myModal').modal('show');
        $('#myModal iframe').attr('src', src);
    });

    $('#myModal button').click(function () {
        
    });



$('#myModal').on('hidden.bs.modal', function (e) {
$('#myModal iframe').removeAttr('src');
})
  

  

</script>

<script src="//api-maps.yandex.ru/2.1/?lang=ru_RU" type="text/javascript"></script>

<script>
if ( $( "#map" ).length ) {

ymaps.ready(init);

function init() {

    var myMap = new ymaps.Map('map', {
            center: [48.442762, 22.723093],
            zoom: 8,
            controls: ['zoomControl']
        }, {
            // В нашем примере хотспотные данные есть только для 9 и 10 масштаба.
            // Поэтому ограничим диапазон коэффициентов масштабирования карты.
            minZoom: 9,
            maxZoom: 10
        });

        // Шаблон URL для данных активных областей.
        // Источник данных будет запрашивать данные через URL вида:
        // '.../hotspot_layer/hotspot_data/9/tile_x=1&y=2', где
        // x, y - это номер тайла, для которого запрашиваются данные,
        // 9 - значение коэффициента масштабирования карты.
    //var tileUrlTemplate = 'hotspot_data/%z/tile_x=%x&y=%y',

        // Шаблон callback-функции, в которую сервер будет оборачивать данные тайла.
        // Пример callback-функции после подстановки - 'testCallback_tile_x_1_y_2_z_9'.
        //keyTemplate = 'testCallback_tile_%c',

        // URL тайлов картиночного слоя.
        // Пример URL после подстановки -
        // '.../hotspot_layer/images/9/tile_x=1&y=2.png'.
        //imgUrlTemplate = 'images/%z/tile_x=%x&y=%y.png',

        // Создадим источник данных слоя активных областей.
        //objSource = new ymaps.hotspot.ObjectSource(tileUrlTemplate, keyTemplate),

        // Создаем картиночный слой и слой активных областей.
        //imgLayer = new ymaps.Layer(imgUrlTemplate, {tileTransparent: true}),
        //hotspotLayer = new ymaps.hotspot.Layer(objSource, {cursor: 'help'});


// Создаем геодезический круг радиусом 1000 километров.
var circle = new ymaps.Circle([[48.442762, 22.723093], 10000], {}, {
    geodesic: true
});
// Добавляем круг на карту.
myMap.geoObjects.add(circle);


    // Добавляем слои на карту.
    myMap.layers.add(hotspotLayer);
    myMap.layers.add(imgLayer);
    
}

}
</script>

</body>
                                  
</html>

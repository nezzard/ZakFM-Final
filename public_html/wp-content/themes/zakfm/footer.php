        <!-- Подвал -->
        <footer>
            <div class="copy">
                © 2017 artpixel.com.ua
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
      //$(document).pjax('.pjax, .menu-item a, .wp-pagenavi a', '.cont', {fragment: '.cont', maxCacheLength: 10000, timeout: 0});


     
    </script>


<script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

  ga('create', 'UA-91151325-1', 'auto');
  ga('send', 'pageview');

</script>


    
    <!-- bxSlider Javascript file -->


    <script src="https://cdn.socket.io/socket.io-1.4.5.js"></script>

    
<script>
  var socket = io('http://zakarpattyafm.com.ua:7080');





  socket.emit('seyGet');
  socket.on('changed', function(changed){
    var image = changed.image;
    if(image === false){
      jQuery('.aplayer-pic').css('background-image', '');
    }else {
      jQuery('.aplayer-pic').css('background-image', 'url('+image+')');
    }
    changed = changed['changed'];
    //console.log(changed);
    jQuery('.aplayer-title').html(changed.song);
    jQuery('.aplayer-author').html('- '+changed.artist);
  })
  socket.on('sendSongg', function (data) {
    jQuery('.all-songs').html('');
    loadPlay(JSON.parse(data));
  });




  function loadPlay(data){    

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


/*
  $('#join').click(function(e){
    e.preventDefault();
    var name = $('#name').val();
    if (name != '') {
      socket.emit('join', name);
     
      $('#login').detach();
      $('#chat').show();
      $('#msg').focus();
      $('.noLogged').removeClass('noLogged');
      //  ready = true;
      $.cookie('chatZakName', name, { expires: 1, path: '/' });

    }
  });

  $('#name').keydown(function(ev){

    if (ev.which === 13)
        $('#````').click();
});*/





  $( document ).ready(function() {
    socket.emit('getJsonChat');
    socket.on('ChatJson', function(data){

      var chatMsgs = JSON.parse(data);
      chatMsgs.forEach(function(msg){
        $('#msgs').append('<li  class="' + msg['role'] +'"><b>' + msg['who'] + ':</b> ' + msg['msg'] + '</li>');

        var objDiv = document.getElementById("msgs");
        objDiv.scrollTop = objDiv.scrollHeight;

      })
    

    })
  });

  <?php if(current_user_can('manage_options')) {$role = 'admin'; } else {$role = 'user'; }?>


$('#join').click(function(){
   
     
    var name = $('#name').val();
    if (name != '') {
      socket.emit('join', name);
     
      $('#login').detach();
      $('#chat').show();
      $('#msg').focus();
      $('.noLogged').removeClass('noLogged');
      //  ready = true;
      $.cookie('chatZakName', name, { expires: 1, path: '/' });
   }
  });

  $('.form-inline').submit(function(e) {

   e.preventDefault();
    var name = $('#name').val();
    if (name != '') {
      socket.emit('join', name);
     
      $('#login').detach();
      $('#chat').show();
      $('#msg').focus();
      $('.noLogged').removeClass('noLogged');
      //  ready = true;
      $.cookie('chatZakName', name, { expires: 1, path: '/' });
    }
  });





  if($.cookie('chatZakName')){
   // socket.emit('join', $.cookie('chatZakName'));
    $('#login').css("display", "none");
    $('#chat').css("display", "block");
    ready = true;
  }

  

  $('#send').click(function(){
   
      var msg = $('#msg').val();
      if(msg.length > 0){
        socket.emit('send', msg, $.cookie('chatZakName'), '<?php echo $role; ?>');
        $('#msg').val('');    
      }

   
  });

  $('.form-inline-msg').submit(function(e) {

    e.preventDefault();
    
      var msg = $('#msg').val();
      if(msg.length > 0){
        socket.emit('send', msg , $.cookie('chatZakName'), '<?php echo $role; ?>');
        $('#msg').val('');
      }
    
  });


    socket.on('chat', function(who, msg, role){
      console.log(role);
      $('#msgs').append('<li class="'+ role + '"><b>' + who + ':</b> ' + msg + '</li>');

      var objDiv = document.getElementById("msgs");
      objDiv.scrollTop = objDiv.scrollHeight;
   
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



$(document).on('ready  pjax:complete', function() {
  $('.bxslider').bxSlider({
    infiniteLoop: true
  });

  ymaps.ready(init);

$('#datepicker').on('changeDate', function() {
    $('#my_hidden_input').val(
        $('#datepicker').datepicker('getFormattedDate')
    );

    
});


if ( $( "#map" ).length ) {

ymaps.ready(init);

function init() {

    var myMap = new ymaps.Map('map', {
            center: [48.438615, 22.719363],
            zoom: 16,
            controls: ['zoomControl']
        }, {
            // В нашем примере хотспотные данные есть только для 9 и 10 масштаба.
            // Поэтому ограничим диапазон коэффициентов масштабирования карты.
            minZoom: 9,
            maxZoom: 18
        });

    myPlacemark = new ymaps.Placemark([48.438615, 22.719363], {
            // Чтобы балун и хинт открывались на метке, необходимо задать ей определенные свойства.
            balloonContentHeader: "Балун метки",
            balloonContentBody: "Содержимое <em>балуна</em> метки",
            balloonContentFooter: "Подвал",
            hintContent: "Хинт метки"
        });
    myMap.behaviors.disable('scrollZoom'); 
    myMap.geoObjects.add(myPlacemark);

    
    
}

}


$('#datepicker').datepicker({
    language: "uk",
     autoclose: true,
     format: "yyyy-mm-dd",
}).on('changeDate', function() {
    window.location.href = "archive/?sortDate="+$('#my_hidden_input').val();
});

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
    <?php wp_footer(); ?>



</body>
                                  
</html>

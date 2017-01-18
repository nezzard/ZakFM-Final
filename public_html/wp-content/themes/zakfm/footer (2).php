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
        $(document).pjax('.pjax, .menu-item a, .wp-pagenavi a', '.cont', {fragment: '.cont', maxCacheLength: 1000000, timeout: 0});


        
    </script>





    
    <!-- bxSlider Javascript file -->


    <script src="https://cdn.socket.io/socket.io-1.4.5.js"></script>

    
<script>
  var socket = io('http://zakarpattyafm.com.ua:7080');


  socket.emit('seyGet');
  socket.on('changed', function(changed){
    var image = changed.image;
    changed = changed['changed'];
    console.log(changed);
    jQuery('.aplayer-title').html(changed.song);
    jQuery('.aplayer-author').html('- '+changed.artist);
    jQuery('.aplayer-pic').css('background-image', 'url('+image+')');
  })
  socket.on('sendSongg', function (data) {
    //console.log(data);
    jQuery('.all-songs').html('');
    loadPlay(data);
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
        console.log(JSON.stringify(value.post.youtube[0]));
        jQuery('.nextP').addClass('thumb-yout');

    }
      
});
        

}
</script> 

	<script src="<?php bloginfo('template_url'); ?>/js/jquery.bxslider.min.js" ></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.6.4/js/bootstrap-datepicker.min.js" ></script>
      <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.6.4/locales/bootstrap-datepicker.uk.min.js" ></script>

    <script src="http://cdn.bootcss.com/aplayer/1.5.8/APlayer.min.js"></script>
      <script src="<?php bloginfo('template_url'); ?>/js/boots.min.js" ></script>
	<script>

$('#datepicker').datepicker({
    language: "uk",
     autoclose: true,
     format: "yyyy-mm-dd",
 

});
$('#datepicker').on('changeDate', function() {
  var chechDate =  $('#my_hidden_input').val($('#datepicker').datepicker('getFormattedDate'));
  console.log($('#datepicker').datepicker('getFormattedDate'));
  window.location.href = '/archive/?sortDate='+$('#datepicker').datepicker('getFormattedDate');
    
});

$('.form-control-date').datepicker({
    language: "uk",
     autoclose: true,
     format: "yyyy-mm-dd"
});




  $('.bxslider').bxSlider({
    infiniteLoop: true
  });
 
$(document).on('pjax:complete', function() {
  $('.bxslider').bxSlider({
    infiniteLoop: true
  });

  $('#datepicker').datepicker({
      language: "uk",
       autoclose: true,
       format: "yyyy-mm-dd",
   

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



    <?php wp_footer(); ?>
    <script src="<?php bloginfo('template_url'); ?>/js/jquery.cache.js"></script>


    <script>
        jQuery('body').on('click', '.thumb-yout, .show-video a, .one-song-tit a', function() {

        var src = 'http://www.youtube.com/v/'+$(this).data('youtube')+'&amp;autoplay=1&version=3&enablejsapi=1';
        $('#myModal').modal('show');
        $('#myModal iframe').attr('src', src);
    });

    $('#myModal button').click(function () {
        
    });



$('#myModal').on('hidden.bs.modal', function (e) {
$('#myModal iframe').removeAttr('src');
})


  $('#join').click(function(){
    var name = $('#name').val();
    if (name != '') {
      socket.emit('join', name);
      $('#login').detach();
      $('#chat').show();
      $('#msg').focus();
      ready = true;
      $.cookie('chatZakName', name, { expires: 1 });

    }
  });

  if($.cookie('chatZakName')){
    socket.emit('join', $.cookie('chatZakName'));
    $('#login').css("display", "none");
    $('#chat').css("display", "block");
    ready = true;
  }

  

  $('#send').click(function(){
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
    }
  });

</script>

</body>
                                  
</html>

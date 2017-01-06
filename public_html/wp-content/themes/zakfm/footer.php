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
    <link href="https://fonts.googleapis.com/css?family=PT+Sans:400,400i,700&amp;subset=cyrillic" rel="stylesheet">

    <link href="<?php bloginfo('template_url'); ?>/css/reset.css" rel="stylesheet" type="text/css" />
    <link href="<?php bloginfo('template_url'); ?>/style.css" rel="stylesheet" type="text/css" />
    
    <!-- bxSlider CSS file -->
    <link href="<?php bloginfo('template_url'); ?>/css/jquery.bxslider.css" rel="stylesheet" />
	<script src="https://code.jquery.com/jquery-1.10.2.min.js"></script>
    <script src="<?php bloginfo('template_url'); ?>/js/jquery.pjax.js"></script>

    <script>
        $(document).pjax('header', '.wrap-in')
    </script>

    
    <!-- bxSlider Javascript file -->
	<script src="<?php bloginfo('template_url'); ?>/js/jquery.bxslider.min.js" ></script>
    
    <script src="http://cdn.bootcss.com/aplayer/1.5.8/APlayer.min.js"></script>
	

<!-- Modal -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">Modal title</h4>
      </div>
      <div class="modal-body">
        <iframe src=""></iframe>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary">Save changes</button>
      </div>
    </div>
  </div>
</div>



    <?php wp_footer(); ?>
    <script src="<?php bloginfo('template_url'); ?>/js/boots.min.js"></script>
    <link href="<?php bloginfo('template_url'); ?>/css/boots.min.css" rel="stylesheet" type="text/css" />
    <script src="<?php bloginfo('template_url'); ?>/js/jquery.cache.js"></script>


    <script>
        jQuery('body').on('click', '.one-song-thumb, .show-video a, .one-song-tit a', function() {

        var src = 'http://www.youtube.com/v/'+$(this).data('youtube')+'&amp;autoplay=1';
        $('#myModal').modal('show');
        $('#myModal iframe').attr('src', src);
    });

    $('#myModal button').click(function () {
        $('#myModal iframe').removeAttr('src');
    });


  $('#join').click(function(){
    var name = $('#name').val();
    if (name != '') {
      socket.emit('join', name);
      $('#login').detach();
      $('#chat').show();
      $('#msg').focus();
      ready = true;
      $.cookie('chatZakName', name);

    }
  });

  if($.cookie('chatZakName')){
    socket.emit('join', $.cookie('chatZakName'));
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
      $('#msgs').append('<li>' + who + ' написал: ' + msg + '</li>');
    }
  });

</script>

</body>
                                  
</html>

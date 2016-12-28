        <!-- Подвал -->
        <footer>
            <div class="copy">
                © 2016 artpixel.com.ua
            </div>
            <div class="foot-menu">
                <ul>
                    <li><a href="#">Головна</a></li>
                    <li><a href="#">Новини</a></li>
                    <li><a href="#">Пошта привітань</a></li>
                    <li><a href="#">Архів пісень</a></li>
                    <li><a href="#">Реклама</a></li>
                    <li><a href="#">Контакти</a></li>
                </ul>
            </div>
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


    <script>
        jQuery('body').on('click', '.one-song-thumb, .show-video a, .one-song-tit a', function() {

        console.log(1);
        var src = 'http://www.youtube.com/v/'+$(this).data('youtube')+'&amp;autoplay=1';
        $('#myModal').modal('show');
        $('#myModal iframe').attr('src', src);
    });

    $('#myModal button').click(function () {
        $('#myModal iframe').removeAttr('src');
    });
</script>

</body>
                                  
</html>

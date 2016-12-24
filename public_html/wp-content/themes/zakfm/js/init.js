/*var option = {
    element: document.getElementById('player1'),                       // Optional, player element
    narrow: false,                                                     // Optional, narrow style
    autoplay: false,                                                    // Optional, autoplay song(s), not supported by mobile browsers
    showlrc: 0,                                                        // Optional, show lrc, can be 0, 1, 2, see: ###With lrc
    mutex: true,                                                       // Optional, pause other players when this player playing
    theme: '#e6d0b2',                                                  // Optional, theme color, default: #b7daff
    mode: 'random',                                                    // Optional, play mode, can be `random` `single` `circulation`(loop) `order`(no loop), default: `circulation`
    preload: 'metadata',                                               // Optional, the way to load music, can be 'none' 'metadata' 'auto', default: 'auto'
    listmaxheight: '513px',                                             // Optional, max height of play list
    music: {                                                           // Required, music info, see: ###With playlist
        //title: parserVal['parserVal']['parsed'][2],                                          // Required, music title
        //author: parserVal['parserVal']['parsed'][3],                          // Required, music author
        url: 'http://195.234.148.51:8000/;stream.mp3',  // Required, music url
        //pic: parserVal['parserVal']['lastfm'],  // Optional, music picture
        lrc: '[00:00.00]lrc here\n[00:01.00]aplayer'                   // Optional, lrc, see: ###With lrc
    }
}
var ap = new APlayer(option);
// Слайдер на главной
$(document).ready(function(){
  $('.bxslider').bxSlider();
});
// Конец Слайдер на главной

$('.show-hide').click(function(){
	$('.navigation ul').fadeToggle();	
});


var refInterval = window.setInterval('update()', 10000); // 30 seconds
var update = function() {

jQuery(document).ready(function($) {
    var data = {
        action: 'my_action',
        whatever: 1234
    };

    // 'ajaxurl' не определена во фронте, поэтому мы добавили её аналог с помощью wp_localize_script()
    jQuery.post( MyAjax.ajaxurl, data, function(response) {
        console.log('Получено с сервера: ' + response);
    });
});

}
update();

var option = {
    element: document.getElementById('player1'),                       // Optional, player element
    narrow: false,                                                     // Optional, narrow style
    autoplay: false,                                                    // Optional, autoplay song(s), not supported by mobile browsers
    showlrc: 0,                                                        // Optional, show lrc, can be 0, 1, 2, see: ###With lrc
    mutex: true,                                                       // Optional, pause other players when this player playing
    theme: '#e6d0b2',                                                  // Optional, theme color, default: #b7daff
    listmaxheight: '513px',                                             // Optional, max height of play list
    music: {                                                           // Required, music info, see: ###With playlist
        title: "Радіо",                                          // Required, music title
        author: "Закарпаття ФМ",                          // Required, music author
        url: 'http://195.234.148.51:8000/;stream.mp3',  // Required, music url
        //pic: parserVal['parserVal']['lastfm'],  // Optional, music picture
    }
}
var ap = new APlayer(option);
// Слайдер на главной




// Конец Слайдер на главной

$('.show-hide').click(function(){
	$('.navigation ul').fadeToggle();	
});


var refInterval = window.setInterval('update()', 10000); // 30 seconds
var update = function() {



}
update();




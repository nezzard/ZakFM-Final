<?
function parseMusic(){

  require_once 'simplehtmldom.php';
  $url = "http://zakfm.font-family.ru/wp-content/themes/zakfm/Studio.html";
  $html = file_get_html($url, false/*, $context*/);
	$table = $html->find('table', 0);
  global $matches;
  $matches = array();
  $table2 = $table->find('tr td div');

  foreach($table2 as $a){
		$matches[] =  $a->plaintext;
 }


//освобождаем ресурсы
$html->clear(); 
unset($html);


  global $wpdb;
  $post = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE post_title = %s AND post_type='song' AND post_status='publish'", $matches[3]. ' - ' .$matches[2] ));
  if ( empty($post) ) {

    //Парс ролика
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_URL, 'https://www.googleapis.com/youtube/v3/search?part=snippet&order=viewCount&q='.rawurlencode($matches[2].'+'.$matches[3]).'&type=video&key=AIzaSyAvDAdEnqrStOJNnpnGy9BkrC_sG-gcHIU');
    $result = curl_exec($ch);
    curl_close($ch);
    $obj = json_decode($result, true);



    //Парс картинки
    $chl = curl_init();
    curl_setopt($chl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($chl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($chl, CURLOPT_URL, 'http://ws.audioscrobbler.com/2.0/?method=artist.getinfo&artist=Ани+Лорак&api_key=603b0439073b39ec6b890756f4345933&format=json');
    $resultl = curl_exec($chl);
    curl_close($chl);
    $objl = json_decode($resultl, true);
    // Создаем массив
      $post_data = array(
       'post_title'    => $matches[3]. ' - ' .$matches[2],
       'post_status'   => 'publish',
       'post_type'     => 'song',
      );

    // Вставляем данные в БД
    $post_id = wp_insert_post( $post_data );


    update_field('виконавець', $matches[3], $post_id);
    update_field('пісня', $matches[2], $post_id);
    update_field('youtube', $obj['items'][0]['id']['videoId'], $post_id);


  require_once(ABSPATH .'wp-admin/includes/media.php');
  require_once(ABSPATH .'wp-admin/includes/file.php');
  require_once(ABSPATH .'wp-admin/includes/image.php');


    media_sideload_image(  $objl['artist']['image'][2]['#text'], $post_id);

    return array('post' => $post, 'parsed' => $matches, 'youtube' => $obj['items'][0]['id']['videoId'], 'lastfm' => $objl['artist']['image'][2]['#text']);
  
  }
  else{


    return array('post' => $post, 'parsed' => $matches, 'youtube' => get_field('youtube',  91), 'lastfm' => $objl['artist']['image'][2]['#text']);

  }
}








/* Парсер следующих*/
add_action('wp_ajax_my_action', 'nextPlay');
add_action('wp_ajax_nopriv_my_action', 'nextPlay');
function nextPlay(){

  require_once 'simplehtmldom.php';


  $url = "http://zakfm.font-family.ru/wp-content/themes/zakfm/Studio.html";
  $html = file_get_html($url, false/*, $context*/);


  $table = $html->find('table', 1);
  
  //$matches = array();
  $table2 = $table->find('tr td div b');

  $arr1=array();
  for ($i=0; $i<=count($table2); $i+=3) {
    array_push( $arr1, array( song => $table2[$i]->plaintext, artist=>$table2[$i+1]->plaintext) );
 usleep(300000);

  }


//освобождаем ресурсы
$html->clear(); 
unset($html);
  
 
wp_send_json( $arr1 );

  wp_die(); // выход нужен для того, чтобы в ответе не было ничего лишнего, только то что возвращает функция

 //   return array('parsed-a' => $arr1[0], 'parsed-s' => $arr1[1], 'youtube' => $obj['items'][0]['id']['videoId'], 'lastfm' => $objl['artist']['image'][2]['#text']);


}








function nextList(){
  foreach (nextPlay() as $key => $value) {
  ?>
  <div class="one-song">
    <div class="one-song-in">
        <div class="num">
            2
          </div>
          <a href="#" class="one-song-thumb">
              <img src="<?php echo lastPars($value['artist']); ?>">
          </a>
          <div class="one-song-descr">
              <div class="one-song-tit">
                  <a href="#">
                      <span>
                          <?php echo $value['artist']; ?>
                      </span>
                      <?php echo $value['song']; ?>
                  </a>
              </div>
          </div>
      </div>
  </div>
  <?php
  }
}



function lastPars($artist){
    //Парс картинки
  $chl = curl_init();
  curl_setopt($chl, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($chl, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($chl, CURLOPT_URL, 'http://ws.audioscrobbler.com/2.0/?method=artist.getinfo&artist='.rawurlencode($artist).'&api_key=603b0439073b39ec6b890756f4345933&format=json');
  $resultl = curl_exec($chl);
  curl_close($chl);
 $lastUrl = json_decode($resultl, true);
  return $lastUrl['artist']['image'][2]['#text'];
}


function youPars($artist, $song){
    //Парс ролика
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_URL, 'https://www.googleapis.com/youtube/v3/search?part=snippet&order=viewCount&q='.rawurlencode($artist.'+'.$song).'&type=video&key=AIzaSyAvDAdEnqrStOJNnpnGy9BkrC_sG-gcHIU');
  $result = curl_exec($ch);
  curl_close($ch);
  return $youtCode = json_decode($result, true);
}

?>
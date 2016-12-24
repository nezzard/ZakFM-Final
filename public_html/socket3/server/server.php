<?php
require_once '../src/HemiFrame/Lib/WebSocket.php';





function parseY($songs){
	$ch = curl_init();
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_URL, 'https://www.googleapis.com/youtube/v3/search?part=snippet&order=viewCount&q='.rawurlencode($songs['post_title']).'&type=video&key=AIzaSyAvDAdEnqrStOJNnpnGy9BkrC_sG-gcHIU');
  $result = curl_exec($ch);
  curl_close($ch);
  return $obj = json_decode($result, true);
}


function parseL($songs){
  $chl = curl_init();
  curl_setopt($chl, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($chl, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($chl, CURLOPT_URL, 'http://ws.audioscrobbler.com/2.0/?method=artist.getinfo&artist='.rawurlencode($songs['post_title']).'&api_key=603b0439073b39ec6b890756f4345933&format=json');
  $resultl = curl_exec($chl);
  curl_close($chl);
  return $objl = json_decode($resultl, true);
}

set_time_limit(0);

$socket = new \HemiFrame\Lib\WebSocket("185.65.245.45", 9090);
$socket->setEnableLogging(true);




$socket->on("receive", function($client, $data) use($socket) {

$json = json_decode($data);
global $wpdb;

$songs = array();
$songsArr = array();
foreach ($json as $d) {
		$songs[] = 'post_title = "'.$d->song.' - '.$d->artist.'"';
		$songsArr[] = array('post_title' => $d->song. ' - ' .$d->artist, 'song_name' => $d->song , 'song_artist' => $d->artist);
}


$string = implode(" OR ", $songs);

$results = $wpdb->get_results('SELECT post_title, ID FROM wp_posts WHERE post_type = "song" AND post_status = "publish" AND '.$string, ARRAY_A );



$d1 = array_column($songsArr, 'post_title');
$d2 = array_column($results, 'post_title');
$diff = array_diff($d1, $d2);
$result = array_intersect_key($songsArr, $diff);
print_r($result);


var_export($notFounds);








foreach ($result as $key => $notFound) {
	sleep(1);
	$post_data = array(
	  'post_title'    => $notFound['post_title'],
	  'post_type'     => 'song',
	  'post_content'  => '',
	  'post_status'   => 'publish',
	  'post_author'   => 1,
	);

	// Вставляем запись в базу данных
	$post_id = wp_insert_post( $post_data );	

	$obj = parseY($notFound['post_title']);
	$objImg = parseL($notFound['post_title']);

	update_field( 'youtube', $obj['items'][0]['id']['videoId'], $post_id);
	update_field( 'виконавець', $notFound['song_artist'], $post_id );
	update_field( 'пісня', $notFound['song_name'], $post_id );

  media_sideload_image(  $objImg['artist']['image'][2]['#text'], $post_id);

	foreach ($socket->getClientsByPath($client->path) as $item) {
		$socket->sendData($item, json_encode($result));
	}

  $results = $wpdb->get_results('SELECT post_title, ID FROM wp_posts WHERE post_type = "song" AND post_status = "publish" AND '.$string, ARRAY_A );

}


/*$a1 = array('one','two');
$a2 = array('one','two','three');*/



//var_dump($songsArr);
//var_dump($results);	

//$a1 = array('one','two');
//var_dump(array('one','two','three'));

//var_dump(array_diff($a2, $a1)); // returns TRUE

});



$socket->on("error", function($socket, $client, $phpError, $errorMessage, $errorCode) {
	var_dump("Error: => " . implode(" => ", $phpError));
});

$socket->startServer();

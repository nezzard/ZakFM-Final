<?php

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
?>
<?php

function chunk_it($filename)
 {
   $size_of_chunk = 4*(1024); // bytes in each chunk, 4k works well
   $data_buffer = '';//data you will return, set to empty string by default

   $fh = fopen('http://127.0.0.1:8080/', 'rb');
   if ($fh === false)
   {
     return false;
   }
   while (!feof($fh))


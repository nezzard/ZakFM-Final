<?php

require_once '../src/HemiFrame/Lib/WebSocket.php';


$socket = new \HemiFrame\Lib\WebSocket("195.28.183.165", 9090);
$client = $socket->connect();

if ($client) {
	$socket->sendData($client, "My data");
	$socket->disconnectClient($client);
}


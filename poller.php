<?php
session_start();
header('Access-Control-Allow-Origin: *');
header('Content-Type: text/event-stream');
header('Cache-Control: no-cache');
header('Connection: keep-alive');
require 'classes/idiorm.php';
require 'config.php';
require 'classes/poll.php';

while(true) {


	//TODO refactor this to be the poll requester!

	//make a query to ask for new polls

	//if there is a new poll..

	//if no new polls..
	//$ret = array('success' => juu);
	//$ret = array('s' => true);  
	$poll = new Poll();

	$kissa = rand(0,1000);         
	echo "data: " . json_encode(array('j' => $kissa, 'newPoll' => $poll->getLatestPoll())) . " \n\n";
	//echo "data: " . json_encode(array('j' => $kissa, 'newPoll' => 'moi')) . " \n\n";
	ob_flush();
	flush();
	sleep(1);
}

?>
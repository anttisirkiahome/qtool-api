<?php
session_start();
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS, PUT');
require 'libraries/flightphp/flight/Flight.php';
require 'classes/idiorm.php';
require 'classes/poll.php';
require 'config.php';

Flight::register('poll', 'Poll');

Flight::route('GET /', function(){
    echo 'OK.';
});

Flight::route('GET /api/', function(){
	//if not authenticated
   echo 'moi';
   //header('HTTP/1.0 401 Unauthorized');
   //exit;
});

Flight::route('GET /api/poll/themes', function(){
	$poll = Flight::poll();
	echo json_encode($poll->getThemes());
});

Flight::route('GET /api/poll', function(){
	$poll = Flight::poll();
	echo json_encode($poll->getLatestPoll());
});

Flight::route('PUT|OPTIONS /api/poll', function(){
	$poll = Flight::poll();
	$r = Flight::request();
	$r = json_decode($r->body, true);
	if(isset($r['type'])) {
		echo json_encode(['success' => $poll->vote($r['updateId'])]);
	} else {
		echo json_encode(['success' => $poll->publishPoll($r['updateId'], $r['duration'])]);	
	}
	
});

Flight::route('POST /api/poll', function(){
	$poll = Flight::poll();

	$r = Flight::request();
	$r = json_decode($r->body, true);

	echo json_encode(['success' => $poll->savePoll($r['q']), 'poll' => $r['q']]);
   
});

Flight::route('POST /api/auth/', function(){
  
    $r = Flight::request();
    $associative_array = json_decode($r->body, true);
    var_dump($associative_array);
    

    //save username and password

    //ask if user exists
    	//does -> continue

    	//doesnt -> 401
		    //header('HTTP/1.0 401 Unauthorized');
  			//exit;
});


/* TEST */

Flight::route('GET /api/poll/test/publishPoll', function(){
	$poll = Flight::poll();
	echo json_encode($poll->publishPoll(49));
});

Flight::start();
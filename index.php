<?php
header('Access-Control-Allow-Origin: *');
require 'libraries/flightphp/flight/Flight.php';

Flight::route('GET /', function(){
    echo 'OK.';
});

Flight::route('GET /api/', function(){
	//if not authenticated
   echo 'moi';
   //header('HTTP/1.0 401 Unauthorized');
   //exit;
});

Flight::route('GET /api/latestPoll', function(){
while(true) {
	$time = date('r');
	echo "data: The server time is: {$time}\n\n";
	ob_flush();
	flush();
	sleep(1);
}
});

Flight::route('/api/poll', function(){
	
	$r = Flight::request();
	$r = json_decode($r->body, true);
	#var_dump($r);
   	#print_r($r['q']);

	echo json_encode(['success' => true]);
   
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

Flight::start();
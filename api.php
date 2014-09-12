<?php
require 'libraries/flightphp/flight/Flight.php';

Flight::route('/', function(){
    echo 'hello world!';
});

Flight::start();
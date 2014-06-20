<?php
require 'libraries/flightphp/Flight.php';

Flight::route('/', function(){
    echo 'hello world!';
});

Flight::start();
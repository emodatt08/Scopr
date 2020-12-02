<?php
// var_dump("Im here"); die;
require "controllers/StationController.php";
require_once "controllers/helpers/Headers.php";
$send = new StationController();
$cross = new Headers();
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // The request is using the POST method
    foreach($cross->cross_origin() as $key => $value){
        echo $value;
    }
    
    echo $send->allStations($_REQUEST);
}else{
    header("Content-type: application/json");
    echo json_encode(["error"=>"Invalid request type"]);
}
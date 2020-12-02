<?php
// var_dump("Im here"); die;
require "controllers/SMSController.php";
require_once "controllers/helpers/Headers.php";
$send = new SMSController();
$cross = new Headers();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // The request is using the POST method
    foreach($cross->cross_origin() as $key => $value){
        echo $value;
    }
    echo $send->sendSMS($_REQUEST);
}elseif($_SERVER['REQUEST_METHOD'] === 'GET'){
    if(isset($_GET['code'])){
        
        echo $send->confirmSMS($_GET);
    }else{
        header("Content-type: application/json");
        echo json_encode(["error"=>"Kindly insert the code"]);
    }
}else{
    header("Content-type: application/json");
    echo json_encode(["error"=>"Invalid request type"]);
}
<?php
// var_dump("Im here"); die;
require "controllers/LoginController.php";
require_once "controllers/helpers/Headers.php";
$send = new LoginController();
$cross = new Headers();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // The request is using the POST method
    foreach($cross->cross_origin() as $key => $value){
        echo $value;
    }
    echo $send->login($_REQUEST);
}elseif($_SERVER['REQUEST_METHOD'] === 'GET'){
    if(isset($_GET['id'])){
        
        echo $send->updateSecretCode($_GET['id']);
    }else{
        header("Content-type: application/json");
        echo json_encode(["error"=>"Kindly insert the user id"]);
    }
}else{
    header("Content-type: application/json");
    echo json_encode(["error"=>"Invalid request type"]);
}

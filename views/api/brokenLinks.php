<?php

require_once  __DIR__."/../../config.php";
require_once SITE_ROOT."/controllers/SearchController.php";
require_once SITE_ROOT."/controllers/helpers/Headers.php";
$send = new SearchController();
$cross = new Headers();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // The request is using the POST method
    foreach($cross->cross_origin() as $key => $value){
        echo $value;
    }
    if(isset($_POST['url'])){      
        echo $send->setBrokenLinks($url);
    }else{
        header("Content-type: application/json");
        echo json_encode(["error"=>"No url"]);
    }
}else{
    header("Content-type: application/json");
    echo json_encode(["error"=>"Invalid request type"]);
}
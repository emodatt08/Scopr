<?php

class Headers{

    public function response(){
        return header("Content-type:application/json");
    }
    public function cross_origin(){
   $header = [ 
       'Origin' => header('Access-Control-Allow-Origin: *'), 
       'Credentials' => header("Access-Control-Allow-Credentials: true"),
       'Methods' =>  header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS'),
       'Age' =>  header('Access-Control-Max-Age: 1000'),
       'Headers' =>   header('Access-Control-Allow-Headers: Origin, Content-Type, X-Auth-Token , Authorization')
   ];

      return $header ;       
    }

    public function json($response = []){
        
        return json_encode($response);
    }


    public function logAction($reqparams,$state=0,$method, $resparams, $method_name){
        date_default_timezone_set('Africa/Accra');
        //print_r($_SERVER['DOCUMENT_ROOT']."mobile/storage/logs/" .date('Y-m-d').".csv");die;
        $logfile = $_SERVER['DOCUMENT_ROOT']."/total/storage/logs/" .date('Y-m-d').".csv";
       
        $state = ($state==0)?'Request':'Response';
        $date = date('Y-m-d H:i:s');
        $reqparams = json_encode($reqparams);
        $resparams = json_encode($resparams);
        $insert = "\n\n $method $state at $date on $method_name with request params as: ".":". $reqparams . " and response as :". $resparams;
        file_put_contents($logfile,$insert ,FILE_APPEND | LOCK_EX);
    }

}
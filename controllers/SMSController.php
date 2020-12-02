<?php
require_once __DIR__."/../config.php";
require_once SITE_ROOT. "/models/SMS.php";
require_once SITE_ROOT. "/controllers/helpers/Headers.php";
require_once SITE_ROOT. "/controllers/helpers/Requests.php";
class SMSController extends SMS{

public $db;
public $response = ["error" => false];
public function __construct()
    {
        $this->sms =  new SMS();
        $this->response_header = new Headers();
        $this->requests = new Requests();
        $this->response = [];
    }

    public function sendSMS($request){
        $request = json_decode(file_get_contents("php://input"), true);
        $this->response_header->logAction($request, 1, "POST", "", "sms");
        if(isset($request['id'])){
            //get user phone number
            $user = $this->sms->getUser($request['id']);
            
            if($user){
                //create otp code
                $code = rand(1000, 5000);             
                //send otp
                $send = $this->sms->curl_post("Your One time code is ".$code, $user['phone_no']);
                $send = ["responseCode"=> 200];
                $this->response_header->logAction($request, 1, "POST", $send, "send");
                if($send["responseCode"] == 200){
                    //store otp
                    $data = ['id' => $request['id'], 'mesg' => $code];
                    //var_dump($data); die();
                    $store = $this->sms->insertOTP($data);
                        if($store){
                            $this->response["responseCode"] = "200";
                            $this->response["responseMessage"] = "Successfully sent sms";
                            $this->response_header->response();
                            $this->response_header->logAction($request, 1, "GET", $this->response, "allsms");
                            return $this->response_header->json($this->response);
                        }
                }

                
            }else{
                $this->response["responseCode"] = "304";
                $this->response["responseMessage"] = "Failed, no sms for this teller";    
                $this->response_header->response();
                $this->response_header->logAction($request, 1, "GET", $this->response, "allsms");
                return $this->response_header->json($this->response);
            }
           
        }else{
            $this->response["responseCode"] = "304";
            $this->response["responseMessage"] = "Required parameter is missing!";
            $this->response_header->response();
            $this->response_header->logAction($request, 1, "GET", $this->response, "allsms");  
            return $this->response_header->json($this->response);
        }
        }


 public function confirmSMS($request){
     
    $this->response_header->logAction($request, 1, "POST", "", "confirmSMS");
    // $request = json_decode(file_get_contents("php://input"), true);
    if ($this->requests->validateOTP($request)) {
        //check for otp
          $check = $this->sms->getTimeStamp($request['code']);
          $findDiff =  $this->checkTimeDiff($check['created_at']);
        //   var_dump($findDiff); die;
                if($findDiff < 60){
                    //delete otp record
                    $delete = $this->sms->deleteOTP($request['code']);
                    $this->response["responseCode"] = "200";
                    $this->response["responseMessage"] = "OTP verification Successfull";
                    $this->response_header->response();
                    $this->response_header->logAction($request, 1, "POST", $this->response, "confirmSMS");
                    return $this->response_header->json($this->response);

                }else{

                    $this->response["responseCode"] = "304";
                    $this->response["responseMessage"] = "OTP verification expired or incorrect";
                    $this->response_header->response();
                    $this->response_header->logAction($request, 1, "POST", $this->response, "confirmSMS");
                    return $this->response_header->json($this->response);
                }
                }else{
                    $this->response["responseCode"] = "404";
                    $this->response["responseMessage"] = "Required parameter is missing!";
                    $this->response_header->response();
                    $this->response_header->logAction($request, 1, "POST", $this->response, "confirmSMS");
                    return $this->response_header->json($this->response);
            }
    
    }

    public function checkTimeDiff($timestamp){
        $timenow = new DateTime(date('Y-m-d H:i:s'));
        $timestamp = new DateTime($timestamp);
        $diff =  $timenow->getTimestamp() - $timestamp->getTimestamp();
        return $diff;
    }
 

}
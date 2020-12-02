<?php
require_once __DIR__."/../config.php";
require_once SITE_ROOT. "/models/DB.php";
require_once SITE_ROOT. "/controllers/helpers/Headers.php";




/**
 * Created by PhpStorm.
 * User: Ripper
 * Date: 11/22/2017
 * Time: 8:24 PM
 */
class LoginController extends DB
{
    public $db;
    public $response = ["error" => false];

    public function __construct()
    {
        $this->db =  new DB();
        $this->response_header = new Headers();
        $this->response = [];
    }

    public function login($request){
        // json response array
        
        $request = json_decode(file_get_contents("php://input"), true);
        //var_dump($request); die;
        if (isset($request['username']) && isset($request['password'])) {
            // receiving the post params
            $username = $request['username'];
            $password = $request['password'];
           
            // get the user by email and password
            $user =  $this->db->getUserByEmailAndPassword($username, $password);
           
            if ($user != false) {  
                if($user['first_login'] == "1"){
                    $status = "true";
                }else{
                    $status = "false";
                }
                // user exists
                $this->response["responseCode"] = "200";
                $this->response["responseMessage"] = "User verified successfully";
                $this->response["user"]["uid"] = $user["userid"];
                $this->response["user"]["username"] = $user["username"];
                $this->response["user"]["email"] = $user["email"];
                $this->response["user"]["branch_id"] = $user["branch_id"];
                $this->response["user"]["phone_no"] = $user["phone_no"];
                $this->response["user"]["secret_code"] = $user["teller_code"];
                $this->response["user"]["first_login"] = $status;
                $this->response["user"]["updated_at"] = $user["updated_at"];
                $this->response_header->response();
                $this->response_header->cross_origin();
                return $this->response_header->json($this->response);
            } else {
                // user with these credentials doesnt exists
                $this->response["responseCode"] = "404";
                $this->response["responseMessage"] = "Login credentials are wrong. Please try again!";                
                $this->response_header->response();
                $this->response_header->cross_origin();
                return $this->response_header->json($this->response);
            }
        } else {
            // required post params is missing
            $this->response["responseCode"] = "304";
            $this->response["responseMessage"] = "Required parameters username or password is missing!";
            $this->response_header->response();
            $this->response_header->cross_origin();
            return $this->response_header->json($this->response);
        }
    }


    public function updateSecretCode($id){
        $getCode = $this->db->updateSecretCode($id);
            if($getCode){
                // $setFirstLoginStatus = $this->db->setFirstTimeStatus($id);
                $this->response["responseCode"] = "200";
                $this->response["responseMessage"] = "User verified successfully";
                $this->response['secret_code'] = $getCode;
                return $this->returnResp(); 
            }else{
                $this->response["responseCode"] = "404";
                $this->response["responseMessage"] = "An error ocurred";
                $this->response['secret_code'] = false;
                return $this->returnResp(); 
            }
    }

   
    public function returnResp(){
        $this->response_header->response();
        $this->response_header->cross_origin();
        return $this->response_header->json($this->response);
    }


    public function logOut($request){
        $setOnlineStatus = $this->db->setOnlineStatus(0);
        if($setOnlineStatus){
           return true;
        }else{
            return false;
        }
    }

}
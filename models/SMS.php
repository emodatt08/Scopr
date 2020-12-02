<?php

/**
 * @author Kollan Hillary
 *
 */

require "Connection.php";

class SMS extends Connection
{
    private $table_name;
    private $timestamp;
    private $trans;
    private $date;
    private $time;
    private $private;
  


    function __construct() {
        // connecting to database
        $db = new Connection();
        $this->conn = $db->connect();
        $this->config = parse_ini_file("config.ini");
        $this->trans = "TF".strtoupper(date("YmdHis").rand(1000,9000));
        $this->timestamp = date("Y-m-d H:i:s");
        $this->date = date("Y-m-d");
        $this->time = date("H:i:s");
        

    }

    public function table($tablename){
        $this->table_name= $tablename;
        return $this->table_name;
    }


    public function curl_post($mesg, $no){

        $data = array(
                "api_key" => $this->config['api_key'],
                "merchant_id" => $this->config['merchant_id'],
                "message" => $mesg,
                "recipients" => $no
              );
        
        $data_string = json_encode($data);
        $ch = curl_init($this->config['url']);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                'Content-Length: ' . strlen($data_string))
        );

        $result = curl_exec($ch);
        return json_decode($result, true);
        
             
    }


    /**
     * Update status of otp
     * returns user details
     */

    public function updateStatus($id){
        
        $status = "1";
        
         $stmt = $this->conn->prepare("UPDATE otp SET status = ? userid = ?");
        
         $stmt->bind_param("ss", $status, $id);
         
            try{   
                 
                $result = $stmt->execute();
                
                $stmt->close();
            }catch(\Exception $e){
                
                return $e->getMessage();
            }
            
            // check for successful update
            if ($result) {
                return true;
            } else {
                return false;
            }
    }

    /**
     * Get timestamp
     */
    public function getTimeStamp($mesg) {
        $stmt = $this->conn->prepare("SELECT created_at
        FROM otp WHERE  mesg = '$mesg' "); 
        if ($stmt->execute()) {
            $trans = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
            $stmt->close();
            return $trans[0];
        } else {
            return null;
        }
    }

    /**
     * Get user
     */
    public function getUser($id) {
        $stmt = $this->conn->prepare("SELECT phone_no
        FROM users WHERE userid = '$id'"); 
        if ($stmt->execute()) {
            $trans = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
            $stmt->close();
            return $trans[0];
        } else {
            return null;
        }
    }

     /**
     * Delete OTP
     */
    public function deleteOTP($id) {
        $stmt = $this->conn->prepare("DELETE 
        FROM otp WHERE mesg = '$id'"); 
        if ($stmt->execute()) {
            $stmt->close();
            return true;
        } else {
            return null;
        }
    }

     /**
     * Insert OTP
     */

    public function insertOTP($data){
        $stmt = $this->conn->prepare("
        INSERT INTO otp(
            mesg,
            userid)VALUES(
                ?,
                ?)");
        $stmt->bind_param("ss", 
        $data['mesg'], 
        $data['id']);
        $result = $stmt->execute();
        $stmt->close();

        // check for successful store
        if ($result) {
            return true;
        } else {
            return false;
        }
    }
    

}
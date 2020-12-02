<?php

/**
 * @author Kollan Hillary
 *
 */

require "Connection.php";

class DB extends Connection
{
    private $table_name;
    private $timestamp;

    function __construct() {
        // connecting to database
        $db = new Connection();
        $this->conn = $db->connect();
        $this->timestamp = date("Y-m-d H:i:s");
    }

    public function table($tablename){
        $this->table_name= $tablename;
        return $this->table_name;
    }



    /**
     * Update secret code
     * returns user details
     */

    public function updateSecretCode($id){
        
        $uuid = (string) rand(1000,9000);
        $status = "0";
        
         $stmt = $this->conn->prepare("UPDATE users SET teller_code = ?, first_login = ? WHERE userid = ?");
        
         $stmt->bind_param("sss", $uuid, $status, $id);
         
            try{   
                 
                $result = $stmt->execute();
                
                $stmt->close();
            }catch(\Exception $e){
                
                return $e->getMessage();
            }
            
            // check for successful update
            if ($result) {
                return $uuid;
            } else {
                return false;
            }
    }
    

    /**
     * Get user by email and password
     */
    public function getUserByEmailAndPassword($username, $password) {
       
        $stmt = $this->conn->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        
        if ($stmt->execute()) {
            $user = $stmt->get_result()->fetch_assoc();
            $stmt->close();
            // verifying user password
            $encrypted_password = $user['password'];
            $hash = $this->checkhashSSHA($password);
            // check if password is equal
            if ($encrypted_password == $hash) {
                // user authentication details are correct
                return $user;
            }
        } else {
            return null;
        }
    }



    /**
     * Decrypting password
     * @param salt, password
     * returns hash string
     */
    public function checkhashSSHA($password) {

        $hash = md5($password);

        return $hash;
    }

    /**
     * set user online status
     * @return boolean
     */

     public function setOnlineStatus($request, $status){
        $stmt = $this->conn->prepare("UPDATE users SET online = ?  WHERE id = ? ");
        $stmt->bind_param("ss", $status, $request['id']);
       try{
      
       $result = $stmt->execute();
       $stmt->close();
       }catch(\Exception $e){
           return $e->getMessage();
       }
       // check for successful update
       if ($result) {
           return true;
       }else{
            return false;
       }
     }
     /**
     * set user online status
     * @return boolean
     */

    public function setFirstTimeStatus($id){
        $stmt = $this->conn->prepare("UPDATE users SET first_login = ?  WHERE userid = ? ");
        $stmt->bind_param("ss", "0", $id);
       try{
      
       $result = $stmt->execute();
       $stmt->close();
       }catch(\Exception $e){
           return $e->getMessage();
       }
       // check for successful update
       if ($result) {
           return true;
       }else{
            return false;
       }
     }

}
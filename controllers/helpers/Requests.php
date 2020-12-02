<?php

class Requests{

    public function validateTrans($request){
        if(
        isset($request['username'])&& 
        isset($request['account_no'])&&
        isset($request['branch_id'])&&
        isset($request['teller_id'])&&
        isset($request['station_id'])&&
        isset($request['amount'])){
            return true;
        }else{
            return false;
        }
        
        
    }

    public function validateOTP($request){
        if(isset($request['code'])){
            return true;
        }else{
            return false;
        }
        
        
    }
   


}
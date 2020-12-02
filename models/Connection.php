<?php
// Ensure reporting is setup correctly
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
/**
 * Created by PhpStorm.
 * User: Ripper
 * Date: 11/22/2017
 * Time: 6:26 AM
 */
class Connection
{
    
    public $conn;
    /**
     * Database config variables
     */
    const HOST = "127.0.0.1";
    const USER = "root";
    const PORT = "3306";
    const PASSWORD = "";
    const DATABASE = "leaks";

    /**
     * Database connection method
     * @return mixed
     */
    public function connect(){
        // Connecting to mysql database
        // try{
        //     $this->conn = new mysqli(self::HOST, self::USER, self::PASSWORD, self::DATABASE);
        //     return $this->conn;
        // }catch(mysqli_sql_exception $e){
        //     var_dump($e->getMessage()); die;
        // }
        
            try{
                $this->conn = new PDO('mysql:host='.self::HOST.';port='.self::PORT.';dbname='. self::DATABASE. ';charset=utf8', self::USER, self::PASSWORD);
                $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $this->conn->setAttribute(PDO::ATTR_EMULATE_PREPARES, FALSE);
                return $this->conn;
            }catch(\Exception $e){
                var_dump($e->getMessage()); die;
            }
            
    }

}
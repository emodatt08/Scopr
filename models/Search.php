<?php

/**
 * @author Kollan Hillary
 *
 */

require "Connection.php";
require "DomDocumentParser.php";

class Search extends Connection
{
    private $parser;
    private $timestamp;
    private $date;
    private $time;
    private $status;
    private $url;


    function __construct() {
        // connecting to database
        $db = new Connection();
        $this->conn = $db->connect();
        $this->timestamp = date("Y-m-d H:i:s");
        $this->date = date("Y-m-d");
        $this->time = date("H:i:s");
        $this->status = "1";
    }


        public function parsePages($url){
            $parser = new DomDocumentParser($url);
            return $parser->getLinks();
        }

        public function parseTitles($url){
            $parser = new DomDocumentParser($url);
            return $parser->getTitleTags();
        }

        public function parseMetas($url){
            $parser = new DomDocumentParser($url);
            return $parser->getMetaTags();
        }

        public function parseImages($url){
            $parser = new DomDocumentParser($url);
            return $parser->getImages();
        }

    /**
     * Get all links
     */
    public function all($page, $pageSize, $request) {
        $term = $request;
        $fromLimit = ($page - 1) * $pageSize;
        // page 1 : (1 - 1)   * 20 = 0
        // page 2 : (2 - 1)  * 20 = 20
        // page 3 : (3 - 1)  * 20 = 40
        try{
            $stmt = $this->conn->prepare("
            SELECT * FROM sites 
            where title LIKE '%$term%' 
            OR url LIKE '%$term%'
            OR keywords LIKE '%$term%'
            OR description LIKE '%$term%'
            ORDER BY clicks DESC LIMIT $fromLimit, $pageSize");
            
            if($stmt->execute()) {
                $search = $stmt->fetchAll(PDO::FETCH_ASSOC);
                return  $search;
            } else {
                return null;
            }
        }catch(\PDOException $e){
             var_dump($e->getMessage());
        }
        
    }

     /**
     * Get all links
     */
    public function getCount($request) {
        $term = $request;
        try{
            $stmt = $this->conn->prepare("
            SELECT * FROM sites 
            where title LIKE '%$term%' 
            OR url LIKE '%$term%'
            OR keywords LIKE '%$term%'
            OR description LIKE '%$term%'
            ORDER BY clicks DESC");
            
            if($stmt->execute()) {
                $search = $stmt->fetchAll(PDO::FETCH_ASSOC);
                $count = count($search);
                return  $count;
            } else {
                return null;
            }
        }catch(\PDOException $e){
             var_dump($e->getMessage());
        }
        
    }

    public function allImages($page, $pageSize, $request) {
        $term = $request;
        $fromLimit = ($page - 1) * $pageSize;
        // page 1 : (1 - 1)   * 20 = 0
        // page 2 : (2 - 1)  * 20 = 20
        // page 3 : (3 - 1)  * 20 = 40
        try{
            $stmt = $this->conn->prepare("
            SELECT * FROM images 
            where image_alt LIKE '%$term%' 
            OR image_link LIKE '%$term%'
            OR raw_image_link LIKE '%$term%'
            AND broken = '0' ORDER BY clicks DESC LIMIT $fromLimit, $pageSize");
            
            if($stmt->execute()) {
                $search = $stmt->fetchAll(PDO::FETCH_ASSOC);
                return  $search;
            } else {
                return null;
            }
        }catch(\PDOException $e){
             var_dump($e->getMessage());
        }
        
    }

     /**
     * Get all links
     */
    public function getImageCount($request) {
        $term = $request;
        try{
            $stmt = $this->conn->prepare("
            SELECT * FROM images 
            where image_alt LIKE '%$term%' 
            OR image_link LIKE '%$term%'
            OR raw_image_link LIKE '%$term%'
            AND broken='0'");
            
            if($stmt->execute()) {
                $search = $stmt->fetchAll(PDO::FETCH_ASSOC);
                $count = count($search);
                return  $count;
            } else {
                return null;
            }
        }catch(\PDOException $e){
             var_dump($e->getMessage());
        }
        
    }



    /**
     * Get all links
     */
    public function linksWithNoDescription() {
        
        try{
            $stmt = $this->conn->prepare("
            SELECT * FROM sites 
            WHERE description = ''
            AND no_desc = '1'
            LIMIT 7 ");
            
            if($stmt->execute()) {
                $search = $stmt->fetchAll(PDO::FETCH_ASSOC);
                return  $search;
            } else {
                return null;
            }
        }catch(\PDOException $e){
             var_dump($e->getMessage());
        }
        
    }


    /**
     * Get all links
     */
    public function linksWithNoTitle() {
        
        try{
            $stmt = $this->conn->prepare("
            SELECT * FROM sites 
            WHERE 
            title = ''
            AND no_title = '1'     
            LIMIT 4 ");
            
            if($stmt->execute()) {
                $search = $stmt->fetchAll(PDO::FETCH_ASSOC);
                return  $search;
            } else {
                return null;
            }
        }catch(\PDOException $e){
             var_dump($e->getMessage());
        }
        
    }


    public function insertIntoSites($data){
        $stmt = $this->conn->prepare("
        INSERT IGNORE INTO websites(
            website_term,
            website_url, 
            website_favicon, 
            count_id,
            created_at
            )VALUES(
                :website_term,
                :website_url, 
                :website_favicon, 
                :count_id,
                :date_created)");
        $stmt->bindParam(":website_term", $data['term']);
        $stmt->bindParam(":website_url", $data['username']); 
        $stmt->bindParam(":website_favicon", $data['favicon']); 
        $stmt->bindParam(":count_id", $data['count']);
        $stmt->bindParam(":date_created", $this->date." ". $this->time);
        $result = $stmt->execute();
        

        // check for successful store
        if ($result) {
            return true;
        } else {
            return false;
        }
    }



    public function updateSitesTitle($data){
        $no_title = (isset($data['title']) && $data['title'] != "") ? "1" : "0";
        $stmt = $this->conn->prepare("
        UPDATE sites SET title = :title, no_title = :no_title where url= :url");
        $stmt->bindParam(":title", $data['title']);
        $stmt->bindParam(":url", $data['url']); 
        $stmt->bindParam(":no_title", $no_title); 
        
        $result = $stmt->execute();
        // check for successful update
        if ($result) {
            return true;
        } else {
            return false;
        }
    }



    public function updateBrokenLinks($data){
        $broken = "1";
        $stmt = $this->conn->prepare("
        UPDATE images SET broken = :broken where url= :url");
        $stmt->bindParam(":broken", $broken);
        $stmt->bindParam(":url", $data['url']); 
        $result = $stmt->execute();
        // check for successful update
        if ($result) {
            return true;
        } else {
            return false;
        }
    }

    public function updateSitesDescriptionOrKeywords($data){
        //var_dump($data['description']); die();
        $no_description = (isset($data['description']) && $data['description'] != " ") ? "1": "0";
        $stmt = $this->conn->prepare("
        UPDATE sites SET description = :description, keywords = :keywords, no_desc = :no_desc where url = :url");
        $stmt->bindParam(":description", $data['description']);
        $stmt->bindParam(":keywords", $data['keywords']); 
        $stmt->bindParam(":url", $data['url']); 
        $stmt->bindParam(":no_desc", $no_description);
        $result = $stmt->execute();
        // check for successful update
        if ($result) {
            return true;
        } else {
            return false;
        }
    }

    public function updateUrlClicks($data){
        $stmt = $this->conn->prepare("
        UPDATE sites SET clicks = clicks + 1 where id = :id");
        $stmt->bindParam(":id", $data['id']); 
        
        $result = $stmt->execute();
        // check for successful update
        if ($result) {
            return true;
        } else {
            return false;
        }
    }


    public function updateImageUrlClicks($data){
        $stmt = $this->conn->prepare("
        UPDATE images SET clicks = clicks + 1 where raw_image_link = :url ");
        $stmt->bindParam(":url", $data['url']); 
        
        $result = $stmt->execute();
        // check for successful update
        if ($result) {
            return true;
        } else {
            return false;
        }
    }


    public function insertIntoSeedSites($data){
        try{
            $status = "1";
            $stmt = $this->conn->prepare("
            INSERT IGNORE INTO sites(
                url,
                title,
                description, 
                keywords, 
                status
                )VALUES(
                    :url,
                    :title,
                    :description, 
                    :keywords, 
                    :status)");
            $stmt->bindParam(":url", $data['url']);
            $stmt->bindParam(":title", $data['title']);
            $stmt->bindParam(":description", $data['description']); 
            $stmt->bindParam(":keywords", $data['keywords']); 
            $stmt->bindParam(":status", $status);
            $result = $stmt->execute();    
        }catch(\PDOException $e){
            var_dump("Sites Insert Error: ". $e->getMessage());
        }
       

        // check for successful store
        if ($result) {
            return true;
        } else {
            return false;
        }
    }

    public function insertIntoImages($data){
        try{
            $downloaded = "0";
            $status = "1";
            $stmt = $this->conn->prepare("
            INSERT IGNORE INTO images(
                website_url,
                image_link, 
                raw_image_link,
                image_title, 
                downloaded, 
                status,
                image_alt
                )VALUES(
                    :website_url,
                    :image_link,
                    :raw_image_link,
                    :image_title, 
                    :downloaded, 
                    :status,
                    :image_alt)");
            $stmt->bindParam(":website_url", $data['url']);
            $stmt->bindParam(":image_link", $data['src']); 
            $stmt->bindParam(":raw_image_link", $data['raw']);
            $stmt->bindParam(":image_title", $data['title']);
            $stmt->bindParam(":downloaded", $downloaded); 
            $stmt->bindParam(":status", $status);
            $stmt->bindParam(":image_alt", $data['alt']);
            $result = $stmt->execute();
        }catch(\PDOException $e){
            var_dump("Image Insert Error: ". $e->getMessage());
        }
       
        

        // check for successful store
        if ($result) {
            return true;
        } else {
            return false;
        }
    }
}
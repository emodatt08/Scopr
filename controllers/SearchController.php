<?php
require_once __DIR__."/../config.php";
require_once SITE_ROOT. "/models/Search.php";
require_once SITE_ROOT. "/controllers/helpers/Headers.php";
require_once SITE_ROOT. "/controllers/helpers/Requests.php";
class SearchController extends Search{

public $db;
public $response = ["error" => false];
public $alreadyCrawled = [];
public $crawling = [];
public $alreadyFoundImages = [];
public $description;
public $keyWords;
public $title;
public function __construct()
    {
        $this->search =  new Search();
        $this->response_header = new Headers();
        $this->requests = new Requests();
        $this->response = [];
    }

    public function getLinks($page, $pageSize,$request){
        $this->response_header->logAction($request, 1, "GET", "", "getLinks");

            $search = $this->search->all($page, $pageSize, $request);
            $count = $this->search->getCount($request);
            if($search && $count){
               
                $this->response_header->logAction($request, 1, "GET", $this->response, "getLinks");
                return ["count" => $count, "search" => $search];
            }else{
               return [];
            }
           
        }

        public function getImages($page, $pageSize,$request){
            $this->response_header->logAction($request, 1, "GET", "", "getImages");
    
                $search = $this->search->allImages($page, $pageSize, $request);
                $count = $this->search->getImageCount($request);
                if($search && $count){
                   
                    $this->response_header->logAction($request, 1, "GET", $this->response, "getImages");
                    return ["count" => $count, "search" => $search];
                }else{
                   return [];
                }
               
            }

        public function getLinksWithNoDesc(){
                $search = $this->search->linksWithNoDescription();
                
                if($search){
                   foreach($search as $href){
                    
                     $this->getKeywordsAndDescription($href['url']);
                    //  var_dump($href['url'], $this->description); die();
                     //update
                     $data = ["description" => $this->clean($this->description), "keywords" => $this->clean($this->keyWords), "url" => $href['url']];
                     
                     $this->search->updateSitesDescriptionOrKeywords($data);
                   }
                   
                }else{
                   return [];
                }
               
            }

            public function getLinksWithNoTitle(){
                $search = $this->search->linksWithNoTitle();
                //var_dump($search, count($search)); die();
                if($search){
                   foreach($search as $href){
                     $this->getTitle($href['url']);             
                   }
                   
                }else{
                   echo "empty";
                }
               
            }

            public function setBrokenLinks($url){
                
                $data = ['url'=>$url];
                $updateBrokenLink = $this->search->updateBrokenLinks($data);
                if($updateBrokenLink){
                    $this->response["responseCode"] = "200";
                    $this->response["responseMessage"] = "Url Broken Link Updated";
                    $this->response['data'] = $updateBrokenLink;
                    return $this->returnResp(); 
                }else{
                    $this->response["responseCode"] = "404";
                    $this->response["responseMessage"] = "An error ocurred";
                    $this->response['data'] = false;
                    return $this->returnResp(); 
               }
            }

            public function clean($actualString) {
            $string = $actualString;
             $cleanText = preg_replace('/[^A-Za-z0-9\-]/', '', $string);
             if(strlen($cleanText) > 0){
                $cleanText = preg_replace('/[^A-Za-z0-9\-]/', ' ', $string);
                return $cleanText;
             }else{
                return $actualString;
             }
                 // Removes special chars.
             }
    public function sendClick($request){
        $update = $this->search->updateUrlClicks($request);
            if($update){
                // $setFirstLoginStatus = $this->db->setFirstTimeStatus($id);
                $this->response["responseCode"] = "200";
                $this->response["responseMessage"] = "Url Click Count Updated";
                $this->response['data'] = $update;
                return $this->returnResp(); 
            }else{
                $this->response["responseCode"] = "404";
                $this->response["responseMessage"] = "An error ocurred";
                $this->response['data'] = false;
                return $this->returnResp(); 
            }
    }

    public function sendImageClick($request){
        //var_dump($request); die;
        $update = $this->search->updateImageUrlClicks($request);
            if($update){
                // $setFirstLoginStatus = $this->db->setFirstTimeStatus($id);
                $this->response["responseCode"] = "200";
                $this->response["responseMessage"] = "Image Url Click Count Updated";
                $this->response['data'] = $update;
                return $this->returnResp(); 
            }else{
                $this->response["responseCode"] = "404";
                $this->response["responseMessage"] = "An error ocurred";
                $this->response['data'] = false;
                return $this->returnResp(); 
            }
    }



    public function returnResp(){
        $this->response_header->response();
        $this->response_header->cross_origin();
        return $this->response_header->json($this->response);
    }

    public function followLinks($url, $seed){
         $links = $this->search->parsePages($url);

         foreach($links as $link){
            
            $href = $link->getAttribute("href");
            
            if(strpos($href, "#") !== false){
                continue;
            }elseif(substr($href, 0, 11) == "javascript:"){
                continue;
            }
            
            $href = $this->createLink($seed, $href);
            //$this->getKeywordsAndDescription($href);
            //echo $href."///".$this->description."<br/>";   
           // $this->getTitle($href);
            if(!in_array($href, $this->alreadyCrawled)){
                $this->alreadyCrawled[] = $href;
                $this->crawling[] = $href;
                //var_dump($this->crawling);
                $this->getDetails($href); 
            }  
              
        } 
        
        
     }

     public function followTitleLinks($url){
        $links = $this->search->parsePages($url);      
        foreach($links as $link){
           $href = $link->getAttribute("href");
          
           if(strpos($href, "#") !== false){
               continue;
           }elseif(substr($href, 0, 11) == "javascript:"){
               continue;
           }
       
           $href = $this->createLink($url, $href);
           //var_dump("href ", $href); die;
           $this->getTitle($href);  
             
       } 
       
       
    }


    public function followImageLinks($url){
        $links = $this->search->parsePages($url);      
        foreach($links as $link){
           $href = $link->getAttribute("href");
          
           if(strpos($href, "#") !== false){
               continue;
           }elseif(substr($href, 0, 11) == "javascript:"){
               continue;
           }
       
           $href = $this->createLink($url, $href);
           $this->getImageData($href);  
             
       } 
       
       
    }


     public function getDetails($url){
        $title = $this->title;
        $description = $this->description;
        $keyWords = $this->keyWords;

        $description = str_replace("\n", "", $description);
        $keyWords = str_replace("\n", "", $keyWords);
        //insert into DB
        $this->insertLink($url, $title, $description, $keyWords);
        
        echo "URL: $url, Title: $title, Description: $description, KeyWords: $keyWords";
    }

     public function returnCrawledLinks(){
         //$links = ["crawling" => $this->crawling, "alreadyCrawled" => $this->alreadyCrawled];
         return $this->alreadyCrawled;
     }

     private function getTitle($parsedUrl){
        $titles = $this->search->parseTitles($parsedUrl);        
        if(sizeof($titles) > 0 || $titles->item(0) != NULL){
            if($titles->item(0)->nodeValue){
                $this->title = str_replace("\n", "", $titles->item(0)->nodeValue);  
                $data = ["url" => $parsedUrl, "title" => $this->clean($this->title)];
                $this->search->updateSitesTitle($data);
            }
                  
        }       
    }

     private function getKeywordsAndDescription($url){
        $metas = $this->search->parseMetas($url);
        
        foreach($metas as $meta){
            if($meta->getAttribute("name") === "description"){
                $this->description = $meta->getAttribute("content");
            }

            if($meta->getAttribute("name") === "keywords"){
                $this->keyWords = $meta->getAttribute("content");
            }
        }

        
     }

     private function getImageData($url){
        $images = $this->search->parseImages($url);
                foreach($images as $image){
                    $raw = $image->getAttribute("src");
                    $src = $this->createLink($url, $raw);
                    $title = $image->getAttribute("title");
                    $alt = $image->getAttribute("alt");
                    $this->checkImages($src, $raw, $url, $alt, $title);
                }
     }


     private function insertLink($url, $title, $description, $keywords){
        try{
            $data = ['url'=> $url, 'title'=> $title, 'description'=> $description, 'keywords' => $keywords];
            $insert = $this->search->insertIntoSeedSites($data);
            if($insert){
                //echo "Inserted into sites table";
            }else{
                echo "Sorry...could not insert into sites table";
            }
        }catch(\Exception $e){
            echo "Error: ". $e->getMessage();
        }
     }

     private function insertImages($url, $raw, $src, $alt, $title){
        try{
            $data = ['url'=> $url, 'raw' => $raw, 'src'=> $src, 'alt'=> $alt, 'title' => $title];
            $insert = $this->search->insertIntoImages($data);
            if($insert){
                //echo "Inserted into Images table";
            }else{
                echo "Sorry...could not insert into Images table";
            }
        }catch(\Exception $e){
            echo "Error: ". $e->getMessage();
        }
     }
     
     private function checkImages($src,$raw, $url, $alt, $title){
         if(!in_array($src, $this->alreadyFoundImages)){
            $this->alreadyFoundImages[] = $src;
            $this->insertImages($url, $raw, $src, $alt, $title);
         }
     }

       
    public function createLink($src, $url){
        
            if(substr($url, 0, 4) == "http"){
                return $url;
            }else{
                return $this->parseUrl($url, $src);
            }
                 
        }
             
    public static function parseUrl($url, $src){
            if(substr($url, 0, 2) == "./"){
                return $parsedUrl = $src.substr($url, 1);
            }elseif(substr($url, 0, 3) == "../"){
                return $parsedUrl = $src.substr($url, 2);
            }elseif(substr($url, 0, 3) == "//"){
                return $parsedUrl = $src.substr($url, 1);
            }elseif(substr($url, 0, 4) == "///"){
                return $parsedUrl = $src.substr($url, 2);
            }else{
                return $src.$url; 
            }
        }

    public function formatResultsNum($num){
        if($num > 1){
            return "results";
        }else{
            return "result";
        }
    }
 
    

}
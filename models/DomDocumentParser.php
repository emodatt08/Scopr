<?php

class DomDocumentParser{
    private $doc;
    private $url;
    public function __construct($url){
        $options = [
            'http' => ['method' => 'GET', 'header' => 'User-Agent: scoprBot/0.1\n']
        ];

        $context = stream_context_create($options);
        $this->doc = new DomDocument();
        //@$this->doc->loadHTML(file_get_contents($url, false, $context));
        @$this->doc->loadHTML($this->fetch($url));
    }

    

    public function getLinks(){
        return $this->doc->getElementsByTagName('a');
    }

    public function getTitleTags(){
        return $this->doc->getElementsByTagName('title');
    }

    public function getMetaTags(){
        return $this->doc->getElementsByTagName('meta');
    }

    public function getImages(){
        return $this->doc->getElementsByTagName('img');
    }



    public function fetch($url){
        $this->url = $url;
        $header=array(
             'User-Agent: Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.9.2.12) Gecko/20101026 Firefox/3.6.12',
             'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
             'Accept-Language: en-us,en;q=0.5',
             'Accept-Encoding: gzip,deflate',
             'Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7',
             'Keep-Alive: 115',
            'Connection: keep-alive',
            );
        //$agent= 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 1.0.3705; .NET CLR 1.1.4322)';
        $agent = "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/86.0.4240.198 Safari/537.36";
 
             $curl = curl_init();
             curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
             curl_setopt($curl, CURLOPT_HEADER, false);
             curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
             curl_setopt($curl, CURLOPT_URL, $this->url);
             curl_setopt($curl, CURLOPT_REFERER, $this->url);
             //curl_setopt($curl,CURLOPT_HTTPHEADER,$header);
             curl_setopt($curl, CURLOPT_USERAGENT, $agent);
             curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
             $str = curl_exec($curl);
             curl_close($curl);
            //var_dump($str, $url); die;
             return $str;
 
         }
}
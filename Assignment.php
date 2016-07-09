<?php

/*
|--------------------------------------------------------------------------
| Assignment Class - Costrategix Assignment
|--------------------------------------------------------------------------
|
| The goal of this class is to handle ,
| - total number of HTTP requests. 
| - total download size for all requests.
|
*/

class Assignment {
    
    static $total_size = 0; 
    static $total_request = 0;    
    public $url;
    public $url_data;
    
    public function __construct($url) {
        
        $this->url = $url;
    }
    
   /*
    *returns total file size of any URL    
    */
    public function get_remote_file_size() {        

           $ch = curl_init();
           curl_setopt_array($ch, array(
               CURLOPT_URL => $this->url,
               CURLOPT_RETURNTRANSFER => true
               ));
           curl_exec($ch);

           $size = curl_getinfo($ch, CURLINFO_SIZE_DOWNLOAD);

           return $size;
        
           curl_close($c);

    }
   
    //returns data of any URL using php cURL
    public function get_url_data() {
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); //Set curl to return the data instead of printing it to the browser.
        curl_setopt($ch,CURLOPT_URL,$this->url);
        $info = curl_exec($ch);
        
        $this->url_data = $info;
        curl_close($ch);
        
        return $this;
    }
    
    
    //get total size and and total request of css files       
    public function css() {
        
        //$ch = file_get_contents($url);
        $info = $this->url_data;
        $regex = "/href=['\"](?P<css>([^'\"]+?\.css)[^'\"]*)/";
        if (preg_match_all($regex, $info, $matches)) {
            //print_r($matches);die();
           foreach($matches[1] as $value) {
               //echo $this->get_file_size($value).'<br>';
               //self::$total_size += $this->get_remote_file_size($value);
               self::$total_request++;
           }
        }
         return $this;       
        
    }
    
    //get total size and and total request of javascript files
    public function javascript() {
        //self::$total_request = 0;
        $info = $this->url_data;
        if (preg_match_all('/\<script(.*?)?\>(.|\\n)*?\<\/script\>/i', $info, $matches)) {
            
           foreach($matches[1] as $value) {

                //self::$total_size += $this->get_remote_file_size($value);
                self::$total_request++;
           }
        }
         return $this;
       
    }
    
    //get total size and and total request of image files
    public function image() {
  
        $info = $this->url_data;
        if (preg_match_all('/(?<=src=|background=|url\()(\'|")?(?<image>.*?)(?=\1|\))/i', $info, $matches)) {
           foreach($matches[1] as $value) {

               //self::$total_size += $this->get_remote_file_size($value);
               self::$total_request++;
           }
        }
        return $this;
        
    }
    
    //get totoal HTTP request from any URL
    public function total_http_request() {
        return self::$total_request;
    }
    
    //get totoal HTTP size from any URL
    public function total_http_size() {
        return self::$total_size;
    }
    
    
}

$obj = new Assignment('http://www.ridersdiary.in');
$total_http_request = $obj->get_url_data()->css()->javascript()->image()->total_http_request(); // total number of HTTP requests
$total_download_size = $obj->get_remote_file_size(); // total download size for all requests

echo 'Total Number Of HTTP Requests : '.$total_http_request.' ,<br>Total Download Size For All Requests : '.$total_download_size;





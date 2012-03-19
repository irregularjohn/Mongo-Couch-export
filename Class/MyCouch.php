<?php

class MyCouch
{		
			function connect()
			{
				$options['host'] = "localhost"; 
				$options['port'] = 5984;
				$couch = new CouchSimple($options); 
				$resp = $couch->send("GET", "/");
				
				$out[0]=$couch;
				$out[1]=$resp;
				
				return $out; 
			}
			
			function listDB($couch)
			{
				$resp = $couch->send("GET", "/_all_dbs"); 
				
				return $resp;
			}
			
			function createDB($couch,$name)
			{
				 $resp = $couch->send("PUT", "/".$name); 
				 return $resp;
			}
			
			function getAllDocs($couch,$name)
			{
				$resp = $couch->send("GET", "/".$name."/_all_docs"); 
				
				return $resp;
			}
			
			function createDoc($couch,$dbname,$docname,$data)
			{
				 $resp = $couch->send("PUT", "/".$dbname."/".$docname, $data); 
				 
				 return $resp;
			}
			
			function getDoc($couch,$dbname,$docname)
			{
				 $resp = $couch->send("GET", "/".$dbname."/".$docname); 
				 
				 return $resp;
			}
			
			function delDoc($couch,$dbname,$docname,$rev)
			{
				$resp = $couch->send("DELETE", "/".$dbname."/".$docname."?rev=".$rev); 
				 
				 return $resp;
			}
		
}

class CouchSimple {
    function CouchSimple($options) {
       foreach($options AS $key => $value) {
          $this->$key = $value;
       }
    } 
   
   function send($method, $url, $post_data = NULL) {
      $s = fsockopen($this->host, $this->port, $errno, $errstr); 
      if(!$s) {
         echo "$errno: $errstr\n"; 
         return false;
      } 

      $request = "$method $url HTTP/1.0\r\nHost: $this->host\r\n"; 

		$this->user=null;

      if ($this->user) {
         $request .= "Authorization: Basic ".base64_encode("$this->user:$this->pass")."\r\n"; 
      }

      if($post_data) {
         $request .= "Content-Length: ".strlen($post_data)."\r\n\r\n"; 
         $request .= "$post_data\r\n";
      } 
      else {
         $request .= "\r\n";
      }

      fwrite($s, $request); 
      $response = ""; 

      while(!feof($s)) {
         $response .= fgets($s);
      }

      list($this->headers, $this->body) = explode("\r\n\r\n", $response); 
      return $this->body;
   }
}

?>

<?php
   include "common.php";

// create curl resource 
   $myData = $_GET['myData'];
// echo var_dump($myData);
   $host = $myData["target"];
   $data = explode(',', $myData["source"])[0];
   $btn  = $myData["command"];

   $src  = array('device', 'power', 'code'); 
   $cmd  = array('/web/deviceinfo', '/web/powerstate?newstate=', '/web/remotecontrol?command=&&1'); 

   if($btn !== 'play') $url = "http://".$host.str_replace('&&1', $btn, str_replace($src, $cmd, 'code'));
   else                $url = "http://".$host.str_replace($src, $cmd, $data);

// exit(var_dump($url));

   $ch = curl_init(); 
   curl_setopt($ch, CURLOPT_VERBOSE, 1);
   curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
   curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 1); 
// set url 
   curl_setopt($ch, CURLOPT_URL, $url); 

// $output contains the output string 
   $output = curl_exec($ch); 

// close curl resource to free up system resources 
   curl_close($ch); 
   echo $output.'|'; 
   parse($output,$keys_vals); 
   if($keys_vals){
     echo '|';
     foreach($keys_vals as $k) echo "$k<br>";
   }

?>


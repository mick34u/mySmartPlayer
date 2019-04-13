<?php
   include "common.php";
   include "config.php";

// create curl resource 
   $myData = $_GET['myData'];
// echo var_dump($myData);
   $host = $myData["target"];
   $dtab = explode(',', $myData["source"]);
   $data = $dtab[0];
   $btn  = $myData["command"];
   
   $src  = array(
    "getStatus",
    "getCradleStatus",
    "getConvergedStatus",
    "getDeviceInfo",
    "getNotifications",
    "getTrafficStats",
    "getNetwork",
    "getSignal",
    "getClients",
    "getWlanClients",
    "getSmsCount",
    "getInbox",
    "login",
    "logout",
    "isLoggedIn",
    "save",
    "getToken");
   $cmd  = array(
    "/api/monitoring/status",
    "/api/cradle/status-info",
    "/api/monitoring/converged-status",
    "/api/device/information",
    "/api/monitoring/check-notifications",
    "/api/monitoring/traffic-statistics",
    "/api/net/current-plmn",
    "/api/device/signal",
    "/api/device/device_list",
    "/api/wlan/host-list",
    "/api/sms/sms-count",
    "/api/sms/sms-list",
    "/api/user/login",
    "/api/user/logout",
    "/api/user/state-login",
    substr(realpath(dirname(__FILE__)), strlen(realpath($_SERVER['DOCUMENT_ROOT'])))."/IPing.php?command=2",
    "/api/webserver/SesTokInfo");

   $file = "../data/curl_huawei_token.txt";

 foreach($dtab as $data){

   $ch = curl_init(); 
   curl_setopt($ch, CURLOPT_VERBOSE, true);
   curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
   curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 2); 

// prepare http headers with token and cookie 
   $headers = explode(PHP_EOL, file_get_contents($file));
   curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

// create url with corresponding API command 
   $url = "http://".$host.str_replace($src, $cmd, $data);
   curl_setopt($ch, CURLOPT_URL, $url);

// setting POSTFIELDS for POST commands
   if(function_exists($data)) call_user_func($data);

// if($data == 'save') exit($url.'#'.$headers[0].'#'.$output);
   $output  .= curl_exec($ch); 
// if($data == 'save') exit($url.'#'.$output);

   curl_close($ch);

// processing the output of certain commands
   if(function_exists($data.'_out')) call_user_func($data.'_out');
 }

   echo $output.'|';
   parse(str_replace('<?xml version="1.0" encoding="UTF-8"?>','<x>',$output),$keys_vals);
   if($keys_vals){
     echo '|';
     foreach($keys_vals as $k) echo "$k<br>";
   }

// this will login and save token and cookie from response header to disk
function login() {

   global $ch, $headers;
   global $un, $pw;

   $strg = '__RequestVerificationToken:';
   if(substr($headers[0], 0, strlen($strg)) == $strg) $token = substr($headers[0], strlen($strg));

   $xml = '<?xml version="1.0"?><request>
        <Username>'.$un.'</Username>
		<Password>'.base64_encode(hash('sha256', $un.$pw.$token, false)).'</Password>
        <password_type>4</password_type></request>';
   $headers[] = 'Content-Type: text/xml'; 
   curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
   curl_setopt($ch, CURLOPT_POST, true);
   curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
   curl_setopt($ch, CURLOPT_HEADERFUNCTION, 'headerf');

   $headers = array();//clear before "login_out"
}

// logout current user
function logout() {

   global $ch, $headers;

   $xml = '<?xml version="1.0"?><request><Logout>1</Logout></request>';
   $headers[] = 'Content-Type: text/xml';
   curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
   curl_setopt($ch, CURLOPT_POST, true);
   curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
}

// get list of last 3 messages
function getInbox() {

   global $ch, $headers;
  
   $xml = '<?xml version="1.0"?><request>
	<PageIndex>1</PageIndex>
	<ReadCount>3</ReadCount>
	<BoxType>1</BoxType>
	<SortType>0</SortType>
	<Ascending>0</Ascending>
	<UnreadPreferred>0</UnreadPreferred>
	</request>';
   $headers[] = 'Content-Type: text/xml';
   curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
   curl_setopt($ch, CURLOPT_POST, true);
   curl_setopt($ch, CURLOPT_POSTFIELDS, $xml); 
}

// redirect url: call IPing.php and save $output to disk
function save() {

   global $ch, $host, $url, $output, $headers;

   $headers     = [];
   $url         = str_replace($host, '127.0.0.1', $url); 
   curl_setopt($ch, CURLOPT_URL,        $url);
   curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
   curl_setopt($ch, CURLOPT_POST,       true);
   curl_setopt($ch, CURLOPT_POSTFIELDS, $output); 

}

// this will save a new token and cookie to disk 
function getToken_out(){

    global $output, $headers, $file;

    $obj = new SimpleXMLElement($output);
//  exit(' Token:'.$obj->TokInfo.' Cookie:'.$obj->SesInfo);

//  Schreiben eines Arrays in eine Datei 
    $headers = array(
     '__RequestVerificationToken:'.$obj->TokInfo,
     'Cookie:'.$obj->SesInfo );
    file_put_contents($file, implode(PHP_EOL, $headers), LOCK_EX); 
}

// save to disk token and cookie from function "headerf"
function login_out(){

   global $headers, $file;

   file_put_contents($file, implode(PHP_EOL, $headers), LOCK_EX); 
}

// this will extract token and cookie from login response header
function headerf($ch, $header) {

   global $headers;

   $strg = '__RequestVerificationToken:';
   if(substr($header, 0, strlen($strg)) == $strg) $headers[] = substr($header, 0, strlen($strg) + 32);
   $strg = 'Set-Cookie:';
   if(substr($header, 0, strlen($strg)) == $strg) $headers[] = substr($header, 4, strlen($strg) + 134);

// $headers[] = $header;//test!
   return strlen($header);   
}
?>


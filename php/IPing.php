<?php
   include "config.php";

// this requires a "chmod +s /usr/bin/ping" on Synology!
// check/adjust IP range below!

$c      = $_GET['command'];
$file   = '../data/hosts.txt';

// command=0: read hosts from disk
if($c == '0'){
  $x = file_get_contents($file);
  echo str_replace(PHP_EOL, '#', $x);
}

// command=1: ping within IP range and save to disk all active addresses
if($c == '1'){
  exec ($ping, $out, $ret);
  foreach($out as $k){
    $s = $from; $l = strlen($s); $i = strpos($k, $s); $j = strpos($k, ':'); 
    if($i > 0) $x .= ','.substr($k, $i+$l, $j-$i-$l).PHP_EOL;
  }
  file_put_contents($file, $x, LOCK_EX);
  echo str_replace(PHP_EOL, '#', $x);
}

// command=2: posted from curl_huawei.php with list of all clients
// extract and save to disk all hostnames and IP addresses
if($c == '2'){
   $s = $_POST["<?xml_version"]; 
   $a = 1;
   for($i = 0; $a > 0; ){
     $a = strpos($s, '<IpAddress>', $i);
     $h = strpos($s, '<HostName>',  $i);
     if($a > 0) { $i = $a + 11; $ip  = substr($s, $i, strpos($s, '<', $i) - $i); }
     if($h > 0) { $i = $h + 10; $hn  = substr($s, $i, strpos($s, '<', $i) - $i);
     if($ip > '0.0.0.0')        $x  .= $hn.','.$ip.PHP_EOL;                              }
   }
  file_put_contents($file, $x, LOCK_EX);
}

// command=3: called from index.php to save to disk an updated hosts table 
if($c == '3'){
  $x = str_replace('|', PHP_EOL, $_GET['source']);
  file_put_contents($file, $x, LOCK_EX);
}

?>

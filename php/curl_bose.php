<?php
    include "common.php";

//  create curl resource 
    $myData = $_GET['myData'];
    $host = $myData["target"];
    $data = $myData["source"];
    $btn  = $myData["command"];
 
    $file = '../data/curl_bose_sources.txt';

    $xml     = "<?xml version=1.0 ?>";
    $xmlkey  = $xml."<key state=press sender=Gabbo>&1</key>".'|'.$xml."<key state=release sender=Gabbo>&1</key>";
    $xmlvol  = $xml."<volume>&1</volume>";
    $xmlbass = $xml."<bass>&1</bass>";
    $xmlsrc  = $xml."<ContentItem source='&1' type='&2' location='&3' sourceAccount='&4' isPresetable=true></ContentItem>";

    $src = array(
        "play",
        "pause",
        "resume",
        "prev",
        "next",
        "vol",
        "bass",
        "mode0",
        "mode1",
        "mode2",
        "mode3",
        "power",
        "presets",
        "save",
        "info",
        "device");
    $cmd = array(
        "select",
        "key,PAUSE",
        "key,PLAY",
        "key,PREV_TRACK",
        "key,NEXT_TRACK",
        "volume",
        "bass",
        "key,REPEAT_OFF,SHUFFLE_OFF", 
        "key,REPEAT_ALL", 
        "key,REPEAT_ONE", 
        "key,SHUFFLE_ON",
        "key,POWER",
        "presets",
        "now_playing",
        "now_playing",
        "info");

    if(substr($btn, 0, 3) == "vol"  && substr($btn, 3, 1) != ",") $btn = substr($btn, 0, 3).','.substr($btn, 3);
    if(substr($btn, 0, 4) == "bass" && substr($btn, 4, 1) != ",") $btn = substr($btn, 0, 4).','.substr($btn, 4);
    $cmds   = explode(',', str_replace($src, $cmd, $btn));
    $url    = "http://".$host.":8090/".$cmds[0];

    if ($cmds[0] == 'select') {
//      set source,type,location,account 
        $dtab = explode(',', $data);
        $data = str_replace(array("&1","&2","&3","&4"), array($dtab[0],$dtab[2],$dtab[1],$dtab[3]), $xmlsrc);
    } else if ($cmds[0] == "volume") {
        $data = str_replace("&1", $cmds[1], $xmlvol);  if($cmds[1] == '') $data = '';
    } else if ($cmds[0] == "bass") {
        $data = str_replace("&1", $cmds[1], $xmlbass); if($cmds[1] == '') $data = '';
    } else if ($cmds[0] == "key") {
        $data = str_replace("&1", $cmds[1], $xmlkey);
        if($cmds[2] != '') {
        $data = $data.'|'.str_replace("&1", $cmds[2], $xmlkey);}
    } else {
        $data = "";
    }

    $ch = curl_init(); 
    curl_setopt($ch, CURLOPT_VERBOSE, 1); 
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 1); 
//  set url 
    curl_setopt($ch, CURLOPT_URL, $url); 

//  if data is empty: GET else POST with data
    if ($data != "") {
      curl_setopt($ch, CURLOPT_POST, true);
//    multiple commands like key...press and key...release!
      $dtab = explode('|', $data);
      for($i = 0; $i < count($dtab); $i++) {
//      exit(":url:".$url.":data:".$data);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $dtab[$i]); 
//      $output contains the response 
        $output = curl_exec($ch); 
      }
    } else {
//    exit(":url:".$url.":data:".$data);
//    $output contains the response 
      $output = curl_exec($ch); 
    }

//  close curl resource to free up system resources 
    curl_close($ch);

    echo $output.'|';               //output unformatted info
    parse($output,$keys_vals);      //output formatted info
    if($keys_vals){
     echo '|';
     $optn = '<option value="&&1,&&2,&&3,&&4">&&5</option>'; $x = array('','','','','');
     foreach($keys_vals as $k){
       echo "$k<br>";               //output short formatted info
       $s = 'NOWPLAYING.CONTENTITEM.SOURCE=';       $l = strlen($s);  
       if($s == substr($k, 0, $l)) $x[0] = substr($k, $l); else {
       $s = 'NOWPLAYING.CONTENTITEM.LOCATION=';     $l = strlen($s);  
       if($s == substr($k, 0, $l)) $x[1] = substr($k, $l); else {
       $s = 'NOWPLAYING.CONTENTITEM.TYPE=';         $l = strlen($s);  
       if($s == substr($k, 0, $l)) $x[2] = substr($k, $l); else {
       $s = 'NOWPLAYING.CONTENTITEM.SOURCEACCOUNT=';$l = strlen($s);  
       if($s == substr($k, 0, $l)) $x[3] = substr($k, $l); else {
       $s = 'NOWPLAYING.CONTENTITEM.ITEMNAME=';     $l = strlen($s);  
       if($s == substr($k, 0, $l)) $x[4] = substr($k, $l); }}}} 
     }
     $optn = str_replace(array('&&1','&&2','&&3','&&4','&&5'), $x, $optn);
     echo '|'.$optn;
    
     if($btn == 'save') file_put_contents($file, $optn.PHP_EOL, FILE_APPEND | LOCK_EX); 

    }
?>


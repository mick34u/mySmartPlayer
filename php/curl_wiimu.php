<?php
    include "common.php";

// create curl resource 
   $myData = $_GET['myData'];
// echo var_dump($myData);
   $host = $myData["target"];
   $data = $myData["source"];
   $btn  = $myData["command"];
   $play = "setPlayerCmd:";
 
   $file = '../data/curl_wiimu_sources.txt';

   $soaptype            = 'Content-type: text/xml';
   $soapact['CreateQ']  = 'Soapaction: "urn:schemas-wiimu-com:service:PlayQueue:1#CreateQueue"';
   $soapact['BrowseQ']  = 'Soapaction: "urn:schemas-wiimu-com:service:PlayQueue:1#BrowseQueue"';
   $soapact['PlayQ']    = 'Soapaction: "urn:schemas-wiimu-com:service:PlayQueue:1#PlayQueueWithIndex"';
   $soapact['GetKeys']  = 'Soapaction: "urn:schemas-wiimu-com:service:PlayQueue:1#GetKeyMapping"';
   $soapact['GetVol']   = 'Soapaction: "urn:schemas-upnp-org:service:RenderingControl:1#GetVolume"';
   $soapxmlbeg          = '<?xml version="1.0"?> <s:Envelope s:encodingStyle="http://schemas.xmlsoap.org/soap/encoding/" xmlns:s="http://schemas.xmlsoap.org/soap/envelope/"> <s:Body> ';
   $soapxmlend          = '</s:Body></s:Envelope>';
   $soapxml['CreateQ']  = $soapxmlbeg.'<u:CreateQueue xmlns:u="urn:schemas-wiimu-com:service:PlayQueue:1"> <QueueContext>&lt;?xml version=&quot;1.0&quot;?&gt; &lt;PlayList&gt; &lt;ListName&gt;&&1&lt;/ListName&gt; &lt;ListInfo&gt; &lt;SourceName&gt;&&2&lt;/SourceName&gt; &lt;TrackNumber&gt;&&3&lt;/TrackNumber&gt; &lt;Radio&gt;&&4&lt;/Radio&gt; &lt;Quality&gt;3&lt;/Quality&gt; &lt;/ListInfo&gt;&lt;Tracks&gt;';
   $soapxml['Track']    = '&lt;Track&&1&gt; &lt;URL&gt;&&2&lt;/URL&gt; &lt;Metadata&gt;&amp;lt;DIDL-Lite xmlns:dc=&amp;quot;http://purl.org/dc/elements/1.1/&amp;quot; xmlns:upnp=&amp;quot;urn:schemas-upnp-org:metadata-1-0/upnp/&amp;quot; xmlns=&amp;quot;urn:schemas-upnp-org:metadata-1-0/DIDL-Lite/&amp;quot;&amp;gt; &amp;lt;item&amp;gt; &amp;lt;dc:title&amp;gt;&&3&amp;lt;/dc:title&amp;gt; &amp;lt;upnp:artist&amp;gt;&&4&amp;lt;/upnp:artist&amp;gt; &amp;lt;upnp:albumArtURI&amp;gt;&&5&amp;lt;/upnp:albumArtURI&amp;gt; &amp;lt;/item&amp;gt;&amp;lt;/DIDL-Lite&amp;gt;&lt;/Metadata&gt;&lt;Source&gt;&&6&lt;/Source&gt;&lt;/Track&&1&gt;';
   $soapxml['CreateQe'] = '&lt;/Tracks&gt;&lt;/PlayList&gt;</QueueContext></u:CreateQueue>'.$soapxmlend;
   $soapxml['PlayQ']    = $soapxmlbeg.'<u:PlayQueueWithIndex xmlns:u="urn:schemas-wiimu-com:service:PlayQueue:1"> <QueueName>&&1</QueueName><Index>1</Index> </u:PlayQueueWithIndex>'.$soapxmlend;
   $soapxml['BrowseQ']  = $soapxmlbeg.'<u:BrowseQueue xmlns:u="urn:schemas-wiimu-com:service:PlayQueue:1"> <QueueName>CurrentQueue</QueueName> </u:BrowseQueue>'.$soapxmlend;
   $soapxml['GetKeys']  = $soapxmlbeg.'<u:GetKeyMapping xmlns:u="urn:schemas-wiimu-com:service:PlayQueue:1"/>'.$soapxmlend;
   $soapxml['GetVol']   = $soapxmlbeg.'<u:GetVolume xmlns:u="urn:schemas-upnp-org:service:RenderingControl:1"> <InstanceID>0</InstanceID><Channel>Master</Channel> </u:GetVolume>'.$soapxmlend;

   $src = array(
        "play",
        "pause",
        "resume",
        "prev",
        "next",
        "vol",
        "mode0",
        "mode1",
        "mode2",
        "mode3",
        "presets",
        "save",
        "info",
        "GetVol",
        "device");
   $cmd = array(
        ":59152/upnp/control/PlayQueue1,CreateQ,PlayQ",
        "/httpapi.asp?command=setPlayerCmd:pause",
        "/httpapi.asp?command=setPlayerCmd:resume",
        "/httpapi.asp?command=setPlayerCmd:prev",
        "/httpapi.asp?command=setPlayerCmd:next",
        "/httpapi.asp?command=setPlayerCmd:vol",
        "/httpapi.asp?command=setPlayerCmd:loopmode:0",
        "/httpapi.asp?command=setPlayerCmd:loopmode:1",
        "/httpapi.asp?command=setPlayerCmd:loopmode:-1",
        "/httpapi.asp?command=setPlayerCmd:loopmode:2",
        ":59152/upnp/control/PlayQueue1,GetKeys",
        ":59152/upnp/control/PlayQueue1,BrowseQ",
        ":59152/upnp/control/PlayQueue1,BrowseQ",
        ":59152/upnp/control/rendercontrol1,GetVol",
        "/httpapi.asp?command=getStatus");

    if(substr($btn, 0, 3) == "vol" && substr($btn, 3, 1) != ":") $btn = substr($btn, 0, 3).':'.substr($btn, 3);
    $cmds   = explode(',', str_replace($src, $cmd, $btn));
    $url    = "http://".$host.$cmds[0];
    $action = $cmds[1];

//  exit(":url:".$url.":act:".$action.":data:".$data);  

    if ($btn == "play") {
//   split sources by pipe in case of a playlist
//   first entry contains <Listname>
     $dtab = explode('|', $data);
     $dcnt = count($dtab);
//   track number = 1 OR (number of items - playlist header)
     if ($dcnt == 1) {
      $trkn = 1;
     } else {
      $trkn = $dcnt - 1;
     }

//   create soap xml structure from sources  
//   get arguments of each source separated by comma
     for($i = 0; $i < $dcnt; $i++) {
       $darg = explode(',', $dtab[$i]);
       if ($i == 0) {
         $queue = $darg[4];
         $data = $soapxml[$cmds[1]];
         $data = str_replace("&&1", $queue,   $data); //Listname
         $data = str_replace("&&2", $darg[0], $data); //Sourcename
         $data = str_replace("&&3", $trkn,    $data); //Tracknumber
//       <Radio>:1 single   -> can replay after power-off, cannot scroll tracks
//       <Radio>:0 playlist -> cannot replay after power-off, can scroll tracks
         if ($dcnt == 1) {
         $data = str_replace("&&4", "1",      $data); //Radio
         } else {
         $data = str_replace("&&4", "0",      $data); //Radio
         }
         $trkn = 1;
       }
       if ($i > 0 OR $dcnt == 1) {
        $data .= $soapxml['Track'];
        $data = str_replace("&&1", $trkn++,  $data); //Track
        $data = str_replace("&&2", $darg[1], $data); //URL
        $data = str_replace("&&3", $darg[4], $data); //Title
//      <upnp:artist>: artist/album or track source  
        if ($darg[2] == "") {
        $data = str_replace("&&4", $darg[0], $data); //Artist
        } else {
        $data = str_replace("&&4", $darg[2], $data); //Artist
        }
        $data = str_replace("&&5", $darg[3], $data); //PicURL
        $data = str_replace("&&6", $darg[0], $data); //Source
       }
     }
//   add a PlayQueue Command after CreateQueue
     $data   .= $soapxml['CreateQe']."|".str_replace("&&1", $queue, $soapxml['PlayQ']);
     $action .= '|'.$cmds[2];
   } else {
    if($action != '') $data = $soapxml[$cmds[1]];
   }

   $ch = curl_init(); 
   curl_setopt($ch, CURLOPT_VERBOSE, 1);
   curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
   curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 1); 
   curl_setopt($ch, CURLOPT_URL, $url);

// run multiple curls using soap, requires http header
   if ($action != '') {
     $dtab = explode('|', $data);
     $darg = explode('|', $action);

//   exit(":url:".$url.":data:".$dtab[0].":arg:".$darg[0]);  

     for($i = 0; $i < count($dtab); $i++) {
       $headers = array($soaptype, $soapact[$darg[$i]]);
       curl_setopt($ch, CURLOPT_HTTPHEADER, $headers); 
       curl_setopt($ch, CURLOPT_POST, true);
       curl_setopt($ch, CURLOPT_POSTFIELDS, $dtab[$i]); 
//     exit(":url:".$url.":head:".$headers[1].":data:".$dtab[$i]);  
//     $output contains the response
       $output  = curl_exec($ch);
     }
   } else { 
// or run single curl using httpapi
//   exit(":url:".$url);  
//   $output contains the response 
     $output = curl_exec($ch); 
   }

// close curl resource to free up system resources 
   curl_close($ch); 

// output must be echoed due to parse() below 
   echo $output.'|';                //output unformatted info

// produce scalable output replacing separating kommas by blank from xoro within {...} 
// and convert certain hex values to ascii
   $outp = $output;
   if (substr($output, 0, 1) == "<") {
     $outp = str_replace(array('&amp;','&lt;','&gt;','&quot;'),array('&','<','>','"'),$outp);
     parse($outp,$keys_vals);       //output formatted info
     if($keys_vals){
      echo '|';
      $optn = '<option value="&&1,&&2,&&3,&&4">&&5</option>'; $x = array('','','','','');
      foreach($keys_vals as $k){
       echo "$k<br>";               //output short formatted info
       $s = 'S:ENVELOPE.S:BODY.U:BROWSEQUEUERESPONSE.QUEUECONTEXT.PLAYLIST.TRACKS.TRACK1.SOURCE=';       $l = strlen($s);  
       if($s == substr($k, 0, $l)) $x[0] = substr($k, $l); else {
       $s = 'S:ENVELOPE.S:BODY.U:BROWSEQUEUERESPONSE.QUEUECONTEXT.PLAYLIST.TRACKS.TRACK1.URL=';     $l = strlen($s);  
       if($s == substr($k, 0, $l)) $x[1] = substr($k, $l); else {
       $s = 'S:ENVELOPE.S:BODY.U:BROWSEQUEUERESPONSE.QUEUECONTEXT.PLAYLIST.TRACKS.TRACK1.METADATA.DIDL-LITE.ITEM.UPNP:ARTIST=';         $l = strlen($s);  
       if($s == substr($k, 0, $l)) $x[2] = substr($k, $l); else {
       $s = 'S:ENVELOPE.S:BODY.U:BROWSEQUEUERESPONSE.QUEUECONTEXT.PLAYLIST.TRACKS.TRACK1.METADATA.DIDL-LITE.ITEM.UPNP:ALBUMARTURI=';$l = strlen($s);  
       if($s == substr($k, 0, $l)) $x[3] = substr($k, $l); else {
       $s = 'S:ENVELOPE.S:BODY.U:BROWSEQUEUERESPONSE.QUEUECONTEXT.PLAYLIST.TRACKS.TRACK1.METADATA.DIDL-LITE.ITEM.DC:TITLE=';     $l = strlen($s);  
       if($s == substr($k, 0, $l)) $x[4] = substr($k, $l); }}}}
      }
      $optn = str_replace(array('&&1','&&2','&&3','&&4','&&5'), $x, $optn);
      echo '|'.$optn;
    
     if($btn == 'save') file_put_contents($file, $optn.PHP_EOL, FILE_APPEND | LOCK_EX); 

     }
   }
// device info (non-xml format)
   if (substr($output, 0, 1) == "{") {
     $outp = str_replace(array('{ "','" }','": "','", "'),array('','',':','<br>'),$outp);
     echo $outp;
   }

?>

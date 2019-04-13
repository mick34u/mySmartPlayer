<?php

$e_last = ''; $e_lastval = ''; $keys_vals = [];

// at each element(tag) with all its attributes
function start($parser,$e_name,$e_attr) {
  $o = $e_name.':';
  global $e_last, $keys_vals;
  if($e_last != '') $e_last .= '.'.$e_name; else $e_last = $e_name;
  foreach($e_attr as $a => $b) {
    $o .= $a."=".$b."<br>";
    if($b > ' ') $keys_vals[] = $e_last.'.'.$a.'='.$b;
  }
  echo $o;//start-tag string including attributes
}
// at end of each element(tag)
function stop($parser,$e_name) {
  $o = ':'.$e_name."<br>";
  global $e_last, $keys_vals;
  $e_last = rtrim(rtrim($e_last,$e_name),'.');
  echo $o;//stop-tag string (new-line)
}
// at each value (part) of an element(tag)
// could run multiple times for same element!
function char($parser,$e_val) {
  $o = $e_val; $o = str_replace("<br>", "", $o);
  global $e_last, $keys_vals;
  if($o > ' ') {
    if(substr($keys_vals[count($keys_vals)-1], 0, strlen($e_last.'=')) == $e_last.'=')
      $keys_vals[count($keys_vals)-1] .= $o; 
    else
      $keys_vals[] = $e_last.'='.$o;
  }
  echo $o;//text between start-/stop-tag
}
// format $inp(string) plus corr.array of keys and values
function parse($inp,$out) {
  $parser = xml_parser_create();
  xml_set_element_handler($parser,"start","stop");
  xml_set_character_data_handler($parser,"char");
  
     $beg = strpos($inp,'<QueueContext>');
     $end = strpos($inp,'</QueueContext>');
     if(is_numeric($beg) && is_numeric($end)){
        xml_parse($parser, substr($inp,0,$beg + 14));
        xml_parse($parser, substr($inp,$beg + 35,$end - $beg));
        xml_parse($parser, substr($inp,$end));
     } else {
        xml_parse($parser, $inp);
     }

  xml_parser_free($parser);
  $out = $keys_vals;
}
?>



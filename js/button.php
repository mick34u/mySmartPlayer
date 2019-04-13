/*-----------------------------------------------------------------------------
   for each button this function generates the HTTP call via "curl"
   using the button value and selected options of its parent node name
-----------------------------------------------------------------------------*/
function b($btn,$url) {
    sel = document.activeElement;
    if(sel.id.indexOf('SelectBoxIt') > 1) sel = document.getElementById(sel.id.substr(0, sel.id.length - 11));
    $btn   = $btn || sel.value;
    if($btn == "") return;
    $url   = $url || "";
    $frm   = sel.parentNode.name; 
    $host  = document.getElementById("hostname_" + $frm).value;
    $hosts = $host.split(",");
    $host  = $hosts[1];
    $urls  = $url.split(",");
    sel    = document.getElementById("info_" + $frm);
    sel.innerHTML = sel.textContent = ' '; 

    /* if url starts with "http" then just load this web page */
    if($urls[0].substr(0,4) == 'http') {
        $url = $urls[0].replace('&&1',$host);
        window.open($url).location;
        return;
    }

    /* get tracks of a playlist from corresponding select options       */
    /* add all options (values and text) to $url each separated by pipe */
    if ($urls[1] == "select_playlists") { 
     sel = document.getElementById($urls[2]);
     for (var i = 0; i < sel.length; i++) {
      if (sel.options[i].value != "") { $url += '|' + sel.options[i].value + ',' + sel.options[i].text; }
     }
    }

    /*alert( "btn:" + $btn + " from node:" + $frm + " host:" + $host + " url:" + $url );*/
  
    /* call http interface of the selected device using cUrl for GET/POST commands */
    $curl = location.pathname+"/php/curl_"+$frm+".php";
    $.get( $curl, { myData: { source: $url, target: $host, command: $btn } }, function(data) { 
        /* after each post show the response in info tag */
        /* show output of http interface in raw or parsed format dep. on last host field */       
        $dtab = data.split('|');
        i = $dtab.length - 1; if(i > $info) i = $info;  
        /*alert($info+$dtab[0].substr(0,60)+$dtab[0].substr($dtab[0].length-60,60));*/
        /*if(i > 0) alert($dtab.length.toString()+i.toString()+$dtab[i]);*/
        sel = document.getElementById("info_" + $frm);
       if(i == 0 || i == 3) sel.textContent = $dtab[i]; else sel.innerHTML = $dtab[i];
    });    

}


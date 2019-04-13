/*-----------------------------------------------------------------------------
   this will reduce the selection to only active devices 
   - if hostname not available the select option is deleted 
   - if no select option left the whole parent form is hidden
   the quickest way was to retrieve list of active devices from the router
   and check for each hostname if it exists in the list 
-----------------------------------------------------------------------------*/
function hide() {

    jQuery.ajaxSetup({async:false});

    /* get all active clients in local network */
    $hosts_script = location.pathname+'/php/IPing.php'; 
    $.get($hosts_script, {command: '0'}, function(data) { $hosts = data.split('#'); });

    /* now remove all hosts from selection which do not exist in $hosts table */   
    $ftab = ['wiimu', 'bose', 'vu'];
    for (f=0; f<$ftab.length; f++) {
      $frm = $ftab[f];
      sel  = document.getElementById("hostname_"+$frm);
      loop(0);
      ip($frm);
      $('#hostname_'+$frm).data("selectBox-selectBoxIt").refresh(); 
    }

    jQuery.ajaxSetup({async:true});

    /* need this loop function to evaluate each response from post */
    function loop(i) {
     if (i >= sel.options.length) return; 
     $hostname   = sel.options[i].value.split(',')[0];
     $host       = sel.options[i].value.split(',')[1];

     /* get IP address from $hosts table by matching NAME OR IP of select options  */
     /* if no match continue with IP of current select option                      */
     for (j=0; j<$hosts.length; j++)
     { if($hosts[j].indexOf($hostname) !== -1 || 
          $hosts[j].indexOf($host)     !== -1) 
       { $host = $hosts[j].split(',')[1]; break; }
     }

     $btn        = 'device';
     $url        = "";
     /*alert( ":btn:" + $btn + ":node:" + $frm + ":host:" + $host + ":url:" + $url );*/
  
     /* call http interface of the selected device using cUrl module */
     $curl = location.pathname+"/php/curl_"+$frm+".php";
     $.get( $curl, { myData: { source: $url, target: $host, command: $btn }}, function(data) {
       /* if host is unavailable delete the option and if no hosts left hide the form */        
       /*alert( data.length.toString() + '@' + data + '@' );*/

       if(data.length < 5) {
         sel.remove(i); i -= 1;
         if(sel.options.length <= 0) sel.parentNode.hidden = true;
       } else {
         sel.options[i].value = $hostname + "," + $host;
         sel.selectedIndex = i;
       }

     });
     
     loop(i + 1);
    } 
}


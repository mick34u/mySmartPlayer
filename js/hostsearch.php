/*-----------------------------------------------------------------------------
   this is called on clicking search button for hostname 
   for the selected hostname this determines the actual IP of the target device
   by ping starting from input IP up to 10 next addresses 
-----------------------------------------------------------------------------*/
function h() {
    sel = document.activeElement; 
    if(sel.id.indexOf('SelectBoxIt') > 1) sel = document.getElementById(sel.id.substr(0, sel.id.length - 11)); 
    $frm        = sel.parentNode.name;
    sel         = document.getElementById("hostip_" + $frm);
    $host       = sel.value;
    sel         = document.getElementById("hostname_" + $frm);
    $hostname   = sel.value.split(',')[0];
    sel         = document.getElementById("info_" + $frm);
    sel.innerHTML = sel.textContent = ' '; 
    $url        = "";
    $btn        = 'device';

    jQuery.ajaxSetup({async:false});

    /* get all active clients in local network */
    $hosts_script = location.pathname+'/php/IPing.php'; 
    $.get($hosts_script, {command: '0'}, function(data) { $hosts = data.split('#'); });

    /* get IP address from $hosts table by matching NAME of selected option */
    /* if found continue with matching single IP else search within range   */
    for (j=0; j<$hosts.length; j++)
    { if($hosts[j].indexOf($hostname) !== -1)
      { $host = $hosts[j].split(',')[1]; break; }
    }

    /* now post a "device" command to max.10 IPs starting from current */    
    $host0 = $host.substr(0, $host.lastIndexOf('.') + 1);
    $host1 = $host.substr($host.lastIndexOf('.') + 1);
    loop(0);

    jQuery.ajaxSetup({async:true});

    /* need this loop function to stop after first successful post */
    function loop(i) {
     $host      = $host0 + (parseInt($host1) + i).toString();
     sel        = document.getElementById("hostip_" + $frm);
     sel.value  = $host;
     if (i > 9) return;

     /*alert(":btn:"+$btn+":node:"+$frm+":host:"+$hostname+$host+":url:"+$url);*/
  
     /* call http interface of the selected device using cUrl module */
     $curl = location.pathname+"/php/curl_"+$frm+".php";
     $.get( $curl, { myData: { source: $url, target: $host, command: $btn } }, function(data) {
       /* after each post check for selected hostname in http response */        
       /* in case of a match the hostname with new IP is added to select options */        
       /*alert( data.indexOf($hostname).toString() );*/

       if (data.indexOf($hostname) !== -1) {
         sel = document.getElementById("hostname_" + $frm );
         for (j=0; j<sel.options.length; j++)
         {  if (sel.options[j].value.indexOf($hostname) !== -1)
           {  sel.options[j].value = sel.value = $hostname+","+$host;
              sel.selectedIndex = j; }
         }
         $('#hostname_'+$frm).data("selectBox-selectBoxIt").refresh(); 
         /* update or add host and IP in $hosts table */
         for (j=0; j<$hosts.length; j++) 
         { if($hosts[j].indexOf($hostname) !== -1 || 
              $hosts[j].indexOf($host)     !== -1) 
           {  $hosts[j] = $hostname+","+$host; break; }
         }
         if(j>=$hosts.length) 
         { $hosts[$hosts.length] = $hostname+","+$host; } 
         /* store updated $hosts table */
         $s = '';
         for (j=0; j<$hosts.length; j++) 
         { if($s == '') $s = $hosts[j]; else $s += '|'+$hosts[j]; }

         /*alert($s);*/
         $.get($hosts_script, {command: '3', source: $s}, function(data) {});

         /* show output of http interface in raw or parsed format dep. on last host field */        
         $dtab = data.split('|');
         /* alert($dtab[0]); if($dtab.length > 1) alert($dtab[1]); */
         sel = document.getElementById("info_" + $frm);
         j = $dtab.length - 1; if(j > $info) j = $info; 
         if(j == 0 || j == 3) sel.textContent = $dtab[j]; else sel.innerHTML = $dtab[j];

       /* if hostname not in http response continue with next IP address */        
       } else {
         loop(i + 1);
       }
      });
   
    } 
}


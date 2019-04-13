/*-----------------------------------------------------------------------------
   determine active hosts using ping within IP range and store result 
-----------------------------------------------------------------------------*/
function ping() {
  $hosts_script = location.pathname+'/php/IPing.php'; 
  $.get($hosts_script, {command: '1'}, function(data) {
    sel = document.getElementById("info_extras");
    sel.textContent = data; });
}


/*----------------------------------------------------------------------------
   additional functions and settings: 
	i0-i3 = set format of info area
	help  = display Readme document
	init  = display only active devices
	ping  = ping within IP range and store result
-----------------------------------------------------------------------------*/
function extra() {
    sel = document.getElementById("info_extras");
    sel.innerHTML = sel.textContent = ' ';    
    sel = document.activeElement;
    if(sel.id.indexOf('SelectBoxIt') > 1) sel = document.getElementById(sel.id.substr(0, sel.id.length - 11)); 
	if(sel.value == 'i0')   $info = 0;  else
	if(sel.value == 'i1')   $info = 1;  else
	if(sel.value == 'i2')   $info = 2;  else
	if(sel.value == 'i3')   $info = 3;  else
	if(sel.value == 'hide') hide();     else
	if(sel.value == 'ping') ping();     else
	if(sel.value == 'help') window.open(location.pathname+"/html/Readme.html").location;
}


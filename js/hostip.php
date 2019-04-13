/*-----------------------------------------------------------------------------
   this will copy IP address from select option to input field
   on initial load and change of hostname
-----------------------------------------------------------------------------*/
function ip($frm){
    sel   = document.getElementById("hostname_" + $frm);
    document.getElementById("hostip_" + $frm).value = sel.value.split(',')[1];
//  clear corresponding info area
    sel = document.getElementById("info_" + $frm);
    sel.innerHTML = sel.textContent = ' ';    
}


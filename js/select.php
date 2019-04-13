/*-----------------------------------------------------------------------------
   for each selected source this function generates the HTTP call via "curl"
   using the selected value and simulating button "play"
-----------------------------------------------------------------------------*/
function s() {
    sel = document.activeElement; 
    if(sel.id.indexOf('SelectBoxIt') > 1) sel = document.getElementById(sel.id.substr(0, sel.id.length - 11)); 
    if(sel.value == "") return;
    $url    = sel.value + ',' + sel.options[sel.selectedIndex].text;
    $btn    = "play";
    b($btn,$url);
}


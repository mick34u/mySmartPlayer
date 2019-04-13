<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link type="text/css" rel="stylesheet" href="http://ajax.googleapis.com/ajax/libs/jquerymobile/1.4.5/jquery.mobile.min.css"/>

    <link type="text/css" rel="stylesheet" href="http://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css"/>
    <link type="text/css" rel="stylesheet" href="http://netdna.bootstrapcdn.com/twitter-bootstrap/2.2.2/css/bootstrap-combined.min.css"/>
    <link type="text/css" rel="stylesheet" href="http://gregfranko.com/jquery.selectBoxIt.js/css/jquery.selectBoxIt.css"/>    

    <script src="http://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="http://ajax.googleapis.com/ajax/libs/jquerymobile/1.4.5/jquery.mobile.min.js"></script>
    
    <script src="http://gregfranko.com/jquery.selectBoxIt.js/js/jquery.selectBoxIt.min.js"></script>

    <style>
       h3        {margin-top:0; margin-bottom:0;}
       head      {padding:10px; zoom:75%; line-height:36px;}
       body      {padding:10px; zoom:75%; line-height:36px;
                  font-family:'FontAwesome', 'sans-serif'; }
       button    {height:32px; width:32px;}
      .hidden    {display:none;}
      .buttons, .buttons .selectboxit, .buttons .selectboxit-options {width:64px;}
    </style>

    <h3>mySmartPlayer</h3>
    
    <form id="extras" name="extras">
	    <select id="sources_extras"    onchange="extra()">
          <?php include ("./html/select_sources_extras.php");  ?>
	    </select>
        <br>
        <div id="info_extras" style="line-height:20px"> </div>
    </form>

</head>
<body> 

    <form id="wiimu" name="wiimu">
        <select id="hostname_wiimu"    onchange="ip('wiimu')">
          <?php include("./html/select_hostnames_wiimu.php");  ?>
        </select>
        <input  type="text"   id="hostip_wiimu" 
                pattern="([0-9]{1,3}\.){3}[0-9]{1,3}" 
                style="height:32px; width:185px; margin:0;"/>
        <button type="button" value="search" onClick="h()"><i class="fa fa-search"></i></button>
        <br>
        <select id="sources_wiimu"     onchange="s()">
          <?php include ("./html/select_sources_wiimu.php");   ?>
        </select>
        <select id="sources__wiimu"    onchange="s()">
         <?php include ("./html/select_sources__wiimu.php");   ?>
        </select>
        <?php include ("./html/select_playlists_wiimu.php");   ?>
        <br>
        <?php include ("./html/select_buttons_wiimu.php");     ?>
        <br>
        <div id="info_wiimu" style="line-height:20px"> </div>
    </form>

    <form id="bose" name="bose">
        <select id="hostname_bose"    onchange="ip('bose')">
          <?php include ("./html/select_hostnames_bose.php");  ?>
        </select>
        <input  type="text"   id="hostip_bose"
                pattern="([0-9]{1,3}\.){3}[0-9]{1,3}" 
                style="height:32px; width:185px; margin:0;"/>
        <button type="button" value="search" onClick="h()"><i class="fa fa-search"></i></button>
        <br>
        <select id="sources_bose"     onchange="s()">
          <?php include ("./html/select_sources_bose.php");    ?>
        </select>
        <select id="sources__bose"    onchange="s()">
          <?php include ("./html/select_sources__bose.php");   ?>
        </select>
        <br>
        <?php include ("./html/select_buttons_bose.php");      ?>
        <br>
        <div id="info_bose" style="line-height:20px"> </div>
    </form>

    <form id="vu" name="vu">
        <select id="hostname_vu"      onchange="ip('vu')">
          <?php include ("./html/select_hostnames_vu.php");    ?>
        </select>
        <input  type="text"   id="hostip_vu"
                pattern="([0-9]{1,3}\.){3}[0-9]{1,3}" 
                style="height:32px; width:185px; margin:0;"/>
        <button type="button" value="search" onClick="h()"><i class="fa fa-search"></i></button>
        <br>
        <select id="sources_vu"       onchange="s()">
          <?php include ("./html/select_sources_vu.php");      ?>
        </select>
        <br>
        <?php include ("./html/select_buttons_vu.php");        ?>
        <br>
        <div id="info_vu" style="line-height:20px"> </div>
    </form>

    <form id="huawei" name="huawei">
        <select id="hostname_huawei"  onchange="ip('huawei')">
          <?php include ("./html/select_hostnames_huawei.php");?>
        </select>
        <input  type="text"   id="hostip_huawei"
                pattern="([0-9]{1,3}\.){3}[0-9]{1,3}" 
                style="height:32px; width:185px; margin:0;"/>
        <button type="button" value="search" onClick="h()"><i class="fa fa-search"></i></button>
        <br>
        <select id="sources_huawei"   onchange="s()">
          <?php include ("./html/select_sources_huawei.php");  ?>
        </select>
        <br>
        <div id="info_huawei" style="line-height:20px"> </div>
    </form>

  <script>

   var $info = 2; /* default=responses short/tabular form*/

/*-----------------------------------------------------------------------------
   overview of functions:
    s()     on selection of any source
    b()     on click at any button, also called from function s() 
    h()     on click at any search-for-hostname button
    ip()    on selection of any hostname, copy IP address to input field
    hosts() remove offline hosts from select options
    ping()  find active IP addresses using ping within certain range
    extra() on selection of any extra options   
-----------------------------------------------------------------------------*/
   <?php 
     include ("./js/select.php");
     include ("./js/button.php");
     include ("./js/hostsearch.php");
     include ("./js/hostip.php");
     include ("./js/hide.php");
     include ("./js/ping.php");
     include ("./js/extra.php");
   ?>
/*-----------------------------------------------------------------------------
   this is is executed once at end of page load (= initialization) 
-----------------------------------------------------------------------------*/
/*
    use selectBoxIt for better dropdown listbox (avoiding native mode on Android)
    selectBoxIt adds select and option tags on initial load 
*/
    $('select:visible').selectBoxIt({ 
        isMobile: function() { return false; }, 
        autoWidth: false,  copyClasses: "container"
    }); 
/*
    show IP adress in input fields
*/
    $frm = 'wiimu';
    ip($frm);
    $frm = 'bose';
    ip($frm);
    $frm = 'vu';
    ip($frm);
    $frm = 'huawei';
    ip($frm);

  </script>

</body>
</html>

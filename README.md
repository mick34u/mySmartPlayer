# mySmartPlayer - Control of WiFi audio devices 

- Control WiFi audio and other devices using different web interfaces
- does not replace the respective standard apps like MuzoPlayer or Soundtouch
  provides much easier and faster access to the most important functions
- is started as a web application with http-command in any web browser and is therefore independent of the operating system
  while the respective standard apps mostly exist only for Android and IOS and not for Windows or Linux


System Requirements:
-------------------------------------------------------

Server:
- Web Server with PHP support:
  - PHP any version from 5 with the extension CURL
  - apache or other web server, different solutions depending on the operating system  
    - on Synology under DSM, the integrated web server with PHP can be activated 
    - On Windows, the available InternetIntegrationServices feature ("IIS") can be enabled
      for the IIS the CGI option has to be activated
    - PHP is easily installed manually, tested with "php.net"
    - On Linux, the Apache server and PHP can be installed from the application package sources
    - Under Android any web server can be installed with PHP from the Store, tested with "Server for PHP"

Client:
- Any web browser should be able to run the app

Installation:
- the whole app folder is copied to the document path of the web server, making the app available anywhere on the local network
- with "http: <ip:port>/phpinfo.php" the installation can be verified (module cUrl activated?)


Benefits of my app compared to manufacturer's standard apps:
--------------------------------------------------------

- "one-app-for-many": on a single screen several devices are controlled in a uniform way
  This saves a lot of time and typing and there is no need to start the appropriate program for the device
- standard apps take a long time to find compatible speakers every time they start up
  There is an extremely long wait with the Bose Soundtouch app. A shame!
  my app is designed with no delay by use of static IP addresses or from previous sessions
  search on demand is possible and will serve to remember the IP address of a selected device      
- Available on all devices such as desktop, tablet, smartphone regardless of the operating system
- unrestricted and much faster access to selected radio stations and music tracks
- Enables much easier management of unified radio / music favorites across all devices
- centralized management of software and favorites allows instant availability on all clients
  With various standard apps favorites can not be copied from one device to multiple devices
  and are user-dependent and distributed among various services (Spotify, TuneIn, Amazon, etc.).  


WiFi Speaker control
-------------------------------------------------------

Supported products:
- HXS910 from XORO and numerous compatible devices from other manufacturers (Medion, AugustInt etc.)
  Control via standard web API of Linkplay-A11 wifi-audio module
  as well as via standard SOAP API of upnp.org and wiimu.com
- Soundtouch-xxx from Bose, 
  Control via standard web API from Bose

Operation of the app:
- Device selection via dropdown
- additional search for selected device within defined IP range
- Music selection via dropdown:
  - Internet radio station offered by services supported by the device,
    such as TuneIn, Spotify, Amazon, and others.
  - Private music collections from any UPnP/Dlna server like Synology
- control buttons:
  - play
  - pause
  - next
  - previous
  - volume
  - player modus (repeat,shuffle)
  - player info (current piece of music)
  - device info
  - preset stations info
  - save current piece of music as select option 


SAT Receiver Control 
-------------------------------------------------------

Supported products:
- VU-SOLO2 and VU-UNO4K from VUplus,
  Control via Enigma2 web interface

Operation:
- corresponds to buttons on the remote control: 
  - ok,exit,up,down,left,right,volume-up,volume-down,menu,epg-info,red,green,yellow,blue,ListofRecordings,EPG

Selection of special functions:
- start the Enigma2 web interface
- start the OSCAM web interface
- Standby
- Restart


Network router control
-------------------------------------------------------

Supported products:
- Huawei E5186
  Control via standard web API

Selection via dropdown list:
- Status, Network, Lan/Wlan Clients, SMS Messages, etc.
- Macro Function
- start the web interface
- start of Speedtest


Settings 
-------------------------------------------------------

Selection via dropdown list:
- the formatting for the info blocks can be set:
  - unformatted = original response from device
  - formatted = start/end tags with line breaks
  - structured = start tags with line breaks
  - as select option = ready to copy/paste and insert into documents containing the dropdown lists
- show only active devices and hide inactive, unreachable devices
- scan active devices within a defined IP range and save to hosts.txt


Quick start guide:
-------------------------------------------------------

- the file "select_hostnames_ [wiimu | bose | vu] .php" contains the hostnames and IP addresses of the devices;
  Check if they are pinged by the app (web server)
  Additional help is provided by the search function, which searches for the selected device from the selectable IP address. 

- the file "select_sources_ [wiimu | bose] .php" contains internet radio stations,
  Internet radio stations are generally valid and will be installed with my list.
  Links from music services like TuneIn, Spotify, Amazon can be entered here.
  Only TuneIn and Amazon were tested, but all services supported by the device should be possible. 

- the file "select_sources __ [wiimu | bose] .php" contains favorite tracks of a private music library via UPnP / Dlna server,
  this list is empty at initial installation and can be recreated afterwards.

- the file "select_playlists_ [wiimu | bose] .php" contains collections of songs or radio stations.
  If select_sources ... php is "select_playlists" instead of a URL,
    From "select_playlists ... php" the select options are started with a corresponding select ID as playlist.
  The playlist can be navigated using the forward / backward keys.
  This feature is only available for Xoro and is not supported by Bose.
  Bose automatically allows you to navigate between all the numbers of the corresponding music album when playing a song. 

- The following procedure is recommended for creating and managing the selection lists:
  - Play a song using the mobile speaker apps for Xoro (like Muzo Player, August Alink etc.) or Soundtouch for Bose.
  - Save the music using mySmartPlayer using the corresponding button (*).
    This adds a select-option line to the curl_ [wiimu | bose] _sources.txt file. 
  - copy "curl ... sources.txt" and paste into "select_sources ... php" with text editor.


<?php
   $un = 'admin'; 
   $pw = 'OGM2OTc2ZTViNTQxMDQxNWJkZTkwOGJkNGRlZTE1ZGZiMTY3YTljODczZmM0YmI4YTgxZjZmMmFiNDQ4YTkxOA=='; 
   $ping = 'for /L %i in (91,1,109) do ping -n 1 -w 1 192.168.1.%i'; 		//Windows
   $ping = 'for i in $(seq 91 109); do ping -c 1 -W 1 192.168.1.$i; done'; //Linux
   $from = ' von '; 	//server language de
   $from = ' from ';	//server language en
?>

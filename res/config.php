<?php

$server_ip = '127.0.0.1';
$out_port = 5950;
$in_port = rand(10000, 65530);
$timeout = 2;
$mapfile = "img/terkep_Gyozo_kivagott.png";
//$mapfile = "img/pontok.png";


//Adatbazis parameterek
$db_host = "localhost";
$db_user = "iparking";
$db_pass = "1p4rk1n6";
$db_name = "iparking";

//Kozelito algoritmus parameterek
$top = 400;
$bottom = 20;
$uprate = 0.2;
$downrate = 0.8;

//Teruleti atlagolas merete
$max_dist = 40;

//Regi adatok kizarasa pozicionalasnal
$timeout_sql = "";
//$timeout_sql = "AND time_rcv > (NOW() - INTERVAL 4 SECOND)";

?>
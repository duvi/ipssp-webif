<?php

require_once('res/config.php');

$command = "";
$command_in = "";
$param = "";
$message = "";
$info_message = "";
$posnum = "";
$mapname = "";
$stations = array();
$stations_checkbox = "";
$stations_select = "";
$stations_select_rec = "";
$positions = array();
$positions_select = "";
$monitors = array();
$sessions = "";
$maps = "";
$posname = "";
$mac = "";
$ip = "";
$pos_x = "";
$pos_y = "";

require_once('functions.php');

    if ($imagesize = getimagesize("$mapfile"))
	{
	$info_message .= "Map image loaded.\n x=" . $imagesize[0] . "px y=" . $imagesize[1] . "px\n";
	}
    else
	{
	$info_message .= "Map image not found!\n";
	}

    if ($_POST && isset($_POST['command']))
	{
	$command = $_POST['command'];
	if (isset($_POST['sta']))
	    {
	    $param = $_POST['sta'];
	    }
	if (isset($_POST['pos']))
	    {
	    $param = $_POST['pos'];
	    $posname = $_POST['pos'];
	    }
	if (isset($_POST['posnum']))
	    {
	    $param = $_POST['posnum'];
	    }
	if (isset($_POST['form_x']) && isset($_POST['form_y']))
	    {
	    $param = $_POST['form_x'] . "," . $_POST['form_y'];
	    }
	if (isset($_POST['mapname']))
	    {
	    $param = $_POST['mapname'];
	    }
	    if (isset($_POST['macname']))
		{
		$param .= " " . $_POST['macname'];
		$mac = $_POST['macname'];
		}
	}

    if ($command)
    {
	if ($socket = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP))
	{
	    $full_command = $command . " " . $param;
	    socket_bind($socket, $server_ip, $in_port);
	    socket_sendto($socket, $full_command, strlen($full_command), 0, $server_ip, $out_port);
	    socket_set_block($socket);
	    socket_set_option($socket,
                          SOL_SOCKET,
                          SO_RCVTIMEO,
                          array("sec"=>$timeout,"usec"=>0));

	    socket_recvfrom($socket, $message_in, 65535, 0, $clientIP, $clientPort);
	    socket_set_nonblock($socket);
	    socket_close($socket);

	    list($command_in, $param_in) = sscanf($message_in, "%s %s");
	    switch ($command_in)
		{
		case "done":
		    $message_file = fopen("position.log", "r");
		    $message .= fread($message_file, filesize("position.log"));
		    fclose($message_file);
		    break;
		case "message":
		    $message .= substr($message_in, strlen($command_in));
		    break;
		case "coords":
		    list($pos_x, $pos_y) = sscanf($param_in, "%i,%i");
		    $message_file = fopen("position.log", "r");
		    $message .= fread($message_file, filesize("position.log"));
		    fclose($message_file);
		    $message .= $pos_x . "," . $pos_y;
		    break;
		case "":
		    $info_message .= "Nothing received.\n Check if server is running!\n";
		}
	}
	else
	{
	    $info_message .= "Can't create socket\n";
	}
    }
    else
    {
    $info_message .= "No command run!\n";
    }

    $mac = get_mac();
    $ip = $_SERVER['REMOTE_ADDR'];
    $info_message .= "Your MAC: " . $mac . "\n";
    $info_message .= "Your IP: " . $ip . "\n";
    $posnum = get_num_pos();
    $mapname = get_map_name();
    $stations = get_stations();
    $stations_checkbox = print_stations_checkbox($stations);
    if (isset($_POST['sta']))
	{
	$stations_select = print_stations($stations, $_POST['sta'], 0);
	$stations_select_rec = print_stations($stations, $_POST['sta'], 1);
	}
    else
	{
	$stations_select = print_stations($stations, $mac, 0);
	$stations_select_rec = print_stations($stations, $mac, 1);
	}
    $positions = get_positions();
    $positions_select = print_positions($positions, $posname);
    $monitors = get_monitors();
    $sessions = get_session($mapname);
    $maps = get_maps();

?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <title>iPark Positioning System</title>
        <link type="text/css" rel="stylesheet" href="res/style.css" />

        <script src="res/jquery.js"></script>
        <script type="text/javascript" src="res/tabber_cookie.js"></script>
        <script type="text/javascript" src="res/tabber.js"></script>
        <script type="text/javascript" src="res/functions.js"></script>

        <script>
          var src_x;
          var src_y;
          var dest_x;
          var dest_y;
        </script>

    </head>
<style>
<!--
.dragme{position:relative;}
-->
</style>
<script language="JavaScript1.2">
<!--

var ie=document.all;
var nn6=document.getElementById&&!document.all;

var isdrag=false;
var x,y;
var dobj;

function movemouse(e)
{
  if (isdrag)
  {
    dobj.style.left = nn6 ? tx + e.clientX - x : tx + event.clientX - x;
    dobj.style.top  = nn6 ? ty + e.clientY - y : ty + event.clientY - y;
    return false;
  }
}

function selectmouse(e) 
{
  var fobj       = nn6 ? e.target : event.srcElement;
  var topelement = nn6 ? "HTML" : "BODY";

  while (fobj.tagName != topelement && (fobj.className.indexOf("dragme") == -1))
  {
    fobj = nn6 ? fobj.parentNode : fobj.parentElement;
  }

  if (fobj.className.indexOf("dragme") != -1)
  {
    isdrag = true;
    dobj = fobj;
    tx = parseInt(dobj.style.left+0);
    ty = parseInt(dobj.style.top+0);
    x = nn6 ? e.clientX : event.clientX;
    y = nn6 ? e.clientY : event.clientY;
    document.onmousemove=movemouse;
    return false;
  }
}

document.onmousedown=selectmouse;
document.onmouseup=new Function("isdrag=false");

//-->
</script>

    <body>

    <div id="container">
    <div id="header">iPark Positioning System
    </div>
    <div id="maincontent">
    <div class="tabber">
<?php
if (file_exists("recording"))
    {
    get_rec_coord();
    $info_message .= "Recording!\n";
    echo ('
    	<div class="tabbertab">
	    <h2>Record</h2>
	    <div class="left">
		<div>
		    <form target="_parent" method="post" >
			<input type="hidden" name="command" value="rec_single 0" />
			<input type="submit" value="Record Off" /> </br>
		    </form>
		</div>
	    ');
    print_info($info_message);
    echo ('
	    </div>
	    <div class="main">
		<div style="background-image:url(' . $mapfile . '); width:' . $imagesize[0] . 'px; height:' . $imagesize[1] . 'px;" class="dragme">
		<img src="img/rotpunkt.png" id="blink_img" onLoad="blink()" style="position:relative;z-index:2;left:' . ($pos_x-5) . 'px;top:' . ($pos_y-5) . 'px;">
		</div>
		<div id=record_message class="info">'
		. nl2br($message) . '
		</div>
	    </div>
	</div>
    ');
    }
else
    {
    echo ('
	<div class="tabbertab">
	    <h2>Info</h2>
	    <div class="left">
		<div>
		    <form target="_parent" method="post">
			Show all station info: 
			<input type="button" onClick="document.getElementById(\'info_message\').innerHTML = \'Loading...\'; show_all();" value="Show"/> </br>
			Show all position info: 
			<input type="button" onClick="document.getElementById(\'info_message\').innerHTML = \'Loading...\'; list_pos();" value="Show"/> </br>
		    </form>
		</div>
    ');
    if ($stations_select)
	{
	echo ('
		<div>
		Show station info:</br>
		    <form name="info_form" target="_parent" method="post">
	' . $stations_select . '
			<input type="button" onClick="document.getElementById(\'info_message\').innerHTML = \'Loading...\'; show_sta();" value="Show"/>
		    </form>
		</div>
	');
	}
    print_info($info_message);
    echo ('
	    </div>
	    <div class="main">
		<div id=info_message class="info">
		</div>
	    </div>
	</div>
	<div class="tabbertab">
	    <h2>Mon</h2>
	    <div class="left">
		<div>
		    <form name=monitor>
    ');
	foreach ($monitors as $monitor)
	    {
	    echo ('<input type="button" onClick="document.getElementById(\'mon_message\').innerHTML = \'Loading...\'; show_mon(\'' . $monitor["mac"] . '\'); document.getElementById(\'mon_message\').innerHTML = \'\';" value="' . $monitor["ip"] . '">');
	    }
    echo ('
		    </form>
		</div>
    ');
	print_info($info_message);
    echo ('
	    </div>
	    <div class="main">
		<div id="monitors" style="background-image:url(' . $mapfile . '); width:' . $imagesize[0] . 'px; height:' . $imagesize[1] . 'px;" class=" dragme">
    ');
	foreach ($monitors as $monitor)
	    {
	    echo ('<img src="img/rotpunkt.png" id="' . $monitor["mac"] . '" title="' . $monitor["ip"] . '" onClick="document.getElementById(\'mon_message\').innerHTML = \'Loading...\'; show_mon(\'' . $monitor["mac"] . '\'); document.getElementById(\'mon_message\').innerHTML = \'\';" style="position:absolute;float:none;z-index:3;opacity:0.4;cursor:pointer;left:' . ($monitor["x"]-5) . 'px;top:' . ($monitor["y"]-5) . 'px;"> ');
	    }
    echo ('
		    <div id="mon_punkt"> </div>
		</div>
		<div id=mon_message class="info">
		</div>
	    </div>
	</div>
	<div class="tabbertab">
	    <h2>Pos</h2>
	    <div class="left">
		<div>
		    <form target="_parent" method="post">
			Load positions<br/>
			<input type="hidden" name="command" value="load_sql" />
			Session: 
			<select name="mapname" onChange="document.getElementById(\'load_pos_ok\').style.visibility = \'visible\'; get_folders();" id="map_select">
	' . $sessions . '
			</select>
			</br>
			Station:
			<select name="macname" id="map_select2">
			</select>
			</br>
			<input type="submit" value="OK" id="load_pos_ok" style="visibility:hidden;" /> </br>
			</br>
		    </form>
		</div>
<!--		<div>
		    <form target="_parent" method="post">
			Clear positions
			<input type="hidden" name="command" value="clear_map" />
			<input type="submit" value="OK" /> </br>
		    </form>
		    <form target="_parent" method="post">
			<select name="mapname" >
	' . $maps . '
			</select>
			<input type="submit" name="command" value="load_map" /> </br>
		    </form>
		    <form target="_parent" method="post">
			<input type="text" name="mapname" /> </br>
			<input type="submit" name="command" value="save_map" /> </br>
		    </form>
		</div>
-->	');
    if ($positions_select)
	{
	echo ('
		<div>
		    <form name="pos_form" target="_parent" method="post">
			<input type="text" name="pos" size="4" />
			</br>
<!--			<input type="submit" name="command" value="del_pos" /> </br>
-->		    </form>
		</div>
	');
	}
	print_info($info_message);
    echo ('
	    </div>
	    <div class="main" >
		<div style="background-image:url(' . $mapfile . '); width:' . $imagesize[0] . 'px; height:' . $imagesize[1] . 'px;" class="dragme">
    ');
	foreach ($positions as $position)
	    {
	    echo ('<img src="img/rotpunkt.png" title="' . $position["name"] . '" onClick="document.getElementById(\'pos_message\').innerHTML = \'Loading...\'; show_pos(' . $position["name"] . ');" style="position:absolute;float:none;z-index:2;opacity:0.8;cursor:pointer;left:' . ($position["x"]-5) . 'px;top:' . ($position["y"]-5) . 'px;"> ');
	    }
    echo ('
		</div>
		<div id=pos_message class="info"> '
		. (nl2br($message)) . '
		</div>
    ');
    }
    echo ('
	    </div>
	</div>
	<div class="tabbertab">
	    <h2>Park</h2>
	    <div class="left">
		<div>
		Click on a parking place to change its status!<br/><br/>
		</div>
    ');
	print_info($info_message);
    echo ('
	    </div>
	    <div class="main">
		<div id="park_map" style="background-image:url(' . $mapfile . '); width:' . $imagesize[0] . 'px; height:' . $imagesize[1] . 'px;" class="dragme">
		</div>
		<div id=park_message class="info">
		</div>
	    </div>
	</div>
	<div class="tabbertab">
	    <h2>Record</h2>
	    <div class="left">
		This module is not working at the moment!<br/><br/>
		<div>
		    Select recording stations<br/>
		    0: recorded<br/>
		    1: not recorded<br/><br/>
		    <form target="_parent" method="post">
    ');
    echo $stations_select_rec;
    echo ('
			</br>
			<input type="submit" name="command" value="record_sta" /> </br>
		    </form>
		    <form target="_parent" method="post">
			<input type="hidden" name="command" value="set_num_pos" />
			<input type="text" name="posnum" value="' . $posnum . '" />
			<input type="submit" value="Set next position" /> </br>
		    </form>
		    <form target="_parent" method="post">
			<input type="hidden" name="command" value="set_session" />
			<input type="text" name="mapname" value="' . $mapname . '"/>
			<input type="submit" value="Set record session folder" /> </br>
		    </form>
		    <form name="pointform" target="_parent" method="post">
			<input type="hidden" name="command" value="rec_single 1" />
			x = <input type="text" name="form_x" size="4" />
			y = <input type="text" name="form_y" size="4" />
			<input type="submit" id="rec_button" style="visibility:hidden;" value="Record" /> </br>
			<p id="rec_text" style="visibility:visible;" >Set your position on the map to record!</p>
		    </form>
		</div>
    ');
	print_info($info_message);
    echo ('
	    </div>
	    <div class="main">
		<div id="record_map_div" onclick="point_it(event)" style="background-image:url(' . $mapfile . '); width:' . $imagesize[0] . 'px; height:' . $imagesize[1] . 'px;" class="dragme">
		<img src="img/rotpunkt.png" id="cross" style="position:relative;visibility:hidden;z-index:2;">
		</div>
		<div id=record_message class="info">
		</div>
	    </div>
	</div>
	<div class="tabbertab">
	    <h2>Locate</h2>
	    <div class="left">
		<div>
		    <form name="locate_form" target="_parent" method="post">
	');
    echo $stations_checkbox;
    echo ('
			</br>
			<label><input type="radio" name="command" value="compare" />compare</label></br>
			<label><input type="radio" name="command" value="comp_diff" CHECKED/>comp_diff</label></br>
			<label><input type="radio" name="command" value="comp_norm" />comp_norm</label></br>
			<label><input type="radio" name="command" value="comp_dist" />comp_dist</label></br>
			<label><input type="radio" name="command" value="comp_felezo" />comp_felezo</label></br>
			<label><input type="radio" name="command" value="comp_knn" />comp_knn</label></br>
			</br>
			<input type="button" id="locate_start" value="start" onClick="document.getElementById(\'locate_start\').style.visibility = \'hidden\'; document.getElementById(\'locate_stop\').style.visibility = \'visible\'; locate();">
			<input type="button" id="locate_stop" value="stop" onClick="document.getElementById(\'locate_start\').style.visibility = \'visible\'; document.getElementById(\'locate_stop\').style.visibility = \'hidden\'; clearCanvas(\'locate_canvas\'); clearTimeout(locate_timer); " style="visibility:hidden;" >
		    </form>
		</div>
    ');
	print_info($info_message);
    echo ('
	    </div>
	    <div class="main">
		<div id="locate_map" style="background-image:url(' . $mapfile . '); width:' . $imagesize[0] . 'px; height:' . $imagesize[1] . 'px;" class="dragme">
		    <canvas id="locate_canvas" width="' . $imagesize[0] . '" height="' . $imagesize[1] . '" style="position: absolute;"></canvas>
		    <div id="locate_punkt"> </div>
		</div>
		<div id=locate_message class="info">
		</div>
	    </div>
	</div>
	<div class="tabbertab">
	    <h2>Navigate</h2>
	    <div class="left">
		<div>
		    <form name="compare_form" target="_parent" method="post">
	');
    echo $stations_select;
    echo ('
			</br>
			<label><input type="radio" name="command" value="compare" />compare</label></br>
			<label><input type="radio" name="command" value="comp_diff" CHECKED/>comp_diff</label></br>
			<label><input type="radio" name="command" value="comp_norm" />comp_norm</label></br>
			<label><input type="radio" name="command" value="comp_dist" />comp_dist</label></br>
			<label><input type="radio" name="command" value="comp_felezo" />comp_felezo</label></br>
			<label><input type="radio" name="command" value="comp_knn" />comp_knn</label></br>
			</br>
			<input type="button" id="navigate_start" value="start" onClick="document.getElementById(\'navigate_start\').style.visibility = \'hidden\'; document.getElementById(\'navigate_stop\').style.visibility = \'visible\'; compare();">
			<input type="button" id="navigate_stop" value="stop" onClick="document.getElementById(\'navigate_start\').style.visibility = \'visible\'; document.getElementById(\'navigate_stop\').style.visibility = \'hidden\'; clearCanvas(\'navigate_canvas\'); clearTimeout(compare_timer);" style="visibility:hidden;" >
		    </form>
		    <form name="navigate_form" target="_parent" method="post">
			<p id="navigate_text" style="visibility:visible;" >Set your destination on the map!</p>
		    </form>
		</div>
    ');
	print_info($info_message);
    echo ('
	    </div>
	    <div class="main">
		<div id="navigate_map" style="background-image:url(' . $mapfile . '); width:' . $imagesize[0] . 'px; height:' . $imagesize[1] . 'px;" class="dragme">
		    <canvas id="navigate_canvas" width="' . $imagesize[0] . '" height="' . $imagesize[1] . '" style="position: absolute;"></canvas>
		    <img src="img/rotpunkt.png" id="compare_punkt" style="position:absolute;z-index:2;left:0px;top:0px;visibility:hidden;">
		    <img src="img/rotpunkt.png" id="navigate_punkt" style="position:absolute;visibility:hidden;z-index:2;">
		    <div id="nav_punkt"> </div>
		</div>
		<script>
		    drawpark();
		</script>
		<div id=compare_message class="info">
		</div>
	    </div>
	</div>
	');
?>
    </div>
    </div>
    </div>
    </body>
</html>


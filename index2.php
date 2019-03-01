<?php

require_once('res/config.php');

$message = "";
$info_message = "";
$mac = "";
$ip = "";

require_once('functions.php');

if ($imagesize = getimagesize("$mapfile")) {
    $info_message .= "Map image loaded.\n x=" . $imagesize[0] . "px y=" . $imagesize[1] . "px\n";
}
else {
    $info_message .= "Map image not found!\n";
}


$mac = get_mac($ssh_host,$ssh_port,$ssh_user,$ssh_pass,$ssh_comm);
$ip = $_SERVER['REMOTE_ADDR'];

?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <title>Indoor Positioning System</title>
        <link type="text/css" rel="stylesheet" href="res/style.css" />

        <script src="res/jquery.js"></script>
        <script type="text/javascript" src="res/functions.js"></script>

    <script>
      var src_x;
      var src_y;
      var dest_x;
      var dest_y;
    </script>

    </head>
    <body >

    <div id="container">
    <div id="maincontent">
<?php
    echo ('
        <div id="navigate_map" style="background-image:url(' . $mapfile . '); width:' . $imagesize[0] . 'px; height:' . $imagesize[1] . 'px;">
        <canvas id="navigate_canvas" width="' . $imagesize[0] . '" height="' . $imagesize[1] . '"></canvas>
        <img src="img/rotpunkt.png" id="compare_punkt" style="position:absolute;z-index:2;left:0px;top:0px;visibility:hidden;">
        <img src="img/rotpunkt.png" id="navigate_punkt" style="position:absolute;visibility:hidden;z-index:2;">
            <div id="nav_punkt"> </div>
        </div>
        <script>
            //drawpark();
            compare2(\'comp_diff\',\'' . str_replace(":", "", $mac) . '\', src_x, src_y, limit);
        </script>
        <div id=compare_message> '
        . (nl2br($message)) . '
        </div>
    ');
?>
    </div>
    </div>
    </body>
</html>

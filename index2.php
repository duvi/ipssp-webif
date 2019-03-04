<?php

require_once('res/config.php');
require_once('functions.php');

$message = "";
$info_message = "";

$mac = get_mac($ssh_host,$ssh_port,$ssh_user,$ssh_pass,$ssh_comm);
$ip = $_SERVER['REMOTE_ADDR'];
$areas = get_areas();

if ($imagesize = getimagesize("$mapfile")) {
    $info_message .= "Map image loaded.\n x=" . $imagesize[0] . "px y=" . $imagesize[1] . "px\n";
}
else {
    $info_message .= "Map image not found!\n";
}

?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <title>Indoor Positioning System</title>
        <link type="text/css" rel="stylesheet" href="res/style.css" />

        <script src="https://code.jquery.com/jquery-1.12.4.min.js" integrity="sha256-ZosEbRLbNQzLpnKIkEdrPv7lOy9C27hHQ+Xp8a4MxAQ=" crossorigin="anonymous"></script>
        <script type="text/javascript" src="res/functions.js"></script>

        <script>
            var area_id;
        </script>
    </head>
    <body>
        <div id="navigate_map" class="map" style="background-image:url('<?php echo $mapfile; ?>'); width:<?php echo $imagesize[0]; ?>px; height:<?php echo $imagesize[1]; ?>px;">
            <canvas id="navigate_canvas" width="<?php echo $imagesize[0]; ?>" height="<?php echo $imagesize[1]; ?>" style="position: absolute;"></canvas>
            <svg id="locate_svg" width="<?php echo $imagesize[0]; ?>" height="<?php echo $imagesize[1]; ?>" style="position: absolute;">
<?php foreach ($areas as $area) : ?>
    <?php
    $points = "";
    foreach ($area["polygon"] as $poligons) {
        $points .= implode(",", $poligons) . " ";
    }
    echo '<polygon id="area-' . $area["id"] . '" class="area" points="' . $points . '" fill-opacity="0" />';
    ?>
<?php endforeach; ?>
            </svg>
            <img src="img/rotpunkt.png" id="compare_punkt" style="position:absolute;z-index:2;left:0px;top:0px;visibility:hidden;">
            <img src="img/rotpunkt.png" id="navigate_punkt" style="position:absolute;visibility:hidden;z-index:2;">
            <div id="nav_punkt"> </div>
        </div>
        <div id="compare_message" class="message">
            <?php echo nl2br($message); ?>
        </div>
        <script>
            locate2('comp_diff','<?php echo str_replace(":", "", $mac); ?>',area_id);
        </script>
    </body>
</html>

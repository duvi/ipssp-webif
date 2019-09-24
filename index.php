<?php

require_once('res/config.php');
require_once('functions.php');
require_once('models/areas.php');

define('_IPSSP', 1);

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

<!DOCTYPE html>
<html>
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <title>Indoor Positioning System</title>
        <link type="text/css" rel="stylesheet" href="res/app.css" />

        <script src="https://code.jquery.com/jquery-1.12.4.min.js" integrity="sha256-ZosEbRLbNQzLpnKIkEdrPv7lOy9C27hHQ+Xp8a4MxAQ=" crossorigin="anonymous"></script>
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
        <script type="text/javascript" src="res/app.js"></script>

        <script>
            var area_id;
        </script>
    </head>
    <body>
        <nav class="navbar d-none">
            <div class="nav nav-tabs" id="nav-tab" role="tablist">
                <a class="nav-item nav-link show active" id="nav-info-tab" data-toggle="tab" href="#nav-info" role="tab" aria-controls="nav-info" aria-selected="true">Info</a>
                <?php foreach ($areas as $area) : ?>
                <a class="nav-item nav-link" id="nav-area_<?php echo $area["id"]; ?>-tab" data-toggle="tab" href="#nav-area_<?php echo $area["id"]; ?>" role="tab" aria-controls="nav-area_<?php echo $area["id"]; ?>" aria-selected="false"><?php echo $area["name"]; ?></a>
                <?php endforeach; ?>
            </div>
        </nav>
        <div id="maincontent" class="container">
            <div class="tab-content" id="nav-tabContent">
                <div class="tab-pane fade show active" id="nav-info" role="tabpanel" aria-labelledby="nav-info-tab">
                    <div class="header">
                        <img src="img/muzeum.jpg" alt="" />
                        <h3>Üdvözli a pécsi Csontváry Múzeum!</h3>
                    </div>
                    <div class="area-content">
                        <p>A szoftver automatikusan meghatározza az Ön pozícióját az épületen belül, és ennek megfelelően további információval szolgál az éppen látottakról.</p>
                        <p>A kezdéshez kérjük katintson a START gombra!</p>
                        <button class="btn btn-primary" id="locate_start" onClick="show_area('comp_diff','<?php echo str_replace(":", "", $mac); ?>',area_id)">START</button>
                    </div>
                </div>
                <?php foreach ($areas as $area) : ?>
                <div class="tab-pane fade" id="nav-area_<?php echo $area["id"]; ?>" role="tabpanel" aria-labelledby="nav-area_<?php echo $area["id"]; ?>-tab">
                    <div class="header">
                        <img src="<?php echo $area["image"]; ?>" alt="<?php echo $area["header"]; ?>" />
                        <h3><?php echo $area["header"]; ?></h3>
                    </div>
                    <div class="area-content"><?php echo $area["content"]; ?></div>
                    <div class="area-title"><?php echo $area["name"]; ?></div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </body>
</html>

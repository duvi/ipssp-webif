<?php

require_once('res/config.php');

define('_IPSSP', 1);

$command = "";
$tab = "";
$command_in = "";
$param = "";
$message = "";
$info_message = "";
$posnum = "";
$mapname = "";
$stations = array();
$stations_select = "";
$stations_select_rec = "";
$positions = array();
$positions_select = "";
$monitors = array();
$areas = array();
$sessions = "";
$maps = "";
$posname = "";
$mac = "";
$ip = "";
$pos_x = "";
$pos_y = "";

require_once('functions.php');

if ($imagesize = getimagesize("$mapfile")) {
    $info_message .= "Map image loaded.\n x=" . $imagesize[0] . "px y=" . $imagesize[1] . "px\n";
}
else {
    $info_message .= "Map image not found!\n";
}

if ($_POST && isset($_POST['command'])) {
    $command = $_POST['command'];
    if (isset($_POST['sta'])) {
        $param = $_POST['sta'];
    }
    if (isset($_POST['pos'])) {
        $param = $_POST['pos'];
        $posname = $_POST['pos'];
    }
    if (isset($_POST['posnum'])) {
        $param = $_POST['posnum'];
    }
    if (isset($_POST['form_x']) && isset($_POST['form_y'])) {
        $param = $_POST['form_x'] . "," . $_POST['form_y'];
    }
    if (isset($_POST['mapname'])) {
        $param = $_POST['mapname'];
    }
    if (isset($_POST['macname'])) {
        $param .= " " . $_POST['macname'];
        $mac = $_POST['macname'];
    }
}

if ($_POST && isset($_POST['tab'])) {
    $tab = $_POST['tab'];
}

if ($command) {
    if ($socket = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP)) {
        $full_command = $command . " " . $param;
        socket_bind($socket, $server_ip, $in_port);
        socket_sendto($socket, $full_command, strlen($full_command), 0, $server_ip, $out_port);
        socket_set_block($socket);
        socket_set_option($socket, SOL_SOCKET, SO_RCVTIMEO, array("sec"=>$timeout,"usec"=>0));
        socket_recvfrom($socket, $message_in, 65535, 0, $clientIP, $clientPort);
        socket_set_nonblock($socket);
        socket_close($socket);

        list($command_in, $param_in) = sscanf($message_in, "%s %s");
        switch ($command_in) {
            case "done":
                $message_file = fopen($logfile, "r");
                $message .= fread($message_file, filesize($logfile));
                fclose($message_file);
                break;
            case "message":
                $message .= substr($message_in, strlen($command_in));
                break;
            case "coords":
                list($pos_x, $pos_y) = sscanf($param_in, "%i,%i");
                $message_file = fopen($logfile, "r");
                $message .= fread($message_file, filesize($logfile));
                fclose($message_file);
                $message .= $pos_x . "," . $pos_y;
                break;
            case "":
                $info_message .= "Nothing received.\n Check if server is running!\n";
        }
    }
    else {
        $info_message .= "Can't create socket\n";
    }
}
else {
    $info_message .= "No command run!\n";
}

$mac = get_mac();
$ip = $_SERVER['REMOTE_ADDR'];
$info_message .= "Your MAC: " . $mac . "\n";
$info_message .= "Your IP: " . $ip . "\n";
$posnum = get_num_pos();
$mapname = get_map_name();
$stations = get_stations();

if (isset($_POST['sta'])) {
    $stations_select = print_stations($stations, $_POST['sta'], 0);
    $stations_select_rec = print_stations($stations, $_POST['sta'], 1);
}
else {
    $stations_select = print_stations($stations, $mac, 0);
    $stations_select_rec = print_stations($stations, $mac, 1);
}

$positions = get_positions();
$positions_select = print_positions($positions, $posname);
$monitors = get_monitors();
$sessions = get_session($mapname);
$maps = get_maps();
$areas = get_areas();

?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <title>Indoor Positioning System</title>

        <script src="https://code.jquery.com/jquery-1.12.4.min.js" integrity="sha256-ZosEbRLbNQzLpnKIkEdrPv7lOy9C27hHQ+Xp8a4MxAQ=" crossorigin="anonymous"></script>
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>

        <link type="text/css" rel="stylesheet" href="res/style.css" />
        <script type="text/javascript" src="res/functions.js"></script>

        <script>
          var src_x;
          var src_y;
          var dest_x;
          var dest_y;
          $(document).ready(function(){
              get_folders();
              get_stations();
          });
        </script>

    </head>

    <body>

        <div class="container-fluid">
            <nav class="navbar fixed-top">
                <div class="nav nav-tabs" id="nav-tab" role="tablist">
                    <span class="navbar-brand mb-0 h1">Indoor Positioning System</span>
                    <?php if (!file_exists("recording")) : ?>
                    <a class="nav-item nav-link <?php echo (($tab == '') ? ' show active' : ''); ?>" id="nav-info-tab" data-toggle="tab" href="#nav-info" role="tab" aria-controls="nav-info" aria-selected="true">Info</a>
                    <a class="nav-item nav-link" id="nav-mon-tab" data-toggle="tab" href="#nav-mon" role="tab" aria-controls="nav-mon" aria-selected="false">Monitor</a>
                    <a class="nav-item nav-link <?php echo (($tab == 'pos') ? ' show active' : ''); ?>" id="nav-pos-tab" data-toggle="tab" href="#nav-pos" role="tab" aria-controls="nav-pos" aria-selected="false">Position</a>
                    <a class="nav-item nav-link disabled" id="nav-park-tab" data-toggle="tab" href="#nav-park" role="tab" aria-controls="nav-park" aria-selected="false">Park</a>
                    <a class="nav-item nav-link <?php echo (($tab == 'rec') ? ' show active' : ''); ?>" id="nav-rec-tab" data-toggle="tab" href="#nav-rec" role="tab" aria-controls="nav-rec" aria-selected="false">Record</a>
                    <a class="nav-item nav-link" id="nav-area-tab" data-toggle="tab" href="#nav-area" role="tab" aria-controls="nav-area" aria-selected="false">Areas</a>
                    <a class="nav-item nav-link" id="nav-loc-tab" data-toggle="tab" href="#nav-loc" role="tab" aria-controls="nav-loc" aria-selected="false">Locate</a>
                    <a class="nav-item nav-link" id="nav-nav-tab" data-toggle="tab" href="#nav-nav" role="tab" aria-controls="nav-nav" aria-selected="false">Navigate</a>
                    <?php endif; ?>
                </div>
            </nav>
            <div id="maincontent" class="row">
                <div class="tab-content col-sm-10" id="nav-tabContent">
                    <?php
                    if (file_exists("recording")) {
                        get_rec_coord();
                        $info_message .= "Recording!\n";
                        echo ('
                            <div class="tab-pane fade show active" id="nav-record" role="tabpanel" aria-labelledby="nav-record-tab">
                        ');
                        include_once('views/recording.php');
                        echo ('
                            </div>
                        ');
                    }
                    else {
                        echo ('
                            <div class="tab-pane fade' . (($tab == "") ? " show active" : "") . '" id="nav-info" role="tabpanel" aria-labelledby="nav-info-tab">
                        ');
                        include_once('views/info.php');
                        echo ('
                            </div>
                            <div class="tab-pane fade" id="nav-mon" role="tabpanel" aria-labelledby="nav-mon-tab">
                        ');
                        include_once('views/monitor.php');
                        echo ('
                            </div>
                            <div class="tab-pane fade' . (($tab == "pos") ? " show active" : "") . '" id="nav-pos" role="tabpanel" aria-labelledby="nav-pos-tab">
                        ');
                        include_once('views/position.php');
                        echo ('
                            </div>
                            <div class="tab-pane fade" id="nav-park" role="tabpanel" aria-labelledby="nav-park-tab">
                        ');
                        include_once('views/park.php');
                        echo ('
                            </div>
                            <div class="tab-pane fade' . (($tab == "rec") ? " show active" : "") . '" id="nav-rec" role="tabpanel" aria-labelledby="nav-rec-tab">
                        ');
                        include_once('views/record.php');
                        echo ('
                            </div>
                            <div class="tab-pane fade" id="nav-area" role="tabpanel" aria-labelledby="nav-area-tab">
                        ');
                        include_once('views/areas.php');
                        echo ('
                            </div>
                            <div class="tab-pane fade" id="nav-loc" role="tabpanel" aria-labelledby="nav-loc-tab">
                        ');
                        include_once('views/locate.php');
                        echo ('
                            </div>
                            <div class="tab-pane fade" id="nav-nav" role="tabpanel" aria-labelledby="nav-nav-tab">
                        ');
                        include_once('views/navigate.php');
                        echo ('
                            </div>
                        ');
                    }
                    ?>
                </div>
                <div class="col-sm-2">
                    <?php include_once('views/sidebar.php'); ?>
                </div>
            </div>
        </div>
    </body>
</html>


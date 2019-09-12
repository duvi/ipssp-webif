<?php

include('res/db.php');

function get_areas() {
    global $info_message;

    $sql = "SELECT id, name, polygon
            FROM area_list
            ORDER BY id";

    $result = db_select($sql);
    if (!$result) {
        $info_message .= "Area list not received.\n";
    }

    $i = 0;
    $rows = array();
    while ($row = mysqli_fetch_array($result)) {
        $rows[$i]["id"] = $row["id"];
        $rows[$i]["name"] = $row["name"];
        $rows[$i]["polygon"] = json_decode($row["polygon"]);
        $i++;
    }

    mysqli_free_result($result);

    return $rows;
}

function print_positions($positions, $pos) {
    include('res/config.php');
    global $info_message;
    $result = "";

    if ($positions) {
        foreach($positions as $position) {
            $result .= '<option value="' . $position["name"] . '"';
            if (!strncmp($position["name"], $pos, 10)) {
                $result .= ' selected="selected"';
            }
            $result .= ' >' . $position["name"] . '</option><br>';
        }
    }

    return $result;
}

function get_num_pos() {
    include('res/config.php');
    global $info_message;
    $result = "";

    if ($socket = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP)) {
        socket_bind($socket, $server_ip, $in_port);
        socket_sendto($socket, "get_num_pos", 11, 0, $server_ip, $out_port);
        socket_set_block($socket);
        socket_set_option($socket, SOL_SOCKET, SO_RCVTIMEO, array("sec"=>$timeout,"usec"=>0));
        socket_recvfrom($socket, $message_in, 65535, 0, $clientIP, $clientPort);
        socket_set_nonblock($socket);
        socket_close($socket);

        list($command_in, $param_in) = sscanf($message_in, "%s %s");
        switch ($command_in) {
            case "posnum":
                list($result) = sscanf($param_in, "%i");
                $info_message .= "Next position: " . $result . "\n";
                break;
            case "":
                $info_message .= "Next position not received.\n";
        }
    }
    else {
        $info_message .= "Can't create socket\n";
    }

    return $result;
}

function get_map_name() {
    include('res/config.php');
    global $info_message;
    $result = "";

    if ($socket = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP)) {
        socket_bind($socket, $server_ip, $in_port);
        socket_sendto($socket, "get_map_name", 12, 0, $server_ip, $out_port);
        socket_set_block($socket);
        socket_set_option($socket, SOL_SOCKET, SO_RCVTIMEO, array("sec"=>$timeout,"usec"=>0));
        socket_recvfrom($socket, $message_in, 65535, 0, $clientIP, $clientPort);
        socket_set_nonblock($socket);
        socket_close($socket);

        list($command_in, $param_in) = sscanf($message_in, "%s %s");
        switch ($command_in) {
            case "mapname":
                list($result) = sscanf($param_in, "%s");
                $info_message .= "Session name: " . $result . "\n";
                break;
            case "":
                $info_message .= "Session name not received.\n";
        }
    }
    else {
        $info_message .= "Can't create socket\n";
    }

    return $result;
}

function get_rec_coord() {
    global $pos_x, $pos_y;
    $file = fopen("recording", "r");
    if ($file) {
        fscanf($file, "%i,%i", $pos_x, $pos_y);
    }
}

function get_session($ses) {
    global $message;
    $result = "";

    if ($handle = opendir('positions')) {
        while (false !== ($entry = readdir($handle))) {
            if ($entry != "." && $entry != "..") {
                $result .= '<option value="' . $entry . '"';
                if (!strncmp($entry, $ses, 10)) {
                    $result .= ' selected="selected"';
                }
                $result .= ' >' . $entry . '</option><br>';
            }
        }
        closedir($handle);
    }

    return $result;
}

function get_maps() {
    $result = "";

    if ($handle = opendir('.')) {
        while (false !== ($entry = readdir($handle))) {
            if (!is_dir($entry) && strstr($entry, ".map")) {
                $entry = substr($entry, 0, -strlen(".map"));
                $result .= '<option value="' . $entry . '"';
                $result .= ' >' . $entry . '</option><br>';
            }
        }
        closedir($handle);
    }

    return $result;
}

function get_mac($ssh_host,$ssh_port,$ssh_user,$ssh_pass,$ssh_comm) {
    $ipFound = FALSE;
    // This code is under the GNU Public Licence
    // Written by michael_stankiewicz {don't spam} at yahoo {no spam} dot com
    // Tested only on linux, please report bugs

    // WARNING: the commands 'which' and 'arp' should be executable
    // by the apache user; on most linux boxes the default configuration
    // should work fine

    // Get the arp executable path
    $location = `which arp`;

    // Execute the arp command and store the output in $arpTable
    // $arpTable = `$location`;
    // $arpTable = `/sbin/arp -n`;
    $connection = ssh2_connect($ssh_host, $ssh_port);
    ssh2_auth_password($connection, $ssh_user, $ssh_pass);
    $stream = ssh2_exec($connection, $ssh_comm);
    stream_set_blocking($stream, true);
    $out = ssh2_fetch_stream($stream, SSH2_STREAM_STDIO);
    $arpTable = stream_get_contents($out);

    // Split the output so every line is an entry of the $arpSplitted array
    $arpSplitted = explode("\n",$arpTable);

    // Get the remote ip address (the ip address of the client, the browser)
    $remoteIp = $_SERVER['REMOTE_ADDR'];

    // Cicle the array to find the match with the remote ip address
    foreach ($arpSplitted as $value) {
        // Split every arp line, this is done in case the format of the arp
        // command output is a bit different than expected
        $valueSplitted = explode(" ",$value);
        foreach ($valueSplitted as $spLine) {
            if (preg_match("/$remoteIp/",$spLine)) {
                $ipFound = true;
            }
            // The ip address has been found, now rescan all the string
            // to get the mac address
            if ($ipFound) {
                // Rescan all the string, in case the mac address, in the string
                // returned by arp, comes before the ip address
                // (you know, Murphy's laws)
                reset($valueSplitted);
                foreach ($valueSplitted as $spLine) {
                    if (preg_match("/[0-9a-f][0-9a-f][:-]".
                        "[0-9a-f][0-9a-f][:-]".
                        "[0-9a-f][0-9a-f][:-]".
                        "[0-9a-f][0-9a-f][:-]".
                        "[0-9a-f][0-9a-f][:-]".
                        "[0-9a-f][0-9a-f]/i",$spLine)) {
                        return $spLine;
                    }
                }
            }
            $ipFound = false;
        }
    }
    return false;
}

?>

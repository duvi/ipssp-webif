<?php

if ($_POST && isset($_POST['command'])) {
    $command = $_POST['command'];
}
else {
    return;
}

require_once('../res/db.php');

switch ($command) {
    case "get_stations":
        get_stations();
        break;
    case "show_station":
        show_station();
        break;
}

function get_stations() {
    $message = "";

    $sql = "SELECT `sta_id`, `record`, `r`, `g`, `b`
            FROM `station_list`
            WHERE `time_last` > DATE_SUB(NOW(), INTERVAL 30 SECOND)
            ORDER BY `time_last` DESC";

    $result = db_select($sql);
    if (!$result) {
        $message .= "Station list not received.\n";
    }
    elseif ($result->num_rows == 0) {
        $message .= "No stations are currently available.\n";
    }
    else {
        $message .= $result->num_rows . " stations received.\n";
    }

    $rows = array();
    while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
        {
        $rows[] = $row;
        }

    mysqli_free_result($result);

    echo json_encode(array('result'=>$rows,'message'=>nl2br($message)));
}

function show_station() {
    $message = "";

    if ($_POST && isset($_POST['station'])) {
        $station = $_POST['station'];
    }
    else {
        return;
    }

    $sql = "SELECT `sta_id`, `channel`
            FROM `station_list`
            WHERE `sta_id` = '" . $station . "'";

    $result = db_select($sql);
    if (!$result)
    {
        $message .= "Station not received.\n";
    }

    if ($row = mysqli_fetch_array($result))
        {
        $message .= "STATION: " . $row['sta_id'] . " CHANNEL: " . $row['channel'] . " \n";

        $sql = "SELECT `monitor_list`.`name`, `station_data`.`signal`, `station_data`.`time_rcv`
                            FROM `station_data`
                            LEFT JOIN `monitor_list`
                            ON `station_data`.`mon_id` = `monitor_list`.`mon_id`
                            WHERE `station_data`.`sta_id` = '" . $station . "'";

        $result2 = db_select($sql);

        while ($row2 = mysqli_fetch_array($result2))
            {
            $message .= "MONITOR: " . $row2['name'] . " SIGNAL: -" . $row2['signal'] . " dBm  TIME: " . $row2['time_rcv'] . " \n";
            }
        mysqli_free_result($result2);
        }

    mysqli_free_result($result);

    echo json_encode(array('message'=>nl2br($message)));
}

?>

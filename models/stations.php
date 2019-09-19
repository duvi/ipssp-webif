<?php

if ($_POST && isset($_POST['command'])) {
    $command = $_POST['command'];
}
else {
    $command = NULL;
}

require_once(__DIR__ . '/../res/db.php');

switch ($command) {
    case "get_stations":
        get_stations(TRUE);
        break;
    case "show_station":
        $station = isset($_POST['station']) ? $_POST['station'] : '';
        show_station($station);
        break;
}

function get_stations($ajax = FALSE) {
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
    while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
        $rows[] = $row;
    }

    mysqli_free_result($result);

    if ($ajax) echo json_encode(array('result'=>$rows,'message'=>nl2br($message)));
    else {
        global $info_message;
        $info_message .= $message;
        return $rows;
    }
}

function show_station($station) {
    $message = "";

    $sql = "SELECT `sta_id`, `channel`
            FROM `station_list`";
    if ($station) $sql .= " WHERE `sta_id` = '" . $station . "'";
    $sql .= " ORDER BY `time_last` DESC";

    $result = db_select($sql);
    if (!$result) {
        $message .= "Station not received.\n";
    }
    elseif ($result->num_rows == 0) {
        $message .= "Station(s) not found in database.\n";
    }

    while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
        $message .= "STATION: " . $row['sta_id'] . " CHANNEL: " . $row['channel'] . " \n";

        $sql = "SELECT IFNULL(`monitor_list`.`name`, `monitor_data`.`ip`) AS name, `station_data`.`signal`, `station_data`.`time_rcv`
                            FROM `station_data`
                            LEFT JOIN `monitor_list` ON `station_data`.`mon_id` = `monitor_list`.`mon_id`
                            LEFT JOIN `monitor_data` ON `station_data`.`mon_id` = `monitor_data`.`mon_id`
                            WHERE `station_data`.`sta_id` = '" . $row['sta_id'] . "'";

        $result2 = db_select($sql);

        while ($row2 = mysqli_fetch_array($result2, MYSQLI_ASSOC)) {
            $message .= "MONITOR: " . $row2['name'] . " SIGNAL: -" . $row2['signal'] . " dBm  TIME: " . $row2['time_rcv'] . " \n";
        }
        mysqli_free_result($result2);
    }

    mysqli_free_result($result);

    echo json_encode(array('message'=>nl2br($message)));
}

?>

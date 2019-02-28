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

?>

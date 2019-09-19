<?php
/* if started from commandline, wrap parameters to $_POST and $_GET */
if (!isset($_SERVER["HTTP_HOST"])) {
    parse_str($argv[1], $_GET);
    parse_str($argv[1], $_POST);
}

if ($_POST && isset($_POST['command'])) {
    $command = $_POST['command'];
}
else {
    $command = NULL;
}

require_once(__DIR__ . '/../res/db.php');

switch ($command) {
    case "get_monitors":
        get_monitors(TRUE);
        break;
    case "show_monitor":
        $monitor = isset($_POST['monitor']) ? $_POST['monitor'] : '';
        show_monitor($monitor);
        break;
}

function get_monitors($ajax = FALSE) {
    $message = "";

    $sql = "SELECT `mon_id` AS `mac`, `ip`, `x`, `y`
            FROM `monitor_list`
            ORDER BY `ip`";

    $result = db_select($sql);
    if (!$result) {
        $message .= "Monitor list not received.\n";
    }
    elseif ($result->num_rows == 0) {
        $message .= "No monitors are currently available.\n";
    }
    else {
        $message .= $result->num_rows . " monitors received.\n";
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

function show_monitor($monitor) {
    $message = "";

    $sql = "SELECT `position_data`.`signal`, `position_list`.`x`, `position_list`.`y`
            FROM `position_data`
            LEFT JOIN `position_list` ON `position_data`.`pos_id` = `position_list`.`pos_id`";

    if ($monitor) $sql .= " WHERE `position_data`.`mon_id` = '" . $monitor . "'";

    $result = db_select($sql);
    if (!$result) {
        $message .= "Signals not received.\n";
    }
    elseif ($result->num_rows == 0) {
        $message .= "Value(s) not found in database.\n";
    }
    else {
        $message .= $result->num_rows . " values received.\n";
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

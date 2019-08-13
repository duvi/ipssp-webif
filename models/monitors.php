<?php

if ($_POST && isset($_POST['command'])) {
    $command = $_POST['command'];
}
else {
    return;
}

require_once('../res/db.php');

switch ($command) {
    case "show_monitor":
        $monitor = isset($_POST['monitor']) ? $_POST['monitor'] : '';
        show_monitor($monitor);
        break;
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
    while ($row = mysqli_fetch_array($result))
        {
        $rows[] = $row;
        }

    mysqli_free_result($result);

    echo json_encode(array('result'=>$rows,'message'=>nl2br($message)));
}

?>

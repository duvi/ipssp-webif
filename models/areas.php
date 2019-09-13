<?php

if ($_POST && isset($_POST['command'])) {
    $command = $_POST['command'];
}
else {
    $command = NULL;
}

require_once(__DIR__ . '/../res/db.php');

switch ($command) {
    case "get_areas":
        get_area(TRUE);
        break;
}

function get_areas($ajax = FALSE) {
    $message = "";

    $sql = "SELECT `id`, `name`, `polygon`
            FROM `area_list`
            ORDER BY `id`";

    $result = db_select($sql);
    if (!$result) {
        $info_message .= "Area list not received.\n";
    }
    elseif ($result->num_rows == 0) {
        $message .= "No areas are currently available.\n";
    }
    else {
        $message .= $result->num_rows . " areas received.\n";
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

    if ($ajax) echo json_encode(array('result'=>$rows,'message'=>nl2br($message)));
    else {
        global $info_message;
        $info_message .= $message;
        return $rows;
    }
}

?>

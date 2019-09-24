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
    case "get_areas":
        get_areas(TRUE);
        break;
    case "show_area":
        $area = isset($_POST['area']) ? $_POST['area'] : '';
        show_area(TRUE, $area);
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
    while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
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

function show_area($ajax = FALSE, $area = NULL) {
    $message = "";

    $sql = "SELECT `id`, `name`, `header`, `image`, `content`
            FROM `area_list`";

    if ($area) $sql .= " WHERE `id` = '" . $area . "'";

    $result = db_select($sql);
    if (!$result) {
        $message .= "Area data not received.\n";
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
        $message .= $row['content'];
        }

    mysqli_free_result($result);

    if ($ajax) echo json_encode(array('result'=>$rows,'message'=>nl2br($message)));
    else return $rows;
}

?>

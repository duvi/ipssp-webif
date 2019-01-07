<?php

require_once('db.php');

$message = "";

    $sql = "SELECT park_list.park_id, park_list.x, park_list.y, park_data.free
            FROM park_list
            LEFT JOIN park_data
            ON park_data.park_id = park_list.park_id";

    $result = db_select($sql);
    if (!$result)
    {
        $message .= "Places not received.\n";
    }

    $rows = array();
    while ($row = mysqli_fetch_array($result))
        {
        $rows[] = $row;
        }

    mysqli_free_result($result);

    $message .= json_encode($rows);

//    echo json_encode(array('result'=>$rows,'message'=>nl2br($message)));
    echo json_encode(array('result'=>$rows));

?>

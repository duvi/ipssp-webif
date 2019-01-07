<?php

require_once('db.php');

$message = "";

    if ($_POST && isset($_POST['mon']))
        {
        $param = $_POST['mon'];
        }
    else
        {
        $param = "b0487ac5f0be";
        }

    $sql = "SELECT position_data.signal, position_list.x, position_list.y
            FROM position_data
            LEFT JOIN position_list
            ON position_data.pos_id = position_list.pos_id
            WHERE position_data.mon_id='" . $param . "'";

    $result = db_select($sql);
    if (!$result)
    {
        $message .= "Signals not received.\n";
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

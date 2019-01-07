<?php

require_once('db.php');

$message = "";

    if ($_POST && isset($_POST['pos']))
        {
        $param = $_POST['pos'];
        }
    else
        {
        $param = "120";
        }

    $sql = "SELECT x, y, time_rec
            FROM position_list
            WHERE pos_id = '" . $param . "'";

    $result = db_select($sql);
    if (!$result)
        {
            $message .= "Position not found.\n";
        }

    if ($row = mysqli_fetch_array($result))
        {
        $message .= "POSITION: " . $param . " [" . $row['x'] . "," . $row['y'] . "] TIME: " . $row['time_rec'] . " \n";

        $sql = "SELECT position_data.signal, position_data.mean, position_data.std_dev, monitor_list.ip
                FROM position_data
                LEFT JOIN monitor_list
                ON position_data.mon_id = monitor_list.mon_id
                WHERE position_data.pos_id = '" . $param . "'
                ORDER BY position_data.mean";

        $result2 = db_select($sql);

        while ($row2 = mysqli_fetch_array($result2))
            {
            $message .= "MON: " . $row2['ip'] . " SIG: -" . $row2['signal'] . " dBm   MEAN: -" . $row2['mean'] . " dBm  DEV: " . $row2['std_dev'] . " \n";
            }
        mysqli_free_result($result2);
        }

    mysqli_free_result($result);

    echo json_encode(array('x'=>$row['x'],'y'=>$row['y'],'message'=>nl2br($message)));

?>

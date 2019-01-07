<?php

require_once('db.php');

$message = "";

    $sql = "SELECT pos_id, x, y, time_rec
            FROM position_list";

    $result = db_select($sql);

    if (!$result)
        {
        $message .= "Positions not received.\n";
        }

    while ($row = mysqli_fetch_array($result))
        {
        $message .= "POSITION: " . $row['pos_id'] . " [" . $row['x'] . "," . $row['y'] . "] TIME: " . $row['time_rec'] . " \n";

        $sql = "SELECT position_data.signal, position_data.mean, position_data.std_dev, monitor_list.ip
                FROM position_data
                LEFT JOIN monitor_list
                ON position_data.mon_id = monitor_list.mon_id
                WHERE position_data.pos_id = '" . $row['pos_id'] . "'
                ORDER BY position_data.mean";

        $result2 = db_select($sql);

        while ($row2 = mysqli_fetch_array($result2))
            {
            $message .= "MON: " . $row2['ip'] . " SIG: -" . $row2['signal'] . " dBm   MEAN: -" . $row2['mean'] . " dBm  DEV: " . $row2['std_dev'] . " \n";
            }
        mysqli_free_result($result2);
        }

    mysqli_free_result($result);

    echo json_encode(array('message'=>nl2br($message)));

?>

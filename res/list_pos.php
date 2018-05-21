<?php

require_once('config.php');

$message = "";

    $connection = mysql_connect($db_host,$db_user,$db_pass);
    if (!$connection)
        {
        die("Database connection failed: " . mysql_error());
        }

    $db_select = mysql_select_db($db_name,$connection);
    if (!$db_select)
        {
        die("Database selection failed: " . mysql_error());
        }

    $sql = "SELECT pos_id, x, y, time_rec
            FROM position_list";

    $result = mysql_query($sql, $connection);
    if (!$result)
        {
        die("Database query failed: " . mysql_error());
        }

    while ($row = mysql_fetch_array($result))
        {
        $message .= "POSITION: " . $row['pos_id'] . " [" . $row['x'] . "," . $row['y'] . "] TIME: " . $row['time_rec'] . " \n";

        $sql = "SELECT position_data.signal, position_data.mean, position_data.std_dev, monitor_list.ip
                FROM position_data
                LEFT JOIN monitor_list
                ON position_data.mon_id = monitor_list.mon_id
                WHERE position_data.pos_id = '" . $row['pos_id'] . "'
                ORDER BY position_data.mean";

        $result2 = mysql_query($sql, $connection);
        if (!$result2)
            {
            die("Database query failed: " . mysql_error());
            }
        while ($row2 = mysql_fetch_array($result2))
            {
            $message .= "MON: " . $row2['ip'] . " SIG: -" . $row2['signal'] . " dBm   MEAN: -" . $row2['mean'] . " dBm  DEV: " . $row2['std_dev'] . " \n";
            }
        mysql_free_result($result2);
        }

    mysql_free_result($result);
    mysql_close($connection);

    echo json_encode(array('message'=>nl2br($message)));

?>


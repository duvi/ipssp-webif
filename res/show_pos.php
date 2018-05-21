<?php

require_once('config.php');

$message = "";
$pos_x = "";
$pos_y = "";

    if ($_POST && isset($_POST['pos']))
        {
        $param = $_POST['pos'];
        }
    else
        {
        $param = "120";
        }

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

    $sql = "SELECT x, y, time_rec
            FROM position_list
            WHERE pos_id = '" . $param . "'";

    $result = mysql_query($sql, $connection);
    if (!$result)
        {
        die("Database query failed: " . mysql_error());
        }

    if ($row = mysql_fetch_array($result))
        {
        $pos_x = $row["x"];
        $pos_y = $row["y"];
        $message .= "POSITION: " . $param . " [" . $pos_x . "," . $pos_y . "] TIME: " . $row['time_rec'] . " \n";

        $sql = "SELECT position_data.signal, position_data.mean, position_data.std_dev, monitor_list.ip
                FROM position_data
                LEFT JOIN monitor_list
                ON position_data.mon_id = monitor_list.mon_id
                WHERE position_data.pos_id = '" . $param . "'
                ORDER BY position_data.mean";

        $result = mysql_query($sql, $connection);
        if (!$result)
            {
            die("Database query failed: " . mysql_error());
            }
        while ($row = mysql_fetch_array($result))
            {
            $message .= "MON: " . $row['ip'] . " SIG: -" . $row['signal'] . " dBm   MEAN: -" . $row['mean'] . " dBm  DEV: " . $row['std_dev'] . " \n";
            }
        }
    else
        {
        $message .= "Position not found.\n";
        }

    mysql_free_result($result);
    mysql_close($connection);

    echo json_encode(array('x'=>$pos_x,'y'=>$pos_y,'message'=>nl2br($message)));

?>


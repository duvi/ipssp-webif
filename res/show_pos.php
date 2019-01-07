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

    $connection = mysqli_connect($db_host,$db_user,$db_pass);
    if (!$connection)
        {
        die("Database connection failed: " . mysqli_error($connection));
        }

    $db_select = mysqli_select_db($connection,$db_name);
    if (!$db_select)
        {
        die("Database selection failed: " . mysqli_error($connection));
        }

    $sql = "SELECT x, y, time_rec
            FROM position_list
            WHERE pos_id = '" . $param . "'";

    $result = mysqli_query($connection,$sql);
    if (!$result)
        {
        die("Database query failed: " . mysqli_error($connection));
        }

    if ($row = mysqli_fetch_array($result))
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

        $result = mysqli_query($connection,$sql);
        if (!$result)
            {
            die("Database query failed: " . mysqli_error($connection));
            }
        while ($row = mysqli_fetch_array($result))
            {
            $message .= "MON: " . $row['ip'] . " SIG: -" . $row['signal'] . " dBm   MEAN: -" . $row['mean'] . " dBm  DEV: " . $row['std_dev'] . " \n";
            }
        }
    else
        {
        $message .= "Position not found.\n";
        }

    mysqli_free_result($result);
    mysqli_close($connection);

    echo json_encode(array('x'=>$pos_x,'y'=>$pos_y,'message'=>nl2br($message)));

?>


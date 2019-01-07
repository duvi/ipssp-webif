<?php

require_once('db.php');

$message = "";

    if ($_POST && isset($_POST['sta']))
        {
        $param = $_POST['sta'];
        }
    else
        {
        $param = "183da22eeaec";
        }

    $sql = "SELECT sta_id, channel
            FROM station_list
            WHERE sta_id='" . $param . "'";

    $result = db_select($sql);
    if (!$result)
    {
        $message .= "Station not received.\n";
    }

    if ($row = mysqli_fetch_array($result))
        {
        $message .= "STATION: " . $row['sta_id'] . " CHANNEL: " . $row['channel'] . " \n";

        $sql = "SELECT monitor_data.ip, station_data.signal, station_data.time_rcv
                            FROM station_data
                            LEFT JOIN monitor_data
                            ON station_data.mon_id = monitor_data.mon_id
                            WHERE station_data.sta_id = '" . $param . "'";

        $result2 = db_select($sql);

        while ($row2 = mysqli_fetch_array($result2))
            {
            $message .= "MONITOR: " . $row2['ip'] . " SIGNAL: -" . $row2['signal'] . " dBm  TIME: " . $row2['time_rcv'] . " \n";
            }
        mysqli_free_result($result2);
        }

    mysqli_free_result($result);

    echo json_encode(array('message'=>nl2br($message)));

?>

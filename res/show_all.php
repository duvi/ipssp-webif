<?php

require_once('db.php');

$message = "";

    $sql = "SELECT sta_id, channel
            FROM station_list";

    $result = db_select($sql);
    if (!$result)
    {
        $message .= "Station not received.\n";
    }

    while ($row = mysqli_fetch_array($result))
        {
        $message .= "STATION: " . $row['sta_id'] . " CHANNEL: " . $row['channel'] . " \n";

        $sql = "SELECT monitor_list.name, station_data.signal, station_data.time_rcv
                            FROM station_data
                            LEFT JOIN monitor_list
                            ON station_data.mon_id = monitor_list.mon_id
                            WHERE station_data.sta_id = '" . $row['sta_id'] . "'";

        $result2 = db_select($sql);

        while ($row2 = mysqli_fetch_array($result2))
            {
            $message .= "MONITOR: " . $row2['name'] . " SIGNAL: -" . $row2['signal'] . " dBm  TIME: " . $row2['time_rcv'] . " \n";
            }
        mysqli_free_result($result2);
        }

    mysqli_free_result($result);

    echo json_encode(array('message'=>nl2br($message)));

?>

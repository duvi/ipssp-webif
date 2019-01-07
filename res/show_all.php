<?php

require_once('config.php');

$message = "";

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
    $sql = "SELECT sta_id, channel
            FROM station_list";

    $result = mysqli_query($connection,$sql);
    if (!$result)
    {
        $message .= "Station not received.\n";
        die("Database query failed: " . mysqli_error($connection));
    }

    while ($row = mysqli_fetch_array($result))
        {
        $message .= "STATION: " . $row['sta_id'] . " CHANNEL: " . $row['channel'] . " \n";

        $sql = "SELECT monitor_data.ip, station_data.signal, station_data.time_rcv
                            FROM station_data
                            LEFT JOIN monitor_data
                            ON station_data.mon_id = monitor_data.mon_id
                            WHERE station_data.sta_id = '" . $row['sta_id'] . "'";

        $result2 = mysqli_query($connection,$sql);
        if (!$result2)
            {
            die("Database query failed: " . mysqli_error($connection));
            }
        while ($row2 = mysqli_fetch_array($result2))
            {
            $message .= "MONITOR: " . $row2['ip'] . " SIGNAL: -" . $row2['signal'] . " dBm  TIME: " . $row2['time_rcv'] . " \n";
            }
        mysqli_free_result($result2);
        }

    mysqli_free_result($result);
    mysqli_close($connection);

    echo json_encode(array('message'=>nl2br($message)));

?>


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
    $sql = "SELECT sta_id, channel
            FROM station_list";

    $result = mysql_query($sql, $connection);
    if (!$result)
    {
        $message .= "Station not received.\n";
        die("Database query failed: " . mysql_error());
    }

    while ($row = mysql_fetch_array($result))
        {
        $message .= "STATION: " . $row['sta_id'] . " CHANNEL: " . $row['channel'] . " \n";

        $sql = "SELECT monitor_data.ip, station_data.signal, station_data.time_rcv
                            FROM station_data
                            LEFT JOIN monitor_data
                            ON station_data.mon_id = monitor_data.mon_id
                            WHERE station_data.sta_id = '" . $row['sta_id'] . "'";

        $result2 = mysql_query($sql, $connection);
        if (!$result2)
            {
            die("Database query failed: " . mysql_error());
            }
        while ($row2 = mysql_fetch_array($result2))
            {
            $message .= "MONITOR: " . $row2['ip'] . " SIGNAL: -" . $row2['signal'] . " dBm  TIME: " . $row2['time_rcv'] . " \n";
            }
        mysql_free_result($result2);
        }

    mysql_free_result($result);
    mysql_close($connection);

    echo json_encode(array('message'=>nl2br($message)));

?>


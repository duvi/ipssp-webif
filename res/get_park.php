<?php

require_once('config.php');

$rows = array();
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

    $sql = "SELECT park_list.park_id, park_list.x, park_list.y, park_data.free
            FROM park_list
            LEFT JOIN park_data
            ON park_data.park_id = park_list.park_id";

    $result = mysql_query($sql, $connection);
    if (!$result)
    {
        $message .= "Places not received.\n";
        die("Database query failed: " . mysql_error());
    }

    while ($row = mysql_fetch_array($result))
        {
        $rows[] = $row;
        }

    mysql_free_result($result);
    mysql_close($connection);

    $message .= json_encode($rows);

//    echo json_encode(array('result'=>$rows,'message'=>nl2br($message)));
    echo json_encode(array('result'=>$rows));

?>
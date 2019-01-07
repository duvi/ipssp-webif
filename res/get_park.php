<?php

require_once('config.php');

$rows = array();
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

    $sql = "SELECT park_list.park_id, park_list.x, park_list.y, park_data.free
            FROM park_list
            LEFT JOIN park_data
            ON park_data.park_id = park_list.park_id";

    $result = mysqli_query($connection,$sql);
    if (!$result)
    {
        $message .= "Places not received.\n";
        die("Database query failed: " . mysqli_error($connection));
    }

    while ($row = mysqli_fetch_array($result))
        {
        $rows[] = $row;
        }

    mysqli_free_result($result);
    mysqli_close($connection);

    $message .= json_encode($rows);

//    echo json_encode(array('result'=>$rows,'message'=>nl2br($message)));
    echo json_encode(array('result'=>$rows));

?>
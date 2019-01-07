<?php

require_once('config.php');

$rows = array();
$message = "";

    if ($_POST && isset($_POST['mon']))
        {
        $param = $_POST['mon'];
        }
    else
        {
        $param = "b0487ac5f0be";
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

    $sql = "SELECT position_data.signal, position_list.x, position_list.y
            FROM position_data
            LEFT JOIN position_list
            ON position_data.pos_id = position_list.pos_id
            WHERE position_data.mon_id='" . $param . "'";

    $result = mysqli_query($connection,$sql);
    if (!$result)
    {
        $message .= "Signals not received.\n";
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


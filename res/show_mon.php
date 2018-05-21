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

    $sql = "SELECT position_data.signal, position_list.x, position_list.y
            FROM position_data
            LEFT JOIN position_list
            ON position_data.pos_id = position_list.pos_id
            WHERE position_data.mon_id='" . $param . "'";

    $result = mysql_query($sql, $connection);
    if (!$result)
    {
        $message .= "Signals not received.\n";
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


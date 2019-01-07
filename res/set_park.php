<?php

require_once('config.php');

$message = "";

    if ($_POST && isset($_POST['park_id']) && isset($_POST['free']))
        {
        $park_id = $_POST['park_id'];
        $free = $_POST['free'];

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

        $sql = "REPLACE INTO park_data(park_id,free)
                VALUES(" . $park_id . "," . $free . ")";

        $result = mysqli_query($connection,$sql);
        if (!$result)
            {
            $message .= "Something went wrong with the database.\n";
            }
        else
            {
            $message .= "Successfully set park#" . $park_id. " status to ";
            switch ($free)
                {
                case 0:
                    $message .= "occupied.\n";
                    break;
                case 1:
                    $message .= "free.\n";
                    break;
                case 2:
                    $message .= "unknown.\n";
                    break;
                }
            $message .= "Please wait, refreshing...\n";
            }
        mysqli_free_result($result);
        mysqli_close($connection);
        }
    else
        {
        $message .= "Not enough parameters!\n";
        }

    echo json_encode(array('message'=>nl2br($message)));

?>
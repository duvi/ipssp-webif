<?php

function db_select($sql) {
    require_once('config.php');

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

    $result = mysqli_query($connection,$sql);
    if (!$result)
        {
        die("Database query failed: " . mysqli_error($connection));
        }

    mysqli_close($connection);

    return $result;
    }

?>

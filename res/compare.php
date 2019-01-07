<?php

    require_once('config.php');

    $sql = "";
    $connection;
    $param;
    $message = "";
    $pos_x = "";
    $pos_y = "";
    $pre_x = "";
    $pre_y = "";
    $pre_limit = "";
    $limit = "";
    $sta_r = "";
    $sta_g = "";
    $sta_b = "";

    function distance($x1, $y1, $x2, $y2)
        {
        global $message;
        $dist = round(sqrt(($x1-$x2)*($x1-$x2)+($y1-$y2)*($y1-$y2)));
//        $message .= "distance: " . $dist . "\n";
        return $dist;
        }

    function calc_point($input)
        {
        global $message;
        global $pos_x;
        global $pos_y;

        if ($row = mysqli_fetch_array($input))
            {
            $pos_x = $row["x"];
            $pos_y = $row["y"];
            $message .= "pos_id: " . $row["pos_id"] . "\n";
            $message .= "calc: " . $row["sum_diff"] . "\n";
            }
        }

    function calc_area($input)
        {
        global $message;
        global $pos_x;
        global $pos_y;
        global $max_dist;
        $result = array();
        $temp = 0;
        $i = 0;

        $file = fopen("area.ini", "r");
        while (fscanf($file, "%i,%i", $result[$i]["x"], $result[$i]["y"]))
            {
//            $message .= "area: " . $result[$i]["x"] . "," .  $result[$i]["y"] . "\n";
            $result[$i]["n"] = 0;
            $result[$i]["calc"] = 0;
            while ($row = mysqli_fetch_array($input))
                {
                if (distance($result[$i]["x"], $result[$i]["y"], $row["x"], $row["y"]) < $max_dist)
                    {
//                    $message .= "point: " . $row["x"] . "," . $row["y"] . " calc: " . $row["sum_diff"] . "\n";
                    $result[$i]["calc"] += $row["sum_diff"];
                    $result[$i]["n"]++;
                    }
                }
            if (($result[$i]["calc"]/$result[$i]["n"]) > $temp)
                {
                $temp = $result[$i]["calc"]/$result[$i]["n"];
                $pos_x = $result[$i]["x"];
                $pos_y = $result[$i]["y"];
                }
//            $message .= "calc: " . $result[$i]["calc"]/$result[$i]["n"] . "\n";
            mysqli_data_seek($input,0);
            $i++;
            }
        }

    function calc_pre_pos()
        {
        global $message;
        global $connection;
        global $param;
        global $pos_x;
        global $pos_y;
        global $pre_x;
        global $pre_y;
        global $pre_limit;
        global $limit;
        global $top;
        global $bottom;
        global $uprate;
        global $downrate;

        if ($pre_x && $pre_y)
            {
            $dist = distance($pre_x, $pre_y, $pos_x, $pos_y);
            $pos_x = round(($pos_x + $pre_x) / 2);
            $pos_y = round(($pos_y + $pre_y) / 2);
            }
        $limit = $pre_limit;
        $message .= " Dist: " . $dist . "\n";
        if ($dist > $pre_limit)
            {
            $pos_x = $pre_x;
            $pos_y = $pre_y;
            $limit = (($pre_limit + $dist * $uprate) > $top) ? $top : (round($pre_limit + $dist * $uprate));
            }
        if ($dist < $pre_limit)
            {
            $limit = (($pre_limit * $downrate) < $bottom) ? $bottom : (round($pre_limit * $downrate));
            }
        $sql = "UPDATE station_list
                SET x=" . $pos_x . ", y=" . $pos_y . ", lim=" . $limit . "
                WHERE sta_id = '" . $param . "'";
        $result = mysqli_query($connection,$sql);
        mysqli_free_result($result);

        }

    if ($_POST && isset($_POST['command']) && isset($_POST['sta']))
        {
        $command = $_POST['command'];
        $param = $_POST['sta'];

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

        switch ($command)
            {
            case "comp_diff":
                $sql_defmon = "SELECT signal, mon_id
                                FROM station_data
                                WHERE signal = (SELECT min(signal)
                                                FROM station_data
                                                WHERE station_data.sta_id = '" . $param . "'
                                                " . $timeout_sql . ")
                                AND sta_id = '" . $param . "'
                                " . $timeout_sql . "
                                ORDER BY time_rcv DESC
                                ";
                $result = mysqli_query($connection,$sql_defmon);
                if (!$result)
                    {
                    $message .= "Default monitor not received.\n";
                    die("Database query failed: " . mysqli_error($connection));
                    }
                else if ($row = mysqli_fetch_array($result))
                    {
                    $def_mon_signal = $row["signal"];
                    $def_mon_id = $row["mon_id"];
                    $message .= "Default monitor: " . $def_mon_id . " " . $def_mon_signal . " dBm\n";

                    $sql = "SELECT t1.pos_id,
                                    t1.x,
                                    t1.y,
                                    AVG(CASE WHEN ABS(sta_diff)<ABS(t1.mean-def_mon_signal)
                                        THEN sta_diff/(t1.mean-def_mon_signal)
                                        ELSE (t1.mean-def_mon_signal)/sta_diff
                                        END) AS sum_diff
                            FROM (
                                SELECT position_data.pos_id, position_data.mon_id, position_data.mean, position_list.x, position_list.y
                                FROM position_data
                                LEFT JOIN position_list
                                ON position_list.pos_id = position_data.pos_id
                                WHERE mon_id <> '" . $def_mon_id . "'
                                )t1
                            LEFT JOIN (
                                SELECT pos_id, signal AS def_mon_signal
                                FROM (
                                    SELECT *
                                    FROM position_data
                                    WHERE mon_id = '" . $def_mon_id . "'
                                     ) AS def_mon
                                )t2
                            ON t2.pos_id = t1.pos_id
                            INNER JOIN (
                                SELECT mon_id, (signal - " . $def_mon_signal . ") AS sta_diff
                                FROM station_data
                                WHERE sta_id = '" . $param . "'
                                AND mon_id <> '" . $def_mon_id . "'
                                " . $timeout_sql . "
                                )t3
                            ON t3.mon_id = t1.mon_id
                            GROUP BY pos_id
                            ORDER BY sum_diff DESC
                    ";
                    }
                else
                    {
                    $message .= "Default monitor not received.\n";
                    }
                break;
            case "comp_knn":
                $sql = "SELECT t1.pos_id,
                                t1.x,
                                t1.y,
                                1/SUM(POW(POW(10,(0-mean)/10)-POW(10,(0-signal)/10),2)) AS sum_diff
                        FROM (
                            SELECT position_data.pos_id, position_data.mon_id, position_data.mean, position_list.x, position_list.y
                            FROM position_data
                            LEFT JOIN position_list
                            ON position_list.pos_id = position_data.pos_id
                            )t1
                        INNER JOIN (
                            SELECT mon_id, signal
                            FROM station_data
                            WHERE sta_id = '" . $param . "'
                            " . $timeout_sql . "
                            )t2
                        ON t2.mon_id = t1.mon_id
                        GROUP BY pos_id
                        ORDER BY sum_diff
                ";
                break;
            }

        if ($sql)
            {
            $result = mysqli_query($connection,$sql);
            if (!$result)
                {
                $message .= "Places not received.\n";
                die("Database query failed: " . mysqli_error($connection));
                }
            else
                {
                calc_area($result);
//                calc_point($result);
                }

            $sql = "SELECT r, g, b, x, y, lim
                    FROM station_list
                    WHERE sta_id = '" . $param . "'";
            $result = mysqli_query($connection,$sql);
            if (!$result)
                {
                $message .= "Colors / ex-position not received.\n";
                die("Database query failed: " . mysqli_error($connection));
                }
            else
                {
                $sta_r = mysqli_result($result, 0, 0);
                $sta_g = mysqli_result($result, 0, 1);
                $sta_b = mysqli_result($result, 0, 2);
                $pre_x = mysqli_result($result, 0, 3);
                $pre_y = mysqli_result($result, 0, 4);
                $pre_limit = mysqli_result($result, 0, 5);
                $message .= "Colors: " . $sta_r . " " . $sta_g . " " . $sta_b . "\n";
                $message .= "Ex-position: " . $pre_x . " " . $pre_y . " Limit: " . $pre_limit . "\n";
                $message .= "Alg-position: " . $pos_x . " " . $pos_y;
                }

            calc_pre_pos();
            $message .= "Final-position: " . $pos_x . " " . $pos_y . " Limit: " . $limit . "\n";
            mysqli_free_result($result);
            }
        else
            {
            $message .= "Wrong algorhythm specified!\n";
            }
        mysqli_close($connection);
        }
    else
        {
        $message .= "Not enough parameters!\n";
        }

    echo json_encode(array('x'=>$pos_x,'y'=>$pos_y,'r'=>$sta_r,'g'=>$sta_g,'b'=>$sta_b,'message'=>nl2br($message)));

?>


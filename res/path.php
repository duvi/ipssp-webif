<?php

    if ($_POST && isset($_POST['sx']) && isset($_POST['sy']) && isset($_POST['tx']) && isset($_POST['ty']))
        {
        $sx = $_POST['sx'];
        $sy = $_POST['sy'];
        $tx = $_POST['tx'];
        $ty = $_POST['ty'];
        }
    else
        {
        $sx = 1;
        $sy = 1;
        $tx = 100;
        $ty = 100;
        }

    $url = "http://127.0.0.1:9000/api/navigate?sx=" . $sx . "&sy=" . $sy . "&tx=" . $tx . "&ty=" . $ty;
    $homepage = file_get_contents($url);

//    echo json_encode(array('result'=>$homepage));
    echo $homepage;

?>
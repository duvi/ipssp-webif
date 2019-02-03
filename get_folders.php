<?php

$result = "";

if ($_POST && isset($_POST['session'])) {
    $session = $_POST['session'];
    if ($handle = opendir("positions/" . $session)) {
        while (false !== ($entry = readdir($handle))) {
            if ($entry != "." && $entry != ".." && is_dir("positions/" . $session . "/" . $entry)) {
                $result .= '<option value="' . $entry . '"';
/*              if (!strncmp($entry, $ses, 10)) {
                    $result .= ' selected="selected"';
                }
*/              $result .= ' >' . $entry . '</option></br>';
            }
        }
    closedir($handle);
    }
}

echo json_encode(array('message'=>nl2br($result)));

?>

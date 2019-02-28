<?php
defined('_IPSSP') or die;
?>

<h2>Locate</h2>
<div class="left">
    <div class="forms">
        <form name="locate_form" target="_parent" method="post">
            For area:<br>
            <select name="station"></select><br>
            For dots:<br>
            <div class="stations"></div>
            <label><input type="radio" name="command" value="compare" />compare</label><br>
            <label><input type="radio" name="command" value="comp_diff" CHECKED/>comp_diff</label><br>
            <label><input type="radio" name="command" value="comp_norm" />comp_norm</label><br>
            <label><input type="radio" name="command" value="comp_dist" />comp_dist</label><br>
            <label><input type="radio" name="command" value="comp_felezo" />comp_felezo</label><br>
            <label><input type="radio" name="command" value="comp_knn" />comp_knn</label><br>
            <br>
            <input type="button" id="locate_start" value="start" onClick="document.getElementById('locate_start').style.visibility = 'hidden'; document.getElementById('locate_stop').style.visibility = 'visible'; locate();">
            <input type="button" id="locate_stop" value="stop" onClick="document.getElementById('locate_start').style.visibility = 'visible'; document.getElementById('locate_stop').style.visibility = 'hidden'; clearCanvas('locate_canvas'); clearAreas('locate_svg'); clearTimeout(locate_timer); " style="visibility:hidden;" >
        </form>
    </div>
</div>
<div class="main">
    <div id="locate_map" class="map" style="background-image:url('<?php echo $mapfile; ?>'); width:<?php echo $imagesize[0]; ?>px; height:<?php echo $imagesize[1]; ?>px;">
        <canvas id="locate_canvas" width="<?php echo $imagesize[0]; ?>" height="<?php echo $imagesize[1]; ?>" style="position: absolute;"></canvas>
        <svg id="locate_svg" width="<?php echo $imagesize[0]; ?>" height="<?php echo $imagesize[1]; ?>" style="position: absolute;">
<?php foreach ($areas as $area) : ?>
    <?php
    $points = "";
    foreach ($area["polygon"] as $poligons) {
        $points .= implode(",", $poligons) . " ";
    }
    echo '<polygon id="area-' . $area["id"] . '" class="area" points="' . $points . '" fill-opacity="0" />';
    ?>
<?php endforeach; ?>
        </svg>
        <div id="locate_punkt"> </div>
    </div>
    <div id="locate_message" class="message">
        <?php echo nl2br($message); ?>
    </div>
</div>

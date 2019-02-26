<?php
defined('_IPSSP') or die;
?>

<h2>Navigate</h2>
<div class="left">
    <div class="forms">
        <form name="compare_form" target="_parent" method="post">
            <div class="stations"></div>
            <label><input type="radio" name="command" value="compare" />compare</label><br>
            <label><input type="radio" name="command" value="comp_diff" CHECKED/>comp_diff</label><br>
            <label><input type="radio" name="command" value="comp_norm" />comp_norm</label><br>
            <label><input type="radio" name="command" value="comp_dist" />comp_dist</label><br>
            <label><input type="radio" name="command" value="comp_felezo" />comp_felezo</label><br>
            <label><input type="radio" name="command" value="comp_knn" />comp_knn</label><br>
            <br>
            <input type="button" id="navigate_start" value="start" onClick="document.getElementById('navigate_start').style.visibility = 'hidden'; document.getElementById('navigate_stop').style.visibility = 'visible'; compare();">
            <input type="button" id="navigate_stop" value="stop" onClick="document.getElementById('navigate_start').style.visibility = 'visible'; document.getElementById('navigate_stop').style.visibility = 'hidden'; clearCanvas('navigate_canvas'); clearTimeout(compare_timer);" style="visibility:hidden;" >
        </form>
        <form name="navigate_form" target="_parent" method="post">
            <p id="navigate_text" style="visibility:visible;" >Set your destination on the map!</p>
        </form>
    </div>
</div>
<div class="main">
    <div id="navigate_map" class="map" style="background-image:url('<?php echo $mapfile; ?>'); width:<?php echo $imagesize[0]; ?>px; height:<?php echo $imagesize[1]; ?>px;">
        <canvas id="navigate_canvas" width="<?php echo $imagesize[0]; ?>" height="<?php echo $imagesize[1]; ?>" style="position: absolute;"></canvas>
        <img src="img/rotpunkt.png" id="compare_punkt" style="position:absolute;z-index:2;left:0px;top:0px;visibility:hidden;">
        <img src="img/rotpunkt.png" id="navigate_punkt" style="position:absolute;visibility:hidden;z-index:2;">
        <div id="nav_punkt"> </div>
    </div>
    <script>
        //drawpark();
    </script>
    <div id="compare_message" class="message">
        <?php echo nl2br($message); ?>
    </div>
</div>

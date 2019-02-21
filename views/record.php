<?php
defined('_IPSSP') or die;
?>

<h2>Record</h2>
<div class="left">
    <div class="forms">
        <p>Set recording options</p>
        <form target="_parent" method="post">
            <input type="hidden" name="command" value="set_num_pos" />
            <input type="text" name="posnum" value="<?php echo $posnum; ?>" />
            <input type="submit" value="Set next position" /> <br>
            <input type="hidden" name="tab" value="rec" />
        </form>
        <form target="_parent" method="post">
            <input type="hidden" name="command" value="set_session" />
            <input type="text" name="mapname" value="<?php echo $mapname; ?>"/>
            <input type="submit" value="Set record session folder" /> <br>
            <input type="hidden" name="tab" value="rec" />
        </form>
        <form name="pointform" target="_parent" method="post">
            <input type="hidden" name="command" value="rec_single 1" />
            x = <input type="text" name="form_x" size="4" />
            y = <input type="text" name="form_y" size="4" />
            <input type="submit" id="rec_button" style="visibility:hidden;" value="Record" /> <br>
            <input type="hidden" name="tab" value="rec" />
            <p id="rec_text" style="visibility:visible;" >Set your position on the map to record!</p>
        </form>
        Select recording stations<br>
        0: recorded<br>
        1: not recorded<br><br>
        <form target="_parent" method="post">
            <?php echo $stations_select_rec; ?>
            <br>
            <input type="submit" name="command" value="record_sta" /> <br>
            <input type="hidden" name="tab" value="rec" />
        </form>
    </div>
    <div class="info">
        <?php echo nl2br($info_message); ?>
    </div>
</div>
<div class="main">
    <div id="record_map_div" class="map" onclick="point_it(event)" style="background-image:url('<?php echo $mapfile; ?>'); width:<?php echo $imagesize[0]; ?>px; height:<?php echo $imagesize[1]; ?>px;">
        <img src="img/rotpunkt.png" id="cross" style="position:relative;visibility:hidden;z-index:2;">
<?php foreach ($positions as $position) : ?>
        <img src="img/rotpunkt.png" title="<?php echo $position["name"]; ?>" style="position:absolute;float:none;z-index:2;opacity:0.6;left:<?php echo $position["x"]-5; ?>px;top:<?php echo $position["y"]-5; ?>px;">
<?php endforeach; ?>
    </div>
    <div id="record_message" class="message">
        <?php echo nl2br($message); ?>
    </div>
</div>

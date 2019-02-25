<?php
defined('_IPSSP') or die;
?>

<h2>Recording</h2>
<div class="left">
    <div class="forms">
        <p><span id="seconds">0</span> masodperc telt el.</p>
        <form target="_parent" method="post" >
            <input type="hidden" name="command" value="rec_single 0" />
            <input type="submit" value="Record Off" />
            <input type="hidden" name="tab" value="rec" />
        </form>
    </div>
    <div class="info">
        <?php echo nl2br($info_message); ?>
    </div>
</div>
<div class="main">
    <div class="map" style="background-image:url('<?php echo $mapfile; ?>'); width:<?php echo $imagesize[0]; ?>px; height:<?php echo $imagesize[1]; ?>px;">
        <img src="img/rotpunkt.png" id="blink_img" onLoad="blink(); seconds();" style="position:relative;z-index:2;left:<?php echo $pos_x-5; ?>px;top:<?php echo $pos_y-5; ?>px;">
    </div>
    <div id="record_message" class="message">
        <?php echo nl2br($message); ?>
    </div>
</div>

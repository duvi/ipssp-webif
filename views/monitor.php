<?php
defined('_IPSSP') or die;
?>

<h2>Monitor</h2>
<div class="left">
    <div class="forms">
        <form name=monitor>
<?php foreach ($monitors as $monitor) : ?>
            <input type="button" onClick="document.getElementById('mon_message').innerHTML = 'Loading...'; show_monitor('<?php echo $monitor["mac"]; ?>'); document.getElementById('mon_message').innerHTML = '';" value="<?php echo $monitor["ip"]; ?>">
<?php endforeach; ?>
        </form>
    </div>
</div>
<div class="main">
    <div id="monitors" class="map" style="background-image:url('<?php echo $mapfile; ?>'); width:<?php echo $imagesize[0]; ?>px; height:<?php echo $imagesize[1]; ?>px;">
<?php foreach ($monitors as $monitor) : ?>
        <img src="img/rotpunkt.png" id="<?php echo $monitor["mac"]; ?>" title="<?php echo $monitor["ip"]; ?>" onClick="document.getElementById('mon_message').innerHTML = 'Loading...'; show_monitor('<?php echo $monitor["mac"]; ?>'); document.getElementById('mon_message').innerHTML = '';" style="position:absolute;float:none;z-index:3;opacity:0.4;cursor:pointer;left:<?php echo $monitor["x"]-5; ?>px;top:<?php echo $monitor["y"]-5; ?>px;">
<?php endforeach; ?>
        <div id="mon_punkt"> </div>
    </div>
    <div id="mon_message" class="message">
        <?php echo nl2br($message); ?>
    </div>
</div>

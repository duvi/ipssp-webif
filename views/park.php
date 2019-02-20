<?php
defined('_IPSSP') or die;
?>

<h2>Park</h2>
<div class="left">
    <div class="forms">
        Click on a parking place to change its status!<br><br>
    </div>
    <?php print_info($info_message); ?>
</div>
<div class="main">
    <div id="park_map" class="map" style="background-image:url('<?php echo $mapfile; ?>'); width:<?php echo $imagesize[0]; ?>px; height:<?php echo $imagesize[1]; ?>px;">
    </div>
    <div id=park_message class="info">
        <?php echo nl2br($message); ?>
    </div>
</div>

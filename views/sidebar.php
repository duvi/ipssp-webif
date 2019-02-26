<?php
defined('_IPSSP') or die;
?>

<h2>Messages</h2>
<div class="sidebar">
    <div class="buttons">
        <input type="button" class="btn btn-primary" onClick="document.getElementById('sidebar_message').innerHTML = 'Loading...'; get_stations();" value="Refresh stations"/>
    </div>
    <div class="info">
        <?php echo nl2br($info_message); ?>
    </div>
    <div id="sidebar_message">
    </div>
</div>

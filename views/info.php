<?php
defined('_IPSSP') or die;
?>

<h2>Info</h2>
<div class="left">
    <div class="forms">
        <form target="_parent" method="post">
            Show all station info:
            <input type="button" onClick="document.getElementById('info_message').innerHTML = 'Loading...'; show_stations();" value="Show"/> <br>
        </form>
        Show station info:<br>
        <form name="info_form" target="_parent" method="post">
            <div class="stations"></div>
        </form>
    </div>
</div>
<div class="main">
    <div id="info_message" class="message">
        <?php echo nl2br($message); ?>
    </div>
</div>

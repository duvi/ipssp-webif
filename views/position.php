<?php
defined('_IPSSP') or die;
?>

<h2>Position</h2>
<div class="left">
    <div class="forms">
        <form target="_parent" method="post">
            Load positions<br>
            <input type="hidden" name="command" value="load_sql" />
            Session:
            <select name="mapname" onChange="get_folders();" id="map_select">
                <?php echo $sessions; ?>
            </select>
            <br>
            Station:
            <select name="macname" id="map_select2">
            </select>
            <br>
            <input type="submit" value="OK" id="load_pos_ok" style="visibility:hidden;" /> <br>
            <br>
            <input type="hidden" name="tab" value="pos" />
        </form>
<!--
        <form target="_parent" method="post">
            Clear positions
            <input type="hidden" name="command" value="clear_map" />
            <input type="submit" value="OK" /> <br>
            <input type="hidden" name="tab" value="pos" />
        </form>
        <form target="_parent" method="post">
            <select name="mapname" >
            ' . $maps . '
            </select>
            <input type="submit" name="command" value="load_map" /> <br>
            <input type="hidden" name="tab" value="pos" />
        </form>
        <form target="_parent" method="post">
            <input type="text" name="mapname" /> <br>
            <input type="submit" name="command" value="save_map" /> <br>
            <input type="hidden" name="tab" value="pos" />
        </form>
-->
<?php if ($positions_select) : ?>
        <form name="pos_form" target="_parent" method="post">
            <input type="text" name="pos" size="4" />
            <br>
<!--
            <input type="submit" name="command" value="del_pos" /> <br>
            <input type="hidden" name="tab" value="pos" />
-->
        </form>
<?php endif; ?>
    </div>
    <div class="info">
        <?php echo nl2br($info_message); ?>
    </div>
</div>
<div class="main" >
    <div id="positions" class="map" style="background-image:url('<?php echo $mapfile; ?>'); width:<?php echo $imagesize[0]; ?>px; height:<?php echo $imagesize[1]; ?>px;">
<?php foreach ($positions as $position) : ?>
        <img src="img/rotpunkt.png" title="<?php echo $position["name"]; ?>" onClick="document.getElementById('pos_message').innerHTML = 'Loading...'; show_pos(<?php echo $position["name"]; ?>);" style="position:absolute;float:none;z-index:2;opacity:0.8;cursor:pointer;left:<?php echo $position["x"]-5; ?>px;top:<?php echo $position["y"]-5; ?>px;">
<?php endforeach; ?>
    </div>
    <div id="pos_message" class="message">
        <?php echo nl2br($message); ?>
    </div>
</div>

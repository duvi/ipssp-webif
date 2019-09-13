<?php
defined('_IPSSP') or die;
?>

<h2>Position</h2>
<div class="left">
    <div class="forms">
        <form target="_parent" method="post">
            Show all position info:<br>
            <input type="button" onClick="document.getElementById('pos_message').innerHTML = 'Loading...'; show_positions();" value="Show"/> <br>
        </form>
        <form target="_parent" method="post">
            Load positions<br>
            <input type="hidden" name="command" value="load_sql" />
            <input type="hidden" name="tab" value="pos" />
            Session:
            <select name="mapname" onChange="get_folders();" id="map_select">
                <?php echo $sessions; ?>
            </select>
            <br>
            Station:
            <select name="macname" id="map_select2">
            </select>
            <br>
            <input type="submit" value="Load" id="load_pos_ok" style="visibility:hidden;" />
        </form>
        <form target="_parent" method="post">
            Clear map<br>
            <input type="hidden" name="command" value="clear_map" />
            <input type="hidden" name="tab" value="pos" />
            <input type="submit" value="Clear" />
        </form>
<?php if ($maps) : ?>
        <form target="_parent" method="post">
            Load map<br>
            <input type="hidden" name="command" value="load_map" />
            <input type="hidden" name="tab" value="pos" />
            <select name="mapname" >
                <?php echo $maps; ?>
            </select>
            <br>
            <input type="submit" value="Load" />
        </form>
<?php endif; ?>
        <form target="_parent" method="post">
            Save map<br>
            <input type="hidden" name="command" value="save_map" />
            <input type="hidden" name="tab" value="pos" />
            <input type="text" name="mapname" />
            <br>
            <input type="submit" value="Save" />
        </form>
<?php if ($positions_select) : ?>
        <form name="pos_form" target="_parent" method="post">
            Delete position<br>
            <input type="hidden" name="command" value="del_pos" />
            <input type="hidden" name="tab" value="pos" />
            <input type="text" name="pos" size="4" />
            <br>
            <input type="submit" value="Delete" />
        </form>
<?php endif; ?>
    </div>
</div>
<div class="main" >
    <div id="positions" class="map" style="background-image:url('<?php echo $mapfile; ?>'); width:<?php echo $imagesize[0]; ?>px; height:<?php echo $imagesize[1]; ?>px;">
<?php foreach ($positions as $position) : ?>
        <img src="img/rotpunkt.png" title="<?php echo $position["name"]; ?>" onClick="document.getElementById('pos_message').innerHTML = 'Loading...'; show_position(<?php echo $position["name"]; ?>);" style="position:absolute;float:none;z-index:2;opacity:0.8;cursor:pointer;left:<?php echo $position["x"]-5; ?>px;top:<?php echo $position["y"]-5; ?>px;">
<?php endforeach; ?>
    </div>
    <div id="pos_message" class="message">
        <?php echo nl2br($message); ?>
    </div>
</div>

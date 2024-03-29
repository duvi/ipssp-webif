<?php
defined('_IPSSP') or die;
?>

<h2>Areas</h2>
<div class="left">
</div>
<div class="main">
    <div id="area_map" class="map" style="background-image:url('<?php echo $mapfile; ?>'); width:<?php echo $imagesize[0]; ?>px; height:<?php echo $imagesize[1]; ?>px;">
        <svg id="area_svg" width="<?php echo $imagesize[0]; ?>" height="<?php echo $imagesize[1]; ?>" style="position: absolute;">
<?php foreach ($areas as $area) : ?>
    <?php
    $points = "";
    foreach ($area["polygon"] as $poligons) {
        $points .= implode(",", $poligons) . " ";
    }
    $onclick = "document.getElementById('area_message').innerHTML = 'Loading...'; show_area(" . $area["id"] . ")";
    echo '<polygon points="' . $points . '" fill="red" stroke="black" fill-opacity="0.6" onClick="' . $onclick . '">';
    echo '<title>' . $area["name"] . '</title>';
    echo '</polygon>';
    ?>
<?php endforeach; ?>
        </svg>
    </div>
    <div id="area_message" class="message">
        <?php echo nl2br($message); ?>
    </div>
</div>

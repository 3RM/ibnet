<?php
use tugmaks\GoogleMaps\Map;
?>

		<?php if(!empty($loc)) {
			echo '<div class="map">';
			echo Map::widget([
		  		'zoom' => 16,
		  		'center' => $loc,
		  		'width' => 80,
		  		'height' => 300,
		  		'widthUnits' => 'UNITS_PERCENT',
		  		'markers' => [['position' => $loc],],
		  	]);
		  	echo '</div>';
		} ?>
<?php
error_reporting(0);

include_once 'AlphaHue/vendor/autoload.php'; // Path to autoload.php file.

$bridge_hostname = '192.168.1.7';
$bridge_username = 'HE1PHw5f3yBFztLniGleiPd4nH-lykybMSHF2mEm';

$hue = new \AlphaHue\AlphaHue($bridge_hostname, $bridge_username);

$sensors = $hue->getSensors();

$buttonevent = $sensors[3]['state']['buttonevent'];
$lastupdated = $sensors[3]['state']['lastupdated'];

$dimmer = array( lastupdated =>  $lastupdated, buttonevent => $buttonevent );

echo json_encode( $dimmer );

/*
if ( $buttonevent == 4002 ) {
	echo 'Lamp is uit!';
} elseif ( $buttonevent == 1002 ) {
	echo 'Lamp is aan!';
} elseif ( $buttonevent == 2002 ) {
	echo 'Omhoog!';
} elseif ( $buttonevent == 3002 ) {
	echo 'Omlaag!';
}*/

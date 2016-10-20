<?php
require( "sonos.class.php" );

$slaapkamer = new SonosPHPController( '192.168.1.11' );

if ( isset ( $_GET['command'] ) ) {
	if ( $_GET['command'] == 'play' ) {
		$slaapkamer = $slaapkamer -> Play();
	} else if ( $_GET['command'] == 'stop' ) {
		$slaapkamer = $slaapkamer -> Stop();
	} else if ( $_GET['command'] == 'up' ) {
		$volume = $slaapkamer -> GetVolume();
		$slaapkamer = $slaapkamer -> SetVolume( $volume + 1 );
	} else if ( $_GET['command'] == 'down' ) {
		$volume = $slaapkamer -> GetVolume();
		$slaapkamer = $slaapkamer -> SetVolume( $volume - 1 );
	}
}

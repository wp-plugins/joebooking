<?php
$conf =& ntsConf::getInstance();
$firstTimeSplash = $conf->get('firstTimeSplash');

$pref = 'http';
if( strlen($firstTimeSplash) && (substr($firstTimeSplash, 0, strlen($pref)) == $pref) ){
	setcookie( 'ntsFirstTimeSplash', 1, time() + 365*24*60*60 );
	$forwardTo = $firstTimeSplash;
	ntsView::redirect( $forwardTo );
	exit;
	}
?>
<?php
require( dirname(__FILE__) . '/_a_init.php' );

$start = $_NTS['REQ']->getParam('start');
$end = $_NTS['REQ']->getParam('end');
$forwardTo = ntsLink::makeLink( 
	'-current-', 
	'', 
	array(
		'start' => $start,
		'end' => $end
		)
	);
ntsView::redirect( $forwardTo );
?>
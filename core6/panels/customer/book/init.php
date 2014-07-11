<?php
$keep = array(
	'location',
	'resource',
	'service',
	'cal',
	'time'
	);

reset( $keep );
foreach( $keep as $k )
{
	$params[$k] = $_NTS['REQ']->getParam($k);
}

ntsView::setPersistentParams($params, 'customer/book' );

include_once( dirname(__FILE__) . '/_init_tm.php' );
?>
<?php
$conf =& ntsConf::getInstance();
$auto_resource = $conf->get('autoResource');
$auto_location = $conf->get('autoLocation');

$keep = array(
	// 'location',
	// 'resource',
	'service',
	'cal',
	'time',
	'asset',
	);

if( ! $auto_location ){
	$keep[] = 'location';
}
if( ! $auto_resource ){
	$keep[] = 'resource';
}

reset( $keep );
foreach( $keep as $k )
{
	$params[$k] = $_NTS['REQ']->getParam($k);
}

ntsView::setPersistentParams($params, 'customer/book' );
include_once( dirname(__FILE__) . '/_init_tm.php' );
?>
<?php
$keep = array(
	'location',
	'resource',
	'service',
	'cal',
	'time',
	'asset',
	);

reset( $keep );
foreach( $keep as $k )
{
	$params[$k] = $_NTS['REQ']->getParam($k);
}

ntsView::setPersistentParams($params, 'customer/book' );

$conf =& ntsConf::getInstance();
$auto_resource = $conf->get('autoResource');
$auto_location = $conf->get('autoLocation');

include_once( dirname(__FILE__) . '/_init_tm.php' );
?>
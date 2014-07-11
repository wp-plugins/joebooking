<?php
$ntsdb =& dbWrapper::getInstance();
$plm =& ntsPluginManager::getInstance();

$plugin = $_NTS['REQ']->getParam( 'plugin' );
$result = $plm->pluginActivate( $plugin );
if( $result ){
	ntsView::setAnnounce( M('Plugin') . ': ' . M('Install') . ': ' . M('OK'), 'ok' );
/* continue */
	$forwardTo = ntsLink::makeLink( '-current-' );
	ntsView::redirect( $forwardTo );
	exit;
	}
else {
	echo '<BR>Database error:<BR>' . $ntsdb->getError() . '<BR>';
	}
?>
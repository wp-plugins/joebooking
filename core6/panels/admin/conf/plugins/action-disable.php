<?php
$ntsdb =& dbWrapper::getInstance();
$plm =& ntsPluginManager::getInstance();

$plugin = $_NTS['REQ']->getParam( 'plugin' );
$result = $plm->pluginDisable( $plugin );
if( $result ){
	ntsView::setAnnounce( M('Plugin') . ': ' . M('Uninstall') . ': ' . M('OK'), 'ok' );
/* continue */
	$forwardTo = ntsLink::makeLink( '-current-' );
	ntsView::redirect( $forwardTo );
	exit;
	}
else {
	echo '<BR>Database error:<BR>' . $ntsdb->getError() . '<BR>';
	}

?>
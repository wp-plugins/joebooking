<?php
$ntsdb =& dbWrapper::getInstance();

$tables = $ntsdb->getTablesInDatabase();
reset( $tables );
foreach( $tables as $t ){
	$sql = "DROP TABLE {PRFX}$t";
	$ntsdb->runQuery( $sql );
	}
ntsView::setAnnounce( M('Uninstall') . ': ' . M('OK'), 'ok' );

$forwardTo = ntsLink::makeLink();
ntsView::redirect( $forwardTo );
exit;
?>
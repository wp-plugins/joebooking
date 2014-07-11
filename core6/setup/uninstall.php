<?php
$ntsdb =& dbWrapper::getInstance();
$tables = $ntsdb->getTablesInDatabase();
reset( $tables );
foreach( $tables as $t ){
	$sql = "DROP TABLE {PRFX}$t";
	if( ! $ntsdb->runQuery($sql) ){
		echo '<br><b>MySQL error!</b>: ' . $wrapper->getError();
		exit;
		}
	}
?>
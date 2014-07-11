<?php
$ntsdb =& dbWrapper::getInstance();

$sqlFile1 = NTS_APP_DIR . '/model/db.sql';
$sql = join( '', file($sqlFile1) );

$sqlFile2 = NTS_APP_DIR . '/model/db.sql';
if( file_exists($sqlFile2) ){
	$sql2 = join( '', file($sqlFile2) );
	$sql .= $sql2;
	}

$sqls = explode( ';', $sql );
reset( $sqls );
foreach( $sqls as $s ){
	$s = trim( $s );
	if( ! $s )
		continue;
	if( ! $ntsdb->runQuery($s) ){
		echo '<br><b>MySQL error!</b>: ' . $ntsdb->getError();
		exit;
		}
	}
?>
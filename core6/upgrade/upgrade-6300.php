<?php
$ntsdb =& dbWrapper::getInstance();

$columns = $ntsdb->getColumnsInTable('appointments');
if( ! isset($columns['duration_break']) ){
	$sql = "ALTER TABLE {PRFX}appointments ADD COLUMN `duration_break` int(11) NOT NULL DEFAULT 0";
	$result = $ntsdb->runQuery( $sql );

	$sql = "ALTER TABLE {PRFX}appointments ADD COLUMN `duration2` int(11) NOT NULL DEFAULT 0";
	$result = $ntsdb->runQuery( $sql );
}

$columns = $ntsdb->getColumnsInTable('services');
if( ! isset($columns['duration2']) ){
	$sql = "ALTER TABLE {PRFX}services ADD COLUMN `duration_break` int(11) NOT NULL DEFAULT 0";
	$result = $ntsdb->runQuery( $sql );

	$sql = "ALTER TABLE {PRFX}services ADD COLUMN `duration2` int(11) NOT NULL DEFAULT 0";
	$result = $ntsdb->runQuery( $sql );
}

$columns = $ntsdb->getColumnsInTable('timeblocks');
if( ! isset($columns['max_capacity']) ){
	$sql = "ALTER TABLE {PRFX}timeblocks ADD COLUMN `max_capacity` int(11) DEFAULT 1";
	$result = $ntsdb->runQuery( $sql );

	// $sql = "UPDATE {PRFX}timeblocks SET max_capacity = capacity";
	// $result = $ntsdb->runQuery( $sql );
}
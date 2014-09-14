<?php
$ntsdb =& dbWrapper::getInstance();

$columns = $ntsdb->getColumnsInTable('locations');
if( ! isset($columns['archive']) )
{
	$sql = "ALTER TABLE {PRFX}locations ADD COLUMN `archive` TINYINT NOT NULL DEFAULT 0";
	$result = $ntsdb->runQuery( $sql );
}

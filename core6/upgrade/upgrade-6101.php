<?php
$ntsdb =& dbWrapper::getInstance();

$columns = $ntsdb->getColumnsInTable('logaudit');
if( ! isset($columns['description']) )
{
	$sql = "ALTER TABLE {PRFX}logaudit ADD COLUMN `description` TEXT";
	$result = $ntsdb->runQuery( $sql );
}

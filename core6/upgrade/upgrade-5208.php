<?php
$ntsdb =& dbWrapper::getInstance();

/* make sure that we added columns in 5.2.6, damn */
$columns = $ntsdb->getColumnsInTable('services');

if( ! isset($columns['duration_max']) )
{
	/* add max duration for services */
	$sql = "ALTER TABLE {PRFX}services ADD COLUMN `duration_max` int(11) DEFAULT 1800";
	$result = $ntsdb->runQuery( $sql );
	$sql = "UPDATE {PRFX}services SET duration_max = duration";
	$result = $ntsdb->runQuery( $sql );
}

if( ! isset($columns['duration_increment']) )
{
	$sql = "ALTER TABLE {PRFX}services ADD COLUMN `duration_increment` int(11) DEFAULT 1800";
	$result = $ntsdb->runQuery( $sql );
	$sql = "UPDATE {PRFX}services SET duration_increment = duration";
	$result = $ntsdb->runQuery( $sql );
}

if( ! isset($columns['price_increment']) )
{
	$sql = "ALTER TABLE {PRFX}services ADD COLUMN `price_increment` VARCHAR(16) DEFAULT ''";
	$result = $ntsdb->runQuery( $sql );
	$sql = "UPDATE {PRFX}services SET price_increment = price";
	$result = $ntsdb->runQuery( $sql );
}

if( ! isset($columns['blocks_resource']) )
{
	$sql = "ALTER TABLE {PRFX}services ADD COLUMN `blocks_resource` TINYINT DEFAULT 0";
	$result = $ntsdb->runQuery( $sql );
}
?>
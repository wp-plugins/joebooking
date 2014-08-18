<?php
$ntsdb =& dbWrapper::getInstance();

$columns = $ntsdb->getColumnsInTable('timeblocks');
if( ! isset($columns['week_applied_on']) )
{
	/* add max duration for services */
	$sql = "ALTER TABLE {PRFX}timeblocks ADD COLUMN `week_applied_on` TINYINT DEFAULT 0";
	$result = $ntsdb->runQuery( $sql );
}

$columns = $ntsdb->getColumnsInTable('invoices');
if( ! isset($columns['customer_id']) )
{
	/* add customer id for invoices */
	$sql = "ALTER TABLE {PRFX}invoices ADD COLUMN `customer_id` int(11) DEFAULT 0";
	$result = $ntsdb->runQuery( $sql );
}

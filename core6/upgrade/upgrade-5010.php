<?php
ini_set( 'memory_limit', '128M' );
set_time_limit( 300 );

$ntsdb =& dbWrapper::getInstance();

$sql = 'DROP TABLE IF EXISTS {PRFX}packs';
$result = $ntsdb->runQuery( $sql );

$sql =<<<EOT

CREATE TABLE IF NOT EXISTS `{PRFX}packs` (
	`id` int(11) NOT NULL auto_increment,

	`service_id` int(11),
	`qty` int(11),
	`price` DOUBLE,
	`show_order` int(11) DEFAULT 1,
	PRIMARY KEY  (`id`)
	);
EOT;
$result = $ntsdb->runQuery( $sql );

/* convert invoices */
$sql =<<<EOT
UPDATE 
	`{PRFX}objectmeta`
SET
	meta_value = meta_data
WHERE
	meta_name = "_invoice" AND
	meta_data <> "" AND
	( (meta_value = 0) OR (meta_value = "") )
EOT;
$result = $ntsdb->runQuery( $sql );

$sql =<<<EOT
UPDATE 
	`{PRFX}objectmeta`
SET
	meta_data = ""
WHERE
	meta_name = "_invoice"
EOT;
$result = $ntsdb->runQuery( $sql );

/* reassign transactions from appointments back to invoices */
$sql =<<<EOT
SELECT
	meta1.obj_id AS app_id, meta1.meta_value AS trans_id, meta2.meta_value AS inv_id
FROM
	`{PRFX}objectmeta` AS meta1
INNER JOIN
	`{PRFX}objectmeta` AS meta2
ON
	meta2.meta_name = "_invoice" AND 
	meta2.obj_class = "appointment" AND
	meta2.obj_id = meta1.obj_id
WHERE
	meta1.meta_name = "_transaction" AND 
	meta1.obj_class = "appointment"
EOT;
$result = $ntsdb->runQuery( $sql );
$convert = array();
if( $result )
{
	while( $i = $result->fetch() ){
		$convert[] = $i;
		}
}
reset( $convert );
foreach( $convert as $i ){
	$invId = $i['inv_id'];
	$transId = $i['trans_id'];
	$sql =<<<EOT
	UPDATE
		{PRFX}transactions
	SET
		invoice_id = $invId
	WHERE
		id = $transId
EOT;
	$result = $ntsdb->runQuery( $sql );
	}

/* clear assignment of transactions to appointments */
$sql =<<<EOT
DELETE FROM
	{PRFX}objectmeta
WHERE
	meta_name = "_transaction" AND
	obj_class = "appointment"
EOT;
$result = $ntsdb->runQuery( $sql );

/* alter transactions */
$sql =<<<EOT

ALTER TABLE 
	`{PRFX}transactions`
ADD COLUMN (
	`amount_net` DOUBLE,
	`pgateway` VARCHAR(32),
	`pgateway_ref` TEXT,
	`pgateway_response` TEXT
	)
EOT;
$result = $ntsdb->runQuery( $sql );

$sql =<<<EOT
UPDATE
	{PRFX}transactions, {PRFX}invoices
SET
	{PRFX}transactions.amount_net = {PRFX}invoices.amount_net,
	{PRFX}transactions.pgateway = {PRFX}invoices.pgateway,
	{PRFX}transactions.pgateway_ref = {PRFX}invoices.pgateway_ref,
	{PRFX}transactions.pgateway_response = {PRFX}invoices.pgateway_response
WHERE
	{PRFX}transactions.invoice_id = {PRFX}invoices.id
EOT;
$result = $ntsdb->runQuery( $sql );

$sql =<<<EOT
UPDATE
	{PRFX}transactions
SET
	pgateway_response = notes
WHERE
	pgateway_response = '' AND
	notes <> ''
EOT;
$result = $ntsdb->runQuery( $sql );

$sql =<<<EOT
UPDATE
	{PRFX}transactions
SET
	pgateway = 'offline'
WHERE
	pgateway = ''
EOT;
$result = $ntsdb->runQuery( $sql );

$sql =<<<EOT
UPDATE
	{PRFX}transactions
SET
	amount = amount_net
WHERE
	amount = 0 AND
	amount_net <> 0
EOT;
$result = $ntsdb->runQuery( $sql );

$sql = "ALTER TABLE {PRFX}transactions DROP COLUMN `notes`";
$result = $ntsdb->runQuery( $sql );

$sql = "ALTER TABLE {PRFX}invoices DROP COLUMN `amount_gross`";
$result = $ntsdb->runQuery( $sql );
$sql = "ALTER TABLE {PRFX}invoices DROP COLUMN `amount_net`";
$result = $ntsdb->runQuery( $sql );
$sql = "ALTER TABLE {PRFX}invoices DROP COLUMN `pgateway`";
$result = $ntsdb->runQuery( $sql );
$sql = "ALTER TABLE {PRFX}invoices DROP COLUMN `pgateway_ref`";
$result = $ntsdb->runQuery( $sql );
$sql = "ALTER TABLE {PRFX}invoices DROP COLUMN `pgateway_response`";
$result = $ntsdb->runQuery( $sql );

// new panels in permissions
$newPanels = array(
	'admin/company/services/packs'			=> 'admin/company/services/edit',
	'admin/payments/invoices'		=> 'admin/conf/payment_gateways',
	'admin/payments/transactions'	=> 'admin/conf/payment_gateways',
	'admin/conf/cron'						=> 'admin/conf/reminders',
	);

$newPermissions = array();
reset( $newPanels );
foreach( $newPanels as $np => $op ){
	$where = array(
		'obj_class'		=> array('=', 'user'),
		'meta_name'		=> array('=', '_disabled_panels'),
		'meta_value'	=> array('=', $op),
		);
	$result = $ntsdb->select( 'obj_id', 'objectmeta', $where );
	while( $i = $result->fetch() ){
		$newPermissions[] = array( 
			'obj_class'		=> 'user',
			'obj_id'		=> $i['obj_id'],
			'meta_name'		=> '_disabled_panels',
			'meta_value '	=> $np,
			);
		}
	}
reset( $newPermissions );
foreach( $newPermissions as $what ){
	$ntsdb->insert( 'objectmeta', $what );
	}
?>
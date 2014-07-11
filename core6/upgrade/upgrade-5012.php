<?php
$ntsdb =& dbWrapper::getInstance();

$sql = 'DROP TABLE IF EXISTS {PRFX}packs';
$result = $ntsdb->runQuery( $sql );

$sql =<<<EOT

CREATE TABLE IF NOT EXISTS `{PRFX}packs` (
	`id` int(11) NOT NULL auto_increment,

	`service_id` int(11),
	`location_id` int(11) NOT NULL DEFAULT 0,
	`resource_id` int(11) NOT NULL DEFAULT 0,

	`qty` int(11),
	`duration` int(11) NOT NULL DEFAULT 0,
	`amount` DOUBLE,

	`price` DOUBLE,
	`expires_in` varchar(32) NOT NULL DEFAULT '1 months',

	`show_order` int(11) DEFAULT 1,
	PRIMARY KEY  (`id`)
	);
EOT;
$result = $ntsdb->runQuery( $sql );

/* create orders */
$sql = 'DROP TABLE IF EXISTS {PRFX}orders';
$result = $ntsdb->runQuery( $sql );

$sql =<<<EOT

CREATE TABLE IF NOT EXISTS `{PRFX}orders` (
	`id` int(11) NOT NULL auto_increment,

	`created_at` int(11),
	`valid_from` int(11),
	`valid_to` int(11),
	`is_active` tinyint DEFAULT 1,

	`pack_id` int(11) NOT NULL,
	`customer_id` int(11) NOT NULL,

	`location_id` int(11) NOT NULL,
	`resource_id` int(11) NOT NULL,
	`service_id` int(11) NOT NULL,

	`qty` int(11) NOT NULL DEFAULT 0,
	`amount` DOUBLE NOT NULL DEFAULT 0,
	`duration` int(11) NOT NULL DEFAULT 0,

	PRIMARY KEY  (`id`)
	);
EOT;
$result = $ntsdb->runQuery( $sql );

$sql = "ALTER TABLE {PRFX}invoices DROP COLUMN `paid_at`";
$result = $ntsdb->runQuery( $sql );

$sql = "ALTER TABLE {PRFX}services ADD COLUMN `pack_only` TINYINT DEFAULT 0";
$result = $ntsdb->runQuery( $sql );

/* ok now populate our invoices from appointments */
$sql =<<<EOT

UPDATE
	{PRFX}invoices, {PRFX}objectmeta, {PRFX}appointments
SET
	{PRFX}invoices.due_at = {PRFX}appointments.starts_at,
	{PRFX}objectmeta.meta_data = {PRFX}invoices.amount
WHERE
	{PRFX}objectmeta.meta_value = {PRFX}invoices.id AND 
	{PRFX}appointments.id = {PRFX}objectmeta.obj_id AND 
	{PRFX}objectmeta.obj_class = 'appointment' AND
	{PRFX}objectmeta.meta_name = '_invoice'
EOT;
$result = $ntsdb->runQuery( $sql );

// new panels in permissions
$newPanels = array(
	'admin/payments/orders'	=> 'admin/customers/browse',
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

/* do refunds for cancelled and noshow apps */
$cancelledIds = array();
$where = array(
	array(
		'completed'	=> array('=', HA_STATUS_CANCELLED )
		),
	array(
		'completed'	=> array('=', HA_STATUS_NOSHOW )
		),
	);
$subQuery = $ntsdb->buildSelect( 'id', 'appointments', $where );

$what = array(
	'meta_data'	=> 0
	);
$where = array(
	'obj_id'	=> array( 'IN', '(' . $subQuery . ')', 2 ),
	'obj_class'	=> array('=', 'appointment'),
	'meta_name'	=> array('=', '_invoice'),
	);
$ntsdb->update( 'objectmeta', $what, $where );
?>
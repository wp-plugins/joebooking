<?php
ini_set( 'memory_limit', '256M' );
set_time_limit( 600 );

$ntsdb =& dbWrapper::getInstance();

/* DATABASE MODIFICATIONS */

/* no classes */
$sql = "ALTER TABLE {PRFX}services DROP COLUMN  `class_type`";
$result = $ntsdb->runQuery( $sql );

/* archive resources */
$sql = "ALTER TABLE {PRFX}resources ADD COLUMN `archive` TINYINT NOT NULL DEFAULT 0";
$result = $ntsdb->runQuery( $sql );

/* add group ref for apps */
$sql = "ALTER TABLE {PRFX}appointments ADD COLUMN `group_ref` VARCHAR(32) NOT NULL DEFAULT ''";
$result = $ntsdb->runQuery( $sql );

/* log audit */
$sql =<<<EOT
CREATE TABLE IF NOT EXISTS `{PRFX}logaudit` (
	`id` int(11) NOT NULL auto_increment,
	`user_id` INT(11) NOT NULL,
	`action_time` INT(11) NOT NULL,

	`obj_class` varchar(32) NOT NULL DEFAULT '',
	`obj_id` int(11) NOT NULL DEFAULT 0,

	`property_name` varchar(32) NOT NULL DEFAULT '',
	`old_value` TEXT DEFAULT '',
	PRIMARY KEY  (`id`)
	);
EOT;
$result = $ntsdb->runQuery( $sql );

/* invoice items */
$sql =<<<EOT
CREATE TABLE IF NOT EXISTS `{PRFX}invoice_items` (
	`id` int(11) NOT NULL auto_increment,
	`invoice_id` INT(11) NOT NULL,

	`amount` DECIMAL(11,2) NOT NULL DEFAULT 0,
	`qty` DECIMAL(7,2) NOT NULL DEFAULT 1,
	`taxable` int(1) NOT NULL DEFAULT 1,

	`title` TEXT DEFAULT '',
	`obj_class` varchar(32) NOT NULL DEFAULT '',
	`obj_id` int(11) NOT NULL DEFAULT 0,
	PRIMARY KEY  (`id`)
	);
EOT;
$result = $ntsdb->runQuery( $sql );

/* DROP COLUMNS IN TRANSACTIONS */
$sql = "ALTER TABLE {PRFX}transactions DROP COLUMN `from_account`";
$result = $ntsdb->runQuery( $sql );
$sql = "ALTER TABLE {PRFX}transactions DROP COLUMN `to_account`";
$result = $ntsdb->runQuery( $sql );

/* TABLES FOR ACCOUNTING */
$sql =<<<EOT
CREATE TABLE IF NOT EXISTS `{PRFX}accounting_journal` (
	`id` int(11) NOT NULL auto_increment,

	`created_at` int(11) NOT NULL,

	`obj_class` VARCHAR(32) NOT NULL,
	`obj_id` int(11) NOT NULL,
	`action` VARCHAR(32) NOT NULL,
	PRIMARY KEY  (`id`)
	);
EOT;
$result = $ntsdb->runQuery( $sql );

$sql =<<<EOT
CREATE TABLE IF NOT EXISTS `{PRFX}accounting_posting` (
	`id` int(11) NOT NULL auto_increment,
	`journal_id` int(11) NOT NULL,

	`expires_at` int(11) NOT NULL,
	`account_type` VARCHAR(32) NOT NULL,
	`account_id` int(11) NOT NULL,
	`asset_id` int(11) NOT NULL,
	`asset_value` DOUBLE NOT NULL DEFAULT 0,
	PRIMARY KEY  (`id`)
	);
EOT;
$result = $ntsdb->runQuery( $sql );

$sql =<<<EOT
CREATE TABLE IF NOT EXISTS `{PRFX}accounting_assets` (
	`id` int(11) NOT NULL auto_increment,
	`asset` TEXT,
	PRIMARY KEY  (`id`)
	);
EOT;
$result = $ntsdb->runQuery( $sql );

/* add asset_id and value */
$sql = "ALTER TABLE {PRFX}packs ADD COLUMN `asset_id` INT(11) NOT NULL DEFAULT 0";
$result = $ntsdb->runQuery( $sql );

$sql = "ALTER TABLE {PRFX}packs ADD COLUMN `asset_value` DOUBLE NOT NULL DEFAULT 0";
$result = $ntsdb->runQuery( $sql );

/* END OF DATABASE MODIFICATIONS */

/* CONVERT ADMINS */
$where = array(
	'obj_class'		=> array('=', 'user'),
	'meta_name'		=> array('=', '_disabled_panels'),
	'meta_value'	=> array('LIKE', 'admin/conf%'),
	);
$result = $ntsdb->select( 'DISTINCT(obj_id)', 'objectmeta', $where );
$staff_ids = array();
while( $i = $result->fetch() )
{
	if( $i['obj_id'] != ntsLib::getCurrentUserId() )
		$staff_ids[] = $i['obj_id'];
}

$cm =& ntsCommandManager::getInstance();

reset( $staff_ids );
foreach( $staff_ids as $staff_id )
{
	$staff = new ntsUser;
	$staff->setId( $staff_id );
	$staff->setProp( '_admin_level', 'staff' );
	$cm->runCommand( $staff, 'update' );
}
/* END OF CONVERT ADMINS */

/* INVOICE ITEMS */
$where = array(
	array(
		'meta_name'		=> array( '=', '_appointment' ),
		'obj_class'		=> array( '=', 'invoice' )
		),
	array(
		'meta_name'		=> array( '=', '_order' ),
		'obj_class'		=> array( '=', 'invoice' )
		)
	);
$result = $ntsdb->select( array('meta_value', 'meta_name', 'meta_data', 'obj_id'), 'objectmeta', $where );
if( $result )
{
	$new_invoice_items = array();
	while( $pInfo = $result->fetch() )
	{
		$item_class = substr( $pInfo['meta_name'], 1 );
		$item_id = $pInfo['meta_value'];
		$amount = $pInfo['meta_data'];
		$invoice_id = $pInfo['obj_id'];

		$item = array(
			'invoice_id'	=> $invoice_id,
			'amount'		=> $amount,
			'qty'			=> 1,
			'taxable'		=> 1,
			'title'			=> '',
			'obj_class'		=> $item_class,
			'obj_id'		=> $item_id,
			);
		$new_invoice_items[] = $item;

		if( count($new_invoice_items) > 200 )
		{
			$ntsdb->insert_multiple( 'invoice_items', $new_invoice_items );
			$new_invoice_items = array();
		}
	}
}

if( $new_invoice_items )
{
	$ntsdb->insert_multiple( 'invoice_items', $new_invoice_items );
	$new_invoice_items = array();
}
/* END OF INVOICE ITEMS */

/* DELETE INVOICE ITEMS FROM OBJECT META */
$where = array(
	array(
		'meta_name'		=> array( '=', '_appointment' ),
		'obj_class'		=> array( '=', 'invoice' ),
		),
	array(
		'meta_name'		=> array( '=', '_order' ),
		'obj_class'		=> array( '=', 'invoice' ),
		)
	);
$result = $ntsdb->delete( 'objectmeta', $where );
/* END OF DELETE INVOICE ITEMS FROM OBJECT META */

/* DISCOUNTS */
$app_discounts = array();
$where = array(
	'meta_name'		=> array( '=', '_discount' ),
	'obj_class'		=> array( '=', 'invoice' ),
	);
$result = $ntsdb->select( array('meta_value', 'meta_data'), 'objectmeta', $where );
if( $result )
{
	while( $pInfo = $result->fetch() )
	{
		list( $child_type, $child_id ) = explode( ':', $pInfo['meta_value'] );
		switch( $child_type )
		{
			case 'appointment':
				$app_discounts[ $child_id ] = $pInfo['meta_data'];
				break;
		}
	}
}

if( $app_discounts )
{
	reset( $app_discounts );
	foreach( $app_discounts as $app_id => $app_discount )
	{
		$object = ntsObjectFactory::get( 'appointment' );
		$object->setId( $app_id );
		$params = array(
			'discount'		=> $app_discount,
			'created_at'	=> $object->getProp('created_at') + 1,
			);
		$cm->runCommand( $object, 'discount', $params );
	}
}
/* END OF DISCOUNTS */

$aam =& ntsAccountingAssetManager::getInstance();
$am =& ntsAccountingManager::getInstance();

/* CONVERT PACKS */
$packs = $ntsdb->get_select( '*', 'packs' );
foreach( $packs as $p )
{
	$asset = (isset($p['rule']) && $p['rule']) ? unserialize($p['rule']) : array();
	$asset = array_merge( $asset, $p );

	$asset_id = $aam->get_asset_id( $asset );
	$asset = $aam->get_asset_by_id( $asset_id );

	$type = $asset['type'];
	if( $type == 'unlimited' )
	{
		$asset_value = 1;
	}
	else
	{
		$asset_value = isset( $p[$type] ) ? $p[$type] : 0;
	}

	$where = array(
		'id'	=> array('=', $p['id'])
		);
	$what = array(
		'asset_id'		=> $asset_id,
		'asset_value'	=> $asset_value,
		);
	$ntsdb->update( 'packs', $what, $where );
}

$sql = "ALTER TABLE {PRFX}packs DROP COLUMN `service_id`, DROP COLUMN `location_id`, DROP COLUMN `resource_id`, DROP COLUMN `qty`, DROP COLUMN `duration`, DROP COLUMN `amount`, DROP COLUMN `rule`";
$result = $ntsdb->runQuery( $sql );
/* END OF CONVERT PACKS */

/* ADD ACCOUNTING FOR APPOINTMENTS */
$result = $ntsdb->select( array('id','price','resource_id','created_at'), 'appointments' );
if( $result )
{
	while( $a = $result->fetch() )
	{
		$app = ntsObjectFactory::get( 'appointment' );
		$app->setByArray( $a );
		$am->add( 'appointment::create', $app, array() );
	}
}

/*
CHECK OUT HOW TO OPTIMIZE TRANSACTIONS ACCOUNTING 
*/

/* ADD ACCOUNTING FOR TRANSACTIONS */
$result = $ntsdb->select( '*', 'transactions' );
if( $result )
{
	while( $a = $result->fetch() )
	{
		$tra = ntsObjectFactory::get( 'transaction' );
		$tra->setByArray( $a );
		$am->add( 'transaction::create', $tra );
	}
}

/* ADD REFUND FOR CANCELLED APPS */
$where = array(
	'completed'	=> array('=', HA_STATUS_CANCELLED),
	);
$result = $ntsdb->select( array('id','price','resource_id','created_at'), 'appointments', $where );
if( $result )
{
	while( $a = $result->fetch() )
	{
		$app = ntsObjectFactory::get( 'appointment' );
		$app->setByArray( $a );
		$am->add( 'appointment::cancel', $app );
	}
}

/* CONVERT ORDERS */
$orders_data = array();
$orders = $ntsdb->get_select( '*', 'orders' );
foreach( $orders as $o )
{
	$asset = (isset($o['rule']) && $o['rule']) ? unserialize($o['rule']) : array();
	$asset = array_merge( $asset, $o );

	$asset_id = $aam->get_asset_id( $asset );
	$asset = $aam->get_asset_by_id( $asset_id );

	$type = $asset['type'];
	$asset_value = isset( $o[$type] ) ? $o[$type] : 0;

	$o['asset_id'] = $asset_id;
	$o['asset_value'] = $asset_value;
	$o['type'] = $type;
	$orders_data[ $o['id'] ] = $o;

	if( ! $o['is_active'] )
		continue;

	$params = array();
	if( isset($o['valid_to']) && $o['valid_to'] )
		$params['expires_at'] = $o['valid_to'];

	$order = ntsObjectFactory::get( 'order' );
	$order->setByArray( $o );
	$am->add( 'order::request', $order, $params );
}

/* FUND APPOINTMENTS BY ORDERS */
$where = array(
	'meta_name'		=> array( '=', '_order' ),
	'obj_class'		=> array( '=', 'appointment' ),
	);

$join = array(
	array(
		'appointments',
		array(
			'appointments.id'	=> array( '=', 'objectmeta.obj_id', TRUE )
			)
		),
	array(
		'orders',
		array(
			'orders.id'	=> array( '=', 'objectmeta.meta_value', TRUE )
			)
		),
	);

$funds = $ntsdb->get_select( 
	array(
		'{PRFX}appointments.duration',
		'{PRFX}appointments.service_id',
		'price',
		'{PRFX}appointments.created_at',
		'{PRFX}appointments.customer_id',
		'obj_id',
		'meta_value',
		'{PRFX}orders.valid_to'
		),
	'objectmeta',
	$where,
	'',
	$join
	);

foreach( $funds as $f )
{
	$order = $orders_data[ $f['meta_value'] ];
	$asset_id = $order['asset_id'];

	// calculate the asset value
	// qty|duration|amount|unlimited
	switch( $order['type'] )
	{
		case 'qty':
		case 'unlimited':
			$asset_value = 1;
			break;
		case 'fixed':
			$asset_value = $f['service_id'];
			break;
		case 'duration':
			$asset_value = $f['duration'];
			break;
		case 'amount':
			$asset_value = $f['price'];
			break;
	}

	$params = array(
		'asset_id'		=> $asset_id,
		'asset_value'	=> $asset_value
		);
	if( isset($f['valid_to']) && $f['valid_to'] )
	{
		$params['expires_at'] = $f['valid_to'];
	}

	$a = ntsObjectFactory::get( 'appointment' );
	$f['id'] = $f['obj_id'];
	$a->setByArray( $f );

	$am->add( 'appointment::fund', $a, $params );
}

/* CLEAR ORDERS */
$sql = "ALTER TABLE {PRFX}orders DROP COLUMN `service_id`, DROP COLUMN `location_id`, DROP COLUMN `resource_id`, DROP COLUMN `qty`, DROP COLUMN `duration`, DROP COLUMN `amount`, DROP COLUMN `rule`, DROP COLUMN `valid_from`, DROP COLUMN `valid_to`";
$result = $ntsdb->runQuery( $sql );

$where = array(
	'meta_name'		=> array( '=', '_order' ),
	'obj_class'		=> array( '=', 'appointment' ),
	);
$result = $ntsdb->delete( 'objectmeta', $where );

/* APPLY PROMOTIONS */
$where = array(
	'meta_name'		=> array('=', '_promotion'),
	);

$promotions_use = $ntsdb->get_select(
	'*',
	'objectmeta',
	$where
	);

foreach( $promotions_use as $pu )
{
	$promo_id = $pu['meta_value'];
	$app_id = $pu['obj_id'];

	$app = ntsObjectFactory::get( 'appointment' );
	$app->setId( $app_id );
	$promotion = ntsObjectFactory::get( 'promotion' );
	$promotion->setId( $promo_id );

	$am->add( 
		'promotion::apply',
		$promotion,
		array(
			'appointment'	=> $app
			),
		$app->getProp('created_at')
		);
}
/* delete meta */
$where = array(
	'meta_name'		=> array( '=', '_promotion' ),
	);
$result = $ntsdb->delete( 'objectmeta', $where );


/* delete packs meta */
$where = array(
	array(
		'meta_name'		=> array( '=', '_pack' ),
		'obj_class'		=> array( '=', 'appointment' ),
		),
	);
$result = $ntsdb->delete( 'objectmeta', $where );

/* delete _disabled_panels */
$delete_meta = array( '_default_apps_view', '_disabled_panels', '_agenda_fields', '_default_calendar' );
foreach( $delete_meta as $dm )
{
	$where = array(
		array(
			'meta_name'		=> array( '=', $dm ),
			'obj_class'		=> array( '=', 'user' ),
			),
		);
	$result = $ntsdb->delete( 'objectmeta', $where );
}
return;
?>
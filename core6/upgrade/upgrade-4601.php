<?php
$ntsdb =& dbWrapper::getInstance();
$cm =& ntsCommandManager::getInstance();


$sql =<<<EOT

DROP TABLE IF EXISTS `{PRFX}orders`;

EOT;
$result = $ntsdb->runQuery( $sql );

$sql =<<<EOT

ALTER TABLE 
	`{PRFX}invoices`
ADD COLUMN (
	`paid_at` int(11),
	`due_at` int(11),
	`amount_gross` DOUBLE,
	`amount_net` DOUBLE,
	`pgateway` VARCHAR(32),
	`pgateway_ref` TEXT,
	`pgateway_response` TEXT
	)
EOT;

$result = $ntsdb->runQuery( $sql );

$sql =<<<EOT

CREATE TABLE IF NOT EXISTS `{PRFX}transactions` (
	`id` int(11) NOT NULL auto_increment,

	`from_account` int(11),
	`to_account` int(11),
	`amount` DOUBLE,
	`created_at` int(11),

	`invoice_id` int(11) NOT NULL,
	`notes` TEXT,

	PRIMARY KEY  (`id`)
	);
EOT;
$result = $ntsdb->runQuery( $sql );

$invoices = array();
$sql = "SELECT id, amount, created_at FROM {PRFX}invoices";
$result = $ntsdb->runQuery( $sql );
while( $r = $result->fetch() ){
	$invoices[] = array( $r['id'], $r['amount'], $r['created_at'] );
	}
reset( $invoices );
foreach( $invoices as $ia ){
	$iid = $ia[0];
	$sql = "SELECT * FROM {PRFX}payments WHERE invoice_id = $iid";
	$result = $ntsdb->runQuery( $sql );
	if( $i = $result->fetch() ){
		$paidAt = $i['paid_at'];
		$pgateway = $i['pgateway'];
		$amount_gross = $i['amount_gross'];
		$amount_net = $i['amount_net'];
		$pgateway_ref = $i['pgateway_ref'];
		$pgateway_response = $i['pgateway_response'];
	
		$sql2 =<<<EOT

UPDATE `{PRFX}invoices`
SET
	paid_at = $paidAt,
	due_at = $paidAt,
	pgateway = "$pgateway",
	amount_gross = $amount_gross,
	amount_net = $amount_net,
	pgateway_ref = "$pgateway_ref",
	pgateway_response = "$pgateway_response"
WHERE
	{PRFX}invoices.id = $iid
EOT;

		$result2 = $ntsdb->runQuery( $sql2 );

		$fromAccount = -1;
		$toAccount = 0;
		$sql3 =<<<EOT

INSERT INTO `{PRFX}transactions` 
( `from_account`,`to_account`,`amount`,`created_at`,`invoice_id`,`notes` ) VALUES 
($fromAccount, $toAccount, $amount_gross, $paidAt, $iid, '')

EOT;

		$result3 = $ntsdb->runQuery( $sql3 );
		$transId = $ntsdb->getInsertId();

		$appId = 0;
		$sql5 =<<<EOT

SELECT obj_id FROM `{PRFX}objectmeta` WHERE obj_class="obj_class" AND meta_name="_invoice" AND meta_value=$iid

EOT;
		$result5 = $ntsdb->runQuery( $sql5 );
		if( $result5 && ($r5 = $result5->fetch()) ){
			$appId = $r5['obj_id'];
			}
		if( $appId ){
			$sql4 =<<<EOT

INSERT INTO `{PRFX}objectmeta` 
(`obj_class`, `obj_id`, `meta_name`, `meta_value` ) VALUES 
('appointment', $appId, '_transaction', $transId)
EOT;

			$result4 = $ntsdb->runQuery( $sql4 );
			}
		}
	}

$sql = "DELETE FROM {PRFX}appointments WHERE is_ghost <> 0";
$result = $ntsdb->runQuery( $sql );

$sql = "ALTER TABLE {PRFX}appointments DROP COLUMN `is_ghost`";
$result = $ntsdb->runQuery( $sql );

$sql = "ALTER TABLE {PRFX}appointments DROP COLUMN `ghost_last_access`";
$result = $ntsdb->runQuery( $sql );

// alter admin permissions
$panelsChange = array(
//	array( 'admin/customers/browse',	M('View') ),
	array( 'admin/customers/newsletter',	'admin/promo/newsletter' ),
	array( 'admin/customers/settings',	'admin/conf/customers'),

	array( 'admin/resources/browse',	'admin/company/resources/browse' ),
	array( 'admin/resources/edit', 		'admin/company/resources/edit' ),
	array( 'admin/resources/create',	'admin/company/resources/create' ),

	array( 'admin/services/browse',		'admin/company/services/browse' ),
	array( 'admin/services/edit',		'admin/company/services/edit' ),
	array( 'admin/services/create',		'admin/company/services/create' ),
	array( 'admin/services/cats',		'admin/company/services/cats' ),

	array( 'admin/locations/browse',	'admin/company/locations/browse' ),
	array( 'admin/locations/edit', 		'admin/company/locations/edit' ),
	array( 'admin/locations/create',	'admin/company/locations/create' ),

	array( 'admin/staff/browse',	'admin/company/staff/browse' ),
	array( 'admin/staff/edit', 		'admin/company/staff/edit' ),
	array( 'admin/staff/create',	'admin/company/staff/create' ),
	
	);
reset( $panelsChange );
foreach( $panelsChange as $pch ){
	list( $from, $to ) = $pch;
	$sql = "UPDATE {PRFX}objectmeta SET meta_value='$to' WHERE meta_name='_disabled_panels' AND meta_value='$from'";
	$result = $ntsdb->runQuery( $sql );
	}
?>
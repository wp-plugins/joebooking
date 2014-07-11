<?php
$ntsdb =& dbWrapper::getInstance();

$sql = "ALTER TABLE {PRFX}services ADD COLUMN `prepay` VARCHAR(16) DEFAULT ''";
$result = $ntsdb->runQuery( $sql );

// now set prepay 100% if service has price and online gateway configured
$pgm =& ntsPaymentGatewaysManager::getInstance();
$allGateways = $pgm->getActiveGateways();
$enablePrepay = true;
if( (count($allGateways) == 1) && ($allGateways[0] == 'offline') ){
	$enablePrepay = false;
	}
if( $enablePrepay ){
	$sql =<<<EOT
UPDATE 
	`{PRFX}services`
SET
	prepay = '100%' 
WHERE
	price <> "" AND price > 0
EOT;
	$result = $ntsdb->runQuery( $sql );
	}
?>
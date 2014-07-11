<?php
$ntsdb =& dbWrapper::getInstance();

/* add max duration for services */
$sql = "ALTER TABLE {PRFX}services ADD COLUMN `duration_max` int(11) DEFAULT 1800";
$result = $ntsdb->runQuery( $sql );

$sql = "ALTER TABLE {PRFX}services ADD COLUMN `duration_increment` int(11) DEFAULT 1800";
$result = $ntsdb->runQuery( $sql );

$sql = "ALTER TABLE {PRFX}services ADD COLUMN `price_increment` VARCHAR(16) DEFAULT ''";
$result = $ntsdb->runQuery( $sql );

$sql = "ALTER TABLE {PRFX}services ADD COLUMN `blocks_resource` TINYINT DEFAULT 0";
$result = $ntsdb->runQuery( $sql );

$sql = "UPDATE {PRFX}services SET duration_max = duration";
$result = $ntsdb->runQuery( $sql );
$sql = "UPDATE {PRFX}services SET duration_increment = duration";
$result = $ntsdb->runQuery( $sql );
$sql = "UPDATE {PRFX}services SET price_increment = price";
$result = $ntsdb->runQuery( $sql );
?>
<?php
$ntsdb =& dbWrapper::getInstance();

/* alter service_id for packs */
$sql = "ALTER TABLE {PRFX}packs CHANGE COLUMN `service_id` `service_id` TEXT NOT NULL";
$result = $ntsdb->runQuery( $sql );

$sql = "ALTER TABLE {PRFX}orders CHANGE COLUMN `service_id` `service_id` TEXT NOT NULL";
$result = $ntsdb->runQuery( $sql );
?>
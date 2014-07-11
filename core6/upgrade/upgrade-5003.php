<?php
$ntsdb =& dbWrapper::getInstance();

/* add min and max from now for timeblocks */
$sql = "ALTER TABLE {PRFX}timeblocks ADD COLUMN `min_from_now` int(11) NOT NULL DEFAULT 10800";
$result = $ntsdb->runQuery( $sql );

$sql = "ALTER TABLE {PRFX}timeblocks ADD COLUMN `max_from_now` int(11) NOT NULL DEFAULT 4838400";
$result = $ntsdb->runQuery( $sql );

$sql = "ALTER TABLE {PRFX}services DROP COLUMN `min_from_now`";
$result = $ntsdb->runQuery( $sql );

$sql = "ALTER TABLE {PRFX}services DROP COLUMN `max_from_now`";
$result = $ntsdb->runQuery( $sql );
?>
<?php
$ntsdb =& dbWrapper::getInstance();

/* add rules for packs */
$sql = "ALTER TABLE {PRFX}packs ADD COLUMN `rule` TEXT";
$result = $ntsdb->runQuery( $sql );

$sql = "ALTER TABLE {PRFX}orders ADD COLUMN `rule` TEXT";
$result = $ntsdb->runQuery( $sql );

/* TABLE FOR PROMOTIONS */
$sql =<<<EOT
CREATE TABLE IF NOT EXISTS `{PRFX}promotions` (
	`id` int(11) NOT NULL auto_increment,
	`title` VARCHAR(255),
	`rule` TEXT,
	`price` TEXT,
	PRIMARY KEY  (`id`)
	);
EOT;
$result = $ntsdb->runQuery( $sql );

/* TABLE FOR COUPONS */
$sql =<<<EOT
CREATE TABLE IF NOT EXISTS `{PRFX}coupons` (
	`id` int(11) NOT NULL auto_increment,
	`code` TEXT,
	`use_limit` INT,
	`promotion_id` INT,
	PRIMARY KEY  (`id`)
	);
EOT;
$result = $ntsdb->runQuery( $sql );
?>
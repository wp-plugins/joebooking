<?php
$ntsdb =& dbWrapper::getInstance();

/* alter service_id for packs */
$sql = "ALTER TABLE {PRFX}packs CHANGE COLUMN `service_id` `service_id` TEXT NOT NULL";
$result = $ntsdb->runQuery( $sql );

$sql = "ALTER TABLE {PRFX}orders CHANGE COLUMN `service_id` `service_id` TEXT NOT NULL";
$result = $ntsdb->runQuery( $sql );

/* add title for packs */
$sql = "ALTER TABLE {PRFX}packs ADD COLUMN `title` VARCHAR(255)";
$result = $ntsdb->runQuery( $sql );

/* set default titles for packs */
$cm =& ntsCommandManager::getInstance();

$packs = ntsObjectFactory::getAll( 'pack' );
foreach( $packs as $pack )
{
	$title = ntsView::objectTitle($pack);
	$where = array(
		'id'	=> array( '=', $pack->getId() ),
		);
	$what = array(
		'title'	=> $title,
		);
	$ntsdb->update( 'packs', $what, $where );
}

/* add capacity for locations */
$sql = "ALTER TABLE {PRFX}locations ADD COLUMN `capacity` int(11) DEFAULT 0";
$result = $ntsdb->runQuery( $sql );
?>
<?php
$ntsdb =& dbWrapper::getInstance();
$cm =& ntsCommandManager::getInstance();
$conf =& ntsConf::getInstance();
$remindBefore = $conf->get( 'remindBefore' );

$now = time();

/* find apps that should be reminded at this run */
$where = array(
	'approved'						=> array('=', 1),
	'completed'						=> array('=', 0),
	'need_reminder'					=> array('<>', 0),
	"(starts_at - $remindBefore)"	=> array('<=', $now),
	'starts_at'						=> array('>', $now),
	);

$result = $ntsdb->select( 'id', 'appointments', $where, 'ORDER BY starts_at' );
if( $result ){
	while( $e = $result->fetch() ){
		$a = ntsObjectFactory::get( 'appointment' );
		$a->setId( $e['id'] );
		$cm->runCommand( $a, 'remind' );
		}
	}
?>
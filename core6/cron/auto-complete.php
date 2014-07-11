<?php
$conf =& ntsConf::getInstance();
$autoComplete = $conf->get('autoComplete');
if( ! $autoComplete )
	return;

$ntsdb =& dbWrapper::getInstance();
$cm =& ntsCommandManager::getInstance();
$now = time();

$where = array(
	'(starts_at + duration)'	=> array('<', ($now - $autoComplete)),
	'completed'					=> array('<', HA_STATUS_COMPLETED)
	);

$result = $ntsdb->select( 'id', 'appointments', $where );
if( $result )
{
	while( $e = $result->fetch() )
	{
		$a = ntsObjectFactory::get( 'appointment' );
		$a->setId( $e['id'] );
		$cm->runCommand( $a, 'complete' );
	}
}
?>
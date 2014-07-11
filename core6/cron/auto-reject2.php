<?php
$conf =& ntsConf::getInstance();
$autoReject = $conf->get('autoReject');
if( ! $autoReject )
	return;

$ntsdb =& dbWrapper::getInstance();
$cm =& ntsCommandManager::getInstance();
$now = time();

$where = array(
	'completed'		=> array('=', 0),
	'created_at'	=> array('<', ($now - $autoReject)),
	'approved'		=> array('=', 0)
	);

$result = $ntsdb->select( 'id', 'appointments', $where );
if( $result )
{
	$commandParams = array(
		'reason' => 'Not approved within ' . ntsTime::formatPeriod($autoReject),
		);
	while( $e = $result->fetch() )
	{
		$a = ntsObjectFactory::get( 'appointment' );
		$a->setId( $e['id'] );
		$cm->runCommand( $a, 'reject', $commandParams );
	}
}
?>
<?php
$ntsdb =& dbWrapper::getInstance();
$id = $NTS_CURRENT_USER->getId();

$t = $NTS_VIEW['t'];

$display = $_NTS['REQ']->getParam( 'display' );

$show = $_NTS['REQ']->getParam( 'show' );
if( ! $show )
	$show = 'upcoming';
$NTS_VIEW['show'] = $show;

list( $year, $month, $day ) = ntsTime::splitDate( $t->formatDate_Db() );
$t->setDateTime( $year, $month, $day, 0, 0, 0 );
$startToday = $t->getTimestamp();

if( $show == 'upcoming' )
{
/* entries */
	$sql =<<<EOT
	SELECT
		id
	FROM
		{PRFX}appointments
	WHERE
		customer_id = $id AND
		starts_at >= $startToday
	ORDER BY
		starts_at ASC
EOT;
}
elseif( $show == 'old' )
{
/* entries */
	$sql =<<<EOT
	SELECT
		id
	FROM
		{PRFX}appointments
	WHERE
		customer_id = $id AND
		starts_at < $startToday
	ORDER BY
		starts_at DESC
EOT;
}

$ids = array();
$result = $ntsdb->runQuery( $sql );
$NTS_VIEW['entries'] = array();
if( $result )
{
	while( $e = $result->fetch() )
	{
		$ids[] = $e['id'];
	}
}

ntsObjectFactory::preload( 'appointment', $ids );

$NTS_VIEW['entries'] = array();
foreach( $ids as $id )
{
	$a = ntsObjectFactory::get( 'appointment' );
	$a->setId( $id );
	$NTS_VIEW['entries'][] = $a;
}

$am =& ntsAccountingManager::getInstance();
$am->load_postings( 'appointment', $ids );

switch( $action ){
	case 'export':
		switch( $display ){
			case 'ical':
				$fileName = 'appointments-' . $t->formatDate_Db() . '.ics';
				ntsLib::startPushDownloadContent( $fileName, 'text/calendar' );
				require( dirname(__FILE__) . '/ical.php' );
				exit;
				break;
			case 'excel':
				$fileName = 'appointments-' . $t->formatDate_Db() . '.csv';
				ntsLib::startPushDownloadContent( $fileName );
				require( dirname(__FILE__) . '/excel.php' );
				exit;
				break;
			}
		break;
	default:
		break;
	}
?>
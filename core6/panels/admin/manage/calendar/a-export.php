<?php
require( dirname(__FILE__) . '/_a_init.php' );

/* get appointments */
$t->setDateDb( $start_date );
$periodStart = $t->getStartDay();
$t->setDateDb( $end_date );
$periodEnd = $t->getEndDay();

$tm2 = ntsLib::getVar( 'admin::tm2' );

$where = array(
	'(starts_at + duration + lead_out)'	=> array('>', $periodStart),
	'starts_at'							=> array('<', $periodEnd)
	);

$where['completed'] = array('>=', 0);
if( $locs )
{
	$where['location_id'] = array( 'IN', $locs );
}
if( $ress )
{
	$where['resource_id'] = array( 'IN', $ress );
}

/* get apps */
$ntsdb =& dbWrapper::getInstance();
$all_apps = $tm2->getAppointments( $where, 'ORDER BY starts_at ASC, id DESC' );

if( ! $all_apps )
{
	echo M('None');
	exit;
}

$labels = ntsAppointment::dump_labels();

reset( $all_apps );
$out = array();
$header = array();
$unset = array( 'lrst', 'is_class', 'customer:first_name', 'customer:last_name' );
foreach( $all_apps as $a )
{
	$app = ntsObjectFactory::get('appointment');
	$app->setId( $a['id'] );
//	$app->setByArray( $a );
	$v = $app->dump();

	reset( $unset );
	foreach( $unset as $u )
	{
		unset( $v[$u] );
	}

	if( ! $header )
	{
		$header = array_keys( $v );
		for( $ii = 0; $ii < count($header); $ii++ )
		{
			if( isset($labels[$header[$ii]]) )
				$header[$ii] = $labels[$header[$ii]];
		}
		$out[] = $header;
	}
	$out[] = $v;
}

$fileName = 'appointments-' . $t->formatDate_Db() . '.csv';
ntsLib::startPushDownloadContent( $fileName );

foreach( $out as $o )
{
	echo ntsLib::buildCsv( $o );
	echo "\n";
}
exit;
?>
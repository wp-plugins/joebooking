<?php
$tm2 = ntsLib::getVar('admin::tm2');
$t = $NTS_VIEW['t'];
$schView = ntsLib::getVar( 'admin/manage:schView' );
$ress_archive = ntsLib::getVar( 'admin::ress_archive' );
$locs_archive = ntsLib::getVar( 'admin::locs_archive' );

$ff =& ntsFormFactory::getInstance();

$formFile = dirname( __FILE__ ) . '/form';
$fParams = array();
$duplicate_form =& $ff->makeForm( $formFile, $fParams );

$cal = $_NTS['REQ']->getParam('cal');
if( ! $cal )
{
	$t->setNow();
	$cal = $t->formatDate_Db();
}

$tmBlocks = $tm2->getBlocks( $cal, true );

$blocks = array();
if( ! $cal )
{
	for( $di = 0; $di <= 6; $di++ )
	{
		$blocks[ $di ] = array();
	}
}
else
{
	$t->setDateDb( $cal );
	$di = $t->getWeekday();
	$blocks[ $di ] = array();
}

reset( $tmBlocks );
foreach( $tmBlocks as $b )
{
	if( ! in_array($b['resource_id'], $schView) )
		continue;

	if( in_array($b['resource_id'], $ress_archive) )
		continue;

	if( in_array($b['location_id'], $locs_archive) )
		continue;

	if( ! isset($blocks[$b['applied_on']]) )
		$blocks[$b['applied_on']] = array();

	$gid = $b['group_id'];
	if( ! isset($blocks[$b['applied_on']][$gid]) )
		$blocks[$b['applied_on']][$gid] = array();

	$now_active = ( ($cal >= $b['valid_from']) && ($cal <= $b['valid_to']) ) ? 1 : 0;
	$b['now_active'] = $now_active;
	$b['now_warning'] = 0;
	$blocks[$b['applied_on']][$gid][] = $b;
}

reset( $blocks );
if( $cal )
{
	$t->setDateDb( $cal );
	$di = $t->getWeekday();
	$dayStart = $t->getStartDay();
	$dayEnd = $t->getEndDay();
	if( ! isset($blocks[$di]) )
		$blocks[$di] = array();

	$toffs = $tm2->getTimeoff( $cal );

	$this_toffs = array();
	foreach( $toffs as $to )
	{
		if( ! in_array($to['resource_id'], $schView) )
			continue;
		if( in_array($to['resource_id'], $ress_archive) )
			continue;
		if( in_array($to['location_id'], $locs_archive) )
			continue;

		$t->setTimestamp( $to['starts_at'] );
		$to['valid_from'] = $t->formatDate_Db();
		$t->setTimestamp( $to['ends_at'] );
		$to['valid_to'] = $t->formatDate_Db();

		$to['starts_at'] =  ( $to['starts_at'] > $dayStart ) ? ($to['starts_at'] - $dayStart) : 0;
		$to['ends_at'] =  ( $to['ends_at'] > $dayEnd ) ? 24*60*60 : ($to['ends_at'] - $dayStart);
		$this_toffs[] = $to;
	}

	/* set timeblocks not active if timeoff overlaps */
	foreach( $blocks[$di] as $gid => $bg )
	{
		reset( $this_toffs );
		foreach( $this_toffs as $to )
		{
			$resource_id = $bg[0]['resource_id'];
			if( $to['resource_id'] != $resource_id )
				continue;

			if( 
				($to['starts_at'] <= $bg[0]['starts_at']) &&
				($to['ends_at'] >= $bg[0]['ends_at'])
			)
			{
				$blocks[$di][$gid][0]['now_active'] = 0;
			}
			elseif(
				1
			)
			{
				$blocks[$di][$gid][0]['now_warning'] = 1;
			}
		}
	}

	reset( $this_toffs );
	foreach( $this_toffs as $to )
	{
		$blocks[$di][] = $to;
	}
}

$sort_func = create_function(
	'$a, $b',
	'
	$compare_a = isset($a[0]) ? $a[0] : $a;
	$compare_b = isset($b[0]) ? $b[0] : $b;

	if( $compare_a["starts_at"] != $compare_b["starts_at"] )
	{
		$return = ($compare_a["starts_at"] - $compare_b["starts_at"]);
	}
	elseif( $compare_a["ends_at"] != $compare_b["ends_at"] )
	{
		$return = ($compare_b["ends_at"] - $compare_a["ends_at"]);
	}
	else
	{
		$return = ($compare_a["valid_from"] - $compare_b["valid_from"]);
	}
	return $return;
	'
	);

foreach( array_keys($blocks) as $di )
{
	uasort( $blocks[$di], $sort_func );
}

$view = array(
	'blocks'			=> $blocks,
	'duplicate_form'	=> $duplicate_form,
	'cal'				=> $cal
	);

$this->render(
	dirname(__FILE__) . '/index.php',
	$view
	);
?>
<?php
$current_filter = ntsLib::getVar( 'admin/manage:current_filter' );
$locs2 = ntsLib::getVar( 'admin::locs2' );
$ress2 = ntsLib::getVar( 'admin::ress2' );
$sers2 = ntsLib::getVar( 'admin::sers2' );

$all_labels = array( 'date', 'time', 'customer', 'location', 'resource', 'service', 'seats' );
$labels = array();

switch( $display )
{
	case 'calendar':
		switch( $range )
		{
			case 'week':
				$labels = array(
					array('time', 'customer'),
//					array('location', 'resource'),
					);
				break;

			case 'month':
				$labels = array(
					'time',
					);
				break;
		}
		break;

	case 'browse':
		$labels = array(
//			array('time', 'customer'),
//			array('customer', 'service'),
			'time',
			);

		if( $split_by == 'month' )
		{
			$labels = array(
				'date',
				);
		}
		else
		{
			$labels = array(
				'time',
				);
		}
		break;
}

/* parse */
if( ! in_array('date', $labels) )
{
	$all_labels = Hc_lib::remove_from_array( $all_labels, 'date' );
}
if( isset($customer_id) )
{
	$all_labels = Hc_lib::remove_from_array( $all_labels, 'customer' );
}
if( 
	( isset($current_filter['l']) && $current_filter['l'] ) OR
	(count($locs2) < 2)
	)
{
	$all_labels = Hc_lib::remove_from_array( $all_labels, 'location' );
}
if( 
	( isset($current_filter['r']) && $current_filter['r'] ) OR
	(count($ress2) < 2)
	)
{
	$all_labels = Hc_lib::remove_from_array( $all_labels, 'resource' );
}

/* parse main and add dropdown */
$labels['main'] = array();
$used_labels = array();
foreach( $labels as $l )
{
	if( is_array($l) )
	{
		$this_l = array();
		reset( $l );
		foreach( $l as $l2 )
		{
			if( ! in_array($l2, $all_labels) )
				continue;
			$this_l[] = $l2;
		}
		if( $this_l )
			$labels['main'][] = $this_l;
		$used_labels = array_merge($used_labels, $this_l);
	}
	else
	{
		if( ! in_array($l, $all_labels) )
			continue;
		if( $l )
			$labels['main'][] = $l;
		$used_labels[] = $l;
	}
}

$labels['dropdown'] = array();

foreach( $all_labels as $l )
{
	if( ! in_array($l, $used_labels) )
	{
		if( $l == 'customer' )
			$l = 'customer_link';
		$labels['dropdown'][] = $l;
	}
}

if( 
	in_array('customer', $all_labels)
	&&
	( ! in_array('customer_link', $labels['dropdown']) )
	)
{
	array_unshift( $labels['dropdown'], 'customer_link' );
}
?>
<?php
$ntsdb =& dbWrapper::getInstance();

/* convert timeblocks as there might be the same group id but different start/end time */
$select = $ntsdb->get_select(
	'DISTINCT(resource_id)',
	'timeblocks'
	);
$rids = array();
foreach( $select as $s )
{
	$rids[] = $s['resource_id'];
}

if( $rids )
{
	$new_gid = $ntsdb->get_select(
		'MAX(group_id)',
		'timeblocks'
		);
	$new_gid++;
}

reset( $rids );
foreach( $rids as $rid )
{
	$where = array(
		'resource_id'	=> array( '=', $rid ),
		);
	$gids = array();
	$select = $ntsdb->get_select(
		'DISTINCT(group_id)',
		'timeblocks',
		$where
		);
	foreach( $select as $s )
	{
		$gids[] = $s['group_id'];
	}

	reset( $gids );
	foreach( $gids as $gid )
	{
		$where = array(
			'resource_id'	=> array( '=', $rid ),
			'group_id'		=> array( '=', $gid ),
			);
		$starts = array();
		$select = $ntsdb->get_select(
			array('starts_at', 'ends_at'),
			'timeblocks',
			$where
			);
		foreach( $select as $s )
		{
			$k = $s['starts_at'] . '-' . $s['ends_at'];
			$starts[ $k ] = 1;
		}

		if( count($starts) > 1 )
		{
			$change_starts = array_keys($starts);
			array_shift( $change_starts );
			foreach( $change_starts as $cs )
			{
				$cs = explode( '-', $cs );
				$where = array(
					'resource_id'	=> array( '=', $rid ),
					'group_id'		=> array( '=', $gid ),
					'starts_at'		=> array( '=', $cs[0] ),
					'ends_at'		=> array( '=', $cs[1] ),
					);
				$set = array(
					'group_id'	=> $new_gid,
					);
				$ntsdb->update(
					'timeblocks',
					$set,
					$where
					);
				$new_gid++;
			}
		}
	}
}

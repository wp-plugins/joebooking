<?php
$mergeToId = isset($params['to']) ? $params['to'] : 0;
if( ! $mergeToId )
	return;
$myId = $object->getId();

$ntsdb =& dbWrapper::getInstance();

/* move appointments */
$ntsdb->update(
	'appointments',
	array(
		'service_id' => $mergeToId
		),
	array(
		'service_id' => array('=', $myId)
		)
	);

/* move timeblocks */
$myTimeblocks = array();
$myTimeblocks = $ntsdb->get_select( 
	'*',
	'timeblocks',
	array(
		'service_id' => array('=', $myId)
		)
	);

reset( $myTimeblocks );
foreach( $myTimeblocks as $tb )
{
/* if we have the same timeblock for the target service then just delete this one */
	if( 
		$other = $ntsdb->get_select(
			'*',
			'timeblocks',
			array(
				'starts_at'		=> array('=', $tb['starts_at']),
				'ends_at'		=> array('=', $tb['ends_at']),
				'applied_on'	=> array('=', $tb['applied_on']),
				'location_id'	=> array('=', $tb['location_id']),
				'resource_id'	=> array('=', $tb['resource_id']),
				'valid_from'	=> array('=', $tb['valid_from']),
				'valid_to'		=> array('=', $tb['valid_to']),
				'service_id'	=> array('=', $mergeToId),
				)
			)
		)
		{
			$ntsdb->delete(
				'timeblocks',
				array(
					'id' => array( '=', $tb['id'] )
					)
				);
		}
/* if overlapping then update those and delete this */
	elseif(
		$other = $ntsdb->get_select(
			'*',
			'timeblocks',
			array(
				'starts_at'		=> array('>=', $tb['ends_at']),
				'ends_at'		=> array('<=', $tb['starts_at']),

				'applied_on'	=> array('=', $tb['applied_on']),
				'location_id'	=> array('=', $tb['location_id']),
				'resource_id'	=> array('=', $tb['resource_id']),
				'valid_from'	=> array('=', $tb['valid_from']),
				'valid_to'		=> array('=', $tb['valid_to']),

				'service_id' => array('=', $mergeToId),
				)
			)
		)
		{
			reset( $other );
			foreach( $other as $oth )
			{
				$new_start = ( $oth['starts_at'] < $tb['starts_at'] ) ? $oth['starts_at'] : $tb['starts_at'];
				$new_end = ( $oth['ends_at'] > $tb['ends_at'] ) ? $oth['ends_at'] : $tb['ends_at'];

				$ntsdb->update(
					'timeblocks',
					array(
						'starts_at'	=> $new_start,
						'ends_at'	=> $new_end,
						),
					array(
						'id' => array( '=', $oth['id'] )
						)
					);
			}

			$ntsdb->delete(
				'timeblocks',
				array(
					'id' => array( '=', $tb['id'] )
					)
				);
		}
/* then just change the service for this timeblock */
	else
	{
		$ntsdb->update(
			'timeblocks',
			array(
				'service_id'	=> $mergeToId,
				),
			array(
				'id' => array( '=', $tb['id'] )
				)
			);
	}
}

/* check if I have any bundles */
$plm =& ntsPluginManager::getInstance();
$activePlugins = $plm->getActivePlugins();
if( in_array('bundles', $activePlugins) )
{
	$entries = ntsObjectFactory::getAll( 'bundle' );

	reset( $entries );
	foreach( $entries as $bundle ){
		$bServices = $bundle->getProp('services');
		$bServices = explode( '-', $bServices );

		$bundleServices = array();
		reset( $bServices );
		foreach( $bServices as $bsid )
			$bundleServices[$bsid] = 1;

		if( isset($bundleServices[$myId]) )
		{
			unset( $bundleServices[$myId] );
			$bundleServices[$mergeToId] = 1;
			$newBundleServices = array_keys( $bundleServices );
			if( count($newBundleServices) < 2 )
			{
				// delete
				$this->runCommand( $bundle, 'delete' );
			}
			else 
			{
				// update
				$newBundleServices = join( '-', $newBundleServices );
				$bundle->setProp( 'services', $newBundleServices );
				$this->runCommand( $bundle, 'update' );
			}
		}
	}
}

/* then just delete this service */
$this->runCommand( $object, 'delete' );
?>
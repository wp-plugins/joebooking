<?php
$ntsdb =& dbWrapper::getInstance();
$serviceId = $object->getId();

/* delete meta as child */
$childMetaClass = '_' . strtolower($className);
$result = $ntsdb->delete( 
	'objectmeta',
	array( 
		'meta_name'		=> array('=', '_assign_service_only'),
		'meta_value'	=> array('=', $serviceId),
		)
	);

/* delete timeblocks */
$sql =<<<EOT
DELETE FROM
	{PRFX}timeblocks
WHERE
	service_id = $serviceId
EOT;
$result = $ntsdb->runQuery( $sql );

$t = new ntsTime();
$today = $t->formatDate_Db();
$todayTimestamp = $t->timestampFromDbDate( $today );

/* reject appointments */
$sql =<<<EOT
SELECT
	id
FROM
	{PRFX}appointments
WHERE
	service_id = $serviceId
EOT;
$result = $ntsdb->runQuery( $sql );
if( $result ){
	while( $e = $result->fetch() ){
		$subId = $e['id'];
		$subObject = ntsObjectFactory::get( 'appointment' );
		$subObject->setId( $subId );

		$params = array(
			'reason' => 'Service no longer offered',
			);
	/* silent if app is earlier than today */
		if( $subObject->getProp('starts_at') < $todayTimestamp ){
			$params['_silent'] = true;
			}
		$this->runCommand( $subObject, 'reject', $params );
		}
	}

/* check if I have any bundles */
$plm =& ntsPluginManager::getInstance();
$activePlugins = $plm->getActivePlugins();
if( in_array('bundles', $activePlugins) ){
	$entries = ntsObjectFactory::getAll( 'bundle' );

	reset( $entries );
	foreach( $entries as $bundle ){
		$bServices = $bundle->getProp('services');
		$bServices = explode( '-', $bServices );

		$bundleServices = array();
		reset( $bServices );
		foreach( $bServices as $bsid )
			$bundleServices[$bsid] = 1;

		if( isset($bundleServices[$serviceId]) ){
			unset( $bundleServices[$serviceId] );
			$newBundleServices = array_keys( $bundleServices );
			if( count($newBundleServices) < 2 ){
				// delete
				$this->runCommand( $bundle, 'delete' );
				}
			else {
				// update
				$newBundleServices = join( '-', $newBundleServices );
				$bundle->setProp( 'services', $newBundleServices );
				$this->runCommand( $bundle, 'update' );
				}
			}
		}
	}
?>
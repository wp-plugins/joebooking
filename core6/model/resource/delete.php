<?php
$ntsdb =& dbWrapper::getInstance();
$objId = $object->getId();

$t = new ntsTime();
$today = $t->formatDate_Db();
$todayTimestamp = $t->timestampFromDbDate( $today );

/* delete _resource_apps */
$sql =<<<EOT
DELETE FROM
	{PRFX}objectmeta
WHERE
	meta_name = "_resource_apps" AND
	meta_value = $objId
EOT;
$result = $ntsdb->runQuery( $sql );

/* delete _resource_schedules */
$sql =<<<EOT
DELETE FROM
	{PRFX}objectmeta
WHERE
	meta_name = "_resource_schedules" AND
	meta_value = $objId
EOT;
$result = $ntsdb->runQuery( $sql );

/* delete timeblocks */
$sql =<<<EOT
DELETE FROM
	{PRFX}timeblocks
WHERE
	resource_id = $objId
EOT;
$result = $ntsdb->runQuery( $sql );

/* delete meta as child */
$childMetaClass = '_' . strtolower($className);
$result = $ntsdb->delete( 
	'objectmeta',
	array( 
		'meta_name'		=> array('=', '_assign_resource_only'),
		'meta_value'	=> array('=', $objId),
		)
	);

/* delete timeoffs */
$sql =<<<EOT
SELECT
	id
FROM
	{PRFX}timeoffs
WHERE
	resource_id = $objId
EOT;
$result = $ntsdb->runQuery( $sql );
if( $result ){
	while( $e = $result->fetch() ){
		$subId = $e['id'];
		$subObject = new ntsObject( 'timeoff' );
		$subObject->setId( $subId );
		$this->runCommand( $subObject, 'delete' );
		}
	}

/* reject appointments */
$sql =<<<EOT
SELECT
	id
FROM
	{PRFX}appointments
WHERE
	resource_id = $objId
EOT;
$result = $ntsdb->runQuery( $sql );
if( $result ){
	while( $e = $result->fetch() ){
		$subId = $e['id'];
		$subObject = ntsObjectFactory::get( 'appointment' );
		$subObject->setId( $subId );

		$params = array(
			'reason' => 'Resource closed',
			);
	/* silent if app is earlier than today */
		if( $subObject->getProp('starts_at') < $todayTimestamp ){
			$params['_silent'] = true;
			}
//		$this->runCommand( $subObject, 'reject', $params );
		$this->runCommand( $subObject, 'delete', $params );
		}
	}
?>
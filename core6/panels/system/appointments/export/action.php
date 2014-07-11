<?php
$_REQUEST[NTS_PARAM_ACTION] = 'export';
$userId = 0;
$authCode = $_NTS['REQ']->getParam( 'code' );

// find user by this code
$sql =<<<EOT
SELECT
	obj_id
FROM
	{PRFX}objectmeta
WHERE
	obj_class = "user" AND
	meta_name = "_auth_code" AND
	meta_value = "$authCode"
EOT;
$result = $ntsdb->runQuery( $sql );
if( $i = $result->fetch() ){
	$userId = $i['obj_id'];
	}

if( ! $userId ){
	echo "user not found";
	exit;
	}

$object = new ntsUser();
$object->setId( $userId );
if( $object->notFound() ){
	echo "user not found";
	exit;
	}

$where = array();

if( $object->hasRole('admin') ){
	$fixId = array();
	$appPermissions = $object->getAppointmentPermissions();
	reset( $appPermissions );
	foreach( $appPermissions as $resId => $acc ){
		if( $acc['view'] ){
			$fixId[] = $resId;
			}
		}
	if( $fixId )
		$where[] = 'resource_id IN (' . join(',', $fixId) . ')';
	else
		$where[] = '0';
	}
else {
	$where[] = "customer_id = $userId";
	}

$where[] = "completed = 0";

$now = time();
$offset = $now - 30 * 24 * 60 * 60;
$where[] = "starts_at > $offset";

/* add limit by resource and location if params supplied */
$location_id = $_NTS['REQ']->getParam( 'location' );
if( $location_id )
{
	$where[] = 'location_id = ' . $location_id;
}
$resource_id = $_NTS['REQ']->getParam( 'resource' );
if( $resource_id )
{
	$where[] = 'resource_id = ' . $resource_id;
}

$whereString = join( ' AND ', $where );

$sql =<<<EOT
SELECT
	id
FROM
	{PRFX}appointments
WHERE
	$whereString
ORDER BY
	starts_at ASC
LIMIT 6000
EOT;

include_once( NTS_APP_DIR . '/helpers/ical.php' );
$NTS_VIEW['ntsCal'] = new ntsIcal();

$result = $ntsdb->runQuery( $sql );
if( $result ){
	while( $e = $result->fetch() ){
		$NTS_VIEW['ntsCal']->addAppointment( $e['id'] );
		}
	}

if( ob_get_contents() )
	ob_end_clean();
?>
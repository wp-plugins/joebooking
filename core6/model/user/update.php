<?php
$uif =& ntsUserIntegratorFactory::getInstance();
$integrator =& $uif->getIntegrator();

$userId = $object->getId();

if( ntsLib::getCurrentUserId() == $userId ){
	if( isset($object->props['_calendar_field']) ){
		$calendarField = $object->props['_calendar_field'];
		if( $calendarField && (! headers_sent()) ){
			setcookie( 'nts_calendar_field', $calendarField, time() + 365*24*60*60 );
			}
		}
	}

list( $objectInfo, $metaInfo ) = $object->getByArray( true, true );

if( isset($metaInfo['new_password']) ){
	$newPassword = $metaInfo['new_password'];
	if( $newPassword ){
		$objectInfo['new_password'] = $newPassword;
		unset( $metaInfo['new_password'] );
		}
	}

$result = $integrator->updateUser( $userId, $objectInfo, $metaInfo );
if( ! $result ){
	$actionResult = 0;
	$actionError = $integrator->getError();
	$actionStop = 1;
	return;
	}

$skipMainTable = true;
?>
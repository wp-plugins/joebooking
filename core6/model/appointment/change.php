<?php
$changes = $object->getChanges();

/* change duration */
if( isset($changes['service_id']) )
{
	$new_service = ntsObjectFactory::get( 'service' );
	$new_service->setId( $object->getProp('service_id') );

	$duration = $object->getProp( 'duration' );
	$new_duration = $new_service->getProp( 'duration' );
	if( $new_duration != $duration )
	{
		$object->setProp( 'duration', $new_duration );
	}

	$lead_out = $object->getProp( 'lead_out' );
	$new_lead_out = $new_service->getProp( 'lead_out' );
	if( $new_lead_out != $lead_out )
	{
		$object->setProp( 'lead_out', $new_lead_out );
	}
}

$this->runCommand( $object, 'update' );
$actionResult = 1;
?>
<?php
for( $ii = 0; $ii < count($apps); $ii++ )
{
	$apps[$ii]['customer_id'] = $cid ? $cid : 0;

	$service = ntsObjectFactory::get('service');
	$service->setId( $apps[$ii]['service_id'] );

	if( 
		(! isset($apps[$ii]['duration'])) OR
		(! $apps[$ii]['duration'])
	)
	{
		$service = ntsObjectFactory::get('service');
		$service->setId( $apps[$ii]['service_id'] );
		$apps[$ii]['duration'] = $service->getProp('duration');
	}

	$apps[$ii]['lead_in'] = $service->getProp('lead_in');
	$apps[$ii]['lead_out'] = $service->getProp('lead_out');
	$apps[$ii]['seats'] = 1;
}
?>
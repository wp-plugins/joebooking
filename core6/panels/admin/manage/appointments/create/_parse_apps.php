<?php
for( $ii = 0; $ii < count($apps); $ii++ )
{
	$apps[$ii]['customer_id'] = $cid ? $cid : 0;

	$service = ntsObjectFactory::get('service');
	$service->setId( $apps[$ii]['service_id'] );

	if( 
		(! isset($apps[$ii]['duration'])) OR
		(! $apps[$ii]['duration'])
	){
		$apps[$ii]['duration'] = $service->getProp('duration');
	}

	if( 
		(! isset($apps[$ii]['duration_break']))
	){
		$apps[$ii]['duration_break'] = $service->getProp('duration_break');
	}

	if( 
		(! isset($apps[$ii]['duration2']))
	){
		$apps[$ii]['duration2'] = $service->getProp('duration2');
	}

	$apps[$ii]['lead_in'] = $service->getProp('lead_in');
	$apps[$ii]['lead_out'] = $service->getProp('lead_out');

	if( ! isset($apps[$ii]['seats']) ){
		$apps[$ii]['seats'] = 1;
	}
	$apps[$ii]['_id'] = -($ii+1);
}
?>
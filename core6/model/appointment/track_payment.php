<?php
$object->reset_accounting_postings();
$amount = $object->getPaidAmount();

if( $amount == 0 ){
	/* check if 100% coupon was applied */
	$full_price = $object->getFullCost();
	$due = $object->getDue();

	if( strlen($full_price) && (! $due) ){
		$amount = $full_price - $due;
		}
	}

if( $amount < 0 )
{
	$commandParams = array(
		'reason' => 'Refund',
		);
//	$this->runCommand( $object, 'cancel', $commandParams );
}
else
{
	$service = ntsObjectFactory::get( 'service' ); 
	$service->setId( $object->getProp( 'service_id' ) );
	$customerId = $object->getProp( 'customer_id' );
	$approvalRequired = $service->checkApproval( $customerId, $amount );

	// check if already approved
	$approved = $object->getProp( 'approved' );

	if( ($amount > 0) && (! $approved) && (! $approvalRequired) )
	{
		$this->runCommand( $object, 'request' );
	}
}
?>
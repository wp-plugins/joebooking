<?php
/* find invoices and if only it's only one item then also delete it */
$objectId = $object->getId();
$objectClassName = $object->getClassName();
$invoices = $object->getInvoices();

reset( $invoices );
foreach( $invoices as $ia )
{
	list( $invoiceId, $myNeededAmount, $due ) = $ia;
	$invoice = ntsObjectFactory::get( 'invoice' );
	$invoice->setId( $invoiceId );
	$items = $invoice->getItemsObjects();

	if( 
		(! count($items)) OR 
		( 
			(count($items) == 1) && 
			(is_object($items[0])) && 
			($items[0]->getClassName() == $objectClassName) && 
			($items[0]->getId() == $objectId)
		)
		)
	{
		$this->runCommand( $invoice, 'delete' );
	}
}
?>
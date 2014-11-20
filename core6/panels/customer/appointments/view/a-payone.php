<?php
$pm =& ntsPaymentManager::getInstance();

$id = $_NTS['REQ']->getParam('_id');
$app = ntsObjectFactory::get( 'appointment' );
$app->setId( $id );

$items = array( $app );
$amounts = array();
$amounts = 0;

if( $items )
{
	$now = time();
	$invoices = $pm->makeInvoices( $items, $amounts, $now );

	if( isset($invoices[0]) )
	{
		$invoice = $invoices[0];
		$refno = $invoice->getProp('refno');
		$forwardTo = ntsLink::makeLink( 'system/invoice', '', array('refno' => $refno) );
		/* reset session */
		$session = new ntsSession;
		$session->sess_destroy();
	}
	else
	{
		$msg = join( ': ',
			array(
				M('Invoice'),
				M('Create'),
				M('Error'),
				)
			);

		$forwardTo = ntsLink::makeLink( '-current-' );
		ntsView::addAnnounce( $msg, 'error' );
	}
}

/* redirect */
ntsView::redirect( $forwardTo );
exit;
?>
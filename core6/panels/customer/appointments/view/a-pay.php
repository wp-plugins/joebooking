<?php
$customer_id = ntsLib::getCurrentUserId();
$aam =& ntsAccountingAssetManager::getInstance();
$am =& ntsAccountingManager::getInstance();

$customer_balance = array();
if( $customer_id )
{
	$customer_balance = $am->get_balance( 'customer', $customer_id );
}

require( dirname(__FILE__) . '/_a_init_objects.php' );

$cm =& ntsCommandManager::getInstance();

/* make invoice */
$pm =& ntsPaymentManager::getInstance();
$items = array();
$amounts = array();
$balance_funded = 0;

reset( $objects );
foreach( $objects as $object )
{
	$app = $object->getByArray();
	$obj_id = $object->getId();

	$default_prepay = $pm->getPrepayAmount( $app );
	$prepay_amount = isset($prepay[$obj_id]) ? $prepay[$obj_id] : $default_prepay;

	if( is_array($prepay_amount) )
	{
		// apply balance
		list( $asset_id, $asset_value ) = $prepay[$obj_id];
		$params = array(
			'asset_id'		=> $asset_id,
			'asset_value'	=> $asset_value,
			);
		$cm->runCommand( $object, 'fund', $params );

		if( $cm->isOk() )
		{
			$balance_funded++;
		}
		else
		{
			$errorText = $cm->printActionErrors();
			ntsView::addAnnounce( $errorText, 'error' );
		}
	}
	else
	{
		$paid_amount = $object->getPaidAmount();
		if( $prepay_amount > $paid_amount )
		{
			$items[] = $object;
			$amounts[] = ($prepay_amount - $paid_amount);
		}
	}
}

$forwardTo = ntsLink::makeLink( '-current-' );
if( $amounts )
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
		$msg = M('Make Invoice') . ': ' . M('Error');
		$forwardTo = ntsLink::makeLink( '-current-' );
		ntsView::addAnnounce( $msg, 'error' );
	}
}

if( $balance_funded )
{
	$msg = array();
	if( $balance_funded > 1 )
		$msg[] = $balance_funded . ' ' . M('Appointments');
	else
		$msg[] = M('Appointment');
	$msg[] = M('Pay By Balance');
	$msg[] = M('OK');
	$msg = join( ': ', $msg);
	ntsView::addAnnounce( $msg, 'ok' );
}

/* redirect */
ntsView::redirect( $forwardTo );
exit;
?>
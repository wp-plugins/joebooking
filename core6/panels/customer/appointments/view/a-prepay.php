<?php
$customer_id = ntsLib::getCurrentUserId();
$aam =& ntsAccountingAssetManager::getInstance();
$am =& ntsAccountingManager::getInstance();
$pm =& ntsPaymentManager::getInstance();
$pgm =& ntsPaymentGatewaysManager::getInstance();

$customer_balance = array();
if( $customer_id )
{
	$customer_balance = $am->get_balance( 'customer', $customer_id );
}
require( dirname(__FILE__) . '/_a_init_objects.php' );

$forwardTo = ntsLink::makeLink( '-current-' );

$aid = $_NTS['REQ']->getParam('ai');
$prepay_amount = $_NTS['REQ']->getParam('prepay');
$asset_id = $_NTS['REQ']->getParam('asset_id');

if ( preg_match("/[^0-9.+]/", $prepay_amount) )
{
	$msg = 'Numbers only for the price';
	ntsView::addAnnounce( $msg, 'error' );
	ntsView::redirect( $forwardTo );
	exit;
}

$session = new ntsSession;
$prepay = $session->userdata( 'prepay' );

if( ! isset($prepay[$aid]) )
{
	$msg = 'Appointment ref is not valid';
	ntsView::addAnnounce( $msg, 'error' );
	ntsView::redirect( $forwardTo );
	exit;
}

if( $asset_id )
{
	/* todo - check if valid */
	$prepay[$aid] = array(
		$asset_id,
		$prepay_amount
		);
}
else
{
	$min_prepay_amount = $default_prepays[$aid];
	if( $pgm->hasOffline() )
	{
		$min_prepay_amount = 0;
	}

	$object = ntsObjectFactory::get( 'appointment' );
	$object->setId( $aid );
	$paid_amount = $object->getPaidAmount();

	if( ($prepay_amount + $paid_amount) < $min_prepay_amount )
	{
		$msg = $prepay_amount . ' amount is not allowed, min ' . $min_prepay_amount;
		ntsView::addAnnounce( $msg, 'error' );
		ntsView::redirect( $forwardTo );
		exit;
	}

	$prepay[$aid] = $prepay_amount;
}

$session->set_userdata( 'prepay', $prepay );

/* if there's only one payment option and one appointment, then redirect directly to invoice */
if( count($objects) == 1 )
{
	$group_ref = $_NTS['REQ']->getParam( 'ref' );
	$forwardTo = ntsLink::makeLink( 
		'-current-',
		'pay',
		array(
			'ref'	=> $group_ref,
			)
		);
}

ntsView::redirect( $forwardTo );
exit;
?>
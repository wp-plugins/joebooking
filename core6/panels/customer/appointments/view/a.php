<?php
$group_ref = $_NTS['REQ']->getParam( 'ref' );
$customer_id = ntsLib::getCurrentUserId();

$show = $_NTS['REQ']->getParam( 'show' );
if( ! $show )
	$show = 'upcoming';

$aam =& ntsAccountingAssetManager::getInstance();
$am =& ntsAccountingManager::getInstance();

$customer_balance = array();
if( $customer_id )
{
	$customer_balance = $am->get_balance( 'customer', $customer_id );
}

if( $group_ref )
{
	require( dirname(__FILE__) . '/_a_init_objects.php' );
}
elseif( $customer_id )
{
	$t = $NTS_VIEW['t'];
	$t->setNow();
	$t->setStartDay();
	$startToday = $t->getTimestamp();

	/* find by customer */
	$where = array(
		'customer_id'	=> array( '=', $customer_id )
		);

	if( $show == 'upcoming' )
	{
		$where['starts_at'] = array( '>=', $startToday );
	}
	else
	{
		$where['starts_at'] = array( '<', $startToday );
	}
	$objects = ntsObjectFactory::find( 'appointment', $where );
}
else
{
	$error_msg = M('Access Denied');
	ntsView::addAnnounce( $error_msg, 'error' );
	$forwardTo = ntsLink::makeLink();
	ntsView::redirect( $forwardTo );
	exit;
}

$view = array(
	'objects'			=> $objects,
	'group_ref'			=> $group_ref,
	'customer_balance'	=> $customer_balance,
	'show'				=> $show,
	);

$this->render( 
	dirname(__FILE__) . '/index.php',
	$view
	);
?>
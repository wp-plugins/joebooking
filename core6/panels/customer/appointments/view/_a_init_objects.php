<?php
$error_msg = '';

$group_ref = $_NTS['REQ']->getParam( 'ref' );
if( ! $group_ref )
{
	$error_msg = M('Required') . ': ' . M('Ref Code');
}

if( $group_ref )
{
	/* find by ref code */
	$where = array(
		'group_ref'	=> array( '=', $group_ref )
		);
	$objects = ntsObjectFactory::find( 'appointment', $where );
	if( (! $objects) OR (! isset($objects[0])) )
	{
		$error_msg = M('Not Found') . ': ' . M('Ref Code');
	}
}

if( $error_msg )
{
	ntsView::addAnnounce( $error_msg, 'error' );
	$targetPanel = '-current-/..';
	$forwardTo = ntsLink::makeLink( $targetPanel );
	ntsView::redirect( $forwardTo );
	exit;
}

/* save ref */
$params = array(
	'ref'	=> $group_ref
	);
ntsView::setPersistentParams($params, 'customer/appointments/view' );

/* init prepay */
$pm =& ntsPaymentManager::getInstance();
$session = new ntsSession;
$prepay = $session->userdata('prepay');
if( ! $prepay )
	$prepay = array();

$save = FALSE;
$default_prepays = array();

reset( $objects );

foreach( $objects as $o )
{
	$app = $o->getByArray();
	$obj_id = $o->getId();

	$default_prepay = $pm->getPrepayAmount( $app );
	$default_prepays[$obj_id] = $default_prepay;

	/* if balance prepay is set, double check if it covers that */
	if( isset($prepay[$obj_id]) && is_array($prepay[$obj_id]) )
	{
		list( $asset_id, $asset_value ) = $prepay[$obj_id];
	/* subtract customer balance */
		$test_customer_balance = $am->minus_balance( $customer_balance, $asset_id, $asset_value );
		if( $test_customer_balance[$asset_id] >= 0 ) // ok
		{
			$customer_balance = $test_customer_balance;
		}
		else // not covering
		{
			unset( $prepay[$obj_id] );
		}
	}

	if( ! isset($prepay[$obj_id]) )
	{
		$balance_cover = $am->balance_cover( $customer_balance, $o );
		if( $balance_cover ) // use balance
		{
			foreach( $balance_cover as $bk => $bv )
			{
				$default_prepay = array( $bk, $bv );
				list( $asset_id, $asset_value ) = $default_prepay;
			/* subtract customer balance */
				$customer_balance = $am->minus_balance( $customer_balance, $asset_id, $asset_value );
				break;
			}
		}

		$prepay[$obj_id] = $default_prepay;
		$save = TRUE;
	}
}
if( $save )
{
	$session->set_userdata( 'prepay', $prepay );
}
?>
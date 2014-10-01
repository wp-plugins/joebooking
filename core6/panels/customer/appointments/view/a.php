<?php
$group_ref = $_NTS['REQ']->getParam( 'ref' );
$customer_id = ntsLib::getCurrentUserId();
$display = $_NTS['REQ']->getParam( 'display' );
$t = $NTS_VIEW['t'];

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
	$t->setNow();
	$t->setStartDay();
	$startToday = $t->getTimestamp();

	/* find by customer */
	$where = array(
		'customer_id'	=> array( '=', $customer_id )
		);

	$addon = '';
	if( $show == 'upcoming' )
	{
		$where['starts_at'] = array( '>=', $startToday );
		$addon = 'ORDER BY starts_at ASC';
	}
	else
	{
		$where['starts_at'] = array( '<', $startToday );
		$addon = 'ORDER BY starts_at DESC';
	}

	$ntsdb =& dbWrapper::getInstance();
	$objects = ntsObjectFactory::find( 'appointment', $where, $addon );
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

switch( $action )
{
	case 'export':
		switch( $display )
		{
			case 'ical':
				$fileName = 'appointments-' . $t->formatDate_Db() . '.ics';

				ntsLib::startPushDownloadContent( $fileName, 'text/calendar' );
				echo $this->render_file( 
					dirname(__FILE__) . '/ical.php',
					$view
					);

				exit;
				break;

			case 'excel':
				$fileName = 'appointments-' . $t->formatDate_Db() . '.csv';

				ntsLib::startPushDownloadContent( $fileName );
				echo $this->render_file( 
					dirname(__FILE__) . '/excel.php',
					$view
					);
				exit;
				break;
		}
		break;
	default:
		break;
}

$this->render( 
	dirname(__FILE__) . '/index.php',
	$view
	);
?>
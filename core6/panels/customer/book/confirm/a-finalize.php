<?php
$cm =& ntsCommandManager::getInstance();
$tm2 = $NTS_VIEW['tm2'];

$session = new ntsSession;
$apps = $session->userdata('apps');
$coupon = $session->userdata('coupon');

require( dirname(__FILE__) . '/_check.php' );

$ready = array();
$group_ref = '';
if( count($apps) > 1 )
{
	$group_ref = ntsLib::generateRand( 
		16,
		array(
			'caps'	=> FALSE,
			'hex'	=> TRUE,
			)
		);
}

for( $ii = 0; $ii < count($apps); $ii++ )
{
	$apps[$ii]['group_ref'] = $group_ref;

	$object = ntsObjectFactory::get( 'appointment' );
	$object->setByArray( $apps[$ii] );

	$params = array(
		'coupon'	=> $coupon
		);
	$cm->runCommand( $object, 'init', $params );

	if( $cm->isOk() )
	{
		$cm->runCommand( $object, '_request' );
		$ready[] = $object;
	}
	else
	{
		$errorText = $cm->printActionErrors();
		ntsView::addAnnounce( $errorText, 'error' );
	}
}

if( count($ready) )
{
	if( count($ready) > 1 )
	{
		$add_msg = join( ': ', array( count($ready), M('Appointments'), M('Created') ) );
	}
	else
	{
		$add_msg = join( ': ', array( M('Appointment'), M('Created') ) );
	}
	ntsView::addAnnounce( $add_msg, 'ok' );

	$ref = $ready[0]->getProp( 'group_ref' );
	$forwardTo = ntsLink::makeLink('customer/appointments/view', '', array('ref' => $ref));
}
else
{
	$forwardTo = ntsLink::makeLink('-current-/..');
}

/* clear cart */
$session->sess_destroy();

ntsView::redirect( $forwardTo );
exit;
?>
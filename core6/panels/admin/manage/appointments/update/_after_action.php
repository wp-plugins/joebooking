<?php
$session = new ntsSession;
if( is_array($object) && (count($object) > 1) OR in_array($real_action, array('delete')) )
{
	$forwardTo = $session->userdata( 'calendar_view' );
}
else
{
	$forwardTo = ntsLink::makeLink( '-current-/../edit/overview' );

	$returnTo = $_NTS['REQ']->getParam( NTS_PARAM_RETURN );
	if( $returnTo )
	{
		switch( $returnTo )
		{
			case 'calendar':
				$forwardTo = $session->userdata( 'calendar_view' );
				break;
		}
	}
}

if( ! $forwardTo )
{
	$forwardTo = ntsLink::makeLink( '-current-/../../calendar' );
}

ntsView::redirect2( $forwardTo );
exit;
?>
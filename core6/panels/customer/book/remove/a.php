<?php
$ai = $_NTS['REQ']->getParam('ai');

$session = new ntsSession;
$apps = $session->userdata('apps');
if( ! $apps )
	$apps = array();

/* sort by starts_at */
usort( $apps, create_function(
	'$a, $b',
	'
	$return = ($a["starts_at"] - $b["starts_at"]);
	return $return;
	'
	)
);

if( isset($apps[$ai-1]) )
{
	array_splice( $apps, $ai-1, 1 );
}

/* save */
$session->set_userdata( 'apps', $apps );

$forwardTo = ntsLink::makeLink(
	'-current-/../confirm',
	'',
	array(
		'time'	=> '-reset-',
		)
	);
ntsView::redirect( $forwardTo );
exit;
?>
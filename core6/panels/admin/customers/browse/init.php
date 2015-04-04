<?php
$persist = array( 
	'cfilter'	=> 'all',
	'search'	=> '',
	'skip'		=> array(),
	);
// $persist['sort'] = NTS_EMAIL_AS_USERNAME ? 'email' : 'username';
$persist['sort'] = 'last_name';

reset( $persist );
foreach( $persist as $p => $default )
{
	$v = $_NTS['REQ']->getParam( $p );
	if( ! $v )
		$v = $default;
	$saveOn[$p] = $v;

	switch( $p )
	{
		case 'cfilter':
			$p = 'filter';
			break;
		case 'skip':
			$v = $v ? explode( '-', $v ) : array();
			break;
	}
	ntsLib::setVar( 'admin/customers/browse::' . $p, $v );
}
ntsView::setPersistentParams( $saveOn, 'admin/customers/browse' );
//ntsView::setBack( ntsLink::makeLink('admin/customers/browse', '', $saveOn) );

$ids = null;
ntsLib::setVar('admin/customers/browse::ids', $ids);
?>
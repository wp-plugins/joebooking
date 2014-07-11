<?php
$conf =& ntsConf::getInstance();

$params = array(
	'Bookable Resource',
	'Bookable Resources',
	'Service',
	'Services',
	'Customer',
	'Customers',
	'Location',
	'Locations',
	);

$default = array();
reset( $params );
foreach( $params as $p )
{
	$default[] = array( $p, M($p) );
}

$ff =& ntsFormFactory::getInstance();
$formFile = dirname( __FILE__ ) . '/form';
$NTS_VIEW['form'] =& $ff->makeForm( $formFile, $default );

switch( $action ){
	case 'update':
		if( $NTS_VIEW['form']->validate() ){
			$formValues = $NTS_VIEW['form']->getValues();
			reset( $params );
			foreach( $formValues as $fk => $fv ){
				$fk = substr( $fk, strlen('term-') );
				$p = $params[ $fk - 1 ];

				$realPropName = 'text-' . $p;
				$conf->set( $realPropName, $fv );
				}

			if( ! ($error = $conf->getError()) ){
				ntsView::setAnnounce( M('Terminology') . ': ' . M('Update') . ': ' . M('OK'), 'ok' );

			/* continue to delivery options form */
				$forwardTo = ntsLink::makeLink( '-current-' );
				ntsView::redirect( $forwardTo );
				exit;
				}
			else {
				echo '<BR>Database error:<BR>' . $error . '<BR>';
				}
			}
		else {
		/* form not valid, continue to create form */
			}
		break;
	}
?>
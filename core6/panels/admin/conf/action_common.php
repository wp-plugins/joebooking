<?php
$conf =& ntsConf::getInstance();

$ff =& ntsFormFactory::getInstance();
$formFile = $myDir . '/form';

$default = array();
reset( $params );
foreach( $params as $p ){
	$default[ $p ] = $conf->get( $p );
	}
$NTS_VIEW['form'] =& $ff->makeForm( $formFile, $default ); 

switch( $action ){
	case 'update':
		if( $NTS_VIEW['form']->validate() ){
			$formValues = $NTS_VIEW['form']->getValues();
			reset( $params );
			foreach( $params as $p )
			{
				if( isset($formValues[$p]) )
				{
					$conf->set( $p, $formValues[$p] );
				}
			}

			if( ! ($error = $conf->getError()) ){
				ntsView::setAnnounce( M('Settings') . ': ' . M('Update') . ': ' . M('OK'), 'ok' );

			/* continue to delivery options form */
				if( isset($getBack) && $getBack )
				{
					$forwardTo = ntsView::getBackLink();
				}
				elseif( isset($forwardTo) && $forwardTo )
				{
					//
				}
				else
				{
					$forwardTo = ntsLink::makeLink( '-current-' );
				}
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
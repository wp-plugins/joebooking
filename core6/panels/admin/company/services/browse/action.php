<?php
$entries = ntsObjectFactory::getAll( 'service', 'ORDER BY show_order ASC, title ASC' );
ntsLib::setVar( 'admin/company/services::entries', $entries );

$ff =& ntsFormFactory::getInstance();
$formParams = array();
reset( $entries );
foreach( $entries as $e )
{
	$formParams['order_' . $e->getId()] = $e->getProp('show_order');
}

$formFile = dirname( __FILE__ ) . '/form';
$NTS_VIEW['form'] =& $ff->makeForm( $formFile, $formParams );

switch( $action )
{
	case 'update':
		if( $NTS_VIEW['form']->validate() )
		{
			$cm =& ntsCommandManager::getInstance();
			$formValues = $NTS_VIEW['form']->getValues();
			reset( $formValues );
			foreach( $formValues as $key => $order )
			{
				$id = trim( substr( $key, strlen('order_') ) );
				$object = ntsObjectFactory::get( 'service' );
				$object->setId( $id );
				$object->setProp( 'show_order', $order );
				$cm->runCommand( $object, 'update' );
			}

			$msg = array( M('Services'), M('Update'), M('OK') );
			$msg = join( ': ', $msg );
			ntsView::addAnnounce( $msg, 'ok' );

		/* continue to the list with anouncement */
			$forwardTo = ntsLink::makeLink( '-current-' );
			ntsView::redirect( $forwardTo );
			exit;
		}

		break;
}
?>
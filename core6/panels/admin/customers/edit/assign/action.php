<?php
$ff =& ntsFormFactory::getInstance();
$cm =& ntsCommandManager::getInstance();

$formFile = dirname( __FILE__ ) . '/form';
$formParams = array();

$fill_in = array( '_assign_resource', '_assign_service', '_assign_resource_only', '_assign_service_only', '_assign_location', '_assign_location_only' );
foreach( $fill_in as $fi )
{
	$value = $object->getProp( $fi );
	if( $value )
		$formParams[$fi] = $value[0];
}

$NTS_VIEW['form'] =& $ff->makeForm( $formFile, $formParams );

$object = ntsLib::getVar( 'admin/customers/edit::OBJECT' );
switch( $action ){
	case 'assign':
		if( $NTS_VIEW['form']->validate() )
		{
			$cm =& ntsCommandManager::getInstance();
			$formValues = $NTS_VIEW['form']->getValues();

			$assign_location = $formValues['_assign_location'] ? array($formValues['_assign_location']) : array();
			$assign_location_only = $formValues['_assign_location_only'] ? $formValues['_assign_location_only'] : NULL;
			$object->setProp( '_assign_location', $assign_location );
			$object->setProp( '_assign_location_only', $assign_location_only );

			$assign_resource = $formValues['_assign_resource'] ? array($formValues['_assign_resource']) : array();
			$assign_resource_only = $formValues['_assign_resource_only'] ? $formValues['_assign_resource_only'] : NULL;
			$object->setProp( '_assign_resource', $assign_resource );
			$object->setProp( '_assign_resource_only', $assign_resource_only );

			$assign_service = $formValues['_assign_service'] ? array($formValues['_assign_service']) : array();
			$assign_service_only = $formValues['_assign_service_only'] ? $formValues['_assign_service_only'] : NULL;
			$object->setProp( '_assign_service', $assign_service );
			$object->setProp( '_assign_service_only', $assign_service_only );

			$cm->runCommand( $object, 'update' );
			if( $cm->isOk() )
			{
				$msg = array();
				$msg[] = M('Assign');
				$msg[] = M('OK');
				$msg = join( ': ', $msg );
				ntsView::addAnnounce( $msg, 'ok' );

			/* continue to customer edit */
				$forwardTo = ntsLink::makeLink( '-current-' );
				ntsView::redirect( $forwardTo );
				exit;
			}
			else
			{
				$actionError = true;
				$errorString = $cm->printActionErrors();
			}
		}
		else
		{
		/* form not valid, continue to edit form */
		}
		break;
	}
?>
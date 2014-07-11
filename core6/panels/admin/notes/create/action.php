<?php
$parent = ntsLib::getVar( 'admin/notes::PARENT' );
$parentId = $parent->getId();
$parentClass = $parent->getClassName();

$ff =& ntsFormFactory::getInstance();
$formFile = dirname( __FILE__ ) . '/form';
$NTS_VIEW['form'] =& $ff->makeForm( $formFile );

switch( $action ){
	case 'create':
		if( $NTS_VIEW['form']->validate() ){
			$formValues = $NTS_VIEW['form']->getValues();

			$now = time();
			$metaData = $now . ':' . ntsLib::getCurrentUserId();

			$ntsdb =& dbWrapper::getInstance();
			$newValues = array(
				'obj_class'		=> $parentClass,
				'obj_id'		=> $parentId,
				'meta_name'		=> '_note',
				'meta_value'	=> $formValues['note'],
				'meta_data'		=> $metaData,
				);
			$result = $ntsdb->insert('objectmeta', $newValues );

			if( $result ){
				$msg = array( M('Note'), M('Add'), M('OK') );
				$msg = join( ': ', $msg );
				ntsView::addAnnounce( $msg, 'ok' );

			/* continue to the list with anouncement */
				ntsView::getBack();
				exit;
				}
			else {
				$errorText = $cm->printActionErrors();
				ntsView::addAnnounce( $errorText, 'error' );
				}
			}
		else {
		/* form not valid, continue to create form */
			}
		break;
	default:
		break;
	}
?>
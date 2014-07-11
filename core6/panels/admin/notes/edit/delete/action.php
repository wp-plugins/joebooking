<?php
$ntsdb =& dbWrapper::getInstance();
$ff =& ntsFormFactory::getInstance();
$noteId = $_NTS['REQ']->getParam('noteid');

$where = array(
	'id'	=> array('=', $noteId)
	);

$formParams = array();
$formParams['noteid'] = $noteId;

$formFile = dirname( __FILE__ ) . '/form';
$NTS_VIEW['form'] =& $ff->makeForm( $formFile, $formParams );

switch( $action ){
	case 'delete':
		$result = $ntsdb->delete('objectmeta', $where );

		if( $result ){
			ntsView::setAnnounce( M('Note') . ': '. M('Delete') . ': ' . M('OK'), 'ok' );
			}
		else {
			$errorText = $cm->printActionErrors();
			ntsView::addAnnounce( $errorText, 'error' );
			}
	/* continue to the list with anouncement */
		ntsView::getBack();
		exit;
		break;
	}
?>
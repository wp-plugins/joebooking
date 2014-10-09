<?php
$appEdit = ntsLib::getVar( 'admin/manage:appEdit' );
$object = ntsLib::getVar( 'admin/manage/appointments/update::OBJECT' );

if( ! is_array($object) )
	$object = array( $object );

reset( $object );
foreach( $object as $obj )
{
	$rid = $obj->getProp( 'resource_id' );

	if( ! in_array($rid, $appEdit) )
	{
		$msg = M('Appointment') . ': ' . M('Edit') . ': ' . M('Permission Denied');
		ntsView::addAnnounce( $msg, 'error' );

		/* continue */
		ntsView::getBack( true );
		exit;
	}
}


$confirm_suffix = '-confirm';
if( substr($action, -strlen($confirm_suffix)) == $confirm_suffix )
{
	$real_action = substr($action, 0, -strlen($confirm_suffix));
}
else
{
	$real_action = $action;
}

if( in_array($real_action, array('delete')) )
{
	$current_user = ntsLib::getCurrentUser();
	$canDelete = ( $current_user->getProp('_admin_level') == 'staff' ) ? FALSE : TRUE;
	if( ! $canDelete )
	{
		$msg = M('Appointment') . ': ' . M('Delete') . ': ' . M('Permission Denied');
		ntsView::addAnnounce( $msg, 'error' );

		require( dirname(__FILE__) . '/_after_action.php' );
	}
}
?>
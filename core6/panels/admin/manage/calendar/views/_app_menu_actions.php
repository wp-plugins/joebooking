<?php
global $NTS_VIEW;
$viewMode = $NTS_VIEW[NTS_PARAM_VIEW_MODE];
if( $viewMode == 'print' ){
	return;
}

$appEdit = ntsLib::getVar( 'admin/manage:appEdit' );
$rid = $app->getProp( 'resource_id' );
if( ! in_array($rid, $appEdit) ){
	return;
}

if( $menu && (is_array($menu[count($menu)-1]) OR ($menu[count($menu)-1] != '-divider-') ) )
{
	$menu[] = '-divider-';
}

/* STATUS ACTIONS */
$status_actions = $app->getStatusActions();
if( $status_actions )
{
	$menu[] = array(
		'href'	=> ntsLink::makeLink(
			'admin/manage/appointments/edit/status', 
			'', 
			array(
				'_id' => $app->getId(),
				NTS_PARAM_RETURN	=> 'calendar',
				)
			),
		'title'	=> '<i class="fa fa-flag-o"></i> ' . M('Set Status'),
		'title'	=> $app->statusLabel('', 'i') . ' ' . M('Change Status'),
		'class'	=> 'hc-ajax-loader'
		);
}

/* EDIT */
//$menu[] = '-divider-';
$menu[] = array(
	'href'	=> ntsLink::makeLink('admin/manage/appointments/edit/overview', '', array('_id' => $app->getId()) ),
	'title'	=> '<i class="fa fa-edit"></i> ' . M('Edit'),
	'class'	=> 'hc-parent-loader'
	);

/* DELETE */
$current_user = ntsLib::getCurrentUser();
$canDelete = ( $current_user->getProp('_admin_level') == 'staff' ) ? FALSE : TRUE;
if( $canDelete )
{
	//$menu[] = '-divider-';
	$menu[] = array(
		'href'	=> ntsLink::makeLink(
			'admin/manage/appointments/update',
			'delete-confirm',
			array(
				'_id' => $app->getId(),
				NTS_PARAM_RETURN	=> 'calendar',
				)
			),
		'title'	=> '<i class="fa fa-times text-danger"></i> ' . M('Delete'),
		'class'	=> 'hc-confirm hc-target-reloader2',
		);
}
?>
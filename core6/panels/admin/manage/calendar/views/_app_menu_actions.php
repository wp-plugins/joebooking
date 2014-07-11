<?php
$appEdit = ntsLib::getVar( 'admin/manage:appEdit' );
$rid = $app->getProp( 'resource_id' );
if( ! in_array($rid, $appEdit) )
{
	return;
}

if( $menu && (is_array($menu[count($menu)-1]) OR ($menu[count($menu)-1] != '-divider-') ) )
{
	$menu[] = '-divider-';
}

/* STATUS ACTIONS */
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

/* EDIT */
//$menu[] = '-divider-';
$menu[] = array(
	'href'	=> ntsLink::makeLink('admin/manage/appointments/edit/overview', '', array('_id' => $app->getId()) ),
	'title'	=> '<i class="fa fa-edit"></i> ' . M('Edit'),
	'class'	=> 'hc-parent-loader'
	);

/* DELETE */
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
	'class'	=> 'hc-confirm',
	);
?>
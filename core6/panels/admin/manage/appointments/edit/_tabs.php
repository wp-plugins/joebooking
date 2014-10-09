<?php
$aam =& ntsAccountingAssetManager::getInstance();
$ntsConf =& ntsConf::getInstance();

$tabs = array();

$object = ntsLib::getVar( 'admin/manage/appointments/edit::OBJECT' );
$appEdit = ntsLib::getVar( 'admin/manage:appEdit' );
$hide = ntsLib::getVar( 'admin/manage/appointments/edit::hide' );

$canDelete = ntsLib::getVar( 'admin/manage/appointments/edit::canDelete' );

$tabs['overview'] = array(
	'title'	=> '<i class="fa fa-edit"></i> ' . M('Overview'),
	);

if( ! is_array($object) )
{
	$actions = $object->getStatusActions();

	if( in_array($rid, $appEdit) )
	{
		$status_actions = array();
		foreach( $actions as $a )
		{
			$status_actions[] = array(
				'title'			=> $a[1],
				'href'			=> ntsLink::makeLink('-current-/../../update', $a[0]),
				'link-class'	=> 'hc-ajax-loader',
				'data-attr'		=> array(
					'wrap-ajax-child'	=> 'li',
					)
				);
		}

		if( $status_actions )
		{
			$tabs['overview'] = array(
				$status_actions,
				'title'			=> $object->statusLabel('', 'i') . ' ' . $object->statusText(),
				'panel'			=> 'overview',
				);
		}
		else
		{
			$tabs['overview'] = array(
				'title'			=> $object->statusLabel('', 'i') . ' ' . $object->statusText(),
				);
		}
	}
}

if( ! is_array($object) ){
	$t = $NTS_VIEW['t'];
	$startsAt = $object->getProp('starts_at');
	$t->setTimestamp( $startsAt );
	$cal = $t->formatDate_Db();

	if( ($startsAt > 0) && in_array($rid, $appEdit) )
	{
		$tabs['change'] = array(
			'title'		=> '<i class="fa fa-calendar"></i> ' . M('Change'),
			);
	}

	$attachEnableCompany = $ntsConf->get('attachEnableCompany');
	if( $attachEnableCompany )
	{
		$am = new ntsAttachManager;
		$attachments = $am->get( $object->getClassName(), $object->getId() );
		$tabs['attachments'] = array(
			'title'	=> '<i class="fa fa-file-o"></i> ' . M('Attachments') . ' [' . count($attachments) . ']',
			);
	}
}

$sendCcForAppointment = $ntsConf->get('sendCcForAppointment');
if( (! is_array($object)) && $sendCcForAppointment )
{
	$cc = $object->getProp('_cc');
	reset( $cc );
	$cc_count = 0;
	foreach( $cc as $cc_to )
	{
		if( trim($cc_to) )
			$cc_count++;
	}

	$tabs['cc'] = array(
		'title'	=> '<i class="fa fa-envelope"></i> ' . M('CC') . ' [' . $cc_count . ']',
		);
}

/* new payments */
if( ! is_array($object) )
{
	$price = $object->getProp('price');
	if( strlen($price) )
	{
		$due_amount = $object->getDue();

		$link_class = '';
		$link_text = '';
		$alert = 0;

		if( $due_amount < 0 )
		{
			$link_class = 'text-success';
			$link_text = ntsCurrency::formatPrice( -$due_amount ) . ' ' . M('Overpaid');
		}
		elseif( $due_amount == 0 )
		{
			$link_class = 'text-success';
			$link_text = '<i class="fa fa-check"></i> ' . M('Payment') . ': ' . M('OK');
		}
		elseif( $due_amount > 0 )
		{
			$link_class = 'text-warning';
			$link_text = '-' . ntsCurrency::formatPrice( $due_amount );
			$link_text = ntsCurrency::formatPriceLabel( -$due_amount );
		}
		else
		{
			$link_text = M('Not Paid');
			$alert = 1;
		}

		$tabs['accounting'] = array(
			'title'			=> $link_text,
			'link-class'	=> $link_class,
			'alert'			=> $alert
			);
	}
}

if( ! is_array($object) )
{
	$group_count = 1;
	$group_ref = $object->getProp('group_ref');
	if( $group_ref )
	{
		$where = array(
			'group_ref'	=> array( '=', $group_ref )
			);
		$group_count = ntsObjectFactory::count( 'appointment', $where );

		if( $group_count > 1 )
		{
			$tabs['group'] = array(
				'title'		=> M('Series') . ': ' . $group_count . ' ' . M('Appointments')
				);
		}
	}
}

if( ! is_array($object) )
{
	$logs = $object->getLogs();
	if( $logs )
	{
		$tabs['history'] = array(
			'title'		=> '<i class="fa fa-list-ul"></i> ' . M('History'),
			'panel'		=> 'history',
			);
	}

	if( $canDelete )
	{
		if( in_array($rid, $appEdit) )
		{
			$tabs['delete'] = array(
				'title'		=> '<i class="fa fa-times text-danger"></i> ' . M('Delete'),
				'panel'		=> '../update',
				'params'	=> array( 
					NTS_PARAM_ACTION	=> 'delete',
					),
				'alert'		=> 1,
				);
		}
	}
}
?>
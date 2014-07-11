<?php
$ntsdb =& dbWrapper::getInstance();
$object = ntsLib::getVar( 'admin/company/services/edit::OBJECT' );

$tabs = array();

$tabs['edit'] = array(
	'title'	=> '<i class="fa fa-edit"></i> ' . M('Edit'),
	);

$tabs['permissions'] = array(
	'title'	=> '<i class="fa fa-lock"></i> ' . M('Permissions'),
	);

$count = $ntsdb->count( 'service_cats' );
if( $count > 0 )
{
	$tabs['cats'] = array(
		'title'	=> '<i class="fa fa-list-ul"></i> ' . M('Categories'),
		);
}

$tabs['recurrent'] = array(
	'title'	=> '<i class="fa fa-calendar"></i> ' . M('Recurring'),
	);

$thisPrice = $object->getProp('price');
if( (strlen($thisPrice) > 0) && ($thisPrice > 0) )
{
	$tabs['payments'] = array(
		'title'	=> '<i class="fa fa-dollar"></i> ' . M('Payments'),
		);
}

$servicesCount = $ntsdb->count( 'services' );
if( $servicesCount > 1 )
{
	$tabs['merge'] = array(
		'title'	=> '<i class="fa fa-copy"></i> ' . M('Merge'),
		);
}

$services_ids = ntsObjectFactory::getAllIds('service');
if( count($services_ids) > 1 )
{
	$tabs['delete'] = array(
		'title'	=> '<i class="fa fa-times text-danger"></i> ' . M('Delete'),
		'alert'	=> 1,
		);
}
?>
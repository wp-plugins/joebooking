<?php
global $NTS_MENU;

$NTS_MENU = array(
	'admin/calendar'	=> array(
		'title'		=> '<i class="fa fa-check-square-o"></i> ' . M('Appointments'),
		'panel'		=> 'admin/manage/calendar'
		),

	'admin/schedules'	=> array(
		'title'		=> '<i class="fa fa-bar-chart-o"></i> ' . M('Availability'),
		'panel'		=> 'admin/manage/schedules'
		),
	'admin/customers'	=> array(
		'title'		=> '<i class="fa fa-user"></i> ' . M('Customers'),
		'panel'		=> 'admin/customers/browse',
		),

	'admin/company'	=> '<i class="fa fa-building-o"></i> ' . M('Company'),
		'admin/company/services'	=> array(
			'title'	=> '<i class="fa fa-tags"></i> ' . M('Services'),
			'panel'	=> 'admin/company/services/browse'
			),
			'admin/company/services/browse'		=> M('View'),
			'admin/company/services/create'		=> M('Add'),
			'admin/company/services/cats'		=> M('Categories'),
			'admin/company/services/packs'		=> M('Packages'),
			'admin/company/services/promotions'	=> M('Promotions'),

		'admin/company/resources'	=>  array(
			'title'	=> '<i class="fa fa-hand-o-up"></i> ' . M('Bookable Resources'),
			'panel'	=> 'admin/company/resources/browse'
			),
			'admin/company/resources/browse'	=> M('View'),
			'admin/company/resources/create'	=> M('Add'),

		'admin/company/locations'	=>  array(
			'title'	=> '<i class="fa fa-home"></i> ' . M('Locations'),
			'panel'	=> 'admin/company/locations/browse'
			),
		'admin/company/staff'	=>  array(
			'title'	=> '<i class="fa fa-user fa-border"></i> ' . M('Administrative Users'),
			'panel'	=> 'admin/company/staff/browse'
			),

	'admin/payments'	=> array(
		'title'		=> '<i class="fa fa-usd"></i> ' . M('Payments'),
		'panel'		=> 'admin/payments',
		),
		'admin/payments/invoices' => array(
			'title'		=> '<i class="fa fa-file-text-o"></i> ' . M('Invoices'),
			'panel'		=> 'admin/payments/invoices/browse',
			),
		'admin/payments/transactions' => array(
			'title'		=> '<i class="fa fa-bars"></i> ' . M('Transactions'),
			'panel'		=> 'admin/payments/transactions/browse',
			),
		'admin/payments/settings' => array(
			'title'		=> '<i class="fa fa-usd"></i> ' . M('Finance Settings'),
			'panel'		=> 'admin/conf/currency',
			),
		'admin/payments/payment_gateways' => array(
			'title'		=> '<i class="fa fa-credit-card"></i> ' . M('Payment Gateways'),
			'panel'		=> 'admin/conf/payment_gateways',
			),

	'admin/conf'	=> '<i class="fa fa-cog"></i> ' . M('Settings'),
			'admin/conf/forms_customers'		=> array(
				'title'	=> M('Customer Form'),
				'panel'	=> 'admin/forms/customers/browse',
				),
			'admin/conf/forms_appointments'		=> array(
				'title'	=> M('Service Forms'),
				'panel'	=> 'admin/forms/appointments/assign',
				),
		'admin/conf/divider2'	=> '-divider-',

		'admin/conf/customers'		=> M('Customers'),

		'admin/conf/promo'		=> array(
			'title'	=> M('Newsletter'),
			'panel'	=> 'admin/promo',
			),

		'admin/conf/email_settings'		=> M('Email'),
		'admin/conf/email_templates'	=> M('Notifications'),
		'admin/conf/terminology'		=> M('Terminology'),

		'admin/conf/cron'				=> M('Automatic Actions'),
		'admin/conf/events'				=> M('Event Actions'),

		'admin/conf/sync'				=> array(
			'title'	=> M('Synchronization'),
			'panel'	=> 'admin/sync'
			),
		'admin/conf/datetime'			=> M('Date and Time'),
		'admin/conf/languages'			=> M('Languages'),
		'admin/conf/attachments'		=> M('Attachments'),

/*
		'admin/conf/themes'				=> M('Themes'),
*/
		'admin/conf/plugins'			=> M('Plugins'),
		'admin/conf/misc'				=> M('Misc'),
		'admin/conf/upgrade'			=> M('Info'),
		'admin/conf/backup'				=> M('Backup'),
	);


$has_price = FALSE;
$all_services = ntsObjectFactory::getAll( 'service' );
reset( $all_services );
foreach( $all_services as $s )
{
	$service_price = $s->getProp('price');
	if( strlen($service_price) && ($service_price > 0) )
	{
		$has_price = TRUE;
		break;
	}
}
if( $has_price )
{
	$NTS_MENU['admin/company/packs'] = array(
		'title'	=> '<i class="fa fa-suitcase"></i> ' . M('Packages'),
		'panel'	=> 'admin/company/services/packs/browse',
		);
	$NTS_MENU['admin/company/promotions'] = array(
		'title'	=> '<i class="fa fa-gift"></i> ' . M('Promotions'),
		'panel'	=> 'admin/company/services/promotions/browse',
		);
}

if( defined('NTS_APP_LEVEL') && (NTS_APP_LEVEL == 'lite') )
{
	$appInfo = ntsLib::getAppInfo();
	$order_link = isset($appInfo['order_link']) ? $appInfo['order_link'] : 'http://www.hitappoint.com/order/';
	$promo_title = 'Pro Version';

	$NTS_MENU['admin/promo'] = array(
		'title'	=> $promo_title,
		'link'	=> $order_link,
		'external'	=> TRUE,
		'order'	=> 200
		);
}
elseif( isset($appInfo['order_link_title']) )
{
	$NTS_MENU['admin/promo'] = array(
		'title'	=> $appInfo['order_link_title'],
		'link'	=> $appInfo['order_link'],
		'external'	=> TRUE,
		'order'	=> 200
		);
}

$uif =& ntsUserIntegratorFactory::getInstance();
$integrator =& $uif->getIntegrator();
$integrator->cleanUp();
?>
<?php
global $NTS_MENU;

$main_menu = array(
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

	'admin/company'	=> array(
		'title'		=> M('Company'),
		'icon'		=> 'building-o',
		),
		'admin/company/services'	=> array(
			'title'	=> M('Services'),
			'icon'	=> 'tags',
			'panel'	=> 'admin/company/services/browse'
			),
			'admin/company/services/browse'		=> M('View'),
			'admin/company/services/create'		=> M('Add'),
			'admin/company/services/cats'		=> M('Categories'),
			'admin/company/services/packs'		=> M('Packages'),
			'admin/company/services/promotions'	=> M('Promotions'),

		'admin/company/resources'	=>  array(
			'title'	=> M('Bookable Resources'),
			'icon'	=> 'hand-o-up',
			'panel'	=> 'admin/company/resources/browse'
			),
			'admin/company/resources/browse'	=> M('View'),
			'admin/company/resources/create'	=> M('Add'),

		'admin/company/locations'	=>  array(
			'title'	=> M('Locations'),
			'icon'	=> 'home',
			'panel'	=> 'admin/company/locations/browse'
			),
		'admin/company/staff'	=>  array(
			'title'	=> M('Administrative Users'),
			'icon'	=> 'user',
			'panel'	=> 'admin/company/staff/browse'
			),

	'admin/payments'	=> array(
		'title'		=> M('Payments'),
		'icon'		=> 'usd',
		'panel'		=> 'admin/payments',
		),
		'admin/payments/invoices' => array(
			'title'		=> M('Invoices'),
			'icon'		=> 'file-text-o',
			'panel'		=> 'admin/payments/invoices/browse',
			),
		'admin/payments/transactions' => array(
			'title'		=> M('Transactions'),
			'icon'		=> 'bars',
			'panel'		=> 'admin/payments/transactions/browse',
			),
		'admin/payments/settings' => array(
			'title'		=> M('Finance Settings'),
			'icon'		=> 'usd',
			'panel'		=> 'admin/conf/currency',
			),
		'admin/payments/payment_gateways' => array(
			'title'		=> M('Payment Gateways'),
			'icon'		=> 'credit-card',
			'panel'		=> 'admin/conf/payment_gateways',
			),

	'admin/sync'	=> '<i class="fa fa-chain"></i> ' . M('Synchronization'),
	'admin/conf' => array(
		'title'		=> M('Settings'),
		'icon'		=> 'cog',
		),
			'admin/conf/forms_customers'		=> array(
				'title'	=> M('Custom Forms') . ': ' . M('Customer'),
				'panel'	=> 'admin/forms/customers/browse',
				),
			'admin/conf/forms_appointments'		=> array(
				'title'	=> M('Custom Forms') . ': ' . M('Appointment'),
				'panel'	=> 'admin/forms/appointments/assign',
				),
		'admin/conf/divider2'	=> '-divider-',

		'admin/conf/customers'		=> M('Customers'),

		'admin/conf/flow'		=> M('Appointment Flow'),

		'admin/conf/promo'		=> array(
			'title'	=> M('Newsletter'),
			'panel'	=> 'admin/promo',
			),

		'admin/conf/email_settings'		=> M('Email'),
		'admin/conf/email_templates'	=> M('Notifications'),

		'admin/conf/cron'				=> M('Automatic Actions'),
		'admin/conf/events'				=> M('Event Actions'),

		'admin/conf/datetime'			=> M('Date and Time'),
		'admin/conf/languages'			=> M('Languages'),
		'admin/conf/attachments'		=> M('Attachments'),

		'admin/conf/plugins'			=> M('Plugins'),
		'admin/conf/misc'				=> M('Misc'),
		'admin/conf/upgrade'			=> M('Info'),
		'admin/conf/backup'				=> M('Backup'),
	);

$NTS_MENU = array_merge( $main_menu, $NTS_MENU );

$has_price = FALSE;
$all_services = ntsObjectFactory::getAll( 'service' );
reset( $all_services );
foreach( $all_services as $s ){
	$service_price = $s->getProp('price');
	if( strlen($service_price) && ($service_price > 0) ){
		$has_price = TRUE;
		break;
	}
}
if( $has_price ){
	$NTS_MENU['admin/company/packs'] = array(
		'title'	=> M('Packages'),
		'icon'	=> 'suitcase',
		'panel'	=> 'admin/company/services/packs/browse',
		);
	$NTS_MENU['admin/company/promotions'] = array(
		'title'	=> M('Promotions'),
		'icon'	=> 'gift',
		'panel'	=> 'admin/company/services/promotions/browse',
		);
}

$orders_count = ntsObjectFactory::count('order');
if( $orders_count ){
	$NTS_MENU['admin/payments/orders'] = array(
		'title'		=> M('Package Orders'),
		'icon'		=> 'list',
		'panel'		=> 'admin/payments/orders/browse',
		);
}

if( defined('NTS_APP_LEVEL') && (NTS_APP_LEVEL == 'lite') ){
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
elseif( isset($appInfo['order_link_title']) ){
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
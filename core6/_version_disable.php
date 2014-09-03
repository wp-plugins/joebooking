<?php
$disable = array();

$disable['hooks'] = array(
	'panels'	=> array(
		'admin/conf/events',
		),
	'files'	=> array(
		'core6/observers',
		)
	);

$disable['attach'] = array(
	'panels'	=> array(
		'admin/attachments',
		'admin/conf/attachments',
		),
	'files'	=> array(
		'uploads',
		)
	);

$disable['uploads_own'] = array(
	'panels'	=> array(
		),
	'files'	=> array(
		'uploads',
		)
	);

$disable['common'] = array(
	'panels'	=> array(
		'admin/company/services/edit/recurrent',
		'admin/company/services/edit/cats',
		'admin/company/services/cats',
		'admin/promo',
		),
	'files'	=> array(
		'theme',
		'core6/version_hitappoint_demo.php',

		'core6/plugins/bundles',
		'core6/plugins/freshbooks',
		'extensions',

		'core6/happ/assets/bootstrap/css/bootstrap3.min.less',
		'core6/happ/assets/bootstrap/css/bootstrap3_cal.less',
		'core6/happ/assets/bootstrap/css/compile3.php',
		'core6/happ/assets/bootstrap/css/hc3.less',
		'core6/happ/assets/bootstrap/css/lessc.inc.php',
		)
	);

$disable['sms'] = array(
	'panels'	=> array(
		),
	'files'	=> array(
		'core6/plugins/sms',
		)
	);

$disable['custom_fields'] = array(
	'panels'	=> array(
		'admin/forms',
		),
	'files'	=> array(
		)
	);

$disable['payment'] = array(
	'panels'	=> array(
		'admin/conf/currency',
		'admin/conf/payment_gateways',
		'admin/payments',
		'admin/customers/edit/payments',
		'admin/customers/edit/accounting',
		'admin/manage/appointments/edit/accounting',
		'admin/company/services/edit/payments',

		'customer/invoices',
		'customer/orders',
		'customer/packs',
		'customer/accounting',
		'customer/appointments/edit/paybalance',

		'system/invoice',
		'system/payment',
		),
	'files'	=> array(
		'core6/model/invoice',
		'core6/objects/ntsCoupon.php',
		'core6/objects/ntsInvoice.php',
		'core6/plugins/freshbooks',
		'core6/payment',
		)
	);

$disable['language'] = array(
	'panels'	=> array(
		'admin/conf/languages',
		'anon/language',
		),
	'files'	=> array(
		)
	);

$disable['packs'] = array(
	'panels'	=> array(
		'admin/company/services/packs',
		),
	'files'	=> array(
		'core6/model/pack',
		'core6/model/order',
		'core6/objects/ntsPack.php',
		'core6/objects/ntsOrder.php',
		'core6/objects/ntsPackBase.php',
		'core6/app/forms/pack.php',
		)
	);

$disable['promotions'] = array(
	'panels'	=> array(
		'admin/company/services/promotions',
		),
	'files'	=> array(
		'core6/model/promotion',
		'core6/objects/ntsPromotion.php',
		)
	);

$disable['resources'] = array(
	'panels'	=> array(
		'admin/company/resources',
		'admin/company/staff',
		'admin/customers/edit/admin',
		),
	'files'	=> array(
		'core6/app/forms/resource.php',
		)
	);

$disable['locations'] = array(
	'panels'	=> array(
		'admin/company/locations',
		),
	'files'	=> array(
		'core6/model/location',
		'core6/app/forms/location.php',
		)
	);

$disable['joomla'] = array(
	'panels'	=> array(
		),
	'files'	=> array(
		'core6/integration/joomla',
		'core6/integration/joomla3',
		)
	);

$disable['wordpress'] = array(
	'panels'	=> array(
		),
	'files'	=> array(
		'core6/integration/wordpress',
		'hitappoint.php',
		'joebooking.php',
		'core6/happ/hclib/wp-plugin-update-checker',
		'core6/happ/hclib/hcWpPremiumPlugin.php',
		)
	);

$disable['wordpress_pro'] = array(
	'panels'	=> array(
		),
	'files'	=> array(
		'core6/happ/hclib/wp-plugin-update-checker',
		'core6/happ/hclib/hcWpPremiumPlugin.php',
		)
	);

$disable['check_license'] = array(
	'panels'	=> array(
		),
	'files'	=> array(
		'core6/panels/admin/conf/upgrade/index_version.php',
		'core6/panels/admin/conf/upgrade/form.php',
		'core6/views/admin-header-license.php',
		)
	);

$disable['jquery'] = array(
	'panels'	=> array(
		),
	'files'	=> array(
		'core6/happ/assets/js/jquery-1.8.3.min.js',
		)
	);
?>
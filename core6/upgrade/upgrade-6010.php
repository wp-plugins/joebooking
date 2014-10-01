<?php
$ri = ntsLib::remoteIntegration();
if( $ri && ($ri == 'wordpress') )
{
	$ntsConf =& ntsConf::getInstance();

	$code = $ntsConf->get('licenseCode');
	// move license code to WordPress options

	$app = ntsLib::getAppProduct();
	$option_name = $app . '_license_code';
	update_site_option( $option_name, $code );
}

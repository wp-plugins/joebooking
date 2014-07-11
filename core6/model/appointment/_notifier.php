<?php
//echo 'action = ' . $mainActionName . '<br>';

if( isset($params['_silent']) && $params['_silent'] )
	return;

if( ! $object->getId() )
	return;

$conf =& ntsConf::getInstance();
$etm =& ntsEmailTemplateManager::getInstance();
$om =& objectMapper::getInstance();

$uif =& ntsUserIntegratorFactory::getInstance();
$integrator =& $uif->getIntegrator();

$lm =& ntsLanguageManager::getInstance();
$defaultLanguage = $lm->getDefaultLanguage();

$attachTo = array();
if( $conf->get('attachIcal') ){
	$attachTo = array(
		'appointment-approve-customer',
		'appointment-approve-provider',
		'appointment-request-customer',
		'appointment-request-provider',
		'appointment-change-customer',
		'appointment-change-provider',
		);
	}
$ntsdb =& dbWrapper::getInstance();

require( dirname(__FILE__) . '/_notifier_customer.php' );
require( dirname(__FILE__) . '/_notifier_provider.php' );
?>
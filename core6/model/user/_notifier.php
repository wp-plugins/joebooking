<?php
//echo 'action = ' . $mainActionName . '<br>';
if( isset($params['_silent']) && $params['_silent'] )
	return;


if( ! NTS_ENABLE_REGISTRATION ){
	if( $mainActionName == 'require_approval' )
		return;
	}

$conf =& ntsConf::getInstance();
$etm =& ntsEmailTemplateManager::getInstance();
$om =& objectMapper::getInstance();

$uif =& ntsUserIntegratorFactory::getInstance();
$integrator =& $uif->getIntegrator();

$lm =& ntsLanguageManager::getInstance();
$defaultLanguage = $lm->getDefaultLanguage();

$ntsdb =& dbWrapper::getInstance();

require( dirname(__FILE__) . '/_notifier_customer.php' );
require( dirname(__FILE__) . '/_notifier_admin.php' );
?>
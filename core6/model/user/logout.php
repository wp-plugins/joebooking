<?php
if( isset($_SESSION['nts_sos_user_id']) )
	unset($_SESSION['nts_sos_user_id']);
if( isset($_SESSION['home_call']) )
	unset($_SESSION['home_call']);

$uif =& ntsUserIntegratorFactory::getInstance();
$integrator =& $uif->getIntegrator();
$integrator->logout();
?>
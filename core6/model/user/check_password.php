<?php
$ntsdb =& dbWrapper::getInstance();
$om =& objectMapper::getInstance();

/* supplied username and password */
if( NTS_EMAIL_AS_USERNAME )
	$suppliedUsername = $object->getProp( 'email' );
else
	$suppliedUsername = $object->getProp( 'username' );
$suppliedPassword = $object->getProp( 'password' ); 

if( strlen($suppliedUsername) < 1 ){
	$actionResult = 0;
	$actionError = M("Wrong username or password");
	}
else {
	$uif =& ntsUserIntegratorFactory::getInstance();
	$integrator =& $uif->getIntegrator();

	if( $integrator->checkPassword($suppliedUsername, $suppliedPassword) ){
		$actionResult = 1;

		if( NTS_EMAIL_AS_USERNAME )
			$userInfo = $integrator->getUserByEmail( $suppliedUsername );
		else
			$userInfo = $integrator->getUserByUsername( $suppliedUsername );
		$object->setId( $userInfo['id'] );
		}
	else {
		$actionResult = 0;
		$actionError = M("Wrong username or password");
		}
	}
?>
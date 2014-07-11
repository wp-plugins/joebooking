<?php
global $NTS_CURRENT_USER;

/* set auth code if none yet */
$authCode = $NTS_CURRENT_USER->getProp( '_auth_code' );
if( ! strlen($authCode) ){
	$authCode = ntsLib::generateRand( 12 );
	$NTS_CURRENT_USER->setProp( '_auth_code', $authCode );

	$cm =& ntsCommandManager::getInstance();
	$cm->runCommand( $NTS_CURRENT_USER, 'update' );
	}
?>
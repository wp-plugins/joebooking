<?php
/* --- RETURN IF EMAIL DISABLED --- */
$conf =& ntsConf::getInstance();
if( $conf->get('emailDisabled') )
	return;

$userLang = $object->getLanguage();
if( ! $userLang )
	$userLang = $defaultLanguage;

/* --- GET TEMPLATE --- */
$key = 'user-' . $mainActionName . '-admin';

/* --- SKIP IF THIS NOTIFICATION DISABLED --- */
$currentlyDisabled = $conf->get( 'disabledNotifications' );
if( in_array($key, $currentlyDisabled) ){
	return;
	}

$templateInfo = $etm->getTemplate( $userLang, $key );

/* --- SKIP IF NO TEMPLATE --- */
if( ! $templateInfo ){
	return;
	}

/* --- FIND ADMINS --- */
$admins = $integrator->getAdmins();
if( ! $admins )
	return;

/* --- PREPARE MESSAGE --- */
/* build tags */
$tags = $om->makeTags_Customer( $object, 'internal' );

/* replace tags */
$subject = str_replace( $tags[0], $tags[1], $templateInfo['subject'] );
$body = str_replace( $tags[0], $tags[1], $templateInfo['body'] );

/* --- SEND EMAIL --- */
reset( $admins );
foreach( $admins as $admin ){
	if( $admin->getProp('_admin_level') != 'admin' )
		continue;

	$adminInfo = $admin->getByArray();

	if( ! isset( $adminInfo['email'] ) )
		continue;

	$adminEmail = trim( $adminInfo['email'] );
	if( ! $adminEmail )
		continue;

/* check if admin has disabled access to customers panel */
	$disabledPanels = $admin->getProp('_disabled_panels');
	if(
		in_array('admin/customers/edit', $disabledPanels ) OR
		in_array('admin/customers/notified', $disabledPanels )
		){
		continue;
		}

	$this->runCommand( $admin, 'email', array('body' => $body, 'subject' => $subject) );	
	}
?>
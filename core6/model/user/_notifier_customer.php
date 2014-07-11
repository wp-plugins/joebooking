<?php
/* --- RETURN IF EMAIL DISABLED --- */
$conf =& ntsConf::getInstance();
if( $conf->get('emailDisabled') )
	return;

/* --- SEND MESSAGE IF EMAIL DEFINED --- */
$userEmail = trim( $object->getProp('email') );
if( ! $userEmail )
	return;

$userLang = $object->getLanguage();
if( ! $userLang )
	$userLang = $defaultLanguage;

/* --- GET TEMPLATE --- */
$key = 'user-' . $mainActionName . '-user';

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

/* --- PREPARE MESSAGE --- */
/* build tags */
$tags = $om->makeTags_Customer( $object, 'external' );

$confirmKey = $object->getProp( '_confirmKey' );
$confirmLink = ntsLink::makeLink( 'anon/register/confirm_email', '', array('key' => $confirmKey) );
$confirmLink = '<a href="' . $confirmLink . '">' . M('Click here to confirm your email') . '</a>';

$tags[0][] = '{USER.CONFIRMATION_LINK}';
$tags[1][] = $confirmLink;

/* replace tags */
$subject = str_replace( $tags[0], $tags[1], $templateInfo['subject'] );
$body = str_replace( $tags[0], $tags[1], $templateInfo['body'] );

/* --- SEND EMAIL --- */
$this->runCommand( $object, 'email', array('body' => $body, 'subject' => $subject) );
?>
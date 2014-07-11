<?php
// generate random code for superadmin access
$sosCode = ntsLib::generateRand( 8 );
$now = time();

$sosSetting = $sosCode . ':' . $now;
$conf =& ntsConf::getInstance();
$conf->set( 'sosCode', $sosSetting );

// send to support
include_once( NTS_LIB_DIR . '/lib/email/ntsEmail.php' );
$email = 'support@hitcode.com';
$mailer = new ntsEmail;
$web_dir = ntsLib::webDirName( ntsLib::getFrontendWebpage() );
$mailer->setSubject( 'HitCode SOS Code: ' . $web_dir );

$url = ntsLib::webDirName( ntsLib::getFrontendWebpage() ) . '/?nts-sos=' . $sosCode;
$body = "<a href=\"$url\">$url</a>";
$mailer->setBody( $body );
$mailer->sendToOne( $email );
?>
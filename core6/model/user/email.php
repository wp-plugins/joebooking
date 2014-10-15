<?php
$userEmail = trim( $object->getProp('email') );
if( ! $userEmail )
	return;

include_once( NTS_LIB_DIR . '/lib/email/ntsEmail.php' );
$body = $params['body'];
$subject = $params['subject'];
$attachements = isset($params['attachements']) ? $params['attachements'] : array();
$fileAttachements = isset($params['fileAttachements']) ? $params['fileAttachements'] : array();

/* --- ADD COMMON HEADER & FOOTER --- */
$conf =& ntsConf::getInstance();
$commonHeader = $conf->get('emailCommonHeader');
$commonFooter = $conf->get('emailCommonFooter');

$need_nl2br = TRUE;
// check if there's <html> or <head> in message so don't add br
if( 
	( strpos($body, '<html>') !== FALSE ) OR
	( strpos($body, '<head>') !== FALSE )
	)
{
	$need_nl2br = FALSE;
}

$final_body = '';
$final_body .= $commonHeader. "\n";
if( $need_nl2br )
{
	$final_body .= nl2br($body);
}
else
{
	$final_body .= $body;
}
$final_body .= "\n" . $commonFooter;

$body = $final_body;

/* --- ADD RECIPIENT TAGS --- */
$om =& objectMapper::getInstance();
$fields = $om->getFields( 'user' );
$tags = array( array(), array() );

$allInfo = '';
foreach( $fields as $f ){
	$value = $object->getProp( $f[0] );
	if( isset($f[2]) && $f[2] == 'checkbox' ){
		$value = $value ? M('Yes') : M('No');
		}

	$tags[0][] = '{RECIPIENT.' . strtoupper($f[0]) . '}';
	$tags[1][] = $value;

/* build the -ALL- tag */
	$allInfo .= M($f[1]) . ': ' . $value . "\n";
	}
$tags[0][] = '{RECIPIENT.-ALL-}';
$tags[1][] = $allInfo;

/* --- PARSE RECIPIENT TAGS --- */
$body = str_replace( $tags[0], $tags[1], $body );
$subject = str_replace( $tags[0], $tags[1], $subject );

/* --- FINALLY SEND EMAIL --- */
$mailer = new ntsEmail;
$mailer->setLanguage( $object->getLanguage() );
$mailer->setSubject( $subject );
$mailer->setBody( $body );

if( $attachements ){
	reset( $attachements );
	foreach( $attachements as $att )
		$mailer->addAttachment( $att[1], $att[0] );
	}
if( $fileAttachements ){
	reset( $fileAttachements );
	foreach( $fileAttachements as $att )
		$mailer->addFileAttachment( $att[1], $att[0] );
	}

$mailer->sendToOne( $userEmail );
$actionResult = 1;
?>
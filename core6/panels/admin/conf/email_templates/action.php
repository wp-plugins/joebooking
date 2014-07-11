<?php
$conf =& ntsConf::getInstance();
$lm =& ntsLanguageManager::getInstance();

$lm =& ntsLanguageManager::getInstance();
$tm =& ntsEmailTemplateManager::getInstance();

$languages = $lm->getActiveLanguages();
$NTS_VIEW['lang'] = $_NTS['REQ']->getParam( 'lang' );
if( ! $NTS_VIEW['lang'] )
	$NTS_VIEW['lang'] = $languages[0];

if( $NTS_VIEW['lang'] == 'en-builtin' )
	$NTS_VIEW['lang'] = 'en';

if( $NTS_VIEW['lang'] != 'en' ){
	$languageConf = $lm->getLanguageConf( $NTS_VIEW['lang'] );
	if( isset($languageConf['charset']) ){
		header( 'Content-Type: text/html; charset=' . $languageConf['charset'] );
		}
	}
?>
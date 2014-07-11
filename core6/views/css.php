<?php
/* it is only used in Joomla now */
if( ! defined('NTS_HEADER_SENT') )
	define( 'NTS_HEADER_SENT', 1 );

/* this page defines NTS_CSS_URL */
global $_NTS;
$conf =& ntsConf::getInstance();
$theme = $conf->get( 'theme' );
if( isset($_REQUEST['nts-theme']) ){
	$theme = $_REQUEST['nts-theme'];
	}

if( preg_match('/^admin/', $_NTS['CURRENT_PANEL']) )
	$NTS_CSS_URL = ntsLink::makeLink('system/pull', '', array('what' => 'css', 'side' => 'admin') );
else
	$NTS_CSS_URL = ntsLink::makeLink('system/pull', '', array('theme' => $theme, 'files' => 'style.css|colors.css') );
?>
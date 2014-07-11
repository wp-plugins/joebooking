<?php
global $NTS_PAGE_TITLE; // backward compatibility
$conf =& ntsConf::getInstance();
$defaultPageTitle = $conf->get( 'htmlTitle' );
ntsView::setTitle( $defaultPageTitle );
?>
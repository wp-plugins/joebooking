<?php
$lng = $_NTS['REQ']->getParam( 'lang' );

global $NTS_CURRENT_USER;
$NTS_CURRENT_USER->setLanguage( $lng );

/* redirect back to the referrer */
$forwardTo = $_SERVER['HTTP_REFERER'];
ntsView::redirect( $forwardTo );
exit;
?>
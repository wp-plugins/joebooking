<?php
if( ! isset($css_files) )
	$css_files = array();
$css_files[] = 'happ/assets/bootstrap/css/_bootstrap3.css';
if( isset($NTS_VIEW['called_remotely']) && $NTS_VIEW['called_remotely'] ){
	$css_files[] = '//maxcdn.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.min.css';
}
else {
	$css_files[] = 'happ/assets/bootstrap/css/font-awesome.min.css';
}
//$css_files[] = 'happ/assets/bootstrap/css/font-awesome-ie7.min.css', 'lt IE 7';
$css_files[] = 'happ/assets/css/hitcode.css';

if( ! isset($js_files) )
	$js_files = array();
$js_files[] = 'happ/assets/js/jquery-1.8.3.min.js';
//$js_files[] = '//code.jquery.com/jquery-1.8.3.min.js';
$js_files[] = 'happ/assets/bootstrap/js/bootstrap.min.js';
$js_files[] = array('happ/assets/bootstrap/js/html5shiv.js', 'lt IE 9');
$js_files[] = array('happ/assets/bootstrap/js/respond.min.js', 'lt IE 9');
$js_files[] = 'happ/assets/js/hc.js';
?>
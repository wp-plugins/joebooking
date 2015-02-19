<?php
$css_files = array(
	'happ/assets/bootstrap/css/_bootstrap3.css',
	'happ/assets/bootstrap/css/datepicker.css',
	'happ/assets/bootstrap/css/font-awesome.min.css',
//	'//maxcdn.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.min.css',
//	array('happ/assets/bootstrap/css/font-awesome-ie7.min.css', 'lt IE 7'),
	'happ/assets/css/hitcode.css',
	);

$js_files = array(
	'happ/assets/js/jquery-1.8.3.min.js',
//	'//code.jquery.com/jquery-1.8.3.min.js',
	'happ/assets/bootstrap/js/bootstrap.min.js',
	array('happ/assets/bootstrap/js/html5shiv.js', 'lt IE 9'),
	array('happ/assets/bootstrap/js/respond.min.js', 'lt IE 9'),
	'happ/assets/bootstrap/js/bootstrap-datepicker.js',
	'happ/assets/bootstrap/js/bootstrap-multiselect.js',
	'happ/assets/js/hc.js',
//	array('core6/assets/js/html5shiv.js', 'lt IE 9'),
//	array('core6/assets/js/respond.min.js', 'lt IE 9'),
	);

if( 
	file_exists(dirname(__FILE__) . '/tinymce')
	&& 
	( ! (isset($nts_is_wordpress) && $nts_is_wordpress) )
	)
{
	$js_files[] = 'happ/assets/tinymce/tinymce.min.js';
}
?>
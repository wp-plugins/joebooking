<?php
$tabs = array();
$ntsdb =& dbWrapper::getInstance();

$tabs['browse'] = array(
	'title'	=> '<i class="fa fa-list"></i> ' . M('View'),
	);

$tabs['create'] = array(
	'title'	=> '<i class="fa fa-plus"></i> ' . M('Add'),
	);

$tabs['cats'] = array(
	'title'	=> '<i class="fa fa-list-ul"></i> ' . M('Categories'),
	);
?>
<?php
$parent = ntsLib::getVar( 'admin/notes::PARENT' );
$parentId = $parent->getId();
$parentClass = $parent->getClassName();

$ntsdb =& dbWrapper::getInstance();
$where = array(
	'obj_class'	=> array('=', $parentClass),
	'obj_id'	=> array('=', $parentId),
	'meta_name'	=> array('=', '_note'),
	);

$result = $ntsdb->select( array('id', 'meta_value', 'meta_data'), 'objectmeta', $where );
$entries = array();
while( $e = $result->fetch() ){
	$entries[] = $e;
	}
ntsLib::setVar( 'admin/notes::entries', $entries );
?>
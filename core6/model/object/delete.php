<?php
$actionResult = 1;

$ntsdb =& dbWrapper::getInstance();
$om =& objectMapper::getInstance();

$className = $object->getClassName();
$metaClass = $object->getMetaClass();
$id = $object->getId();

/* MAIN TABLE */
if( (! isset($skipMainTable)) || (! $skipMainTable) ){
	$actionDescription = 'Delete object data from the database';

	$tblName = $om->getTableForClass( $object->getClassName() );
	$whereString = "id = $id";

	$result = $ntsdb->delete( 
		$tblName,
		array( 
			'id' => array( '=', $id ),
			)
		);

	if( $result ){
		$actionResult = 1;
		}
	else {
		$actionResult = 0;
		$actionError = $ntsdb->getError();
		}
	}
	
/* delete meta */
if( $metaClass ){
	$result = $ntsdb->delete(
		'objectmeta',
		array(
			'obj_id'	=> array('=', $id),
			'obj_class'	=> array('=', $metaClass),
			)
		);

	if( $result ){
		$actionResult = 1;
		}
	else {
		$actionResult = 0;
		$actionError = $ntsdb->getError();
		}
	}

/* delete meta as child */
$childMetaClass = '_' . strtolower($className);
$result = $ntsdb->delete( 
	'objectmeta',
	array( 
		'meta_name'		=> array('=', $childMetaClass),
		'meta_value'	=> array('=', $id),
		)
	);

/* ACCOUNTING JOURNAL */
$journal_ids = $ntsdb->get_select( 
	'id',
	'accounting_journal',
	array(
		'obj_class'		=> array('=', $className),
		'obj_id'		=> array('=', $id),
		)
	);

if( $journal_ids )
{
	$result = $ntsdb->delete(
		'accounting_posting',
		array( 
			'journal_id'	=> array('IN', $journal_ids),
			)
		);
	$result = $ntsdb->delete(
		'accounting_journal',
		array( 
			'id'	=> array('IN', $journal_ids),
			)
		);
}
/* END OF ACCOUNTING JOURNAL */

/* delete logaudit */
$result = $ntsdb->delete( 
	'logaudit',
	array( 
		'obj_class'		=> array('=', $className),
		'obj_id'	=> array('=', $id),
		)
	);


if( $result ){
	$actionResult = 1;
	}
else {
	$actionResult = 0;
	$actionError = $ntsdb->getError();
	}
?>
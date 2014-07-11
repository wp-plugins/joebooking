<?php
$ntsdb =& dbWrapper::getInstance();

$fldName = $object->getProp( 'name' );
$formId = $object->getProp( 'form_id' );

/* ALTER TABLE IF THIS IS NOT A BUILT-IN FIELD */
$className = '';

$result = $ntsdb->select(
	'class',
	'forms',
	array(
		'id' => array( '=', $formId ),
		)
	);

$o = $result->fetch();
if( $o ){
	$className = $o['class'];
	}

if( $className ){
	if( $className != 'appoinment' ){
		$om =& objectMapper::getInstance();
		list( $coreProps, $metaProps ) = $om->getPropsForClass( 'user' );
		$biltInFields = array_keys( $coreProps );

		$builtIn = in_array($fldName, $biltInFields) ? true : false;

		if( ! $builtIn ){
			$result = $ntsdb->delete(
				'objectmeta',
				array(
					'meta_name'	=> array( '=', $fldName ),
					'obj_class'	=> array( '=', $className ),
					)
				);
			}
		}
	}
$actionResult = 1;
?>
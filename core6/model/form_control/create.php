<?php
$attr = $object->getProp( 'attr' );
if( is_array($attr) ){
	$attr = serialize( $attr );
	$object->setProp( 'attr', $attr );
	}

$validators = $object->getProp( 'validators' );
if( is_array($validators) ){
	$validators = serialize( $validators );
	$object->setProp( 'validators', $validators );
	}

/* ALTER TABLE IF THIS IS NOT A BUILT-IN FIELD */
$fldName = $object->getProp( 'name' );
$fldName = trim( $fldName );
$fldName = strtolower( $fldName );
if( ! $fldName ){
	$fldName = $object->getProp( 'title' );
	$fldName = ntsLib::sanitizeTitle( $fldName );
	$fldName = ntsLib::sanitizeSqlName( $fldName );
	}

$object->setProp( 'name', $fldName );

$actionResult = 1;
?>
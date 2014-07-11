<?php
$attr = $object->getProp( 'attr' );
if( is_array($attr) ){
	if( $attr )
		$attr = serialize( $attr );
	else
		$attr = '';
	$object->setProp( 'attr', $attr );
	}

$validators = $object->getProp( 'validators' );
if( is_array($validators) ){
	if( $validators )
		$validators = serialize( $validators );
	else
		$validators = '';
	$object->setProp( 'validators', $validators );
	}
?>
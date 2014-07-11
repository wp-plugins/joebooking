<?php
switch( $inputAction ){
	case 'display':
		if( $conf['value'] ){
			$conf['value'] = join( "\n", $conf['value'] );
			}
		else
			$conf['value'] = '';
		break;
	}

require( NTS_LIB_DIR . '/lib/form/inputs/textarea.php' );

switch( $inputAction ){
	case 'submit':
		$options = explode( "\n", $input );
		$input = array();
		reset( $options );
		foreach( $options as $o ){
			$o = trim( $o );
			$input[] = $o;
			}
		break;
	}
?>
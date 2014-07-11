<?php
switch( $inputAction ){
	case 'display':
		$input .= '<INPUT TYPE="hidden" ID="' . $conf['htmlId'] . '" NAME="' . $conf['id'] . '"' . ' VALUE="' . $conf['value'] . '">';
		$input .= htmlspecialchars( $conf['value'] );
		break;

	case 'submit':
		$input = $_NTS['REQ']->getParam( $handle );
		break;

	case 'check_submit':
		$input = isset( $_POST[$handle] ) ? true : false;
		break;
	}
?>
<?php
if( ! defined('NTS_PHONE_DIGITS_QTY') )
	define( 'NTS_PHONE_DIGITS_QTY', 10 );

switch( $inputAction ){
	case 'validate':
		$input = $_NTS['REQ']->getParam( $handle );
		// strip everything but digits
		$input = preg_replace( '/[^0-9]/', '', $input );

		if( (strlen($input) > 0) && (strlen($input) < NTS_PHONE_DIGITS_QTY) ){
			$validationFailed = true;
			$validationError = M('Phone number should have at least {NUM} digits', array('NUM' => NTS_PHONE_DIGITS_QTY) );
			}
		break;

	case 'display':
		$input .= '<INPUT class="form-control" TYPE="text" ID="' . $conf['id'] . '" NAME="' . $conf['id'] . '"';
		$input .= ' VALUE="' . htmlspecialchars( $conf['value'] ) . '"';
		$inputParams = ntsForm::makeInputParams( $conf['attr'] );
		if( $inputParams )
			$input .= ' ' . $inputParams;
		if( isset($conf['readonly']) && $conf['readonly'] )
			$input .= ' READONLY DISABLED CLASS="readonly"';
		$input .= '>';
		break;

	case 'submit':
		$input = $_NTS['REQ']->getParam( $handle );
		// strip everything but digits
		$input = preg_replace( '/[^0-9]/', '', $input );
		break;

	case 'check_submit':
		$input = isset( $_POST[$handle] ) ? true : false;
		break;

	}
?>
<?php
switch( $inputAction ){
	case 'display':
		$input .= '<INPUT class="form-control" TYPE="text" ID="' . $conf['htmlId'] . '" NAME="' . $conf['id'] . '"';
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
		break;

	case 'check_submit':
		$input = isset( $_POST[$handle] ) ? true : false;
		break;
	}
?>
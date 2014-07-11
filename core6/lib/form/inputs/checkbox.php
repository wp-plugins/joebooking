<?php
switch( $inputAction ){
	case 'display':
		if( isset($conf['readonly']) && $conf['readonly'] ){
			$input .= '<INPUT TYPE="hidden" ID="' . $conf['htmlId'] . '" NAME="' . $conf['id'] . '"';
			$inputParams = ntsForm::makeInputParams( $conf['attr'] );
			if( $inputParams )
				$input .= ' ' . $inputParams;
			if( isset($conf['box_value']) ){
				$input .= ' VALUE="' . $conf['box_value'] . '"';
				}
			elseif( $conf['value'] ){
				$input .= ' VALUE="' . $conf['value'] . '"';
				}
			$input .= '>';
			$input .= '[X]';
			}
		else {
			$input .= '<INPUT TYPE="checkbox" ID="' . $conf['htmlId'] . '" NAME="' . $conf['id'] . '"';
			$inputParams = ntsForm::makeInputParams( $conf['attr'] );
			if( $inputParams )
				$input .= ' ' . $inputParams;
			if( $conf['value'] ){
				$input .= ' CHECKED';
				}
			if( isset($conf['box_value']) )
				$input .= ' VALUE="' . $conf['box_value'] . '"';
			if( isset($conf['readonly']) && $conf['readonly'] )
				$input .= ' READONLY DISABLED CLASS="readonly"';
			$input .= '>';

			if( isset($conf['label']) )
			{
				$input .= $conf['label'];
			}
			}
		break;

	case 'submit':
		$input = ( $_NTS['REQ']->getParam($handle) ) ? 1 : 0;
		break;

	case 'check_submit':
		$input = true;
		break;
	}
?>
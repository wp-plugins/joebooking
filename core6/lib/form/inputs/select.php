<?php
switch( $inputAction ){
	case 'display':
		$input .= '<SELECT ID="' . $conf['htmlId'] . '" NAME="' . $conf['id'] . '"';
		if( ! isset($conf['attr']['style']) )
			$conf['attr']['style'] = 'width: auto;';
		else
			$conf['attr']['style'] .= '; width: auto;';

		$class = array('form-control');
		if( isset($conf['attr']['class']) )
			$class[] = $conf['attr']['class'];
		unset( $conf['attr']['class'] );

		$inputParams = ntsForm::makeInputParams( $conf['attr'] );
		if( $inputParams )
			$input .= ' ' . $inputParams;
		if( isset($conf['readonly']) && $conf['readonly'] )
		{
			$input .= ' READONLY DISABLED';
			$class[] = 'readonly';
		}
		$input .= ' CLASS="' . join(' ', $class) . '"';
		$input .= '>';

		if( isset($conf['options']) ){
			reset( $conf['options'] );
			foreach( $conf['options'] as $optionConf ){
				// optgroup
				if( count($optionConf) == 1 ){
					$input .= '<OPTGROUP LABEL="' . $optionConf[0] . '">';
					}
				// option
				else {
					$optionConf[0] = trim( $optionConf[0] );
					$optionConf[1] = trim( $optionConf[1] );
					$selected = ($optionConf[0] == $conf['value']) ? ' SELECTED' : '';
				// option class
					if( isset($optionConf[2]) && $optionConf[2] )
						$input .= '<OPTION CLASS="' . $optionConf[2] . '" VALUE="' . $optionConf[0] . '"' . $selected . '>' . $optionConf[1];
					else
						$input .= '<OPTION VALUE="' . $optionConf[0] . '"' . $selected . '>' . $optionConf[1];
					}
				}
			}

		$input .= '</SELECT>';
		break;

	case 'submit':
		$input = $_NTS['REQ']->getParam( $handle );
		break;

	case 'check_submit':
		$input = isset( $_POST[$handle] ) ? true : false;
		break;
	}
?>
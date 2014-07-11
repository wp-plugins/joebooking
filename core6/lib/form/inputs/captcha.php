<?php
$now = time();

switch( $inputAction ){
	case 'validate':
		$input = $_NTS['REQ']->getParam( $handle );
		$hidden1 = $handle . '_ts';
		$hidden2 = $handle . '_hash';

		$value1 = $_NTS['REQ']->getParam( $hidden1 );
		$value2 = $_NTS['REQ']->getParam( $hidden2 );

		// allow time not older than 10 minutes
		if( $now > $value1 + 10 * 60 ){
			$validationFailed = true;
			$validationError = M('Wrong Code');
			}
		else {
			$test2 = md5($input + $value1);
			if( $test2 != $value2 ){
				$validationFailed = true;
				$validationError = M('Wrong Code');
				}
			}
		break;

	case 'display':
		$digits = array(
			'0'	=> array( 1,1,1, 1,0,1, 1,0,1, 1,0,1, 1,1,1 ),
			'1'	=> array( 0,0,1, 0,1,1, 1,0,1, 0,0,1, 0,0,1 ),
			'2'	=> array( 1,1,1, 0,0,1, 1,1,1, 1,0,0, 1,1,1 ),
			'3'	=> array( 1,1,1, 0,0,1, 0,1,1, 0,0,1, 1,1,1 ),
			'4'	=> array( 1,0,1, 1,0,1, 1,1,1, 0,0,1, 0,0,1 ),
			'5'	=> array( 1,1,1, 1,0,0, 1,1,1, 0,0,1, 1,1,1 ),
			'6'	=> array( 1,1,1, 1,0,0, 1,1,1, 1,0,1, 1,1,1 ),
			'7'	=> array( 1,1,1, 0,0,1, 0,1,0, 1,0,0, 1,0,0 ),
			'8'	=> array( 1,1,1, 1,0,1, 1,1,1, 1,0,1, 1,1,1 ),
			'9'	=> array( 1,1,1, 1,0,1, 1,1,1, 0,0,1, 1,1,1 ),
			);

		$code = mt_rand( 0, 999 );
		$code = sprintf( "%03d", $code );

		$cols = 3;
		$rows = 5;

		$input .=<<<EOT
<style>
#nts table#nts-captcha1, #nts table#nts-captcha2 {
	padding: 0 0;
	margin: 0 0;
	border-width: 0px;
	border-collapse: collapse;
	}
#nts table#nts-captcha1 {
	margin: 0 0 0 0;
	}
#nts table#nts-captcha1 td, #nts table#nts-captcha2 td {
	padding: 0 0;
	margin: 0 0;
	font-size: 4px;
	line-height: 4px;
	border-width: 0px;
	}
#nts table#nts-captcha1 td {
	padding: 0 2px;
	}
#nts table#nts-captcha2 td {
	padding: 0 2px;
	}
#nts table#nts-captcha2 td.on {
	background-color: #006600;
	}
#nts table#nts-captcha2 td.off {
	background-color: #ffffff;
	}
</style>

EOT;

	$input .= '<ul class="list-inline">';
	$input .= '<li>';
		$input .= '<p>';
		$input .= '<INPUT class="form-control" TYPE="text" ID="' . $conf['htmlId'] . '" NAME="' . $conf['id'] . '"';
		$input .= ' VALUE="' . htmlspecialchars( $conf['value'] ) . '"';
		$inputParams = ntsForm::makeInputParams( $conf['attr'] );
		if( $inputParams )
			$input .= ' ' . $inputParams;
		$input .= '>';
		$input .= '</p>';
	$input .= '</li>';

	$input .= '<li>';
		$input .= '<table id="nts-captcha1">';
		$input .= '<tr>';
		for( $s = 0; $s < strlen($code); $s++ ){
			$thisDigit = substr( $code, $s, 1 );
			$input .= '<td>';
			$input .= '<table id="nts-captcha2">';
			for( $r = 0; $r < $rows; $r++ ){
				$input .= '<tr>';
				for( $c = 0; $c < $cols; $c++ ){
					$index = $r * $cols + $c;
					$class = $digits[ $thisDigit ][ $index ] ? 'on' : 'off';
					$input .= '<td class="' . $class . '">';
					$input .= '&nbsp;';
					$input .= '</td>';
					}
				$input .= '</tr>';
				}
			$input .= '</table>';
			$input .= '</td>';
			}
		$input .= '</tr>';
		$input .= '</table>';
	$input .= '</li>';

	$input .= '</ul>';

		$hidden1 = $conf['id'] . '_ts';
		$hidden2 = $conf['id'] . '_hash';

		$value1 = $now;
		$value2 = md5($code + $value1);

		$input .= '<INPUT TYPE="hidden" NAME="' . $hidden1 . '" VALUE="' . $value1 . '">';
		$input .= '<INPUT TYPE="hidden" NAME="' . $hidden2 . '" VALUE="' . $value2 . '">';

		break;

	case 'submit':
		$input = $_NTS['REQ']->getParam( $handle );
		break;

	case 'check_submit':
		$input = isset( $_POST[$handle] ) ? true : false;
		break;
	}
?>
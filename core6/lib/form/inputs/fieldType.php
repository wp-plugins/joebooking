<?php
$conf['options'] = array(
	array('text',		M('Text') ),
	array('checkbox',	M('Yes/No') ),
	array('textarea',	M('Textarea') ),
	array('select',		M('Select') ),
	);
$conf['attr']['onChange'] = 'toggleSizeControl( this.value );';

require( NTS_LIB_DIR . '/lib/form/inputs/select.php' );

switch( $inputAction ){
	case 'display':
//		$jsFile = NTS_LIB_DIR . '/lib/js/functions.js';
//		$jsCode = ntsLib::fileGetContents( $jsFile );
		$input .=<<<EOT

<SCRIPT LANGUAGE="JavaScript">
function toggleSizeControl( typeSelected ){
	switch( typeSelected ){
	// TEXT
		case 'text':
			jQuery('#requireOptions-Textarea').hide();
			jQuery('#requireOptions-Select' ).hide();
			jQuery('#requireOptions-Checkbox' ).hide();
			jQuery('#validators_select' ).hide();
			jQuery('#requireOptions-Text' ).show();
			jQuery('#validators' ).show();
			break;

	// YES/NO
		case 'checkbox':
			jQuery('#requireOptions-Textarea' ).hide();
			jQuery('#requireOptions-Text' ).hide();
			jQuery('#validators' ).hide().hide();
			jQuery('#requireOptions-Select' ).hide();
			jQuery('#validators_select' ).hide();
			jQuery('#requireOptions-Checkbox' ).show()
			break;

	// TEXTAREA
		case 'textarea':
			jQuery('#requireOptions-Text' ).hide();
			jQuery('#requireOptions-Select' ).hide();
			jQuery('#requireOptions-Checkbox' ).hide();
			jQuery('#validators_select' ).hide();
			jQuery('#requireOptions-Textarea' ).show();
			jQuery('#validators' ).show();
			break;

	// SELECT
		case 'select':
			jQuery('#requireOptions-Select' ).show();
			jQuery('#validators_select' ).show();
			jQuery('#requireOptions-Textarea' ).hide();
			jQuery('#requireOptions-Text' ).hide();
			jQuery('#requireOptions-Checkbox' ).hide();
			jQuery('#validators' ).hide();
			break;
		}
	}
</SCRIPT>

EOT;
		break;
	}
?>
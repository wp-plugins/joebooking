<?php
class ntsValidator
{
	var $checkValue = NULL;
	var $formValues = array();
	var $validationParams = array();
	var $controlConf = array();
	var $error = NULL;

	function __construct()
	{
		$this->reset();
	}

	function reset()
	{
		$this->error = NULL;
	}

	function setError( $str )
	{
		$this->error = $str;
	}

	function getError()
	{
		return $this->error;
	}

	function getValidatorsFor( $type )
	{
		$return = array();
	// get my method names
		$methods = get_class_methods( $this );
		reset( $methods );
		$suffix = '_display';
		foreach( $methods as $m )
		{
			if( substr($m, -strlen($suffix)) == $suffix )
			{
				list( $types, $title ) = $this->$m();
				if( ! in_array($type, $types) )
					continue;
				$validator_name = substr($m, 0, -strlen($suffix));
				$ok = array(
					$validator_name,
					$title,
					$types
					);
				$return[ $validator_name ] = $ok;
			}
		}
		return $return;
	}

	function getValidatorInfo( $key )
	{
		$m = $key . '_display';
		if( method_exists($this, $m) )
		{
			list( $types, $title ) = $this->$m();
			$return = array( $key, $title );
		}
		else
		{
			$return = array( $key, 'N/A' );
		}
		return $return;
	}

	function run( $code )
	{
		$suffix = '.php';
		if( substr($code, -strlen($suffix)) == $suffix )
			$code = substr($code, 0, -strlen($suffix));
		return $this->$code();
	}

/* VALIDATIONS */
	function checked()
	{
		$return = $this->checkValue ? TRUE : FALSE;
		return $return;
	}

	function checked_display()
	{
		return array( array('checkbox'), M('Checkbox On') );
	}

	function integer()
	{
		$return = TRUE;
		if ( preg_match("/[^0-9]/", $this->checkValue) )
			$return = FALSE;
		return $return;
	}

	function integer_display()
	{
		return array( array('text', 'textarea'), M('Integers Only') );
	}

	function greaterEqualThan()
	{
		if( isset($this->validationParams['compareWithField']) ){
			$compareWithField = $this->validationParams['compareWithField'];
			$compareWith = $this->formValues[ $compareWithField ];
			}
		else {
			$compareWith = $this->validationParams['compareWith'];
			}

		$this->checkValue = trim( $this->checkValue );
		$return = ( $this->checkValue < $compareWith ) ? FALSE : TRUE;
		return $return;
	}

	function greaterThan()
	{
		$return = TRUE;
		if( isset($this->validationParams['compareFields']) )
		{
			for( $ii = 0; $ii < count($this->validationParams['compareFields']); $ii++ )
			{
				$fa = $this->validationParams['compareFields'][$ii];
				$me = $this->formValues[ $fa[0] ];
				$other = $this->formValues[ $fa[1] ];

				if( $ii == ( count($this->validationParams['compareFields']) - 1 ) )
				{ // last one
					if( $me <= $other )
					{
						$return = FALSE;
						break;
					}
				}
				else
				{
					if( $me > $other )
					{
						$return = TRUE;
						break;
					}
					if( $me < $other )
					{
						$return = FALSE;
						break;
					}
				}
			}
		}
		else {
			$this->checkValue = trim( $this->checkValue );
			if( isset($this->validationParams['compareWithField']) ){
				$compareWithField = $this->validationParams['compareWithField'];
				$compareWith = $this->formValues[ $compareWithField ];
				}
			else {
				$compareWith = $this->validationParams['compareWith'];
				}

			if( $this->checkValue <= $compareWith ){
				$return = FALSE;
				}
			}
		return $return;
	exit;
	}

	function lessEqualThan()
	{
		if( isset($this->validationParams['compareWithField']) ){
			$compareWithField = $this->validationParams['compareWithField'];
			$compareWith = $this->formValues[ $compareWithField ];
			}
		else {
			$compareWith = $this->validationParams['compareWith'];
			}

		$this->checkValue = trim( $this->checkValue );
		$return = ( $this->checkValue > $compareWith ) ? FALSE : TRUE;
		return $return;
	}

	function lowercaseLetterNumberUnderscore()
	{
		$return = TRUE;
		if ( preg_match("/[^0-9a-z_]/", $this->checkValue) )
			$return = FALSE;
		return $return;
	}

	function lowercaseLetterNumberUnderscore_display()
	{
		return array( array('text', 'textarea'), M('Lowercase English Letters, Numbers, Underscores Only') );
	}

	function letterNumberUnderscore()
	{
		$return = TRUE;
		if ( preg_match("/[^0-9a-z_]/i", $this->checkValue) )
			$return = FALSE;
		return $return;
	}

	function letterNumberUnderscore_display()
	{
		return array( array('text', 'textarea'), M('English Letters, Numbers, Underscores Only') );
	}

	function notEmpty()
	{
		$return = TRUE;
		if( is_array($this->checkValue) )
		{
			if( ! $this->checkValue )
				$return = FALSE;
		}
		else
		{
			$this->checkValue = trim( $this->checkValue );
			if( ! strlen($this->checkValue) ){
				$return = FALSE;
				}
		}
		return $return;
	}

	function notEmpty_display()
	{
		return array( array('mobilephone', 'text', 'textarea'), M('Required') );
	}

	function notEqualTo()
	{
		if( isset($this->validationParams['compareWithField']) ){
			$compareWithField = $this->validationParams['compareWithField'];
			$compareWith = $this->formValues[ $compareWithField ];
			}
		else {
			$compareWith = $this->validationParams['compareWith'];
			}

		$this->checkValue = trim( $this->checkValue );

		$return = TRUE;
		// COMPARE WITH FIELD
		if( $this->checkValue == $compareWith ){
			$return = FALSE;
			}
		return $return;
	}

	function notFirstOptionInSelect()
	{
		$return = TRUE;
		$this->checkValue = trim( $this->checkValue );

		// ALLOW EMPTY VALUE
		if( ! $this->checkValue ){
			}
		else {
			if( isset($this->controlConf['options'][0]) ){
				$firstOption = $this->controlConf['options'][0][0];
				if( $firstOption == $this->checkValue ){
					$return = FALSE;
					}
				}
			}
		return $return;
	}

	function notFirstOptionInSelect_display()
	{
		return array( array('select'), M('Not The First Option In Select List') );
	}

	function number()
	{
		$return = TRUE;
		$this->checkValue = str_replace( ' ', '',  $this->checkValue );
		if ( preg_match("/[^0-9.+-]/", $this->checkValue) )
			$return = FALSE;
		return $return;
	}

	function number_display()
	{
		return array( array('text', 'textarea'), M('Numbers only') );
	}

	function oneEntryOnly_display()
	{
		return array( array('text', 'textarea'), M('One Entry Only') );
	}

	function phone10()
	{
		$return = TRUE;
		$numberOfDigits = 10;
		$countMatches = 0;
		if( preg_match_all("/\d/", $this->checkValue, $matches) )
		{
			$countMatches = count($matches[0]);
		}
		if ( $countMatches != $numberOfDigits )
			$return = FALSE;
		return $return;
	}

	function phone10_display()
	{
		return array( array('text', 'textarea'), M('Valid Phone Number (10 digits)') );
	}

	function phone11()
	{
		$return = TRUE;
		$numberOfDigits = 11;
		$countMatches = 0;
		if( preg_match_all("/\d/", $this->checkValue, $matches) )
		{
			$countMatches = count($matches[0]);
		}
		if ( $countMatches != $numberOfDigits )
			$return = FALSE;
		return $return;
	}

	function phone11_display()
	{
		return array( array('text', 'textarea'), M('Valid Phone Number (11 digits)') );
	}

	function url()
	{
		$return = TRUE;
		if ( ! preg_match("/^[a-zA-Z]+[:\/\/]+[A-Za-z0-9\-_]+\\.+[A-Za-z0-9\.\/%&=\?\-_]+$/i", $this->checkValue) )
			$return = FALSE;
		return $return;
	}

	function url_display()
	{
		return array( array('text', 'textarea'), M('Valid URL Syntax') );
	}

	function email()
	{
		$return = TRUE;
		if( ! strlen($this->checkValue) )
			return $return;
		
// thanks to http://www.iamcal.com/publish/articles/php/parsing_email/
		$addr_spec = '([^\\x00-\\x20\\x22\\x28\\x29\\x2c\\x2e\\x3a-\\x3c'.
					'\\x3e\\x40\\x5b-\\x5d\\x7f-\\xff]+|\\x22([^\\x0d'.
					'\\x22\\x5c\\x80-\\xff]|\\x5c[\\x00-\\x7f])*\\x22)'.
					'(\\x2e([^\\x00-\\x20\\x22\\x28\\x29\\x2c\\x2e'.
					'\\x3a-\\x3c\\x3e\\x40\\x5b-\\x5d\\x7f-\\xff]+|'.
					'\\x22([^\\x0d\\x22\\x5c\\x80-\\xff]|\\x5c\\x00'.
					'-\\x7f)*\\x22))*\\x40([^\\x00-\\x20\\x22\\x28'.
					'\\x29\\x2c\\x2e\\x3a-\\x3c\\x3e\\x40\\x5b-\\x5d'.
					'\\x7f-\\xff]+|\\x5b([^\\x0d\\x5b-\\x5d\\x80-\\xff'.
					']|\\x5c[\\x00-\\x7f])*\\x5d)(\\x2e([^\\x00-\\x20'.
					'\\x22\\x28\\x29\\x2c\\x2e\\x3a-\\x3c\\x3e\\x40'.
					'\\x5b-\\x5d\\x7f-\\xff]+|\\x5b([^\\x0d\\x5b-'.
					'\\x5d\\x80-\\xff]|\\x5c[\\x00-\\x7f])*\\x5d))*';

		if ( ! preg_match("!^$addr_spec$!", $this->checkValue)){
			$return = FALSE;
			}
		return $return;
	}

	function email_display()
	{
		return array( array('text', 'textarea'), M('Valid Email Required') );
	}

	function confirmPassword()
	{
		$return = TRUE;
		$mainPasswordField = $this->validationParams[ 'mainPasswordField' ];
		$mainPasswordFieldValue = $this->formValues[ $mainPasswordField ];

		$this->checkValue = trim( $this->checkValue );

		// NOT EQUAL TO THE ALLOW EMPTY VALUE
		if( $mainPasswordFieldValue != $this->checkValue ){
			$return = FALSE;
			}
		return $return;
	}

	function checkUsername()
	{
		$return = TRUE;
		$uif =& ntsUserIntegratorFactory::getInstance();
		$integrator =& $uif->getIntegrator();

		$myWhere = array();
		$myWhere['username'] = array('=', $this->checkValue);

		if( isset($this->formValues['id']) && ($this->formValues['id'] > 0) ){
			$myId = $this->formValues['id'];
			$myWhere['id'] = array('<>', $myId);
			}

		$count = $integrator->countUsers( $myWhere );
		if( $count ){
			$return = FALSE;
			}
		return $return;
	}

	function checkUsername_display()
	{
		return array( array('text', 'textarea'), M('Unique') . ': ' . M('Username') );
	}

	function checkUserEmail()
	{
		$return = TRUE;
		if( ! (defined('NTS_ALLOW_DUPLICATE_EMAILS') && NTS_ALLOW_DUPLICATE_EMAILS) )
		{
			$uif =& ntsUserIntegratorFactory::getInstance();
			$integrator =& $uif->getIntegrator();

			$myWhere = array();
			if( strlen($this->checkValue) )
			{
				$myWhere['email'] = array('=', $this->checkValue);
				if( isset($this->formValues['id']) && ($this->formValues['id'] > 0) )
				{
					$myId = $this->formValues['id'];
					$myWhere['id'] = array('<>', $myId);
				}

				$count = $integrator->countUsers( $myWhere );
				if( $count )
				{
					$return = FALSE;
				}
			}
		}
		return $return;
	}

	function checkUserEmail_display()
	{
		return array( array('text', 'textarea'), M('Unique') . ': ' . M('Email') );
	}

	function checkUniqueProperty()
	{
		$prefix = isset($this->validationParams['prefix']) ? $this->validationParams['prefix'] : '';
		$return = TRUE;
		$ntsdb =& dbWrapper::getInstance();
		$propName = $this->validationParams['prop'];
		$className = $this->validationParams['class'];
		$om =& objectMapper::getInstance();
		$tblName = $om->getTableForClass( $className );

		$where = array();
		$where[ $propName ] = array( '=', $prefix . $this->checkValue ); 

		if( isset($this->formValues['id']) && ($this->formValues['id'] > 0) ){
			$myId = $this->formValues['id'];
			$where['id'] = array('<>', $myId );
			}

		if( isset($this->validationParams['skipMe']) && $this->validationParams['skipMe'] ){
			if( isset($this->formParams['myId']) && $this->formParams['myId'] ){
				$myId = $this->formParams['myId'];
				$where['id'] = array('<>', $myId );
				}
			}

		if( isset($this->validationParams['also']) && $this->validationParams['also'] ){
			reset($this->validationParams['also']);
			foreach( $this->validationParams['also'] as $k => $a ){
				$where[$k] = $a;
				}
			}

		$count = $ntsdb->count( $tblName, $where );
		if( $count > 0 ){
			$return = FALSE;
			}
		return $return;
	}

	function strongPassword()
	{
		$return = TRUE;
		if( strlen($this->checkValue) )
		{
		// at least 8 chars
			if( strlen($this->checkValue) < 8 )
			{
				$return = FALSE;
				$this->setError( M('Use at least 8 characters') );
			}
			elseif( ! ( preg_match('/\d/', $this->checkValue) && preg_match('/\w/', $this->checkValue) ) )
			{
				$return = FALSE;
				$this->setError( M('Use both letters and numbers') );
			}
		}
		return $return;
	}
}
?>
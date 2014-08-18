<?php
class ntsForm2
{
	var $formId;
	var $readonly = FALSE;

	function __construct( $formId = '' )
	{
		if( ! $formId )
		{
			$formId = ntsLib::generateRand(6, array('caps' => FALSE));
		}
		$this->formId = 'nts_form_' . $formId;
	}

	function start( $regen_id = FALSE )
	{
		if( $regen_id )
		{
			$formId = ntsLib::generateRand(6, array('caps' => FALSE));
			$this->formId = 'nts_form_' . $formId;
		}
		$startUp = '';
		$startUp .= "\n" . '<FORM class="form-horizontal form-condensed form-striped" METHOD="post"';
		$startUp .= ' ACTION="' . ntsLib::getRootWebpage() . '"';
		$startUp .=  ' ENCTYPE="multipart/form-data"';
		$startUp .= ' NAME="' . $this->formId . '"';
		$startUp .= ' ID="' . $this->formId . '"';
		$startUp .= ">\n";
		return $startUp;
	}

	function end()
	{
		$return = '';
		$return .= '</FORM>';
		return $return;
	}

	function input( $type, $inputArray )
	{
		$input = $this->build_input($type, $inputArray);
		$return = $input->view;
		return $return;
	}

	function build_input( $type, $inputArray )
	{
		$return = '';

		if( $this->readonly )
			$inputArray[ 'readonly' ] = 1;

		$conf = array_merge( ntsForm::_inputDefaults(), $inputArray );

		if( ! isset($conf['value']) )
		{
			$conf['value'] = '';
			if( isset($conf['default']) )
			{
				$conf['value'] = $conf['default'];
			}
		}

		if( ! isset($conf['error']) )
		{
			$conf['error'] = '';
		}

//		$conf['error'] = $this->_getErrorForInput( $conf['id'] );
		$input = '';
		$inputAction = 'display';
		if( ! isset($conf['htmlId']) )
		{
			$conf['htmlId'] = $this->formId . $conf['id'];
		}

	// INCLUDE THE RIGHT INPUT FILE
		$inputFile = NTS_LIB_DIR . '/lib/form/inputs/' . $type . '.php';

		if( ! file_exists($inputFile) )
		{
			echo $shortName . ' file does not exist!<BR';
			return;
		}

		$conf['id'] = ntsView::setRealName( $conf['id'] );
		require( $inputFile );

		if( isset($conf['after']) )
			$input .= ' ' . $conf['after'];

		if( isset($conf['before']) )
			$input = $conf['before'] . ' ' . $input;

		$return = new stdClass;
		$return->type = $type;
		$return->view = $input;
		$return->error = $conf['error'];
		$return->help = $conf['help'];

		return $return;
	}

	function make_post_params( $panel, $action = '', $supplied_params = array() )
	{
		global $_NTS, $NTS_PERSISTENT_PARAMS;
		$params = array();

		if( preg_match('/^\-current\-/', $panel) )
		{
			$replaceFrom = '-current-';
			$replaceTo = $_NTS['CURRENT_PANEL'];

			if( strlen($replaceTo) && preg_match('/\/\.\./', $panel) )
			{
				$downCount = substr_count( $panel, '/..' );
				$re = "/^(.+)(\/[^\/]+){" . $downCount. "}$/U";
				preg_match($re, $replaceTo, $ma);
				$replaceFrom = '-current-' . str_repeat('/..', $downCount);
				$replaceTo = $ma[1];
			}
			$panel = str_replace( $replaceFrom, $replaceTo, $panel );
		}

		$params[ NTS_PARAM_PANEL ] = $panel;
		if( strlen($action) )
			$params[ NTS_PARAM_ACTION ] = $action;

		if( $NTS_PERSISTENT_PARAMS )
		{
			reset( $NTS_PERSISTENT_PARAMS );
		/* global */
			if( isset($NTS_PERSISTENT_PARAMS['/']) )
			{
				reset( $NTS_PERSISTENT_PARAMS['/'] );
				foreach( $NTS_PERSISTENT_PARAMS['/'] as $p => $v )
				{
					$params[ $p ] = $v;
				}
			}
		/* above panel */
			reset( $NTS_PERSISTENT_PARAMS );
			foreach( $NTS_PERSISTENT_PARAMS as $pan => $pampam )
			{
				if( substr($panel, 0, strlen($pan) ) != $pan )
					continue;
				reset( $pampam );
				foreach( $pampam as $p => $v )
					$params[ $p ] = $v;
			}
		}

		foreach( $supplied_params as $p => $v )
		{
			if( ($v === NULL) OR ($v == '-reset-') )
			{
				unset( $params[$p] );
			}
			else
			{
				$params[ $p ] = $v;
			}
		}

		reset( $params );
		$postParts = array();
		foreach( $params as $p => $v )
		{
			$realP = ntsView::setRealName( $p );
			if( is_array($v) )
			{
				foreach( $v as $va )
					$postParts[] = '<INPUT TYPE="hidden" NAME="' . $realP . '[]" VALUE="' . $va . '">';
			}
			else
				$postParts[] = '<INPUT TYPE="hidden" NAME="' . $realP . '" VALUE="' . $v . '">';
		}

		$post = join( "\n", $postParts );
		return $post;
		}
}

class ntsFormFactory {
	function ntsFormFactory(){
		$this->forms = array();
		$this->_fileCache = array();
		}

	function &makeForm( $formFile, $defaults = array(), $key = '', $idPrefix = '' )
	{
		$formFile = str_replace( '\\', '/', $formFile );
		$index = ( strlen($key) ) ? $formFile . $key : $formFile;	
		if( ! isset($this->forms[$index]) )
		{
			$form = new ntsForm( $formFile, $defaults, $idPrefix );
			$this->forms[$index] = $form;
		}
		return $this->forms[$index];
	}

	// Singleton stuff
	static function &getInstance(){
		return ntsLib::singletonFunction( 'ntsFormFactory' );
		}
	}

class ntsForm {
	function ntsForm( $formFile, $defaults = array(), $idPrefix = '' ){
		$this->formFile = $formFile;

		$this->defaults = $defaults;
		$this->inputs = array();

		$this->errors = array();
		$this->values = array();
		$this->params = array();

		$this->requiredFields = 0;
		$this->formAction = 'display';
		$this->readonly = false;

		$this->skipRequiredAlert = false;
		$this->valid = true;

		$this->_controlsCache = array();
		$this->useCache = false;
		$this->formId = '';
		$this->idPrefix = $idPrefix;
		$this->noprint = FALSE;
		}

	static function start( $formId = '' )
	{
		if( ! $formId )
		{
			$formId = ntsLib::generateRand(6, array('caps' => FALSE));
			$formId = 'nts_form_' . $formId;
		}
		$startUp = '';
		$startUp .= "\n" . '<FORM class="form-horizontal form-condensed form-striped" METHOD="post"';
		$startUp .= ' ACTION="' . ntsLib::getRootWebpage() . '"';
		$startUp .=  ' ENCTYPE="multipart/form-data"';
		$startUp .= ' NAME="' . $formId . '"';
		$startUp .= ' ID="' . $formId . '"';
		$startUp .= ">\n";
		return $startUp;
	}

	function setParams( $params ){
		$this->params = $params;
		}

/* Builds the set of hidden fields for panel, action, and params */
	function makePostParams( $panel, $action = '', $params = array() ){
		global $_NTS, $NTS_PERSISTENT_PARAMS;

		if( preg_match('/^\-current\-/', $panel) ){
			$replaceFrom = '-current-';
			$replaceTo = $_NTS['CURRENT_PANEL'];

			if( strlen($replaceTo) && preg_match('/\/\.\./', $panel) ){
				$downCount = substr_count( $panel, '/..' );
				$re = "/^(.+)(\/[^\/]+){" . $downCount. "}$/U";
				preg_match($re, $replaceTo, $ma);
				$replaceFrom = '-current-' . str_repeat('/..', $downCount);
				$replaceTo = $ma[1];
				}
			$panel = str_replace( $replaceFrom, $replaceTo, $panel );
			}

		$params[ NTS_PARAM_PANEL ] = $panel;
		if( strlen($action) )
			$params[ NTS_PARAM_ACTION ] = $action;

		if( $NTS_PERSISTENT_PARAMS ){
			reset( $NTS_PERSISTENT_PARAMS );
		/* global */
			if( isset($NTS_PERSISTENT_PARAMS['/']) ){
				reset( $NTS_PERSISTENT_PARAMS['/'] );
				foreach( $NTS_PERSISTENT_PARAMS['/'] as $p => $v ){
					$params[ $p ] = $v;
					}
				}
		/* above panel */
			reset( $NTS_PERSISTENT_PARAMS );
			foreach( $NTS_PERSISTENT_PARAMS as $pan => $pampam ){
				if( substr($panel, 0, strlen($pan) ) != $pan )
					continue;
				reset( $pampam );
				foreach( $pampam as $p => $v )
					$params[ $p ] = $v;
				}
			}

		reset( $params );
		$postParts = array();
		foreach( $params as $p => $v ){
			$realP = ntsView::setRealName( $p );
			if( is_array($v) ){
				foreach( $v as $va )
					$postParts[] = '<INPUT TYPE="hidden" NAME="' . $realP . '[]" VALUE="' . $va . '">';
				}
			else
				$postParts[] = '<INPUT TYPE="hidden" NAME="' . $realP . '" VALUE="' . $v . '">';
			}

		$post = join( "\n", $postParts );
		return $post;
		}

	function getName(){
		return $this->formId;
		}

	function display( $vars = array(), $skipEnd = false, $skipStart = false ){
		global $_NTS, $NTS_VIEW, $NTS_CURRENT_USER;
		$this->requiredFields = 0;
		$this->formAction = 'display';

	// START UP HTML
		$this->formId = 'nts_form_' . $this->idPrefix . ntsLib::generateRand(6, array('caps' => false));
		$startUp = '';

	// NOW DISPLAY CONTROLS
		$displayFile = $this->formFile . '.php';
		if( $this->defaults )
		{
			extract( $this->defaults );
		}

		ob_start();
		require( $displayFile );
		$formContent = ob_get_contents();


	// SHOW REQUIRED TEXT
		if( $this->requiredFields > 0 && ( ! $this->skipRequiredAlert) ){
			$formContent = "\n<P>" . '<i>' . '* ' . M('Required field') . '</i></p>' . $formContent;
			}
		ob_end_clean();

		if( ! $skipStart ){
			$startUp .= "\n" . '<FORM class="form-horizontal form-condensed form-striped" METHOD="post"';
			$startUp .= ' ACTION="' . ntsLib::getRootWebpage() . '"';
			$startUp .=  ' ENCTYPE="multipart/form-data"';
			$startUp .= ' NAME="' . $this->formId . '"';
			$startUp .= ' ID="' . $this->formId . '"';
			$startUp .= ">\n";
			}

		if( $this->noprint ){
			ob_start();
			}

		echo $startUp;
		echo $formContent;

	// END HTML
		if( ! $skipEnd ){
			$end = '';
			$end .= '</FORM>';
			echo $end;
			}

		if( $this->noprint ){
			$return = ob_get_contents();
			ob_end_clean();
			return $return;
			}
		}

/* registers input */
	function registerInput( $type, $inputArray, $validators = array() ){
		$conf = array_merge( ntsForm::_inputDefaults(), $inputArray );
		$conf[ 'type' ] = $type;
		$conf[ 'validators' ] = $validators;
		$this->inputs[] = $conf;
		}

/* builds input HTML code */
	static function wrapInput( $label, $input, $aligned = TRUE )
	{
		$return = '';
		$inputs = is_array($input) ? $input : array( $input );

		$surroundClass = 'form-group';
		if( is_object($input) && $input->error )
		{
			$surroundClass .= ' has-error';
		}
		elseif( count($inputs) > 1 )
		{
			reset( $inputs );
			foreach( $inputs as $in )
			{
				if( is_object($in) && $in->error )
				{
					$surroundClass .= ' has-error';
					break;
				}
			}
		}

		$return .= '<div class="' . $surroundClass . '">';
		if( is_object($input) && ($input->type == 'checkbox') )
		{
			if( $aligned )
			{
//				$return .= '<div class="col-md-10 col-md-offset-2">';
				$return .= '<div class="control-holder">';
			}
			else
			{
				$return .= '<div class="col-md-12">';
			}
		}
		else
		{
//			$return .= '<label class="col-md-2 control-label">';
			$return .= '<label class="control-label">';
			$return .= $label;
			$return .= '</label>';

//			$return .= '<div class="col-md-10">';
			$return .= '<div class="control-holder">';
		}

		foreach( $inputs as $input )
		{
			if( is_object($input) && $input->type == 'checkbox' )
			{
				$return		.= '<div class="checkbox">';
				$return			.= '<label>';
				$return				.= $input->view;
				if( count($inputs) <= 1 )
				{
					$return				.= ' ' . $label;
				}
				$return			.= '</label>';
				if( $input->error )
				{
					$return		.= '<span class="help-inline">' . $input->error . '</span>';
				}
				if( $input->help )
				{
					$return		.= '<span class="help-block">' . $input->help . '</span>';
				}
				$return		.= '</div>';
			}
			else
			{
				$wrap_start = '';
				$wrap_end = '';
				if( 
					(! is_object($input)) OR
					in_array( $input->type, array('labelData') )
					)
				{
					if( count($inputs) < 2 )
					{
						$wrap_start = '<p class="form-control-static">';
						$wrap_end = '</p>';
					}
				}

				if( is_object($input) )
				{
					$return .= $wrap_start . $input->view . $wrap_end;
				}
				else
				{
					$return .= $wrap_start . $input . $wrap_end;
				}

				if( is_object($input) )
				{
					if( $input->error )
					{
						$return .= '<span class="help-inline">' . $input->error . '</span>';
					}
					if( $input->help )
					{
						$return .= '<span class="help-block">' . $input->help . '</span>';
					}
				}
			}
		}
		$return .= '</div>';
		$return .= '</div>';
		return $return;
	}

	function buildInput( $type, $inputArray, $validators = array() )
	{
		$return = '';

		if( $this->readonly )
			$inputArray[ 'readonly' ] = 1;

		if( $this->formAction == 'validate' ){
			return $this->registerInput( $type, $inputArray, $validators );
			}

		$conf = array_merge( ntsForm::_inputDefaults(), $inputArray );

		if( ($type == 'radio') OR ($type == 'radioSet') ){
			$conf['groupValue'] = $this->getValue( $conf['id'], $conf['default'] );
			}

		if( ! isset($conf['value']) ){
			$conf['value'] = $this->getValue( $conf['id'], $conf['default'] );
			}

	/* if it is one entry only */
		if( isset($validators) && is_array($validators) )
		{
			reset( $validators );
			foreach( $validators as $va )
			{
				$shortValidatorName = basename( $va['code'], '.php' );
				if( ($shortValidatorName == 'oneEntryOnly') && ( strlen($conf['value']) > 0 ) )
				{
					$conf['attr']['readonly'] = 'readonly';
					$conf['attr']['disabled'] = 'disabled';
				}
			}
		}

		$conf['error'] = $this->_getErrorForInput( $conf['id'] );
		$input = '';
		$inputAction = 'display';
		if( ! isset($conf['htmlId']) ){
			$conf['htmlId'] = $this->formId . $conf['id'];
			}

	// INCLUDE THE RIGHT INPUT FILE
		$inputFile = NTS_LIB_DIR . '/lib/form/inputs/' . $type . '.php';

		if( ! file_exists($inputFile) ){
			echo $shortName . ' file does not exist!<BR';
			return;
			}

		$conf['id'] = ntsView::setRealName( $conf['id'] );
		require( $inputFile );

		if( isset($conf['after']) )
			$input .= ' ' . $conf['after'];

		if( isset($conf['before']) )
			$input = $conf['before'] . ' ' . $input;

		$return = new stdClass;
		$return->type = $type;
		$return->view = $input;
		$return->error = $conf['error'];
		$return->help = $conf['help'];

		return $return;
	}

	function makeInput( $type, $inputArray, $validators = array() ){
		$return = '';

		if( $this->readonly )
			$inputArray[ 'readonly' ] = 1;

		if( $this->formAction == 'validate' ){
			return $this->registerInput( $type, $inputArray, $validators );
			}

		$conf = array_merge( ntsForm::_inputDefaults(), $inputArray );

		if( $type == 'radio' ){
			$conf['groupValue'] = $this->getValue( $conf['id'], $conf['default'] );
			}

		if( ! isset($conf['value']) ){
			$conf['value'] = $this->getValue( $conf['id'], $conf['default'] );
			}

	/* if it is one entry only */
		reset( $validators );
		foreach( $validators as $va ){
			$shortValidatorName = basename( $va['code'], '.php' );
			if( ($shortValidatorName == 'oneEntryOnly') && ( strlen($conf['value']) > 0 ) ){
				$conf['attr']['readonly'] = 'readonly';
				$conf['attr']['disabled'] = 'disabled';
				}
			}

		$conf['error'] = $this->_getErrorForInput( $conf['id'] );
		$input = '';
		$inputAction = 'display';
		if( ! isset($conf['htmlId']) ){
			$conf['htmlId'] = $this->formId . $conf['id'];
			}

	// INCLUDE THE RIGHT INPUT FILE
		$inputFile = NTS_LIB_DIR . '/lib/form/inputs/' . $type . '.php';

		if( ! file_exists($inputFile) ){
			echo $shortName . ' file does not exist!<BR';
			return;
			}

		$conf['id'] = ntsView::setRealName( $conf['id'] );
		if( $this->useCache ){
			if( ! isset($this->_controlsCache[$inputFile]) ){
				$code2run = file_get_contents( $inputFile );
				$code2run = str_replace( '<?php', '', $code2run );
				$code2run = str_replace( '?>', '', $code2run );
				$this->_controlsCache[$inputFile] = $code2run;
				}
			$code2run = $this->_controlsCache[$inputFile];
			eval( $code2run );
			}
		else {
			require( $inputFile );
			}

		if( $conf['help'] )
			$input .= '<span class="help-box">' . $conf['help'] . '</span>';

	// COMPILE OUTPUT
//		if( $conf['error'] )
//			$return .= '<strong class="alert">' . $conf['error'] . '</strong><br />';
		$return .= $input;

		if( $conf['error'] )
		{
			if( isset($inputArray['errorBlock']) && $inputArray['errorBlock'] )
				$return .= '<span class="help-block">';
			else
				$return .= '<span class="help-inline">';
			$return .= '<span class="text-danger">' . $conf['error'] . '</span></span>';
		}

		if( $conf['required'] )
			$this->requiredFields++;

		return $return;
		}

/* Validates form */
	function validate( $removeValidation = array(), $keepErrors = FALSE ){
		global $_NTS, $NTS_VIEW;
		$formValid = true;

		if( $this->defaults )
		{
			extract( $this->defaults );
		}

		$this->inputs = array();
		if( ! $keepErrors )
			$this->errors = array();
		if( $this->errors )
			$formValid = false;
		$this->values = array();

		ob_start();
		$formFile = $this->formFile . '.php';
		$this->formAction = 'validate';
		require( $formFile );
		ob_end_clean();

	// NOW GRAB
		reset( $this->inputs );
		$supplied = array();
		foreach( $this->inputs as $controlConf ){
			$suppliedValue = $this->grabValue( $controlConf['id'], $controlConf['type'], $controlConf );
			$this->values[ $controlConf['id'] ] = $suppliedValue;
			$supplied[] = $controlConf['id'];
			}

	// NOW VALIDATE
		reset(  $this->inputs );
		$formValues = array_merge( $this->defaults, $this->values );

		$val = new ntsValidator;
		$val->formValues = array_merge( $this->defaults, $this->values );

		foreach(  $this->inputs as $controlConf ){
			$val->reset();
			$val->controlConf = $controlConf;
			$val->checkValue = $this->values[ $controlConf['id'] ];

			if( ! in_array($controlConf['id'], $supplied) )
				continue;
 			$checkValue = $this->values[ $controlConf['id'] ];

		/* built-in control validation */
			$inputAction = 'validate';
			$validationFailed = false;
			$validationError = '';

			$shortName = 'inputs/' . $controlConf['type'] . '.php';
			$handle = $controlConf['id'];

			$inputFile = NTS_LIB_DIR . '/lib/form/inputs/' . $controlConf['type'] . '.php';
			if( file_exists($inputFile) )
			{
				require( $inputFile );
			}
			else
				echo $shortName . ' file does not exist!<BR';

			if( $validationFailed ){
				$this->errors[ $controlConf['id'] ] = $validationError;
				$formValid = false;
				break;
				}

			if( ! isset($controlConf['validators']) )
				continue;
	
		/* external validation */
			$val->reset();
			$val->controlConf = $controlConf;
			$val->checkValue = $this->values[ $controlConf['id'] ];
			if( (! $removeValidation) || (! in_array($controlConf['id'],$removeValidation) ) ){
				reset( $controlConf['validators'] );
				foreach( $controlConf['validators'] as $validatorInfo ){
					$val->validationParams = ( isset($validatorInfo['params']) ) ? $validatorInfo['params'] : array();

					if( ! $val->run($validatorInfo['code']) ){
						$validationError = $val->getError();
						if( ! $validationError )
							$validationError = $validatorInfo['error'];

						$this->errors[ $controlConf['id'] ] = $validationError;

						$formValid = false;
						break;
						}
					}
				}
			}

		$this->valid = $formValid;
		return $formValid;
		}

	function setValue( $ctlId, $ctlValue ){
		$this->values[ $ctlId ] = $ctlValue;
		}

	function getValues(){
		return $this->values;
		}

/* Prefills an input attributes */
	static function _inputDefaults(){
		$def = array(
			'id'		=> ntsLib::generateRand(6, array('caps' => false)),
			'label'		=> '',
			'default'	=> '',
			'help'		=> '',
			'attr'		=> array(),
			'required'	=> 0,
			);
		return $def;
		}

	function getDefaults(){
		return $this->defaults;
		}

/* Checks if a value has been supplied, or returns default otherwise */
	function getValue( $name, $defaultValue = '' ){
		if( isset($this->values[$name]) ){
			$value = $this->values[$name];
			}
		elseif( isset($this->defaults[$name]) ){
			$value = $this->defaults[$name];
			}
		else {
			$value = $defaultValue;
			}
		return $value;
		}

/* Checks if a validation error happend for this input */
	function _getErrorForInput( $name ){
		$error = ( isset($this->errors[$name]) ) ? $this->errors[$name] : '';
		return $error;
		}

/* Builds HTML string with input attributes */
	static function makeInputParams( $params = array() )
	{
		$paramsCode = array();
		reset( $params );
		$widthSet = FALSE;

		if( isset($params['size']) && $params['size'] )
		{
			if( ! isset($params['style']) )
				$params['style'] = '';
			if( $params['style'] )
				$params['style'] .= '; ';
//			$params['style'] .= 'width: ' . ($params['size'] + 1) . 'em;';
			$widthSet = TRUE;
		}
		else
		{
			if( isset($params['cols']) && $params['cols'] )
			{
				if( ! isset($params['style']) )
					$params['style'] = '';
				if( $params['style'] )
					$params['style'] .= '; ';
				$params['style'] .= 'width: ' . ($params['cols']+1) . 'em;';
				$widthSet = TRUE;
			}

			if( isset($params['rows']) && $params['rows'] )
			{
				if( ! isset($params['style']) )
					$params['style'] = '';
				if( $params['style'] )
					$params['style'] .= '; ';
				$params['style'] .= 'height: ' . ($params['rows']+1) . 'em;';
			}
		}

		if( ! $widthSet )
		{
//			$params['style'] .= 'width: 100%;';
		}

		foreach( $params as $key => $value )
		{
			if( $key == '_class' )
				continue;
			if( is_array($value) )
				continue;
			$paramsCode[] = $key . '="' . htmlspecialchars($value) . '"';
		}
		$return = join( ' ', $paramsCode );
		return $return;
	}

/* Grabs an input value - actual code in the input file */
	function grabValue( $handle, $type = '', $conf = array() ){
		global $_NTS, $NTS_VIEW;

		$input = '';
		$inputAction = 'submit';

	// INCLUDE THE RIGHT INPUT FILE
		$shortName = 'inputs/' . $type . '.php';

		$inputFile = NTS_LIB_DIR . '/lib/form/inputs/' . $type . '.php';
		if( file_exists($inputFile) )
			require( $inputFile );
		else
			echo $shortName . ' file does not exist!<BR';

	/* if not admin then strip HTML tags */
		global $NTS_CURRENT_USER;
		if( ! $NTS_CURRENT_USER->hasRole('admin') ){
			if( is_array($input) ){
				}
			else {
				$input = strip_tags( $input );
				}
			}

		return $input;
		}

/* Checks if an input has been really supplied - actual code in the input file */
	function inputSupplied( $handle, $type = '' ){
		$input = '';
		$inputAction = 'check_submit';

	// INCLUDE THE RIGHT INPUT FILE
		$shortName = 'inputs/' . $type . '.php';

		$inputFile = NTS_LIB_DIR . '/lib/form/inputs/' . $type . '.php';
		$handle = ntsView::setRealName( $handle );
		if( file_exists($inputFile) )
			require( $inputFile );
		else
			echo $shortName . ' file does not exist!<BR';

		return $input;
		}
	}
?>
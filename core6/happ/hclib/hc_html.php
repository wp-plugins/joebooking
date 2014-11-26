<?php
class HC_Html_Element_Input extends HC_Html_Element
{
	protected $error = '';
	protected $help = '';
	protected $name = 'name';
	protected $value = NULL;

	function __construct( $name, $value )
	{
		$this->set_name( $name );
		$this->set_value( $value );
		$this->set_tag( 'input' );
	}

	function set_value( $value )
	{
		$this->value = $value;
	}
	function value()
	{
		return $this->value;
	}

	function set_name( $name )
	{
		$this->name = $name;
	}
	function name()
	{
		return $this->name;
	}

	function set_error( $error )
	{
		$this->error = $error;
	}
	function error()
	{
		return $this->error;
	}

	function set_help( $help )
	{
		$this->help = $help;
	}
	function help()
	{
		return $this->help;
	}

	function render()
	{
		$return = '';

		$error = $this->error();
		$help = $this->help();

		if( $error )
		{
			$return .= '<div class="has-error">';
		}

		$return .= parent::render();

		if( $error )
		{
			$return .= '<span class="help-inline">' . $error . '</span>';
		}

		if( $help )
		{
			$return .= '<span class="help-block">' . $help . '</span>';
		}

		if( $error )
		{
			$return .= '</div>';
		}

		return $return;
	}
}

class HC_Html_Element_Input_Textarea extends HC_Html_Element_Input
{
	function __construct( $name, $value )
	{
		parent::__construct( $name, $value );
		$this->set_tag( 'textarea' );
		$this->set_attr( 'name', $name );
		$this->add_attr( 'class', 'form-control' );
		$this->add_child( $value );
	}
}

class HC_Html_Element_Input_Select extends HC_Html_Element_Input
{
	function __construct( $name, $value )
	{
		parent::__construct( $name, $value );
		$this->set_tag( 'select' );
		$this->set_attr( 'name', $name );
		$this->add_attr( 'class', 'form-control' );
	}

	function init( $more )
	{
		if( isset($more['options']) )
		{
			$this->set_options( $more['options'] );
			unset( $more['options'] );
		}
		parent::init( $more );
	}

	public function set_options( $options )
	{
		foreach( $options as $key => $label )
		{
			$option = new HC_Html_Element;
			$option->set_tag( 'option' );
			$option->set_attr( 'value', $key );
			$option->add_child( $label );
			if( $this->value() == $key )
			{
				$option->set_attr( 'selected', 'selected' );
			}
			$this->add_child( $option );
		}
	}
}

class HC_Html_Element_Input_Time extends HC_Html_Element_Input_Select
{
	function __construct( $name, $value )
	{
		parent::__construct( $name, $value );

		$step = 15 * 60;
		$out = '';
		$options = array();

		$t = new Hc_time;
		$t->setDateDb( 20130118 );

		$start_with = 0;
		$end_with = 24 * 60 * 60;

		/*
		if( isset($data['conf']['min']) && ($data['conf']['min'] > $start_with) )
		{
			$start_with = $data['conf']['min'];
		}
		if( isset($data['conf']['max']) && ($data['conf']['max'] < $end_with) )
		{
			$end_with = $data['conf']['max'];
		}
		*/
		if( $end_with < $start_with )
		{
			$end_with = $start_with;
		}

		if( $value && ($value > $end_with) )
		{
			$value = $value - 24 * 60 * 60;
		}

		if( $start_with )
			$t->modify( '+' . $start_with . ' seconds' );

		$no_of_steps = ( $end_with - $start_with) / $step;
		for( $ii = 0; $ii <= $no_of_steps; $ii++ )
		{
			$sec = $start_with + $ii * $step;
			$options[ $sec ] = $t->formatTime();
			$t->modify( '+' . $step . ' seconds' );
		}

		$this->set_options( $options );
	}
}

class HC_Html_Element_Input_Text extends HC_Html_Element_Input
{
	function __construct( $name, $value )
	{
		parent::__construct( $name, $value );
		$this->set_attr( 'name', $name );
		$this->set_attr( 'value', $value );
		$this->set_attr( 'type', 'text' );
		$this->add_attr( 'class', 'form-control' );
	}
}

class HC_Html_Element_Input_Password extends HC_Html_Element_Input
{
	function __construct( $name, $value )
	{
		parent::__construct( $name, $value );
		$this->set_attr( 'name', $name );
		$this->set_attr( 'value', $value );
		$this->set_attr( 'type', 'text' );
		$this->add_attr( 'class', 'form-control' );
	}
}

class HC_Html_Element_Input_Radio extends HC_Html_Element_Input
{
	function __construct( $name, $value )
	{
		parent::__construct( $name, $value );
		$this->set_attr( 'name', $name );
		$this->set_attr( 'value', $value );
		$this->set_attr( 'type', 'radio' );
	}
}

class HC_Html_Element_Input_Checkbox extends HC_Html_Element_Input
{
	function __construct( $name, $value )
	{
		parent::__construct( $name, $value );
		$this->set_attr( 'name', $name );
		$this->set_attr( 'value', $value );
		$this->set_attr( 'type', 'checkbox' );
	}
}

class HC_Html_Element_Input_Hidden extends HC_Html_Element_Input
{
	function __construct( $name, $value )
	{
		parent::__construct( $name, $value );
		$this->set_attr( 'name', $name );
		$this->set_attr( 'value', $value );
		$this->set_attr( 'type', 'hidden' );
	}
}

class HC_Html_Element
{
	protected $tag = 'input';
	protected $attr = array();
	protected $children = array();

	function __construct()
	{
	}

	function init( $more )
	{
		if( isset($more['help']) )
		{
			$el->set_help( $more['help'] );
			unset( $more['help'] );
		}

		if( isset($more['value']) )
		{
			$el->set_value( $more['value'] );
			unset( $more['value'] );
		}

		reset( $more );
		foreach( $more as $k => $v )
		{
			$el->add_attr( $k, $v );
		}
	}

	function set_tag( $tag )
	{
		$this->tag = $tag;
	}
	function tag()
	{
		return $this->tag;
	}

	function attr( $key = '' )
	{
		$return = array();
		if( $key === '' )
		{
			$return = $this->attr;
		}
		elseif( isset($this->attr[$key]) )
		{
			$return = $this->attr[$key];
		}
		return $return;
	}

	function add_attr( $key, $value )
	{
		if( isset($this->attr[$key]) )
		{
			$this->attr[$key][] = $value;
		}
		else
		{
			$this->set_attr( $key, $value );
		}
	}

	function set_attr( $key, $value )
	{
		$this->attr[$key] = array( $value );
	}

	function add_child( $child )
	{
		$this->children[] = $child;
	}
	function children()
	{
		return $this->children;
	}

	function before_render()
	{
	}

	function render()
	{
		$this->before_render();

		$return = '';
		$return .= '<' . $this->tag();

		$attr = $this->attr();
		if( $attr )
		{
			$return .= ' ';
		}
		foreach( $attr as $key => $val )
		{
			if ($key == 'value')
			{
				for( $ii = 0; $ii < count($val); $ii++ )
				{
					$val[$ii] = htmlspecialchars( $val[$ii] );
					$val[$ii] = str_replace( array("'", '"'), array("&#39;", "&quot;"), $val[$ii] );
				}
			}
			$return .= $key . '="' . join(' ', $val) . '" ';
		}

		$children = $this->children();
		if( $children )
		{
			$return .= '>';

			reset( $children );
			foreach( $children as $child )
			{
				if( is_object($child) )
				{
					$return .= $child->render();
				}
				else
				{
					$return .= $child;
				}
			}

			$return .= '</' . $this->tag() . '>';
		}
		else
		{
			$return .= '/>';
		}
		return $return;
	}
}

class HC_Html
{
	/**
	* label_row
	*
	* Outputs the content with a label, mainly used in forms
	*
	* @param	string $label
	* @param	string/array $content 
	* @return	string
	*/
	static function label_row( $label, $content )
	{
		$return = '';

		$contents = is_array($content) ? $content : array( $content );
		$label = HC_Lib::parse_lang( $label );

		$return .=	'<div class="form-group">';

		$return .=		'<label class="control-label">';
		$return .=			$label;
		$return .=		'</label>';

		$return .=		'<div class="control-holder">';

		foreach( $contents as $content )
		{
			$return .=		$content;
		}

		$return .=		'</div>';
		$return .=	'</div>';
		return $return;
	}

	/**
	* input
	*
	* Outputs HTML code for input
	*
	* @param	HC_Form_Input $input
	* @return	string
	*/
	static function input( $input, $more = array() )
	{
		$return = '';
		$type = $input->type();

		$class = 'HC_Html_Element_Input_' . ucfirst($type);
		if( ! class_exists($class) )
		{
			echo "'$class' is not defined!";
		}

		$el = new $class( $input->name(), $input->value() );
		$el->init( $more );

		$error = $input->error();
		if( $error )
		{
			$el->set_error( $error );
		}

		return $el->render();
	}












	static function dropdown_menu( $menu, $class = 'dropdown-menu', $more_li_class = '' )
	{
		$renderer = new Hc_renderer;
		$view_file = dirname(__FILE__) . '/view/dropdown_menu.php';
		return $renderer->render( 
			$view_file, 
			array(
				'menu'			=> $menu,
				'class'			=> $class,
				'more_li_class'	=> $more_li_class,
				)
			);
	}

	/*
	$input could be either string or
	$input = new stdClass;
	$input->type = $type;
	$input->view = $input;
	$input->error = $conf['error'];
	$input->help = $conf['help'];
	*/
	static function wrap_input( $label, $input, $aligned = TRUE )
	{
		$return = '';
		$inputs = is_array($input) ? $input : array( $input );

		$label = Hc_lib::parse_lang( $label );

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
				$return .= '<div class="control-holder">';
			}
			else
			{
				$return .= '<div class="col-sm-12">';
			}
		}
		else
		{
			$return .= '<label class="control-label">';
			$return .= $label;
			$return .= '</label>';

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
}

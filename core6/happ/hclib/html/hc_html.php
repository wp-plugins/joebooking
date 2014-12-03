<?php
class HC_Html_Factory
{
	public static function element( $element )
	{
		$return = new HC_Html_Element( $element );
		return $return;
	}

	public static function input( $type, $name, $value, $more = array() )
	{
		$class = 'HC_Html_Element_Input_' . ucfirst($type);
		if( ! class_exists($class) )
		{
			/* attempt to load the file */
			$file = dirname(__FILE__) . '/form/' . $type . '.php';
			if( file_exists($file) )
			{
				include_once( $file );
			}
			else
			{
				echo "'$class' is not defined!";
			}
		}
		$return = new $class( $name, $value, $more );
		return $return;
	}

	public static function widget( $element )
	{
		$widget = 'HC_Html_Widget_' . ucfirst($element);
		if( ! class_exists($widget) )
		{
			/* attempt to load the file */
			$file = dirname(__FILE__) . '/widgets/' . $element . '.php';
			if( file_exists($file) )
			{
				include_once( $file );
			}
		}

		if( class_exists($widget) )
		{
			return new $widget();
		}
		else
		{
			throw new Exception( "No class defined: '$widget'" );
		}
	}
}

class HC_Html_Element_Input extends HC_Html_Element
{
	protected $error = '';
	protected $help = '';
	protected $name = 'name';
	protected $value = NULL;

	function __construct( $name, $value, $more = array() )
	{
		$this->set_name( $name );
		$this->set_value( $value );
		$this->set_tag( 'input' );

		if( isset($more['help']) )
		{
			$this->set_help( $more['help'] );
			unset( $more['help'] );
		}

		if( isset($more['value']) )
		{
			$this->set_value( $more['value'] );
			unset( $more['value'] );
		}

		reset( $more );
		foreach( $more as $k => $v )
		{
			$this->add_attr( $k, $v );
		}
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
			if( is_array($error) )
			{
				$error = join( ' ', $error );
			}
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

class HC_Html_Element
{
	protected $tag = 'input';
	protected $attr = array();
	protected $children = array();
	protected $addon = array();
	protected $wrap = array();

	function __construct( $tag )
	{
		$this->set_tag( $tag );
	}

	function set_tag( $tag )
	{
		$this->tag = $tag;
		return $this;
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

	function add_attr( $key, $value, $take_care = 0 )
	{
		if( isset($this->attr[$key]) )
		{
			$this->attr[$key][] = $value;
		}
		else
		{
			$this->set_attr( $key, $value );
		}
		return $this;
	}

	function set_attrs( $array )
	{
		reset( $array );
		foreach( $array as $k => $v )
		{
			$this->set_attr( $k, $v );
		}
	}

	function set_attr( $key, $value )
	{
		$this->attr[$key] = array( $value );
		return $this;
	}

	function add_child( $child )
	{
		$this->children[] = $child;
		return $this;
	}
	function children()
	{
		return $this->children;
	}

	function add_wrap( $wrap )
	{
		$this->wrap[] = $wrap;
		return $this;
	}
	function wrap()
	{
		return $this->wrap;
	}

	function add_addon( $addon )
	{
		$this->addon[] = $addon;
		return $this;
	}
	function addon()
	{
		return $this->addon;
	}

	function render()
	{
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

		$addon = $this->addon();
		if( $addon )
		{
			reset( $addon );
			foreach( $addon as $ao )
			{
				if( is_object($ao) )
				{
					$return .= $ao->render();
				}
				else
				{
					$return .= $ao;
				}
			}
		}

		if( $wrap = $this->wrap() )
		{
			foreach( $wrap as $wr )
			{
				$return = $wr->add_child( $return )->render();
			}
		}

		return $return;
	}
}

class HC_Html
{
	static function alert_danger( $error )
	{
		if( ! $error )
			return;
		$return = array();
		$return[] =	'<div class="alert alert-danger">';
		$return[] =		'<button type="button" class="close" data-dismiss="alert">&times;</button>';
		if( is_array($error) )
		{
			$return[] =		'<ul class="list-unstyled">';
			foreach( $error as $k => $e )
			{
				$return[] =		'<li>';
				$return[] =		$e;
				$return[] =		'</li>';
			}
			$return[] =		'</ul>'; 
		}
		else
		{
			$return[] =		$error;
		}
		$return[] =	'</div>';

		$return = join( "\n", $return );
		return $return;
	}

	static function factory_panels()
	{
		$return = NULL;

		switch( $element )
		{
			case 'panels':
				return;
		}
	
	}

	/**
	* label_row
	*
	* Outputs the content with a label, mainly used in forms
	*
	* @param	string $label
	* @param	string/array $content 
	* @return	string
	*/
	static function label_row( $label, $content, $error = 0 )
	{
		$return = '';

		$contents = is_array($content) ? $content : array( $content );
		$label = HC_Lib::parse_lang( $label );

		$div = HC_Html_Factory::element( 'div' )
				->add_attr( 'class', 'form-group', 1 );
		if( $error )
			$div->add_attr( 'class', 'has-error' );

		$label_c = HC_Html_Factory::element( 'label' )
					->add_attr( 'class', 'control-label', 1 )
					->add_child( $label );

		$div->add_child( $label_c );

		$div2 = HC_Html_Factory::element( 'div' )
				->add_attr( 'class', 'control-holder', 1 );
		foreach( $contents as $content )
		{
			$div2->add_child( $content );
		}
		$div->add_child( $div2 );

		$return = $div->render();
		return $return;


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
	static function input( $input_array, $more = array() )
	{
		$return = '';

		$value = isset($input_array['value']) ? $input_array['value'] : '';
		$el = HC_Html_Factory::input(
			$input_array['type'],
			$input_array['name'],
			$value,
			$more
			);

		$error = isset($input_array['error']) ? $input_array['error'] : '';
		if( $error )
		{
			$el->set_error( $error );
		}

		return $el->render();
	}












	static function dropdown_menu( $menu, $class = 'dropdown-menu', $more_li_class = '' )
	{
		$renderer = new Hc_renderer;
		$view_file = dirname(__FILE__) . '/../view/dropdown_menu.php';
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

include_once( dirname(__FILE__) . '/form/basic.php' );

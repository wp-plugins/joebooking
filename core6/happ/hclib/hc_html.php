<?php
class HC_View_Layout
{
	protected $partials = array();
	protected $template = '';
	protected $params = array();

	function set_partial( $key, $value )
	{
		$this->partials[$key] = $value;
	}
	function partial( $key )
	{
		$return = isset($this->partials[$key]) ? $this->partials[$key] : '';
		return $return;
	}

	function has_partial( $key )
	{
		return (isset($this->partials[$key]) && $this->partials[$key]) ? TRUE : FALSE;
	}

	function set_template( $template )
	{
		$this->template = $template;
	}
	function template()
	{
		return $this->template;
	}

	function set_params( $params )
	{
		foreach( $params as $param => $value )
		{
			$this->set_param( $param, $value );
		}
	}
	function set_param( $param, $value )
	{
		$this->params[ $param ] = $value;
	}
	function params()
	{
		return $this->params;
	}
	function param( $key )
	{
		$return = isset($this->params[$key]) ? $this->params[$key] : '';
		return $return;
	}
}

class HC_Html_Factory
{
	public static function element( $element )
	{
		$return = new HC_Html_Element( $element );
		return $return;
	}

	public static function input( $element, $name = '' )
	{
		static $classes = array();
		$class_key = 'input_' . $element;

		if( isset($classes[$class_key]) )
		{
			$class = $classes[$class_key];
		}
		else
		{
			$widget_locations = HC_App::widget_locations();
			foreach( $widget_locations as $prfx => $location )
			{
				$class = strtoupper($prfx) . '_Form_Input_' . ucfirst($element);
				if( ! class_exists($class) )
				{
					/* attempt to load the file */
//echo "ATTEMPT TO LOAD '$class'<br>";
//				$file = dirname(__FILE__) . '/widgets/' . $element . '.php';
					$file = $location . '/form/' . $element . '.php';
					if( file_exists($file) )
					{
						include_once( $file );
					}
				}
				if( class_exists($class) )
				{
					$classes[$class_key] = $class;
					break;
				}
			}
		}

		if( class_exists($class) )
		{
			if( $name )
				$return = new $class( $name );
			else
				$return = new $class;
			return $return;
		}
		else
		{
			throw new Exception( "No class defined: '$class'" );
		}
	}

	public static function widget( $element )
	{
		static $classes = array();
		$class_key = 'widget_' . $element;

		if( isset($classes[$class_key]) )
		{
			$class = $classes[$class_key];
		}
		else
		{
			$widget_locations = HC_App::widget_locations();
			foreach( $widget_locations as $prfx => $location )
			{
				$class = strtoupper($prfx) . '_Html_Widget_' . ucfirst($element);
				if( ! class_exists($class) )
				{
					/* attempt to load the file */
//echo "ATTEMPT TO LOAD '$class'<br>";
//				$file = dirname(__FILE__) . '/widgets/' . $element . '.php';
					$file = $location . '/html/' . $element . '.php';
					if( file_exists($file) )
					{
						include_once( $file );
					}
				}
				if( class_exists($class) )
				{
					$classes[$class_key] = $class;
					break;
				}
			}
		}

		$args = func_get_args();
		if( class_exists($class) )
		{

			$return = new $class();
			array_shift( $args );
			if( $args )
			{
				call_user_func_array( array($return, "init"), $args );
			}
			return $return;
		}
		else
		{
			throw new Exception( "No class defined: '$class'" );
		}
	}
}

class HC_Html_Element
{
	protected $tag = 'input';
	protected $attr = array();
	protected $children = array();
	protected $addon = array();
	protected $wrap = array();

	function __construct( $tag = '' )
	{
		if( strlen($tag) )
			$this->set_tag( $tag );
	}

	public function __toString()
	{
		return $this->render();
    }

	function init()
	{
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

	protected function prep_attr( $key, $value )
	{
		switch( $key )
		{
			case 'title':
				if( is_string($value) )
				{
					$value = strip_tags($value);
					$value = trim($value);
				}
				break;
		}
		return $value;
	}

	function add_attr( $key, $value = NULL )
	{
		if( count(func_get_args()) == 1 )
		{
			// supplied as array
			foreach( $key as $key => $value )
			{
				$this->add_attr( $key, $value );
			}
		}
		else
		{
			if( is_array($value) )
			{
				foreach( $value as $v )
				{
					$this->add_attr( $key, $v );
				}
			}
			else
			{
				$value = $this->prep_attr( $key, $value );
				if( isset($this->attr[$key]) )
				{
					$this->attr[$key][] = $value;
				}
				else
				{
					if( ! is_array($value) )
						$value = array( $value ); 
					$this->attr[$key] = $value;
				}
			}
		}
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
		foreach( $attr as $key => $val )
		{
			switch( $key )
			{
				case 'value':
					for( $ii = 0; $ii < count($val); $ii++ )
					{
						$val[$ii] = htmlspecialchars( $val[$ii] );
						$val[$ii] = str_replace( array("'", '"'), array("&#39;", "&quot;"), $val[$ii] );
					}
					break;
			}

			$val = join(' ', $val);
			if( strlen($val) )
			{
				$return .= ' ' . $key . '="' . $val . '"';
			}
		}

		$children = $this->children();
		if( $children )
		{
			$return .= '>';

			reset( $children );
			foreach( $children as $child )
			{
//				$return .= "\n";
				if( is_array($child) )
				{
					foreach( $child as $subchild )
					{
						if( is_object($subchild) )
						{
							$return .= $subchild->render();
						}
						else
						{
							$return .= $subchild;
						}
					}
				}
				elseif( is_object($child) )
				{
					$return .= $child->render();
				}
				else
				{
					$return .= $child;
				}
			}

//			$return .= "\n";
			$return .= '</' . $this->tag() . '>';
		}
		else
		{
			if( in_array($this->tag(), array('br', 'input')) )
			{
				$return .= '/>';
			}
			else
			{
				$return .= '></' . $this->tag() . '>';
			}
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
	static function label( $class, $label = '' )
	{
		$return = '';
		if( ! strlen($label) )
			$label ='&nbsp;';

		$return = HC_Html_Factory::element('span')
			->add_attr('class', array('label', 'label-' . $class))
			->add_child($label)
			;
		return $return;
	}

	static function icon( $icon, $fw = TRUE )
	{
		if( substr($icon, 0, 2) == '<i' )
			return $icon;

		$return = HC_Html_Factory::element('i');
		if( strlen($icon) )
		{
			$return
				->add_attr('class', array('fa', 'fa-' . $icon))
				;

			if( $fw )
				$return->add_attr('class', 'fa-fw');
//			$return = $out->render();
		}
		return $return;
	}

	static function page_header( $header )
	{
		$wrap = HC_Html_Factory::element('div')
			->add_attr( 'class', 'page-header' )
			->add_child( $header )
			;
		return $wrap->render();
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
	* @param	array $input ('value', 'error', 'type', 'name')
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

class HC_Form_Input
{
	protected $type = 'text';
	protected $name = 'name';
	protected $error = '';
	protected $value = NULL;

	function __construct( $name = '' )
	{
		if( strlen($name) )
			$this->set_name( $name );
	}

	/* this will default classes and more attributes if needed*/
	function more( $el, $more = array() )
	{
		$el->add_attr( 'class', 'form-control', 1 );
		foreach( $more as $k => $v )
		{
			$el->add_attr( $k, $v );
		}
		return $el;
	}

	/* if fails should return the error message otherwise NULL */
	function _validate()
	{
		$return = NULL;
		return $return;
	}

	/* this will add error messages and help text if needed*/
	function decorate( $return )
	{
		$error = $this->error();
		if( $error )
		{
			if( is_array($error) )
			{
				$error = join( ' ', $error );
			}
			$return .= '<span class="help-inline">' . $error . '</span>';
		}

/*
		if( $help )
		{
			$return .= '<span class="help-block">' . $help . '</span>';
		}
*/
		return $return;
	}

	function set_type( $type )
	{
		$this->type = $type;
	}
	function type()
	{
		return $this->type;
	}

	function set_error( $error )
	{
		if( ! $this->error )
			$this->error = $error;
	}
	function error()
	{
		return $this->error;
	}

	function set_value( $value )
	{
		$this->value = $value;
		if( $error = $this->_validate() )
		{
			$this->set_error( $error );
		}
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

/* will be overwritten in child classes */
	function grab( $post )
	{
		$name = $this->name();
		$value = NULL;
		if( isset($post[$name]) )
		{
			$value = $post[$name];
		}
		$this->set_value( $value );
	}

	function to_array()
	{
		$return = array(
			'name'	=> $this->name(),
			'type'	=> $this->type(),
			'value'	=> $this->value(),
			'error'	=> $this->error(),
			);
		return $return;
	}
}
include_once( dirname(__FILE__) . '/widgets/form/basic.php' );

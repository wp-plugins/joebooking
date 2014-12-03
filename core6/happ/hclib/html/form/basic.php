<?php
class HC_Html_Element_Input_Textarea extends HC_Html_Element_Input
{
	function __construct( $name, $value, $more )
	{
		parent::__construct( $name, $value, $more );
		$this->set_tag( 'textarea' );
		$this->set_attr( 'name', $name );
		$this->add_attr( 'class', 'form-control' );
		$this->add_child( $value );
	}
}

class HC_Html_Element_Input_Select extends HC_Html_Element_Input
{
	function __construct( $name, $value, $more )
	{
		if( isset($more['options']) )
		{
			$this->set_options( $more['options'] );
			unset( $more['options'] );
		}

		parent::__construct( $name, $value, $more );

		$this->set_tag( 'select' );
		$this->set_attr( 'name', $name );
		$this->add_attr( 'class', 'form-control' );
		
	}

	public function set_options( $options )
	{
		foreach( $options as $key => $label )
		{
			$option = HC_Html_Factory::element('option');
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

class HC_Html_Element_Input_Text extends HC_Html_Element_Input
{
	function __construct( $name, $value, $more )
	{
		parent::__construct( $name, $value, $more );
		$this->set_attr( 'name', $name );
		$this->set_attr( 'value', $value );
		$this->set_attr( 'type', 'text' );
		$this->add_attr( 'class', 'form-control' );
	}
}

class HC_Html_Element_Input_Password extends HC_Html_Element_Input
{
	function __construct( $name, $value, $more )
	{
		parent::__construct( $name, $value, $more );
		$this->set_attr( 'name', $name );
		$this->set_attr( 'value', $value );
		$this->set_attr( 'type', 'password' );
		$this->add_attr( 'class', 'form-control' );
	}
}

class HC_Html_Element_Input_Radio extends HC_Html_Element_Input
{
	function __construct( $name, $value, $more )
	{
		parent::__construct( $name, $value, $more );
		$this->set_attr( 'name', $name );
		$this->set_attr( 'value', $value );
		$this->set_attr( 'type', 'radio' );
	}
}

class HC_Html_Element_Input_Checkbox extends HC_Html_Element_Input
{
	function __construct( $name, $value, $more )
	{
		if( isset($more['label']) )
		{
			$this->add_addon( $more['label'] );
			unset( $more['label'] );
		}

		parent::__construct( $name, $value, $more );
		$this->set_attr( 'name', $name );
		$this->set_attr( 'value', $value );
		$this->set_attr( 'type', 'checkbox' );

		$this->add_wrap(
			HC_Html_Factory::element('label')
//				->add_attr( 'class', 'checkbox', 1 )
//				->add_attr( 'class', 'inline', 1 )
			);
		$this->add_wrap(
			HC_Html_Factory::element('div')
				->add_attr( 'class', 'checkbox', 1 )
			);

	}
}

class HC_Html_Element_Input_Hidden extends HC_Html_Element_Input
{
	function __construct( $name, $value, $more )
	{
		parent::__construct( $name, $value, $more );
		$this->set_attr( 'name', $name );
		$this->set_attr( 'value', $value );
		$this->set_attr( 'type', 'hidden' );
	}
}

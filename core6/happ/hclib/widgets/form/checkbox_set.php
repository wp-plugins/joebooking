<?php
class HC_Form_Input_Checkbox_Set extends HC_Form_Input
{
	protected $options = array();
	protected $more = array();
	protected $readonly = array();
	protected $value = array();
	protected $inline = TRUE;

	function add_option( $value, $label = NULL, $more = '' )
	{
		$this->options[$value] = $label;
		if( $more ){
			$this->more[$value] = $more;
		}
		return $this;
	}
	function options()
	{
		return $this->options;
	}
	function more()
	{
		return $this->more;
	}

	function set_inline( $inline = TRUE )
	{
		$this->inline = $inline;
		return $this;
	}
	function inline()
	{
		return $this->inline;
	}

	function set_readonly( $value, $ro = TRUE )
	{
		$this->readonly[$value] = $ro;
		return $this;
	}
	function readonly( $value = NULL )
	{
		if( $value === NULL ){
			$return = $this->readonly;
		}
		else {
			$return = ( array_key_exists($value, $this->readonly) && $this->readonly[$value] ) ? TRUE : FALSE;
		}
		return $return;
	}

	function grab( $post )
	{
		$name = $this->name();
		$value = array();
		if( isset($post[$name]) )
		{
			$value = $post[$name];
		}
		$this->set_value( $value );
	}

	function render_one( $value, $decorate = TRUE )
	{
		$options = $this->options();
		$full_value = $this->value();
		$label = $options[$value];

		$sub_el = HC_Html_Factory::input('checkbox', $this->name() . '[]' )
			->set_my_value($value)
			;
		if( $this->readonly($value) ){
			$sub_el->set_readonly();
			}
		if( strlen($label) ){
			$sub_el->set_label( $label );
		}
		if( in_array($value, $full_value) ){
			$sub_el->set_value(1);
		}

		if( $decorate ){
			$return = $this->decorate( $sub_el->render() );
		}
		else {
			$return = $sub_el->render($decorate);
		}
		return $return;
	}

	function render()
	{
		$options = $this->options();
		$full_value = $this->value();
		$inline = $this->inline();

		$el = HC_Html_Factory::widget('list')
			;

		if( $inline ){
			$el->add_attr('class', array('list-inline'));
			$el->add_attr('class', array('list-separated'));
		}
		else {
			$el->add_attr('class', array('list-unstyled'));
			$el->add_attr('class', array('list-separated'));
			// $el->add_attr('class', array('list-padded'));
		}

		$attr = $this->attr();
		foreach( $attr as $key => $val ){
			$el->add_attr($key, $val);
		}

		foreach( $options as $value => $label ){
			$el->add_item( $this->render_one($value) );
		}

		$return = $this->decorate( $el->render(), FALSE );
		return $return;
	}
}
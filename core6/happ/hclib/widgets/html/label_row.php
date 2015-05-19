<?php
class HC_Html_Widget_Label_Row extends HC_Html_Element
{
	protected $label = NULL;
	protected $content = array();
	protected $content_static = FALSE;
	protected $error = FALSE;

	function set_error( $error )
	{
		$this->error = $error;
		return $this;
	}
	function error()
	{
		return $this->error;
	}
	function set_label( $label )
	{
		$this->label = $label;
		return $this;
	}
	function label()
	{
		return $this->label;
	}
	function set_content( $content )
	{
		$this->content = $content;
		return $this;
	}
	function content()
	{
		$return = $this->content;
		if( ! is_array($return) )
		{
			$return = array( $return );
		}
		return $return;
	}

	function set_content_static( $content_static = TRUE )
	{
		$this->content_static = $content_static;
		return $this;
	}
	function content_static()
	{
		return $this->content_static;
	}

	function render()
	{
		$error = $this->error();
		$label = $this->label();
		$content = $this->content();

		$div = HC_Html_Factory::element( 'div' )
			->add_attr('class', 'form-group')
			;
		if( $error ){
			$div->add_attr('class', 'has-error');
		}

		if( $label ){
			$label_c = HC_Html_Factory::element( 'label' )
				->add_attr('class', 'control-label')
				->add_child( $label )
				;
			$div->add_child( $label_c );
		}

		$content_holder = HC_Html_Factory::element( 'div' )
			->add_attr('class', 'control-holder')
			;
		if( $this->content_static() ){
			$content_holder->add_attr('class', 'form-control-static');
		}

		foreach( $content as $cont ){
			$content_holder->add_child( $cont );
		}

		$div->add_child( $content_holder );
		return $div->render();
	}
}
?>
<?php
class HC_Html_Widget_Flatmodal
{
	protected $closer = '';
	protected $content = '';

	function set_closer( $closer )
	{
		$this->closer = $closer;
		return $this;
	}
	function closer()
	{
		return $this->closer;
	}

	function set_content( $content )
	{
		$this->content = $content;
		return $this;
	}
	function content()
	{
		return $this->content;
	}

	function render()
	{
		$closer = $this->closer();

		if( ! $closer )
		{
			$closer = HC_Html_Factory::element('a')
				->add_child( 'Back' )
				->add_attr('class', array('btn btn-warning-o'))
				;
		}

		$closer
			->add_attr('class', array('hc-flatmodal-closer'))
			->add_attr('href', '#')
			->add_attr('style', 'display: none;')
			->add_attr('style', 'margin-bottom: 1em;')
			;

		$out = HC_Html_Factory::element('div')
			->add_attr('class', 'hc-flatmodal-parent')
			->add_child( $closer )
			->add_child( 
				HC_Html_Factory::element('div')
					->add_attr('class', array('hc-flatmodal-container', 'hc-ajax-container'))
				)
			->add_child( $this->content() )
			;
		$return = $out->render();

		return $return;
	}
}
?>
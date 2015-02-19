<?php
include_once( dirname(__FILE__) . '/container.php' );
class HC_Html_Widget_Panel extends HC_Html_Widget_Container
{
	protected $header = NULL;
	protected $body = NULL;
	protected $footer = NULL;

	function set_header( $thing )
	{
		$this->header = $thing;
		return $this;
	}
	function header()
	{
		return $this->header;
	}

	function set_body( $thing )
	{
		$this->body = $thing;
		return $this;
	}
	function body()
	{
		return $this->body;
	}

	function set_footer( $thing)
	{
		$this->footer = $thing;
		return $this;
	}
	function footer()
	{
		return $this->footer;
	}

	function render()
	{
		$out = HC_Html_Factory::element('div')
			->add_attr('class', 'panel')
			;

		$attr = $this->attr();
		foreach( $attr as $k => $v ){
			$out->add_attr( $k, $v );
		}

		$header = $this->header();
		if( $header ){
			$out->add_child(
				HC_Html_Factory::element('div')
					->add_attr('class', 'panel-heading')
					->add_child($header)
				);
		}

		$body = $this->body();
		if( $body ){
			$out->add_child(
				HC_Html_Factory::element('div')
					->add_attr('class', 'panel-body')
					->add_child($body)
				);
		}

		$footer = $this->footer();
		if( $footer ){
			$out->add_child(
				HC_Html_Factory::element('div')
					->add_attr('class', 'panel-footer')
					->add_child($footer)
				);
		}

		$return = $out->render();
		return $return;
	}
}
?>
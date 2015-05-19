<?php
class HC_Html_Widget_Ajax_Loader extends HC_Html_Element
{
	protected $title = NULL;
	protected $no_caret = FALSE;
	protected $wrap = FALSE;

	function set_title( $title )
	{
		$this->title = $title;
		return $this;
	}
	function title()
	{
		return $this->title;
	}

	function set_no_caret( $no_caret = TRUE )
	{
		$this->no_caret = $no_caret;
		return $this;
	}
	function no_caret()
	{
		return $this->no_caret;
	}

	function set_wrap( $wrap = TRUE )
	{
		$this->wrap = $wrap;
		return $this;
	}
	function wrap()
	{
		return $this->wrap;
	}

	function render()
	{
		$out = array();

	/* build trigger */
		$title = $this->title();
		if(
			is_object($title) &&
			( $title->tag() == 'a' )
			)
		{
			$trigger = $title;
		}
		else
		{
			$full_title = $title;
			$title = strip_tags($title);
			$title = trim($title);

			$trigger = HC_Html_Factory::element('a')
				->add_attr('title', $title)
				->add_child(
					$full_title
					)
				;
		}

		$trigger
			->add_attr('class', 'hc-ajax-loader')
			;

		$wrap = HC_Html_Factory::element('div')
			->add_attr('class', 'hc-ajax-parent')
			;
		$container = HC_Html_Factory::element('div')
			->add_attr('class', 'hc-ajax-container')
			;
		$wrap->add_child( $trigger );
		$wrap->add_child( $container );

		return $wrap->render();
	}
}
?>
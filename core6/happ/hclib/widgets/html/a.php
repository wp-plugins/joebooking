<?php
class HC_Html_Widget_A extends HC_Html_Element
{
	function __construct()
	{
		parent::__construct('a');
	}

	function render()
	{
		$already_title = $this->attr('title');
		if( ! $already_title ){
			$children_return = $this->_prepare_children();
			$this->add_attr('title', $children_return);
		}
		return parent::render();
	}
}
<?php
class HC_Html_Widget_Leftright
{
	protected $left = '';
	protected $right = '';

	function set_left( $left )
	{
		$this->left = $left;
		return $this;
	}
	function left()
	{
		return $this->left;
	}

	function set_right( $right )
	{
		$this->right = $right;
		return $this;
	}
	function right()
	{
		return $this->right;
	}

	function render()
	{
		$out = HC_Html_Factory::element('div')
			->add_attr('class', 'squeeze-in')
			->add_child( 
				HC_Html_Factory::element('div')
					->add_attr('class', 'squeeze-in')
					->add_attr('class', 'pull-left')
					->add_attr('style', 'width: 50%;')
						->add_child( $this->left() )
				)
			->add_child( 
				HC_Html_Factory::element('div')
					->add_attr('class', 'squeeze-in')
					->add_attr('class', 'pull-right')
					->add_attr('class', 'text-right')
					->add_attr('style', 'width: 50%;')
						->add_child( $this->right() )
				)
			->add_child( 
				HC_Html_Factory::element('div')
					->add_attr('class', 'clearfix')
						->add_child( '' )
				)
			;
		$return = $out->render();

		return $return;
	}
}
?>
<?php
class HC_Html_Widget_Leftright
{
	protected $left = '';
	protected $right = '';
	protected $align_right = '';

	function set_left( $left )
	{
		$this->left = $left;
		return $this;
	}
	function left()
	{
		return $this->left;
	}

	function set_right( $right, $align_right = TRUE )
	{
		$this->right = $right;
		$this->align_right = $align_right;
		return $this;
	}
	function right()
	{
		return $this->right;
	}

	function render()
	{
		$left = HC_Html_Factory::element('li')
			->add_attr('class', 'squeeze-in')
			->add_attr('class', 'pull-left')
			->add_attr('style', 'width: 50%;')
				->add_child( $this->left() )
			;
		$right = HC_Html_Factory::element('li')
			->add_attr('class', 'squeeze-in')
			->add_attr('class', 'pull-right')
			->add_attr('style', 'width: 50%;')
				->add_child( $this->right() )
			;
		if( $this->align_right ){
			$right->add_attr('class', 'text-right');
		}
		$clearfix = HC_Html_Factory::element('div')
			->add_attr('class', 'clearfix')
			->add_child( '' )
			;

		$out = HC_Html_Factory::element('ul')
			->add_attr('class', 'list-inline')
			->add_child( $left )
			->add_child( $right )
			;

		$total = array();
		$total[] = $out;
		$total[] = $clearfix;

		$return = '';
		foreach( $total as $t ){
			$return .= $t->render();
		}
		return $return;
	}
}
?>
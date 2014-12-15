<?php
class HC_Html_Widget_Grid extends HC_Html_Element
{
	protected $items = array();

	function add_item( $item, $width )
	{
		$this->items[] = array( $item, $width );
		return $this;
	}

	function items()
	{
		return $this->items;
	}

	function render()
	{
		$out = HC_Html_Factory::element('div');
		$out->add_attr( 'class', 'row' );

		$items = $this->items();
		foreach( $items as $item )
		{
			list( $item, $width ) = $item;
			$out->add_child( 
				HC_Html_Factory::element('div')
					->add_attr( 'class', 'col-sm-' . $width )
					->add_child( $item )
				);
		}
		return $out->render();
	}
}
?>
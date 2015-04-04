<?php
class HC_Html_Widget_Grid extends HC_Html_Element
{
	protected $items = array();

	function add_item( $item, $width, $more_attr = array() )
	{
		$this->items[] = array( $item, $width, $more_attr );
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
		$attr = $this->attr();
		foreach( $attr as $k => $v ){
			$out->add_attr( $k, $v );
		}

		$items = $this->items();
		foreach( $items as $item ){
			list( $item, $width, $more_attr ) = $item;

			$slot = HC_Html_Factory::element('div')
				->add_attr( 'class', 'col-sm-' . $width )
				;

			if( $more_attr ){
				foreach( $more_attr as $k => $v ){
					$slot->add_attr( $k, $v );
				}
			}
			$slot->add_child( $item );

			$out->add_child( $slot );
		}
		return $out->render();
	}
}
?>
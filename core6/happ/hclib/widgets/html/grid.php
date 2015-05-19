<?php
class HC_Html_Widget_Grid extends HC_Html_Element
{
	protected $items = array();
	protected $scale = 'sm'; // can be sm, lg, xs, md
	protected $slim = FALSE;

	function add_item( $item, $width, $more_attr = array() )
	{
		$this->items[] = array( $item, $width, $more_attr );
		return $this;
	}
	function items()
	{
		return $this->items;
	}

	function slim()
	{
		return $this->slim;
	}
	function set_slim( $slim = TRUE )
	{
		$this->slim = $slim;
		return $this;
	}

	function scale()
	{
		return $this->scale;
	}
	function set_scale( $scale )
	{
		$this->scale = $scale;
		return $this;
	}

	function render()
	{
		$slim = $this->slim();
		$out = HC_Html_Factory::element('div');
		$out
			->add_attr( 'class', 'row' )
			;
		if( $slim ){
			$out
				->add_attr( 'class', 'row-slim' )
				;
		}

		$attr = $this->attr();
		foreach( $attr as $k => $v ){
			$out->add_attr( $k, $v );
		}

		$scale = $this->scale();
		$items = $this->items();
		foreach( $items as $item ){
			list( $item, $width, $more_attr ) = $item;

			$slot = HC_Html_Factory::element('div')
				->add_attr( 'class', 'col-' . $scale . '-' . $width )
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
<?php
include_once( dirname(__FILE__) . '/container.php' );
class HC_Html_Widget_List_Folded extends HC_Html_Widget_Container
{
	function add_item( $item, $folding_key )
	{
		$this->items[] = array( $item, $folding_key );
		return $this;
	}

	function render()
	{
		$folded = array();
		$items = $this->items();
		foreach( $items as $item )
		{
			list($item, $folding_key) = $item;
			if( ! isset($folded[$folding_key]) )
			{
				$folded[$folding_key] = array();
			}
			$folded[$folding_key][] = $item;
		}

		$out = HC_Html_Factory::element( 'ul' )
			->add_attr('class', 'list-unstyled')
			->add_attr('class', 'collapse-panel')
			;
		$attr = $this->attr();
		foreach( $attr as $k => $v )
		{
			$out->add_attr( $k, $v );
		}
		reset( $folded );
		foreach( $folded as $fk => $items )
		{
			$collapser = HC_Html_Factory::element('a')
				->add_attr('href', '#')
				->add_attr('data-toggle', 'collapse-next')
				->add_attr('class', 'display-block')
				->add_child( 'HAHA' )
				;
			$out->add_child(
				HC_Html_Factory::element('li')
					->add_child( $collapser )
				); 

			$subout = HC_Html_Factory::widget( 'list' );
			foreach( $items as $item )
			{
				$subout->add_item( $item );
			}

			$out->add_child(
				HC_Html_Factory::element('li')
					->add_attr('class', 'collapse')
					->add_child( $subout )
				);
		}

		return $out->render();
	}
}
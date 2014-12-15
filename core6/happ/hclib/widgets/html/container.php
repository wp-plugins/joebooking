<?php
class HC_Html_Widget_Container extends HC_Html_Element
{
	protected $items = array();

	function __construct()
	{
		parent::__construct( 'container' );
	}

	function add_item( $item, $item_value = NULL )
	{
		if( $item_value === NULL )
			$this->items[] = $item;
		else
			$this->items[$item] = $item_value;
		return $this;
	}

	function remove_item( $key )
	{
		unset( $this->items[$key] );
		return $this;
	}

	function set_items( $items )
	{
		$this->items = $items;
		return $this;
	}
	function items()
	{
		return $this->items;
	}

	function render()
	{
		$out = '';

		$items = $this->items();
		foreach( $items as $item )
		{
			if( is_object($item) )
			{
				$out .= $item->render();
			}
			else
			{
				$out .= $item;
			}
		}
		return $out;
	}
}
?>
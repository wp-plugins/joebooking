<?php
include_once( dirname(__FILE__) . '/container.php' );
class HC_Html_Widget_List extends HC_Html_Widget_Container
{
	private $active = NULL;
	private $item_attr = array();

	function add_item_attr( $item, $key, $value )
	{
		if( ! isset($this->item_attr[$item]) ){
			$this->item_attr[$item] = HC_Html_Factory::element('a');
		}
		$this->item_attr[$item]->add_attr( $key, $value );
		return $this;
	}
	function item_attr( $item, $key = '' )
	{
		if( ! isset($this->item_attr[$item]) ){
			$this->item_attr[$item] = HC_Html_Factory::element('a');
		}
		return $this->item_attr[$item]->attr( $key );
	}

	function set_active( $active )
	{
		$this->active = $active;
		return $this;
	}
	function active()
	{
		return $this->active;
	}

	function add_divider()
	{
		$items = $this->items();
		if( count($items) )
			$this->add_item( '-divider-' );
		return $this;
	}

	function render()
	{
		$out = HC_Html_Factory::element( 'ul' );
		$attr = $this->attr();
		foreach( $attr as $k => $v ){
			$out->add_attr( $k, $v );
		}

		$items = $this->items();
		foreach( $items as $key => $item )
		{
			$li = HC_Html_Factory::element('li');
			$item_attr = $this->item_attr($key);
			foreach( $item_attr as $k => $v ){
				$li->add_attr( $k, $v );
			}

			if( is_string($item) && ($item == '-divider-') )
			{
				$item = '&nbsp;';
				$li->add_attr('class', 'divider');
			}
			else
			{
				if( $key === $this->active() )
				{
					$li->add_attr('class', 'active');
				}
				if(
					in_array('nav', $out->attr('class')) && 
					(
						is_string($item)
						OR
						(
						is_object($item) &&
						method_exists($item, 'tag') && 
						( $item->tag() == 'input' )
						)
					)
					)
				{
					$item = HC_Html_Factory::element('span')
						->add_child( $item )
						;
				}
			}

			$li->add_child( $item );
			$out->add_child( $li );
		}

		return $out->render();
	}
}
?>
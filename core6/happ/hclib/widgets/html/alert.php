<?php
include_once( dirname(__FILE__) . '/container.php' );
class HC_Html_Widget_Alert extends HC_Html_Widget_Container
{
	private $type = array(); // may be success, success-o, danger, danger-o, info, archive, archive-o

	function __construct()
	{
		parent::__construct('div');
	}

	function render()
	{
		$out = HC_Html_Factory::element('div')
			->add_attr('class', 'alert')
			->add_attr('class', 'display-block')
			;

		$attr = $this->attr();
		foreach( $attr as $k => $v ){
			$out->add_attr( $k, $v );
		}

		$items = $this->items();
		if( count($items) > 1 ){
			$list = HC_Html_Factory::widget('list')
				->add_attr('class', 'list-unstyled')
				;
			$list->set_items( $items );
			$out->add_child( $list );
		}
		else {
			$out->add_child( $items );
		}
		return $out->render();
	}
}
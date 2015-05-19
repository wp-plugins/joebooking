<?php
include_once( dirname(__FILE__) . '/list.php' );
class HC_Html_Widget_List_Ajax extends HC_Html_Widget_List
{
	function render()
	{
		$wrap = HC_Html_Factory::element('div')
			->add_attr('class', 'hc-ajax-parent')
			;
		$container = HC_Html_Factory::element('div')
			->add_attr('class', 'hc-ajax-container')
			;

		$list = parent::render();

		$wrap->add_child( $list );
		$wrap->add_child( $container );

		return $wrap->render();
	}
}
?>
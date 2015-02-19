<?php
include_once( dirname(__FILE__) . '/container.php' );
class HC_Html_Widget_Table extends HC_Html_Widget_Container
{
	protected $header = array();
	protected $rows = array();

	function add_row($row)
	{
		$this->rows[] = $row;
	}
	function rows()
	{
		return $this->rows;
	}

	function set_header( $header )
	{
		$this->header = $header;
		return $this;
	}
	function header()
	{
		return $this->header;
	}

	function render()
	{
		$out = HC_Html_Factory::element( 'table' );
		$attr = $this->attr();
		foreach( $attr as $k => $v ){
			$out->add_attr( $k, $v );
		}
		// $out->add_attr('border', 1);

		$rows = $this->rows();
		foreach( $rows as $row )
		{
			$tr = HC_Html_Factory::element('tr');
			foreach( $row as $r ){
				$td = HC_Html_Factory::element('td');
				$td->add_child( $r );
				$tr->add_child( $td );
				}
			$out->add_child( $tr );
		}

		return $out->render();
	}
}
?>
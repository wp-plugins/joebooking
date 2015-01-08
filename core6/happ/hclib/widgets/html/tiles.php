<?php
class HC_Html_Widget_Tiles extends HC_Html_Widget_Container
{
	protected $per_row = 3;

	function set_per_row( $per_row )
	{
		$this->per_row = $per_row;
		return $this;
	}
	function per_row()
	{
		return $this->per_row;
	}

	function render()
	{
		$out = array();
		$items = $this->items();
		$per_row = $this->per_row();
		$number_of_rows = ceil( count($items) / $per_row );

		$row_class = 'row';
		switch( $per_row )
		{
			case 1:
				$tile_class = array('col-sm-12');
				break;
			case 2:
				$tile_class = array('col-sm-6');
				break;
			case 3:
				$tile_class = array('col-sm-4');
				break;
			case 4:
				$tile_class = array('col-sm-3');
				break;
			case 6:
				$tile_class = array('col-sm-6');
				break;
		}

		for( $ri = 0; $ri < $number_of_rows; $ri++ )
		{
			$row = HC_Html_Factory::element('div')->add_attr('class', $row_class, 1);
			for( $ii = ($ri*$per_row); $ii < (($ri+1)*$per_row); $ii++ )
			{
				if( isset($items[$ii]) )
				{
					$tile = HC_Html_Factory::element('div')->add_attr('class', $tile_class, 1);
					$row->add_child( 
						$tile->add_child($items[$ii])
						);
				}
			}
			$out[] = $row;
		}

		$return = '';
		foreach( $out as $o )
		{
			$return .= $o->render();
		}
		return $return;
	}
}
?>
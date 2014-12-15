<?php
class HC_Html_Element_Input_Date extends HC_Html_Element_Input_Text
{
	function __construct( $name, $value, $more )
	{
		$id = 'nts-' . $name;
		$display_name = $name . '_display';
		$display_id = 'nts-' . $display_name;

		$t = HC_Lib::time();

		$js_options = array();
		if( isset($more['options']) )
		{
			reset( $more['options'] );
			foreach( $more['options'] as $k => $v )
			{
				switch( $k )
				{
					case 'startDate':
						if( $v > $value )
						{
							$value = $v;
						}
						$t->setDateDb( $v );
						$v = $t->formatDate();
						break;
				}
				$js_options[] = "$k: \"$v\"";
			}
		}
		$js_options[] = "weekStart: " . $t->weekStartsOn;
		$js_options = join( ",\n", $js_options );
		unset( $more['options'] );

		$value ? $t->setDateDb( $value ) : $t->setNow();
		$value = $t->formatDate_Db();
		$display_value = $t->formatDate();

		parent::__construct( $display_name, $display_value, $more );

		$datepicker_format = $t->formatToDatepicker();

		$this
			->add_attr('id', $display_id)
			->add_attr('data-date-format', $datepicker_format)
			->add_attr('data-date-week-start', $t->weekStartsOn)
			;
		$this->add_attr( 'style', 'width: 8em' );

		$hidden = HC_Html_Factory::input( 'hidden', $name, $value );
		$hidden->add_attr( 'id', $id );
		$this->add_addon( $hidden );

		$script = HC_Html_Factory::element( 'script' );
		$script->add_attr( 'language', 'JavaScript' );
		$js_code = <<<EOT

jQuery('#$display_id').datepicker({
	$js_options,
	dateFormat: '$datepicker_format',
	autoclose: true
	})
	.on('changeDate', function(ev)
		{
		var dbDate = 
			ev.date.getFullYear() 
			+ "" + 
			("00" + (ev.date.getMonth()+1) ).substr(-2)
			+ "" + 
			("00" + ev.date.getDate()).substr(-2);
		jQuery('#$id').val( dbDate );
		});

EOT;
		$script->add_child( $js_code );
		$this->add_addon( $script );
	}
}

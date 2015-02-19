<?php
class HC_Form_Input_Date extends HC_Form_Input_Text
{
	protected $options = array();

	function add_option( $k, $v )
	{
		$this->options[$k] = $v;
	}
	function options()
	{
		return $this->options;
	}

	function render()
	{
		$name = $this->name();
		$value = $this->value();
		$id = 'nts-' . $name;

		$t = HC_Lib::time();
		$value ? $t->setDateDb( $value ) : $t->setNow();
		$value = $t->formatDate_Db();

		$out = HC_Html_Factory::widget('container');

	/* hidden field to store our value */
		$hidden = HC_Html_Factory::input('hidden')
			->set_name( $name )
			->set_value( $value )
			->set_id($id)
			;
		$out->add_item( $hidden );

	/* text field to display */
		$display_name = $name . '_display';
		$display_id = 'nts-' . $display_name;
		$datepicker_format = $t->formatToDatepicker();
		$display_value = $t->formatDate();

		$text = HC_Html_Factory::input('text')
			->set_name( $display_name )
			->set_value( $display_value )
			->set_id($display_id)
			->add_attr('data-date-format', $datepicker_format)
			->add_attr('data-date-week-start', $t->weekStartsOn)
			->add_attr( 'style', 'width: 8em' )
			;
		$out->add_item( $text );

	/* JavaScript to make it work */
		$js_options = array();
		
		$options = $this->options();
		foreach( $options as $k => $v )
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

		$js_options[] = "weekStart: " . $t->weekStartsOn;
		$js_options = join( ",\n", $js_options );

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
		$out->add_item( $script );

		$return = $this->decorate( $out->render() );
		return $return;
	}
}

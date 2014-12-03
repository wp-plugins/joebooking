<?php
class HC_Html_Element_Input_Timeframe extends HC_Html_Element_Input
{
	function __construct( $name, $value = array(), $more = array() )
	{
		$this->set_tag( 'ul' );
		$this->add_attr( 'class', 'list-inline', 1 );
		$this->add_attr( 'class', 'list-separated', 1 );

		$start = HC_Html_Factory::input( 'time', 'start', '', $more );
		$this->add_child( 
			HC_Html_Factory::element('li')
				->add_child( $start )
			);

		$this->add_child( 
			HC_Html_Factory::element('li')
				->add_child( '-' )
			);

		$end = HC_Html_Factory::input( 'time', 'end', '', $more );
		$this->add_child( 
			HC_Html_Factory::element('li')
				->add_child( $end )
			);

		$checkbox = HC_Html_Factory::input( 'checkbox', $name . '_all_day', '', array('label' => lang('time_all_day')) );
		$this->add_child( 
			HC_Html_Factory::element('li')
				->add_child( $checkbox )
			);

		$script = HC_Html_Factory::element( 'script' )
					->set_attr( 'language', 'JavaScript' )
					->add_child("
jQuery('#$display_id').datepicker({
	$js_options,
	dateFormat: '$datepicker_format',
	autoclose: true
	})
	.on('changeDate', function(ev)
		{
		var dbDate = 
			ev.date.getFullYear() 
			+ '' + 
			('00' + (ev.date.getMonth()+1) ).substr(-2)
			+ '' + 
			('00' + ev.date.getDate()).substr(-2);
		jQuery('#$id').val( dbDate );
		});
");

		$this->add_addon( $script );

	}
}

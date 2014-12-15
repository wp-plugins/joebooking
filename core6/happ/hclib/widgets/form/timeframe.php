<?php
class HC_Form_Input_Timeframe extends HC_Form_Input
{
	protected $start;
	protected $end;

	function __construct( $name )
	{
		$this->start = HC_Html_Factory::input( 'time', $name . '_start' );
		$this->end = HC_Html_Factory::input( 'time', $name . '_end' );
	}

	function set_value( $value )
	{
		parent::set_value( $value );
		$this->start->set_value( $value[0] );
		$this->end->set_value( $value[1] );
	}

	function render()
	{
		$wrap = HC_Html_Factory::widget( 'list' )
			->add_attr('class', array('list-inline', 'list-separated'))
			;
		$wrap->add_item( $this->start );
		$wrap->add_item( ' - ' );
		$wrap->add_item( $this->end );

		return $this->decorate( $wrap->render() );
	}

	function grab( $post )
	{
		$this->start->grab( $post );
		$this->end->grab( $post );

		$value = array(
			$this->start->value(),
			$this->end->value(),
			);

		$this->set_value( $value );
	}

	function _validate()
	{
		$return = parent::_validate();
		/*
		if( $return )
			return $return;

		// check if end is not equal to start
		if( $this->end->value() <= $this->start->value() )
		{
			$return = lang('time_error_end_after_start');
		}
		*/
		return $return;
	}

	function lala( $name, $value = array(), $more = array() )
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
					->add_attr( 'language', 'JavaScript' )
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

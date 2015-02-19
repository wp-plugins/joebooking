<?php
class HC_Form_Input_Timeframe extends HC_Form_Input_Composite
{
	function __construct( $name )
	{
		$this->fields['start'] = HC_Html_Factory::input( 'time', $name . '_start' );
		$this->fields['end'] = HC_Html_Factory::input( 'time', $name . '_end' );
	}

	function render()
	{
		$wrap = HC_Html_Factory::widget( 'list' )
			->add_attr('class', array('list-inline', 'list-separated'))
			;
		$wrap->add_item( $this->fields['start'] );
		$wrap->add_item( ' - ' );
		$wrap->add_item( $this->fields['end'] );

		return $this->decorate( $wrap->render() );
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
}
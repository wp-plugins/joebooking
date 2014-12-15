<?php
class HC_Form_Input_Time extends HC_Form_Input_Select
{
	function __construct( $name = '' )
	{
		parent::__construct( $name );

		$start_with = 0;
		$end_with = 24 * 60 * 60;

/*
		if( isset($more['conf']['min']) && ($more['conf']['min'] > $start_with) )
		{
			$start_with = $more['conf']['min'];
		}
		if( isset($more['conf']['max']) && ($more['conf']['max'] < $end_with) )
		{
			$end_with = $more['conf']['max'];
		}
		unset( $more['conf'] );
*/
		if( $end_with < $start_with )
		{
			$end_with = $start_with;
		}

		$step = 15 * 60;
		$options = array();

		$t = HC_Lib::time();
		$t->setDateDb( 20130118 );

/*
		if( $value && ($value > $end_with) )
		{
			$value = $value - 24 * 60 * 60;
		}
*/
		if( $start_with )
			$t->modify( '+' . $start_with . ' seconds' );

		$no_of_steps = ( $end_with - $start_with) / $step;
		for( $ii = 0; $ii <= $no_of_steps; $ii++ )
		{
			$sec = $start_with + $ii * $step;
			$options[ $sec ] = $t->formatTime();
			$t->modify( '+' . $step . ' seconds' );
		}

		$this->set_options( $options );
	}
}

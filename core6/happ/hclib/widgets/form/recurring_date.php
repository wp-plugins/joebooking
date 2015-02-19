<?php
class HC_Form_Input_Recurring_Date extends HC_Form_Input_Composite
{
	function __construct( $name = '' )
	{
		parent::__construct( $name );

		$name = $this->name();
		$this->fields['datestart'] = HC_Html_Factory::input('date', $name . '_datestart' );
		$this->fields['dateend'] = HC_Html_Factory::input('date', $name . '_dateend' );
		$this->fields['repeat'] = HC_Html_Factory::input('radio', $name . '_repeat' );

		$this->fields['weeklycustom'] = HC_Html_Factory::input('checkbox_set', $name . '_weeklycustom' ); 
		$this->fields['inoutin'] = HC_Html_Factory::input('text', $name . '_inoutin' ); 
		$this->fields['inoutout'] = HC_Html_Factory::input('text', $name . '_inoutout' ); 
	}

	function set_value( $value = array() )
	{
		if( ! is_array($value) ){
			$value = $this->_unserialize( $value );
		}

		parent::set_value( $value );

		$value = $this->value();
		$value = $this->_serialize($value);
		$this->value = $value;
	}

	function dates_details()
	{
		$value = $this->value(TRUE);
		$dates = $this->dates();

		$return = array();

		$t = HC_Lib::time();
		$return[] = $t->formatDateRange( $value['datestart'], $value['dateend'] );
		$t->setDateDb( $value['datestart'] );

		if( $value['dateend'] > $value['datestart'] ){
			switch( $value['repeat'] ){
				case 'daily':
					$return[] = lang('time_daily');
					break;
				case 'weekday':
					$return[] = lang('time_every_weekday') . ' (' . $t->formatWeekdayShort(1) .  ' - ' . $t->formatWeekdayShort(5) . ')';
					break;
				case 'weekly':
					$return[] = lang('time_weekly') . ' (' . lang('time_every') . ' ' . $t->formatWeekdayShort() . ')';
					break;
				case 'weeklycustom':
					$custom_days = array();
					foreach( $value['weeklycustom'] as $wkd ){
						$custom_days[] = $t->formatWeekdayShort($wkd);
					}
					$custom_days = join(', ', $custom_days);
					$return[] = lang('time_weekly') . ' (' . $custom_days . ')';
					break;
				case 'inout':
					$line = array();
					$line[] = $value['inoutin'];
					$line[] = ($value['inoutin'] > 1) ? lang('time_days') : lang('time_day');
					$line[] = lang('time_day_on');
					$line[] = '/';
					$line[] = $value['inoutout'];
					$line[] = ($value['inoutout'] > 1) ? lang('time_days') : lang('time_day');
					$line[] = lang('time_day_off');

					$return[] = join(' ', $line);
					break;
				case 'monthlyday':
					$return[] = lang('time_monthly') . ' (' .  $t->formatWeekdayShort() . ': ' . lang('time_every') . ' ' . $t->formatWeekOfMonth() . ')';
					break;
				case 'monthlydayend':
					$return[] = lang('time_monthly') . ' (' .  $t->formatWeekdayShort() . ': ' . lang('time_every') . ' ' . $t->formatWeekOfMonthFromEnd() . ')';
					break;
				case 'monthlydate':
					$return[] = lang('time_monthly') . ' (' . lang('time_day') . ' ' . $t->getDay() . ')';
					break;
			}
		}

		$return = join(', ', $return);
		if( $value['dateend'] > $value['datestart'] ){
			$return .= ' [' . count($dates) . ']';
		}

		return $return;
	}

	function dates( $serialized = NULL )
	{
		$return = array();

		if( $serialized === NULL ){
			$serialized = $this->value();
		}

		$value = $this->_unserialize( $serialized );

		$t = HC_Lib::time();
		$t->setDateDb( $value['datestart'] );
		$rex_date = $value['datestart'];
		while( $rex_date <= $value['dateend'] ){
			switch( $value['repeat'] ){
				case 'daily':
					$return[] = $rex_date;
					$t->modify( '+1 day' );
					break;

				case 'weekday':
					if( ! in_array($t->getWeekday(), array(0,6)) ){
						$return[] = $rex_date;
					}
					$t->modify( '+1 day' );
					while( in_array($t->getWeekday(), array(0,6)) ){
						$t->modify( '+1 day' );
					}
					break;

				case 'weekly':
					$return[] = $rex_date;
					$t->modify( '+1 week' );
					break;

				case 'weeklycustom':
					$custom_weekday = $value['weeklycustom'];
					if( in_array($t->getWeekday(), $custom_weekday) ){
						$return[] = $rex_date;
					}
					$t->modify( '+1 day' );
					if( $custom_weekday ){
						while( ! in_array($t->getWeekday(), $custom_weekday) ){
							$t->modify( '+1 day' );
						}
					}
					break;
				case 'monthlyday':
					$return[] = $rex_date;
					$this_week = $t->getWeekOfMonth();
					$t->modify( '+4 weeks' );
					while( $t->getWeekOfMonth() != $this_week ){
						$t->modify( '+1 week' );
					}
					break;
				case 'monthlydayend':
					$return[] = $rex_date;
					$this_week = $t->getWeekOfMonthFromEnd();
					$t->modify( '+4 weeks' );
					while( $t->getWeekOfMonthFromEnd() != $this_week ){
						$t->modify( '+1 week' );
					}
					break;
				case 'monthlydate':
					$return[] = $rex_date;
					$t->modify( '+1 month' );
					break;
				case 'inout':
					$return[] = $rex_date;
					$in_out_in = $value['inoutin'];
					$in_out_out = $value['inoutout'];
					if( ! isset($in_out_count) )
						$in_out_count = 1;
					if( $in_out_count < $in_out_in ){
						$t->modify( '+1 day' );
						$in_out_count++;
					}
					else {
						$in_out_count = 1;
						$t->modify( '+' . ($in_out_out+1) . ' day' );
					}
					break;
			}
			$rex_date = $t->formatDate_Db();
		}
		return $return;
	}

	private function _unserialize( $value )
	{
		$return = array();
		$value = explode('_', $value);

		$return['datestart'] = array_shift($value);
		$return['dateend'] = array_shift($value);

		$repeat = array_shift($value);
		$return['repeat'] = $repeat;

		switch( $repeat ){
			case 'weeklycustom':
				$return['weeklycustom'] = $value;
				break;
			case 'inout':
				$return['inoutin'] = array_shift( $value );
				$return['inoutout'] = array_shift( $value );
				break;
		}
		return $return;
	}

	private function _serialize( $value )
	{
		$return = array();
		$return[] = $value['datestart'];
		$return[] = $value['dateend'];

		$repeat = $value['repeat'];
		$return[] = $repeat;

		switch( $repeat ){
			case 'weeklycustom':
				$return[] = join('_', $value['weeklycustom']);
				break;
			case 'inout':
				$return[] = $value['inoutin'];
				$return[] = $value['inoutout'];
				break;
		}
		$return = join('_', $return);
		return $return;
	}

	function value( $need_array = FALSE )
	{
		$return = parent::value();
		if( $need_array && (! is_array($return)) ){
			$return = $this->_unserialize($return);
		}
		return $return;
	}

	function render()
	{
		$value = $this->value(TRUE);

		$t = HC_Lib::time();
		if( isset($value['datestart']) ){
			$t->setDateDb( $value['datestart'] );
		}

		$wrap = HC_Html_Factory::widget( 'list' )
			->add_attr('class', array('list-unstyled', 'list-separated'))
			;

		/* DATES */
		$item_dates = HC_Html_Factory::widget( 'list' )
			->add_attr('class', array('list-inline', 'list-separated'))
			;
		$item_dates->add_item( $this->fields['datestart'] );
		$item_dates->add_item( ' - ' );
		$item_dates->add_item( $this->fields['dateend'] );

		$wrap->add_item( $item_dates );

		/* RECURRING OPTIONS */
		$repeat = clone $this->fields['repeat'];

		$repeat->add_option( 'daily', 
			lang('time_daily')
			);
		$repeat->add_option( 'weekday',
			lang('time_every_weekday') . ' (' . $t->formatWeekdayShort(1) .  ' - ' . $t->formatWeekdayShort(5) . ')'
			);
		$repeat->add_option( 'weekly',
			lang('time_weekly') . ' (' . lang('time_every') . ' ' . $t->formatWeekdayShort() . ')'
			);

		$weekly_custom = clone $this->fields['weeklycustom'];
		$this_weekday = $t->getWeekday();

		if( ! $weekly_custom->value() ){
			$weekly_custom->set_value( array($this_weekday) );
		}

		$wkds = array( 0, 1, 2, 3, 4, 5, 6 );
		$wkds = $t->sortWeekdays( $wkds );
		foreach( $wkds as $wkd ){
			$weekly_custom->add_option($wkd, $t->formatWeekdayShort($wkd));
		}
		$weekly_custom->set_readonly($this_weekday);

		$repeat->add_option( 'weeklycustom',
			lang('time_weekly') . ' (' . lang('time_custom_days') . ')',
			$weekly_custom
			);

		if( ! $this->fields['inoutin']->value() ){
			$this->fields['inoutin']->set_value(2);
		}
		if( ! $this->fields['inoutout']->value() ){
			$this->fields['inoutout']->set_value(2);
		}

		$repeat->add_option( 'inout',
			'X ' . lang('time_days') . ' ' . lang('time_day_on') . ' / ' . 'Y ' . lang('time_days') . ' ' . lang('time_day_off'),
			HC_Html_Factory::widget('list')
				->add_attr('class', array('list-inline', 'list-separated'))
				->add_item( $this->fields['inoutin']->add_attr('size', 2) )
				->add_item( lang('time_days') . ' ' . lang('time_day_on') )
				->add_item( $this->fields['inoutout']->add_attr('size', 2) )
				->add_item( lang('time_days') . ' ' . lang('time_day_off') )
			);

		$repeat->add_option( 'monthlyday',
			lang('time_monthly') . ' (' .  $t->formatWeekdayShort() . ': ' . lang('time_every') . ' ' . $t->formatWeekOfMonth() . ')'
			);
		$repeat->add_option( 'monthlydayend',
			lang('time_monthly') . ' (' .  $t->formatWeekdayShort() . ': ' . lang('time_every') . ' ' . $t->formatWeekOfMonthFromEnd() . ')'
			);
		$repeat->add_option( 'monthlydate',
			lang('time_monthly') . ' (' . lang('time_day') . ' ' . $t->getDay() . ')'
			);

		$wrap->add_item( $repeat );

		$return = $wrap->render();
		$return = $this->decorate( $return );

		return $return;
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
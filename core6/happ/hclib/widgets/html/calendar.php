<?php
class HC_Html_Widget_Calendar
{
	private $range = 'week'; // may be week or month
	private $date = '';
	private $content = array();
	private $wide_slot = TRUE;

	function __construct( $date = '' )
	{
		$t = HC_Lib::time();
		$t->setNow();
		$this->set_date( $t->formatDate_Db() );
	}

	function dates()
	{
		$t = HC_Lib::time();
		$t->setDateDb( $this->date() );
		$return = $t->getDates( $this->range() );
		return $return;
	}

	function set_date_content( $date, $content )
	{
		$this->content[$date] = $content;
	}
	function date_content( $date )
	{
		return isset($this->content[$date]) ? $this->content[$date] : NULL;
	}

	function set_date( $date )
	{
		$this->date = $date;
	}
	function date()
	{
		return $this->date;
	}

	function set_wide_slot( $wide_slot )
	{
		$this->wide_slot = $wide_slot;
	}
	function wide_slot()
	{
		return $this->wide_slot;
	}

	function set_range( $range )
	{
		$this->range = $range;
	}
	function range()
	{
		return $this->range;
	}

	function render()
	{
		$t = HC_Lib::time();

		$t->setDateDb( $this->date() );

		switch( $this->range() )
		{
			case 'week':
				$t->setDateDb( $this->date() );

				$t->setStartWeek();
				$start_date = $t->formatDate_Db();
				$t->setEndWeek();
				$end_date = $t->formatDate_Db();
				break;

			case 'month':
				$t->setDateDb( $this->date() );

				$t->setStartMonth();
				$start_date = $t->formatDate_Db();
				$t->setEndMonth();
				$end_date = $t->formatDate_Db();
				break;
		}

		$t->setDateDb( $start_date );
		$month_matrix = $t->getMonthMatrix( $end_date );

		$out = HC_Html_Factory::element('div')
			->add_attr('class', 'hc_cal')
			;

		$slot_width = 1;
	/* wide slot */
		if( ($this->range() == 'week') && ($this->wide_slot()) )
		{
			$slot_width = 3;
			$month_matrix = array( 
				array_slice($month_matrix[0], 0, 4),
				array_slice($month_matrix[0], 4)
				);
		}

		foreach( $month_matrix as $week => $days )
		{
			$grid = HC_Html_Factory::widget('grid')
				->add_attr('class', 'hc-cal-row')
				;
 
			foreach( $days as $rex_date )
			{
				$t->setDateDb( $rex_date );

				$day = HC_Html_Factory::element('div')
					->add_attr('class', 'thumbnail')
					->add_attr('class', 'squeeze-in')
					;

/*
				$label = HC_Html_Factory::widget('list')
					->add_attr('class', array('nav', 'nav-stacked'))
					->add_item(
						HC_Html_Factory::element('h4')
							->add_child( $t->formatWeekdayShort() )
							->add_child(' ')
							->add_child(
								HC_Html_Factory::element('small')
									->add_child( $t->formatDate() )
								)
							)
					->add_divider()
					;
				$day->add_child( $label );
*/

				$date_content = $this->date_content($rex_date);
				if( $date_content )
				{
					$day->add_child( $date_content );
				}
				else
				{
					$day = '';
				}
				$grid->add_item( 
					$day,
					$slot_width,
					array(
						'class'	=> 'hc-cal-day'
						)
					);
			}
			$out->add_child( $grid );

		}
		return $out->render();
	}
}

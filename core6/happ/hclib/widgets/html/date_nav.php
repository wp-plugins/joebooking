<?php
include_once( dirname(__FILE__) . '/list.php' );
class HC_Html_Widget_Date_Nav extends HC_Html_Widget_List
{
	private $range = 'week'; // may be week or day
	private $link = NULL;
	private $date_param = 'date';
	private $range_param = 'range';
	private $date = '';
	private $params = array();

	function __construct( $start = '' )
	{
		$t = HC_Lib::time();
		$t->setNow();
		$this->set_date( $t->formatDate_Db() );
	}

	function set_param( $key, $value )
	{
		$this->params[ $key ] = $value;
		return $this;
	}
	function params()
	{
		return $this->params;
	}
	function param( $key )
	{
		return isset($this->params[$key]) ? $this->params[$key] : NULL;
	}

	function set_date( $date )
	{
		$this->date = $date;
		$this->set_param( $this->date_param(), $date );
	}
	function date()
	{
		return $this->date;
	}

	function set_range( $range )
	{
		$this->range = $range;
		$this->set_param( $this->range_param(), $range );
	}
	function range()
	{
		return $this->range;
	}

	function set_link( $link )
	{
		$this->link = $link;
	}
	function link()
	{
		return $this->link;
	}

	function set_date_param( $param )
	{
		$this->date_param = $param;
	}
	function date_param()
	{
		return $this->date_param;
	}

	function set_range_param( $param )
	{
		$this->range_param = $param;
	}
	function range_param()
	{
		return $this->range_param;
	}

	function render()
	{
		if( ! $link = $this->link() )
		{
			return 'HC_Html_Widget_Date_Nav: link is not set!';
		}

		$link_params = $this->params();
		$t = HC_Lib::time();
		switch( $this->range() )
		{
			case 'week':
				$t->setDateDb( $this->date() );

				$t->setStartWeek();
				$start_date = $t->formatDate_Db();
				$t->setEndWeek();
				$end_date = $t->formatDate_Db();

				$nav_title = $t->formatDateRange( $start_date, $end_date );

				$t->setDateDb( $this->date() );
				$t->modify( '-1 week' );
				$t->setStartWeek();
				$before_date =  $t->formatDate_Db();

				$t->setDateDb( $this->date() );
				$t->setEndWeek();
				$t->modify( '+1 day' );
				$after_date =  $t->formatDate_Db();
				break;

			case 'month':
				$t->setDateDb( $this->date() );
				$month_view = $t->getMonthName() . ' ' . $t->getYear();

				$t->setDateDb( $this->date() );
				$t->modify( '-1 month' );
				$t->setStartMonth();
				$before_date =  $t->formatDate_Db();

				$t->setDateDb( $this->date() );
				$t->setEndMonth();
				$t->modify( '+1 day' );
				$after_date =  $t->formatDate_Db();

				$nav_title = $month_view;
				break;
		}

//		$this->add_attr('class', 'pagination');
		$this->add_attr('class', array('nav', 'nav-pills'));

		$this->add_item( 
			'before',
			HC_Html_Factory::element('a')
				->add_attr(
					'href',
					$link->set_params($link_params)->url(
						array(
							$this->date_param() => $before_date
							)
						)
					)
				->add_attr('class', array('btn', 'btn-default'))
				->add_child('&lt;&lt;')
			);

		$current_nav = HC_Html_Factory::widget('dropdown')
			->set_title( $nav_title )
			;

		switch( $this->range() )
		{
			case 'week':
				$current_nav->add_item(
					HC_Html_Factory::element('a')
						->add_child( lang('time_month') )
						->add_attr(
							'href',
							$link->set_params($link_params)->url(
								array(
									$this->range_param() => 'month'
									)
								)
							)
					);
				break;

			case 'month':
				$current_nav->add_item(
					HC_Html_Factory::element('a')
						->add_child( lang('time_week') )
						->add_attr(
							'href',
							$link->set_params($link_params)->url(
								array(
									$this->range_param() => 'week'
									)
								)
							)
					);
				break;
		}

		$this->add_item( 
			'current',
			$current_nav
			);

		$this->add_item( 
			'after',
			HC_Html_Factory::element('a')
				->add_attr(
					'href',
					$link->set_params($link_params)->url(
						array(
							$this->date_param() => $after_date
							)
						)
					)
				->add_attr('class', array('btn', 'btn-default'))
				->add_child('&gt;&gt;')
			);

		$this->set_active( 'current' );
		return parent::render();
	}
}

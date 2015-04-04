<?php
include_once( dirname(__FILE__) . '/list.php' );
class HC_Html_Widget_Date_Nav extends HC_Html_Widget_List
{
	private $range = 'week'; // may be week or day
	private $link = NULL;
	private $date_param = 'date';
	private $range_param = 'range';
	private $date = '';
	private $submit_to = '';
	private $enabled = array('day', 'week', 'month', 'custom', 'all');

	function __construct( $start = '' )
	{
		$t = HC_Lib::time();
		$t->setNow();
		$this->set_date( $t->formatDate_Db() );
	}

	function set_date( $date )
	{
		$this->date = $date;
	}
	function date()
	{
		return $this->date;
	}

	function set_enabled( $enabled )
	{
		$this->enabled = $enabled;
	}
	function enabled()
	{
		return $this->enabled;
	}

	function set_submit_to( $submit_to )
	{
		$this->submit_to = $submit_to;
	}
	function submit_to()
	{
		return $this->submit_to;
	}

	function set_range( $range )
	{
		$this->range = $range;
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
		if( ! $link = $this->link() ){
			return 'HC_Html_Widget_Date_Nav: link is not set!';
		}

		$t = HC_Lib::time();

		switch( $this->range() ){
			case 'all':
				$nav_title = lang('common_all');
				$t->setNow();
				$start_date = $end_date = 0;
				$start_date = $end_date = $t->formatDate_Db();
				break;

			case 'custom':
				list( $start_date, $end_date ) = explode('_', $this->date());
				$nav_title = $t->formatDateRange( $start_date, $end_date );
				$nav_title = lang('time_custom_range');

				$t->setDateDb($start_date)->modify('-1 day');
				$before_date =  $t->formatDate_Db();

				$t->setDateDb($end_date)->modify( '+1 day' );
				$after_date =  $t->formatDate_Db();
				break;

			case 'day':
				$t->setDateDb( $this->date() );
				$start_date = $end_date = $t->formatDate_Db();

				$nav_title = $t->formatDateRange( $start_date, $end_date );

				$t->modify( '-1 day' );
				$before_date =  $t->formatDate_Db();

				$t->setDateDb( $this->date() );
				$t->modify( '+1 day' );
				$after_date =  $t->formatDate_Db();
				break;

			case 'week':
				$t->setDateDb( $this->date() );

				$start_date = $t->setStartWeek()->formatDate_Db();
				$end_date = $t->setEndWeek()->formatDate_Db();

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

				$start_date = $t->setStartMonth()->formatDate_Db();
				$end_date = $t->setEndMonth()->formatDate_Db();

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

		// $this->add_attr('class', array('nav', 'nav-pills'));
		$this->add_attr('class', array('list-inline', 'list-separated'));

		$wrap_nav_title = HC_Html_Factory::element('a')
			->add_attr('class', array('btn', 'btn-default'))
			->add_child( $nav_title )
			;

		$current_nav = HC_Html_Factory::widget('dropdown')
			->set_title( $wrap_nav_title )
			;

		$range_options = array(
			'week'	=>
				HC_Html_Factory::element('a')
					->add_child( lang('time_week') )
					->add_attr('href', $link->url( array($this->range_param() => 'week', $this->date_param() => $start_date) )),
			'month'	=>
				HC_Html_Factory::element('a')
					->add_child( lang('time_month') )
					->add_attr('href', $link->url( array($this->range_param() => 'month', $this->date_param() => $start_date) )),
			'day'	=>
				HC_Html_Factory::element('a')
					->add_child( lang('time_day') )
					->add_attr('href', $link->url( array($this->range_param() => 'day', $this->date_param() => $start_date) )),
			'custom'	=>
				HC_Html_Factory::element('a')
					->add_child( lang('time_custom_range') )
					->add_attr('href', $link->url( array($this->range_param() => 'custom', $this->date_param() => $start_date . '_' . $end_date) )),
			'all'	=>
				HC_Html_Factory::element('a')
					->add_child( lang('common_all') )
					->add_attr('href', $link->url( array($this->range_param() => 'all', $this->date_param() => NULL) )),
			);

		$enabled = $this->enabled();
		foreach( $range_options as $k => $v ){
			if( ! in_array($k, $enabled) ){
				continue;
			}
			if( $k != $this->range() ){
				$current_nav->add_item( $range_options[$k] );
			}
		}

		$this->add_item_attr('current', 'class', array('dropdown'));

		switch( $this->range() ){
			case 'custom':
				$this->add_item(
					'current',
					$current_nav
					);

			/* now add form */
				$form = HC_Lib::form()
					->set_input( 'start_date', 'date' )
					->set_input( 'end_date', 'date' )
					;

				$form->set_values( 
					array(
						'start_date'	=> $start_date,
						'end_date'		=> $end_date,
						)
					);

				$display_form = HC_Html_Factory::widget('form')
					->add_attr('action', $this->submit_to() )
					;

				$display_form
					->add_item(
						HC_Html_Factory::widget('list')
							->add_attr('class', 'list-inline')
							->add_attr('class', 'list-separated')
							->add_item(
								$form->input('start_date')
								)
							->add_item('-')
							->add_item(
								$form->input('end_date')
								)
							->add_item(
								HC_Html_Factory::element('input')
									->add_attr('type', 'submit')
									->add_attr('class', array('btn', 'btn-default'))
									->add_attr('title', lang('common_ok') )
									->add_attr('value', lang('common_ok') )
								)
						)
					;
				$this->add_item( 'form', $display_form );
				break;

			case 'all':
				$this->add_item(
					'current',
					$current_nav
					);
				break;

			default:
				$this->add_item( 
					'before',
					HC_Html_Factory::element('a')
						->add_attr('href', $link->url(array($this->date_param() => $before_date)))
						->add_attr('class', array('btn', 'btn-default'))
						->add_child('&lt;&lt;')
					);

				$this->add_item( 
					'current',
					$current_nav
					);

				$this->add_item( 
					'after',
					HC_Html_Factory::element('a')
						->add_attr( 'href', $link->url(array($this->date_param() => $after_date)) )
						->add_attr('class', array('btn', 'btn-default'))
						->add_child('&gt;&gt;')
					);

				break;
		}

		$this->set_active( 'current' );
		return parent::render();
	}
}

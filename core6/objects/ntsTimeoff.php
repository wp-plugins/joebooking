<?php
class ntsTimeoff extends ntsObject {
	function __construct()
	{
		parent::ntsObject( 'timeoff' );
	}

	function get_conflicts()
	{
		$return = array();
		if( ntsLib::hasVar( 'admin::tm2' ) )
		{
			$tm2 = ntsLib::getVar( 'admin::tm2' );
		}
		else
		{
			$tm2 = new haTimeManager2();
		}

		$where = array(
			'(starts_at + duration + lead_out)'	=> array('>', $this->getProp('starts_at')),
			'(starts_at - lead_in)'			=> array('<', $this->getProp('ends_at')),
			'completed'						=> array('=', 0),
			'resource_id'					=> array('=', $this->getProp('resource_id')), 
			);
		$conflict_apps = $tm2->getAppointments($where);
		foreach( $conflict_apps as $ca )
		{
			$app = ntsObjectFactory::get('appointment');
			$app->setByArray( $ca );
			$return[] = $app;
		}
		return $return;
	}

	function time_view()
	{
		$t = new ntsTime;
		$t->setTimestamp( $this->getProp('starts_at') );
		$from_date = $t->formatDateFull();
		$from_time = $t->formatTime();
		$t->setTimestamp( $this->getProp('ends_at') );
		$to_date = $t->formatDateFull();
		$to_time = $t->formatTime();

		$time_view = '';
		if( $to_date == $from_date )
		{
			$time_view .= '<i class="fa fa-calendar fa-fw"></i>';
			$time_view .= '<strong>';
			$time_view .= $from_date;
			$time_view .= '</strong>';
			$time_view .= ' ';
			$time_view .= '<i class="fa fa-clock-o fa-fw"></i>' . $from_time . ' - ' . $to_time;
		}
		else
		{
			$time_view .= '<i class="fa fa-calendar fa-fw"></i>';
			$time_view .= '<strong>';
			$time_view .= $from_date . ' - ' . $to_date;
			$time_view .= '</strong>';
		}
		return $time_view;
	}
}
?>
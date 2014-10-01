<?php
$this_link = ntsLink::makeLink('-current-');
$session = new ntsSession;
$session->set_userdata( 'calendar_view', $this_link );

$t = $NTS_VIEW['t'];

$customer = ntsLib::getVar( 'admin/customers/edit::OBJECT' );
$customer_id = $customer->getId();

$where = array(
	'customer_id'	=> array( '=', $customer_id ),
	'completed'		=> array('>=', 0),
	);

$period = $_NTS['REQ']->getParam('period');
if( $period )
{
	$split_by = ( strlen($period) == 6 ) ? 'month' : 'day';
	if( $split_by == 'month' )
	{
		$t->setMonthDb( $period );
		$t->setStartMonth();
		$periodStart = $t->getTimestamp();
		$t->setEndMonth();
		$periodEnd = $t->getTimestamp();
	}
	else
	{
		$t->setDateDb( $period );
		$periodStart = $t->getStartDay();
		$periodEnd = $t->getEndDay();
	}

	$where['(starts_at + duration + lead_out)']	= array('>', $periodStart);
	$where['starts_at'] = array('<', $periodEnd);
}

$tm2 = ntsLib::getVar( 'admin::tm2' );

$all_apps = $tm2->getAppointments(
	$where, 
	'ORDER BY starts_at DESC, id DESC'
	);

$calendar_dir = NTS_APP_DIR . '/panels/admin/manage/calendar';
$display = 'browse';
$split_by = 'month';
require( $calendar_dir . '/_a_prepare_apps.php' );
require( $calendar_dir . '/_a_labels.php' );

if( $period )
{
	$view_file = 'period_list';
	$keys = array_keys( $apps );
	if( isset($keys[0]) )
		$apps = $apps[$keys[0]];
	else
		$apps = array();
}
else
{
	$view_file = 'list';
}

$view = array(
	'display'		=> $display,
	'show_control'	=> FALSE,
	'labels'		=> $labels,
	'apps'			=> $apps,
	'customer_id'	=> $customer_id,
	'stats'			=> $stats,
	);

$this->render(
	$calendar_dir . '/views/' . $view_file . '.php',
	$view
	);
?>
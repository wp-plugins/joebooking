<?php
$current_filter = ntsLib::getVar( 'admin/manage:current_filter' );

$t = $NTS_VIEW['t'];
$t->setNow();
$today = $t->formatDate_Db();

if( isset($start_date) && isset($end_date) )
{
	$set_cal = $start_date;
	if( ($set_cal < $today) && ($end_date > $today) )
	{
		$set_cal = $today;
	}
}
else
{
	$set_cal = $today;
}

$add_params = array(
	'cal'			=> $set_cal,
	'nts-filter'	=> '-reset-',
	);
if( isset($current_filter['r']) )
{
	$add_params['resource_id'] = $current_filter['r'];
}
if( isset($current_filter['l']) )
{
	$add_params['location_id'] = $current_filter['l'];
}
if( isset($customer_id) )
{
	$add_params['customer_id'] = $customer_id;
}

$add_link = ntsLink::makeLink(
	'admin/manage/appointments/create',
	'',
	$add_params
	);
?>
<a href="<?php echo $add_link; ?>" class="btn btn-default btn-success">
	<i class="fa fa-plus"></i> <?php echo M('Appointment'); ?>
</a>
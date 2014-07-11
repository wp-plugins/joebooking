<?php
$ntsConf =& ntsConf::getInstance();
$t = $NTS_VIEW['t'];

$btn_status = 'success';
foreach( $selected_status as $k => $v )
{
	if( ! is_array($v) )
		continue;
	$break_this = FALSE;
	foreach( $v as $k2 => $v2 )
	{
		if( is_array($v2) )
		{
			$btn_status = 'danger';
			$break_this = TRUE;
			break;
		}
		elseif( ! $v2 )
		{
			$btn_status = 'archive';
			$break_this = TRUE;
			break;
		}
	}
	if( $break_this )
		break;
}

$changed = FALSE;
if (
	$selected['location_id'] OR 
	$selected['resource_id'] OR 
	$selected['service_id'] OR 
	$selected['starts_at']
)
{
	$changed = TRUE;
}
$check_status = $changed ? $selected_status : $app_status;

$locs_all = ntsLib::getVar( 'admin::locs_all' );
$ress_all = ntsLib::getVar( 'admin::ress_all' );
$sers_all = ntsLib::getVar( 'admin::sers_all' );

$locs = ntsLib::getVar( 'admin::locs' );
$ress = ntsLib::getVar( 'admin::ress' );
$sers = ntsLib::getVar( 'admin::sers' );

$appEdit = ntsLib::getVar( 'admin/manage:appEdit' );
$object = ntsLib::getVar( 'admin/manage/appointments/edit::OBJECT' );
$objectId = $object->getId();
$a = $object->getByArray();

$to_select = array();
if( count($locs) > 1 )
	$to_select[] = 'location';
if( count($ress) > 1 )
	$to_select[] = 'resource';
if( count($sers) > 1 )
	$to_select[] = 'service';
$to_select[] = 'time';

$lid = $object->getProp('location_id');
$current_location = ntsObjectFactory::get('location');
$current_location->setId( $lid );

$rid = $object->getProp('resource_id');
$current_resource = ntsObjectFactory::get('resource');
$current_resource->setId( $rid );

$sid = $object->getProp('service_id');
$current_service = ntsObjectFactory::get('service');
$current_service->setId( $sid );

$starts_at = $object->getProp('starts_at'); 
$current_time = $starts_at;

$createdAt = $object->getProp('created_at'); 
$duration = $object->getProp('duration'); 

$NTS_VIEW['t']->setTimestamp( $starts_at );
$dateView = $NTS_VIEW['t']->formatWeekdayShort() . ', ' . $NTS_VIEW['t']->formatDate();

$timeView = $NTS_VIEW['t']->formatTime();
$NTS_VIEW['t']->modify( '+' . $duration . ' seconds' );
$timeView .= ' - ' . $NTS_VIEW['t']->formatTime();

$customerId = $object->getProp('customer_id');
$customer = new ntsUser;
$customer->setId( $customerId );

$dump = $object->dump( TRUE );
?>

<?php
$current_status = isset($check_status['time'][$starts_at]) ? $check_status['time'][$starts_at] : 0;
if( is_array($current_status) )
	$current_selector_class = 'danger-o';
elseif($current_status)
	$current_selector_class = 'success-o';
else
	$current_selector_class = 'archive-o';
?>

<?php
if( $selected['cal'] )
{
	$cal = $selected['cal'];
}
else
{
	$t->setTimestamp( $starts_at );
	$cal = $t->formatDate_Db();
}

$errors = array();
if( isset($selected_status['time'][$starts_at]) && is_array($selected_status['time'][$starts_at]) )
{
	$errors = $selected_status['time'][$starts_at];
}
$starts_at = $selected['starts_at'];
?>
<?php require( dirname(__FILE__) . '/../../create/views/_index_time.php' ); ?>
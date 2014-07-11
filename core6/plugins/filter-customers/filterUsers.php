<?php
global $NTS_CURRENT_USER;
if( $NTS_CURRENT_USER )
{
	$thisId = $NTS_CURRENT_USER->getId();
	if( $thisId <= 0 )
		return;
}

/* affects only customers */
$check_where = isset($where[0]) ? $where[0] : $where;

if(
	! ( 
		(
		isset($check_where['_role']) &&
		($check_where['_role'][0] == '=') &&
		($check_where['_role'][1] == 'customer')
		)
	OR
		(
		isset($check_where['_restriction'])
		)
	)
	
	
){
	return;
}

$filter_ids = ntsPluginFilterCustumers_AllowCustomers();

/* modify $where */
if( isset($where[0]) )
{
	for( $ii = 0; $ii < count($where); $ii++ )
	{
		$where[$ii]['  id  '] = $filter_ids ? array('IN', $filter_ids) : array('=', 0);
	}
}
else
{
	$where['  id  '] = $filter_ids ? array('IN', $filter_ids) : array('=', 0);
}

//$where['  id  '] = array('IN', $filter_ids );
//$ntsdb->_debug = TRUE;
?>
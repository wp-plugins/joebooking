<?php
function ntsPluginFilterCustumers_AllowCustomers( $thisId = 0 )
{
	global $NTS_CURRENT_USER;

	if( ! $thisId )
	{
		if( ! $NTS_CURRENT_USER )
			return array();
		$thisId = $NTS_CURRENT_USER->getId();
	}

	global $NTS_PLUGIN_FILTER_CUSTOMERS_ALLOW_CUSTOMERS;
	if( ! isset($NTS_PLUGIN_FILTER_CUSTOMERS_ALLOW_CUSTOMERS) )
	{
		$NTS_PLUGIN_FILTER_CUSTOMERS_ALLOW_CUSTOMERS = array();
	}

	if( isset($NTS_PLUGIN_FILTER_CUSTOMERS_ALLOW_CUSTOMERS[$thisId]) )
	{
		return $NTS_PLUGIN_FILTER_CUSTOMERS_ALLOW_CUSTOMERS[$thisId];
	}

	$NTS_PLUGIN_FILTER_CUSTOMERS_ALLOW_CUSTOMERS[$thisId] = array();

	$ntsdb =& dbWrapper::getInstance();

	/* get the resources */
	$ress = array();

	$appPermissions = $NTS_CURRENT_USER->getAppointmentPermissions();
	reset( $appPermissions );
	foreach( $appPermissions as $rid => $pa )
	{
		if( $pa['view'] || $pa['edit'] )
			$ress[] = $rid; 
	}
	$schPermissions = $NTS_CURRENT_USER->getSchedulePermissions();
	reset( $schPermissions );
	foreach( $schPermissions as $rid => $pa )
	{
		if( $pa['view'] || $pa['edit'] )
			$ress[] = $rid; 
	}
	$ress = array_unique( $ress );

	/* get ids of these customers */
	$filter_ids = array();

	$filter_ids1 = array();
	$filter_where = array(
		'resource_id'	=> array( 'IN', $ress ),
		);

	$filter_result = $ntsdb->select( 
		'DISTINCT(customer_id)',
		'appointments',
		$filter_where
		);
	if( $filter_result )
	{
		while( $i = $filter_result->fetch() )
		{
			$filter_ids1[] = $i['customer_id'];
		}
	}

/* also _created_by this one */
	$filter_ids2 = array();
	$filter_where = array(
		'obj_class'		=> array( '=', 'user' ),
		'meta_name'		=> array( '=', '_created_by' ),
		'meta_value'	=> array( '=', $thisId ),
		);

	$filter_result = $ntsdb->select( 
		'DISTINCT(obj_id)',
		'objectmeta',
		$filter_where
	);
	if( $filter_result )
	{
		while( $i = $filter_result->fetch() )
		{
			$filter_ids2[] = $i['obj_id'];
		}
	}

/* also which had no appointments at all */
	global $NTS_PLUGIN_FILTER_CUSTOMERS_NOAPPS;
	if( ! isset($NTS_PLUGIN_FILTER_CUSTOMERS_NOAPPS) )
	{
		$NTS_PLUGIN_FILTER_CUSTOMERS_NOAPPS = array();

		$plugin = 'filter-customers';
		$plm =& ntsPluginManager::getInstance();
		$pluginSettings = $plm->getPluginSettings( $plugin );
		$allowNoApps = 1;
		if( isset($pluginSettings['no_apps']) )
		{
			$allowNoApps = $pluginSettings['no_apps'];
		}

		if( $allowNoApps )
		{
			$sql = "SELECT DISTINCT(customer_id) FROM {PRFX}appointments";
			$already_ids = array();
			$filter_result = $ntsdb->runQuery( $sql );
			if( $filter_result )
			{
				while( $i = $filter_result->fetch() )
				{
					$already_ids[] = $i['customer_id'];
				}
			}

			if( $already_ids )
			{
				$where = array(
					'id'	=> array( 'NOT IN', $already_ids )
					);
			}
			else
			{
				$where = array();
			}

			$uif =& ntsUserIntegratorFactory::getInstance();
			$integrator =& $uif->getIntegrator();
			$NTS_PLUGIN_FILTER_CUSTOMERS_NOAPPS = $integrator->loadUsers( $where );

/*
			foreach( $remain_users as $ru )
			{
				$NTS_PLUGIN_FILTER_CUSTOMERS_NOAPPS[] = $ru['id'];
			}
*/
		/*
			$filter_result = $ntsdb->select(
				'id',
				'users',
				$where
				);
			if( $filter_result )
			{
				while( $i = $filter_result->fetch() )
				{
					$NTS_PLUGIN_FILTER_CUSTOMERS_NOAPPS[] = $i['id'];
				}
			}
		*/
		}
	}

	$filter_ids = array_merge( $filter_ids1, $filter_ids2, $NTS_PLUGIN_FILTER_CUSTOMERS_NOAPPS );
	$filter_ids = array_unique( $filter_ids );
	$NTS_PLUGIN_FILTER_CUSTOMERS_ALLOW_CUSTOMERS[$thisId] = $filter_ids;
	return $filter_ids;
}
?>
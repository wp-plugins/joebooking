<?php
$tm2 = ntsLib::getVar( 'admin::tm2' );
$t = $NTS_VIEW['t'];

switch( $action ){
	case 'export':
		ini_set( 'memory_limit', '256M' );
		break;

	case 'search':
		$ff =& ntsFormFactory::getInstance();
		$formFile = dirname( __FILE__ ) . '/searchForm';
		$NTS_VIEW['searchForm'] =& $ff->makeForm( $formFile );

		if( $NTS_VIEW['searchForm']->validate() )
		{
			$formValues = $NTS_VIEW['searchForm']->getValues();
			$params = array();

			$saveOn = ntsView::getPersistentParams( 'admin/customers/browse' );
			if( $formValues['search_for'] )
			{
				$params['search'] = $formValues['search_for'];
				$saveOn['search'] = $params['search'];
			}
			else
			{
				unset($saveOn['search']);
				ntsView::resetPersistentParam( 'admin/customers/browse', 'search' );
			}

			if( $NTS_VIEW[NTS_PARAM_VIEW_MODE] )
				$params[NTS_PARAM_VIEW_MODE] = $NTS_VIEW[NTS_PARAM_VIEW_MODE];

			ntsView::setPersistentParams( $saveOn, 'admin/customers/browse' );

			$forwardTo = ntsLink::makeLink( '-current-', '', $params );
			ntsView::redirect( $forwardTo, false );
			exit;
		}
		else
		{
		}
		break;

	default:
		break;
	}

$ntsdb =& dbWrapper::getInstance();
$saveOn = array();

$skip = ntsLib::getVar( 'admin/customers/browse::skip' );
$NTS_VIEW['skip'] = $skip;

$ff =& ntsFormFactory::getInstance();

$searchFormParams = array();
if( $search = $_NTS['REQ']->getParam('search') ){
	$searchFormParams['search_for'] = $search;
	}
$search = trim( $search );
$search = strtolower( $search );

$NTS_VIEW['search'] = $search;
$formFile = dirname( __FILE__ ) . '/searchForm';
$NTS_VIEW['searchForm'] =& $ff->makeForm( $formFile, $searchFormParams );

$uif =& ntsUserIntegratorFactory::getInstance();
$integrator =& $uif->getIntegrator();

$total_where = array();
$total_where['_role'] = array( '=', 'customer' );
$NTS_VIEW['grandTotalCount'] = $integrator->countUsers( $total_where );

$showAllDisplays = array(
	'print',
	'excel'
	);

$mainWhere['_role'] = array( '=', 'customer' );
if( $skip )
{
	$mainWhere['id'] = array( 'NOT IN', $skip );
}

if( NTS_EMAIL_AS_USERNAME )
{
	$order = array(
		array( 'email', 'ASC' )
		);
}
else
{
	$order = array(
		array( 'username', 'ASC' ),
		);
}

$display = $_NTS['REQ']->getParam( 'display' );
if( ntsLib::isAjax() )
{
	$display = 'ajax';
}
$limit = '';

switch( $action ){
	case 'export':
		$display = 'excel';
		break;
	default:
		break;
	}

$returnTo = ntsLib::getVar('admin::returnTo');
$showPerPage = $returnTo ? 24 : 12;

$filter = ntsLib::getVar( 'admin/customers/browse::filter' );
$filterIds = array();
switch( $filter )
{
	case 'all':
		break;

	case 'new':
		$res = $integrator->getUsers(
			array(),
			array(
				array('created', 'DESC')
				),
			$showPerPage
			);

		reset( $res );
		foreach( $res as $r )
		{
			$filterIds[] = $r['id'];
		}
		break;

	case 'email_not_confirmed':
	case 'not_approved':
	case 'suspended':
		$res = $integrator->getUsers(
			array(
				'_restriction'	=> array('=', $filter),
				)
			);

		reset( $res );
		foreach( $res as $r )
		{
			$filterIds[] = $r['id'];
		}
		break;

	case 'active':
		$filterIds = $ntsdb->get_select(
			'customer_id',
			'appointments',
			array(
				),
			'GROUP BY customer_id ORDER BY COUNT(id) DESC LIMIT ' . $showPerPage
			);
		break;

	case 'recent':
		/* min time between now and created or starts_at appointment */
		$now = time();
		$res = $ntsdb->get_select(
			'DISTINCT(customer_id)',
			'appointments',
			array(
				),
			"ORDER BY LEAST( ABS(created_at - $now), ABS(starts_at - $now)) ASC LIMIT $showPerPage"
			);

		reset( $res );
		foreach( $res as $r )
		{
			$filterIds[] = $r['customer_id'];
		}
		break;
}

if( $filterIds )
{
	$mainWhere['id'] = array( 'IN', $filterIds );
}

if( $NTS_VIEW['search'] ){
	$where = array();

	$searchIn = array();
	$om =& objectMapper::getInstance();
	$fields = $om->getFields( 'customer', 'external' );
	reset( $fields );
	foreach( $fields as $f ){
		$searchIn[] = $f[0];
		}

	$ri = ntsLib::remoteIntegration();
	if( $ri == 'wordpress' ){
		$searchIn[] = 'user_nicename';
		$searchIn[] = 'display_name';
	}

	reset( $searchIn );
	foreach( $searchIn as $sin ){
		$thisWhere = $mainWhere;
		$thisWhere[ $sin ] = array( 'LIKE', '%' . $NTS_VIEW['search'] . '%' );
		$where[] = $thisWhere;
		}
	}
else {
	$where = $mainWhere;
	}

if( in_array($display, $showAllDisplays) ){
	$limit = '';
	$NTS_VIEW['showPerPage'] = 'all';
	$NTS_VIEW['currentPage'] = 1;
	}
else {
	$NTS_VIEW['showPerPage'] = $showPerPage;
	$NTS_VIEW['currentPage'] = $_NTS['REQ']->getParam('p');
	if( ! $NTS_VIEW['currentPage'] )
		$NTS_VIEW['currentPage'] = 1;
	$limit = ( ($NTS_VIEW['currentPage'] - 1) * $NTS_VIEW['showPerPage'] ) . ',' . $NTS_VIEW['showPerPage'];
	}

/* load users */
$users = $integrator->getUsers(
	$where,
	$order,
	$limit
	);

reset( $users );
$userIds = array();
foreach( $users as $u )
{
	$userIds[] = $u['id'];
}

/* if filter then sort by ids */
if( $filterIds )
{
	$userIds = ntsLib::sortArrayByArray( $userIds, $filterIds );
}

reset( $userIds );
$entries = array();
foreach( $userIds as $uid )
{
	$user = new ntsUser();
	$user->setId( $uid );
	$entries[] = $user;
}

$NTS_VIEW['totalCount'] = $integrator->countUsers( $where );

$display = $_NTS['REQ']->getParam( 'display' );
if( in_array($display, $showAllDisplays) ){
	$NTS_VIEW['showFrom'] = 1;
	$NTS_VIEW['showTo'] = $NTS_VIEW['totalCount'];
	}
else {
	if($NTS_VIEW['totalCount'] > 0){
		$NTS_VIEW['showFrom'] = 1 + ($NTS_VIEW['currentPage'] - 1) * $NTS_VIEW['showPerPage'];
		$NTS_VIEW['showTo'] = $NTS_VIEW['showFrom'] + $NTS_VIEW['showPerPage'] - 1;
		if( $NTS_VIEW['showTo'] > $NTS_VIEW['totalCount'] )
			$NTS_VIEW['showTo'] = $NTS_VIEW['totalCount'];
		}
	else {
		$NTS_VIEW['showFrom'] = 0;
		$NTS_VIEW['showTo'] = 0;
		}
	}

$this->data['entries'] = $entries;

/* count apps */
$upcomingCount = array();
$oldCount = array();
$grandTotal = 0;
$totalAmount = 0;
$paidAmount = 0;

if( $userIds ){
	$NTS_VIEW['t']->setNow();
	$NTS_VIEW['t']->setStartDay();
	$fromNow = $NTS_VIEW['t']->getTimestamp();

	/* addonWhere */
	$locs = ntsLib::getVar( 'admin::locs' );
	$ress = ntsLib::getVar( 'admin::ress' );
	$sers = ntsLib::getVar( 'admin::sers' );
	$addonWhere = array(
		'location_id'	=> array( 'IN', $locs ),
		'resource_id'	=> array( 'IN', $ress ),
		'service_id'	=> array( 'IN', $sers ),
		);

	$where = array(
		'starts_at'		=> array( '>=', $fromNow ),
		'customer_id'	=> array( 'IN', $userIds ),
		'completed'		=> array( '>=', 0 ),
		);
	reset( $addonWhere );
	foreach( $addonWhere as $k => $v ){
		if( (! isset($where[$k])) && (! isset($where['id'])) )
			$where[$k] = $v;
		}
	$upcomingCount = $tm2->countAppointments( $where, 'customer_id' );

	$where = array(
		'starts_at'		=> array( '<', $fromNow ),
		'customer_id'	=> array( 'IN', $userIds ),
		'completed'		=> array( '>=', 0 ),
		);
	reset( $addonWhere );
	foreach( $addonWhere as $k => $v ){
		if( (! isset($where[$k])) && (! isset($where['id'])) )
			$where[$k] = $v;
		}
	$oldCount = $tm2->countAppointments( $where, 'customer_id' );
	}

ntsLib::setVar( 'admin/customers::upcomingCount', $upcomingCount );
ntsLib::setVar( 'admin/customers::oldCount', $oldCount );

/* count users that are disabled, pending approval etc */
//$ntsdb->_debug = TRUE;
$restricted_count = array();
$restricts = array( 'email_not_confirmed', 'not_approved', 'suspended'  );
foreach( $restricts as $r )
{
	$this_where = array(
//		'_role'			=> array( '=', 'customer' ),
		'_restriction'	=> array( '=', $r ),
		);
	$this_count = $integrator->countUsers( $this_where );
	if( $this_count )
		$restricted_count[$r] = $this_count;
}
//$ntsdb->_debug = FALSE;
$this->data['restricted_count'] = $restricted_count;

switch( $action ){
	case 'export':
		$fileName = 'customers-' . $t->formatDate_Db() . '.csv';
		ntsLib::startPushDownloadContent( $fileName );
		require( dirname(__FILE__) . '/excel.php' );
		exit;
		break;
	default:
		break;
	}

$this->render( 
	dirname(__FILE__) . '/index.php',
	$this->data
	);
?>
<?php
// which i can view
$locs2 = array();
$ress2 = array();
$sers2 = array();

$appView = ntsLib::getVar( 'admin/manage:appView' );
$schView = ntsLib::getVar( 'admin/manage:schView' );

if( $schView || $appView )
{
	$tm2->setLocation(0);
	$lrss = $tm2->getLrs();
	reset( $lrss );
	foreach( $lrss as $lrs )
	{
		if( ! in_array($lrs[0], $locs2) )
		{
			$locs2[] = $lrs[0];
		}
	}
	$tm2->setLocation($locs);

	$tm2->setResource(0);
	$lrss = $tm2->getLrs();

	reset( $lrss );
	foreach( $lrss as $lrs )
	{
		if( 
			(! in_array($lrs[1], $ress2)) &&
			( in_array($lrs[1], $schView) OR in_array($lrs[1], $appView) )
		)
		{
			$ress2[] = $lrs[1];
		}
	}
	$tm2->setResource($ress);

	$tm2->setService(0);
	$lrss = $tm2->getLrs();
	reset( $lrss );
	foreach( $lrss as $lrs )
	{
		if( ! in_array($lrs[2], $sers2) )
			$sers2[] = $lrs[2];
	}
	$tm2->setService($sers);
}
else
{
	$locs = $locs2 = array(-1);
	$ress = $ress2 = array(-1);
	$sers = $sers2 = array(-1);

	$tm2->setResource($ress);
	$tm2->setLocation($locs);
	$tm2->setService($sers);
}

$sortRess = ntsObjectFactory::getAllIds( 'resource' );
$ress = ntsLib::sortArrayByArray( $ress, $sortRess );
$ress2 = ntsLib::sortArrayByArray( $ress2, $sortRess );

ntsLib::setVar( 'admin::tm2', $tm2 );

/* currently filtered */
ntsLib::setVar( 'admin::ress', $ress );
ntsLib::setVar( 'admin::locs', $locs );
ntsLib::setVar( 'admin::sers', $sers );

/* all possible locations and resources */
ntsLib::setVar( 'admin::ress2', $ress2 );
ntsLib::setVar( 'admin::locs2', $locs2 );
ntsLib::setVar( 'admin::sers2', $sers2 );
?>
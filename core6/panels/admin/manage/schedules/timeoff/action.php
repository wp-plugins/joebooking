<?php
$tm2 = ntsLib::getVar('admin::tm2');

$ress = ntsLib::getVar( 'admin::ress' );
$tm2->setResource( $ress );
$timeoffs = $tm2->getTimeoff();

/* split by current, future, past */
uasort( $timeoffs, create_function(
	'$a, $b',
	'
	if( $a["starts_at"] != $b["starts_at"] ){
		$return = ($b["starts_at"] - $a["starts_at"]);
		}
	else {
		$return = ($b["starts_at"] - $a["starts_at"]);
		}
	return $return;
	'
	)
);

$now = time();
$entries = array(
	'now'		=> array(),
	'future'	=> array(),
	'past'		=> array(),
	);
$total_count = count($timeoffs);
reset( $timeoffs );
foreach( $timeoffs as $to )
{
	$obj = ntsObjectFactory::get( 'timeoff' );
	$obj->setByArray( $to );

	if(
		($to['starts_at'] <= $now) &&
		($to['ends_at'] >= $now)
		)
	{
		$entries['now'][] = $obj;
	}
	elseif(
		$to['ends_at'] < $now
		)
	{
		$entries['past'][] = $obj;
	}
	else
	{
		$entries['future'][] = $obj;
	}
}

/* sort future asc */
$sortFunc = create_function('$a, $b', 'return ($a->getProp("starts_at") - $b->getProp("starts_at"));');
usort( $entries['future'], $sortFunc );

ntsLib::setVar( 'admin/manage/timeoff:entries', $entries );
ntsLib::setVar( 'admin/manage/timeoff:total_count', $total_count );
?>
<?php
require( dirname(__FILE__) . '/_a_init.php' );

/* get appointments */
$t->setDateDb( $start_date );
$periodStart = $t->getStartDay();
$t->setDateDb( $end_date );
$periodEnd = $t->getEndDay();

$tm2 = ntsLib::getVar( 'admin::tm2' );

$where = array(
	'(starts_at + duration + lead_out)'	=> array('>', $periodStart),
	'starts_at'							=> array('<', $periodEnd)
	);

$where['completed'] = array('>=', 0);
if( $locs ){
	$where['location_id'] = array( 'IN', $locs );
}
if( $ress ){
	$where['resource_id'] = array( 'IN', $ress );
}

/* get apps */
$ntsdb =& dbWrapper::getInstance();
$all_apps = $tm2->getAppointments( $where, 'ORDER BY starts_at ASC, id DESC' );

if( ! $all_apps ){
	echo M('None');
	exit;
}

$unset = array();


$ff =& ntsFormFactory::getInstance();
$form_file = dirname(__FILE__) . '/views/_export_form';
$form =& $ff->makeForm( $form_file );

if( $form->validate() ){
	$formValues = $form->getValues();
	reset( $formValues );
	foreach( $formValues as $k => $v ){
		$k = substr( $k, strlen('field_') );
		if( ! $v ){
			$unset[] = $k;
		}
	}
}

/* save unset fields in preferences */
$ci = ntsLib::getCurrentUser();
$ci->setPreference( 'unset_download_fields', $unset );

$labels = ntsAppointment::dump_labels();

$unset = array_merge(
	array( 'lrst', 'is_class', 'customer:first_name', 'customer:last_name', 'duration_short', 'clean_up_short' ),
	$unset
	);

reset( $all_apps );
$out = array();
$header = array();

foreach( $all_apps as $a ){
	$app = ntsObjectFactory::get('appointment');
	$app->setId( $a['id'] );
//	$app->setByArray( $a );
	$v = $app->dump();

	reset( $unset );
	foreach( $unset as $u ){
		unset( $v[$u] );
	}

	if( ! $header ){
		$header = array_keys( $v );
		for( $ii = 0; $ii < count($header); $ii++ ){
			if( isset($labels[$header[$ii]]) )
				$header[$ii] = $labels[$header[$ii]];
		}
		$out[] = $header;
	}
	$out[] = $v;

	/* with second part */
	if( $app->getProp('duration2') ){
		$v2 = $app->dump(FALSE, array(), 2);

		reset( $unset );
		foreach( $unset as $u ){
			unset( $v2[$u] );
		}
		$out[] = $v2;
	}
}

$fileName = 'appointments-' . $t->formatDate_Db() . '.csv';
ntsLib::startPushDownloadContent( $fileName );

foreach( $out as $o ){
	echo ntsLib::buildCsv( $o );
	echo "\n";
}
exit;
?>
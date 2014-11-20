<?php
$ntsdb =& dbWrapper::getInstance();
$cm =& ntsCommandManager::getInstance();

$sql = "ALTER TABLE {PRFX}services DROP COLUMN `until_closed`";
$result = $ntsdb->runQuery( $sql );
$sql = "ALTER TABLE {PRFX}services DROP COLUMN `pack_only`";
$result = $ntsdb->runQuery( $sql );

$sql = "ALTER TABLE {PRFX}appointments DROP COLUMN `until_closed`";
$result = $ntsdb->runQuery( $sql );

$columns = $ntsdb->getColumnsInTable('services');
if( ! isset($columns['blocks_location']) )
{
	$sql = "ALTER TABLE {PRFX}services ADD COLUMN `blocks_location` TINYINT DEFAULT 0";
	$result = $ntsdb->runQuery( $sql );
}

$sql = "UPDATE {PRFX}objectmeta SET meta_data = 0 WHERE meta_name='_resource_schedules' AND meta_data='none'";
$result = $ntsdb->runQuery( $sql );
$sql = "UPDATE {PRFX}objectmeta SET meta_data = 1 WHERE meta_name='_resource_schedules' AND meta_data='view'";
$result = $ntsdb->runQuery( $sql );
$sql = "UPDATE {PRFX}objectmeta SET meta_data = 3 WHERE meta_name='_resource_schedules' AND meta_data='edit'";
$result = $ntsdb->runQuery( $sql );

$sql = "UPDATE {PRFX}objectmeta SET meta_data = 0 WHERE meta_name='_resource_apps' AND meta_data='none'";
$result = $ntsdb->runQuery( $sql );
$sql = "UPDATE {PRFX}objectmeta SET meta_data = 1 WHERE meta_name='_resource_apps' AND meta_data='view'";
$result = $ntsdb->runQuery( $sql );
$sql = "UPDATE {PRFX}objectmeta SET meta_data = 3 WHERE meta_name='_resource_apps' AND meta_data='edit'";
$result = $ntsdb->runQuery( $sql );
$sql = "UPDATE {PRFX}objectmeta SET meta_data = 7 WHERE meta_name='_resource_apps' AND meta_data='manage'";
$result = $ntsdb->runQuery( $sql );

$sql = "UPDATE {PRFX}form_controls SET description = \"Please make sure that you include the country code. For example, the US number (248) 123-7654 becomes +12481237654, the UK number 07777123456 becomes +447777123456.\" WHERE type='mobilephone'";
$result = $ntsdb->runQuery( $sql );

/* convert schedules */
$sql = "SELECT id FROM {PRFX}resources";
$result = $ntsdb->runQuery( $sql );
$allResIds = array();
while( $r = $result->fetch() ){
	$allResIds[] = $r['id'];
	}
$sql = "SELECT id FROM {PRFX}locations";
$result = $ntsdb->runQuery( $sql );
$allLocIds = array();
while( $r = $result->fetch() ){
	$allLocIds[] = $r['id'];
	}
$sql = "SELECT id FROM {PRFX}services";
$result = $ntsdb->runQuery( $sql );
$allSerIds = array();
while( $r = $result->fetch() ){
	$allSerIds[] = $r['id'];
	}

$newBlocks = array();
reset( $allResIds );
$groupId = 0;
foreach( $allResIds as $resId ){
	$sql = "SELECT * FROM {PRFX}schedules WHERE resource_id = $resId ORDER BY valid_from";
	$oldSchedules = array();
	$result = $ntsdb->runQuery( $sql );

	$validFrom = 20300101;
	$validTo = 20000101;
	while( $r = $result->fetch() ){
		$r['_services'] = array();
		$r['_locations'] = array();

		$schId = $r['id'];

	// services
		$sql2 = "SELECT meta_value FROM {PRFX}objectmeta WHERE obj_class = 'schedule' AND obj_id = $schId AND meta_name = '_service'";
		$result2 = $ntsdb->runQuery( $sql2 );
		while( $r2 = $result2->fetch() ){
			$r['_services'][] = $r2['meta_value'];
			}
	// locations
		$sql3 = "SELECT meta_value FROM {PRFX}objectmeta WHERE obj_class = 'schedule' AND obj_id = $schId AND meta_name = '_location'";
		$result3 = $ntsdb->runQuery( $sql3 );
		while( $r3 = $result3->fetch() ){
			$r['_locations'][] = $r3['meta_value'];
			}

	// now get timeblocks
		$sql4 = "SELECT * FROM {PRFX}timeblocks WHERE schedule_id = $schId";
		$result4 = $ntsdb->runQuery( $sql4 );
		$tblocks = array();

		for( $j = 0; $j <= 6; $j++ ){
			$tblocks[$j] = array();
			}

		while( $r4 = $result4->fetch() ){
			$r4['_services'] = $r['_services'];
			$r4['_locations'] = $r['_locations'];

			unset( $r4['schedule_id'] );
			unset( $r4['id'] );
			$appliedOn = $r4['applied_on'];
			unset( $r4['applied_on'] );
			if( $r4['selectable_fixed'] ){
				$fixed = unserialize($r4['selectable_fixed']);
				reset( $fixed );
				foreach( $fixed as $fix ){
					$newR = $r4;
					$newR['starts_at'] = $fix;
					$newR['ends_at'] = 0;
					$newR['selectable_every'] = 0;
					unset($newR['selectable_fixed']);
					$tblocks[ $appliedOn ][] = $newR;
					}
				}
			else {
				unset($r4['selectable_fixed']);
				$tblocks[ $appliedOn ][] = $r4;
				}
			}
		$r['tblocks'] = $tblocks;

		$oldSchedules[] = $r;
		}
//_print_r( $oldSchedules );
//echo count( $oldSchedules ) . '<br>';

	$thisBlocks = array();
	reset( $oldSchedules );
	foreach( $oldSchedules as $sch ){
		$groupId++;
		reset( $sch['tblocks'] );
		foreach( $sch['tblocks'] as $appliedOn => $tblocks ){
			reset( $tblocks );
			foreach( $tblocks as $tb ){
				$iterateLocations = ( array_diff($allLocIds, $tb['_locations']) ) ? $tb['_locations'] : array(0);
				$iterateServices = ( array_diff($allSerIds, $tb['_services']) ) ? $tb['_services'] : array(0);

				reset( $iterateLocations );
				foreach( $iterateLocations as $lid ){
					reset( $iterateServices );
					foreach( $iterateServices as $sid ){
						$newBlocks[] = array(
							'location_id'		=> $lid,
							'service_id'		=> $sid,
							'valid_from'		=> $sch['valid_from'],
							'valid_to'			=> $sch['valid_to'],
							'capacity'			=> $sch['capacity'], 
							'applied_on'		=> $appliedOn,
							'resource_id'		=> $sch['resource_id'],
							'starts_at'			=> $tb['starts_at'],
							'ends_at'			=> $tb['ends_at'],
							'selectable_every'	=> $tb['selectable_every'],
							'group_id'			=> $groupId,
							);
						}
					}
				}
			}
		}
	}

$sql = "TRUNCATE {PRFX}timeblocks";
$result = $ntsdb->runQuery( $sql );

$sql = "ALTER TABLE {PRFX}timeblocks DROP COLUMN `selectable_fixed`";
$result = $ntsdb->runQuery( $sql );
$sql = "ALTER TABLE {PRFX}timeblocks DROP COLUMN `schedule_id`";
$result = $ntsdb->runQuery( $sql );

$sql = "ALTER TABLE {PRFX}timeblocks ADD COLUMN `location_id` int(11) NOT NULL";
$result = $ntsdb->runQuery( $sql );
$sql = "ALTER TABLE {PRFX}timeblocks ADD COLUMN `resource_id` int(11) NOT NULL";
$result = $ntsdb->runQuery( $sql );
$sql = "ALTER TABLE {PRFX}timeblocks ADD COLUMN `service_id` int(11) NOT NULL";
$result = $ntsdb->runQuery( $sql );
$sql = "ALTER TABLE {PRFX}timeblocks ADD COLUMN `valid_from` int(11) NOT NULL";
$result = $ntsdb->runQuery( $sql );
$sql = "ALTER TABLE {PRFX}timeblocks ADD COLUMN `valid_to` int(11) NOT NULL";
$result = $ntsdb->runQuery( $sql );
$sql = "ALTER TABLE {PRFX}timeblocks ADD COLUMN `capacity` int(11) DEFAULT 1";
$result = $ntsdb->runQuery( $sql );
$sql = "ALTER TABLE {PRFX}timeblocks ADD COLUMN `group_id` int(11) NOT NULL";
$result = $ntsdb->runQuery( $sql );

$maxEnd = 0;
$minStart = 24 * 60 * 60;
reset( $newBlocks );
foreach( $newBlocks as $nb ){
	if( $nb['starts_at'] < $minStart )
		$minStart = $nb['starts_at'];
	if( $nb['ends_at'] > $maxEnd )
		$maxEnd = $nb['ends_at'];
	$sqlAdd = 'INSERT INTO {PRFX}timeblocks (' . join(',', array_keys($nb)) . ') VALUES (' . join(',', array_values($nb)) . ')';
	$resultAdd = $ntsdb->runQuery( $sqlAdd );
	}

$conf =& ntsConf::getInstance();
$conf->set( 'timeStarts', $minStart );
$conf->set( 'timeEnds', $maxEnd );

/* delete schedules and blocks */
$sql = "DROP TABLE {PRFX}schedules";
$result = $ntsdb->runQuery( $sql );
$sql = "DELETE FROM {PRFX}objectmeta WHERE obj_class = 'schedule'";
$result = $ntsdb->runQuery( $sql );

$sql = "DROP TABLE {PRFX}packs";
$result = $ntsdb->runQuery( $sql );
$sql = "DELETE FROM {PRFX}objectmeta WHERE obj_class = 'pack'";
$result = $ntsdb->runQuery( $sql );
?>
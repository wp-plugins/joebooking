<?php
$actionResult = 1;

$ntsdb =& dbWrapper::getInstance();
$om =& objectMapper::getInstance();
$className = $object->getClassName();
$objId = $object->getId();

if( ! ( isset($objectInfo) && isset($metaInfo) ) ){
	list( $objectInfo, $metaInfo ) = $object->getByArray( true, true );
	}
	
$metaClass = $object->getMetaClass();
$id = $object->getId();
if( $id <= 0 ){
	return;
	}

/* do queries */
/* main table */
if( $objectInfo && (! ( isset($skipMainTable) && $skipMainTable )) ){
	$actionDescription = 'Store object data in database';
	$tblName = $om->getTableForClass( $className );

	$result = $ntsdb->update( 
		$tblName,
		$objectInfo,
		array(
			'id' => array('=', $id)
			)
		);
	
	if( $result ){
		$actionResult = 1;
		}
	else {
		$actionResult = 0;
		$actionError = $ntsdb->getError();
		}
	}

/* meta properties */
if( $metaClass && $metaInfo ){
	/* get current meta properties */
	$currentMetas = array();
	$sql =<<<EOT
SELECT 
	meta_name, meta_value, meta_data, id
FROM 
	{PRFX}objectmeta 
WHERE
	obj_id = $id AND 
	obj_class = "$className"
EOT;

	$result = $ntsdb->runQuery( $sql );
	while( $u = $result->fetch() ){
		if( isset($metaInfo[$u['meta_name']]) )
			$currentMetas[] = $u;
		}

	/* get new meta properties */
	$newMetas = $om->prepareMeta( $id, $metaClass, $metaInfo, false );

	$toAdd = array();
	$toDelete = array();
	$toUpdate = array();

	/* skip ones that already exist */
	$newMetaCount = count( $newMetas );
	for( $i = ($newMetaCount - 1); $i >= 0; $i-- ){
		$currentMetaCount = count( $currentMetas );
		for( $j = ($currentMetaCount - 1); $j >= 0; $j-- ){
			if( 
				( $newMetas[$i]['meta_name']	== $currentMetas[$j]['meta_name'] ) && 
				( $newMetas[$i]['meta_value']	== $currentMetas[$j]['meta_value'] ) && 
				( $newMetas[$i]['meta_data']	== $currentMetas[$j]['meta_data'] ) &&
				( strlen($newMetas[$i]['meta_value'])	== strlen($currentMetas[$j]['meta_value']) )
				){
				array_splice( $newMetas, $i, 1 );
				array_splice( $currentMetas, $j, 1 );
//echo "<h1>SKIP $i to $j</h1>";
				break;
				}
			}
		}

	/* ok, which ones we can update */
	$newMetaCount = count( $newMetas );
	for( $i = ($newMetaCount - 1); $i >= 0; $i-- ){
		$currentMetaCount = count( $currentMetas );
		for( $j = ($currentMetaCount - 1); $j >= 0; $j-- ){
			if( 
				( $newMetas[$i]['meta_name']	== $currentMetas[$j]['meta_name'] ) && 
				( $newMetas[$i]['meta_value']	== $currentMetas[$j]['meta_value'] ) && 
				( strlen($newMetas[$i]['meta_value'])	== strlen($currentMetas[$j]['meta_value']) )
				){
				$updateArray = array(
					'meta_data' => $newMetas[$i]['meta_data'],
					);
				$toUpdate[] = array( $currentMetas[$j]['id'], $updateArray );
				array_splice( $newMetas, $i, 1 );
				array_splice( $currentMetas, $j, 1 );
				break;
				}
			}
		}

	$newMetaCount = count( $newMetas );
	for( $i = ($newMetaCount - 1); $i >= 0; $i-- ){
		$currentMetaCount = count( $currentMetas );
		for( $j = ($currentMetaCount - 1); $j >= 0; $j-- ){
			if( 
				( $newMetas[$i]['meta_name']	== $currentMetas[$j]['meta_name'] )
				){
				$updateArray = array(
					'meta_value' => $newMetas[$i]['meta_value'],
					'meta_data' => $newMetas[$i]['meta_data'],
					);
				$toUpdate[] = array( $currentMetas[$j]['id'], $updateArray );
				array_splice( $newMetas, $i, 1 );
				array_splice( $currentMetas, $j, 1 );
				break;
				}
			}
		}

	/* to update */
	reset( $toUpdate );
	foreach( $toUpdate as $ua ){
		$metaId = $ua[0];
		unset( $ua[1]['meta_name'] );
		$result = $ntsdb->update( 
			'objectmeta',
			$ua[1],
			array(
				'id' => array('=', $metaId)
				)
			);
		}

	/* to add */
	reset( $newMetas );
	foreach( $newMetas as $ma ){
		$ma['obj_id'] = $id;
		$ma['obj_class'] = $metaClass;
		$result = $ntsdb->insert( 'objectmeta', $ma );
		}

	/* to delete */
	reset( $currentMetas );
	foreach( $currentMetas as $ma ){
		$id2delete = $ma['id'];

		$result = $ntsdb->delete(
			'objectmeta',
			array(
				'id' => array('=', $id2delete)
				)
			);
		}
	}

/* track changes */
require( dirname(__FILE__) . '/_track.php' );
if( isset($track[$className]) )
{
	$changes = $object->getChanges();
	$log_changes = array();
	foreach( $changes as $property_name => $old_value )
	{
		if( ! in_array($property_name, $track[$className]) )
		{
			continue;
		}
		$log_changes[ $property_name ] = $old_value;
	}

	if( $log_changes )
	{
		$time = time();
		$currentUserId = ntsLib::getCurrentUserId();
		$currentUserId = $this->act_as ? $this->act_as : ntsLib::getCurrentUserId();

		$description = $object->_change_reason ? $object->_change_reason : '';
		$object->_change_reason = '';

		foreach( $log_changes as $property_name => $old_value )
		{
			$log = array(
				'user_id'		=> $currentUserId,
				'action_time'	=> $time,
				'obj_class'		=> $className,
				'obj_id'		=> $objId,
				'property_name'	=> $property_name,
				'old_value'		=> $old_value,
				'description'	=> $description,
				);
			$result = $ntsdb->insert(
				'logaudit',
				$log
				);
		}
	}
}

ntsObjectFactory::clearCache( $className, $id );
?>
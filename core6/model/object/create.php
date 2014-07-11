<?php
$actionResult = 1;

$ntsdb =& dbWrapper::getInstance();
$om =& objectMapper::getInstance();
$className = $object->getClassName();

if( ! ( isset($objectInfo) && isset($metaInfo) ) ){
	list( $objectInfo, $metaInfo ) = $object->getByArray( true );
	}
$metaClass = $object->getMetaClass();

/* do queries */
/* main table */
$tblName = $om->getTableForClass( $className );
if( $objectInfo && (! ( isset($skipMainTable) && $skipMainTable )) ){
	$actionDescription = 'Store object data in database';

	/* check if show order needed */
	if( $om->isPropRegistered($className, 'show_order') ){
		$setShowOrder = $object->getProp( 'show_order' );
		if( ! $setShowOrder ){

			$sql =<<<EOT
			SELECT 
				MAX(show_order) AS max_show_order
			FROM
				{PRFX}$tblName
EOT;
			$result = $ntsdb->runQuery( $sql );
			$max = $result->fetch();

			$showOrder = $max['max_show_order'] + 1;
			$object->setProp( 'show_order', $showOrder );
			$objectInfo[ 'show_order' ] = $showOrder;
			}
		}

	/* already id? */
	$objectId = $object->getId();
	if( $objectId && (! isset($objectInfo['id'])) ){
		$objectInfo['id'] = $objectId;
		}
	$result = $ntsdb->insert( $tblName, $objectInfo );

	if( $result ){
		$actionResult = 1;
		$newId = $ntsdb->getInsertId();
		$object->setId( $newId, false );
		}
	else {
		$actionResult = 0;
		$actionError = $ntsdb->getError();
		}
	}

if( ! $actionResult ){
	echo 'ERROR: ' . $actionError . '<br>';
	return;
	}

/* meta properties */
if( $metaClass && $metaInfo ){
	$metas = $om->prepareMeta( $newId, $metaClass, $metaInfo );
	reset( $metas );
	foreach( $metas as $ma ){
		$result = $ntsdb->insert( 'objectmeta', $ma );
		}
	}
$object->resetUpdatedProps();

/* add id to logaudit */
require( dirname(__FILE__) . '/_track.php' );
if( isset($track[$className]) )
{
	$time = time();
	$currentUserId = $this->act_as ? $this->act_as : ntsLib::getCurrentUserId();
	$log = array(
		'user_id'		=> $currentUserId,
		'action_time'	=> $time,
		'obj_class'		=> $className,
		'obj_id'		=> $newId,
		'property_name'	=> 'id',
		'old_value'		=> 0
		);
	$result = $ntsdb->insert(
		'logaudit',
		$log
		);
}
?>
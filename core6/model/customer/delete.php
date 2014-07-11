<?php
$ntsdb =& dbWrapper::getInstance();
$userId = $object->getId();

$t = new ntsTime();
$today = $t->formatDate_Db();
$todayTimestamp = $t->timestampFromDbDate( $today );

/* delete appointments and orders */
$where = array(
	'customer_id'	=> array( '=', $userId ),
	);
$result = $ntsdb->select( 'id', 'appointments', $where );
if( $result ){
	while( $e = $result->fetch() ){
		$subId = $e['id'];
		$subObject = ntsObjectFactory::get( 'appointment' );
		$subObject->setId( $subId );
		$this->runCommand( $subObject, 'delete' );
		}
	}

$result = $ntsdb->select( 'id', 'orders', $where );
if( $result ){
	while( $e = $result->fetch() ){
		$subId = $e['id'];
		$subObject = ntsObjectFactory::get( 'order' );
		$subObject->setId( $subId );
		$this->runCommand( $subObject, 'delete' );
		}
	}
?>
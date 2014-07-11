<?php
$mergeToId = isset($params['to']) ? $params['to'] : 0;
if( ! $mergeToId )
	return;
$myId = $object->getId();

$ntsdb =& dbWrapper::getInstance();
$ntsdb->_debug = TRUE;

/* move appointments */
$ntsdb->update(
	'appointments',
	array(
		'customer_id' => $mergeToId
		),
	array(
		'customer_id' => array('=', $myId)
		)
	);

/* move orders */
$ntsdb->update(
	'orders',
	array(
		'customer_id' => $mergeToId
		),
	array(
		'customer_id' => array('=', $myId)
		)
	);

/* accounting */
$ntsdb->update(
	'accounting_posting',
	array(
		'account_id' => $mergeToId
		),
	array(
		'account_type'	=> array('LIKE', 'customer%'),
		'account_id'	=> array('=', $myId)
		)
	);

/* then just delete this customer */
$this->runCommand( $object, 'delete' );
?>
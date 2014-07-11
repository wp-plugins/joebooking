<?php
$object = ntsLib::getVar( 'admin/customers/edit::OBJECT' );

$merge_to = NULL;
$other_id = $_NTS['REQ']->getParam('customer_id');
if( $other_id )
{
	$merge_to = new ntsUser();
	$merge_to->setId( $other_id );
}

$view = array(
	'object'	=> $object,
	'merge_to'	=> $merge_to,
	);

$this->render(
	dirname(__FILE__) . '/index.php',
	$view
	);
?>
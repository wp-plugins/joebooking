<?php
$customer_id = $object->getId();

/* save it in session */
$session = new ntsSession;
$apps = $session->userdata('apps');

for( $ii = 0; $ii < count($apps); $ii++ )
{
	$apps[$ii]['customer_id'] = $customer_id;
}
$session->set_userdata( 'apps', $apps );

require( dirname(__FILE__) . '/../confirm/a-finalize.php' );
?>
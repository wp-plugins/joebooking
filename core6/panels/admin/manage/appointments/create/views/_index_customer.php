<?php
$obj = new ntsUser;;
$obj->setId( $cid );
$obj_class = $obj->getClassName();

$customer_available = array(
	$cid	=> $available['customer']
	);
?>
<?php
echo $this->render_file(
	dirname(__FILE__) . '/_object.php',
	array(
		'obj_class'	=> 'customer',
		'obj'		=> $obj,
		'available'	=> $customer_available,
		'this_id'	=> $cid,
		'all_ids'	=> $locs,
		'a'			=> $a,
		)
	);
?>

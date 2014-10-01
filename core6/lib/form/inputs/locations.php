<?php
$objects = ntsObjectFactory::getAll( 'location' );

$conf['includeAll'] = true;
$conf['options'] = array();
reset( $objects );
foreach( $objects as $obj  )
{
	$conf['options'][] = array( $obj->getId(), ntsView::objectTitle($obj) );
}
require( NTS_LIB_DIR . '/lib/form/inputs/checkboxSet.php' );
?>
<?php
/* test file */
include_once( dirname(__FILE__) . '/hclib/hc_lib.php' );
$distdir = 'd:\www\_dist';

$new = $_GET['new'];
$old = $_GET['old'];

$new_dir = $distdir . '/' . $new;
$old_dir = $distdir . '/' . $old;

$new_listing = HC_Lib::list_recursive( $new_dir );
$old_listing = HC_Lib::list_recursive( $old_dir );

$added = array_diff( $new_listing, $old_listing );
$deleted = array_diff( $old_listing, $new_listing );
$same = array_intersect( $new_listing, $old_listing );
$changed = array();
foreach( $same as $f ){
	$old_hash = md5(file_get_contents($old_dir . '/' . $f));
	$new_hash = md5(file_get_contents($new_dir . '/' . $f));
	if( $new_hash != $old_hash ){
		$changed[] = $f;
	}
}

if( $added ){
	echo "ADDED:<br>";
	echo "====<br>";
	foreach( $added as $f ){
		echo $f . '<br>';
	}
	echo '<br>';
}

if( $deleted ){
	echo "DELETED:<br>";
	echo "====<br>";
	foreach( $deleted as $f ){
		echo $f . '<br>';
	}
	echo '<br>';
}

if( $changed ){
	echo "CHANGED:<br>";
	echo "====<br>";
	foreach( $changed as $f ){
		echo $f . '<br>';
	}
	echo '<br>';
}


?>
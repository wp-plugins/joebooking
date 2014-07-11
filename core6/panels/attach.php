<?php
$hash = isset( $_GET['nts-file'] ) ? $_GET['nts-file'] : '';
if( ! $hash )
{
	exit;
}

$am = new ntsAttachManager;
$file = $am->get_by_hash( $hash );

if( ! $file )
{
	exit;
}

$contentType = $file['file_type'];
if( ! $contentType )
{
	exit;
}

$original_name = $am->original_name( $file['file'] ); 
$full_file = $file['full_file'];

header("Content-type: $contentType");

if( ! $file['is_image'] )
{
	header("Content-Disposition: attachment; filename=\"$original_name\"");
	header("Pragma: public");
	header("Expires: 0");
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
	header("Connection: close");
	header("Content-Transfer-Encoding: binary");
}

/*
header("Type: application/force-download");
header("Content-Type: application/force-download");
header("Content-Length: $totalSize");
*/


if( file_exists($full_file) )
{
	readfile( $full_file );
}
exit;
?>
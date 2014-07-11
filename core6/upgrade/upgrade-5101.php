<?php
$ntsdb =& dbWrapper::getInstance();

$sql =<<<EOT

UPDATE
	{PRFX}objectmeta
SET
	{PRFX}objectmeta.obj_class = 'invoice',
	{PRFX}objectmeta.meta_name = '_appointment',

	{PRFX}objectmeta.obj_id = (@temp:={PRFX}objectmeta.obj_id),
	{PRFX}objectmeta.obj_id = {PRFX}objectmeta.meta_value,
	{PRFX}objectmeta.meta_value = @temp
WHERE
	{PRFX}objectmeta.meta_name = '_invoice' AND
	{PRFX}objectmeta.obj_class = 'appointment'

EOT;
$result = $ntsdb->runQuery( $sql );

$sql =<<<EOT

UPDATE
	{PRFX}objectmeta
SET
	{PRFX}objectmeta.obj_class = 'invoice',
	{PRFX}objectmeta.obj_id = {PRFX}objectmeta.meta_value,
	{PRFX}objectmeta.meta_name = '_order',
	{PRFX}objectmeta.meta_value = {PRFX}objectmeta.obj_id
WHERE
	{PRFX}objectmeta.meta_name = '_invoice' AND
	{PRFX}objectmeta.obj_class = 'order'

EOT;
$result = $ntsdb->runQuery( $sql );
?>
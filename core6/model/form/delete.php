<?php
$ntsdb =& dbWrapper::getInstance();
$formId = $object->getId();

/* delete controls */
$sql =<<<EOT
SELECT
	id
FROM
	{PRFX}form_controls
WHERE
	form_id = $formId
EOT;
$result = $ntsdb->runQuery( $sql );
if( $result ){
	while( $e = $result->fetch() ){
		$subId = $e['id'];
		$subObject = ntsObjectFactory::get( 'form_control' );
		$subObject->setId( $subId );
		$this->runCommand( $subObject, 'delete' );
		}
	}

?>
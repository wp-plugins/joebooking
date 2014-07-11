<?php
$ntsdb =& dbWrapper::getInstance();
$om =& objectMapper::getInstance();

$className = $object->getClassName();
$tblName = $om->getTableForClass( $className );
$myId = $object->getId();

$additionalConditions = isset($params['additionalConditions']) ? $params['additionalConditions'] : '';
if( $additionalConditions ){
	$additionalConditions = " AND $additionalConditions";
	}

/* my order */
$sql =<<<EOT
SELECT 
	show_order 
FROM 
	{PRFX}$tblName 
WHERE
	id = $myId
EOT;

$result = $ntsdb->runQuery( $sql );
$myInfo = $result->fetch();

$myOrder = $myInfo['show_order'];

/* check which one is lower then flip */
$sql =<<<EOT
SELECT 
	id, show_order 
FROM 
	{PRFX}$tblName 
WHERE
	show_order >= $myOrder
	$additionalConditions
	AND id <> $myId
ORDER BY
	show_order ASC
LIMIT 1
EOT;

$result = $ntsdb->runQuery( $sql );
$otherOne = $result->fetch();
if( $otherOne ){
	$newOrder = $otherOne['show_order'];
	$otherId = $otherOne['id'];

	if( $newOrder == $myOrder ){
		$newOrder = $myOrder + 1;
		}

	$sql =<<<EOT
UPDATE 
	{PRFX}$tblName  
SET 
	show_order = $myOrder
WHERE id = $otherId
EOT;
	$result = $ntsdb->runQuery( $sql );

	$sql =<<<EOT
UPDATE 
	{PRFX}$tblName  
SET show_order = $newOrder
WHERE id = $myId
EOT;
	$result = $ntsdb->runQuery( $sql );
	}

$actionResult = 1;
?>
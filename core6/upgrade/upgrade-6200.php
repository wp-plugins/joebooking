<?php
$ntsdb =& dbWrapper::getInstance();

if( ! $ntsdb->tableExists('languages') )
{
	/* translate items */
	$sql =<<<EOT
CREATE TABLE IF NOT EXISTS `{PRFX}languages` (
	`id` int(11) NOT NULL auto_increment,
	`lang` varchar(16) NOT NULL,
	`original` TEXT,
	`custom` TEXT,
	PRIMARY KEY  (`id`)
	);
EOT;
	$result = $ntsdb->runQuery( $sql );

	/* check if we have customized terminology */
	$where = array(
		'name'	=> array( 'LIKE', 'text-%' ),
		);
	$terms = $ntsdb->get_select(
		array('name', 'value'),
		'conf',
		$where
		);
	$custom = array();
	reset( $terms );
	foreach( $terms as $t )
	{
		$t['name'] = substr( $t['name'], strlen('text-') );
		$custom[ $t['name'] ] = $t['value'];
	}

	$lm =& ntsLanguageManager::getInstance();
	$lang = $lm->getDefaultLanguage();

	reset( $custom );
	foreach( $custom as $k => $v )
	{
		$lm->set_custom(
			$lang,
			$k,
			$v
			);
	}

	/* delete all custom terms */
	$where = array(
		'name'	=> array( 'LIKE', 'text-%' ),
		);
	$terms = $ntsdb->delete(
		'conf',
		$where
		);
}
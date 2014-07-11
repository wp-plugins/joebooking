<?php
set_time_limit( 120 );
ini_set( 'memory_limit', '126M' );

$conf =& ntsConf::getInstance();
$ntsdb =& dbWrapper::getInstance();

switch( $action ){
	case 'upload':
		$ff =& ntsFormFactory::getInstance();
		$formFile = dirname( __FILE__ ) . '/form';
		$form =& $ff->makeForm( $formFile );

		if( $form->validate() ){
			$formValues = $form->getValues();

			$saveConf = array(
				'installationId'	=> $conf->get('installationId'),
				'licenseCode'		=> $conf->get('licenseCode')
				);
			if( ! $formValues['file']['error'] ){
				$fullFileName = $formValues['file']['tmp_name'];
				$lines = file( $fullFileName );
				reset( $lines );
			/* find prefix */
				$oldPrefix = '';
				$oldTables = array();
				reset( $lines );
				foreach( $lines as $line ){
					$line = trim( $line );
					if( ! $line )
						continue;
					$re = '/(INTO|EXISTS)\s+([^\s]+)\b/U';
					if( preg_match($re, $line, $ma) ){
						$oldTables[ $ma[2] ] = 1;
						}
					}

				$oldPrefix = '';
				$oldTables = array_keys( $oldTables );

				$checkLen = 1;
				while( $checkLen ){
					$check = substr($oldTables[0], 0, $checkLen);
					for( $ii = 1; $ii < count($oldTables); $ii++ ){
						// skip users table
						if( substr($oldTables[$ii], - strlen('users')) == 'users' ){
							continue;
							}

						if( substr($oldTables[$ii], 0, $checkLen) != $check ){
							$oldPrefix = substr($oldTables[0], 0, $checkLen - 1);
							$checkLen = 0;
							break;
							}
						}
					if( $checkLen ){
						$checkLen++;
						}
					else {
						break;
						}
					}

				$drop_users = TRUE;
				if( substr($oldPrefix, 0, strlen('wp_')) == 'wp_' )
				{
					$drop_users = FALSE;
				}

			/* clear old tables */
				$tables = $ntsdb->getTablesInDatabase();
				reset( $tables );
				foreach( $tables as $t ){
					if( $t == 'conf' ){
						$sql = "TRUNCATE TABLE {PRFX}$t";
//						$sql = "DELETE FROM {PRFX}$t WHERE NOT ( (name = 'installationId') OR (name = 'licenseCode') )";
						$ntsdb->runQuery( $sql );
						}
					else {
						$sql = "DROP TABLE {PRFX}$t";
						if( ($t != 'users') OR $drop_users )
							$ntsdb->runQuery( $sql );
						}
					}

				$count = 0;
				foreach( $lines as $line ){
					$line = trim( $line );
					if( ! $line )
						continue;

				/* try to parse the table name */
					$re = '/[INTO|EXISTS]\s+' . $oldPrefix . '([^\s]+)\b/U';
					if( preg_match($re, $line, $ma) ){
						$tbl = $ma[1];
						if( $tbl == 'emaillog' ){
							$line = '';
							}
						else {
							$search = $oldPrefix . $tbl;
							$replace = '{PRFX}' . $tbl;
							$line = str_replace( $search, $replace, $line );
							}
						}

					if( $line ){
						$ntsdb->runQuery( $line );
						$count++;
						}
					}
					
				reset( $saveConf );
				foreach( $saveConf as $sc => $sv ){
					$conf->set( $sc, $sv );
					}
				ntsView::setAnnounce( M('Restore') . ': ' . "$count database queries". ': ' . M('OK'), 'ok' );
				}
			else {
				ntsView::setAnnounce( 'Upload Error', 'error' );
				}
			}
		else {
		/* form not valid, continue to create form */
			}
		break;
	
	default:
		break;
	}
?>
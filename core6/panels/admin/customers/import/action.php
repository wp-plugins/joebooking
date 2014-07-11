<?php
set_time_limit( 120 );
ini_set( 'memory_limit', '256M' );

$ff =& ntsFormFactory::getInstance();
$conf =& ntsConf::getInstance();
$ntsdb =& dbWrapper::getInstance();

$separator = $conf->get('csvDelimiter');

$formFile = dirname( __FILE__ ) . '/form';
$formParams = array();
$NTS_VIEW['form'] =& $ff->makeForm( $formFile, $formParams );

$customer_fields = array();
$className = 'customer';
$om =& objectMapper::getInstance();

foreach( $om->getFields( $className, 'internal' ) as $cf )
{
	$customer_fields[] = $cf[0];
}

$required_fields = array();
reset( $customer_fields );
foreach( $customer_fields as $fn )
{
	$f = $om->getControl( $className, $fn );
	if( isset($f[3]) )
	{
		foreach( $f[3] as $val )
		{
			if( preg_match('/notempty/i', $val['code']) )
			{
				$required_fields[] = $fn;
				break;
			}
		}
	}
}

$customer_fields[] = '_notes';
$customer_fields[] = '_restriction';
$NTS_VIEW['customer_fields'] = $customer_fields;
$NTS_VIEW['required_fields'] = $required_fields;

switch( $action )
{
	case 'upload':
		if( $NTS_VIEW['form']->validate() )
		{
			$formValues = $NTS_VIEW['form']->getValues();
			if( ! $formValues['file']['error'] )
			{
				$fullFileName = $formValues['file']['tmp_name'];
				if( ($handle = fopen($fullFileName, "r")) !== FALSE)
				{
					$line_no = 0;
					$entries = array();
					while( ($line = fgetcsv($handle, 1000, $separator)) !== FALSE )
					{
					// titles
						if( ! $line_no )
						{
							$prop_names = $line;
							for( $ii = 0; $ii < count($prop_names); $ii++ )
							{
								reset( $customer_fields );
								foreach( $customer_fields as $f )
								{
									if( strtolower($prop_names[$ii]) == $f['name'] )
									{
										$prop_names[$ii] = strtolower($prop_names[$ii]);
									}
								}
							}
							$prop_count = count( $prop_names );

						// check for mandatory fields
							$missing_fields = array();
							reset( $required_fields );
							foreach( $required_fields as $f )
							{
								if( ! in_array($f, $prop_names) )
								{
									$missing_fields[] = $f;
								}
							}
							if( $missing_fields )
							{
								$err_msg = M('Mandatory Fields Missing') . ': ' . join(', ', $missing_fields);
								ntsView::setAnnounce( $err_msg, 'error' );

								$forwardTo = ntsLink::makeLink( '-current-' );
								ntsView::redirect( $forwardTo );
								exit;
							}

						// check if any fields are not parsed
							$not_parsed_fields = array();
							reset( $prop_names );
							foreach( $prop_names as $f )
							{
								$f = trim( $f );
								if( ! $f )
									continue;
									
								if( ! (in_array($f, $customer_fields) OR in_array(strtolower($f), $customer_fields)) )
								{
									$not_parsed_fields[] = $f;
								}
							}

							if( $not_parsed_fields )
							{
								$err_msg = M('Fields Not Recognized') . ': ' . join(', ', $not_parsed_fields);
								ntsView::setAnnounce( $err_msg, 'ok' );
							}
						}
						else
						{
							$values = array();
							for( $i = 0; $i < $prop_count; $i++ )
							{
								$check_name = strtolower($prop_names[$i]);
								if( in_array($check_name, $customer_fields) )
								{
									if( isset($line[$i]) )
										$values[ $check_name ] = $line[$i];
									else
										$values[ $check_name ] = '';
									if( in_array($check_name, $required_fields) )
									{
										if( ! strlen($values[$check_name]) )
										{
											$values = array();
											break;
										}
									}
								}
							}
							if( $values )
							{
								$entries[] = $values;
							}
						}
						$line_no++;
					}
					fclose($handle);
				}

				$count = 0;
				$cm =& ntsCommandManager::getInstance();
				$uif =& ntsUserIntegratorFactory::getInstance();
				$integrator =& $uif->getIntegrator();

				reset( $entries );
				foreach( $entries as $e )
				{
				/* check if this customer already exists - check email, first_name and last_name */
					$myWhere = array();
					$myWhere['email'] = array('=', $e['email']);
					$myWhere['first_name'] = array('=', $e['first_name']);
					$myWhere['last_name'] = array('=', $e['last_name']);
					$already_count = $integrator->countUsers( $myWhere );
					if( $already_count )
					{
						$user_label = $e['first_name'] . ' ' . $e['last_name'] . ' [' . $e['email'] . ']';
						$announce = array( $user_label, M('Already in use') );
						$announce = join( ': ', $announce );
						ntsView::addAnnounce( $announce, 'error' );
					}
					else
					{
						$object = new ntsUser();
						$e['_notes'] = explode( ';', $e['_notes'] );
						$e['_restriction'] = explode( ';', $e['_restriction'] );

						$object->setByArray( $e );
						$object->setProp('_role', array($className) );
//						$object->setProp('_created_by', $NTS_CURRENT_USER->getId() );
						$cm->runCommand( $object, 'create' );
						$count++;
					}
				}
				if( $count > 0 )
				{
					$announce = array( M('Customers'), M('Create'), $count, M('OK') );
					$announce = join( ': ', $announce );
					ntsView::addAnnounce( $announce, 'ok' );
				}
			}
			else
			{
				ntsView::setAnnounce( 'Upload Error', 'error' );
			}
		}
		else
		{
		/* form not valid, continue to create form */
		}
		break;
	
	default:
		break;
}
?>
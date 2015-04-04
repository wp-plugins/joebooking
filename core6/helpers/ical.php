<?php
if( ! class_exists('vcalendar') ){
	include_once( NTS_LIB_DIR . '/lib/datetime/iCalcreator.class.php' );
	}

class ntsIcal {
	var $appointments = array();
	var $timezone = 0;
	var $summary = '';
	var $description = '';

	function ntsIcal(){
		$this->appointments = array();
		$this->setTimezone( NTS_COMPANY_TIMEZONE );

		$customFile = NTS_EXTENSIONS_DIR . '/ical.xml';
		if( file_exists($customFile) ){
			$xmlCode = ntsLib::fileGetContents( $customFile );

		/* get first line to see if encoding is defined */
			$firstLine = ntsLib::fileGetFirstLine( $customFile );
			$re = '/encoding\s*=\s*[\'|\"](.+)[\'|\"]/U';
			if( preg_match($re, $firstLine, $ma) ){
				$encoding = $ma[1];
				$parser = new xml_simple( $encoding );
				}
			else {
				$parser = new xml_simple();
				}

			$templateConf = $parser->parse( $xmlCode );

			if( $parser->error ){
			/* template file error */
				$error = "ICAL TEMPLATE FILE ERROR:<br>" . $customFile . "<br>" . $parser->error;
				echo $error;
				return;
				}
			if( ! ( isset($templateConf['summary']) && isset($templateConf['description']) )){
			/* template file error */
				$error = "ICAL TEMPLATE FILE ERROR:<br>" . $customFile . "<br>" . 'summary or description tag missing';
				echo $error;
				return;
				}
			$this->summary = $templateConf['summary'];
			$this->description = $templateConf['description'];
			}
		else {
			$summary = '{APPOINTMENT.SERVICE}';
			$ntsConf =& ntsConf::getInstance();
			$summarySetting = $ntsConf->get('icalSummary');

			switch( $summarySetting ){
				case 'customer':
					$summary = '{CUSTOMER_SHORT}';
					break;
				case 'service':
					$summary = '{APPOINTMENT.SERVICE}';
					break;
				case 'resource':
					$summary = '{BOOKABLE_RESOURCE_SHORT}';
					if( NTS_SINGLE_RESOURCE ){
						$summary = '{APPOINTMENT.SERVICE}';
					}
					break;
			}

			$this->summary =<<<EOT
$summary
EOT;

			$this->description =<<<EOT
{TIMEZONE_NOTE}
{APPOINTMENT.SERVICE}
{BOOKABLE_RESOURCE}
{CUSTOMER}
EOT;
			}
		}

	function addAppointment( $appId ){
		if( is_object($appId) )
			$appId = $appId->getId();
		$this->appointments[] = $appId;
		}

	function setTimezone( $tz ){
		$this->timezone = $tz;
		}

	function printOut( $addNotes = FALSE ){
		$om =& objectMapper::getInstance();
		$ntsdb =& dbWrapper::getInstance();
		$ntsdb->_enableCache = false;

		$web_dir = ntsLib::webDirName( ntsLib::getFrontendWebpage() );
		$cal = new vcalendar(); // initiate new CALENDAR
		$cal->setConfig( 'unique_id', $web_dir );
//		$cal->setProperty( 'method', 'publish' );
		$cal->setProperty( 'method', 'request' );
		$cal->setProperty( 'x-wr-timezone', $this->timezone );

		reset( $this->appointments );
		foreach( $this->appointments as $appId ){
			$a = ntsObjectFactory::get( 'appointment' );
			$a->setId( $appId );

			$serviceTitle = ntsView::appServiceView( $a );

			$location = new ntsObject('location');
			$location->setId( $a->getProp('location_id') );

			$customer = new ntsUser();
			$customer->setId( $a->getProp('customer_id') );
			$resource = ntsObjectFactory::get( 'resource' );
			$resource->setId( $a->getProp('resource_id') );

			$event = new vevent(); // initiate a new EVENT
			$event->setProperty( 'uid', 'app-' . $a->getId() . '-' . $web_dir );

//			$t = new ntsTime( $a->getProp('starts_at'), $this->timezone );
//			list( $year, $month, $day, $hour, $min ) = $t->getParts(); 
//			$event->setProperty( 'dtstart', $year, $month, $day, $hour, $min, 00, $this->timezone );  // 24 dec 2006 19.30

			$t = new ntsTime( $a->getProp('starts_at'), 'UTC' );
			list( $year, $month, $day, $hour, $min ) = $t->getParts(); 
			$event->setProperty( 'dtstart', $year, $month, $day, $hour, $min, 00, 'Z' );  // 24 dec 2006 19.30

			$t->modify( '+' . $a->getProp('duration') . ' seconds' );
			list( $year, $month, $day, $hour, $min ) = $t->getParts(); 
//			$event->setProperty( 'dtend', $year, $month, $day, $hour, $min, 00, $this->timezone );  // 24 dec 2006 19.30
			$event->setProperty( 'duration', 0,		0,		0,		0,		$a->getProp('duration') );

		// parse tags
			$tags = $om->makeTags_Appointment( $a, 'internal' );

			$tags[0][] = '{TIMEZONE_NOTE}';
			$timezoneNote = '';
			if( NTS_ENABLE_TIMEZONES >= 0 ){
				$timezoneNote = M('Timezone') . ': ' . ntsTime::timezoneTitle($this->timezone);
				}
			$tags[1][] = $timezoneNote;

			$tags[0][] = '{BOOKABLE_RESOURCE}';
			$bookableResource = '';
			if( ! NTS_SINGLE_RESOURCE ){
				$bookableResource = M('Bookable Resource') . ': ' . $resource->getProp('title');
				}
			$tags[1][] = $bookableResource;

			$tags[0][] = '{BOOKABLE_RESOURCE_SHORT}';
			$bookableResourceShort = '';
			if( ! NTS_SINGLE_RESOURCE ){
				$bookableResourceShort = $resource->getProp('title');
				}
			$tags[1][] = $bookableResourceShort;

			$customerInfo = M('Customer') . ': ' . $customer->getProp('first_name') . ' ' . $customer->getProp('last_name');
			$tags[0][] = '{CUSTOMER}';
			$tags[1][] = $customerInfo;

			$customerInfoShort = $customer->getProp('first_name') . ' ' . $customer->getProp('last_name');
			$tags[0][] = '{CUSTOMER_SHORT}';
			$tags[1][] = $customerInfoShort;

			$summary = str_replace( $tags[0], $tags[1], $this->summary );
			$description = str_replace( $tags[0], $tags[1], $this->description );

		// add notes if any
			if( $addNotes ){
				$notes = $a->getProp('_note');
				if( $notes ){
					$comments = array();
					foreach( $notes as $note ){
						$noteText = $note[0];
						list( $noteTime, $noteUserId ) = explode( ':', $note[1] );
						$noteUser = new ntsUser;
						$noteUser->setId( $noteUserId );
						$noteUserView = ntsView::objectTitle( $noteUser );
						$comments[] = $noteUserView . ': ' . $noteText;
						}
					$commentsView = join( ", ", $comments );
					$event->setProperty( 'comment', $commentsView );
					$description .= "\n" . M('Notes') . ": " . $commentsView;
					}
				}

			$event->setProperty( 'summary', $summary );
			$event->setProperty( 'description', $description );
			if( ! NTS_SINGLE_LOCATION )
			{
				$event->setProperty( 'location', ntsView::objectTitle($location) );
			}

			$event->setProperty( 'attendee', $customer->getProp('first_name') . ' ' . $customer->getProp('last_name') . ' <' . $customer->getProp('email') . '>'  );
//			$event->setProperty( 'organizer', $resource->getProp('title') );

			$cal->addComponent( $event );
			ntsObjectFactory::clearCache( 'appointment', $appId );
			}

		$return = $cal->createCalendar();                   // generate and get output in string
		return $return;
		}
	}
?>
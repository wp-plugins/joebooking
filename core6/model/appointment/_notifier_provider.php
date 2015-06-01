<?php
/* --- RETURN IF EMAIL DISABLED --- */
$conf =& ntsConf::getInstance();
if( $conf->get('emailDisabled') )
	return;

$runActions = array();
if( ($mainActionName == 'change') && ($changes = $object->getChanges()) && ( isset($changes['resource_id']) ) ){
	/* old resource */
	$oldRid = $changes['resource_id'];
	$resource = ntsObjectFactory::get( 'resource' );
	$resource->setId( $oldRid );

	list( $appsAdmins, $scheduleAdmins ) = $resource->getAdmins();
	$providers = array();
	reset( $appsAdmins );
	foreach( $appsAdmins as $admId => $access ){
		if( $access['notified'] ){
			$provider = new ntsUser;
			$provider->setId( $admId );
			$providers[] = $provider;
			}
		}
	$runActions[] = array( 'reassign_from', $providers );

	/* new resource */
	$newRid = $object->getProp('resource_id');
	$resource = ntsObjectFactory::get( 'resource' );
	$resource->setId( $newRid );

	list( $appsAdmins, $scheduleAdmins ) = $resource->getAdmins();
	$providers = array();
	reset( $appsAdmins );
	foreach( $appsAdmins as $admId => $access ){
		if( $access['notified'] ){
			$provider = new ntsUser;
			$provider->setId( $admId );
			$providers[] = $provider;
			}
		}
	$runActions[] = array( 'reassign_to', $providers );
	}
else {
/* --- GET TEMPLATE --- */
	$key = 'appointment-' . $mainActionName . '-provider';

	/* --- SKIP IF THIS NOTIFICATION DISABLED --- */
	$currentlyDisabled = $conf->get( 'disabledNotifications' );
	if( in_array($key, $currentlyDisabled) ){
		return;
		}

	/* --- SKIP IF NO TEMPLATE --- */
	$userLang = $defaultLanguage;
	$templateInfo = $etm->getTemplate( $userLang, $key );
	if( ! $templateInfo ){
		return;
		}

	$resourceId = $object->getProp( 'resource_id' );
	$resource = ntsObjectFactory::get( 'resource' );
	$resource->setId( $resourceId );

	list( $appsAdmins, $scheduleAdmins ) = $resource->getAdmins();
	$providers = array();
	reset( $appsAdmins );
	foreach( $appsAdmins as $admId => $access ){
		if( $access['notified'] ){
			$provider = new ntsUser;
			$provider->setId( $admId );
			$providers[] = $provider;
			}
		}
	$runActions[] = array( $mainActionName, $providers );
	}

reset( $runActions );
foreach( $runActions as $ra ){
	list( $mainActionName, $providers ) = $ra;
/* --- GET TEMPLATE --- */
	$key = 'appointment-' . $mainActionName . '-provider';

	/* --- SKIP IF THIS NOTIFICATION DISABLED --- */
	$currentlyDisabled = $conf->get( 'disabledNotifications' );
	if( in_array($key, $currentlyDisabled) ){
		continue;
		}

	/* --- SKIP IF NO TEMPLATE --- */
	$userLang = $defaultLanguage;
	$templateInfo = $etm->getTemplate( $userLang, $key );
	if( ! $templateInfo ){
		continue;
		}

	reset( $providers );
	foreach( $providers as $provider ){
		/* parse templates */
		$tags = $om->makeTags_Appointment( $object, 'internal', TRUE, $provider->getTimezone() );
		if( isset($params['reason']) ){
			$tags[0][] = '{REJECT_REASON}';
			$tags[1][] = $params['reason'];
			$tags[0][] = '{CANCEL_REASON}';
			$tags[1][] = $params['reason'];

			$tags[0][] = '{APPOINTMENT.REJECT_REASON}';
			$tags[1][] = $params['reason'];
			$tags[0][] = '{APPOINTMENT.CANCEL_REASON}';
			$tags[1][] = $params['reason'];
			}

		/* quick links */
		$authCode = $object->getProp( 'auth_code' );
		$approveLink = ntsLink::makeLink( 'system/appointments/edit', 'approve', array('auth' => $authCode, 'id' => $object->getId()) );
		$approveLink = '<a href="' . $approveLink . '">' . M('Approve') . '</a>';
		$rejectLink = ntsLink::makeLink( 'system/appointments/edit', 'reject', array('auth' => $authCode, 'id' => $object->getId()) );
		$rejectLink = '<a href="' . $rejectLink . '">' . M('Reject') . '</a>';

		$tags[0][] = '{APPOINTMENT.QUICK_LINK_APPROVE}';
		$tags[1][] = $approveLink;
		$tags[0][] = '{APPOINTMENT.QUICK_LINK_REJECT}';
		$tags[1][] = $rejectLink;

		/* add .ics attachement */
		$attachements = array();
		if( in_array($key, $attachTo) ){
			include_once( NTS_APP_DIR . '/helpers/ical.php' );
			$ntsCal = new ntsIcal();
			// $ntsCal->setTimezone( NTS_COMPANY_TIMEZONE );
			$ntsCal->setTimezone( $provider->getTimezone() );
			$ntsCal->addAppointment( $object );
			$str = $ntsCal->printOut();

			$attachName = 'appointment-' . $object->getId() . '.ics';
			$attachements[] = array( $attachName, $str );

			$tags[0][] = '{APPOINTMENT.LINK_TO_ICAL}';
			$tags[1][] = 'cid:' . $attachName;
			}
		else {
			$tags[0][] = '{APPOINTMENT.LINK_TO_ICAL}';
			$tags[1][] = '';
			}

		$group_ref = $object->getProp( 'group_ref' );
		if( $group_ref ){
			$customer_link = ntsLink::makeLink('customer/appointments/view', '', array('ref' => $group_ref));
			$customer_link = '<a href="' . $customer_link . '">' . M('View') . '</a>';
		}
		else {
			$customer_link = '';
		}
		$tags[0][] = '{APPOINTMENT.CUSTOMER_LINK_TO}';
		$tags[1][] = $customer_link;

		$provider_link = ntsLink::makeLink('admin/manage/appointments/edit/overview', '', array('_id' => $object->getId()));
		$provider_link = '<a href="' . $provider_link . '">' . M('View') . '</a>';
		$tags[0][] = '{APPOINTMENT.PROVIDER_LINK_TO}';
		$tags[1][] = $provider_link;

		/* replace tags */
		$subject = str_replace( $tags[0], $tags[1], $templateInfo['subject'] );
		$body = str_replace( $tags[0], $tags[1], $templateInfo['body'] );

	/* --- SEND EMAIL --- */
		$this->runCommand( $provider, 'email', array('body' => $body, 'subject' => $subject, 'attachements' => $attachements) );
	}

	/* --- CC IF ANY --- */
	$cc = $object->getProp('_cc');
	if( $cc ){
		reset( $cc );
		foreach( $cc as $cc_to ){
			$cc_to = trim( $cc_to );
			if( $cc_to ){
				$tempUser = new ntsUser;
				$tempUser->setProp('email', $cc_to);
				$tempUser->setProp('first_name', $cc_to);
				$this->runCommand( $tempUser, 'email', array('body' => $body, 'subject' => $subject, 'attachements' => $attachements) );
			}
		}
	}
	}
?>
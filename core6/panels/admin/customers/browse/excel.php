<?php
$entries = $this->data['entries'];
$upcomingCount = ntsLib::getVar( 'admin/customers::upcomingCount' );
$oldCount = ntsLib::getVar( 'admin/customers::oldCount' );

/* fields */
$fields = array();
$className = 'customer';
$om =& objectMapper::getInstance();
$customerFields = $om->getFields( $className, 'internal' );
reset( $customerFields );
foreach( $customerFields as $f )
{
	$fields[] = array( $f[0], $f[1], $f[2] );
}

/*
if( NTS_EMAIL_AS_USERNAME ){
	$fields[] = array( 'email', M('Email') );
	}
else {
	$fields[] = array( 'username', M('Username') );
	$fields[] = array( 'email', M('Email') );
	}
$fields[] = array( 'full_name', M('Full Name') );
*/
$fields[] = array( 'appointments', M('Appointments') );
//$fields[] = array( 'status', M('Status') );
$fields[] = array( '_notes', M('Notes') );
$fields[] = array( '_restriction', M('Restrictions') );

$headers = array();
reset( $fields );
foreach( $fields as $f )
{
//	$headers[] = $f[1];
	$headers[] = $f[0];
}
echo ntsLib::buildCsv( array_values($headers) );
echo "\n";

$t = $NTS_VIEW['t'];

reset( $entries );
foreach( $entries as $u ){
	$objId = $u->getId();
	$output = array();
	reset( $fields );
	foreach( $fields as $f )
	{
		$fieldView = $u->getProp($f[0]);
		if( isset($f[2]) && ($f[2] == 'checkbox') )
		{
			$fieldView = $fieldView ? M('Yes') : M('No');
		}
		$output[$f[0]] = $fieldView;
	}

//	$output['email'] = $u->getProp('email');
//	$output['username'] = $u->getProp('username');
//	$output['full_name'] = ntsView::objectTitle( $u );

/*
	list( $alert, $cssClass, $message ) = $u->getStatus();
	$output['status'] = $message;
*/

	$notesView = '';
	$notes = $u->getProp('_note');
	if( $notes )
	{
		$notesView = array();
		foreach( $notes as $noteText => $note )
		{
			list( $noteTime, $noteUserId ) = explode( ':', $note );
/* don't show the user
			$noteUser = new ntsUser;
			$noteUser->setId( $noteUserId );
			$noteUserView = ntsView::objectTitle( $noteUser );
			$notesView[] = $noteUserView . ': ' . $noteText;
*/
			$notesView[] = $noteText;
		}
		$notesView = join( ";", $notesView );
	}
	$output['_notes'] = $notesView;

	$restrictionView = '';
	$restrictions = $u->getProp('_restriction');
	if( $restrictions )
	{
		$restrictionView = array();
		foreach( $restrictions as $restriction )
		{
			$restrictionView[] = $restriction;
		}
		$restrictionView = join( ";", $restrictionView );
	}
	$output['_restriction'] = $restrictionView;

	$totalCount = 0;
	if( isset($upcomingCount[$objId]) )
		$totalCount += $upcomingCount[$objId];
	if( isset($oldCount[$objId]) )
		$totalCount += $oldCount[$objId];
	$output['appointments'] = $totalCount;

	$outLines = array();
	reset( $fields );
	foreach( $fields as $f ){
		$outLines[] = $output[ $f[0] ];
		}
	echo ntsLib::buildCsv( $outLines );
	echo "\n";
	}
?>
<?php
$ntsdb =& dbWrapper::getInstance();
$conf =& ntsConf::getInstance();

$key = $_NTS['REQ']->getParam( 'key' );
$key = trim( $key );

/* get this user restriction */
$sql =<<<EOT
SELECT 
	obj_id
FROM 
	{PRFX}objectmeta
WHERE
	meta_name = "_confirmKey" AND
	meta_value = "$key" AND 
	obj_class = "user"
EOT;

$result = $ntsdb->runQuery( $sql );
$r = $result->fetch();

/* ok */
if( $r ){
	$cm =& ntsCommandManager::getInstance();
	$userAdminApproval = $conf->get('userAdminApproval');

	$userId = $r['obj_id'];
	$object = new ntsUser();
	$object->setId( $userId );

	$cm->runCommand( $object, 'confirm_email' );
	if( $userAdminApproval ) {
	/* required approval by admin */
		$cm->runCommand( $object, 'require_approval' );

		$display = 'waitingApproval';
		$forwardTo = ntsLink::makeLink( '-current-/../', '', array('display' => $display) );
		ntsView::redirect( $forwardTo );
		exit;
		}
	else {
	/* autoapprove */
		$cm->runCommand( $object, 'activate' );
		ntsView::addAnnounce( M('Congratulations, your account has been created and activated'), 'ok' );
	/* then login */
		$cm->runCommand( $object, 'login' );

	/* continue to login dispatcher */
		$forwardTo = ntsLink::makeLink( 'anon/login/dispatcher' );
		ntsView::redirect( $forwardTo );
		exit;
		}
	}
/* failed - wrong code probably */
else {
	ntsView::addAnnounce( M('Wrong Confirmation Link'), 'error' );
	$forwardTo = ntsLink::makeLink();
	ntsView::redirect( $forwardTo );
	exit;
	}
?>
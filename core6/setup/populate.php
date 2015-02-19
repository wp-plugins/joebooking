<?php
$ntsdb =& dbWrapper::getInstance();

$cm =& ntsCommandManager::getInstance();

global $NTS_SETUP_ADMINS;
/* admin */
if( ! $NTS_SETUP_ADMINS ){
	$adminFname = $_POST['admin_fname'];
	$adminLname = $_POST['admin_lname'];
	$adminUsername = $_POST['admin_username'];
	$adminEmail = $_POST['admin_email'];
	$adminPassword = $_POST['admin_pass'];

	$admin = new ntsUser();
	$admin->setProp( 'username', $adminUsername );
	$admin->setProp( 'password', $adminPassword );
	$admin->setProp( 'email', $adminEmail );
	$admin->setProp( 'first_name', $adminFname );
	$admin->setProp( 'last_name', $adminLname );

	$cm->runCommand( $admin, 'create' );
	$adminId = $admin->getId();
	if( ! $cm->isOk() ){
		echo '<BR>Command error:<BR>' . $cm->printActionErrors() . '<BR>';
		exit;
		}
	$NTS_SETUP_ADMINS = array( $adminId );
	}

reset( $NTS_SETUP_ADMINS );
foreach( $NTS_SETUP_ADMINS as $admId ){
	$admin = new ntsUser;
	$admin->setId( $admId );
	$admin->setProp( '_role', array('admin') );
	$cm->runCommand( $admin, 'update' );
	}

/* email sent from */
$setting = $admin->getProp( 'email' );
$conf->set( 'emailSentFrom', $setting );

/* email sent from name */
$setting = $admin->getProp( 'first_name' ) .  ' ' . $admin->getProp( 'last_name' );
$conf->set( 'emailSentFromName', $setting );

/* DEFAULT FORMS - CUSTOMER */
$form = new ntsObject( 'form' );
$form->setProp( 'title', 'Customer Form' );
$form->setProp( 'class', 'customer' );
$form->setProp( 'details', '' );

$cm->runCommand( $form, 'create' );
if( ! $cm->isOk() ){
	echo '<BR>Command error:<BR>' . $cm->printActionErrors() . '<BR>';
	exit;
	}
// FORM CONTROLS
$order = 0;
$controls = array(
	array( 'username',		'Username', 	'text',	array('size' => 24), array( array('code' => 'notEmpty', 'error' => M('Required')), array('code' => 'checkUsername', 'error' => 'This username is already in use', 'params' => array('skipMe'	=> 1) ) ) ),
	array( 'email',			'Email',		'text',	array('size' => 32), array( array('code' => 'notEmpty', 'error' => 'Required'), array('code' => 'checkUserEmail', 'error' => 'This email is already in use', 'params' => array('skipMe'	=> 1) ) ) ),
	array( 'first_name',	'First Name',	'text',	array('size' => 32), array( array('code' => 'notEmpty', 'error' => 'Please enter the first name') ) ),
	array( 'last_name',		'Last Name',	'text',	array('size' => 32), array( array('code' => 'notEmpty', 'error' => 'Please enter the last name') ) ),
	);
reset( $controls );
$formId = $form->getId();
foreach( $controls as $c ){
	$order++;
	$object = ntsObjectFactory::get( 'form_control' );
	$object->setProp( 'form_id', $formId );
	$object->setProp( 'ext_access', 'write' );
	$object->setProp( 'class', 'customer' );

	$object->setProp( 'name', $c[0] );
	$object->setProp( 'title', $c[1] );
	$object->setProp( 'type', $c[2] );
	$object->setProp( 'attr', $c[3] );
	$object->setProp( 'validators', $c[4] );
	$object->setProp( 'show_order', $order );
	$cm->runCommand( $object, 'create' );
	}

// payment gateways
$setting = array('offline');
$conf->set( 'paymentGateways', $setting );

// SAVE THE INSTALLED VERSION
$conf->set('currentVersion', ntsLib::getAppVersion() );

$now = time();
$conf->set('backupLastRun', $now );

/* populate scripts */
if( ! isset($script) )
	$script = isset($_POST['script']) ? $_POST['script'] : '';
if( ! $script ){
	$scripts = ntsLib::listFiles( dirname(__FILE__) . '/scripts' );
	if( isset($scripts[0]) ){
		$script = substr( $scripts[0], 0, -strlen('.php') );
		}
	}

if( $script ){
	$scriptFile = dirname(__FILE__) . '/scripts/' . $script . '.php';
	if( file_exists($scriptFile) ){
		require( $scriptFile );
		}
	}

$refno = $conf->get( 'installationId' );
if( ! $refno ){
	$refno = md5(rand());
	$conf->set( 'installationId', $refno );
	}
?>
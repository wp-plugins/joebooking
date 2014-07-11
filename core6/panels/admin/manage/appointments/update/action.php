<?php
$object = ntsLib::getVar( 'admin/manage/appointments/update::OBJECT' );
$ff =& ntsFormFactory::getInstance();

$confirm_suffix = '-confirm';
if( substr($action, -strlen($confirm_suffix)) == $confirm_suffix )
{
	$real_action = substr($action, 0, -strlen($confirm_suffix));
}
else
{
	$real_action = $action;
}

$form_file = 'form';
if( in_array($real_action, array('reject')) )
	$form_file = 'form-reason';

$NTS_VIEW['form'] =& $ff->makeForm( dirname(__FILE__) . '/' . $form_file );

if( substr($action, -strlen($confirm_suffix)) == $confirm_suffix )
{
	$cm =& ntsCommandManager::getInstance();

	$real_action = substr($action, 0, -strlen($confirm_suffix));
	if( ! is_array($object) )
		$object = array( $object );

	require( dirname(__FILE__) . '/_action-' . $real_action . '.php' );
	require( dirname(__FILE__) . '/_after_action.php' );
}
?>
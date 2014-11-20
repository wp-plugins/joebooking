<?php
$matrix = array(
'appointments'	=> array(
	'customer'	=> array(
		array( 'appointment-require_approval-customer',	M('Appointment') . ': ' . M('Approval') . ': ' . M('Required') ),
		array( 'appointment-approve-customer',			M('Appointment') . ': ' . M('Approve') ),
		array( 'appointment-request-customer',			M('Appointment') . ': ' . M('Request (Automatically Approved)') ),
		array( 'appointment-reject-customer', 			M('Appointment') . ': ' . M('Reject') ),
		array( 'appointment-cancel-customer',			M('Appointment') . ': ' . M('Cancel') ),
		array( 'appointment-complete-customer',			M('Appointment') . ': ' . M('Completed') ),
		array( 'appointment-change-customer',			M('Appointment') . ': ' . M('Change') ),
		array( 'appointment-noshow-customer',			M('Appointment') . ': ' . M('No Show') ),
		array( 'appointment-showup-customer',			M('Appointment') . ': ' . M('Unmark') . ': ' . M('No Show') ),
		array( 'appointment-remind-customer',			M('Appointment') . ': ' . M('Reminder') ),
		array( 'order-request-customer',					M('Package') . ': ' . M('Purchase') ),
		),
	'provider'	=> array(
		array( 'appointment-require_approval-provider',	M('Appointment') . ': ' . M('Approval') . ': ' . M('Required') ),
		array( 'appointment-approve-provider',			M('Appointment') . ': ' . M('Approve') ),
		array( 'appointment-request-provider',			M('Appointment') . ': ' . M('Request (Automatically Approved)') ),
		array( 'appointment-reject-provider', 			M('Appointment') . ': ' . M('Reject') ),
		array( 'appointment-cancel-provider',			M('Appointment') . ': ' . M('Cancel') ),
		array( 'appointment-complete-provider',			M('Appointment') . ': ' . M('Completed') ),
		array( 'appointment-change-provider',			M('Appointment') . ': ' . M('Change') ),
		array( 'appointment-reassign_from-provider',	M('Appointment') . ': ' . M('Reassign') . ': ' . M('From') ),
		array( 'appointment-reassign_to-provider',		M('Appointment') . ': ' . M('Reassign') . ': ' . M('To') ),
		array( 'appointment-noshow-provider',			M('Appointment') . ': ' . M('No Show') ),
		array( 'appointment-showup-provider',			M('Appointment') . ': ' . M('Unmark') . ': ' . M('No Show') ),
		array( 'appointment-remind-provider',			M('Appointment') . ': ' . M('Reminder') ),
		array( 'order-request-admin',					M('Package') . ': ' . M('Purchase') ),
		),
	),
'user'	=> array(
	'user'	=> array(
		array( 'user-require_email_confirmation-user',	M('Email') . ': ' . M('Confirmation') . ': ' . M('Required') ),
		array( 'user-require_approval-user',			M('Waiting For Approval') ),
		array( 'user-activate-user',					M('User') . ': ' . M('Activate') . ': ' . M('OK') ),
		array( 'user-reset_password-user', 				M('Password Reset') ),
		),
	'admin'	=> array(
		array( 'user-require_approval-admin',	M('Approval') . ': ' . M('Required') ),
		array( 'user-activate-admin',			M('User') . ': ' . M('Activate') . ': ' . M('OK') ),
		),
	),
);
?>
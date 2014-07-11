<?php
global $NTS_CURRENT_USER;
?>

<table class="ntsForm">
<tr>
	<td class="ntsFormLabel"><?php echo M('Enable Registration'); ?>?</td>
	<td class="ntsFormValue">
<?php
	echo $this->makeInput (
	/* type */
		'checkbox',
	/* attributes */
		array(
			'id'	=> 'enableRegistration',
			)
		);
?>
	</td>
</tr>

<tr>
	<td class="ntsFormLabel"><?php echo M('Approval Required'); ?></td>
	<td class="ntsFormValue">
<?php
	echo $this->makeInput (
	/* type */
		'checkbox',
	/* attributes */
		array(
			'id'	=> 'userAdminApproval',
			)
		);
?>
	</td>
</TR>
<tr>
	<td colspan="2">
	<i><?php echo M('The administrator will need to manually approve new user accounts before they can access the system'); ?></i>
	</td>
</tr>

<tr>
	<td class="ntsFormLabel"><?php echo M('User Login Required?'); ?></td>
	<td class="ntsFormValue">
<?php
	echo $this->makeInput (
	/* type */
		'checkbox',
	/* attributes */
		array(
			'id'	=> 'userLoginRequired',
			)
		);
?>
	</td>
</tr>

<tr>
	<td class="ntsFormLabel"><?php echo M('Email Confirmation Required'); ?>?</td>
	<td class="ntsFormValue">
<?php
	echo $this->makeInput (
	/* type */
		'checkbox',
	/* attributes */
		array(
			'id'	=> 'userEmailConfirmation',
			)
		);
?>
	</TD>
</TR>
<tr>
	<td colspan="2">
	<i><?php echo M('Newly registered users will be asked to confirm their email by following a confirmation link in the email message'); ?></i>
	</td>
</tr>

<tr>
	<td class="ntsFormLabel"><?php echo M('Use Email As Username'); ?></td>
	<td class="ntsFormValue">
<?php
	$ri = ntsLib::remoteIntegration();
	$thisFieldReadonly = $ri ? TRUE : FALSE;
	$attr = array(
		'id'		=> 'emailAsUsername'
		);
	if( $thisFieldReadonly ){
		$attr['readonly'] = 1;
		$attr['value'] = 0;
		}
	echo $this->makeInput (
	/* type */
		'checkbox',
	/* attributes */
		$attr
		);
?>
	</td>
</tr>
<tr>
	<td colspan="2">
	<i><?php echo M('When enabling this, be sure you remember your own email address'); ?>:</i> <b><?php echo $NTS_CURRENT_USER->getProp('email'); ?></b>
	</td>
</tr>

<tr>
	<td class="ntsFormLabel"><?php echo M('Allow Duplicate Emails'); ?></td>
	<td class="ntsFormValue">
<?php
	$ri = ntsLib::remoteIntegration();
	$thisFieldReadonly = $ri ? TRUE : FALSE;
	$attr = array(
		'id'		=> 'allowDuplicateEmails'
		);
	if( $thisFieldReadonly ){
		$attr['readonly'] = 1;
		$attr['value'] = 0;
		}
	echo $this->makeInput (
	/* type */
		'checkbox',
	/* attributes */
		$attr
		);
?>
	</td>
</tr>

<tr>
	<td class="ntsFormLabel"><?php echo M('Allow No Email'); ?></td>
	<td class="ntsFormValue">
<?php
	$thisFieldReadonly = false;
	$attr = array(
		'id'		=> 'allowNoEmail'
		);
	if( $thisFieldReadonly ){
		$attr['readonly'] = 1;
		$attr['value'] = 0;
		}
	echo $this->makeInput (
	/* type */
		'checkbox',
	/* attributes */
		$attr
		);
?>
	</td>
</tr>

<tr>
	<td class="ntsFormLabel"><?php echo M('Customers Can Cancel Appointments'); ?></td>
	<td class="ntsFormValue">
<?php
	echo $this->makeInput (
	/* type */
		'checkbox',
	/* attributes */
		array(
			'id'	=> 'customerCanCancel',
			)
		);
?>
	</td>
</tr>

<?php if( 0 ) : ?>
<tr>
	<td class="ntsFormLabel"><?php echo M('Customers Can Reschedule Appointments'); ?></td>
	<td class="ntsFormValue">
<?php
	echo $this->makeInput (
	/* type */
		'checkbox',
	/* attributes */
		array(
			'id'	=> 'customerCanReschedule',
			)
		);
?>
	</td>
</tr>
<?php endif; ?>

<tr>
	<td class="ntsFormLabel"><?php echo M('Timezone'); ?></td>
	<td class="ntsFormValue">
<?php
$tzOptions = array(
	array( 1, M('Allow To Set Own Timezone') ),
	array( 0, M('Only View The Timezone') ),
	array( -1, M('Do Not Show The Timezone') ),
	);
echo $this->makeInput (
/* type */
	'select',
/* attributes */
	array(
		'id'	=> 'enableTimezones',
		'options'	=> $tzOptions,
		)
	);
?>
</td>
</tr>

<tr>
	<td class="ntsFormLabel"><?php echo M('First Time Visitors Splash Screen'); ?></td>
	<td class="ntsFormValue">
<?php
echo $this->makeInput (
/* type */
	'textarea',
/* attributes */
	array(
		'id'	=> 'firstTimeSplash',
		'attr'	=> array(
			'cols'	=> 48,
			'rows'	=> 6,
			),
		)
	);
?>
<br>If you use a web address (starting with http://), it will redirect to that page
</td>
</tr>

<tr>
	<td>&nbsp;</td>
	<td>
<?php echo $this->makePostParams('-current-', 'update'); ?>
<input class="btn btn-default" type="submit" value="<?php echo M('Save'); ?>">
	</td>
</tr>
</TABLE>

<SCRIPT LANGUAGE="JavaScript">
var emailAsUsernameCtl = "#<?php echo $this->getName(); ?>-emailAsUsername";
var userEmailConfirmationCtl = "#<?php echo $this->getName(); ?>-userEmailConfirmation";
var allowNoEmailCtl = "#<?php echo $this->getName(); ?>-allowNoEmail";
var allowDuplicateEmailsCtl = "#<?php echo $this->getName(); ?>-allowDuplicateEmails";

function ntsProcessInputs_1(){
	if ( 
		( jQuery(emailAsUsernameCtl).attr("checked") == true ) ||
		( jQuery(userEmailConfirmationCtl).attr("checked") == true )
		){
		jQuery(allowNoEmailCtl).attr('checked', false);
		jQuery(allowNoEmailCtl).attr('disabled', true);
		jQuery(allowDuplicateEmailsCtl).attr('checked', false);
		jQuery(allowDuplicateEmailsCtl).attr('disabled', true);
		}
	else {
		jQuery(allowNoEmailCtl).removeAttr('disabled');
		jQuery(allowDuplicateEmailsCtl).removeAttr('disabled');
		}
	}

function ntsProcessInputs_2(){
	if ( jQuery(allowNoEmailCtl).attr("checked") == true ){
		jQuery(emailAsUsernameCtl).attr('checked', false);
		jQuery(emailAsUsernameCtl).attr('disabled', true);
		jQuery(userEmailConfirmationCtl).attr('checked', false);
		jQuery(userEmailConfirmationCtl).attr('disabled', true);
		}
	else {
		jQuery(emailAsUsernameCtl).removeAttr('disabled');
		jQuery(userEmailConfirmationCtl).removeAttr('disabled');
		}
	}

function ntsProcessInputs_3(){
	if ( jQuery(allowDuplicateEmailsCtl).attr("checked") == true ){
		jQuery(emailAsUsernameCtl).attr('checked', false);
		jQuery(emailAsUsernameCtl).attr('disabled', true);
		}
	else {
		jQuery(emailAsUsernameCtl).removeAttr('disabled');
		}
	}

jQuery(emailAsUsernameCtl).bind( "click", ntsProcessInputs_1 );
jQuery(userEmailConfirmationCtl).bind( "click", ntsProcessInputs_1 );
jQuery(allowNoEmailCtl).bind( "click", ntsProcessInputs_2 );
jQuery(allowDuplicateEmailsCtl).bind( "click", ntsProcessInputs_3 );

ntsProcessInputs_1();
ntsProcessInputs_2();
</script>
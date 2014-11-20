<?php
global $NTS_CURRENT_USER;
$ri = ntsLib::remoteIntegration();
?>

<table class="ntsForm">
<tr>
	<td class="ntsFormLabel"><?php echo M('Registration'); ?></td>
	<td class="ntsFormValue">
<?php
	echo $this->makeInput (
	/* type */
		'select',
	/* attributes */
		array(
			'id'	=> 'enableRegistration',
			'options'	=> array(
				array( 1, M('Yes') ),
				array( 0, M('No') ),
				),
			)
		);
?>
	</td>
</tr>

<tr>
	<td class="ntsFormLabel"><?php echo M('Approval'); ?></td>
	<td class="ntsFormValue">
<?php
	echo $this->makeInput (
	/* type */
		'select',
	/* attributes */
		array(
			'id'	=> 'userAdminApproval',
			'options'	=> array(
				array( 1, M('Required') ),
				array( 0, M('No') ),
				),
			)
		);
?>
	</td>
</TR>

<tr>
	<td class="ntsFormLabel"><?php echo M('Login'); ?></td>
	<td class="ntsFormValue">
<?php
	echo $this->makeInput (
	/* type */
		'select',
	/* attributes */
		array(
			'id'	=> 'userLoginRequired',
			'options'	=> array(
				array( 1, M('Required') ),
				array( 0, M('Not Required') ),
				),
			)
		);
?>
	</td>
</tr>

<tr>
	<td class="ntsFormLabel"><?php echo M('Email') . ': ' . M('Confirmation'); ?></td>
	<td class="ntsFormValue">
	<?php
	echo $this->makeInput (
	/* type */
		'select',
	/* attributes */
		array(
			'id'	=> 'userEmailConfirmation',
			'options'	=> array(
				array( 1, M('Required') ),
				array( 0, M('No') ),
				),
			)
		);
	?>
	</TD>
</TR>

<?php if( ! $ri ) : ?>
	<tr>
		<td class="ntsFormLabel"><?php echo M('Use Email As Username'); ?></td>
		<td class="ntsFormValue">
	<?php
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
<?php endif; ?>

<?php if( ! $ri ) : ?>
	<tr>
		<td class="ntsFormLabel"><?php echo M('Emails'); ?>: <?php echo M('Duplicated'); ?></td>
		<td class="ntsFormValue">
	<?php
		echo $this->makeInput (
		/* type */
			'select',
		/* attributes */
			array(
				'id'		=> 'allowDuplicateEmails',
				'options'	=> array(
					array( 1, M('Allowed') ),
					array( 0, M('No') ),
					),
				)
			);
	?>
		</td>
	</tr>
<?php endif; ?>

<tr>
	<td class="ntsFormLabel"><?php echo M('No Email'); ?></td>
	<td class="ntsFormValue">
<?php
	$thisFieldReadonly = false;
	$attr = array(
		'id'		=> 'allowNoEmail',
		'options'	=> array(
			array( 1, M('Allowed') ),
			array( 0, M('No') ),
			),
		);
	if( $thisFieldReadonly ){
		$attr['readonly'] = 1;
		$attr['value'] = 0;
		}
	echo $this->makeInput (
	/* type */
		'select',
	/* attributes */
		$attr
		);
?>
	</td>
</tr>

<tr>
	<td class="ntsFormLabel"><?php echo M('Appointment'); ?>: <?php echo M('Cancel'); ?></td>
	<td class="ntsFormValue">
<?php
	echo $this->makeInput (
	/* type */
		'select',
	/* attributes */
		array(
			'id'	=> 'customerCanCancel',
			'options'	=> array(
				array( 1, M('Allowed') ),
				array( 0, M('No') ),
				),
			)
		);
?>
	</td>
</tr>

<?php if( 0 ) : ?>
<tr>
	<td class="ntsFormLabel"><?php echo M('Appointment'); ?>: <?php echo M('Reschedule'); ?></td>
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
	array( 1, M('Edit') ),
	array( 0, M('View') ),
	array( -1, M('Hide') ),
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
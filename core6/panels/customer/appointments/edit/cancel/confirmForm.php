<?php
$return = $this->getValue(NTS_PARAM_RETURN);
$params[NTS_PARAM_RETURN] = $return;

$id = $this->getValue('id');
$params['_id'] = $id;

$conf =& ntsConf::getInstance();
$requireCancelReason = $conf->get( 'requireCancelReason' );
?>
<?php if( $requireCancelReason ) : ?>
	<p>
	<?php echo M('Please provide the cancel reason'); ?>
	</p>
	<p>
	<?php
		echo $this->makeInput (
		/* type */
			'textarea',
		/* attributes */
			array(
				'id'		=> 'reason',
				'attr'		=> array(
					'cols'	=> 48,
					'rows'	=> 4,
					),
				'default'	=> '',
				),
		/* validators */
			array(
				)
			);
	?>
	</p>
<?php endif; ?>

<p>
<?php echo $this->makePostParams('-current-', 'cancel', $params ); ?>
<input class="btn btn-danger" type="submit" VALUE="<?php echo M('Yes'); ?>, <?php echo M('Cancel'); ?>"> <A class="btn btn-default" HREF="javascript:history.go(-1);"><?php echo M('No'); ?></A>

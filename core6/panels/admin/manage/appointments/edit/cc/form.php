<?php
$ccTo = 3;
?>
<ul class="list-unstyled list-separated">
<?php  for( $cc = 1; $cc <= $ccTo; $cc++ ) : ?>
	<li>
		<?php echo M('Email'); ?>: 
		<?php
		echo $this->makeInput (
		/* type */
			'text',
		/* attributes */
			array(
				'id'		=> 'cc_' . $cc,
				'attr'		=> array(
					'size'	=> 32,
					),
				'default'	=> '',
				),
		/* validators */
			array(
				array(
					'code'		=> 'email', 
					'error'		=> M('Valid Email Required'),
					),
				)
			);
		?>
	</li>
<?php endfor; ?>
</ul>

<?php echo $this->makePostParams('-current-', 'save'); ?>
<input class="btn btn-default" type="submit" value="<?php echo M('Save'); ?>">

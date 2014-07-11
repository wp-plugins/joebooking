<div class="page-header">
	<H2><?php echo M('Login'); ?></H2>
</div>

<?php 
echo $NTS_VIEW['form']->display( 
	array(
		'user' => $NTS_VIEW['user']
		)
	);
?>

<div class="form-horizontal collapse-panel">
	<?php
	echo ntsForm::wrapInput(
		'',
		'<a href="#" data-toggle="collapse-next">' . M('Forgot Your Password') . '?</a>'
		);
	?>
	<div class="collapse">
		<?php echo $NTS_VIEW['form_forgot']->display(); ?>
	</div>
</div>

<?php if( NTS_ENABLE_REGISTRATION ) : ?>
	<div class="form-horizontal">
		<?php
		echo ntsForm::wrapInput(
			'',
			'<a href="' . ntsLink::makeLink('anon/register' ) . '">' . M('New to our site? Please take a moment to register!') . '?</a>'
			);
		?>
	</div>
<?php endif; ?>


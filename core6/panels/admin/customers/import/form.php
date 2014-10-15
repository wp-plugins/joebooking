<?php
$customer_fields = $NTS_VIEW['customer_fields'];
$required_fields = $NTS_VIEW['required_fields'];
?>

<div class="row">
<div class="col-md-6">
	<?php
	echo ntsForm::wrapInput(
		M('CSV File'),
		$this->buildInput (
		/* type */
			'upload',
		/* attributes */
			array(
				'id'	=> 'file',
				)
			)
		);
	?>

	<?php echo $this->makePostParams('-current-', 'upload'); ?>
	<?php
	echo ntsForm::wrapInput(
		'',
		'<input class="btn btn-default" type="submit" value="' . M('Import') . '">'
		);
	?>
</div>
<div class="col-md-6">
	<p>
	<?php echo M('We can recognize the following fields in your CSV file'); ?>:
	</p>

	<ul class="list-unstyled list-separated">
	<?php foreach( $customer_fields as $cf ) : ?>
		<li>
			<?php if( in_array($cf, $required_fields) ) : ?>
				<strong><?php echo $cf; ?></strong>
			<?php else :  ?>
				<?php echo $cf; ?>
			<?php endif; ?>
		</li>
	<?php endforeach; ?>
	</ul>
</div>
</div>

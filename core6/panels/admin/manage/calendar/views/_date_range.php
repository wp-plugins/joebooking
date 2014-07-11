<?php
$form = new ntsForm2;
?>
<?php
echo $form->start(TRUE);
echo $form->make_post_params(
	'-current-',
	'range',
	array(
		'start'	=> NULL,
		'end'	=> NULL,
		)
	);
?>
<ul class="list-inline">
	<li>
		<?php
		echo $form->input(
			'date/Calendar',
			array(
				'id'	=> 'start',
				'value'	=> $start_date,
				)
			);
		?>
	</li>
	<li>
		-
	</li>
	<li>
	<?php
	echo $form->input(
		'date/Calendar',
		array(
			'id'	=> 'end',
			'value'	=> $end_date,
			)
		);
	?>
	</li>
	<li>
		<input class="btn btn-info" type="submit" value="<?php echo M('Go'); ?>"> 
	</li>
</ul>
<?php echo $form->end(); ?>
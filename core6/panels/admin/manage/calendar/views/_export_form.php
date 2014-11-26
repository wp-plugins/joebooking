<?php
$labels = ntsAppointment::dump_labels();
?>
<div style="width: 30em; overflow: auto; text-align: left;">
	<ul class="list-inline list-separated">
	<?php foreach( $labels as $k => $label ) : ?>
		<li style="float: left;">
			<label>
			<?php
			echo $this->makeInput(
			/* type */
				'checkbox',
			/* attributes */
				array(
					'id'		=> 'field_' . $k,
					'default'	=> 1,
					)
				);
			?> <?php echo $label; ?>
			</label> 
		</li>
	<?php endforeach; ?>
	</ul>
</div>

<?php echo $this->makePostParams('-current-', 'export'); ?>

<p>
<input class="btn btn-default" type="submit" value="<?php echo M('Download'); ?>">
</p>
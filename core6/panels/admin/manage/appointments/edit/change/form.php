<?php
$selected_status = $this->getValue('selected_status');

$btn_status = 'success';
foreach( $selected_status as $k => $v )
{
	if( ! is_array($v) )
		continue;
	$break_this = FALSE;
	foreach( $v as $k2 => $v2 )
	{
		if( is_array($v2) )
		{
			$btn_status = 'danger';
			$break_this = TRUE;
			break;
		}
		elseif( ! $v2 )
		{
			$btn_status = 'archive';
			$break_this = TRUE;
			break;
		}
	}
	if( $break_this )
		break;
}
?>
<ul class="list-unstyled">
<li style="margin-bottom: 0.5em;">
	<div class="checkbox">
	<label>
		<?php
		echo $this->makeInput(
			'checkbox',
			array(
				'id'	=> 'notify_customer',
				'value'	=> 1,
				)
			);
		?> <?php echo M('Customer'); ?>: <?php echo M('Notification'); ?>
	</label>
	</div>
</li>

<li>
	<?php
	echo $this->makePostParams('-current-', 'change' );
	?>
	<input class="btn btn-<?php echo $btn_status; ?>" type="submit" title="<?php echo M('Change'); ?>" value="<?php echo M('Change'); ?>">
</li>
</ul>
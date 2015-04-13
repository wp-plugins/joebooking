<?php
$app_info = ntsLib::getAppInfo();
global $NTS_CURRENT_USER;
$entries = ntsLib::getVar( 'admin/company/services::entries' );
$totalCols = 4;
?>
<table class="table table-condensed table-striped">

<?php if( count($entries) > 1 ) : ?>
<tr>
<th><?php echo M('Title'); ?></th>
<th><?php echo M('Duration'); ?></th>
<?php if( ! $NTS_CURRENT_USER->isPanelDisabled('admin/payments') ) : ?>
	<th><?php echo M('Price'); ?></th>
<?php endif; ?>

<?php if( count($entries) > 3 ) : ?>
<th><?php echo M('Show Order'); ?><br><span style="font-size: 0.8em; font-weight: normal;"><?php echo M('Smaller Goes First'); ?></span></th>
<?php else : ?>
<th>&nbsp;</th>
<?php endif; ?>

</tr>
<?php endif; ?>

<?php for( $ii = 0; $ii < count($entries); $ii++ ) : ?>
<?php 	
		$e = $entries[$ii];
?>
<tr>
<td>
<?php
echo ntsLink::printLink(
	array(
		'panel'		=> '-current-/../edit/edit',
		'params'	=> array('_id' => $e->getId()),
		'title'		=> ntsView::objectTitle($e),
		),
	true
	);
?>
</td>

<td>
<?php
$duration = $e->getProp('duration');
$lead_out = $e->getProp('lead_out');
$duration2 = $e->getProp('duration2');
$duration_view = ntsTime::formatPeriodShort($duration);
if( $duration2 ){
	$duration_view .= ' + ' . ntsTime::formatPeriodShort($duration2);
}
if( $lead_out ){
	$duration_view .= ' + ' . ntsTime::formatPeriodShort($lead_out);
}
?>
<?php echo $duration_view; ?>
</td>

<?php if( ! $NTS_CURRENT_USER->isPanelDisabled('admin/payments') ) : ?>
	<td>
	<?php echo ntsCurrency::formatServicePrice($e->getProp('price')); ?>
	</td>
<?php endif; ?>

<td>
<?php if( count($entries) > 3 ) : ?>

<?php
	echo $this->makeInput (
	/* type */
		'text',
	/* attributes */
		array(
			'id'		=> 'order_' . $e->getId(),
			'attr'		=> array(
				'size'	=> 2,
				),
			),
	/* validators */
		array(
			array(
				'code'		=> 'notEmpty.php', 
				'error'		=> M('Required'),
				),
			array(
				'code'		=> 'integer.php', 
				'error'		=> M('Numbers only'),
				),
			)
		);
?>

<?php elseif( count($entries) > 1 ) : ?>

<?php
echo ntsLink::printLink(
	array(
		'panel'		=> '-current-/../edit/edit',
		'action'	=> 'up',
		'params'	=> array('_id' => $e->getId()),
		'title'		=> M('Up'),
		'attr'		=> array(
			'class'	=> 'ok',
			),
		)
	);
?>

<?php
echo ntsLink::printLink(
	array(
		'panel'		=> '-current-/../edit/edit',
		'action'	=> 'down',
		'params'	=> array('_id' => $e->getId()),
		'title'		=> M('Down'),
		'attr'		=> array(
			'class'	=> 'ok',
			),
		)
	);
?>
<?php endif; ?>
</td>
</tr>

<?php endfor; ?>

<?php if( count($entries) > 3 ) : ?>
<tr>
<td colspan="<?php echo ($totalCols - 1); ?>"></td>
<td>
<?php echo $this->makePostParams('-current-', 'update'); ?>
<INPUT class="btn btn-default" TYPE="submit" VALUE="<?php echo M('Update'); ?>">
</td>
</tr>
<?php endif; ?>

</table>
<?php
$id = $this->getValue('id');
$params = array(
	'_id'	=> $id,
	);

$forwardTo = $this->getValue('forwardTo');
$forwardTo = trim( $forwardTo );
if( $forwardTo ){
	$params['forwardTo'] = $forwardTo;
	}
?>
<?php echo $this->makePostParams('-current-', 'delete', $params ); ?>
<input class="btn btn-danger" type="submit" VALUE="<?php echo M('Delete'); ?>">
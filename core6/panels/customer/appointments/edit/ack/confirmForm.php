<?php
$return = $this->getValue(NTS_PARAM_RETURN);
$params[NTS_PARAM_RETURN] = $return;

$id = $this->getValue('id');
$params['_id'] = $id;
?>
<p>
<?php echo $this->makePostParams('-current-', 'ack', $params ); ?>
<input type="submit" VALUE="<?php echo M('Acknowledge'); ?>"> <A HREF="javascript:history.go(-1);"><?php echo M('Go Back'); ?></A>

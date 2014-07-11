<p class="text-danger">
<?php echo M('Are you sure?'); ?>
<?php echo $this->makePostParams('-current-', 'confirm' ); ?>
<input type="submit" class="btn btn-danger" VALUE="<?php echo M('Delete'); ?>">
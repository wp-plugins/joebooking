<h2><?php echo M('Upgrade'); ?>: <?php echo M('OK'); ?></h2>
<h3><?php echo M('New Version'); ?>: <?php echo $NTS_VIEW['newVersion']; ?></h3>

<p>
<?php echo M('These upgrade scripts have been executed'); ?>:
<ol>
<?php foreach( $NTS_VIEW['runFiles'] as $rf ) : ?>
	<li><?php echo $rf; ?></li>
<?php endforeach; ?>
</ol>
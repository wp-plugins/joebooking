<h2><?php echo M('Upgrade'); ?>: <?php echo M('OK'); ?></h2>
<h3><?php echo $NTS_VIEW['newVersion']; ?></h3>

<ol>
<?php foreach( $NTS_VIEW['runFiles'] as $rf ) : ?>
	<li><?php echo $rf; ?></li>
<?php endforeach; ?>
</ol>
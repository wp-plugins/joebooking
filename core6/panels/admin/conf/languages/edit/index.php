<?php
$lm =& ntsLanguageManager::getInstance();
$lConf = $lm->getLanguageConf( $lang );
?>
<div class="page-header">
	<h2><?php echo M('Languages'); ?></h2>
</div>

<h3>
<?php echo $lang; ?> <small><?php echo $lConf['language']; ?>
</h3>

<div class="hc-page-status" data-src="<?php echo ntsLink::makeLink('-current-', '', array('what' => 'stats', 'language' => $lang)); ?>">
	<?php require( dirname(__FILE__) . '/_stats.php' ); ?>
</div>

<?php
$ii = 0;
?>
<ul class="list-unstyled list-separated">
<?php foreach( $all_strings as $k ) : ?>
	<?php
	$ii++;
	?>
	<li class="hc-target" data-src="<?php echo ntsLink::makeLink('-current-', '', array('id' => $ii, 'what' => 'one', 'language' => $lang)); ?>">
		<?php require( dirname(__FILE__) . '/_one.php' ); ?>
	</li>
<?php endforeach; ?>
</ul>

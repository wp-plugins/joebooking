<?php
$conf =& ntsConf::getInstance();
$firstTimeSplash = $conf->get('firstTimeSplash');
?>
<?php echo $firstTimeSplash; ?>
<p>
<a class="nts-splash-ok btn btn-default" href="<?php echo ntsLink::makeLink('-current-', 'ok' ); ?>"><?php echo M('OK'); ?></a>
</p>

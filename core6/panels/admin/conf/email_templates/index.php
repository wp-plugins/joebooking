<?php
$conf =& ntsConf::getInstance();
$currentlyDisabled = $conf->get( 'disabledNotifications' );
require( dirname(__FILE__) . '/_keys.php' );
?>
<style>
.ntsLinksContainer {
	padding: 0 0;
	}
.ntsLinksContainer li {
	margin: 0 0 0.5em 0;
	list-style-type: none;
	}
</style>
<span class="label label-success">x</span> Active <span class="label label-danger">+</span> Disabled

<p>
<h3><?php echo M('Appointments'); ?></h3>

<p>
<table>
<tr>
	<th><?php echo M('Customer'); ?></th>
	<th><?php echo M('Administrative User'); ?></th>
</tr>
<tr>
<td style="vertical-align: top;">
<ul class="ntsLinksContainer">
<?php foreach( $matrix['appointments']['customer'] as $keyArray ) : ?>
	<li>
	<?php if( in_array($keyArray[0], $currentlyDisabled) ) : ?>
		<a href="<?php echo ntsLink::makeLink('-current-/edit', 'activate', array('key' => $keyArray[0]) ); ?>" title="<?php echo M('Click to activate'); ?>"><span class="label label-danger">+</span></a>
	<?php else : ?>
		<a href="<?php echo ntsLink::makeLink('-current-/edit', 'disable', array('key' => $keyArray[0]) ); ?>" title="<?php echo M('Click to disable'); ?>"><span class="label label-success">x</span></a>
	<?php endif; ?>
	<a href="<?php echo ntsLink::makeLink('-current-/edit', '', array('lang' => $NTS_VIEW['lang'], 'key' => $keyArray[0]) ); ?>"><?php echo $keyArray[1]; ?></a>
	</li>
<?php endforeach; ?>
</ul>
</td>

<td style="vertical-align: top;">
<ul class="ntsLinksContainer">
<?php foreach( $matrix['appointments']['provider'] as $keyArray ) : ?>
	<li>
	<?php if( in_array($keyArray[0], $currentlyDisabled) ) : ?>
		<a href="<?php echo ntsLink::makeLink('-current-/edit', 'activate', array('key' => $keyArray[0]) ); ?>" title="<?php echo M('Click to activate'); ?>"><span class="label label-danger">+</span></a>
	<?php else : ?>
		<a href="<?php echo ntsLink::makeLink('-current-/edit', 'disable', array('key' => $keyArray[0]) ); ?>" title="<?php echo M('Click to disable'); ?>"><span class="label label-success">x</span></a>
	<?php endif; ?>
	<a href="<?php echo ntsLink::makeLink('-current-/edit', '', array('lang' => $NTS_VIEW['lang'], 'key' => $keyArray[0]) ); ?>"><?php echo $keyArray[1]; ?></a>
	</li>
<?php endforeach; ?>
</ul>
</td>
</table>

<p>
<h3><?php echo M('User Registration'); ?></h3>

<p>
<table>
<tr>
	<th><?php echo M('User'); ?></th>
	<th><?php echo M('Administrator'); ?></th>
</tr>
<tr>
<td style="vertical-align: top;">
<ul class="ntsLinksContainer">
<?php foreach( $matrix['user']['user'] as $keyArray ) : ?>
	<li>
	<?php if( in_array($keyArray[0], $currentlyDisabled) ) : ?>
		<a href="<?php echo ntsLink::makeLink('-current-/edit', 'activate', array('key' => $keyArray[0]) ); ?>" title="<?php echo M('Click to activate'); ?>"><span class="label label-danger">+</span></a>
	<?php else : ?>
		<a href="<?php echo ntsLink::makeLink('-current-/edit', 'disable', array('key' => $keyArray[0]) ); ?>" title="<?php echo M('Click to disable'); ?>"><span class="label label-success">x</span></a>
	<?php endif; ?>
	<a href="<?php echo ntsLink::makeLink('-current-/edit', '', array('lang' => $NTS_VIEW['lang'], 'key' => $keyArray[0]) ); ?>"><?php echo $keyArray[1]; ?></a>
	</li>
<?php endforeach; ?>
</ul>
</td>
<td style="vertical-align: top;">
<ul class="ntsLinksContainer">
<?php foreach( $matrix['user']['admin'] as $keyArray ) : ?>
	<li>
	<?php if( in_array($keyArray[0], $currentlyDisabled) ) : ?>
		<a href="<?php echo ntsLink::makeLink('-current-/edit', 'activate', array('key' => $keyArray[0]) ); ?>" title="<?php echo M('Click to activate'); ?>"><span class="label label-danger">+</span></a>
	<?php else : ?>
		<a href="<?php echo ntsLink::makeLink('-current-/edit', 'disable', array('key' => $keyArray[0]) ); ?>" title="<?php echo M('Click to disable'); ?>"><span class="label label-success">x</span></a>
	<?php endif; ?>
	<a href="<?php echo ntsLink::makeLink('-current-/edit', '', array('lang' => $NTS_VIEW['lang'], 'key' => $keyArray[0]) ); ?>"><?php echo $keyArray[1]; ?></a>
	</li>
<?php endforeach; ?>
</ul>
</td>
</table>
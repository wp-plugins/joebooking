<li class="dropdown">
	<a class="btn btn-default dropdown-toggle" data-toggle="dropdown" href="#">
		<?php if( 'calendar' == $display ) : ?>
			<i class="fa fa-calendar fa-fw"></i> 
			<?php if( 'month' == $range ) : ?>
				<?php echo M('Month'); ?>
			<?php elseif( 'week' == $range ) : ?>
				<?php echo M('Week'); ?>
			<?php elseif( in_array($range, array('day', 'dayloc')) ) : ?>
				<?php echo M('Day'); ?>
			<?php endif; ?>
		<?php elseif( 'browse' == $display ) : ?>
			<i class="fa fa-list fa-fw"></i> <?php echo M('List'); ?>
		<?php endif; ?>
		<span class="caret"></span>
	</a>

	<?php
	$dmenu = array();
	if( ('calendar' != $display) OR ('week' != $range) )
	{
		$dmenu[] = array(
			'href'	=> ntsLink::makeLink('-current-', '', array('display' => 'calendar', 'range' => 'week')),
			'title'	=> '<i class="fa fa-calendar"></i> ' . M('Week')
			);
	}

	if( ('calendar' != $display) OR ('month' != $range) )
	{
		$dmenu[] = array(
			'href'	=> ntsLink::makeLink('-current-', '', array('display' => 'calendar', 'range' => 'month')),
			'title'	=> '<i class="fa fa-calendar"></i> ' . M('Month')
			);
	}

	if( ('calendar' != $display) OR ( ! in_array($range, array('day', 'dayloc')) ) )
	{
		$dmenu[] = array(
			'href'	=> ntsLink::makeLink('-current-', '', array('display' => 'calendar', 'range' => 'day')),
			'title'	=> '<i class="fa fa-calendar"></i> ' . M('Day')
			);
	}

	if( 'browse' != $display )
	{
		$dmenu[] = '-divider-';
		$dmenu[] = array(
			'href'	=> ntsLink::makeLink('-current-', '', array('display' => 'browse')),
			'title'	=> '<i class="fa fa-list"></i> ' . M('List')
			);
	}
	?>
	<?php echo Hc_html::dropdown_menu($dmenu); ?> 
</li>
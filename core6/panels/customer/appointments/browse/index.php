<?php
global $NTS_CURRENT_USER;
$now = time();
$ff =& ntsFormFactory::getInstance();
$customerId = $NTS_CURRENT_USER->getId();
$ntsdb =& dbWrapper::getInstance();
$displayColumns = array();

$pgm =& ntsPaymentGatewaysManager::getInstance();
$has_online = $pgm->hasOnline();

$t = $NTS_VIEW['t'];

$conf =& ntsConf::getInstance();
$customerAcknowledge = $conf->get( 'customerAcknowledge' );
$canCancel = $conf->get('customerCanCancel');
$canReschedule = $conf->get('customerCanReschedule');
$canReschedule = 0;

$aam =& ntsAccountingAssetManager::getInstance();
$am =& ntsAccountingManager::getInstance();
$customer_balance = $am->get_balance( 'customer', $customerId );

if( ! NTS_SINGLE_LOCATION )
	$displayColumns[] = 'location';
if( ! NTS_SINGLE_RESOURCE )
	$displayColumns[] = 'resource';

/* check if this customer has orders or payments */
$paidAppsCount = 0;
$where = array(
	'customer_id'	=> array('=', $customerId)
	);
$orderCount = $ntsdb->count( 'orders', $where );
if( ! $orderCount )
{
	$where = array(
		'customer_id'	=> array( '=', $customerId ),
		'price'			=> array( '>', 0 ),
		);
	$paidAppsCount = $ntsdb->count( 'appointments', $where );
}
if( $paidAppsCount || $orderCount )
{
	$displayColumns[] = 'payment';
}

$datesShown = array();
?>

<ul class="nav nav-tabs">
	<li class="active">
	<?php if($NTS_VIEW['show'] == 'old') : ?>
		<a class="dropdown-toggle" data-toggle="dropdown" href="#">
			<?php echo M('Old'); ?> <b class="caret"></b>
		</a>
		<ul class="dropdown-menu">
			<li>
				<a href="<?php echo ntsLink::makeLink('-current-', '', array('show' => 'upcoming') ); ?>">
					<?php echo M('Upcoming'); ?>
				</a>
			</li>
		</ul>
	<?php else : ?>
		<a class="dropdown-toggle" data-toggle="dropdown" href="#">
			<?php echo M('Upcoming'); ?> <b class="caret"></b>
		</a>
		<ul class="dropdown-menu">
			<li>
				<a href="<?php echo ntsLink::makeLink('-current-', '', array('show' => 'old') ); ?>">
					<?php echo M('Old'); ?>
				</a>
			</li>
		</ul>
	<?php endif; ?>
	</li>

	<?php if( NTS_ENABLE_TIMEZONES > 0 ) : ?>
		<li>
			<a class="dropdown-toggle" data-toggle="dropdown" href="#">
				<i class="fa fa-fw fa-globe"></i> 
				<?php echo ntsTime::timezoneTitle($NTS_CURRENT_USER->getTimezone()); ?> <b class="caret"></b>
			</a>
			<ul class="dropdown-menu">
				<li>
					<span>
					<?php
					$formTimezoneParams = array(
						'tz'	=> $NTS_CURRENT_USER->getTimezone(),
						);
					$formTimezone =& $ff->makeForm( dirname(__FILE__) . '/formTimezone', $formTimezoneParams );
					$formTimezone->display();
					?>
					</span>
				</li>
			</ul>
		</li>
	<?php elseif( NTS_ENABLE_TIMEZONES < 0 ) : ?>

	<?php else : ?>
		<li>
			<span>
				<i class="fa fa-fw fa-globe"></i> 
				<?php echo ntsTime::timezoneTitle($NTS_CURRENT_USER->getTimezone()); ?>
			</span>
		</li>
	<?php endif; ?>

	<?php if( count($NTS_VIEW['entries']) ) : ?>
		<li>
			<?php 
			$overParams = array(
				'show'	=> $NTS_VIEW['show'],
				);
			?>
			<a class="dropdown-toggle" data-toggle="dropdown" href="#">
				<i class="fa fa-fw fa-download"></i> <?php echo M('Download'); ?> 
				<b class="caret"></b>
			</a>
			<ul class="dropdown-menu">
				<li>
					<?php $overParams['display'] = 'ical'; ?>
					<a href="<?php echo ntsLink::makeLink('-current-', 'export', $overParams ); ?>">iCal</a>
				</li>
				<li>
					<?php $overParams['display'] = 'excel'; ?>	
					<a href="<?php echo ntsLink::makeLink('-current-', 'export', $overParams ); ?>">Excel</a>
				</li>
			</ul>
		</li>
	<?php endif; ?>
</ul>


<?php if( ! count($NTS_VIEW['entries']) ) : ?>
	<p>
	<?php echo M('None'); return; ?>
	</p>
<?php endif; ?>

<ul class="list-unstyled">

<?php foreach( $NTS_VIEW['entries'] as $a ) : ?>
	<?php
	$t->setTimestamp( $a->getProp('starts_at') );
	$dateView = $t->formatDateFull();
	$dateView = $t->getMonthName() . ' ' . $t->getYear();
	?>
	<?php if( ! isset($datesShown[$dateView]) ) : ?>
		<li>
			<h3><?php echo $dateView; ?></h3>
		</li>
		<?php $datesShown[$dateView] = 1; ?>
	<?php endif; ?>

	<?php require( dirname(__FILE__) . '/_index_one.php' ); ?>

<?php endforeach; ?>
</ul>
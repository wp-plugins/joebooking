<?php
global $NTS_CURRENT_USER;
$customerId = $NTS_CURRENT_USER->getId();
$ntsdb =& dbWrapper::getInstance();

$current_panel = $_NTS['CURRENT_PANEL'];
$active_link = '';
$actives = array(
	'customer/book'			=> 'book',
	'customer/packs'		=> 'packs',
	'customer/profile'		=> 'profile',
	'customer/appointments'	=> 'appointments',
	'customer/invoices'		=> 'invoices',
	'customer/accounting'	=> 'accounting',
	);
foreach( $actives as $ak => $av )
{
	if( substr($current_panel, 0, strlen($ak)) == $ak )
	{
		$active_link = $av;
		break;
	}
}

$links = array();
$links['book'] = array(
	ntsLink::makeLink(
		'customer/book',
		'',
		array(
			'location'	=> '-reset-',
			'resource'	=> '-reset-',
			'service'	=> '-reset-',
			'cal'		=> '-reset-',
			'time'		=> '-reset-',
			)
		),
	'<i class="fa fa-calendar"></i> ' . M('Schedule Now')
	);

$packs_count = $ntsdb->count( 
	'packs',
	array(
		'price' => array('>', 0)
		)
	);
$pgm =& ntsPaymentGatewaysManager::getInstance();
$has_online = $pgm->hasOnline();

if( $packs_count && $has_online )
{
	$links['packs'] = array(
		ntsLink::makeLink('customer/packs'),
		'<i class="fa fa-shopping-cart"></i> ' . M('Purchase')
		);
}
$links['divider1'] = '';

$links['profile'] = array(
	ntsLink::makeLink('customer/profile'),
	'<i class="fa fa-user"></i>' . ' ' . $NTS_CURRENT_USER->getProp('first_name') . ' ' . $NTS_CURRENT_USER->getProp('last_name')
	);

$current_user =& ntsLib::getCurrentUser();
$current_user_id = $current_user->getId();

$customer_links = array();

$customer_links['appointments'] = array(
	ntsLink::makeLink('customer/appointments/view', '', array('ref' => '-reset-')),
	'<i class="fa fa-check-square-o"></i> ' . M('Appointments')
	);

/* check if customer has invoices */
$pm =& ntsPaymentManager::getInstance();
$ids = $pm->getInvoicesOfCustomer( $current_user_id );
if( $ids )
{
	$customer_links['invoices'] = array(
		ntsLink::makeLink('customer/invoices/browse'),
		'<i class="fa fa-file-text-o"></i> ' . M('Invoices')
		);
}
/* if balance */
$am =& ntsAccountingManager::getInstance();
$entries = $am->get_postings( 'customer', $current_user_id );
if( $entries )
{
	$customer_links['balance'] = array(
		ntsLink::makeLink('customer/accounting'),
		'<i class="fa fa-suitcase"></i> ' . M('Balance')
		);
}
$customer_links['profile'] = array(
	ntsLink::makeLink('customer/profile'),
	'<i class="fa fa-user"></i>' . ' ' . M('Profile')
	);
$links['profile'][2] = $customer_links;

$links['logout'] = array(
	ntsLink::makeLink('user/logout'),
	'<i class="fa fa-sign-out"></i> ' . M('Logout')
	);
?>
<?php foreach( $links as $lk => $l ) : ?>
	<?php if( is_array($l) ) : ?>
		<?php if( $active_link == $lk ) : ?>
			<li class="active">
		<?php else : ?>
			<li>
		<?php endif; ?>
			<?php
			list( $link_title, $link_icon ) = Hc_lib::parse_icon( $l[1] );
			?>
			<?php if( isset($l[2]) ) : ?>
				<a href="#" title="<?php echo $link_title; ?>" class="dropdown-toggle squeeze-in" data-toggle="dropdown">
					<ul class="list-inline">
						<li>
						<?php echo $link_icon; ?>
						</li>
						<li>
							<span class="hidden-xs">
							<?php echo $link_title; ?>
							</span>
						</li>
						<li>
							<span class="caret"></span>
						</li>
					</ul>
				</a>
				<ul class="dropdown-menu">
					<?php foreach( $l[2] as $l2 ) : ?>
						<?php
						list( $link_title, $link_icon ) = Hc_lib::parse_icon( $l2[1] );
						?>
						<li>
							<a href="<?php echo $l2[0]; ?>" title="<?php echo $link_title; ?>">
								<?php echo $link_icon; ?><?php echo $link_title; ?>
							</a>
						</li>
					<?php endforeach; ?>
				</ul>
			<?php else : ?>
				<a href="<?php echo $l[0]; ?>" class="squeeze-in" title="<?php echo $link_title; ?>">
					<ul class="list-inline">
						<li>
						<?php echo $link_icon; ?>
						</li>
						<li>
							<span class="hidden-xs">
							<?php echo $link_title; ?>
							</span>
						</li>
					</ul>
				</a>
			<?php endif; ?>
		</li>
	<?php else : ?>
		<li class="divider">&nbsp;</li>
	<?php endif; ?>
<?php endforeach; ?>

<?php if( file_exists(NTS_EXTENSIONS_DIR . '/more-links-customer.php') ) : ?>
<?php	require(NTS_EXTENSIONS_DIR . '/more-links-customer.php'); ?>
<?php endif; ?>
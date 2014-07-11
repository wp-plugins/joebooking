<?php
$current_panel = $_NTS['CURRENT_PANEL'];
$active_link = '';
$actives = array(
	'anon/login'		=> 'login',
	'anon/register'		=> 'register',
	'customer/book'		=> 'book',
	'customer/packs'	=> 'packs'
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

$packs_count = $ntsdb->count( 'packs', array('price', array('>', 0)) );
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
$links['login'] = array(
	ntsLink::makeLink('anon/login'),
	'<i class="fa fa-sign-in"></i> ' . M('Login')
	);
if( NTS_ENABLE_REGISTRATION )
{
	$links['register'] = array(
		ntsLink::makeLink('anon/register'),
		'<i class="fa fa-pencil"></i> ' . M('Register')
		);
}
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
			<a href="<?php echo $l[0]; ?>">
				<?php echo $link_icon; ?><span class="hidden-xs"><?php echo $link_title; ?></span>
			</a>
		</li>
	<?php else : ?>
		<li class="divider">&nbsp;</li>
	<?php endif; ?>
<?php endforeach; ?>

<?php if( file_exists(NTS_EXTENSIONS_DIR . '/more-links-customer.php') ) : ?>
<?php	require(NTS_EXTENSIONS_DIR . '/more-links-customer.php'); ?>
<?php endif; ?>

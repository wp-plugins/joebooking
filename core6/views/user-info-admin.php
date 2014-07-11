<?php
global $NTS_CURRENT_USER;
$customerId = $NTS_CURRENT_USER->getId();
$ntsdb =& dbWrapper::getInstance();

$current_panel = $_NTS['CURRENT_PANEL'];
$active_link = '';
$actives = array(
	'admin/profile'		=> 'profile',
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
if( preg_match('/^admin/', $current_panel) )
{
	$links['book'] = array(
		ntsLink::makeLink('customer/book'),
		'<i class="fa fa-calendar"></i> ' . M('Frontend View')
		);
}
else
{
	$links['area'] = array(
		ntsLink::makeLink('admin'),
		'<i class="fa fa-cog"></i> ' . M('Admin Area')
		);
}

$userFullName = trim( ntsView::objectTitle($NTS_CURRENT_USER) );
$userTitle = '<strong>' . $NTS_CURRENT_USER->getProp('username') . '</strong>';
if( $userFullName )
{
	$userTitle .= ' (' . $userFullName . ')';
}

$links['divider2'] = '';
$links['profile'] = array(
	ntsLink::makeLink('admin/profile'),
	'<i class="fa fa-user"></i>' . ' ' . $userTitle
	);
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
			<a href="<?php echo $l[0]; ?>" title="<?php echo $link_title; ?>">
				<?php echo $link_icon; ?><span class="hidden-xs"><?php echo $link_title; ?></span>
			</a>
		</li>
	<?php else : ?>
		<li class="divider">&nbsp;</li>
	<?php endif; ?>
<?php endforeach; ?>
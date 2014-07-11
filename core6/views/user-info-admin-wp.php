<?php
$current_panel = $_NTS['CURRENT_PANEL'];

$links = array();
if( preg_match('/^admin/', $current_panel) )
{
	if( $this->pages )
	{
		$front_url = $GLOBALS['NTS_CONFIG'][$this->app]['FRONTEND_WEBPAGE'];

		$links['book'] = array(
			$front_url,
			'<i class="fa fa-calendar"></i> ' . M('Frontend View')
			);
	}
}
else
{
	$admin_link = get_admin_url() . 'admin.php?page=' . $this->slug;
	$links['area'] = array(
		$admin_link,
		'<i class="fa fa-cog"></i> ' . M('Admin Area')
		);
}
?>
<?php foreach( $links as $lk => $l ) : ?>
	<?php if( is_array($l) ) : ?>
		<li>
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
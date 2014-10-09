<?php
$current_panel = $_NTS['CURRENT_PANEL'];

$links = array();
if( preg_match('/^admin/', $current_panel) )
{
	if( $this->pages )
	{
		if( count($this->pages) > 1 )
		{
			$links['book'] = array(
				'<i class="fa fa-calendar"></i> ' . M('Frontend View'),
				);

			foreach( $this->pages as $p )
			{
				$links['book'][] = array(
					get_permalink($p),
					get_the_title($p)
					);
			}
		}
		else
		{
			$front_url = $GLOBALS['NTS_CONFIG'][$this->app]['FRONTEND_WEBPAGE'];
			$links['book'] = array(
				$front_url,
				'<i class="fa fa-calendar"></i> ' . M('Frontend View')
				);
		}
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
		<?php if( isset($l[1]) && is_array($l[1]) ) : ?>
			<?php
			$l1 = array_shift($l);
			list( $link_title, $link_icon ) = Hc_lib::parse_icon( $l1 );
			?>
			<li class="dropdown">
				<a class="dropdown-toggle" data-toggle="dropdown" href="#" title="<?php echo $link_title; ?>">
					<?php echo $link_icon; ?><span class="hidden-xs"><?php echo $link_title; ?></span> <b class="caret"></b>
				</a>
				<ul class="dropdown-menu">
				<?php foreach( $l as $l2 ) : ?>
					<li>
						<?php
						list( $link_title, $link_icon ) = Hc_lib::parse_icon( $l2[1] );
						?>
						<a href="<?php echo $l2[0]; ?>" title="<?php echo $link_title; ?>">
							<?php echo $link_icon; ?><span class="hidden-xs"><?php echo $link_title; ?></span>
						</a>
					</li>
				<?php endforeach; ?>
				</ul>
			</li>
		<?php else : ?>
			<li>
				<?php
				list( $link_title, $link_icon ) = Hc_lib::parse_icon( $l[1] );
				?>
				<a href="<?php echo $l[0]; ?>" title="<?php echo $link_title; ?>">
					<?php echo $link_icon; ?><span class="hidden-xs"><?php echo $link_title; ?></span>
				</a>
			</li>
		<?php endif; ?>
	<?php else : ?>
		<li class="divider">&nbsp;</li>
	<?php endif; ?>
<?php endforeach; ?>
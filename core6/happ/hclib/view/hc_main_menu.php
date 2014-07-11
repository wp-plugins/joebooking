<?php
if( ! $menu )
	return;
?>

<div class="navbar navbar-default">
	<div class="navbar-header">
		<button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".nts-navbar-collapse">
			<span class="sr-only">Toggle navigation</span>
			<span class="fa fa-bar"></span>
			<span class="fa fa-bar"></span>
			<span class="fa fa-bar"></span>
		</button>
	</div>

	<div class="collapse navbar-collapse nts-navbar-collapse">
		<ul class="nav navbar-nav">
		<?php foreach( $menu as $m ) : ?>
			<?php
			$current_one = ( isset($m['active']) && $m['active'] ) ? TRUE : FALSE;
			$li_class = array();
			if( isset($m['children']) && $m['children'] )
				$li_class[] = 'dropdown';
			if( $current_one )
				$li_class[] = 'active';
			$li_class = join( ' ', $li_class );
			list( $link_title, $link_icon ) = Hc_lib::parse_icon( $m['title'] );
			?>

			<li class="<?php echo $li_class; ?>">

			<?php if( isset($m['children']) && $m['children'] ) : ?>

				<a class="dropdown-toggle" data-toggle="dropdown" href="#" title="<?php echo $link_title; ?>">
					<?php echo $link_icon; ?><?php echo $link_title; ?><b class="caret"></b>
				</a>

				<?php echo Hc_html::dropdown_menu($m['children']); ?> 

			<?php else : ?>

				<?php if( isset($m['external']) && $m['external'] ) : ?>
					<a target="_blank" href="<?php echo $m['link']; ?>" title="<?php echo $link_title; ?>">
						<span class="alert alert-success-o">
							<?php echo $link_title; ?>
						</span>
					</a>
				<?php else : ?>
					<a href="<?php echo $m['href']; ?>" title="<?php echo $link_title; ?>">
						<?php echo $link_icon; ?><?php echo $link_title; ?>
					</a>
				<?php endif; ?>

			<?php endif; ?>
			</li>
		<?php endforeach; ?>
		</ul>
	</div>
</div>

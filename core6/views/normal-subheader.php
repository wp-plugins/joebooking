<?php
$subHeaderContent = '';
if( isset($NTS_VIEW['subHeaderFile']) && $NTS_VIEW['subHeaderFile'] )
{
	ob_start();
	require( $NTS_VIEW['subHeaderFile'] );
	$subHeaderContent = ob_get_contents();
	ob_end_clean();
	$subHeaderContent = trim( $subHeaderContent );
}
?>
<?php if( strlen($subHeaderContent) ) : ?>
	<div class="page-header">
		<?php	echo $subHeaderContent; ?>
	</div>
<?php endif; ?>

<?php
// check if we have to show this menu
$showMenu3 = FALSE;
if( $NTS_VIEW['menu3'] )
{
	foreach( $NTS_VIEW['menu3'] as $m )
	{
		$currentOne = FALSE;
		if(
			( isset($m['panel']) && ($m['panel'] == substr($_NTS['CURRENT_PANEL'], 0, strlen($m['panel']))) )
			){
			$currentOne = TRUE;
			}
		if( $currentOne )
		{
			$showMenu3 = TRUE;
			break;
		}
	}
}
?>

<?php if( $NTS_VIEW['menu3'] && $showMenu3 ) : ?>
	<ul class="nav nav-tabs">
	<?php foreach( $NTS_VIEW['menu3'] as $m ) : ?>
		<?php if( is_array($m) && isset($m[0]) ) : ?>
			<?php
			$currentOne = FALSE;
			if(
				( isset($m['panel']) && ($m['panel'] == substr($_NTS['CURRENT_PANEL'], 0, strlen($m['panel']))) )
				){
				$currentOne = TRUE;
				}
			if( ! $currentOne )
				unset($m[0]);
			?>
		<?php endif; ?>

		<?php if( is_array($m) && isset($m[0]) ) : ?>
			<!-- dropdown -->
			<?php
			$currentOne = FALSE;
			if(
				( isset($m['panel']) && ($m['panel'] == substr($_NTS['CURRENT_PANEL'], 0, strlen($m['panel']))) )
				){
				$currentOne = TRUE;
				}

			$class = array();
			$class[] = 'dropdown';
			if( $currentOne )
				$class[] = 'active';
			$class = join( ' ', $class );
			reset( $m );
			?>
			<li class="<?php echo $class; ?>">
				<?php
				list( $linkTitle, $linkIcon ) = Hc_lib::parse_icon( $m['title'] );
				?>
				<a class="dropdown-toggle" data-toggle="dropdown" href="#">
					<?php echo $linkIcon; ?><span class="hidden-xs"><?php echo $linkTitle; ?></span> <span class="caret"></span>
				</a>

				<ul class="dropdown-menu">
				<?php foreach( $m[0] as $mk => $m2 ) : ?>
					<?php
					if( ! is_numeric($mk) )
						continue;
//					if( ! is_array($m2) )
//						continue;

					if( isset($m2['href']) )
					{
						$link = $m2['href'];
					}
					else
					{
						if( ! isset($m2['params']) )
							$m2['params'] = array();
						$link = ntsLink::makeLink( $m2['panel'], '', $m2['params'], false );
					}
					$currentOne = FALSE;
					if( 
						(
							isset($m2['panel']) &&
							($m2['panel'] == substr($_NTS['CURRENT_PANEL'], 0, strlen($m2['panel'])))
						)
						){
						$currentOne = TRUE;
						}
					$class = array();
					$linkClass = array();
					if( $currentOne )
						$class[] = 'active';
					if( isset($m2['alert']) && $m2['alert'] )
						$linkClass[] = 'text-danger';

					$nts_ajax_loader = FALSE;
					if( isset($m2['link-class']) && $m2['link-class'] )
					{
						$linkClass[] = $m2['link-class'];
					}

					$data_attr = array();
					$linkClass = join( ' ', $linkClass );
					if( preg_match('/nts\-ajax\-loader/', $linkClass) )
					{
						$nts_ajax_loader = TRUE;
						$class[] = 'nts-ajax-parent';
					}
					elseif( preg_match('/hc\-ajax\-loader/', $linkClass) )
					{
						$class[] = 'hc-ajax-parent';
						if( isset($m2['data-attr']) )
						{
							foreach( $m2['data-attr'] as $k => $v )
							{
								$data_attr['data-' . $k] = $v;
							}
						}
					}
					$class = join( ' ', $class );
					list( $linkTitle, $linkIcon ) = Hc_lib::parse_icon( $m2['title'] );

					$data_attr_string = '';
					if( $data_attr )
					{
						reset( $data_attr );
						foreach( $data_attr as $k => $v )
						{
							$data_attr_string .= ' ' . $k . '="' . $v . '"';
						}
					}
					?>
					<li class="<?php echo $class; ?>"<?php echo $data_attr_string; ?>>
						<a class="<?php echo $linkClass; ?>" href="<?php echo $link; ?>" title="<?php echo $linkTitle; ?>">
							<?php echo $linkIcon; ?><?php echo $linkTitle; ?>
						</a>
						<?php if( $nts_ajax_loader ) : ?>
							<div class="nts-ajax-container" style="padding: 0.5em 1em;"></div>
						<?php endif; ?>
					</li>
				<?php endforeach; ?>
				</ul>
			</li>

		<?php else : ?>

			<?php
				$link = ntsLink::makeLink( $m['panel'], '', $m['params'], false );
				$currentOne = false;
				if(
					($m['panel'] == substr($_NTS['CURRENT_PANEL'], 0, strlen($m['panel'])) )
					){
					$currentOne = true;
					}
				$class = array();
				$linkClass = array();
				if( $currentOne )
					$class[] = 'active';
				if( $m['alert'] )
				{
					$class[] = 'text-danger';
					$linkClass[] = 'text-danger';
				}
				if( $m['link-class'] )
				{
					$linkClass[] = $m['link-class'];
				}

				$class = join( ' ', $class );
				$linkClass = join( ' ', $linkClass );

				list( $linkTitle, $linkIcon ) = Hc_lib::parse_icon( $m['title'] );
			?>
			<li class="<?php echo $class; ?>">
				<a class="<?php echo $linkClass; ?>" href="<?php echo $link; ?>" title="<?php echo $linkTitle; ?>">
					<span class="<?php echo $linkClass; ?>"><?php echo $linkIcon; ?><span class="hidden-xs"><?php echo $linkTitle; ?></span></span>
				</a>
			</li>

		<?php endif; ?>
	<?php endforeach; ?>
	</ul>
<?php endif; ?>

<!-- ANNOUNCE IF ANY -->
<?php if( ntsView::isAnnounce() ) : ?>
	<?php $text = ntsView::getAnnounceText();	?>
	<?php foreach( $text as $t ) : ?>
	<?php if( $t[1] == 'error' ) : ?>
		<div class="alert alert-danger">
	<?php elseif( $t[1] == 'info' ) : ?>
		<div class="alert alert-warning-o">
	<?php else : ?>
		<div class="alert alert-success">
	<?php endif; ?>
		<?php echo $t[0]; ?>
		</div>
	<?php endforeach; ?>
	<?php ntsView::clearAnnounce(); ?>
<?php endif; ?>

<!-- DISPLAY PAGE -->
<?php
if( isset($NTS_VIEW['output']) )
{
	echo $NTS_VIEW['output'];
}
else
{
	if( file_exists($NTS_VIEW['displayFile']) )
		require( $NTS_VIEW['displayFile'] );
}
?>
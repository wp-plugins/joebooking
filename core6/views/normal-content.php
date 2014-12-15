<?php
global $NTS_MENU, $_NTS, $NTS_VIEW;
$ri = ntsLib::remoteIntegration();
$i_inside = isset($NTS_VIEW['isInside']) ? $NTS_VIEW['isInside'] : FALSE;
$container = ($ri OR $i_inside) ? 'container-fluid' : 'container';
?>

<div id="nts">
<div class="<?php echo $container; ?>">

<?php if( ntsView::isAdminAnnounce() ) : ?>
	<?php 
	$text = ntsView::getAdminAnnounceText();
	if( ! is_array($text) )
		$text = array( $text );
	?>
	<?php foreach( $text as $t ) : ?>
		<?php if( $t[1] == 'ok' ) : ?>
			<div class="alert alert-info">
		<?php elseif( $t[1] == 'info' ) : ?>
			<div class="alert alert-warning-o">
		<?php else : ?>
			<div class="alert alert-danger">
		<?php endif; ?>
		<?php echo $t[0]; ?>
		</div>
	<?php endforeach; ?>
	<?php ntsView::clearAdminAnnounce(); ?>
<?php endif; ?>

<?php if( preg_match('/^admin/', $_NTS['CURRENT_PANEL']) ) : ?>
<?php	require( dirname(__FILE__) . '/admin-header.php' ); ?>
<?php endif; ?>

<!-- USER ACCOUNT INFO  -->
<?php
$t = isset($NTS_VIEW['t']) ? $NTS_VIEW['t'] : new ntsTime;
$t->setNow();
?>
<div>
	<?php require( dirname(__FILE__) . '/user-info.php' ); ?>
</div>

<!-- MENU  -->
<?php
if( isset($_NTS['ROOT_INFO']) && $_NTS['ROOT_INFO'] && isset($_NTS['ROOT_INFO'][1]) )
{
	$menu_root = $_NTS['ROOT_INFO'][1];
	$current_user = ntsLib::getCurrentUser();
	if( ! $current_user->getId() )
	{
		$menu_root = 'anon';
	}

	$menu = HC_Html_Factory::widget('main_menu');
	$menu->set_engine( 'nts' );
	$menu->set_menu( $NTS_MENU );
	$menu->set_current( $_NTS['CURRENT_PANEL'] );

	$disabled_panels = $current_user->getDisabledPanels();
	if( $disabled_panels )
	{
		$menu->set_disabled( $disabled_panels );
	}
	echo $menu->render( $menu_root . '/' );
}
?>

<?php if( $NTS_VIEW['menu1'] ) : ?>
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
			<?php foreach( $NTS_VIEW['menu1'] as $k => $m ) : ?>
				<?php
				$currentOne = FALSE;
				$menuPanel = isset($m['panel']) ? $m['panel'] : $k;
				if(
					( 
						($menuPanel == substr($_NTS['CURRENT_PANEL'], 0, strlen($menuPanel))) && 
						( (strlen($_NTS['CURRENT_PANEL']) == $menuPanel) OR (substr($_NTS['CURRENT_PANEL'], strlen($menuPanel), 1) == '/') ) 
					)
					OR
					( ($k == substr($_NTS['CURRENT_PANEL'], 0, strlen($k))) && ( (strlen($_NTS['CURRENT_PANEL']) == $k) || (substr($_NTS['CURRENT_PANEL'], strlen($k), 1) == '/') ) )
					OR
					( $menuPanel == $_NTS['CURRENT_PANEL'] )
					){
					$currentOne = TRUE;
					}
				$link = ntsLink::makeLink($menuPanel, '', array(), FALSE, TRUE);
				$liClass = array();
				if( $currentOne )
					$liClass[] = 'active';
				if( $m['children'] )
					$liClass[] = 'dropdown';
				$liClass = join( ' ', $liClass );

				list( $linkTitle, $linkIcon ) = Hc_lib::parse_icon( $m['title'] );
				?>
				<li class="<?php echo $liClass; ?>">
					<?php if( ! $m['children'] ) : ?>
						<a href="<?php echo $link; ?>" title="<?php echo $linkTitle; ?>">
							<?php echo $linkIcon; ?><?php echo $linkTitle; ?>
						</a>
					<?php else : ?>
						<a class="dropdown-toggle" data-toggle="dropdown" href="#" title="<?php echo $linkTitle; ?>">
							<?php echo $linkIcon; ?><?php echo $linkTitle; ?><b class="caret"></b>
						</a>

						<ul class="dropdown-menu">
						<?php foreach( $m['children'] as $k2 => $m2 ) : ?>
							<?php
							$menuPanel2 = isset($m2['panel']) ? $m2['panel'] : $k2;
							$link2 = ntsLink::makeLink($menuPanel2, '', array(), FALSE, TRUE);
							list( $linkTitle, $linkIcon ) = Hc_lib::parse_icon( $m2['title'] );
							?>
							<?php if( $linkTitle == '-divider-' ) : ?>
								<li class="divider"></li>
							<?php else : ?>
								<li>
									<a href="<?php echo $link2; ?>" title="<?php echo $linkTitle; ?>">
										<?php echo $linkIcon; ?><?php echo $linkTitle; ?>
									</a>
								</li>
							<?php endif; ?>
						<?php endforeach; ?>
						</ul>
					<?php endif; ?>
				</li>
			<?php endforeach; ?>
			</ul>
		</div>

	</div>
<?php endif; ?>

<?php 
	if(
		(! ( isset($NTS_VIEW[NTS_PARAM_VIEW_RICH]) && ($NTS_VIEW[NTS_PARAM_VIEW_RICH] == 'basic'))) &&
		isset($NTS_VIEW['subHeaderFile']) && 
		$NTS_VIEW['subHeaderFile'] && 
		file_exists($NTS_VIEW['subHeaderFile'])
		) : 
?>
	<?php require( dirname(__FILE__) . '/normal-subheader.php'); ?>
<?php else : ?>

<!-- ANNOUNCE IF ANY -->
<?php 	if( ntsView::isAnnounce() ) : ?>
	<?php $text = ntsView::getAnnounceText();	?>
	<?php foreach( $text as $t ) : ?>
	<?php if( $t[1] == 'error' ) : ?>
		<div class="alert alert-danger">
	<?php else : ?>
		<div class="alert alert-success">
	<?php endif; ?>
		<?php echo $t[0]; ?>
		</div>
	<?php endforeach; ?>
	<?php ntsView::clearAnnounce(); ?>
<?php 	endif; ?>

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
<?php endif; ?>

<?php if( isset($NTS_VIEW['systemFooterFile']) && file_exists($NTS_VIEW['systemFooterFile']) ) : ?>
	<?php require( $NTS_VIEW['systemFooterFile'] ); ?>
<?php endif; ?>

</div>
</div><!-- end of #nts -->

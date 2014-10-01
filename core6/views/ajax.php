<?php
if( ob_get_contents() )
{
	ob_end_clean();
}
global $NTS_VIEW;
?>
<?php /* ?>
<p>
<i><?php echo $_SERVER["REQUEST_URI"]; ?></i><br><br>
<?php */ ?>

<?php 
$showMenu3 = FALSE;
if( 
	$NTS_VIEW['menu3'] &&
	(! ( isset($NTS_VIEW[NTS_PARAM_VIEW_RICH]) && ($NTS_VIEW[NTS_PARAM_VIEW_RICH] == 'basic')))
	)
{
	$showMenu3 = TRUE;
}
$showMenu3 = FALSE;

if( $showMenu3 )
{
	$showMenu3 = FALSE;
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
<?php if( $showMenu3 ) : ?>
	<?php	require( $NTS_VIEW['subHeaderFile'] ); ?>

	<p>
	<ul class="nav nav-tabs">
	<?php foreach( $NTS_VIEW['menu3'] as $m ) : ?>
		<?php
			$link = ntsLink::makeLink( $m['panel'], '', $m['params'], false );
			$currentOne = false;
			if( 
				($m['panel'] == substr($_NTS['CURRENT_PANEL'], 0, strlen($m['panel'])) ) ||
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
				<span class="<?php echo $linkClass; ?>"><?php echo $linkIcon; ?><?php echo $linkTitle; ?></span>
			</a>
		</li>
	<?php endforeach; ?>
	</ul>
<?php endif; ?>

<?php if( ntsView::isAnnounce() ) : ?>
	<?php $text = ntsView::getAnnounceText();	?>
	<?php foreach( $text as $t ) : ?>
	<?php if( $t[1] == 'error' ) : ?>
		<div class="alert alert-danger alert-condensed2">
	<?php else : ?>
		<div class="alert alert-success alert-condensed2">
	<?php endif; ?>
		<?php echo $t[0]; ?>
		</div>
	<?php endforeach; ?>
	<?php ntsView::clearAnnounce(); ?>
<?php endif; ?>

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
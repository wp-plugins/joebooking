<?php
if( isset($custom_translate[$k]) )
{
	$v = $custom_translate[$k];
	$class = 'success';
}
elseif( isset($file_translate[$k]) )
{
	$v = $file_translate[$k];
	$class = 'info';
}
else
{
	$v = $k;
	$class = 'danger';
}
$edit_link = ntsLink::makeLink( 
	'-current-',
	'',
	array(
		'id' 		=> $ii,
		'language'	=> $lang,
		'what'		=> 'edit',
		)
	);
?>
<div class="hc-ajax-parent panel panel-<?php echo $class; ?>">
	<div class="panel-heading">
		<ul class="list-unstyled">
			<?php if( ($lang != 'en') OR isset($custom_translate[$k]) ) : ?>
				<li class="text-italic">
					<?php echo $k; ?>
				</li>
			<?php endif; ?>
			<li>
				<a href="<?php echo $edit_link; ?>" class="hc-ajax-loader hc-ajax-scroll" title="<?php echo M('Edit'); ?>"><?php echo $v; ?></a>
			</li>
		</ul>
	</div>
	<div class="panel-body hc-ajax-container" style="display: none;"></div>
</div>

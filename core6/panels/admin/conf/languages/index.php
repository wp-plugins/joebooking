<?php
/* current language */
$conf =& ntsConf::getInstance();
$activeLanguages = $conf->get('languages');

/* language manager */
$lm =& ntsLanguageManager::getInstance();
$activeLanguages = $lm->getActiveLanguages();
$languages = $lm->getLanguages();

$template_lang_conf = $lm->getLanguageConf( 'languageTemplate' );
$all_strings = array_keys( $template_lang_conf['interface'] );

$active = array();
$available = array();

reset( $languages );
foreach( $languages as $l )
{
	if( in_array($l, $activeLanguages) )
		$active[] = $l;
	else
		$available[] = $l;
}

/* default language  */
$defaultLanguageConf = $lm->getLanguageConf( 'languageTemplate' );
?>

<div class="page-header">
	<h2><?php echo M('Languages'); ?></h2>
</div>

<h3><?php echo M('Active'); ?></h3>

<ul class="list-unstyled list-separated">
<?php foreach( $active as $lang ) : ?>
	<?php $lConf = $lm->getLanguageConf( $lang );	?>
	<li class="alert alert-archive-o">
		<div class="row">
			<div class="col-md-2">
				<h4><?php echo $lang; ?><br><small><?php echo $lConf['language']; ?></small></h4>
			</div>

			<div class="col-md-4">
				<ul class="list-inline">
					<?php if( count($active) > 1 ) : ?>
						<li>
							<a class="btn btn-danger-o" href="<?php echo ntsLink::makeLink('admin/conf/languages', 'disable', array('language' => $lang) ); ?>">
								<?php echo M('Disable'); ?>
							</a>
						</li>
					<?php endif; ?>
					<li>
						<a class="btn btn-default" href="<?php echo ntsLink::makeLink('admin/conf/languages/edit', '', array('language' => $lang) ); ?>"><?php echo M('Edit'); ?></a>
					</li>
				</ul>
			</div>

			<div class="col-md-6 text-right">
				<?php
				$this_lang_conf = $lm->getLanguageConf( $lang );
				$file_translate = $this_lang_conf['interface'];
				$custom_translate = $lm->get_custom( $lang );
				?>
				<?php require( dirname(__FILE__) . '/edit/_stats.php' ); ?>
			</div>
		</div>
	</li>
<?php endforeach; ?>
</ul>

<?php if( count($available) > 0 ) : ?>
	<h3><?php echo M('Available'); ?></h3>

	<ul class="list-unstyled list-separated">

	<?php foreach( $available as $lang ) : ?>
		<li class="alert alert-archive-o">
			<div class="row">
				<?php $lConf = $lm->getLanguageConf( $lang ); ?>
				<?php if( $lConf['error'] ) : ?>
					<b><?php echo $lang; ?></b><br>
					<?php echo M('Status'); ?>: <b>XML File Error</b><br>
					<i><?php echo $lConf['error']; ?></i>
				<?php else : ?>
					<div class="col-md-2">
						<h4><?php echo $lang; ?><br><small><?php echo $lConf['language']; ?></small></h4>
					</div>

					<div class="col-md-4">
						<ul class="list-inline">
							<li>
								<a class="btn btn-success-o" href="<?php echo ntsLink::makeLink('admin/conf/languages', 'activate', array('language' => $lang) ); ?>"><?php echo M('Activate'); ?></a>
							</li>
							<li>
								<a class="btn btn-default" href="<?php echo ntsLink::makeLink('admin/conf/languages/edit', '', array('language' => $lang) ); ?>"><?php echo M('Edit'); ?></a>
							</li>
						</ul>
					</div>

					<div class="col-md-6 text-right">
						<?php
						$this_lang_conf = $lm->getLanguageConf( $lang );
						$file_translate = $this_lang_conf['interface'];
						$custom_translate = $lm->get_custom( $lang );
						?>
						<?php require( dirname(__FILE__) . '/edit/_stats.php' ); ?>
					</div>
				<?php endif; ?>
			</div>
		</li>
	<?php endforeach; ?>
	</ul>
<?php endif; ?>
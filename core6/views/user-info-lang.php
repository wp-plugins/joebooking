<?php
$lm =& ntsLanguageManager::getInstance();
$languages = $lm->getActiveLanguages();
$currentLanguage = $NTS_CURRENT_USER->getLanguage();
?>
<?php if( count($languages) > 1 ) : ?>
	<li>
		<span class="nav-item smaller text-muted">
			<?php echo M('Language'); ?>:

			<?php foreach( $languages as $lng ) : ?>
				<?php if( $currentLanguage != $lng ) : ?>
					<?php
//				$langConf = $lm->getLanguageConf( $lng );
//				$langTitle = $langConf['language'];
						$langTitle = $lng;
						if( $langTitle == 'en-builtin' )
						{
							$langTitle = 'en';
						}
					?>
					<a href="<?php echo ntsLink::makeLink('anon/language', '', array('lang' => $lng)); ?>"><?php echo $langTitle; ?></a>
				<?php endif; ?>
			<?php endforeach; ?>
		</span>
	</li>
<?php endif; ?>
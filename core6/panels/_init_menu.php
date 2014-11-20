<?php
global $NTS_MENU, $NTS_CURRENT_USER;

/* BUILD MENUS */
/* FIRST LEVEL MENU */
$NTS_VIEW['menu1'] = array();

$NTS_VIEW['menu3'] = array();
if( 1 OR $NTS_VIEW['menu1'] )
{
	/* if subheader exists then parent of current, otherwise second level after root */
	$subheaderInfo = ntsLib::findClosestFile( $currentPanel, 'subheader.php', TRUE );
	if( $subheaderInfo )
	{
		/* check if alias here then no subheader */
		$aliasInfo = ntsLib::findClosestFile( ntsLib::getParentPath($currentPanel), 'alias.php', true );
		if( ! $aliasInfo )
		{
			if( ! array_key_exists('subHeaderFile', $NTS_VIEW) )
			{
				$NTS_VIEW['subHeaderFile'] = $subheaderInfo[0];
			}

			$subheaderPath = $subheaderInfo[1];
			$tabsFile = ntsLib::fileInCoreDirs('panels/' . $subheaderPath . '/_tabs.php');
			
			if( 
				(! (isset($NTS_VIEW[NTS_PARAM_VIEW_RICH]) && ($NTS_VIEW[NTS_PARAM_VIEW_RICH] == 'basic')))
				&&
				$tabsFile
				)
			{
				require( $tabsFile );
				$final_tabs = array();
				$last_seq = 0;
				foreach( $tabs as $k => $tab )
				{
					if( ! isset($tab['panel']) )
						$tab['panel'] = $k;
					$tab['panel'] = $subheaderPath . '/' . $tab['panel'];
					if( ! isset($tab['params']) )
						$tab['params'] = array();
					if( ! isset($tab['alert']) )
						$tab['alert'] = 0;
					if( ! isset($tab['link-class']) )
						$tab['link-class'] = '';
					if( ! isset($tab['seq']) )
					{
						$tab['seq'] = $last_seq + 10;
						$last_seq = $tab['seq'];
					}

					if( is_array($tab) && isset($tab[0]) )
					{
						foreach( array_keys($tab) as $tk )
						{
							if( ! is_numeric($tk) )
								continue;

							if( ! isset($tab[$tk]['panel']) )
								$tab[$tk]['panel'] = $k . '/' . $tk;

							$tab[$tk]['panel'] = ntsLib::getParentPath($currentPanel) . '/' . $tab[$tk]['panel'];
							if( ! isset($tab[$tk]['params']) )
								$tab[$tk]['params'] = array();
							if( ! isset($tab[$tk]['alert']) )
								$tab[$tk]['alert'] = 0;
							if( ! isset($tab[$tk]['seq']) )
							{
								$tab[$tk]['seq'] = $last_seq + 10;
								$last_seq = $tab[$tk]['seq'];
							}
						}
					}

				/* check if panel allowed */
					if( ! $NTS_CURRENT_USER->isPanelDisabled( $tab['panel'] ) )
					{
						$final_tabs[] = $tab;
					}
				}

				if( count($final_tabs) > 1 )
				{
					reset( $final_tabs );
					foreach( $final_tabs as $tab )
					{
						$NTS_VIEW['menu3'][] = $tab;
					}
				}
			}
		}
	}
}
else
{
	$subheaderInfo = ntsLib::findClosestFile( $currentPanel, 'subheader.php', FALSE );
	if( $subheaderInfo )
	{
		if( ! array_key_exists('subHeaderFile', $NTS_VIEW) )
		{
			$NTS_VIEW['subHeaderFile'] = $subheaderInfo[0];
		}
	}
}

usort( $NTS_VIEW['menu3'], create_function('$a, $b', 'return ntsLib::numberCompare($a["seq"], $b["seq"]);') );

$headerFile = '';
$footerFile = '';
if( isset($rootInfo) && $rootInfo )
{
	$rootPath = $rootInfo[1];
	$headerFile = ntsLib::fileInCoreDirs( '/panels/' . $rootPath . '/header.php' );
	$footerFile = ntsLib::fileInCoreDirs( '/panels/' . $rootPath . '/footer.php' );
}
if( $headerFile && $footerFile )
{
	$NTS_VIEW['headerFile'] = $headerFile;
//	$NTS_VIEW['footerFile'] = $footerFile;
	$NTS_VIEW['systemFooterFile'] = $footerFile;
}
else
{
/* for customer view */
	$check_dirs = array(
		NTS_RUN_DIR . '/theme/',
		NTS_APP_DIR . '/../theme/'
		);

	foreach( $check_dirs as $checkd )
	{
		$checkf = $checkd . 'index.php';
		if( file_exists($checkf) )
		{
			$NTS_VIEW['isInside'] = TRUE;
			$NTS_VIEW['isTheme'] = $checkf;
			break;
		}
	}
}
?>
<?php
class ntsCommandManager {
	var $_ok;
	var $actionResults;
	var $_returnParams;
	var $silent = false;
	var $act_as = 0;

	function ntsCommandManager(){
		$this->_ok = 0;
		$this->actionResults = array();
		$this->_returnParams = array();
		$this->silent = false;
		}

	function getFiles( $object, $mainActionName, $actionFiles = true, $notifyFiles = true ){
		$return = array();
		$thisActions = array();

	// GET REAL ACTION NAME BY THE OBJECT CLASS
		$className = $object->getClassName();
		$actionName = $className . '::' . $mainActionName;

		$runLevel = 50;
		$thisActions[] = array( $actionName, $runLevel );

	// GET INHERITS
		/* simplify it - every object inherits from generic 'object' */
		$inheritsFrom = array( 'object' );
		switch( $className ){
			case 'user':
				if( $object->hasRole('customer') ){
					$inheritedActionName = 'customer' . '::' . $mainActionName;
					$thisActions[] = array( $inheritedActionName, ++$runLevel );
					}
				break;
			default:
				$inheritsFrom = array( 'object' );
				break;
			}

		reset( $inheritsFrom );
		foreach( $inheritsFrom as $inhClassName ){
			$runLevel++;
			$inheritedActionName = $inhClassName . '::' . $mainActionName;
			$thisActions[] = array( $inheritedActionName, $runLevel );
			}

		$thisActions[] = array( $actionName . '_after', ++$runLevel );
		$thisActions[] = array( $inheritedActionName . '_after', ++$runLevel );

	// IF IT IS DISABLED
		$disabledCommands = array();
		if( isset($GLOBALS['DISABLED_COMMANDS']) )
			$disabledCommands = array_merge( $disabledCommands, $GLOBALS['DISABLED_COMMANDS'] );

		reset( $thisActions );
		foreach( $thisActions as $thisActionInfo ){
			$thisActionName = $thisActionInfo[ 0 ];
			if( in_array($thisActionName, $disabledCommands) ){
				$this->addActionError( '', 'This action is disabled' );
				$this->_ok = 0;
				return false;
				}
			}

	/* array of files to run */
		$runFiles = array();

	/* ACTION FILES */
		$actionFound = false;
		$commandRunFiles = array();
		reset( $thisActions );

		$commandFileDirs = array();

	/* plugin files */
		$plm =& ntsPluginManager::getInstance();
		$activePlugins = $plm->getActivePlugins();
		reset( $activePlugins );
		foreach( $activePlugins as $plg ){
			$commandFileDirs[] = $plm->getPluginFolder( $plg ) . '/model';
			}

		$fileDir = NTS_APP_DIR . '/model';
		if( ! in_array($fileDir, $commandFileDirs) )
			$commandFileDirs[] = $fileDir;

		foreach( $thisActions as $thisActionInfo ){
			$thisActionName = $thisActionInfo[ 0 ];
			$thisActionRunLevel = $thisActionInfo[ 1 ];

			$commandShortFileName = str_replace( '::', '/', $thisActionName ) . '.php';

			reset( $commandFileDirs );
			foreach( $commandFileDirs as $cfd ){
				$commandFullFileName = $cfd . '/' . $commandShortFileName;

				if( file_exists($commandFullFileName) ){
					if( $actionFiles ){
						$runFiles[] = array( $commandFullFileName, $thisActionRunLevel );
						}
					$actionFound = true;
					break;
					}
				}

		/* notifier */
			$notifierRunLevel = 90;
			reset( $commandFileDirs );
			foreach( $commandFileDirs as $cfd ){
				$commandFullFileName = $cfd . '/' . $commandShortFileName;
				$notifierFullFileName = dirname( $commandFullFileName ) . '/_notifier.php';

				if( file_exists($notifierFullFileName) ){
					if( $notifyFiles ){
						// check if already here
						$addThis = true;
						reset( $runFiles );
						foreach( $runFiles as $rf ){
							if( $rf[0] == $notifierFullFileName ){
								$addThis = false;
								break;
								}
							}
						if( $addThis ){
							$runFiles[] = array( $notifierFullFileName, $notifierRunLevel );
							}
						}
//					break;
					}
				}

		/* observers */
			$observerRunLevel = 80;
			$observerFile = '_observers.php';

			reset( $commandFileDirs );
			foreach( $commandFileDirs as $cfd )
			{
				$observerFullFile = $cfd . '/' . $observerFile;

				if( file_exists($observerFullFile) )
				{
					$observers = array();
					require( $observerFullFile);
					reset( $observers );
					foreach( $observers as $obs )
					{
						list( $observerFile, $observerClass, $observerMethod ) = $obs;
						$runFiles[] = array( array($thisActionName, $observerFile, $observerClass, $observerMethod), $observerRunLevel );
					}
				}
			}
			}
/*
_print_r( $runFiles );
*/
		if( ! $actionFound )
		{
			_print_r( $thisActions );
			echo "action(s) not defined!<BR>";
			exit;
		}

	/* PLUGIN FILES */
		/*
		$plm =& pluginManager::getInstance();
		reset( $thisActions );
		foreach( $thisActions as $thisActionName ){
			$thisRunFiles = $plm->getActionFiles( $thisActionName );
			$runFiles = array_merge( $runFiles, $thisRunFiles );
			}
		*/

		return $runFiles;
		}

	function runFiles( $runFiles, &$object, $mainActionName, $params = array() ){
		$return = true;
		if( ! $runFiles )
			$runFiles = array();

	/* SORT BY RUN LEVEL */
		usort( $runFiles, create_function('$a, $b', 'return ntsLib::numberCompare($a[1], $b[1]);' ) );

/*
reset( $runFiles );
foreach( $runFiles as $fArray ){
	$f = $fArray[ 0 ];
	$rl =  $fArray[ 1 ];
	echo "$f : $rl<BR>";
	}
*/
	/* RUN FILES */
		reset( $runFiles );
		foreach( $runFiles as $fArray )
		{
			$f = $fArray[ 0 ];

			$actionDescription = '';
			$actionError = '';
			$actionResult = 0;
			$actionStop = 0;

			if( is_array($f) )
			{
				/* object oriented version */
				$thisActionName = $f[0];
				$actionFile = $f[1];
				$actionClass = $f[2];
				$actionMethod = $f[3];

				include_once( $actionFile );
				$actionObject = new $actionClass;
				call_user_func_array( array($actionObject, $actionMethod), array($thisActionName, $object, $mainActionName, $params) );
			}
			else
			{
				require( $f );

				if( $actionDescription )
				{
					if( $actionResult )
					{
						$this->_ok = 1;
					}
					else
					{
						$this->addActionError( $actionDescription, $actionError );
						$this->_ok = 0;
					}
				}
				else 
				{
					if( $actionResult )
					{
						$this->_ok = 1;
					}
					else
					{
						$this->addActionError( '', $actionError );
					}
				}
				if( $actionStop )
					break;
			}
		}

		return $return;
	}

	function runCommand( &$object, $mainActionName, $params = array() ){
		$runFiles = $this->getFiles( $object, $mainActionName, true, (! $this->silent) );
		return $this->runFiles( $runFiles, $object, $mainActionName, $params );
		}

	function isOk(){
		return $this->_ok;
		}

	function resetActionErrors(){
		$this->actionResults = array();
		}
	function getActionErrors(){
		return $this->actionResults;
		}
	function addActionError( $desc, $msg ){
		$this->actionResults[] = array( $desc, $msg );
		}

	function printActionErrors(){
		$errors = $this->getActionErrors();
		reset( $errors );

		$lines = array();
		foreach( $errors as $errArray ){
			if( $errArray[0] ){
				$lines[] = 'Action: <b>' . $errArray[ 0 ] . '</b><br>' . $errArray[ 1 ];
				}
			else {
				if( $errArray[1] ){
//					$lines[] = 'Error: ' . $errArray[1];
					$lines[] = $errArray[1];
					}
				}
			}
//		$return = '<ul><li>' . join( '</li><li>', $lines ) . '</li></ul>';

		$return = join( '<br>', $lines );
		$this->resetActionErrors();
		return $return;
		}
		
	// Singleton stuff
	static function &getInstance(){
		return ntsLib::singletonFunction( 'ntsCommandManager' );
		}
	}
?>
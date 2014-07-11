<?php
class ntsEmailLogger {
	var $params = array();
/*
	'to'
	'from'
	'from_name'
	'subject'
	'body'
	'alt_body'
*/

	function ntsEmailLogger(){
		$this->params = array();
		}

	function setParam( $pName, $pValue ){
		$this->params[ $pName ] = $pValue;
		}

	function getParams(){
		return $this->params;
		}

	function add_sql(){
		return;
		$ntsdb =& dbWrapper::getInstance();

		$tblName = 'emaillog';
		$paramsArray = $this->getParams();
		$paramsArray['sent_at'] = time();

		$ntsdb->insert( $tblName, $paramsArray );
		}

	function add(){
		$outFile = NTS_APP_DIR . '/../emaillog.txt';

		if( file_exists($outFile) ){
			$paramsArray = $this->getParams();
			$date = date( "F j, Y, g:i a", time() );
			$paramsArray['sent_at'] = $date;

			$text = array();
			foreach( $paramsArray as $k => $v ){
				$v = str_replace( "\n", "", $v );
				$text[] = $k . ':' . $v;
				}
			$text = join( "\n", $text );

			$fp = fopen( $outFile, 'a' );
			fwrite( $fp, $text . "\n\n" );
			fclose($fp);
			}
		}
	}
?>
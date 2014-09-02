<?php
class ntsCurrency
{
	static function formatPriceNumber( $amount )
	{
		$conf =& ntsConf::getInstance();
		$formatConf = $conf->get( 'priceFormat' );
		list( $beforeSign, $decPoint, $thousandSep, $afterSign ) = $formatConf;

		$amount = number_format( $amount, 2, $decPoint, $thousandSep );
		$return = $amount;
		return $return;
	}

	static function formatPrice( $amount )
	{
		$conf =& ntsConf::getInstance();
		$formatConf = $conf->get( 'priceFormat' );
		list( $beforeSign, $decPoint, $thousandSep, $afterSign ) = $formatConf;

		$amount = ntsCurrency::formatPriceNumber( $amount );
		$return = $beforeSign . $amount . $afterSign;
		return $return;
	}

	static function formatPriceLabel( $amount )
	{
		$return = ntsCurrency::formatPrice( $amount );
		if( $amount >= 0 )
		{
			$return = '<span class="label label-success">' . $return . '</span>';
		}
		else
		{
			$return = '<span class="label label-danger">' . $return . '</span>';
		}
		return $return;
	}

	static function formatServicePrice( $amount, $defaultAmount = '' )
	{
		$return = '';
		if( strlen($amount) )
		{
			if( $amount > 0 )
			{
				$price = ntsCurrency::formatPrice( $amount );
				$return = $price;
			}
			else
			{
				$return = M('Free');
			}
		}
		if( strlen($defaultAmount) && ($defaultAmount != $amount) )
		{
			$return .= ' <span style="text-decoration: line-through;">' . ntsCurrency::formatPrice( $defaultAmount ) . '</span>';
		}
		return $return;
	}
}

class ntsHtmlTable
{
	var $header;
	var $rows;
	var $config;

	function __construct()
	{
		$this->header = array();
		$this->rows = array();
	}

	function configView( $config )
	{
		$this->config = $config;
	}

	function prepareView( $e )
	{
		$return = array();

		global $NTS_VIEW;
		$t = $NTS_VIEW['t'];

		foreach( $e as $k => $fieldValue )
		{
			if( ! isset($this->config[$k]) )
				continue;
			$fieldType = $this->config[$k];
			switch( $fieldType ){
				case 'date':
					$t->setTimestamp( $fieldValue );
					$returnView = $t->formatDate();
					break;
				case 'date_never':
					if( $fieldValue ){
						$t->setTimestamp( $fieldValue );
						$returnView = $t->formatDate();
						}
					else {
						$returnView = M('Never Expires');
						}
					break;
				case 'price':
					$returnView = ntsCurrency::formatPrice( $fieldValue );
					break;
				case 'integer':
					$returnView = (int) $fieldValue;
					break;
				default:
					$returnView = $fieldValue;
					break;
				}
			$return[ $k ] = $returnView;
		}
		return $return;
	}

	function setHeader( $header )
	{
		$this->header = $header;
	}

	function addRow( $row )
	{
		$this->rows[] = $row;
	}
	function display(){
?>

<table class="table">
<?php if( $this->header ) : ?>
	<?php reset( $this->header ); ?>
	<tr>
	<?php foreach( $this->header as $r ) : ?>

<?php	if( is_array($r) ) : ?>
<?php
			if( isset($r['value']) ){
				$value = $r['value'];
				unset( $r['value'] );
				reset( $r );
				$props = array();
				foreach( $r as $k => $v ){
					$props[] = $k . '="' . $v . '"';
					}
				$props = join( ' ', $props );
				}
			else {
				$value = '';
				}
?>
			<th <?php echo $props; ?>><?php echo $value; ?></th>
<?php	else : ?>
			<th><?php echo $r; ?></th>
<?php	endif; ?>

	<?php endforeach; ?>
	</tr>
<?php endif; ?>

<?php reset( $this->rows ); ?>
<?php foreach( $this->rows as $row ) : ?>
	<?php if( ! is_array($row) ) : ?>
		<?php echo $row; ?>
	<?php else : ?>
		<tr>
		<?php reset( $row ); ?>
		<?php 	foreach( $row as $r ) : ?>
		<?php		if( is_array($r) ) : ?>
		<?php
						if( isset($r['value']) ){
							$value = $r['value'];
							unset( $r['value'] );
							reset( $r );
							$props = array();
							foreach( $r as $k => $v ){
								$props[] = $k . '="' . $v . '"';
								}
							$props = join( ' ', $props );
							}
						else {
							$value = '';
							}
		?>
						<td <?php echo $props; ?>><?php echo $value; ?></td>
		<?php		else : ?>
						<td><?php echo $r; ?></td>
		<?php		endif; ?>
		<?php 	endforeach; ?>
		</tr>
	<?php endif; ?>
<?php endforeach; ?>

</table>

<?php
		}
	}

class ntsListingTable {
	var $entries;
	var $fields;

	function ntsListingTable( $fields, $entries ){
		$this->fields = $fields;
		$this->entries = $entries;
		}

	function displayField( $fn, $e ){
		global $NTS_VIEW;
		$t = $NTS_VIEW['t'];

		$return = '';
		$fieldType = $this->fields[$fn][0];
		$fieldValue = is_object($e) ? $e->getProp($fn) : $e[$fn];
		switch( $fieldType ){
			case 'date':
				$t->setTimestamp( $fieldValue );
				$return = $t->formatDate();
				break;
			case 'date_never':
				if( $fieldValue ){
					$t->setTimestamp( $fieldValue );
					$return = $t->formatDate();
					}
				else {
					$return = M('Never Expires');
					}
				break;
			case 'price':
				$return = ntsCurrency::formatPrice( $fieldValue );
				break;
			default:
				$return = $fieldValue;
				break;
			}
		return $return;
		}
	function display(){
?>

<div class="table-responsive">
<table class="table table-striped">
<?php reset( $this->fields ); ?>
<tr>
<?php foreach( $this->fields as $fn => $fa ) : ?>
	<th><?php echo $fa[1]; ?></th>
<?php endforeach; ?>
</tr>

<?php reset( $this->entries ); ?>
<?php foreach( $this->entries as $e ) : ?>
<tr>
<?php reset( $this->fields ); ?>
<?php 	foreach( $this->fields as $fn => $fa ) : ?>
	<td><?php echo $this->displayField($fn, $e); ?></td>
<?php 	endforeach; ?>
</tr>
<?php endforeach; ?>

</table>
</div>

<?php
		}
	}
?>
<?php
class ntsLink {
	var $target = '';
	var $prefix = '';

	function ntsLink(){
		$this->setTarget( ntsLib::getRootWebpage() );
		$this->prefix = '';
		}

	function setTarget( $trg ){
		$this->target = $trg;
		}

	function prepare( $panel = '', $action = '', $params = array() ){
		$this->prefix = ntsView::makeGetParams( $panel, $action, $params );
		}

	static function makeLink( $panel = '', $action = '', $params = array(), $return = false, $skipSaveOn = false ){
		return ntsLink::makeLinkFull( ntsLib::getRootWebpage(), $panel, $action, $params, $skipSaveOn );
		}
	static function makeLinkFull( $target, $panel = '', $action = '', $params = array(), $skipSaveOn = false ){
		$addOn = '';
		if( isset($params['#']) ){
			$addOn = '#' . $params['#'];
			unset( $params['#'] );
			}
		$rootWebPage = $target;

		$joiner = '';
		if( strpos($rootWebPage, '?') === false )
		{
			if( substr($rootWebPage, -1) != '?' )
				$joiner = '?';
		}
		else
		{
			if( substr($rootWebPage, -1) != '&' )
				$joiner = '&';
		}

		$getParams = ntsView::makeGetParams( $panel, $action, $params, $skipSaveOn );
		if( $getParams )
			$link =  $rootWebPage . $joiner . $getParams;
		else
			$link =  $rootWebPage;
		if( $addOn )
			$link .= $addOn;
		return $link;
		}

	static function printLink( $p = array(), $showIfDisabled = false ){
		global $NTS_CURRENT_USER;
		$return = '';
		if( ! isset($p['action']) )
			$p['action'] = '';
		if( ! isset($p['params']) )
			$p['params'] = array();
		if( ! isset($p['return']) )
			$p['return'] = false;
		if( ! isset($p['skipSaveOn']) )
			$p['skipSaveOn'] = false;

		$panel = ntsView::parsePanel( $p['panel'] );
		$attrLine = ' ';
		if( isset($p['attr']) ){
			$attrs = array();
			foreach( $p['attr'] as $pk => $pv ){
				$attrs[] = $pk . '="' . $pv . '"';
				}
			$attrLine = ' ' . join( ' ', $attrs );
			}

		if( ! $NTS_CURRENT_USER->isPanelDisabled($panel) ){
			$link = ntsLink::makeLink( $panel, $p['action'], $p['params'], $p['return'], $p['skipSaveOn'] );
			$return = '<a' . $attrLine . ' href="' . $link . '">' . $p['title'] . '</a>';
			}
		elseif( $showIfDisabled ){
			$return = '<span' . $attrLine . '>' . $p['title'] . '</span>';
			}
		return $return;
		}
	}

class ntsView {
	static function formatPercent( $percent, $html = FALSE )
	{
		$return = 100 * $percent;
		$return_number = ceil( $return );
		$return = $return_number . '%';
		if( $html )
		{
			if( $return_number == 0 )
				$return = '<span class="text-danger">' . $return . '</span>';
			elseif( $return_number < 100 )
				$return = '<span class="text-warning">' . $return . '</span>';
			else
				$return = '<span class="text-success">' . $return . '</span>';
		}
		return $return;
	}

	static function setTitle( $title ){
		global $NTS_PAGE_TITLE_ARRAY, $NTS_PAGE_TITLE;
		if( ! $NTS_PAGE_TITLE_ARRAY )
			$NTS_PAGE_TITLE_ARRAY = array();
		$NTS_PAGE_TITLE_ARRAY[] = $title;
		$NTS_PAGE_TITLE = join( ' - ', $NTS_PAGE_TITLE_ARRAY ); // for backward compatibility
		}

	static function getTitle(){
		global $NTS_PAGE_TITLE_ARRAY;
		if( ! $NTS_PAGE_TITLE_ARRAY )
			$NTS_PAGE_TITLE_ARRAY = array();
		$return = join( ' - ', $NTS_PAGE_TITLE_ARRAY );
		return $return;
		}

	static function setNextAction( $panel, $action = '' ){
		global $_NTS;
		$panel = ntsView::parsePanel( $panel );
		$_NTS['REQUESTED_PANEL'] = $panel;
		$_NTS['REQUESTED_ACTION'] = $action;
		}

	static function objectTitle( $object, $html = FALSE, $params = array() ){
		global $NTS_VIEW;
		if( ! $object )
			return;
		$className = $object->getClassName();
		switch( $className ){
			case 'invoice':
				$return = M('Invoice') . ' ' . $object->getProp('refno');
				break;

			case 'order':
				$packId = $object->getProp( 'pack_id' );
				$pack = ntsObjectFactory::get( 'pack' );
				$pack->setId( $packId );
				$return = ntsView::objectTitle($pack);
				break;

			case 'pack':
				$return = $object->getProp('title');
				if( ! $return )
				{
					$return = M('Package') . ' #' . $object->getId();
				}
				break;

			case 'service_cat':
				if( $object->getId() )
					$return = $object->getProp( 'title' );
				else
					$return = M('Uncategorized');
				break;

			case 'location':
				$return = $object->getProp( 'title' );
				if( $html )
				{
					$return = '<i class="fa fa-home"></i> ' . $return;
				}
				break;

			case 'resource':
				$return = $object->getProp( 'title' );
				if( $html )
				{
					$icon = array('fa fa-hand-o-up');

					$is_archived = $object->getProp('archive');
					if( $is_archived )
					{
						$icon[] = 'text-muted';
						$return .= ' [' . M('Archive') . ']';
					}

					$is_internal = $object->getProp('_internal');
					if( $is_internal )
					{
						$icon[] = 'text-warning';
						$return .= ' [' . M('Internal') . ']';
					}

					$return = '<i class="' . join(' ', $icon) . '"></i> ' . $return;
				}
				break;

			case 'promotion':
				$return = $object->getProp( 'title' );
				$return .= ' [' . $object->getModificationView() . ']';
				if( $html )
				{
					$return = '<i class="fa fa-gift"></i> ' . $return;
				}
				break;

			case 'coupon':
				$return = '';
				if( $html )
				{
					$return .= '<span class="text-success">';
				}
				$return .= $object->getProp( 'code' );
				if( $html )
				{
					$return .= '</span>';
				}
				$promotion = ntsObjectFactory::get( 'promotion' );
				$promotion->setId( $object->getProp('promotion_id') );
				$return .= ', ' . ntsView::objectTitle( $promotion );
				break;

			case 'service':
				$conf =& ntsConf::getInstance();

				$return = $object->getProp( 'title' );
				$durationView = '';
				if( $durationView ){
					$return .= ' [' . $durationView . ']';
					}
				if( $html )
				{
					$return = '<i class="fa fa-tag"></i> ' . $return;
				}
				break;

			case 'timeoff':
				$return = M('Timeoff');
				$t = isset($NTS_VIEW['t']) ? $NTS_VIEW['t'] : new ntsTime;

				$t->setTimestamp( $object->getProp('starts_at') );
				$start_date = $t->formatDate_Db();
				$t->setTimestamp( $object->getProp('ends_at') );
				$end_date = $t->formatDate_Db(); 

				if( $end_date != $start_date )
				{
					$return = $t->formatDateRange( $start_date, $end_date );
				}
				else
				{
					$t->setTimestamp( $object->getProp('starts_at') );
					$return = $t->formatDateFull();
					$return .= ' ' . $t->formatTime();
					$t->setTimestamp( $object->getProp('ends_at') );
					$return .= ' - ' . $t->formatTime();
				}

				if( $html )
				{
					$return = '<i class="fa fa-coffee"></i> ' . $return;
				}
				break;

			case 'appointment':
				$service = ntsObjectFactory::get( 'service' );
				$service->setId( $object->getProp('service_id') );
				$serviceView = ntsView::objectTitle( $service );

				$t = isset($NTS_VIEW['t']) ? $NTS_VIEW['t'] : new ntsTime;
				$startsAt = $object->getProp('starts_at');
				if( $startsAt )
				{
					$t->setTimestamp( $startsAt ); 
					$dateView = $t->formatWeekdayShort() . ', ' . $t->formatDate();
					$timeView = $t->formatTime( $object->getProp('duration') );

					if( isset($params['skip']) && in_array('date',$params['skip']) )
					{
						$return = $timeView . ' ' . $serviceView;
					}
					else
					{
						$return = $dateView . ' ' . $timeView . ' ' . $serviceView;
					}
				}
				else
				{
					$timeView = M('Not Scheduled');
					$return = $serviceView . ' [' . $timeView . ']';
				}

				$id = $object->getId();
				if( $id < 0 )
				{
					$return = '[' . M('New') . '] ' . $return;
				}
				break;

			case 'user':
				$return = array();
				$userId = $object->getId();
				if( $object->getProp( 'first_name' ) )
					$return[] = $object->getProp( 'first_name' );
				if( $object->getProp( 'last_name' ) )
					$return[] = $object->getProp( 'last_name' );
			
				if( $return )
					$return = join( ' ', $return );
				else
					$return = M('Customer') . ' #' . $object->getId();

				if( $html )
				{
					if( $userId == -1 )
					{
						$return = '<i class="fa fa-gears fa-border"></i> ' . $return;
					}
					else
					{
						if( $object->hasRole('admin') )
						{
							$return = '<i class="fa fa-user fa-border"></i> ' . $return;
						}
						else
						{
							$return = '<i class="fa fa-user"></i> ' . $return;
						}
					}
				}
				break;

			default:
				$return = $object->getProp( 'title' );
				break;
			}

		$className = $object->getClassName();
	/* plugin files */
		$plm =& ntsPluginManager::getInstance();
		$activePlugins = $plm->getActivePlugins();
		reset( $activePlugins );
		$viewFiles = array();
		foreach( $activePlugins as $plg ){
			$viewFiles[] = $plm->getPluginFolder( $plg ) . '/views/' . $className . '/title.php';
			}
		reset( $viewFiles );
		foreach( $viewFiles as $vf ){
			if( file_exists($vf) ){
				require( $vf );
				break;
				}
			}

		list( $link_title, $link_icon ) = Hc_lib::parse_icon( $return );
		$return = $link_icon . $link_title;
		return $return;
		}

	static function appServiceView( $a, $noPrice = FALSE ){
		$return = '';

		$service = ntsObjectFactory::get( 'service' );
		$service->setId( $a->getProp('service_id') );

		$seats = $a->getProp('seats');
		$duration = $a->getProp('duration');

		$return .= ntsView::serviceView( $service, $seats, $duration );

		if( ! $noPrice )
		{
			$thisPrice = $a->getProp('price');
			$priceView = ntsCurrency::formatServicePrice($thisPrice);
			if( strlen($priceView) ){
				$return .= "\n" . M('Price') . ': ' . $priceView;
				}
		}
		return $return;
		}

	static function serviceView( $service, $seats, $duration ){
		$return = '';
		$return .= $service->getProp('title');
		return $return;
		}

	static function setBack( $to, $alsoAjax = false ){
		global $NTS_VIEW;
		if( ntsLib::isAjax() ){
			$_SESSION['nts-get-back-ajax'] = $to;
			}
		else {
			$_SESSION['nts-get-back'] = $to;
			if( $alsoAjax ){
				$_SESSION['nts-get-back-ajax'] = $to;
				}
			}
		}

	static function redirect2( $to )
	{
		if( ntsLib::isAjax() )
		{
			// clear flash
			ntsView::clearAdminAnnounce();
			ntsView::clearAnnounce();

			$out = array(
				'redirect'	=> $to,
				);

			header("Type: application/json");
			header("Content-Type: application/json");
			echo json_encode($out);
		}
		else
		{
			if( ! headers_sent() )
			{
				header( 'Location: ' . $to );
			}
			else
			{
//				$html = "<META http-equiv=\"refresh\" content=\"0;URL=$to\">";
				$html = "<a href=\"$to\">$to</a>";
				echo $html;
			}
		}
		exit;
		return;
	}

	static function redirect( $to, $force = false, $parent = false ){
		global $NTS_VIEW;
		$html = '';
		if( ntsLib::isAjax() ){
			if( $force ){
			$html =<<<EOT

<script language="JavaScript">
document.location.href="$to";
</script>

EOT;
				}
			else {
/* check if we have another parent nts-ajax-container */
			$randomId = ntsLib::generateRand(6, array('caps' => false));

			$html =<<<EOT
<span id="nts-$randomId"></span>

EOT;

			if( $parent ){
				$html .=<<<EOT

<script language="JavaScript">
var myGrandparent = jQuery('#nts-$randomId').closest( '.nts-ajax-return' ).parents( '.nts-ajax-return' );

EOT;
				}
			else {
				$html .=<<<EOT

<script language="JavaScript">
var myGrandparent = jQuery('#nts-$randomId').closest( '.nts-ajax-return' );

EOT;
				}

$viewModeParam = NTS_PARAM_VIEW_MODE;
			$html .=<<<EOT
if( myGrandparent.length > 0 ){
	myGrandparent = myGrandparent.first();
	var thisFormData = '$viewModeParam=ajax';
	jQuery.ajax({
		type: "GET",
		url: "$to",
		data: thisFormData
		})
		.done( function(msg){
			myGrandparent.html( msg );
			});
	}
else{
	document.location.href="$to";
	}
</script>

EOT;
				}
			echo $html;
			exit;
			}
		else {
			if( ! headers_sent() ){
				header( 'Location: ' . $to );
				exit;
				}
			else {
//			$html = "<META http-equiv=\"refresh\" content=\"0;URL=$to\">";
				$html = "<a href=\"$to\">$to</a>";
				echo $html;
				}
			}
		}

	static function getBackLink( $force = false, $parent = false ){
		global $NTS_VIEW;

		$to = '';
		if( $force ){
			if( isset($_SESSION['nts-get-back']) && $_SESSION['nts-get-back'] ){
				$to = $_SESSION['nts-get-back'];
				unset( $_SESSION['nts-get-back'] );
				}
			}
		else {
			if( ntsLib::isAjax() ){
				$to = $_SESSION['nts-get-back-ajax'];
				unset( $_SESSION['nts-get-back-ajax'] );
				}
			else {
				if( isset($_SESSION['nts-get-back']) && $_SESSION['nts-get-back'] ){
					$to = $_SESSION['nts-get-back'];
					unset( $_SESSION['nts-get-back'] );
					}
				}
			}

		if( ! $to ){
			$to = ntsLink::makeLink('-current-');
			}
		return $to;
		}

	static function getBack( $force = false, $parent = false ){
		$to = ntsView::getBackLink( $force, $parent );
		ntsView::redirect( $to, $force, $parent );
		}

	static function resetPersistentParams( $rootPanel = '' ){
		global $NTS_PERSISTENT_PARAMS;
		if( ! $rootPanel )
			$rootPanel = '/';
		if( ! $NTS_PERSISTENT_PARAMS )
			$NTS_PERSISTENT_PARAMS = array();
		$NTS_PERSISTENT_PARAMS[ $rootPanel ] = array();
		}

	static function resetPersistentParam( $panel, $key ){
		global $NTS_PERSISTENT_PARAMS;
		unset( $NTS_PERSISTENT_PARAMS[ $panel ][ $key ] );
		}

	static function getPersistentParams( $rootPanel = '' ){
		global $NTS_PERSISTENT_PARAMS, $NTS_VIEW;
		if( ! $rootPanel )
			$rootPanel = '/';
		$return = isset($NTS_PERSISTENT_PARAMS[$rootPanel]) ? $NTS_PERSISTENT_PARAMS[$rootPanel] : array();
		return $return;
		}

	static function setPersistentParams( $pNames, $rootPanel = '' ){
		global $NTS_PERSISTENT_PARAMS;

		if( ! $rootPanel )
			$rootPanel = '/';

		if( ! $NTS_PERSISTENT_PARAMS )
			$NTS_PERSISTENT_PARAMS = array();
		if( ! isset($NTS_PERSISTENT_PARAMS[ $rootPanel ]) )
			$NTS_PERSISTENT_PARAMS[ $rootPanel ] = array();

		foreach( $pNames as $pName => $pValue ){
			if( is_array($pValue) || strlen($pValue) )
				$NTS_PERSISTENT_PARAMS[ $rootPanel ][ $pName ] = $pValue;
			}
		}

	static function parsePanel( $panel ){
		global $_NTS;
		$currentTag = '-current-';
		if( substr($panel, 0, strlen($currentTag)) == $currentTag )
		{
			$replaceFrom = '-current-';
			$replaceTo = $_NTS['CURRENT_PANEL'];

			if( strpos($panel, '..') !== false )
			{
				if( strlen($replaceTo) )
				{
					$downCount = substr_count( $panel, '/..' );
					$re = "/^(.+)(\/[^\/]+){" . $downCount. "}$/U";
					preg_match($re, $replaceTo, $ma);

					$replaceFrom = '-current-' . str_repeat('/..', $downCount);
					$replaceTo = $ma[1];
					$panel = str_replace( $replaceFrom, $replaceTo, $panel );
				}
				else
				{
					$panel = '';
				}
			}
			else
			{
				$panel = str_replace( $replaceFrom, $replaceTo, $panel );
			}
		}
		return $panel;
		}

	static function makeGetParams( $panel = '', $action = '', $params = array(), $skipSaveOn = false ){
		global $NTS_PERSISTENT_PARAMS, $_NTS;
		$panel = ntsView::parsePanel( $panel );

		if( isset($params['-skipSaveOn-']) && $params['-skipSaveOn-'] ){
			unset( $params['-skipSaveOn-'] );
			$skipSaveOn = true;
			}

		if( $panel )
			$params[ NTS_PARAM_PANEL ] = $panel;
		if( $action )
			$params[ NTS_PARAM_ACTION ] = $action;

		if( $NTS_PERSISTENT_PARAMS && (! $skipSaveOn) ){
			reset( $NTS_PERSISTENT_PARAMS );
		/* global */
			if( isset($NTS_PERSISTENT_PARAMS['/']) ){
				reset( $NTS_PERSISTENT_PARAMS['/'] );
				foreach( $NTS_PERSISTENT_PARAMS['/'] as $p => $v ){
					if( ! isset($params[$p]) )
						$params[ $p ] = $v;
					}
				}
		/* above panel */
			$setIn = array();
			reset( $NTS_PERSISTENT_PARAMS );
			foreach( $NTS_PERSISTENT_PARAMS as $pan => $pampam ){
				if( substr($panel, 0, strlen($pan) ) != $pan )
					continue;
				reset( $pampam );
				foreach( $pampam as $p => $v ){
					if( 
//						( isset($setIn[$p]) && (strlen($pan) > strlen($setIn[$p])) ) OR
						( ! isset($params[$p]) )
						){
						$params[ $p ] = $v;
						$setIn[ $p ] = $pan;
						}
					}
				}
			}

		reset( $params );
		$linkParts = array();
		foreach( $params as $p => $v ){
			if( $v || ($v === 0) || ($v === '0') ){
				if( is_array($v) )
				{
					$v = join( '-', $v );
				}
				elseif( is_object($v) )
				{
					$v = $v->getId();
				}
				if( $v == '-reset-' )
					continue;

				$realP = ntsView::setRealName( $p );
				$linkParts[] = $realP . '=' . urlencode($v);
				}
			}

		if( $linkParts )
			$link = join( '&', $linkParts );
		else
			$link = '';
		return $link;
		}

	static function setRealName( $pName ){
		$return = $pName;
		$pref = 'nts-';
		if( substr($pName, 0, strlen($pref)) != $pref ){
			$return = $pref . $return;
			}
		return $return;
		}
	static function getRealName( $pName ){
		$return = $pName;
		$pref = 'nts-';
		if( substr($pName, 0, strlen($pref)) ==  $pref ){
			$return = substr($pName, strlen($pref));
			}
		return $return;
		}

	static function prepareUrlParams( $params = array() ){
		reset( $params );
		$linkParts = array();
		foreach( $params as $p => $v ){
			if( $v )
				$linkParts[] = $p . '=' . urlencode($v);
			}
		$link = join( '&', $linkParts );
		return $link;
		}

	static function addAdminAnnounce( $msg, $type = 'ok' ){
	// type might be 'error' or 'ok'
		if( ! isset($_SESSION['announce_text_admin']) ){
			$_SESSION['announce_text_admin'] = array();
			}
		$_SESSION['announce_text_admin'][] = array( $msg, $type );
		}
	static function setAdminAnnounce( $msg, $type = 'ok' ){
	// type might be 'error' or 'ok'
		$_SESSION['announce_text_admin'] = array( array( $msg, $type ) );
		}
	static function isAdminAnnounce(){
		$return = ( isset($_SESSION['announce_text_admin']) )? true : false;
		return $return;
		}
	static function getAdminAnnounceText(){
		if( isset($_SESSION['announce_text_admin']) ){
			$return = $_SESSION['announce_text_admin'];
			}
		else {
			$return = '';
			}
		return $return;
		}
	static function clearAdminAnnounce(){
		unset( $_SESSION['announce_text_admin'] );
		}

	static function addAnnounce( $msg, $type = 'ok', $order = 50 ){
	// type might be 'error' or 'ok'
		if( ! isset($_SESSION['announce_text']) ){
			$_SESSION['announce_text'] = array();
			}
		$_SESSION['announce_text'][] = array( $msg, $type, $order );
		}

	static function setAnnounce( $msg, $type = 'ok' ){
		ntsView::addAnnounce( $msg, $type );
		}
	static function isAnnounce(){
		$return = ( isset($_SESSION['announce_text']) )? true : false;
		return $return;
		}

	static function getAnnounceText(){
		if( isset($_SESSION['announce_text']) ){
			$return = $_SESSION['announce_text'];

		/* SORT BY ORDER */
			usort( $return, create_function('$a, $b', 'return ntsLib::numberCompare($a[2], $b[2]);' ) );
			}
		else {
			$return = '';
			}
		return $return;
		}

	static function getAnnounceType(){
		$return = ( isset($_SESSION['announce_type']) )? $_SESSION['announce_type'] : '';
		return $return;
		}

	static function clearAnnounce(){
		unset( $_SESSION['announce_text'] );
		unset( $_SESSION['announce_type'] );
		}
	}
?>
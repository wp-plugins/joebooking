<?php
global $NTS_CURRENT_USER;

$lm =& ntsLanguageManager::getInstance();
$etm =& ntsEmailTemplateManager::getInstance();
$om =& objectMapper::getInstance();

$defaultLanguage = $lm->getDefaultLanguage();
$userLang = $defaultLanguage;

$key = 'user-require_approval-user';
$templateInfo = $etm->getTemplate( $userLang, $key );

$tags = array( array(), array() );

if( ($NTS_CURRENT_USER->getId() < 1) && (isset($_SESSION['temp_customer_id'])) ){
	$tempCustomer = new ntsUser();
	$tempCustomer->setId( $_SESSION['temp_customer_id'] );
	$tags = $om->makeTags_Customer( $tempCustomer, 'external' );
	}
else {
	$tags = $om->makeTags_Customer( $NTS_CURRENT_USER, 'external' );
	}

$subject = str_replace( $tags[0], $tags[1], $templateInfo['subject'] );
$body = str_replace( $tags[0], $tags[1], $templateInfo['body'] );
?>
<p>
<H2><?php echo nl2br( $subject ); ?></H2>

<p>
<?php echo nl2br( $body ); ?>

<p>
<a href="<?php echo ntsLink::makeLink(); ?>"><?php echo M('Home'); ?></a> 

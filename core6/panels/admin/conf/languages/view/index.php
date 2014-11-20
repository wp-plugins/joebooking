<?php
/* language manager */
$lm =& ntsLanguageManager::getInstance();
$tm =& ntsEmailTemplateManager::getInstance();
$lng = $NTS_VIEW['lng'];

$languageConf = $lm->getLanguageConf( $lng );

/* default language  */
$defaultLanguageConf = $lm->getLanguageConf( 'languageTemplate' );

$missingInterface = array_diff( array_keys($defaultLanguageConf['interface']), array_keys($languageConf['interface']) );
$missingTemplates = array_diff( array_keys($defaultLanguageConf['templates']), array_keys($languageConf['templates']) );
?>

<H2>Front-End Language - <?php echo $languageConf['language']; ?></H2>

<p>
<table>
<tr>
	<td><b>Author</b></td>
	<td><?php echo $languageConf['author']; ?></td>
</tr>
<tr>
	<td><b>Last Update</b></td>
	<td><?php echo $languageConf['lastUpdate']; ?></td>
</tr>
</table>

<?php if( count($missingInterface) ) : ?>
	<p>
	<h3><?php echo count($missingInterface); ?> Missing Interface String(s)</h3>
	<p>
	<ul>
		<li>Copy the code below and paste into <b>extensions/languages/<?php echo $lng; ?>/interface.xml</b>, just before the ending <i><?php echo htmlspecialchars('</file>'); ?></i> tag.</li>
		<li>Enter your language translations between the <i><?php echo htmlspecialchars('<translate></translate>'); ?></i> tags</li>
	</ul>

<p>
<b>Click at the code to select all. Please make sure that you paste this code just before the ending <i><?php echo htmlspecialchars('</file>'); ?> tag</i></b>.

<p>
<textarea cols="64" rows="12" wrap="off" style="white-space:nowrap; overflow: scroll;" ONCLICK="this.focus();this.select();">
<?php
foreach( $missingInterface as $mi ){
$mi = htmlspecialchars( $mi );
$code =<<<EOT
<string>
	<original>$mi</original>
	<translate>$mi</translate>
</string>
EOT;

	$code = htmlspecialchars( $code );
	echo $code . "\n";
	}
?>
</textarea>
<?php elseif( count($missingTemplates) ) : ?>
	<p>
	<h3><?php echo count($missingTemplates); ?> Missing Email Template(s)</h3>
	<p>
	<ul>
		<li>Copy the code below and paste into <b>extensions/languages/<?php echo $lng; ?>/emails.xml</b>, just before the ending <i><?php echo htmlspecialchars('</emails>'); ?></i> tag.</li>
		<li>Enter your language translations between the <i><?php echo htmlspecialchars('<subject></subject>'); ?></i> and <i><?php echo htmlspecialchars('<body></body>'); ?></i> tags</li>
	</ul>

<p>
<b>Click at the code to select all. Please make sure that you paste this code just before the ending <i><?php echo htmlspecialchars('</emails>'); ?> tag</i></b>.

<p>
<textarea cols="64" rows="12" wrap="off" style="white-space:nowrap; overflow: scroll;" ONCLICK="this.focus();this.select();">
<?php
foreach( $missingTemplates as $mi ){
$template = $tm->getTemplate( 'en', $mi );
$subject = $template['subject'];
$body = $template['body'];

$mi = htmlspecialchars( $mi );
$code =<<<EOT
<message>
<key>$mi</key>
<subject>$subject</subject>
<body>
$body
</body>
</message>

EOT;

	$code = htmlspecialchars( $code );
	echo $code . "\n";
	}
?>
</textarea>
<?php endif; ?>

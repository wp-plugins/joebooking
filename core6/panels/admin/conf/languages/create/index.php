<?php
/* language manager */
$lm =& ntsLanguageManager::getInstance();

/* default language  */
$defaultLanguageConf = $lm->getLanguageConf( 'languageTemplate' );
?>
<H2>Create New Language File</H2>
<ul>
	<li>At your computer, create a new XML file and name it accordingly to the language, for example <b>es.xml</b>, <b>de.xml</b>, or <b>ru.xml</b>. Open this file in a text editor like Notepad or similar.
	<li>Paste the XML code below it into your new XML file at your computer.
	<li>Enter your translations between the <i>&lt;translate&gt;</i><i>&lt;/translate&gt;</i> tags (default English text is already there, replace it with your language strings). <b>DO NOT change the text between <i>&lt;original&gt;</i><i>&lt;/original&gt;</i> tags!</b>
	<li>If you need to use HTML code in the text strings, no problem, use it as needed, then replace the <i>&lt;</i> with <i>[</i>, and <i>&gt;</i> with <i>]</i>. For example, <i>&lt;b&gt;some text&lt;/b&gt;</i> becomes <i>[b]some text[/b]</i>. Please make sure that you do this otherwise the language file will not work.
	<li>Once you are done translating, upload your new laguage file to <b>extensions/languages</b> folder.
</ul>

<p>
<b>Here is the XML code to paste into your new language XML file. Click at the code to select all.</b>
<p>
<textarea cols="64" rows="12" wrap="off" style="white-space:nowrap; overflow: scroll;" ONCLICK="this.focus();this.select();">
<?php
$lastUpdate = date('j F, Y');
$code =<<<EOT
<?xml version="1.0" encoding="ISO-8859-1"?>
<file>
<language>Your Language</language>
<author>Your Name</author>
<lastUpdate>$lastUpdate</lastUpdate>

EOT;

$code = htmlspecialchars( $code );
echo $code . "\n";
?>
<?php
foreach( $defaultLanguageConf['interface'] as $original => $translate ){
$original = htmlspecialchars( $original );
$code =<<<EOT
<string>
	<original>$original</original>
	<translate>$original</translate>
</string>
EOT;
	$code = htmlspecialchars( $code );
	echo $code . "\n";
	}
?>
<?php
$code =<<<EOT
</file>
EOT;
$code = htmlspecialchars( $code );
echo $code . "\n";
?>
</textarea>

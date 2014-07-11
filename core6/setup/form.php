<?php
//echo "<p>File <b>db.php</b> found, trying to check the database settings ...";

/* check database information */
require( dirname(__FILE__) . '/../init_db.php' );

$tbPrefix = $db['dbprefix'] . 'v6_';
$wrapper = new ntsMysqlWrapper(
	$db['hostname'],
	$db['username'],
	$db['password'],
	$db['database'],
	$tbPrefix
	);

if( ! $wrapper->checkSettings() ){
	echo '<br>';
	echo $wrapper->getError();
	echo "<p>If it is an error with your MySQL login details, please fix this problem in the <b>db.php</b> file then try again.";
	exit;
	}
//echo '<span class="success">seems ok</span>';

/* check if it is already installed */
$installedVersion = '';
$currentTables = $wrapper->getTablesInDatabase();
if( in_array('conf', $currentTables) ){
	echo '<p><span style="color: #ff0000; font-size: 125%;">Application tables already exist, make sure you are not overwriting an existing database!</span>';
	}
?>
<SCRIPT LANGUAGE="JavaScript">
function checkForm(){
	if( ! document.conf_form.admin_username.value ){
		alert( 'Please enter the administrator username!' );
		return false;
		}
	if( ! document.conf_form.admin_pass.value ){
		alert( 'Please enter the administrator password!' );
		return false;
		}
	if( ! document.conf_form.admin_pass2.value ){
		alert( 'Please confirm the administrator password!' );
		return false;
		}
	if( ! document.conf_form.admin_email.value ){
		alert( 'Please enter the administrator email!' );
		return false;
		}
	if( document.conf_form.admin_pass2.value != document.conf_form.admin_pass.value ){
		alert( 'The entered passwords differ!' );
		return false;
		}
	return true;
	}
</SCRIPT>

<FORM METHOD="post" ID="conf_form" NAME="conf_form">
<input type="hidden" name="step" value="create">

<?php
// get scripts
$scripts = ntsLib::listFiles( dirname(__FILE__) . '/scripts' );
?>

<?php if( $scripts ) : ?>
<P>
<?php
$options = array();
reset( $scripts );
foreach( $scripts as $script ){
	$shortName = substr( $script, 0, -strlen('.php') );
	$displayName = ntsLib::upperCaseMe($shortName);
	$options[] = array( $shortName, $displayName );
	}
?>
<LABEL FOR="script">
<SPAN>Quick Configurations</SPAN>
<select name="script" id="script">
<?php foreach( $options as $opt ) : ?>
<option value="<?php echo $opt[0]; ?>"><?php echo $opt[1]; ?>
<?php endforeach; ?>
</select>
</LABEL>
<?php endif; ?>


<P>
<H3>Administrator Account</H3>

<LABEL FOR="admin_username">
<SPAN>Username</SPAN>
<INPUT TYPE="text" NAME="admin_username" VALUE="admin" SIZE="24" TABINDEX="3">
</LABEL>

<LABEL FOR="admin_pass">
<SPAN>Password</SPAN>
<INPUT TYPE="password" NAME="admin_pass" VALUE="" TABINDEX="4">
</LABEL>

<LABEL FOR="admin_pass2">
<SPAN>Repeat Password</SPAN>
<INPUT TYPE="password" NAME="admin_pass2" VALUE="" TABINDEX="5">
</LABEL>

<LABEL FOR="admin_email">
<SPAN>Email</SPAN>
<INPUT TYPE="text" NAME="admin_email" VALUE="admin@yoursiteaddress.com" SIZE="36" TABINDEX="6">
</LABEL>

<LABEL FOR="admin_fname">
<SPAN>First Name</SPAN>
<INPUT TYPE="text" NAME="admin_fname" VALUE="James" SIZE="24" TABINDEX="7">
</LABEL>

<LABEL FOR="admin_lname">
<SPAN>Last Name</SPAN>
<INPUT TYPE="text" NAME="admin_lname" VALUE="Brown" SIZE="24" TABINDEX="8">
</LABEL>

<LABEL FOR="go">
<SPAN>&nbsp;</SPAN>
<INPUT NAME="go" TYPE="button" VALUE="Proceed to setup" ONCLICK="return checkForm() && this.form.submit();" TABINDEX="9">
</LABEL>


<P>
</FORM>

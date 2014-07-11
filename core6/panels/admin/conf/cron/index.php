<?php
$reminderUrl = ntsLink::makeLinkFull( ntsLib::getFrontendWebpage(), '', '', array('nts-cron' => 1) );
?>
<p>
Please note that for some of the features like appointment reminders and automatic rejects, you will need to set up a <b>cron job</b>. Cron job is a process that runs periodically at your web server.
<p>
<ul>
	<li>Log in to your web hosting control panel and go to Cron Jobs</li>
	<li>Add a cron job that will be pulling the following command every hour:
	<p>
    <b>wget -O /dev/null '<?php echo $reminderUrl; ?>'</b>
	</li>
</ul>

<p>
<?php
$NTS_VIEW['form']->display();
?>
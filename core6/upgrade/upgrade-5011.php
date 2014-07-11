<?php
$ntsdb =& dbWrapper::getInstance();

$sql =<<<EOT

DROP TABLE IF EXISTS `{PRFX}dayhours`;
EOT;
$result = $ntsdb->runQuery( $sql );

/* alter appointments */
$sql =<<<EOT

ALTER TABLE 
	`{PRFX}appointments`
ADD COLUMN (
	`completed` tinyint NOT NULL DEFAULT 0
	)
EOT;
$result = $ntsdb->runQuery( $sql );

/* convert appointments */
$statusNoShow = HA_STATUS_NOSHOW;
$statusCancelled = HA_STATUS_CANCELLED;

$sql =<<<EOT
UPDATE 
	`{PRFX}appointments`
SET
	completed = $statusNoShow
WHERE
	no_show != 0
EOT;
$result = $ntsdb->runQuery( $sql );

$sql =<<<EOT
UPDATE 
	`{PRFX}appointments`
SET
	completed = $statusCancelled
WHERE
	cancelled != 0
EOT;
$result = $ntsdb->runQuery( $sql );

/* alter appointments */
$sql = "ALTER TABLE {PRFX}appointments DROP COLUMN `no_show`";
$result = $ntsdb->runQuery( $sql );
$sql = "ALTER TABLE {PRFX}appointments DROP COLUMN `cancelled`";
$result = $ntsdb->runQuery( $sql );

/* set complete appointments */
$t = new ntsTime;
$t->setNow();
$startDay = $t->getStartDay();
$statusCompleted = HA_STATUS_COMPLETED;
$sql =<<<EOT
UPDATE 
	`{PRFX}appointments`
SET
	completed = $statusCompleted
WHERE
	completed = 0 AND
	(starts_at + duration + lead_out) < $startDay
EOT;
$result = $ntsdb->runQuery( $sql );
?>
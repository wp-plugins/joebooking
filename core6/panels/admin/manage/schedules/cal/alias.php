<?php
$alias = 'admin/manage/cal';

$returnTo = ntsLink::makeLink( '-current-/..' );
$returnTo = '-current-/..';
ntsLib::setVar('admin/manage/cal::returnTo', $returnTo);
?>
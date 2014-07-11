<?php
$noteId = $_NTS['REQ']->getParam('noteid');
ntsLib::setVar( 'admin/notes/edit::noteId', $noteId );
?>
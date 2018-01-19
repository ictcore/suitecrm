<?php
// Ignore fields from export.
// give field name in uppercase in array.
// E.g :- $excludeColumns['Module-Name'] = array('FIELDNAME');
$excludeColumns['Calls'] = array('SET_COMPLETE');
$excludeColumns['Meetings'] = array('SET_COMPLETE');
$excludeColumns['Tasks'] = array('SET_COMPLETE');
$excludeColumns['Notes'] = array('FILENAME');
$excludeColumns['Campaigns'] = array('TRACK_CAMPAIGN','LAUNCH_WIZARD');
?>

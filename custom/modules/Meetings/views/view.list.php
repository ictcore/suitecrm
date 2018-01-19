<?php

// custom/modules/Meetings/views/view.list.php

require_once('include/MVC/View/views/view.list.php');
if (file_exists('custom/modules/Meetings/MeetingsListViewSmarty.php')) {
    require_once('custom/modules/Meetings/MeetingsListViewSmarty.php');
} else {
    require_once('modules/Meetings/MeetingsListViewSmarty.php');
}

class MeetingsViewList extends ViewList {

    function MeetingsViewList() {
        parent::ViewList();
    }

    function preDisplay() {
        $this->lv = new MeetingsListViewSmarty();
        // Enable Custom Export Feature
        global $db;
        $selectExportMod = "Select module from custom_ictbroadcast_modules_tbl where module ='{$this->module}'";
        $query = $db->query($selectExportMod);
        if ($query->num_rows > 0) {
            require_once 'modules/CE_custom_ictbroadcast/customIctbroadcastUtils.php';
            $this->lv->actionsMenuExtraItems[] = buildMyMenuItem($this->module);
        }
        // End
    }

}

?>

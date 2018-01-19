<?php

// custom/modules/Prospects/views/view.list.php

require_once('include/MVC/View/views/view.list.php');
if (file_exists('custom/modules/Prospects/ProspectsListViewSmarty.php')) {
    require_once('custom/modules/Prospects/ProspectsListViewSmarty.php');
} else {
    require_once('modules/Prospects/ProspectsListViewSmarty.php');
}

class ProspectsViewList extends ViewList {

    function LeadsViewList() {
        parent::ViewList();
    }

    function preDisplay() {
        $this->lv = new ProspectsListViewSmarty();

        // Bug: Missing "add to target list" entry in the action menu
        $this->lv->targetList = true;
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
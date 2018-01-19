<?php

// custom/modules/Opportunities/views/view.list.php

require_once('include/MVC/View/views/view.list.php');
if (file_exists('custom/modules/Opportunities/OpportunitiesListViewSmarty.php')) {
    require_once('custom/modules/Opportunities/OpportunitiesListViewSmarty.php');
} else {
    require_once('modules/Opportunities/OpportunitiesListViewSmarty.php');
}

class OpportunitiesViewList extends ViewList {

    function OpportunitiesViewList() {
        parent::ViewList();
    }

    function preDisplay() {
        $this->lv = new OpportunitiesListViewSmarty();
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
<?php

// custom/modules/Cases/views/view.list.php

require_once('include/MVC/View/views/view.list.php');
if (file_exists('custom/modules/Cases/CasesListViewSmarty.php')) {
    require_once('custom/modules/Cases/CasesListViewSmarty.php');
} else {
    require_once('modules/Cases/CasesListViewSmarty.php');
}

class CasesViewList extends ViewList {

    function CasesViewList() {
        parent::ViewList();
    }

    function preDisplay() {
        $this->lv = new CasesListViewSmarty();
        // Enable Custom Ictbroadcast Feature
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
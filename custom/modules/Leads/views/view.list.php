<?php

require_once('include/MVC/View/views/view.list.php');
if (file_exists('custom/modules/Leads/LeadsListViewSmarty.php')) {
    require_once('custom/modules/Leads/LeadsListViewSmarty.php');
} else {
    require_once('modules/Leads/LeadsListViewSmarty.php');
}

class LeadsViewList extends ViewList
{
    /**
     * @see ViewList::preDisplay()
     */
    public function preDisplay(){
        require_once('modules/AOS_PDF_Templates/formLetter.php');
        formLetter::LVPopupHtml('Leads');
        parent::preDisplay();

        $this->lv = new LeadsListViewSmarty();
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

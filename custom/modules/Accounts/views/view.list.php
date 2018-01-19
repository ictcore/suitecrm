<?php

require_once('include/MVC/View/views/view.list.php');
if (file_exists('custom/modules/Accounts/AccountsListViewSmarty.php')) {
    require_once('custom/modules/Accounts/AccountsListViewSmarty.php');
} else {
    require_once('modules/Accounts/AccountsListViewSmarty.php');
}

class AccountsViewList extends ViewList
{
    /**
     * @see ViewList::preDisplay()
     */
    public function preDisplay(){
        require_once('modules/AOS_PDF_Templates/formLetter.php');
        formLetter::LVPopupHtml('Accounts');
        parent::preDisplay();

        $this->lv = new AccountsListViewSmarty();
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

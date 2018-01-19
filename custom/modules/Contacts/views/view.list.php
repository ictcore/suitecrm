<?php

require_once('include/MVC/View/views/view.list.php');
if (file_exists('custom/modules/Contacts/ContactsListViewSmarty.php')) {
    require_once('custom/modules/Contacts/ContactsListViewSmarty.php');
} else {
    require_once('modules/Contacts/ContactsListViewSmarty.php');
}

class ContactsViewList extends ViewList
{
    /**
     * @see ViewList::preDisplay()
     */
    public function preDisplay(){
        require_once('modules/AOS_PDF_Templates/formLetter.php');
        formLetter::LVPopupHtml('Contacts');
        parent::preDisplay();

        $this->lv = new ContactsListViewSmarty();
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

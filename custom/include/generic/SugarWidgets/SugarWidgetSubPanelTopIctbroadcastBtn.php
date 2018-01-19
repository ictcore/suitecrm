<?php

if (!defined('sugarEntry') || !sugarEntry)
    die('Not A Valid Entry Point');

require_once('include/generic/SugarWidgets/SugarWidgetSubPanelTopButton.php');

class SugarWidgetSubPanelTopIctbroadcastBtn extends SugarWidgetSubPanelTopButton {

    function display($widget_data) {
        global $app_strings;
        global $currentModule;

        $focus = $widget_data['focus'];
        $title = 'Ictbroadcast';

        if (ACLController::moduleSupportsACL($widget_data['module']) && !ACLController::checkAccess($widget_data['module'], 'ictbroadcast', true)) {
            $button = ' <input type="button" name="" id="" class="button"' . "\n"
                    . ' title="No export access"'
                    . ' value="No export access"' . "\n"
                    . ' disabled />';
            return $button;
        }

        $link = 'index.php?module=CE_custom_ictbroadcast&action=ictbroadcastSubPanel&format=excel&parent_module='
                . $currentModule . '&subpanel='
                . $widget_data['subpanel_definition']->name
                . '&record=' . $focus->id . '&search=' . $_REQUEST['search'];

        return ' <input type="button" name="" id="" class="button"' . "\n"
                . ' title="' . $title . '"'
                . ' value="' . $title . "\"\n"
                . " onclick='document.location=\"$link\"' />\n";
    }

}

?>
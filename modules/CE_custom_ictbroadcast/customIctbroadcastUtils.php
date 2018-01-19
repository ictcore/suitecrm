<?php

function buildMyMenuItem($module_name) {
    global $app_strings, $sugar_version;
    if (preg_match('/(6\.3\.[0-9])/', $sugar_version) || preg_match('/(6\.4\.[0-9])/', $sugar_version)) {
      /*  $script = ' <a onmouseover="hiliteItem(this,\'yes\');" onmouseout="unhiliteItem(this);" style="width: 150px" class="menuItem" href="javascript:void(0)" ' .
                'onclick="return sListView.send_form(true, \'' . $module_name .
                '\', \'index.php?entryPoint=ictbroadcastCustom&ictbroadcastOpt=excel\',\'' . $app_strings['LBL_LISTVIEW_NO_SELECTED'] . '\')">Custom ictbroadcast</a>';*/
                
                
                $script = ' <a onmouseover="hiliteItem(this,\'yes\');" onmouseout="unhiliteItem(this);" style="width: 150px" class="menuItem" href="javascript:void(0)" ' .
                'onclick="return sListView.send_form(true, \'' . $module_name .
                '\', \'index.php?module=CE_custom_ictbroadcast&action=create_campaign_ictbroadcast\',\'' . $app_strings['LBL_LISTVIEW_NO_SELECTED'] . '\')">Custom ictbroadcast</a>';

        return $script;
    } else {
        $script = ' <a href="javascript:void(0)" ' .
                'onclick="return sListView.send_form(true, \'' . $module_name .
                '\', \'index.php?entryPoint=ictbroadcastCustom&ictbroadcastOpt=campaign\',\'' . $app_strings['LBL_LISTVIEW_NO_SELECTED'] . '\')">Custom Ictbroadcast</a>';
                
                /* $script = ' <a href="index.php?module=CE_custom_ictbroadcast&action=create_campaign_ictbroadcas" ' .
                 $app_strings['LBL_LISTVIEW_NO_SELECTED'] . '\')">Custom Ictbroadcast</a>';*/

        return $script;
    }
}

function enableCustomIctbroadcastInSubpanel($module, $subpanels = array()) {
    require_once 'include/SubPanel/SubPanelTiles.php';
    $focus = BeanFactory::getBean($module);
    $subpanel = new SubPanelTiles($focus, $module);
    $ExportBtn = array(
        array('widget_class' => 'SubPanelTopIctbroadcastBtn')
    );
    foreach ($subpanels as $subpanel_setup) {
        $supanel_Details = array();
        if (!is_null($subpanel->subpanel_definitions->layout_defs['subpanel_setup'][$subpanel_setup]['top_buttons'])) {
            $supanel_Details = $subpanel->subpanel_definitions->layout_defs['subpanel_setup'][$subpanel_setup]['top_buttons'];
        }
        $subpanel_AllExportBtn = array_merge($supanel_Details, $ExportBtn);

        $fileContent = '';
        $fileContent .= '<?php 
            if (!defined("sugarEntry") || !sugarEntry)
    die("Not A Valid Entry Point");     
    
    $layout_defs["' . $module . '"]["subpanel_setup"]["' . $subpanel_setup . '"]["top_buttons"]
';

        $contect = 'array(';
        foreach ($subpanel_AllExportBtn as $key => $array) {
            $contect .= $key . ' => array(';
            foreach ($array as $widget => $button) {
                $contect .='"' . $widget . '" => "' . $button . '",';
            }
            $contect .='),';
        }
        $contect .='); ?>';
        if (!file_exists('custom/Extension/modules/' . $module . '/Ext/Layoutdefs')) {
            mkdir('custom/Extension/modules/' . $module . '/Ext/Layoutdefs', 0777, true);
        }
        $file_path = 'custom/Extension/modules/' . $module . '/Ext/Layoutdefs/_override' . $subpanel_setup . '_customIctbroadcastBtn.php';
        if (!file_exists($file_path)) {
        $file = fopen($file_path, "w");
        fwrite($file, $fileContent . ' = ' . $contect);
        chmod($file_path, 0777);
        fclose($file);
    }
}
}

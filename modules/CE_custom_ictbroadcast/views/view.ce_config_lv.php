<style type="text/css">
table.custom-module-table {background: #fff;  border: 1px solid #ddd; border-bottom:none; border-right:none;   box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);  padding: 0;  margin: 10px 0;  width: 100%;}
table.custom-module-table td{border-bottom: 1px solid #ddd;  border-right: 1px solid #ddd;   padding: 10px; font-size: 12px;}
table.custom-module-table td:hover { background: #fbfbfb;}
table.custom-module-table td input { margin-right: 15px;}
table.custom-module-table th{ background: #f6f6f6;   border-bottom: 1px solid #ddd; border-right: 1px solid #ddd; text-align: left; font-size: 14px;    font-weight: bold;    margin: 0;    padding: 10px;    text-shadow: 1px 1px #fff;    }

td.btn-bar { padding-top: 10px;}
.btn-bar .blue-btn:hover{box-shadow: 0 -2px 0 rgba(0, 0, 0, 0.27) inset; font-weight: bold; font-family: Arial,Verdana,Helvetica,sans-serif; }
.btn-bar .blue-btn{-webkit-border-radius:2px;     -moz-border-radius:2px;    border-radius:2px;    -webkit-box-shadow:0 1px 0 rgba(0,0,0,0.05);    -moz-box-shadow:0 1px 0 rgba(0,0,0,0.05);    box-shadow:0 1px 0 rgba(0,0,0,0.05);
 -webkit-box-sizing:border-box;    -moz-box-sizing:border-box;    box-sizing:border-box;    -webkit-transition:all .5s;    -moz-transition:all .5s;
 -o-transition:all .5s;    transition:all .5s;    -webkit-user-select:none;    -moz-user-select:none;    -ms-user-select:none;    background: none repeat scroll 0 0 rgba(0, 0, 0, 0);    border: 1px solid #4e8ccf;
 color: #4e8ccf;    padding: 6px 15px; font-weight: bold; font-family: Arial,Verdana,Helvetica,sans-serif; }

</style>
<?php
if (!defined('sugarEntry') || !sugarEntry) {
    die('Not A Valid Entry Point');
}
require_once('include/MVC/View/SugarView.php');

class CE_custom_ictbroadcastViewCE_config_lv extends SugarView {

    function display() {
        global $db, $app_list_strings, $current_user;
        require_once('modules/MySettings/TabController.php');
        $controller = new TabController();
        $tabArray = $controller->get_tabs($current_user);
        $all_enableModules = $tabArray[0];
        $excluded_modules = array('Home', 'Calendar', 'Bugs', 'Emails','KBDocuments', 'Forecasts', 'Iframeapp');
        $saved_module_array = $saved_module_row = array();

        //Get saved module preference
        $select_query = "SELECT module,id FROM custom_ictbroadcast_modules_tbl";
        $saved_module_result = $db->query($select_query);
        while ($saved_module_row = $db->fetchByAssoc($saved_module_result)) {
            $saved_module_array[$saved_module_row['id']] = $saved_module_row['module'];
        }

        $html = '';
        if (!empty($_REQUEST['msg']) && $_REQUEST['msg'] == 'success') {
            $html = "<div style='margin:10px;text-align:center'><span style='color:green;font-weight:bold;'> Changes saved successfully.</span></div>";
        }
        $html .= '<tr>
            <td colspan="3" class="btn-bar">
                <input type="button" class="blue-btn" name="selectAllModules" id="selectAllModules" value="Select All" onclick="checkAllmodules();">
                <input type="button" class="blue-btn"  name="deselectAllModules" id="deselectAllModules" value="Deselect All" onclick="uncheckAllModules();">
            </td>
        </tr>';
        $html .= "<form name='ictbroadcast_module' method='post' action='index.php?module=CE_custom_ictbroadcast&action=saveExportModule'>
                 <table width='100%' cellspacing='0' cellpadding='0' id='ModuleTable' class='custom-module-table'><tr><th colspan='4'>Select modules to enable custom export feature for module list-views.</th></tr><tr>";
        $currentColumn = 0;
        $maxColumn = 4;
        sort($all_enableModules);
        foreach ($all_enableModules as $module) {
            if (!in_array($module, $excluded_modules)) {
                if (in_array($module, $saved_module_array)) {
                    $checked = "checked=checked";
                } else {
                    $checked = "";
                }
                if ($currentColumn < $maxColumn) {
                    $html .= "<td><input type='checkbox' name='chkmodule[]' value='{$module}' {$checked}>{$app_list_strings['moduleList'][$module]}</td>";
                    $currentColumn++;
                } else {
                    $html .= "</tr><tr><td><input type='checkbox' name='chkmodule[]' value='{$module}' {$checked}>{$app_list_strings['moduleList'][$module]}</td>";
                    $currentColumn = 1;
                }
            }
        }
        if ($currentColumn < $maxColumn) {
            $diff = $maxColumn - $currentColumn;
        }
        for ($i = 0; $i < $diff; $i++) {
            $html .= "<td> &nbsp;</td>";
        }
        $html .= '</tr></table>';
        $html .= '<div class="btn-bar">
                        <input type="submit" class="blue-btn" name="save" value="Save" class="button" >
                        <input type="button" class="blue-btn" name="cancel" value="Cancel" class="button" onclick="redirectToindex();">
                 </div></form>';

        parent::display();
        echo $html;

        echo '
            <script type="text/javascript">
            function redirectToindex(){
                location.href = "index.php?module=Administration&action=index";
            }
            function checkAllmodules(){
                  var inputs =   document.getElementById("ModuleTable").getElementsByTagName("input");
                    for (var i = 0; i < inputs.length; i++) {
                        if(inputs[i].name.indexOf("chkmodule[]") == 0) {
                                inputs[i].checked =  true;
                        }
                    }
            }
            function uncheckAllModules(){
                  var inputs =   document.getElementById("ModuleTable").getElementsByTagName("input");
                    for (var i = 0; i < inputs.length; i++) {
                        if(inputs[i].name.indexOf("chkmodule[]") == 0) {
                                inputs[i].checked =  false;
                        }
                    }
            }       
                      
         </script>';
    }

}

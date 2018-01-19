<style type="text/css">
    .module-row {background: #fff;   border: 1px solid #ddd;    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);   display: inline-block;    margin: 10px 0;    padding: 0;    width: 100%;}
    .module-row .label {background: #F6F6F6;  border-bottom: 1px solid #ddd;   display: inline-block;    font-size: 14px;    font-weight: bold;    margin: 0;    padding: 10px 1%;    text-shadow: 1px 1px #fff;    width: 98%;}
    .module-row ul{margin: 0;  padding: 1%;   width: 98%;}
    .module-row li {  display: inline-block;   font-size: 12px;    margin: 0;   padding: 4px 0;   width: 20%;}
    .module-row li input { margin-right: 10px;}
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

class CE_custom_ictbroadcastViewCE_Integration extends SugarView {

     function display() {


        global $db, $app_list_strings,$current_user;
        require_once 'include/SubPanel/SubPanelTiles.php';
        require_once('modules/MySettings/TabController.php');
        $controller = new TabController();
        $tabArray = $controller->get_tabs($current_user);
        $all_enableModules = $tabArray[0];
        $hidsubpanels_arr = SubPanelDefinitions::get_hidden_subpanels();
        if(is_null($hidsubpanels_arr)){
            $hidsubpanels_arr = array();
        }
        $excluded_modules = array('Home', 'Calendar', 'Bugs', 'Emails', 'Tasks', 'Notes', 'Campaigns', 'Calls', 'Meetings');
        sort($hidsubpanels_arr);
        $excluded_subpanels = array('activities', 'history', 'therevisions', 'campaigns');
        $excluded_all_subpanels = array_merge($excluded_subpanels, $hidsubpanels_arr);
        $saved_subpanel_array = array();
        //Get saved subpanel preference
        $select_query_sub = "SELECT * FROM custom_ictbroadcast_integration_tbl";
        $saved_integration_result = $db->query($select_query_sub);
        $saved_integration_row = $db->fetchByAssoc($saved_integration_result);

        $link = $saved_integration_row['link'];
        $key = $saved_integration_row ['token'];


        /*while ($saved_subpanel_row = $db->fetchByAssoc($saved_subpanel_result)) {
            $saved_subpanel_array[$saved_subpanel_row['module']] = $saved_subpanel_row['subpanel'];
        }*/

        $html = "<div style='font-size: 12pt;font-weight: bold;padding-bottom: 8px;'>Enable custom ictbroadcast Integration.</div>";
        if (!empty($_REQUEST['msg']) && $_REQUEST['msg'] == 'success') {
            $html = "<div style='margin:10px;text-align:center'><span style='color:green;font-weight:bold;'> Changes saved successfully.</span></div>";
        }
        //saveIntegration
        $html .= "<form name='exportSubpanelForm' method='post' action='index.php?module=CE_custom_ictbroadcast&action=saveExportSubpanel'>
                ";
        $html .="<div class='cell form-group' ><label class='control-label'  for='link'>Link<span class='required'>*</span></label><div class='field' data-name='link'><input name='link' class='form-control' value='{$link}' title='' type='text'></div></div>";
        $html .="<div class='cell form-group' ><label class='control-label'  for='key'>Key<span class='required'>*</span></label><div class='field' data-name='group'><textarea name='key' class='form-control' >{$key}</textarea></div></div>";

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
           </script>';
    }

}
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
    .le_field{
        width: 150px;
        float: left;
        border: 1px solid grey;
        background: #eee;
        padding: 2px;
        margin: 2px 3px;
        overflow: hidden;
        clear: both;
        display: block;
    }
    .layout_div{
        border: 1px solid grey;
        min-height: 500px;
        float: left;
        min-width: 190px;
        margin-left : 25px;
    }
    .sel-field-div{width:290px; position: relative; padding:2px 23px 2px 3px !important}
    .sel-field-div img {position: absolute;right: 3px; top: 3px; z-index: 111;cursor: pointer;}
    .dp-custom{border-color: #ccc; height: 30px; line-height: 0; margin-left: 10px; width: 150px;}
    .dp-custom:focus{border-color: #000;}

</style>
<?php
if (!defined('sugarEntry') || !sugarEntry) {
    die('Not A Valid Entry Point');
}
require_once('include/MVC/View/SugarView.php');
require_once ('modules/ModuleBuilder/parsers/ParserFactory.php');
require_once ('modules/ModuleBuilder/MB/AjaxCompose.php');
require_once 'modules/ModuleBuilder/parsers/constants.php';

class CE_custom_exportViewCE_config_exportfields_sub extends SugarView {

    function display() {
        $smarty = new Sugar_Smarty();
        global $mod_strings, $current_user,$app_list_strings;
        $smarty->display('modules/ModuleBuilder/tpls/includes.tpl');

        require_once('modules/MySettings/TabController.php');
        require_once 'include/SubPanel/SubPanelTiles.php';
        $controller = new TabController();
        $tabArray = $controller->get_tabs($current_user);
        $modules = $tabArray[0];
        $hidsubpanels_arr = SubPanelDefinitions::get_hidden_subpanels();
        if (is_null($hidsubpanels_arr)) {
            $hidsubpanels_arr = array();
        }
        $excluded_modules = array('Home', 'Calendar', 'Bugs', 'Emails', 'KBDocuments', 'Forecasts', 'Iframeapp', 'Tasks', 'Notes', 'Campaigns', 'Calls', 'Meetings');
        $excluded_subpanels = array('activities', 'history', 'therevisions', 'campaigns');
        $excluded_all_subpanels = array_merge($excluded_subpanels, $hidsubpanels_arr);
        sort($modules);
        $html = '';
        if (!empty($_REQUEST['msg']) && $_REQUEST['msg'] == 'success') {
            $html .= "<div style='margin:10px;text-align:center'><span style='color:green;font-weight:bold;'> Changes saved successfully.</span></div>";
        }
        $html .= "<form name='export_module'>";
        $html .= "<div class='btn-bar'>
                        <input type='button' class='blue-btn button' name='save' value='Save' onclick='callActionForSaveExportFieldsSubpanel();' >
                        <input type='button' class='blue-btn' name='cancel' value='Cancel' class='button' onclick='redirectToindex();'>
                 </div>";

        $html .= "<table width='100%' cellspacing='0' cellpadding='0' id='ModuleTable' class='custom-module-table'>
                 <tr>
                 <th colspan='2'>
                 Select module and its subpanel to enable custom export fields for subpanel list-views.
                 </th>
                 </tr>
                 <tr>";
        $html .= "<td colspan='2'><lable><strong>Select module:   </lable></strong><select class='dp-custom' name='modules' id='modules' onchange='getSubpanels(this);'>
                    <option value=''>Select Module</option> ";
        foreach ($modules as $module) {
            if (!in_array($module, $excluded_modules)) {
                $focus = BeanFactory::getBean($module);
                $subpanel = new SubPanelTiles($focus, $module);
                $display_SubpanelsInMod = array();
                $subpanel_details = is_array($subpanel->subpanel_definitions->layout_defs['subpanel_setup']) ? $subpanel->subpanel_definitions->layout_defs['subpanel_setup'] : array($subpanel->subpanel_definitions->layout_defs['subpanel_setup']);
                foreach ($subpanel_details as $subpanel_name => $defs) {
                    if (!in_array($subpanel_name, $excluded_all_subpanels)) {
                        $sub_type = $defs['type'];
                        if (is_null($sub_type) || (!is_null($sub_type) && $sub_type != 'collection')) {
                            if (!empty($defs['title_key'])) {
                        $display_SubpanelsInMod[$defs['title_key']] = $subpanel_name;
                    }
                }
                    }
                }
                if (!empty($display_SubpanelsInMod)) {
                    $html .= "<option value='{$module}'>{$app_list_strings['moduleList'][$module]}</option>";
                }
            }
        }
        $html .= "</select>";
        $html .= "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<lable><strong>Select subpanel:   </lable></strong><select class='dp-custom' name='subpanel' id='subpanel' >
                    <option value=''>Select Subpanel</option></select></td>";
        $html .= "</tr></table>";
        $html .= "<div class='btn-bar'>
                        <input type='button' class='blue-btn' name='save' value='Save' class='button' onclick='callActionForSaveExportFieldsSubpanel();'>
                        <input type='button' class='blue-btn' name='cancel' value='Cancel' class='button' onclick='redirectToindex();'>
                 </div></form>";
        echo $html;
        echo "<script type='text/javascript'>
            function loadScript(url, callback) {
            var script = document.createElement('script')
            script.type = 'text/javascript';
            if (script.readyState) { //IE
                script.onreadystatechange = function () {
                    if (script.readyState == 'loaded' || script.readyState == 'complete') {
                        script.onreadystatechange = null;
                        callback();
                    }
                };
            } else { //Others
                script.onload = function () {
                    callback();
                };
            }
            script.src = url;
            document.getElementsByTagName('head')[0].appendChild(script);
        }
        loadScript('http://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js', function () {
                $.getScript(\"http://ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/jquery-ui.js\");
                $('document').ready(function(){
                    $('#subpanel').change(function(){
                        $('#layouts').html('');
                        var module = $('#modules').val();
                        var subpanel_module = $('#subpanel').val();
                        var view_type = 'subpanel';
                        get_fieldsSubpanel(module,view_type,subpanel_module)
                        var layOutLength = $('#layouts').length;
                        if(layOutLength == 0){
                          $('#subpanel').after('<div id=\"layouts\" style=\"margin:10px\"></div>');
                        }
                        if(subpanel_module != ''){
                        $('#layouts').append('<div id=\"fieldlist\" style=\"float: left;border: 1px solid;min-height:529px;min-width:158px;\"></div>') ;
                        }
                    });
                });
         });
                function getSubpanels(el){
                    var module = el.value;
                    $('#layouts').html('');
                   $.ajax({
                            url: 'index.php?module=CE_custom_export&action=getSubpanels',
                            type: 'POST',
                            data: {'module_name': module},
                            success:function(data){
                               $('#subpanel').html(data);
                            }
                            });
                }
         
                function get_fieldsSubpanel(module,view_type,subpanel_module){
                var subpanel_module_Text = $('#subpanel option:selected').text();
                      $.ajax({
                            url: 'index.php?module=CE_custom_export&action=getModuleFields',
                            type: 'POST',
                            data: {'module_name': module,'view_type':view_type,'subpanel_module':subpanel_module},
                            success:function(data){
                                $('#fieldlist').html('');
                                var field_defs = $.parseJSON(data);
                                var counter = 1;
                                var html = '<div class=\"le_row special\" id=\"1013\" style=\"display: block;\"><span class=\"panel_name\" style=\"text-align: center;\">'+subpanel_module_Text+' Fields</span><div class=\"le_field special\" id=\"1014\"><span>(filler)</span><span class=\"field_name\">(filler)</span></div><div class=\"le_field special\" id=\"1015\"><span>(filler)</span><span class=\"field_name\">(filler)</span></div></div>';
                                $.each( field_defs['allFields'], function( key, value ) {
                                    if(value.label_value != null)
                                    html += '<div class=\"le_field field-div ui-state-default draggable\" id=\"'+counter+'\"><div><span id=\"le_label_'+counter+'\">'+value.label_value+'</span> <span class=\"field_name\">'+value.name+'</span> <span class=\"field_label\">'+value.vname+'</span> <span id=\"le_tabindex_'+counter+'\" class=\"field_tabindex\"></span></div></div>';
                                    counter++;
                                });
                                var html1 = '';
                                $.each( field_defs['exportFields'], function( key, value ) {
                                    if(value.label_value != null)
                                    html1 += '<div class=\"le_field sel-field-div\" ><div><span id=\"le_label\">'+value.label_value+'</span> <span class=\"field_name\">'+value.name+'</span> <span class=\"field_label\">'+value.vname+'</span> <span id=\"le_tabindex\" class=\"field_tabindex\"></span></div><img onclick=\"removeElement(this);\" src=\"modules/CE_custom_export/image/delete.gif\"></div>';
                                    counter++;
                                });
                                $('#fieldlist').append(html);
                                $('#fieldlist').after('<div id=\"fieldlayout\" class=\"layout_div\"><div class=\"panel_name\" style=\"width: 97%;position: relative;text-align: center;\">Export Fields List</div><div id=\"droppable\" style=\"min-height: 500px;margin-top: 0;width: 100%;\">'+html1+'</div></div>');
                                $.getScript(\"modules/CE_custom_export/js/layout-set.js\");
                            }
                        });

                }
                function callActionForSaveExportFieldsSubpanel(){
                  var exportFieldsArray = new Array();
                  var sel_module = $('#modules').val();
                  
                  var subpanel_module = $('#subpanel').val();
                  if(sel_module != '' && subpanel_module != ''){
                  $('#droppable').find('.le_field').find('div').each(function(){
                     var exportFields = $(this).find('.field_name').text();
                     exportFieldsArray.push(exportFields);
                  });
                  $.ajax({
                     url:'index.php?module=CE_custom_export&action=storeSelectedExportFieldsSubPanel',
                     type:'POST',
                     data:{'sel_module':sel_module,'subpanel_module':subpanel_module,'exportFieldsArray':exportFieldsArray},
                     success:function(data){
                       location.href = 'index.php?module=CE_custom_export&action=ce_config_exportfields_sub&msg=success';
                     }
                  });
                  }else{
                     alert('Please select module and subpanel before save');
                  }
                }
                function redirectToindex(){
                location.href = 'index.php?module=Administration&action=index';
            }
              </script>";
        parent::display();
    }

}

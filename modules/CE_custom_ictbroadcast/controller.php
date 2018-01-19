<?php

if (!defined('sugarEntry') || !sugarEntry)
    die('Not A Valid Entry Point');

require_once 'include/MVC/Controller/SugarController.php';

class CE_custom_ictbroadcastController extends SugarController {

    public function action_ce_config_lv() {
        global $current_user;
        if (!is_admin($current_user)) {
            sugar_die($GLOBALS['app_strings']['ERR_NOT_ADMIN']);
        }
        $this->view = 'ce_config_lv';
        $GLOBALS['view'] = $this->view;
    }

    
    
     public function action_create_campaign_ictbroadcast() {
     
       //echo "IctbroadcastSubPanelListtestidn";exit;
        global $current_user;
         if (!is_admin($current_user)) {
            sugar_die($GLOBALS['app_strings']['ERR_NOT_ADMIN']);
        }
      //  echo "dsdsds";
        $this->view = 'ce_edit';
        
      
        
    }
    
     public function action_ce_config_sp() {


        global $current_user;
        if (!is_admin($current_user)) {
            sugar_die($GLOBALS['app_strings']['ERR_NOT_ADMIN']);
        }

        $this->view = 'ce_integration';
        // $this->view = 'ce_config_sp';
        //$GLOBALS['view'] = $this->view;
    }


function action_saveExportSubpanel() {
        require_once 'modules/CE_custom_ictbroadcast/customIctbroadcastUtils.php';
        global $db;
        //$selectModAndSubpanelArr = $_REQUEST['chksubpanel'];
        $date = TimeDate::getInstance()->nowDb();
        $select_query = "SELECT * FROM custom_ictbroadcast_integration_tbl";
        $query = $db->query($select_query);
        $key = $_REQUEST['key'];

          $link = $_REQUEST['link'];
        

        $delete_query = "DELETE FROM custom_ictbroadcast_integration_tbl";
        $db->query($delete_query);
        $insert_query = "INSERT INTO custom_ictbroadcast_integration_tbl
                                        (
                                         link,
                                         token
                                         )
                            VALUES (
                                    '{$link}',
                                    '{$key}'
                                    )";
            $db->query($insert_query);
           // enableCustomIctbroadcastInSubpanel($module, $subpanels);
        //}
        header("Location: index.php?module=CE_custom_ictbroadcast&action=ce_config_sp&msg=success");
        exit;
    }

 function action_saveExportModule() {
        global $db;
        $modules = $_REQUEST['chkmodule'];
        $date = TimeDate::getInstance()->nowDb();
        $delete_query = "DELETE FROM custom_ictbroadcast_modules_tbl";
        $db->query($delete_query);
        foreach ($modules as $module) {
            $id = create_guid();
            $insert_query = "INSERT INTO custom_ictbroadcast_modules_tbl
                                        (id,
                                         module,
                                         date_created)
                            VALUES ('{$id}',
                                    '{$module}',
                                    '{$date}')";
            $db->query($insert_query);
        }
        header("Location: index.php?module=CE_custom_ictbroadcast&action=ce_config_lv&msg=success");
        exit;
    }



   /* public function action_ce_config_ictbroadcastfields() {
        global $current_user;
        if (!is_admin($current_user)) {
            sugar_die($GLOBALS['app_strings']['ERR_NOT_ADMIN']);
        }
        $this->view = 'ce_config_exportfields';
        $GLOBALS['view'] = $this->view;
    }

    public function action_ce_config_ictbroadcastfields_sub() {
        global $current_user;
        if (!is_admin($current_user)) {
            sugar_die($GLOBALS['app_strings']['ERR_NOT_ADMIN']);
        }
        $this->view = 'ce_config_ictbroadcastfields_sub';
        $GLOBALS['view'] = $this->view;
    }

    public function action_editview() {
        global $current_user;
        if (!is_admin($current_user)) {
            sugar_die($GLOBALS['app_strings']['ERR_NOT_ADMIN']);
        }
        $this->view = 'noaccess';
        $GLOBALS['view'] = $this->view;
    }

    public function action_listview() {
        global $current_user;
        if (!is_admin($current_user)) {
            sugar_die($GLOBALS['app_strings']['ERR_NOT_ADMIN']);
        }
        $this->view = 'noaccess';
        $GLOBALS['view'] = $this->view;
    }

    public function action_detailview() {
        global $current_user;
        if (!is_admin($current_user)) {
            sugar_die($GLOBALS['app_strings']['ERR_NOT_ADMIN']);
        }
        $this->view = 'noaccess';
        $GLOBALS['view'] = $this->view;
    }

   

   
    

    //For exporting data from subpanel
    function action_exportSubPanel() {
        global $app_strings, $db, $beanList;

        $module = $_REQUEST['parent_module'];
        $subpanel = $_REQUEST['subpanel'];
        $record = $_REQUEST['record'];
        $format = $_REQUEST['format'];
        $focus = BeanFactory::getBean($module);
        if ($focus->bean_implements('ACL')) {
            if (!ACLController::checkAccess($focus->module_dir, 'ictbroadcast', true)) {
                ACLController::displayNoAccess();
                sugar_die('');
            }
        }

        $focus->retrieve($record); // get the seed record for parent bean

        require_once('include/SubPanel/SubPanelDefinitions.php');
        $subpanel_def = new SubPanelDefinitions($focus);
        $panel = $subpanel_def->load_subpanel($subpanel);

        // New changes regarding selected fields for export

        $subpanel_module = $panel->_instance_properties['module'];
        $module_obj = new $beanList[$subpanel_module]();
        $field_defination = $module_obj->getFieldDefinitions();
        $getExportFieldsFromDB = "Select sequence,export_fields from ce_custom_ictbroadcast where module = '{$module}' and subpanel_module = '{$subpanel_module}' and view_type='subpanel' order by sequence asc";
        $query = $db->query($getExportFieldsFromDB);
        $exportFieldsArr = array();
        while ($getFields = $db->fetchByAssoc($query)) {
            $exportFieldsArr[$getFields['export_fields']]['name'] = $field_defination[$getFields['export_fields']]['name'];
            $exportFieldsArr[$getFields['export_fields']]['vname'] = $field_defination[$getFields['export_fields']]['vname'];
        }

        if (!empty($exportFieldsArr)) {
            $panel->panel_definition['list_fields'] = $exportFieldsArr;
        }
// End 
        //load language for target module
        $targetModuleLang = return_module_language('en_us', $panel->template_instance->module_dir);

        //include custom created listview spific for subpanel export
        require_once 'modules/CE_custom_ictbroadcast/IctbroadcastSubPanelList.php';
        $exportList = new ExportSubPanelList();
        $exportObjects = $exportList->process_dynamic_listview($module, $focus, $panel);

        //Convert all record objects into array
        $exportListRows = array();
        foreach ($exportObjects as $objId => $object) {
            $exportListRows[] = $object->get_list_view_data();
        }
        // ignore coloumn from subpanel 
        if (file_exists('modules/CE_custom_ictbroadcast/ce_ignorefields_sp.php')) {
            require_once('modules/CE_custom_ictbroadcast/ce_ignorefields_sp.php');
        } else {
            $excludeColumns = array();
        }
        //Transform all columns in to specific cols. defined in subpanel defs for subpanel 
        $finalExportData = array();
        $ignoreFieldArray = array('EDIT_BUTTON', 'REMOVE_BUTTON');
        $getignoreColumnFromArray = array();
        $excludeColumnsArray = (is_null($excludeColumns[$module][$subpanel])) ? array() : $excludeColumns[$module][$subpanel];
        foreach ($excludeColumnsArray as $key => $hidefields) {
            $getignoreColumnFromArray[] = $hidefields;
        }


        $removeFieldsFromExport = array_merge($getignoreColumnFromArray, $ignoreFieldArray);
        foreach ($exportListRows as $id => $exportListRow) {
            foreach ($panel->panel_definition['list_fields'] as $fieldName => $options) {
                if (!is_null($targetModuleLang[$options['vname']])) {
                    $label = $targetModuleLang[$options['vname']];
                } else {
                    if (!is_null($app_strings[$options['vname']])) {
                        $label = $app_strings[$options['vname']];
                    } else {
                        $label = $options['vname'];
                    }
                }
                if (isset($label) && $label != "" && !in_array(strtoupper($fieldName), $removeFieldsFromExport))
                    $finalExportData[$id][$label] = trim(strip_tags($exportListRow[strtoupper($fieldName)]));
            }
        }




        //pass data to correct formate
        switch ($format) {
            case "ictbroadcast" :
                $this->exportToExcel($panel->parent_bean->name, $panel->mod_strings[$panel->_instance_properties['title_key']], $finalExportData);
                break;
            case "pdf" :
                $this->exportToPdf($panel->parent_bean->name, $panel->mod_strings[$panel->_instance_properties['title_key']], $finalExportData);
                break;
        }
//        echo "<pre>";
//        print_r($finalExportData);
//        echo "</pre>";
        exit;
    }

    function exportToExcel($parent_name, $subpanel_title, $finalExportData) {
        global $current_user;
        $filename = $parent_name . '_' . $subpanel_title;
        $filename = preg_replace('/[\s]+/', '_', $filename);
        $filename = preg_replace('/[^a-zA-Z0-9_\.-]/', '', $filename);

        function cleanData(&$str) {
            $str = preg_replace("/\t/", "\\t", $str);
            $str = preg_replace("/\r?\n/", " ", $str);
            $str = preg_replace("/&nbsp;/", "", $str);
            if (strstr($str, '"'))
                $str = '"' . str_replace('"', '""', $str) . '"';
        }

        //clean the ob before writing to xl
        ob_clean();
        header("Content-Disposition: attachment; filename={$filename}.xls");
        header("Content-Type: application/vnd.ms-excel");

        $flag = false;
        foreach ($finalExportData as $row) {
            if (!$flag) {
                // display field/column names as first row
                echo implode("\t", array_keys($row)) . "\r\n";
                $flag = true;
            }
            array_walk($row, 'cleanData');
            //echo implode("\t", array_values($row)) . "\r\n";
            echo htmlspecialchars_decode(implode("\t", array_values($row)), ENT_QUOTES) . "\r\n";
        }
    }

    function exportToPdf($parent_name, $subpanel_title, $finalExportData) {
        $filename = $parent_name . '-' . $subpanel_title;
        $this->create_pdf($filename, $finalExportData);
    }

    function create_pdf($name, $fields) {
        require_once('modules/CE_custom_export/pdf/config/lang/eng.php');
        require_once('modules/CE_custom_export/pdf/tcpdf.php');

        // create new PDF document
        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

        // set header and footer fonts
        $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
        $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

        // set default monospaced font
        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

        //set margins
        $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
        $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

        //set image scale factor
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

        //set some language-dependent strings
        $pdf->setLanguageArray($l);

        // set font
        $pdf->SetFont('helvetica', '', 11);
        $pdf->AddPage('L');


        $tbl = '<table cellspacing="0" cellpadding="1" border="0" width="100%" align="center">
		<tr>
			<td><h3>' . $name . '</h3></td>
		</tr>
	</table>';

        $pdf->writeHTML($tbl, true, false, false, false, '');

        $tbl = '<table width="100%" border="1" cellspacing="0" cellpadding="1">
              <thead><tr><th style="width:5%;"><strong>No</strong></th>';
        foreach ($fields[0] as $title => $value) {
            $tbl .= '<th><strong>' . $title . '</strong></th>';
        }

        $tbl .= '</tr></thead>';

        foreach ($fields as $index => $row) {
            $count = $index + 1;
            $tbl .= '<tbody><tr nobr="true"><td style="width:5%;">' . $count . '</td>';
            foreach ($row as $title => $value) {
                $tbl .= '<td>' . $value . '</td>';
            }
            $tbl .= '</tr></tbody>';
        }
        $tbl .= '</table>';

        $pdf->writeHTML($tbl, true, false, false, false, '');
        //clear buffer first
        ob_clean();
        //Close and output PDF document
        $pdf->Output($name . '.pdf', 'D');
    }

    //End Export functions
    public function action_getModuleFields() {
        global $db, $beanList;
        $module = $_POST['module_name'];
        $view_type = $_POST['view_type'];
        $subpanel_module_name = $_POST['subpanel_module'];
        $modObl = '';
        $subpanel_module = ' and ';
        if (isset($_POST['view_type']) && $_POST['view_type'] == 'subpanel') {
            $subpanel_module = "and subpanel_module = '{$subpanel_module_name}' and ";
        }
        if ($view_type == 'module') {
            $modObl = $module;
        } else {
            $modObl = $subpanel_module_name;
        }
        $module_obj = new $beanList[$modObl]();
        $field_defination = $module_obj->getFieldDefinitions();

        $main_table_name = $module_obj->table_name;
        $get_fields_query = "SHOW COLUMNS FROM {$main_table_name}";
        $fields_result = $db->query($get_fields_query);
        while ($fields_row = $db->fetchByAssoc($fields_result)) {
            $module_fields[$fields_row['Field']] = $fields_row['Field'];
        }
        if (array_key_exists('email1', $field_defination)) {
            $module_fields['email1'] = 'email1';
        }

        $cstm_table_name = $main_table_name . "_cstm";
        $get_cstm_fields_query = "SHOW COLUMNS FROM {$cstm_table_name}";
        $cstm_fields_result = $db->query($get_cstm_fields_query);
        if ($cstm_fields_result->num_rows > 0) {
            while ($cstm_fields_row = $db->fetchByAssoc($cstm_fields_result)) {
                $module_fields[$cstm_fields_row['Field']] = $cstm_fields_row['Field'];
            }
        }
        unset($module_fields['deleted']);
        $fields_defination = array();
        foreach ($field_defination as $field_name => $defination) {
            if (array_search($field_name, $module_fields)) {
                foreach ($defination as $key => $value) {
                    $fields_defination['allFields'][$field_name][$key] = $value;
                    $fields_defination['exportFields'][$field_name][$key] = $value;
                    if ($key == 'vname') {
                        $targetModuleLang = return_module_language('en_us', $module_obj->module_dir);
                        $fields_defination['allFields'][$field_name]['label_value'] = $targetModuleLang[$value];
                        $fields_defination['exportFields'][$field_name]['label_value'] = $targetModuleLang[$value];
                    }
                }
            }
        }

        // New changes regarding selected fields for export
        $getExportFieldsFromDB = "Select sequence,export_fields from ce_custom_export where module = '{$module}' $subpanel_module  view_type='{$view_type}' order by sequence asc";
        $query = $db->query($getExportFieldsFromDB);
        $exportFieldsArr = array();
        while ($getFields = $db->fetchByAssoc($query)) {
            unset($fields_defination['allFields'][$getFields['export_fields']]);
            $exportFieldsArr[] = $getFields['export_fields'];
        }
        foreach ($exportFieldsArr as $key => $field) {
            $exportFieldsArrSeq[$field] = $fields_defination['exportFields'][$field];
        }
        $fields_defination['exportFields'] = (empty($exportFieldsArrSeq)) ? array() : $exportFieldsArrSeq;


        // End 

        echo json_encode($fields_defination);
        exit;
    }

    function action_storeSelectedExportFields() {
        global $db;
        $sel_module = $_REQUEST['sel_module'];
        $exportFieldsArray = $_REQUEST['exportFieldsArray'];
        $insertDataArray = array();

        $deleteEntry = "Delete from ce_custom_export where module ='{$sel_module}' and view_type='module'";
        $db->query($deleteEntry);
        foreach ($exportFieldsArray as $key => $field) {
            $id = create_guid();
            $insertDataArray[] = "('" . $id . "',
                                   '" . $sel_module . "',
                                   '" . $key . "',
                                   '" . $field . "',
                                   'module')";
        }
        if (!empty($insertDataArray)) {
            $insertDataArray_string = implode(',', $insertDataArray);
            $insertdata = "Insert into ce_custom_export (id,module,sequence,export_fields,view_type) 
                                             Values {$insertDataArray_string}";
            $db->query($insertdata);
        }
        echo 'done';
        exit;
    }

    function action_storeSelectedExportFieldsSubPanel() {
        global $db;
        $sel_module = $_REQUEST['sel_module'];
        $subpanel_module = $_REQUEST['subpanel_module'];
        $exportFieldsArray = $_REQUEST['exportFieldsArray'];

        $deleteEntry = "Delete from ce_custom_export where module ='{$sel_module}' and subpanel_module = '{$subpanel_module}' AND view_type='subpanel'";
        $db->query($deleteEntry);
        $insertDataArray = array();
        foreach ($exportFieldsArray as $key => $field) {
            $id = create_guid();
            $insertDataArray[] = "('" . $id . "',
                                   '" . $sel_module . "',
                                   '" . $subpanel_module . "',
                                   '" . $key . "',
                                   '" . $field . "',
                                   'subpanel')";
        }
        if (!empty($insertDataArray)) {
            $insertDataArray_string = implode(',', $insertDataArray);
            $insertdata = "Insert into ce_custom_export (id,module,subpanel_module,sequence,export_fields,view_type) 
                                             Values {$insertDataArray_string}";
            $db->query($insertdata);
        }
        echo 'done';
        exit;
    }

    public function action_getSubpanels() {
        global $db, $app_list_strings, $current_user;
        require_once 'include/SubPanel/SubPanelTiles.php';
        $hidsubpanels_arr = SubPanelDefinitions::get_hidden_subpanels();
        if (is_null($hidsubpanels_arr)) {
            $hidsubpanels_arr = array();
        }
        $excluded_modules = array('Home', 'Calendar', 'Bugs', 'Emails', 'Tasks', 'Notes', 'Campaigns', 'Calls', 'Meetings');
        sort($hidsubpanels_arr);
        $module = $_REQUEST['module_name'];
        $excluded_subpanels = array('activities', 'history', 'therevisions', 'campaigns');
        $html = '<option value>Select Subpanel</option>';
        $excluded_all_subpanels = array_merge($excluded_subpanels, $hidsubpanels_arr);
        if (!in_array($module, $excluded_modules)) {
            $focus = BeanFactory::getBean($module);
            $subpanel = new SubPanelTiles($focus, $module);
            $display_SubpanelsInMod = array();
            foreach ($subpanel->subpanel_definitions->layout_defs['subpanel_setup'] as $subpanel_name => $defs) {
                $module_sub = $subpanel->subpanel_definitions->layout_defs['subpanel_setup'][$subpanel_name]['module'];
                if (!in_array($subpanel_name, $excluded_all_subpanels)) {
                    $display_SubpanelsInMod[$defs['title_key']] = $module_sub;
                }
            }

            if (!empty($display_SubpanelsInMod)) {
                $targetModuleLang = return_module_language('en_us', $module);
                foreach ($display_SubpanelsInMod as $title => $subpanel_setup) {
                    if (strpos($title, 'LBL_') !== false) {
                        $sub_label = $targetModuleLang[$title];
                    } else if (!is_null($app_list_strings['moduleList'][$title])) {
                        $sub_label = $app_list_strings['moduleList'][$title];
                    } else {
                        $sub_label = $title;
                    }
                    if (!in_array($subpanel_setup, $excluded_all_subpanels) && !empty($title)) {
                        $html .= "<option value='{$subpanel_setup}' >{$sub_label}</option>";
                    }
                }
            }
        }

        echo $html;
        exit;
    }*/

}

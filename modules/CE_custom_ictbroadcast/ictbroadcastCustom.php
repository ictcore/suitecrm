<?php

ini_set('zlib.output_compression', 'Off');

ob_start();
require_once('include/export_utils.php');
global $app_strings, $db;

//print_r($records);
//exit;
/////////////////////////////////////////////////////////////////////Custom Ictbroadcast get select contacts ids /////////////////////////////////////////////////////////////////////////////////////

if ($_REQUEST['ictbroadcastOpt'] == 'campaign') {

    $module = $_REQUEST['module'];
    $focus = BeanFactory::getBean($module);
    $records = explode(",", $_REQUEST['uid']);
    $ids = implode(',',$records);
  header('Location: index.php?module=CE_custom_ictbroadcast&action=create_campaign_ictbroadcast&uid='.$ids.'&source_module='.$module);

  exit;

}

///////////////////////////////////////////////////////////////////////////////////////END///////////////////////////////////////////////////////////////////////////////////////////////////////////////

 $module = $_REQUEST['source_module'];
 $focus = BeanFactory::getBean($module);
 $records = explode(",", $_REQUEST['uid']);
$ids = implode(',',$records);




if (file_exists('custom/modules/' . $module . '/metadata/listviewdefs.php')) {
    require_once('custom/modules/' . $module . '/metadata/listviewdefs.php');
} else {
    require_once('modules/' . $module . '/metadata/listviewdefs.php');
}



// New changes regarding selected fields for export
$getExportFieldsFromDB = "Select sequence,export_fields from ce_custom_export where module = '{$module}' and view_type='module' order by sequence asc";
$query = $db->query($getExportFieldsFromDB);
$exportFieldsArr = array();
$exportFieldsArrAsSeq = array();
while ($getFields = $db->fetchByAssoc($query)) {
    $exportFieldsArr[$module][strtoupper($getFields['export_fields'])]['label'] = $this->bean->field_name_map[$getFields['export_fields']]['vname'];
    $exportFieldsArr[$module][strtoupper($getFields['export_fields'])]['default'] = true;
}

if (!empty($exportFieldsArr)) {
    $list_viewDefs = $exportFieldsArr;
}
// End 
if (file_exists('modules/CE_custom_ictbroadcast/ce_ignorefields_lv.php')) {
    require_once('modules/CE_custom_ictbroadcast/ce_ignorefields_lv.php');
} else {
    $excludeColumns = array();
}
$excludeColumnsArray = (is_null($excludeColumns[$module])) ? array() : $excludeColumns[$module];
foreach ($excludeColumnsArray as $hidefields) {
    unset($list_viewDefs[$module][$hidefields]);
}

$filter = array();
$all_fields_in_module = array_keys($focus->field_defs);
foreach ($all_fields_in_module as $field) {
    $filter[$field] = true;
}
if ($focus->bean_implements('ACL')) {
    if (!ACLController::checkAccess($focus->module_dir, 'export', true)) {
        ACLController::displayNoAccess();
        sugar_die('');
    }
}

$targetModuleLang = return_module_language('en_us', $focus->module_dir);


$list_viewDefs = $listViewDefs;


$limit = -1;
//if all is selected
if (is_null($_REQUEST['uid'])) {
    $limit = 2000;
}

if (!is_null($_REQUEST['uid'])) {
    $id_arr = array();
    foreach ($records as $id) {
        $id_arr[] = '"' . $id . '"';
    }
    $record_id = implode(",", $id_arr);
    $where = "{$focus->table_name}.id in ({$record_id})";
} elseif (isset($_REQUEST['all'])) {
    $where = '';
} else {
    if (!empty($_REQUEST['current_post'])) {
        $ret_array = generateSearchWhere($module, $_REQUEST['current_post']);

        $where = $ret_array['where'];
        $searchFields = $ret_array['searchFields'];
    } else {
        $where = '';
    }
}
 if($where != "")
   $where = " and ".$where;
 else
   $where = '';
$params['custom_where'] = $where." group by {$focus->table_name}.id ";

require_once('include/ListView/ListViewData.php');
$listObject = new ListViewData();
$exportListRows = $listObject->getListViewData($focus, '', 0, $limit, $filter, $params);

if (!is_null($_REQUEST['uid'])) {
    foreach ($exportListRows['data'] as $key => $dataArray) {
        if (!in_array($dataArray['ID'], $records)) {
            unset($exportListRows['data'][$key]);
        }
    }
}
$finalData = array();
/*foreach ($exportListRows['data'] as $id => $exportListRow) {
    foreach ($list_viewDefs[$module] as $fieldName => $options) {
        if (!is_null($targetModuleLang[$options['label']])) {
            $label = $targetModuleLang[$options['label']];
        } else {
            if (!is_null($app_strings[$options['label']])) {
                $label = $app_strings[$options['label']];
            } else {
                $label = $options['label'];
            }
        }
        if (isset($label) && $label != "" && isset($options['default']) && $options['default'] == true) {
            $finalExportData[$id][$label] = trim(strip_tags($exportListRow[strtoupper($fieldName)]));
    }
}
}
*/
foreach ($exportListRows['data'] as $id => $exportListRow) {
$finalData[] = $exportListRow;

}

$campaing_type = $_REQUEST['campaign_type'];

 $group = $_REQUEST['group'];

$json_data = array();

if(($campaing_type == 'voice' AND ($_FILES['fle']['type']=='audio/x-wav' OR $_FILES['fle']['type']=='audio/wav')) OR ($campaing_type == 'fax' AND ($_FILES['fle']['type']=='application/pdf' OR $_FILES['fle']['type']=='image/tiff' )) OR ($campaing_type =='voice_interactive' AND ($_FILES['fle']['type']=='audio/x-wav' OR $_FILES['fle']['type']=='audio/wav'))){
  $arguments = array('contact_group'=> array('name' => $group));
    $result  = broadcast_api('Contact_Group_Create', $arguments);
    if($result[0] == true) {
        $contact_group_id = $result[1];
        $json_data['group_id'] = $contact_group_id;
        $json_data['campaign_type'] = $campaing_type;
    } else {
        $errmsg = $result[1];

            //  header("Location: index.php?module=CE_custom_ictbroadcast&action=create_campaign_ictbroadcast&uid='.$ids.'&source_module='.$module.&msg=error");
                header('Location: index.php?module=CE_custom_ictbroadcast&action=create_campaign_ictbroadcast&uid='.$ids.'&source_module='.$module.'&msg=error');

        exit;

    }

    foreach($finalData as $contact){

       $firstname =  $contact['FIRST_NAME'];
       $lastname =  $contact['LAST_NAME'];
        $contact  =  $contact['PHONE_MOBILE'];
       $email =  $contact['EMAIL1'];

        $contact = array(
            'phone' => $contact , 
            'first_name'=>$firstname, 
            'last_name'=> $lastname, 
            'email'=> $email
            );
            $arguments = array('contact'=>$contact, 'contact_group_id'=> $json_data['group_id']);
            $result  = broadcast_api('Contact_Create', $arguments);
            if($result[0] == true) {
             $contact_id = $result[1];
            } else {
             $errmsg = $result[1];
                                   header('Location: index.php?module=CE_custom_ictbroadcast&action=create_campaign_ictbroadcast&uid='.$ids.'&source_module='.$module.'&msg=error');

        exit;
            }
    }

}else{
                         header('Location: index.php?module=CE_custom_ictbroadcast&action=create_campaign_ictbroadcast&uid='.$ids.'&source_module='.$module.'&msg=error');

        exit;
}
if($campaing_type == 'voice' || $campaing_type=='fax' || $campaing_type =='voice_interactive' ){

      if($campaing_type=='voice'){

            $method = 'Recording_Create';
            $method_campaign =  'Campaign_Create';

        }elseif($campaing_type=='fax'){
            $method = 'Document_Create';
            $method_campaign =  'Campaign_Fax_Create';
        }elseif($campaing_type == 'voice_interactive'){
            $method = 'Recording_Create';

            $method_campaign =  'Campaign_Interactive_Create';
        }
        $mimetyper = $_FILES['fle']['type'];
        $m_file = curl_file_create($_FILES['fle']['tmp_name'], $mimetyper, basename($_FILES['fle']['tmp_name']));
        $arguments = array('title'=> $group, 'media'=>$m_file);
        $result = broadcast_api($method, $arguments);
        if($result[0] == true) {
            $recording_id = $result[1];
            //print_r($recording_id);
        } else {
            $errmsg = $result[1];
                                 header('Location: index.php?module=CE_custom_ictbroadcast&action=create_campaign_ictbroadcast&uid='.$ids.'&source_module='.$module.'&msg=error');

        exit;
        }

        if($campaing_type == 'voice' || $campaing_type=='fax'){

                $campaign = array(
                'contact_group_id'  => $contact_group_id,     //  contact_group_id
                'message'           => $recording_id,     //  recording_id
                );
        }
        if($campaing_type =='voice_interactive'){

         $campaign = array(
            'contact_group_id'  => $contact_group_id,     //  contact_group_id
            'message'           => $recording_id,     //  recording_id
            'extension_key'     => '1',     // any value from 0 to 7 
            'extension_id'      => $_REQUEST['extension'],     // extension_id 
            );

        }

         $arguments = array('campaign'=>$campaign);
        $result = broadcast_api($method_campaign , $arguments);
        if($result[0] == true) {
            $campaign_id = $result[1];
        //print_r($campaign_id);
        } else {
            echo "campaignerror";
            $errmsg = $result[1];
            //print_r($errmsg);
        header('Location: index.php?module=CE_custom_ictbroadcast&action=create_campaign_ictbroadcast&uid='.$ids.'&source_module='.$module.'&msg=error');

        exit;
        }

        $arguments = array('campaign_id'=>$campaign_id);
        $result = broadcast_api('Campaign_Start', $arguments); 
    }

    header("Location: index.php?module=CE_custom_ictbroadcast&action=create_campaign_ictbroadcast&uid=".$ids."&source_module=".$module."&msg=success");

        exit;

//echo $module. "fff<pre>";print_r( $finalExportData);

//print_r($finalExportData);
//

/*if ($_REQUEST['ictbroadcastOpt'] == 'excel') {
//echo 'hi';
  // $this->view = 'ce_edit';
  $ids = implode(',',$records);
  header('Location: index.php?module=CE_custom_ictbroadcast&action=create_campaign_ictbroadcast&uid='.$ids.'&source_module='.$module);
  
   //exportToExcel($module, $finalExportData);
   
   //echo "adeel bhutta";
} else {
    exportToPDF($module, $finalExportData);
}

function exportToPDF($module, $fields) {
    global $app_list_strings;
    $name = $app_list_strings['moduleList'][$module];
    require_once('modules/CE_custom_ictbroadcast/pdf/config/lang/eng.php');
    require_once('modules/CE_custom_ictbroadcast/pdf/tcpdf.php');

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

function exportToExcel($module, $finalExportData) {
    global $app_list_strings;
    ?>
    <iframe src='ictbroadcastform.php' name='select_frame' width="100%" height="280" frameBorder="0"  scrolling="no" ></iframe>
<?php 
exit;
   // echo "<pre>";print_r($finalExportData);
    
   // exit;
    $filename = $app_list_strings['moduleList'][$module];
    if ($_REQUEST['members'] == true)
        $filename .= '_' . 'members';

    function cleanData(&$str) {
        $str = preg_replace("/\t/", "\\t", $str);
        $str = preg_replace("/\r?\n/", " ", $str);
        $str = preg_replace("/&nbsp;/", "", $str);
        if (strstr($str, '"'))
            $str = '"' . str_replace('"', '""', $str) . '"';
    }

    //clean the ob before writing to xl
    ob_clean();
   // header("Content-Disposition: attachment; filename={$filename}.xls");
   // header("Content-Type: application/vnd.ms-excel");

    $flag = false;
    foreach ($finalExportData as $row) {
        if (!$flag) {
            // display field/column names as first row
            echo implode("\t", array_keys($row)) . "\r\n";
            $flag = true;
        }
        array_walk($row, 'cleanData');
        //echo implode("\t", array_values($row)) . "\r\n";
         echo htmlspecialchars_decode(implode("\t", array_values($row)),ENT_QUOTES) . "\r\n";
    }
}
*/
    function broadcast_api($method, $arguments = array()) {
        global $db, $app_list_strings, $current_user;
        require_once('modules/MySettings/TabController.php');
        $controller = new TabController();
        $tabArray = $controller->get_tabs($current_user);
        $all_enableModules = $tabArray[0];
        $excluded_modules = array('Home', 'Calendar', 'Bugs', 'Emails','KBDocuments', 'Forecasts', 'Iframeapp');
        $saved_module_array = $saved_module_row = array();

        $select_query_sub = "SELECT * FROM custom_ictbroadcast_integration_tbl";
        $saved_integration_result = $db->query($select_query_sub);
        $saved_integration_row = $db->fetchByAssoc($saved_integration_result);

         $ipadrs = $saved_integration_row['link'];
        $token = $saved_integration_row ['token'];

        $url = ($ipadrs!=''  ? $ipadrs : 'http://202.142.186.26/rest'); // returns true
        $barer = ($token!=''  ? $token : 'eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJpc3MiOm51bGwsImF1ZCI6IklDVEJyb2FkY2FzdCBBUEkgQ2xpZW50cyIsImlhdCI6MTUxMjQ0Nzg3NywibmJmIjoxNTEyNDQ3ODc3LCJleHAiOjE1NzQ2NTU4NzcsInVzZXJfaWQiOiIzIn0.e4LxMrp9gBf5_j2Mreklh3V7UeeBALiAKjuTQQuOzwmki7qcuis9jKhR1q42o1oHj65S5zS5eYOzdijSekEDs7zcXHQaPX8TGPvHiC71YeezRXLds68IZuuZeiwsPr_NJYXGEhP60CM8YF-nLovY_9zjdZf_DudbyjmSbS4biqI'); // returns true
         
       $service_url   = $url;

        $post_data    = array();
         /* $post_data    = array(
        'api_username' => $api_username,
        'api_password' => $api_password
          );*/
          $api_url = "$service_url/$method";
          $curl = curl_init($api_url);
          curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
          curl_setopt($curl, CURLOPT_POST, true);

          foreach($arguments as $key => $value) {
        if(is_array($value)){
          $post_data[$key] = json_encode($value);
        } else {
          $post_data[$key] = $value;
        }
          }
          curl_setopt($curl, CURLOPT_POSTFIELDS, $post_data);
          curl_setopt($curl, CURLOPT_HTTPHEADER, array('Authorization: Bearer '.$barer));
          curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
          // enable following line in case, having trouble with certificate validation
          // curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
          $curl_response = curl_exec($curl);
          curl_close($curl);
          return json_decode($curl_response);  
    }

sugar_cleanup(true);
?>

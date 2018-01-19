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

class CE_custom_ictbroadcastViewCE_Integration extends SugarView {

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
            $html = "<div style='margin:10px;text-align:center'><span style='color:green;font-weight:bold;'> Campaign  Running successfully.</span></div>";
        }

        if (!empty($_REQUEST['msg']) && $_REQUEST['msg'] == 'error') {
            $html = "<div style='margin:10px;text-align:center'><span style='color:red;font-weight:bold;'> Some thing wrong Please check file type correct according to campaign type.</span></div>";
        }
       
        $html .= "<form name='ictbroadcast_module' method='post' action='index.php?entryPoint=ictbroadcastCustom&ictbroadcastOpt=campaign_start' enctype='multipart/form-data'>
                 <table width='100%' cellspacing='0' cellpadding='0' id='ModuleTable' class='custom-module-table'><tr></tr><tr>";
       $html .="<input type='hidden' name='uid' value=".$_REQUEST['uid']."><input type='hidden' name='source_module' value=".$_REQUEST['source_module'].">
       <div class='cell form-group' ><label class='control-label'  for='group'>Group<span class='required'>*</span></label><div class='field' data-name='group'><input name='group' class='form-control' value='' title='' type='text'></div></div>";

       $html .='<div class="cell form-group" data-name="campaign_type">
    <label class="control-label" data-name="campaign_type">Select Campaign Type</label>
    <!--<div class="field" data-name="format">{{{format}}}</div>-->
 <div class="field" data-name="campaign_type">
<select name="campaign_type" id = "c_type" class="form-control"> 
        <option value="voice" selected="">Message Campaign</option>
       <!-- <option value="voice_agent">Agent Campaign</option>-->
        <option value="voice_interactive">Interactive Campaign</option>

      <!--  <option value="voice_ivr">IVR Campaign</option>-->
        <option value="fax">Fax Campaign</option>
</select>   
</div>
</div>';

$arguments = array();
     $result  = $this->broadcast_api('User_Extension_List', $arguments);
     if($result[0] == true) {
       $extention_data = $result[1];
   // print_r($extention_data);
     } else {
       $errmsg = $result[1];
   //  print_r($errmsg);
       echo "No Extension Found";
     }
$html .='<div class="form-group"  id="press1" style="display:none">
                        <label class="control-label" data-name="campaign_type">Select Extention</label>
                        <select name="extension" id="extension" class="select2 form-control">';

                        foreach($extention_data as $extentions){
                       $html .='<option value='.$extentions->extension_id.'>'.$extentions->name. '</option>';
                       
                        }
               $html .='</select></div>';
$html .=' <div class="form-group"  id="file">
          <label class="control-label" data-name="campaign_type">Choose File</label>
          <input type="file" name="fle" id="fle"  class="input-large" >
         </div>';
        $html .= '</tr></table>';
        $html .= '<div class="btn-barttt">
                        <input type="submit" class="blue-btn" name="save" value="Save" class="button" >
                        <input type="button" class="blue-btn" name="cancel" value="Cancel" class="button" onclick="redirectToindex();">
                 </div></form>';

                 
                 
                 
                 
                 
                 
                 
                 
        
                 
        parent::display();
        echo $html;

        echo '
            <script type="text/javascript">
            function redirectToindex(){
                location.href = "index.php?module=Leads&action=index";
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


?>

<script type="text/javascript">
jQuery(document).ready(function() {

      $("#c_type").change(function()
    {
        var id=$(this).val();

        var dataString = 'id='+ id;
        // alert(id); 
        if(id=='voice_interactive'){
            // alert(id); 
          $("#file").show();
          $("#press1").show();

        }else{

            $("#file").show();
            $("#press1").hide();

        }
       
    });
});
</script>
<?php
    }


         public function broadcast_api($method, $arguments = array()) {
      // update following with proper access info
      $api_username = 'zuha';    // <=== Username at ICTBroadcast
      $api_password = 'godisone';  // <=== Password at ICTBroadcast
      $service_url  = 'http://202.142.186.26/rest'; // <=== URL for ICTBroadcast REST APIs

      $post_data    = array(
        'api_username' => $api_username,
        'api_password' => $api_password
      );
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
      // enable following line in case, having trouble with certificate validation
      // curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
      $curl_response = curl_exec($curl);
      curl_close($curl);
      return json_decode($curl_response); 
    }    

}

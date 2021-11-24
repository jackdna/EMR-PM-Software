<?php
/*
/*
// The MIT License (MIT)
// THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.
 

 Purpose: Manage Patient's Demographics Information.
 Access Type: Indirect Access.
 
*/
include_once("../../../config/globals.php");
require_once("../../../library/patient_must_loaded.php");
include_once($GLOBALS['srcdir']."/classes/demographics.class.php");
include_once($GLOBALS['srcdir']."/classes/cls_common_function.php");
include_once($GLOBALS['srcdir']."/classes/audit_common_function.php");
$pid = $patient_id = $_SESSION['patient'];
$OBJCommonFunction = new CLSCommonFunction;
$patient_data_obj = new Demographics();
$pg_title = 'Demographics';

$erp_error=array();
if(isERPPortalEnabled()) {
	try {
		$erpSql="Select id from patient_data where id=$pid and erp_pt_comm_pref_completed=1 AND erp_patient_id!='' ";
		$erpSqlRs=imw_query($erpSql);
		if($erpSqlRs && imw_num_rows($erpSqlRs)==1) {
			include_once($GLOBALS['srcdir']."/erp_portal/patients.php");
			$obj_patients = new Patients();
			$obj_patients->getCommunicationPref($pid);
		}
	} catch(Exception $e) {
		$erp_error[]='Unable to connect to ERP Portal';
	}
}

$data = $patient_data_obj->load_demographics_data($patient_id);
$defaults	=	$patient_data_obj->load_defaults_data();
$library_path = $GLOBALS['webroot'].'/library';
$last_appointment = get_patient_last_appointment($patient_id);
$vocabulary = $defaults['vocabulary'];
$mandatory_flds = array_keys(array_filter($defaults['mandatory_fld']),2);
$advisory_flds = array_keys(array_filter($defaults['mandatory_fld']),1);
if( is_array($data->heard_aboutus) && count($data->heard_aboutus) > 0 )
{
	foreach($data->heard_aboutus as $temp)
	{
		$h_id	=	trim($temp['heard_id']);
		$temp['heard_options'] = preg_replace('/0-9/i','num',$temp['heard_options']);
		$temp['heard_options'] = trim(str_replace(array(".","(",")","-","/","\\","'","&amp;"),'',str_replace(' ','_',$temp['heard_options'])));
		$heardAboutSuggestions[$temp['heard_options']] = get_heard_about_suggestions($h_id);	
	}
}

//pre($data->patient_data);
$rowGetPatientData = (array)$data->patient_data;
$restricted_Row = (array)$data->patient_data->restrict_providers;
$policyStatus = (int)$_SESSION['AUDIT_POLICIES']['Patient_record_Created_Viewed_Updated'];
		
if($policyStatus == 1){
	$patientDataFields = array(); 
	$patientDataFields = make_field_type_array("patient_data");
	if($patientDataFields == 1146){
		$patientDataError = "Error : Table 'patient_data' doesn't exist";
	}
	$respPartyDataFields = array(); 
	$respPartyDataFields = make_field_type_array("resp_party");
	if($respPartyDataFields == 1146){
		$respPartyError = "Error : Table 'resp_party' doesn't exist";
	}
	$empDataFields = array(); 
	$empDataFields = make_field_type_array("employer_data");
	if($empDataFields == 1146){
		$empDataError = "Error : Table 'employer_data' doesn't exist";
	}
	$customDataFields = array(); 
	$customDataFields = make_field_type_array("patient_custom_field");
	if($customDataFields == 1146){
		$customError = "Error : Table 'patient_custom_field' doesn't exist";
	}		
	$restrictedProvidersDataFields = array(); 
	$restrictedProvidersDataFields = make_field_type_array("restricted_providers");
	if($restrictedProvidersDataFields == 1146){
		$restrictedProvidersError = "Error : Table 'restricted_providers' doesn't exist";
	}
}

//IM-4513:- if age of the patient is below 18 years of age and the Responsible Party/Guarantor section opens up, autofill the patient's address to the Responsible Party section.
$reponsible_party_arr=array();
if($data->patient_data->ptDOB != "" && $data->patient_data->ptDOB !="0000-00-00"){
    $patientAgeArry = ageCalculator($data->patient_data->ptDOB);
    $ptAge = $patientAgeArry->y;
    if($ptAge<18){
        if( stripslashes($data->patient_data->rpPtAdd)=='' && stripslashes($data->patient_data->rpPtZip)=='' && stripslashes($data->patient_data->rpPtCity)=='' && stripslashes($data->patient_data->rpPtState)=='' ) {
            $resp_party_arr['resp_ptStreet']=stripslashes($data->patient_data->ptStreet);
            $resp_party_arr['resp_ptStreet2']=stripslashes($data->patient_data->ptStreet2);
            $resp_party_arr['resp_ptPostalCode']=stripslashes($data->patient_data->ptPostalCode);
            $resp_party_arr['resp_ptzip_ext']=stripslashes($data->patient_data->ptzip_ext);
            $resp_party_arr['resp_ptCity']=trim(stripslashes($data->patient_data->ptCity));
            $resp_party_arr['resp_ptState']=trim(stripslashes($data->patient_data->ptState));
            
        }
    }
}


//if (substr_count($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip')){ob_start("ob_gzhandler");}else{ob_start();}
ob_start();
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <title><?php echo 'Demographics :: imwemr ::';?></title>
    <!-- Bootstrap -->
    <link href="<?php echo $library_path; ?>/css/bootstrap.min.css" rel="stylesheet" type="text/css">
    <!-- Bootstrap Selctpicker CSS -->
    <link href="<?php echo $library_path; ?>/css/bootstrap-select.css" rel="stylesheet" type="text/css">
    <!-- Application Common CSS -->
    <link href="<?php echo $library_path; ?>/css/common.css" rel="stylesheet" type="text/css">
		<link href="<?php echo $library_path; ?>/css/patient_info.css" rel="stylesheet" type="text/css">

		<!-- Demographic Page CSS -->
    <!--<link href="<?php echo $library_path; ?>/css/demographics.css" rel="stylesheet">-->
    <!-- Messi Plugin for fancy alerts CSS -->
	<link href="<?php echo $library_path; ?>/messi/messi.css" rel="stylesheet" type="text/css">
    <!-- DateTime Picker CSS -->
    <link rel="stylesheet" type="text/css" href="<?php echo $library_path; ?>/css/jquery.datetimepicker.min.css"/>
    <?php if(constant('DEFAULT_PRODUCT') == "imwemr") { ?>
        <link href="<?php echo $library_path; ?>/css/imw_css.css" rel="stylesheet">
    <?php } ?>
    
    <script>
			/* Default JS Variables */
			var type_head_source = [];
			var web_root = '<?php echo $GLOBALS['webroot']; ?>';
			var mandatory = <?php echo json_encode($defaults['mandatory_fld']); ?>;
			var mandatory_fld = <?php echo json_encode($mandatory_flds); ?>;
			var advisory_fld = <?php echo json_encode($advisory_flds); ?>;
			/* Typehead for Heard About US text area Field */
			var suggestions_ha =  <?php echo json_encode($heardAboutSuggestions); ?>;
			var heardAboutSearch = <?php echo json_encode($data->heardAboutSearch); ?>;
			var change_flag, _this, $_this = false;
			/* Vocabulary for demographics page */
			var vocabulary = <?php echo json_encode($vocabulary); ?>;
			/* Global Phone Format */
			var phone_format = '<?php echo $GLOBALS['phone_format'] ?>';
			var operator = '<?php echo $defaults['operator_name']; ?>';
			var ser_root = "<?php echo $GLOBALS['webroot']."/data/".constant('PRACTICE_PATH')."/xml/refphy/" ?>";
      top.$('.eldemo').show();
      
      var isERPPortalEnabled = '<?php echo isERPPortalEnabled(); ?>';
      var isUGAEnable = '<?php echo isUGAEnable(); ?>';
            var resp_party_arr='<?php echo json_encode($resp_party_arr);?>';
		</script>
    
    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
  </head>
  <body onUnload="$('#hidChkDemoTabDbStatus',top.document).val('');">
  	<div class="clearfix"></div>
    
		<div class="bg-white" id="body_div">
        <form class="form-inline1 margin_0" name="demographics_edit_form" action="demographics_save.php?tdId=<?=$tabId;?>" method="post" enctype="multipart/form-data" autocomplete="off">

        <!-- Hidden Fields Section Start -->
        <input type="hidden" id="edit_patient_id" name="edit_patient_id" value="<?php echo $data->patient_data->ptID; ?>"/>
        <input type="hidden" id="zipCodeStatus" name="zipCodeStatus" value="<?php echo $data->patient_data->zipCodeStatus; ?>"/>
        <input type="hidden" id="curr_date" name="curr_date" value="<?php echo $data->patient_data->curr_date; ?>">
        <input type="hidden" id="seltab" name="seltab" value="<?php echo $data->patient_data->seltab; ?>">
        <input type="hidden" id="Allow_erx_medicare" name="Allow_erx_medicare" value="<?php echo $data->patient_data->allow_erx_medicare; ?>" />
        <input type="hidden" id="erx_entry" name="erx_entry" value="<?php echo $data->patient_data->erx_entry;?>">
        <input type="hidden" id="preObjBack" name="preObjBack" value="<?php echo $data->patient_data->preObjBack; ?>"/>
        <input type="hidden" id="patientDob" name="patientDob" value="<?php echo $data->patient_data->ptDOB; ?>">
        <input type="hidden" id="patientSex" name="patientSex" value="<?php echo $data->patient_data->ptGender; ?>">
        <input type="hidden" id="patientStreet" name="patientStreet" value="<?php echo $data->patient_data->ptStreet; ?>">
        <input type="hidden" id="chkErxAsk" name="chkErxAsk" value="<?php echo $data->patient_data->chkErxAsk; ?>">
        <input type="hidden" id="hidDemoChangeOption" name="hidDemoChangeOption" value="<?php echo $data->patient_data->hidDemoChangeOption; ?>">
        <input type="hidden" id="ptInfoCollapseStatus" name="ptInfoCollapseStatus"  />
        <input type="hidden" id="resPartyCollapseStatus" name="resPartyCollapseStatus"  />
        <input type="hidden" id="ptOccCollapseStatus" name="ptOccCollapseStatus"  />
        <input type="hidden" id="miscCollapseStatus" name="miscCollapseStatus"  />
        <input type="hidden" id="hidden_providersToRestrictDemographics" name="hidden_providersToRestrictDemographics" value="<?php echo $data->patient_data->hidden_providersToRestrictDemographics; ?>"/>
        <input type="hidden" name="hid_create_acc_resp_party" id="hid_create_acc_resp_party" value="<?php echo $data->patient_data->hid_create_acc_resp_party; ?>" />
        <!-- Hidden Fields Section End -->
        
        <!-- Top Bar Section -->
        
        <div class="container-fluid">
        	<div class="row">
          <div class="col-sm-12 demotop pd5" style="min-height:42px;">
            <div class="col-sm-9 ">
            	<div class="row">
            
            	<div class="col-sm-2 ">
              	<div class="checkbox margin_0 valign-top">
                	<?php $checked = ($data->patient_data->ptReportExemption) ? 'checked': ''; ?>
                  <input type="checkbox" name="reportExemption" id="reportExemption" value="1" <?php echo $checked;?> onchange="top.chk_change_in_form('',this,'DemoTabDb',event);" autocomplete="off">
                  <label for="reportExemption"><b>Exempt from Reports</b></label>
               	</div>
             	</div>
              
              <div class="col-sm-4">
              	<div class="row">
									<?php $data->patient_data->ptHeardAbtDesc = addslashes($data->patient_data->ptHeardAbtDesc); ?>
                  <label class="col-xs-3 nowrap text-right pd0" for="elem_heardAbtUs"><b>Heard about us</b></label>
                  <span class="col-xs-4">
                    <select name="elem_heardAbtUs" id="elem_heardAbtUs" class="form-control minimal" data-width="100%" data-txt-cols="<?php echo $data->patient_data->heard_about_us_cols; ?>" data-desc="<?php echo $data->patient_data->ptHeardAbtDesc; ?>" data-prev-val="<?php echo addslashes($data->patient_data->ptHeardAbtUs); ?>" >
                        <option value=""><?php echo imw_msg('drop_sel'); ?></option>
                        <?php 
                          $display_img = "hidden";
													$selHeardAbtStr = "";
                          $forSelTypeAhed = "";
                          if(is_array($data->heard_aboutus) && count($data->heard_aboutus) > 0){
                          foreach($data->heard_aboutus as $rowHeardAbtUs){
                          $sel = "";
                          $h_id	=	trim($rowHeardAbtUs['heard_id']);
                          $h_opt=	$rowHeardAbtUs['heard_options'];
                          if(trim($data->patient_data->ptHeardAbtUs) == $h_id ){
                            $sel = "selected='selected'";
                            $forSelTypeAhed = str_ireplace("'","",stripslashes($rowHeardAbtUs['heard_options']));
                            $forSelTypeAhed = str_ireplace(":","",$forSelTypeAhed);
														$selHeardAbtStr = stripslashes($rowHeardAbtUs['heard_options']);
                          }
                        ?>
                          <option value="<?php echo $h_id."-".$h_opt; ?>" <?php echo $sel; ?>><?php echo stripslashes($h_opt); ?></option>
                          <?php		
                          }
                        }
                        ?>
                        <option value="Other">Other</option>
                   	</select>
               	 		
               	 		<div id="otherHeardAboutBox" class="hidden">
                    	<div class="input-group">
                      	<input class="form-control" id="heardAbtOther" type="text" name="heardAbtOther" data-prev-val="" />
                      	<label class="input-group-btn btn btn-xs btn-primary back_other" id="imgBackHeardAboutUs" data-tab-name="elem_heardAbtUs">
                      		<span class="glyphicon glyphicon-arrow-left"></span>
                      	</label>
                      </div>
                      </div>
                	</span>
               		<?php
										$searchFieldShow = $txtFieldShow = false;
										$searchEvents = '';
										if( $data->patient_data->ptHeardAbtUs ) {
											if( in_array($selHeardAbtStr,$data->heardAboutSearch) ) {
												$searchFieldShow = true;
												if( $selHeardAbtStr == 'Doctor' ){
													$searchEvents = 'onkeyup="top.loadPhysicians(this,\'heardAbtSearchId\')";onfocus="top.loadPhysicians(this,\'heardAbtSearchId\')";';
												}
												else {
													$searchEvents = 'onkeydown="if( event.keyCode == 13) { searchHeardAbout(); }";';
												}
											} else{
												$txtFieldShow = true;
											}
										}
									?>
                  <span class="col-xs-5 pd0">
                    <span id="tdHeardAboutDesc">
                    	<textarea class="form-control <?php echo ($txtFieldShow ? 'inline' : 'hidden');?>" id="heardAbtDesc" name="heardAbtDesc" rows="1" cols="<?php echo $data->patient_data->heard_about_us_cols;?>" data-provide="multiple" data-seperator="newline"><?php echo stripslashes($data->patient_data->ptHeardAbtDesc); ?></textarea>
                  	</span>
                  	<div id="tdHeardAboutSearch" class="<?php echo ($searchFieldShow ? 'inline' : 'hidden');?>">
                  		<div class="input-group">
                   			<input type="hidden" id="heardAbtSearchId" name="heardAbtSearchId" value="<?php echo stripslashes($data->patient_data->ptHeardAbtSearchId); ?>" />
                   			<input type="text" class="form-control " id="heardAbtSearch" name="heardAbtSearch" value="<?php echo stripslashes($data->patient_data->ptHeardAbtSearch); ?>" autoComplete="off" <?php echo $searchEvents;?> />
                    		<label class="input-group-addon btn" onClick="searchHeardAbout();" >
                      		<span class="glyphicon glyphicon-search"></span>
                      	</label>
                  		</div>
										</div>		
                  </span> 	         
              	</div>
             	</div>
              
              <div class="col-sm-4">
                <div class="row">
                <label class="col-xs-4 col-md-3 nowrap text-right"><b>Pt. Status</b></label>
                <span class="col-xs-4">
                    <?php if(core_check_privilege(array('priv_Sch_Override'))===false){ $pt_status_enabled = ' disabled="disabled"';} ?>
                    <select <?php echo $pt_status_enabled;?> class="form-control minimal" name="elem_patientStatus" id="elem_patientStatus" data-width="100%" data-prev-val="<?php echo addslashes($data->patient_data->ptPatientStatus); ?>">
                      <?php 
                        $arr_pt_status = core_pt_status_list();
                        echo core_make_select_options($arr_pt_status, "pt_status_name", "pt_status_name", $data->patient_data->ptPatientStatus);
                      ?>
                    </select>
                </span>
                <span class="col-xs-4 col-md-5"> 	
                    <span class="hidden" id="dod_patient_td">
                      <span class="input-group" >
                        <?php $pat_as_dod	=	($data->patient_data->dod_patient != '00-00-0000')	?	$data->patient_data->dod_patient : '';?>
                        <input class="form-control datepicker" name="dod_patient" id="dod_patient" title="MM-DD-YYYY" value="<?php echo $pat_as_dod; ?>" data-prev-val="<?php echo $pat_as_dod; ?>" />
                        <label class="input-group-addon btn" for="dod_patient">
                          <span class="glyphicon glyphicon-calendar"></span>
                        </label>
                      </span>
                    </span>
                    <span class="hidden" id="tdOtherPatientStatus">
                      <input type="text" class="form-control" name="otherPatientStatus" id="otherPatientStatus" value="<?php echo $data->patient_data->ptOtherPatientStatus;?>" data-prev-val="<?php echo $data->patient_data->ptOtherPatientStatus; ?>" />
                    </span>
                </span>
              </div>
              </div>
                
              <div class="col-sm-2 ">
                <div class="row">
                <?php
                  $patRes	=	get_extract_record('patient_data','id',$patient_id,'pat_account_status');
                  $patAcStatus = $patRes['pat_account_status'];
                  $acOptions ='';
                  $acRs = imw_query("Select * from account_status WHERE del_status=0 ORDER BY status_name");
                  while($acRes = imw_fetch_array($acRs)){
                  $sel = '';
                    if($acRes['id']==$patAcStatus){ $sel='SELECTED'; }
                    $acOptions.='<option value="'.$acRes['id'].'" '.$sel.' >'.$acRes['status_name'].'</option>';
                  }
                ?>
                <label class="col-xs-3 text-right">
                  <a href="javascript:top.get_set_pat_acc_status();" title="Patient Account Status" class="text_purple pointer"><b>Pt.&nbsp;AS</b></a>
                </label>
                
                <div class="col-xs-9" id="divAcStatus">
                	
                  	<select name="account_status" id="account_status" class="form-control minimal" data-width="100%" title="Patient Account Status" data-source="select" data-action="set_patient_status" >
                    	<?php 
                      echo $acOptions;
                      if($_SESSION['sess_privileges']['priv_admin'] == 1) {
                        echo '<option value="other">Other</option>';
                      }
                      ?>
                      </select>
                    
                    <div class="col-xs-12 hidden" id="otherStsDiv">
                    	<div class="row">
                      	<div class="col-xs-9">
                          <span class="input-group" >
                            <input type="text" name="other_status" id="other_status" value="" class="form-control">
                            <label class="input-group-btn btn btn-xs btn-primary back_other" id="imgBackAcctSts" data-tab-name="account_status">
                            <span class="glyphicon glyphicon-arrow-left"></span>
                          </label>
                          </span>
                    		</div>
                        <div class="col-xs-3">
                    			<input type="button" name="btnSetStatus" id="btnSetStatus" class="btn btn-success btn-xs" value="Save" data-action="set_patient_status" data-source="btn" >
                          <input type="hidden" name="oldStatus" id="oldStatus" value="<?php echo $patAcStatus;?>" >
                   			</div>
                    
                     	</div>   
                   	</div>
                    
              	</div>
                
              	</div>   
              </div>
            	
              </div>
            </div>
            
            <div class="col-sm-3 text-right form-inline">
              <div class="checkbox margin_0 valign-top" >
                <?php $checked = ($data->patient_data->vip == 1) ? 'checked' : ''; ?>
                <input type="checkbox" id="vip" name="vip" <?php echo $checked; ?> value="1" <?php echo $vipSts.' '.$clickFun;?> data-prev-val="<?php echo $checked; ?>" />
                <label for="vip"><b>VIP</b></label>
              </div>
              
              <div class="checkbox margin_0 valign-top">
                <input title="Hold Statements" type="checkbox" id="h_statement" name="h_statement" <?php echo($data->patient_data->hold_statement == 1 ? "checked":""); ?> value="1" <?php echo $vipSts.' '.$clickFun;?> onClick="top.chk_change_in_form('<?php if($data->patient_data->hold_statement == 1) echo 'checked'; ?>',this,'DemoTabDb',event);" />
                <label for="h_statement"><b>HS</b></label>
              </div>
              
              <?php
                  if($data->patient_data->noBalanceBillTxt == "No Balance Bill"){
                ?>     
              <div class="checkbox margin_0 valign-top">
                <input id="noBalBill" type="checkbox" value="1" <?php echo($data->patient_data->ptNoBalanceBill ? "checked" : "" ); ?> name="noBalBill" onClick="top.chk_change_in_form('<?php echo($data->patient_data->ptNoBalanceBill ? "checked" : "" ); ?>',this,'DemoTabDb',event);">
                <label for="noBalBill"><b><?php echo $data->patient_data->noBalanceBillTxt; ?></b></label>
              </div>
              
              <?php
                  }
              ?>
              
              <div class="checkbox margin_0 valign-top">
                <input type="checkbox" name="emr" id="emr" value="1" <?php if($data->patient_data->ptEMR == 1) echo 'checked';?> onClick="top.chk_change_in_form('<?php if($data->patient_data->ptEMR==1) echo 'checked'; ?>',this,'DemoTabDb',event);">
                <label for="emr"><b>EMR</b></label>
                <input type="hidden" name="hid_emr" id="hid_emr" value="<?php echo $data->patient_data->ptEMR;?>">
              </div>      
              
              <span class="btn btn-primary btn-xs " style="cursor:default;">ID: <?php echo $data->patient_data->ptID.($data->patient_data->patient_mrn ? '/'.$data->patient_data->patient_mrn:'');?></span>
             	
            
            </div>
          </div>
          </div>
       	</div>
        <!-- Top Bar Section End -->
        
        <div class="clearfix"></div>
        
        
        <div class="container-fluid mt2	">
          
          <div class="row">
            
            <div class="col-sm-4">
            	<div class="demobox">
              <div class="">
                
                <h2 class="head">
                	PATIENT DEMOGRAPHICS
                	<button class="btn btn-success pull-right " type="button" id="demographics_hx" data-action='demographics_history' style="margin-top:-5px;">Demographics Hx</button>  
                	<span class="pull-right" style="margin-right:60px;">
                    	<?php $pt_disable_checked = ($data->patient_data->pt_disable == 1)?"checked":"";?>
                      <div class="checkbox checkbox-inline" >
                        <input type="checkbox" class="checkbox" name="pt_disable" id="pt_disable" <?php echo $pt_disable_checked;?> value="1">
                        <label for="pt_disable" title="Mark patient as Disabled">Disabled</label>
                      </div>
									</span>
               	</h2>
                
                <div class="clearfix"></div>
                
                <div class="row mb5 pt-box">
                  <div class="col-sm-12 grid-box" tabindex="0">
                  
                    <div class="col-sm-4">
                      <div class="row">
                        
                        <div class="col-sm-5">
                          <label for="title">Title</label>
                          <?php $tempTitle	=	$data->patient_data->ptTitle; ?>
                          <select name="title" id="title" class="form-control minimal" data-width="100%" data-prev-val="<?php echo addslashes($tempTitle); ?>" title="<?php echo imw_msg('drop_sel'); ?>" data-header="<?php echo imw_msg('drop_sel'); ?>" >
                            <option value="" <?php if($tempTitle == ""){echo("selected");} ?>> </option>
                            <option value="Mr." <?php if($tempTitle == "Mr."){echo("selected");}?>>Mr.</option>
                            <option value="Mrs." <?php if(($data->patient_data->ptMaritalStatus == "married" && $data->patient_data->ptGender =="Female") || $tempTitle == "Mrs."){echo("selected");}?>>Mrs.</option>
                            <option value="Ms." <?php if($tempTitle == "Ms."){echo("selected");}?>>Ms.</option>
                            <option value="Miss" <?php if($tempTitle == "Miss"){echo("selected");}?>>Miss</option>
                            <option value="Master" <?php if($tempTitle == "Master"){echo("selected");}?>>Master</option>
                            <option value="Prof." <?php if($tempTitle == "Prof."){echo("selected");}?>>Prof.</option>
                            <option value="Dr." <?php if($tempTitle == "Dr."){echo("selected");}?>>Dr.</option>
                          </select>
                          <input type="hidden" name="hidd_prev_title" id="hidd_prev_title" value="<?php echo $tempTitle;?>">
                        </div>
                        
                        <div class="col-sm-7">
                          <label for="fname">First Name</label>
                          <input type="text" name="fname" id="fname" value="<?php echo stripslashes($data->patient_data->ptFname);?>" class="form-control" data-prev-val="<?php echo addslashes($data->patient_data->ptFname); ?>" >
                          <input type="hidden" name="hidd_prev_fname" id="hidd_prev_fname" value="<?php echo $data->patient_data->ptFname;?>">
                        </div>
                      
                      </div>
                    </div>
                    
                    <div class="col-sm-2">
                      <label for="mname">Middle</label>
                      <input type="text" name="mname" id="mname" value="<?php echo stripslashes($data->patient_data->ptMname);?>" class="form-control" data-prev-val="<?php echo addslashes($data->patient_data->ptMname);?>">
                      <input type="hidden" name="hidd_prev_mname" id="hidd_prev_mname" value="<?php echo $data->patient_data->ptMname;?>">
                    </div>
                    
                    <div class="col-sm-6">
                      <div class="row">
                        <div class="col-sm-6">
                          <label for="lname">Last Name</label>
                          <input type="text" name="lname" id="lname" value="<?php echo stripslashes($data->patient_data->ptLname);?>" class="form-control" data-prev-val="<?php echo addslashes($data->patient_data->ptLname);?>" />
                          <input type="hidden" name="hidd_prev_lname" id="hidd_prev_lname" value="<?php echo $data->patient_data->ptLname;?>">
                        </div>
                        
                        <div class="col-sm-6">
                          <label for="suffix">Suffix</label>
                          <input name="suffix" id="suffix" type="text" class="form-control" value="<?php echo stripslashes($data->patient_data->ptSuffix)?>" data-prev-val="<?php echo addslashes($data->patient_data->ptSuffix);?>" />
                          <input type="hidden" name="hidd_prev_suffix" id="hidd_prev_suffix" value="<?php echo $data->patient_data->ptSuffix;?>">
                        </div>
                      </div>
                    </div>
                    
                    <div class="col-sm-4">
                      <label for="status">Birth Name</label>
                      <input type="text" name="birth_name" id="birth_name" value="<?php echo stripslashes($data->patient_data->ptBname);?>" class="form-control" data-prev-val="<?php echo addslashes($data->patient_data->ptBname);?>" />
                  	</div>
                    
                    <div class="col-sm-2">
                      <label for="sex">Sex</label>
                      <input type="hidden" name="hidd_prev_sex" id="hidd_prev_sex" value="<?php echo $data->patient_data->ptGender;?>">
                      <?php
												$arrGender = gender();
											?>
                      <select name="sex" id="sex" class="form-control minimal" data-width="100%" data-prev-val="<?php echo addslashes($data->patient_data->ptGender); ?>" title="<?php echo imw_msg('drop_sel'); ?>" >
											<option value="" selected>&nbsp;</option>
                      <?php
												foreach($arrGender as $key => $val)
												{
													$key = trim($key);
													$sel = ($data->patient_data->ptGender == $key ) ? 'selected' : '';
													echo '<option value="'.$key.'" '.$sel.'>'.$key.'</option>';
												}
											?>
                      </select>
                    </div>
                    
                    <div class="col-sm-6">
                    	<div class="row">
                      
                        <div class="col-sm-6">
                          <label for="dob">DOB(<?php echo inter_date_format(); ?>)</label>
                          <?php
                            $create_date = "";
                            if($data->patient_data->ptDOB != "" && $data->patient_data->ptDOB !="0000-00-00")
                            {
                              $create_date = get_date_format($data->patient_data->ptDOB);
                              //Get Age
                              $patient_age_text = "";
                              $patientAgeArr = ageCalculator($data->patient_data->ptDOB);;
                              $patient_age = $patientAgeArr->y;
                              $patient_age_month = $patientAgeArr->m;
                            }
                            else
                            {
                              $create_date="";
                              $patient_age = 0;
                              $patient_age_month = 0;
                            }
                            /*$date_qry = "SELECT DATE_FORMAT(SUBDATE(CAST(FROM_DAYS(DATEDIFF(NOW(),SUBDATE('".$data->patient_data->ptDOB."',INTERVAL 1 YEAR))) AS DATE),INTERVAL 1 YEAR),'%m') AS MONTHS,DATE_FORMAT(SUBDATE(CAST(FROM_DAYS(DATEDIFF(NOW(),SUBDATE('".$data->patient_data->ptDOB."',INTERVAL 1 YEAR))) AS DATE),INTERVAL 1 YEAR),'%y') AS YEAR ";
                            $date_result = imw_query($date_qry);
                            $arrDate = imw_fetch_array($date_result);
                            if($patient_age_text == "Mon.")
                            {
                              $patient_age_month = $patient_age;
                              $patient_age = "";
                            }
                            else
                            {
                              if(date("d") == $day){
                                $patient_age_month = 0;	
                              }
                              else{
                                $patient_age_month = $arrDate[0]-1;	
                              }
                            }*/
                           
                          ?>
                          <input type="hidden" name="from_date_byram" id="from_date_byram" value="<?php echo get_date_format(date("Y-m-d"));?>" >
                          <input type="hidden" name="hidd_prev_dob" id="hidd_prev_dob" value="<?php echo $data->patient_data->ptDOB;?>">
                          <div class="input-group">
                            <input name='dob' id="dob" type="text"  class="form-control" title='<?php echo inter_date_format();?>' value='<?php echo $create_date; ?>' data-prev-val="<?php echo addslashes($create_date); ?>" maxlength=10 />
                            <?php
                              if($data->patient_data->ptDOB != '0000-00-00')
                              {
                                $PatientDOB  = $data->patient_data->ptDOB;
                              }
                            ?>
                            <label class="input-group-addon btn" for="dob">
                              <span class="glyphicon glyphicon-calendar"></span>
                            </label>
                          </div>	
                        </div>
                      
                        <div class="col-sm-6">
                          <label>Age</label>
                          <div class="form-control" id="ageBox"><span id="patient_age"><?php echo(($patient_age > 0) ? $patient_age : "0");?></span>&nbsp;<small>Year</small>,&nbsp;<span id="patient_age_month"><?php echo(($patient_age_month > 0) ? $patient_age_month : "0");?></span>&nbsp;<small>Months</small></div>
                        </div>
                        
                   		</div>	     
                    </div>
                    
                    <div class="col-sm-6">
                      <div class="row">
                      	<div class="col-xs-6">
                        	<label for="ss">Social Security</label>
                          <input type="hidden" name="hidd_prev_ss" id="hidd_prev_ss" value="<?php echo $data->patient_data->ptSS;?>">
                          <input name="ss" id="ss" type="text" class="form-control" value="<?php echo stripslashes($data->patient_data->ptSS)?>" data-prev-val="<?php echo addslashes($data->patient_data->ptSS);?>" />
                      	</div>
                        <div class="col-sm-6">
                      		<label for="status">Marital Status</label>
                      		<input type="hidden" name="hidd_prev_mstatus" id="hidd_prev_mstatus" value="<?php echo $data->patient_data->ptMaritalStatus;?>">
                          <select  name="status" id="status" class="form-control minimal" data-width="100%" data-prev-val="<?php echo addslashes($data->patient_data->ptMaritalStatus); ?>" title="<?php echo imw_msg('drop_sel'); ?>" data-header="<?php echo imw_msg('drop_sel'); ?>"  >
                          <?php
                            //echo "<!-- ".$data->patient_data->ptMaritalStatus." -->\n";
                            foreach ($defaults['marital_status'] as $s) 
                            {
                              if ($s == "") {
                                echo "<option value=''";
                              } else {
                                echo "<option value='".$s."'";
                              }
                              
                            if (strtolower($s) == strtolower($data->patient_data->ptMaritalStatus) || empty($s))
                              echo " selected";
                              echo ">".(empty($s) ? ' ' : ucwords($s))."</option>\n";
                            }
                          ?>
                          </select>
                    		</div>
                      </div>
                    </div>
                    	
                    
                   	<div class="col-sm-6">
                    	<div class="row">
                      	<div class="col-sm-6">
                        	<label for="sexual_orientation">Sexual&nbsp;Orientation</label>
                          <?php 
														$arrSOR = sexual_orientation();
													?>	
                          <select name="sexual_orientation" id="sexual_orientation" class="form-control minimal" data-width="100%" data-prev-val="<?php echo addslashes($data->patient_data->ptSexualOrientation); ?>" title="<?php echo imw_msg('drop_sel'); ?>" >
                          <option value="" selected>&nbsp;</option>
                          <?php 
														foreach($arrSOR as $key => $val)
														{
															$sel = ($data->patient_data->ptSexualOrientation == $key ) ? 'selected' : '';
															echo '<option value="'.$key.'" '.$sel.'>'.$val['value'].'</option>';
														}
													
													?>
                        	</select>
                          
                         	<div id="otherSORBox" class="hidden">
                          	<div class="input-group ">
                            	<input class="form-control" name="otherSOR" id="otherSOR" value="<?php echo stripslashes($data->patient_data->ptOtherSOR); ?>" data-prev-val="<?php echo addslashes($data->patient_data->ptOtherSOR); ?>"  />
                              <label class="input-group-btn btn btn-xs btn-primary back_other" id="imgBackSOR" data-tab-name="sexual_orientation">
                              	<span class="glyphicon glyphicon-arrow-left"></span>
                             	</label>
                           	</div>
                        	</div>
                      	</div>
                        
                        <div class="col-sm-6">
                        	<label for="gender_identity" >Gender&nbsp;Identity</label>
                          <?php 
														$arrGID = gender_identity();
													?>
                          <select name="gender_identity" id="gender_identity" class="form-control minimal" data-width="100%" data-prev-val="<?php echo addslashes($data->patient_data->ptGenderIdentity); ?>" title="<?php echo imw_msg('drop_sel'); ?>" >
                          <option value="" selected>&nbsp;</option>
                        	<?php 
														foreach($arrGID as $key => $val)
														{
															$sel = ($data->patient_data->ptGenderIdentity == $key ) ? 'selected' : '';
															echo '<option value="'.$key.'" '.$sel.'>'.$val['value'].'</option>';
														}
													?>
                         	</select>
                          <div id="otherGIBox" class="hidden">
                          	<div class="input-group ">
                            	<input class="form-control" name="otherGI" id="otherGI" value="<?php echo stripslashes($data->patient_data->ptOtherGI); ?>" data-prev-val="<?php echo addslashes($data->patient_data->ptOtherGI); ?>"  />
                              <label class="input-group-btn btn btn-xs btn-primary back_other" id="imgBackGI" data-tab-name="gender_identity">
                              	<span class="glyphicon glyphicon-arrow-left"></span>
                             	</label>
                           	</div>
                        	</div>
                      	</div>
                            
                   		</div>   
                    </div>
                    
                    <div class="clearfix"></div>
                    
                    <div class="col-sm-4">
                     	<div class="row">
                     		<div class="col-xs-8">
                     			<label for="maiden_fname">Mother's F. Name</label>
                      		<input type="text" name="maiden_fname" id="maiden_fname" value="<?php echo stripslashes($data->patient_data->ptMaidenFname);?>" class="form-control" data-prev-val="<?php echo addslashes($data->patient_data->ptMaidenFname);?>" />
												</div>
												<div class="col-xs-4">
													<label for="maiden_mname">Middle </label>
                      		<input type="text" name="maiden_mname" id="maiden_mname" value="<?php echo stripslashes($data->patient_data->ptMaidenMname);?>" class="form-control" data-prev-val="<?php echo addslashes($data->patient_data->ptMaidenMname);?>" />
												</div>
											</div>
                  	</div>
                    
                    <div class="col-sm-2">
                      
                      <label for="maiden_lname">Maiden </label>
                      <input type="text" name="maiden_lname" id="maiden_lname" value="<?php echo stripslashes($data->patient_data->ptMaidenLname);?>" class="form-control" data-prev-val="<?php echo addslashes($data->patient_data->ptMaidenLname);?>" />
                    </div>
                    
                    <div class="col-sm-3">
                      <label for="phonetic_name">Phonetic Name</label>
                     	<input type="text" name="phonetic_name" id="phonetic_name" value="<?php echo stripslashes($data->patient_data->ptPhoneticName);?>" class="form-control" data-prev-val="<?php echo addslashes($data->patient_data->ptPhoneticName);?>" />
                    </div>
                    
                    <div class="col-sm-3">
                    	<label for="nick_name">Nick Name</label>
                     	<input type="text" name="nick_name" id="nick_name" value="<?php echo stripslashes($data->patient_data->ptNickName);?>" class="form-control" data-prev-val="<?php echo addslashes($data->patient_data->ptNickName);?>" />
                    </div>
                  
                  </div>
                </div>
              	
            	</div>
              
              <div class="clearfix"></div>
              
              <div class="mt5">
                
                <div class="row"> 		
                	<div class="col-sm-12 allcomunication">
                      <h2 class="head">
                        <div class="radio radio-inline">
                          <input type="radio" name="all_communication" id="all_communication" value="0" checked="checked" class="css-checkbox" autocomplete="off">
                          <label for="all_communication">All Communication</label>
                        </div>
                        <span id="add_new_address" title="Add More" class="pointer pull-right">
                        	<i class="glyphicon glyphicon-plus"></i>
                       	</span>
                        
                        	
                      </h2>
									</div>
                </div>	    
                
                <div class="plr10" id="div_all_addresses">
                  <div class="row">
                		<div class="col-xs-12 pt-box"><div class="row grid-box" tabindex="0">
                   	
                    <div class="col-sm-6">
                      <label for="street">Street1</label>
                      <input type="hidden" name="id_address[0]" value="<?php echo $data->patient_data->default_address;?>">
                      <input name="street[0]" id="street" type="text" class="form-control" value="<?php echo stripslashes($data->patient_data->ptStreet);?>" data-prev-val="<?php echo addslashes($data->patient_data->ptStreet);?>" />
                      <input type="hidden" name="hidd_prev_street" id="hidd_prev_street" value="<?php echo $data->patient_data->ptStreet;?>">
                    </div>
                    
                    <div class="col-sm-6">
                      <label for="street2">Street2</label>
                      <input name="street2[0]" id="street2" type="text" class="form-control" value="<?php echo stripslashes($data->patient_data->ptStreet2);?>" data-prev-val="<?php echo addslashes($data->patient_data->ptStreet2);?>" />
                      <input type="hidden" name="hidd_prev_street2" id="hidd_prev_street2" value="<?php echo $data->patient_data->ptStreet2;?>">
                    </div>
                    
                    <div class="col-sm-4">
                      <label for="code"><?php getZipPostalLabel(); ?></label>
                      <div class="clearfix"></div>
                      <input type="hidden" name="hidd_prev_postal_code" id="hidd_prev_postal_code" value="<?php echo $data->patient_data->ptPostalCode;?>">
                      <div class="row">
                        <div class="col-xs-<?php echo (inter_zip_ext() ? '6':'12');?>">
                          <input name="postal_code[0]" type="text" class="form-control" id="code" onBlur="zip_vs_state(this.value,'edit_patient',this);" value="<?php echo stripslashes($data->patient_data->ptPostalCode); ?>" data-prev-val="<?php echo addslashes($data->patient_data->ptPostalCode);?>" maxlength="<?php echo inter_zip_length();?>" size="<?php echo inter_zip_length();?>">
                        </div>
                        <?php if(inter_zip_ext()){?>
                          <div class="col-xs-6">
                            <input name="zip_ext[0]" type="text" id="zip_ext" class="form-control" value="<?php echo stripslashes($data->patient_data->ptzip_ext); ?>" data-prev-val="<?php echo addslashes($data->patient_data->ptzip_ext);?>" maxlength="4" size="4">
                          </div>
                        <?php }?>
                      </div>        
                    </div>	    
                    
                    <div class="col-sm-2">
                      <label for="city">City</label>
                      <input name="city[0]" type="text" class="form-control" id="city" value="<?php echo trim(stripslashes($data->patient_data->ptCity)); ?>" data-prev-val="<?php echo addslashes($data->patient_data->ptCity);?>" />
                      <input type="hidden" name="hidd_prev_city" id="hidd_prev_city" value="<?php echo $data->patient_data->ptCity;?>">
                    </div>
                    
                    <div class="col-sm-2">
                      <label for="state"><?php echo ucwords(inter_state_label());?></label>
                      <input name="state[0]" type="text" maxlength="<?php if(inter_state_val() == "abb") echo '2';?>" class="form-control" id="state" value="<?php echo trim(stripslashes($data->patient_data->ptState));?>" data-prev-val="<?php echo addslashes($data->patient_data->ptState);?>">
                      <input type="hidden" name="hidd_prev_state" id="hidd_prev_state" value="<?php echo trim($data->patient_data->ptState);?>">
                    </div>
                    
                    <div class="col-sm-2">
                      <label for="county">County</label>
                      <input name="county[0]" type="text" class="form-control" id="county" value="<?php echo trim(stripslashes($data->patient_data->county));?>" data-prev-val="<?php echo addslashes($data->patient_data->county);?>" />
                      <input type="hidden" name="hidd_prev_county" id="hidd_prev_county" value="<?php echo trim($data->patient_data->county);?>">
                    </div>
                    
                    <div class="col-sm-2">
                      <label for="country_code">Country</label>
                      <input name="country_code[0]" type="text" class="form-control" id="country_code" value="<?php echo trim(stripslashes($data->patient_data->country_code));?>" data-prev-val="<?php echo addslashes($data->patient_data->country_code);?>" />
                      <input type="hidden" name="hidd_prev_country" id="hidd_prev_country" value="<?php echo trim($data->patient_data->country_code);?>">
                    </div>
                    
                    <div class="col-sm-4">
                      <?php $checked = ($data->patient_data->preferr_contact == 0) ? 'checked' : ''; ?>
                      <div class="radio nowrap mt5 mb5">
                        <input id="pf_contact_home" type="radio" name="pf_contact" title="Preferred" value="0" <?php echo $checked;?> data-prev-val="<?php echo $checked;?>" />
                        <label for="pf_contact_home">Home Phone <?php getHashOrNo(); ?></label>
                      </div>
                      <input name='phone_home' id="phone_home" type='text' class="form-control" value='<?php echo stripslashes(core_phone_format($data->patient_data->ptPhoneHome)); ?>' data-prev-val="<?php echo addslashes(core_phone_format($data->patient_data->ptPhoneHome));?>" maxlength="<?php echo inter_phone_length();?>"/>
                      <input type="hidden" name="hidd_prev_phone_home" id="hidd_prev_phone_home" value="<?php echo trim($data->patient_data->ptPhoneHome);?>">
                    </div>
                    
                    <div class="col-sm-4">
                      <div class="row">
                        <div class="col-sm-8">
                            
                          <?php $checked = ($data->patient_data->preferr_contact == 1) ? 'checked' : ''; ?>
                          <div class="radio nowrap mt5 mb5">
                            <input id="pf_contact_work" type="radio" name="pf_contact" title="Preferred" value="1" <?php echo $checked;?> data-prev-val="<?php echo $checked;?>" />
                            <label for="pf_contact_work">Work Phone <?php getHashOrNo(); ?> </label>
                          </div>
                          <input name='phone_biz' id="phone_biz" type='text'  class="form-control" value='<?php echo stripslashes(core_phone_format($data->patient_data->ptPhoneBiz)); ?>' data-prev-val="<?php echo addslashes(core_phone_format($data->patient_data->ptPhoneBiz));?>" maxlength="<?php echo inter_phone_length();?>"/>
                          <input type="hidden" name="hidd_prev_phone_biz" id="hidd_prev_phone_biz" value="<?php echo trim($data->patient_data->ptPhoneBiz);?>">
                        </div>
                        
                        <div class="col-sm-4">
                          <label for="phone_biz_ext" style="margin-top:6px; margin-bottom:6px;" class="extno">Ext.</label>
                          <input type="text" name="phone_biz_ext" id="phone_biz_ext" value="<?php echo $data->patient_data->ptPhoneBiz_ext;?>" data-prev-val="<?php echo $data->patient_data->ptPhoneBiz_ext;?>" class="form-control">
                        </div>	
                      </div>
                    </div>
                    
                    <div class="col-sm-4">
                      <?php $checked = ($data->patient_data->preferr_contact == 2) ? 'checked' : ''; ?>
                      <div class="radio nowrap mt5 mb5">
                        <input id="pf_contact_mobile" type="radio" name="pf_contact" title="Preferred" value="2" <?php echo $checked;?> data-prev-val="<?php echo $checked;?>" />
                        <label for="pf_contact_mobile">Mobile Phone <?php getHashOrNo(); ?></label>
                      </div>
                      <input name='phone_cell' id="phone_cell" type='text'  class="form-control" value='<?php echo stripslashes(core_phone_format($data->patient_data->ptPhoneCell)); ?>' data-prev-val="<?php echo addslashes(core_phone_format($data->patient_data->ptPhoneCell));?>" maxlength="<?php echo inter_phone_length();?>"/>
                          <input type="hidden" name="hidd_prev_phone_cell" id="hidd_prev_phone_cell" value="<?php echo trim($data->patient_data->ptPhoneCell);?>">
                    </div>
                    
                    <div class="col-sm-8">
                      <label for="ptDemoEmail">Email-Id</label>
                      <input name="ptDemoEmail" id="ptDemoEmail" type="text" class="form-control" value="<?php echo $data->patient_data->ptEmail?>" onkeypress="search_email(event, 'div_email_section', 15, 393)" data-prev-val="<?php echo addslashes($data->patient_data->ptEmail);?>" autocomplete="false" />
                      <div name="div_email_section" id="div_email_section" style="width: auto; display: none; position: absolute; bottom: 30px; z-index: 100; margin-top: 0px;">
                        <select size="4" onkeypress="select_option(event, this, 'ptDemoEmail')" onclick="select_option_with_mouse(this, 'ptDemoEmail')" class="list-group pd0 margin_0 pointer" style="width:100%;">
                            <option class="list-group-item pd3" selected="" value="@aol.com">aol.com</option>
                            <option class="list-group-item pd3" value="@gmail.com">gmail.com</option>
                            <option class="list-group-item pd3" value="@hotmail.com">hotmail.com</option>
                            <option class="list-group-item pd3" value="@msn.com">msn.com</option>
                            <option class="list-group-item pd3" value="@yahoo.com">yahoo.com</option>
                        </select>
                      </div>
                      <input type="hidden" name="hidd_prev_email" id="hidd_prev_email" value="<?php echo trim($data->patient_data->ptEmail);?>">
                      <?php 
                        $eMailEvent = "";
                        if($blClientBrowserIsIpad == true){
                          $eMailEvent = "onChange=\"select_option_with_mouse(this, 'ptDemoEmail');\"";
                        }
                        else{
                          $eMailEvent = "onClick=\"select_option_with_mouse(this, 'ptDemoEmail');\"";
                        }
                      ?>
                    </div>
                    
                    <div class="col-sm-4 ">
                      <label>&nbsp;</label>
                      <div class="checkbox pointer">
                        <input name="chk_mobile" id="chk_mobile" value="1" type="checkbox" <?php echo ($data->patient_data->ptChkMobile==1 ? "checked" : "" ); ?> onClick="top.chk_change_in_form('<?php echo ($data->patient_data->ptChkMobile==1 ? "checked" : "" ); ?>',this,'DemoTabDb',event);">
                        <label for="chk_mobile">Mobile</label>
                      </div>    
                    </div>
										</div></div>	
                    <div class="clearfix mb5"></div>
                  
                    <!-- Multiple Blocks Start Here -->
                    
                    <div id="div_addlist" class="row">
                      <div id="address_grid">
                        <input type="hidden" name="address_del_id" id="address_del_id">
                        <?php 
                          if(is_array($data->all_comm) && count($data->all_comm) > 0 )
                          {
                            $address_cnt = 0;
                            foreach($data->all_comm as $row)
                            {
                              $address_cnt++;	
                        ?>
                             	<div class="col-xs-12 pt-box"><div class="row grid-box" tabindex="0">
                              <div id="div_address<?php echo $address_cnt;?>">
                                <input type="hidden" name="id_address[<?php echo $address_cnt;?>]" value="<?php echo $row['id'];?>">
                                <!-- Header -->   
                                
                                  <div class="" >
                                  	<div class="col-sm-12">
                                    <h2 class="head">
                                      <div class="radio radio-inline">
                                        <input type="radio" name="all_communication" id="all_communication<?=$address_cnt?>" autocomplete="off" value="<?php echo $address_cnt;?>">
                                        <label for="all_communication<?=$address_cnt?>">All Communication</label>
                                      </div>
                                       <span id="address_close" title="Delete Address" onClick="del_address('<?php echo $address_cnt;?>','<?php echo $row['id'];?>')" class=" pull-right margin-top-20 pointer"><i class="glyphicon glyphicon-remove"></i></span>
                                    </h2>
                                    </div>
                                  </div>
                                
                                
                                <!-- Body -->
                                
                                <div class="col-xs-12 ">
                  
                                  <div class="col-xs-6 ">
                                    <label for="street_<?php echo $address_cnt;?>">Street 1</label>
                                    <input name="street[<?php echo $address_cnt;?>]" id="street_<?php echo $address_cnt;?>" type="text" value="<?php echo $row['street'];?>" class="form-control" />
                                  </div>
                    
                                  <div class="col-xs-6 ">
                                    <label for="street2_<?php echo $address_cnt;?>">Street 2</label>
                                    <input name="street2[<?php echo $address_cnt;?>]" id="street2_<?php echo $address_cnt;?>" type="text" class="form-control" value="<?php echo $row['street2'];?>" />
                                  </div>
                    
                                  <div class="col-xs-6">
                                    <label for="code<?php echo $address_cnt;?>"><?php getZipPostalLabel(); ?></label>
                                    <div class="row">
                                      <div class="col-xs-<?php echo (inter_zip_ext() ? '6' : '12');?>">
                                        <input class="form-control" name="postal_code[<?php echo $address_cnt;?>]" type="text" id="code<?php echo $address_cnt;?>" onChange="zip_vs_state_R6(this,document.getElementsByName('city[<?php echo $address_cnt;?>]'),document.getElementsByName('state[<?php echo $address_cnt;?>]'),document.getElementsByName('country_code[<?php echo $address_cnt;?>]'),document.getElementsByName('county[<?php echo $address_cnt;?>]'));" onBlur="zip_vs_state_R6(this,document.getElementsByName('city[<?php echo $address_cnt;?>]'),document.getElementsByName('state[<?php echo $address_cnt;?>]'),document.getElementsByName('country_code[<?php echo $address_cnt;?>]'),document.getElementsByName('county[<?php echo $address_cnt;?>]'));" value="<?php echo $row['postal_code'];?>" data-prev-val="" maxlength="<?php echo inter_zip_length();?>">
                                      </div>
                                       
                                      <?php if(inter_zip_ext()){ ?>
                                      <div class="col-xs-6">
                                        <input name="zip_ext[<?php echo $address_cnt;?>]" type="text" id="zip_ext_<?php echo $address_cnt;?>" value="<?php echo $row['zip_ext'];?>" class="form-control" maxlength="4" />
                                      </div>   
                                      <?php }?>
                                    </div>
                                  </div>
                    
                                  <div class="col-xs-6">
                                    <label for="city_<?php echo $address_cnt;?>">City</label>
                                    <input name="city[<?php echo $address_cnt;?>]" type="text" id="city_<?php echo $address_cnt;?>" value="<?php echo $row['city'];?>" class="form-control" />
                                  </div>
                                  
                                  <div class="col-xs-2">
                                    <label for="state_<?php echo $address_cnt;?>"><?php echo ucwords(inter_state_label());?></label>
                                    <input name="state[<?php echo $address_cnt;?>]" type="text" maxlength="<?php if(inter_state_val() == "abb")echo '2';?>" id="state_<?php echo $address_cnt;?>" value="<?php echo $row['state'];?>" class="form-control">
                                  </div>
                                  
                                  <div class="col-xs-6">
                                    <label for="county_<?php echo $address_cnt;?>">County</label>
                                    <input name="county[<?php echo $address_cnt;?>]" type="text" class="form-control" id="county_<?php echo $address_cnt;?>" value="<?php echo $row['county'];?>" />
                                  </div>
                                  
                                  <div class="col-xs-4">
                                    <label for="country_code_<?php echo $address_cnt;?>">Country</label>
                                    <input name="country_code[<?php echo $address_cnt;?>]" type="text" id="country_code_<?php echo $address_cnt;?>" value="<?php echo $row['country_code'];?>" class="form-control" />
                                  </div>
                    
                                  <div class="clearfix mb5"></div>
                    
                                </div>
                                
                            	</div>
															</div></div>
                        <?php 
                            }
                          }
                        ?>
                      </div>
                      <!-- END BLOCK FOR MULTIPLE ADDRESSES ------------->  
                      </div>
                
                  </div>
                </div>
        
              </div>
              
              <div class="clearfix "></div>
              </div>
            </div>
            
            <div class="col-sm-4">
              <div class="demobox">
                <h2 class="head">MORE INFORMATION</h2>
                <div class="clearfix"></div>
                
                <div class="plr10 pt-box">
                  <div class="row grid-box" tabindex="0">
                    
                    <div class="col-sm-4">
                      <div class="morinfo" >
                      	<div class="" id="ptImageDiv">
                        <?php 
													echo show_thumb_image($data->patient_data->patient_image,100,80);
                        ?>
                        </div>
                        <button class="btn btn-success f-bold width-100 mt2" onClick="scan_patient_image();" type="button">UPLOAD</button>
                      </div>
                  	</div>
                    
                    <div class="col-sm-8">
                      <div class="pdlr15">
                        <div class="row">
                          <?php
                            $created_by =	$data->patient_data->ptCreatedBy;
                            $c_user = get_extract_record('users','id',$created_by);
                            $name_disp = core_name_format($c_user['lname'], $c_user['fname'], $c_user['mname']);
                            $tdate = get_date_format(date('Y-m-d',strtotime($data->patient_data->ptRegDate)));
                          ?>
                          <div class="col-sm-6">
                            <label for="created_by_name">Created By</label>
                            <input tabindex="-1" type="text" readonly name="created_by_name" id="created_by_name" value="<?php echo $name_disp; ?>" data-prev-val="<?php echo $name_disp; ?>" class="form-control showDisabled" >
                            <input type="hidden" name="created_by" id="created_by" value="<?php echo $created_by; ?>">
                          </div>
                          
                          <div class="col-sm-6">
                            <label for="reg_date">Registration Date</label>
                            <input tabindex="-1" readonly name="reg_date" id="reg_date" type="text" title="<?php echo inter_date_format();?> date of onset" value="<?php echo $tdate;?>" data-prev-val="<?php echo $tdate;?>" class="form-control" maxlength="10" onFocus="get_focus_obj(this);"/>
                          </div>
                          
                          <div class="col-sm-6">
                            <label for="">Driving License</label>
                            <input name="dlicence" id="dlicence" type="text" class="form-control" value="<?php echo stripslashes($data->patient_data->ptDrivingLicence);?>" data-prev-val="<?php echo addslashes($data->patient_data->ptDrivingLicence); ?>" />
                            <div class="row">
                              <div class="col-xs-8" >
                                <button class="btn btn-primary f-bold width-100 mt5" type="button" id="btPatScanDo" onClick="<?php if(core_check_privilege(array("priv_vo_pt_info")) == true){ ?> view_only_acc_call(1); return false; <?php }else{ ?>scan_licence(<?php echo $pid;?>);<?php } ?>">License</button>
                              </div>
                             
                              <div class="col-sm-4">
                                <input type="text" style="height:0px; width:0px;position:absolute;left:-100000px" id="scanner" autocomplete="off">
                                <button type="button" class="btn btn-default mt5 mb5" onclick="set_Focus()">
                                  <i class="glyphicon glyphicon-print"></i>
                                </button>
                              </div>
                            </div>
                   
                          </div>
                          
                          <div class="col-sm-6">
                            <div class="previmg" id="ptLicDiv">
                              <?php
															$thumbImageSrc = '';
															if( $data->patient_data->pt_license_image){
																$tmpArr = explode("/",$data->patient_data->pt_license_image);
																$lKey = count($tmpArr)-1;
																$tmpArr[$lKey] = 'thumbnail/'.end($tmpArr);
																$thumbImageSrc = implode('/',$tmpArr);
																
																$thumbImageSrc = file_exists($thumbImageSrc) ? $thumbImageSrc : $data->patient_data->pt_license_image;
																echo show_thumb_image($thumbImageSrc,80,60); ?>
                              	<span class="layer" data-toggle='modal' data-target='#imageLicense'></span>
                              <?php } ?>	
                            </div>
                            
                          </div>
                          
                        </div>
                      </div>
                    </div>
                  
                  </div>
                </div>
                
                <div class="clearfix"></div>
                
                <div class="panel-group accordion" id="accordion" role="tablist" aria-multiselectable="true">
                  
                  <?php if(constant("ENABLE_REAL_ELIGILIBILITY") == "YES") { ?>
                    <div class="panel panel-default">
                      <div class="panel-heading" role="tab" id="headingOne">
                        <h4 class="panel-title">
                          <a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseOne" aria-expanded="true" aria-controls="collapseOne">Eligibility Last Check Detail</a>
                        </h4>
                      </div>
                      
                      <div id="collapseOne" class="panel-collapse collapse pt-box" role="tabpanel" aria-labelledby="headingOne">
                        <div class="panel-body">
                          <?php if($data->elig_data->elig_count > 0) { ?>
                            <div class="col-sm-12 grid-box" tabindex="0" >
                              <div class="row">
                                <table class="table mb0">
                                  <thead>
                                    <tr>
                                      <th class="col-sm-3 text-center" title="<?php echo $data->elig_data->attr_title; ?>">Status</th>
                                      <th class="col-sm-3 text-center">Date</th>
                                      <th class="col-sm-2 text-center">Time</th>
                                      <th class="col-sm-2 text-center">DEC</th>
                                      <th class="col-sm-2 text-center">OP</th>
                                    </tr>
                                  </thead>
                                  <tbody>
                                    <tr class="nowrap">
                                      <td class="col-sm-2 text-center">
                                        <a href="javascript:void(0);" onClick="get271Report('<?php echo $data->elig_data->elig_id; ?>')" class="<?php echo $data->elig_data->attr_class?>"><?php echo $data->elig_data->elig_resp; ?></a>
                                      </td>
                                      <td class="col-sm-2 text-center">
                                        <a href="javascript:void(0);" onClick="get271Report('<?php echo $data->elig_data->elig_id; ?>')" class="<?php echo $data->elig_data->attr_class?>"><?php echo $data->elig_data->elig_date; ?></a>
                                      </td>
                                      <td class="col-sm-2 text-center">
                                        <a href="javascript:void(0);" onClick="get271Report('<?php echo $data->elig_data->elig_id; ?>')" class="<?php echo $data->elig_data->attr_class?>"><?php echo $data->elig_data->elig_time; ?></a>
                                      </td>
                                      <td class="col-sm-2 text-center">
                                        <a href="javascript:void(0);" onClick="get271Report('<?php echo $data->elig_data->elig_id; ?>')" class="<?php echo $data->elig_data->attr_class?>"><?php echo $data->elig_data->elig_dec; ?></a>
                                      </td>
                                      <td class="col-sm-2 text-center">
                                        <a href="javascript:void(0);" onClick="get271Report('<?php echo $data->elig_data->elig_id; ?>')" class="<?php echo $data->elig_data->attr_class?>"><?php echo $data->elig_data->elig_op; ?></a>
                                      </td>
                                      
                                    </tr> 
                                  </tbody>
                                </table>
                              </div>	
                            </div>
                          <?php
                              }
                            else
                            {
                              echo '<div class="col-sm-12 pd10 grid-box" tabindex="0">No Last Check Detail Found!</div>';
                            }
                          ?>
                        </div>
                      </div>
                    </div>
                  <?php } ?>
                  
                  <div class="panel panel-default">
                    
                    <div class="panel-heading" role="tab" id="headingTwo">
                      <h4 class="panel-title">
                        <a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">Notes</a>
                      </h4>
                    </div>
                    
                    <div id="collapseTwo" class="panel-collapse collapse in pt-box" role="tabpanel" aria-labelledby="headingTwo">
                      <div class="panel-body grid-box" tabindex="0">
                        
                        <div class="col-sm-12 mt5">
                          <textarea id="patient_notes" name="patient_notes" class="form-control notetxtara" data-prev-val="<?php echo addcslashes(addslashes($data->patient_data->ptPatientNotes)); ?>"><?php echo stripslashes($data->patient_data->ptPatientNotes); ?></textarea>
                        </div>   
                        
                        <div class="clearfix"></div>
          
                        <!-- CheckBox Scheduler -->
                        <div class="col-sm-3">
                        <?php $checked = ($data->patient_data->ptchkNotesScheduler == 1) ? 'checked' : ''; ?>
                          <div class="checkbox">
                            <input type="checkbox" id="chkNotesScheduler" name="chkNotesScheduler" <?php echo $checked; ?> value="1" data-prev-val="<?php echo $checked; ?>" />
                            <label for="chkNotesScheduler">Scheduler</label>
                          </div>
                        </div>
                        <!-- CheckBox Chart Notes -->
                        <div class="col-sm-3">
                        <?php $checked = ($data->patient_data->ptChkNotesChartNotes == 1) ? 'checked' : ''; ?>
                          <div class="checkbox">
                            <input type="checkbox" id="chkNotesChartNotes" name="chkNotesChartNotes"<?php echo $checked; ?> value="1" data-prev-val="<?php echo $checked; ?>" />
                            <label for="chkNotesChartNotes">Chart&nbsp;Notes</label>
                          </div>
                        </div>
                        <!-- CheckBox Accounting -->
                        <div class="col-sm-3">
                        <?php $checked = ($data->patient_data->ptChkNotesAccounting == 1) ? 'checked' : ''; ?>
                          <div class="checkbox">
                            <input type="checkbox" id="chkNotesAccounting" name="chkNotesAccounting" <?php echo $checked; ?> value="1" data-prev-val="<?php echo $checked; ?>" />
                            <label for="chkNotesAccounting">Accounting</label>
                          </div>
                        </div>
                        <!-- CheckBox Optical -->
                        <div class="col-sm-3">
                        <?php $checked = ($data->patient_data->ptChkNotesOptical == 1) ? 'checked' : ''; ?>
                          <div class="checkbox">
                            <input type="checkbox" id="chkNotesOptical" name="chkNotesOptical" <?php echo $checked; ?> value="1" data-prev-val="<?php echo $checked; ?>" />
                            <label for="chkNotesOptical">Optical</label>
                          </div>
                        </div>
                        
                      </div>
                    </div>
                    
                  </div>
                  
                  <div class="panel panel-default">
                    
                    <div class="panel-heading" role="tab" id="headingThree">
                      <h4 class="panel-title">
                        <a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                        Emergency Contact
                      </a>
                      </h4>
                    </div>
                    
                    <div id="collapseThree" class="panel-collapse collapse pt-box" role="tabpanel" aria-labelledby="headingThree">
                      <div class="panel-body grid-box" tabindex="0">
                        
                        <div class="col-sm-12">
                          <div class="row">
                          
                            <div class="col-xs-12 col-md-4">
                              <label for="contact_relationship">Emergency Name</label>
                              <input name='contact_relationship' id="contact_relationship" type='text' class="form-control"  value="<?php echo stripslashes($data->patient_data->ptContactRelationship); ?>" data-prev-val="<?php echo addslashes($data->patient_data->ptContactRelationship); ?>" />
                            </div>
                            
                            <div class="col-xs-12 col-md-4">
                              <label>Relationship</label>
                              <select name='emerRelation' id="emerRelation" class="form-control minimal " title="Relationship" data-header="Relationship" data-width="100%">
                                <?php
                                  $arrEmerRelation = get_relationship_array('emergency_relation');
                                  foreach ($arrEmerRelation as $s)
                                  {
                                    echo "<option value='".$s."'";
                                    if ($s == $data->patient_data->ptEmergencyRelationship){
                                      echo " selected";
                                      echo ">".ucfirst($s)."</option>\n";
                                    }
                                    else{
                                      echo ">".ucfirst($s)."</option>\n";
                                    }
                                  }
                                ?>
                              </select>
                              <div id="relation_other_box" class="hidden">
                                <div class="input-group ">
                              		<input type="text" id="relation_other_textbox" name="relation_other_textbox" class="form-control" value="<?php echo $data->patient_data->ptEmergencyRelOther?>" onKeyUp="top.chk_change_in_form('<?php echo addslashes($data->patient_data->ptEmergencyRelOther); ?>',this,'DemoTabDb',event);">
                                  <label class="input-group-btn btn btn-primary btn-xs back_other" data-tab-name="emerRelation" id="imgRelOtherTextBox">
                                  	<i class="glyphicon glyphicon-arrow-left"></i>
                                  </label>  
                                </div>
                             	</div>   
                           	</div>	
      
                            <div class="col-xs-12 col-md-4">
                              <label>Emergency Tel #</label>
                              <input name="phone_contact" id="phone_contact" type='text' class="form-control" onChange="set_phone_format(this,'<?php echo $GLOBALS['phone_format'] ?>');" value="<?php echo stripslashes(core_phone_format($data->patient_data->ptPhoneContact)); ?>" data-prev-val="<?php echo addslashes(core_phone_format($data->patient_data->ptPhoneContact));?>" onKeyUp="top.chk_change_in_form('<?php echo addslashes(core_phone_format($data->patient_data->ptPhoneContact)); ?>',this,'DemoTabDb',event);" onKeyPress="save_data(event);" onFocus="get_focus_obj(this);" />
                            </div>      
                            
                            <div class="clearfix mb5"></div>
                            
                          </div>
                        </div> 
                        
                      </div>
                    </div>
                    
                  </div>
                  
                  <div class="panel panel-default">
                    
                    <div class="panel-heading" role="tab" id="headingFour">
                      <h4 class="panel-title">
                        <a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseFour" aria-expanded="false" aria-controls="collapseFour">Reminder Choices</a>
                      </h4>
                    </div>
                    
                    <div id="collapseFour" class="panel-collapse collapse pt-box" role="tabpanel" aria-labelledby="headingFour">
                      <div class="panel-body grid-box" tabindex="0">
                        <div class="col-xs-12">
                          
                          <div class="row mt5">
                            <?php
                                if($data->patient_data->ptHipaaMail <> 1){
                                  $data->patient_data->ptHipaaMail = 0;
                                }
                                if($data->patient_data->ptHipaaEmail <> 1){
                                  $data->patient_data->ptHipaaEmail = 0;
                                }
                                if($data->patient_data->ptHipaaVoice <> 1){
                                  $data->patient_data->ptHipaaVoice = 0;
                                }
                            ?>	
                            
                            <div class="col-sm-3">
                              <div class="checkbox checkbox-inline">
                                <?php $checked = ($data->patient_data->ptHipaaMail == 1) ? 'checked' : ''; ?>
                                <input type="checkbox" id="hipaa_mail" name="hipaa_mail" value="1" <?php echo $checked;?> data-prev-val="<?php echo $checked; ?>" />
                                <label for="hipaa_mail">Postal Mail</label>
                              </div>
                            </div>
                              
                            <div class="col-sm-3">
                              <div class="checkbox checkbox-inline">
                                <?php $checked = ($data->patient_data->ptHipaaEmail == 1) ? 'checked' : ''; ?>
                                <input type="checkbox" name="hipaa_email" id="hipaa_email" class="form-control" value="1" <?php echo $checked; ?> data-prev-val="<?php echo $checked; ?>" />
                                <label for="hipaa_email">eMail</label>
                              </div>
                            </div>
                              
                            <div class="col-sm-3">
                              <div class="checkbox checkbox-inline">
                                <?php $checked = ($data->patient_data->ptHipaaVoice == 1) ? 'checked' : ''; ?>
                                <input type="checkbox" name="hipaa_voice" id="hipaa_voice" class="form-control" value="1" <?php echo $checked; ?> data-prev-val="<?php echo $checked; ?>" onClick="display_hide_timmings();"/>
                                <label for="hipaa_voice">Voice</label>
                              </div>
                            </div>
                              
                            <div class="col-sm-3">
                              <div class="checkbox checkbox-inline">
                                <?php $checked = ($data->patient_data->ptHipaaText == 1) ? 'checked' : ''; ?>
                                <input type="checkbox" name="hipaa_text" id="hipaa_text" class="form-control" value="1" <?php echo $checked; ?> data-prev-val="<?php echo $checked; ?>" />
                                <label for="hipaa_text">Text</label>
                              </div>
                            </div>
                            
                            <div class="clearfix"></div>
                          </div>	
                          <!-- Row2 -->
                          
                          <?php
                            $timmingDisp = 'hidden';
                            if($data->patient_data->ptHipaaVoice == 1 )
                            {
                              $timmingDisp = 'show';
                              $query	=	"Select * FROM patient_call_timmings WHERE patient_id='".$patient_id."' ORDER BY id";
                              $sql	=	imw_query($query);
                              $i = 1;
                              
                              $arrTimmings = array();
                              while($row = imw_fetch_assoc($sql))
                              {
                                $arrTimmings[$i]['id'] = $row['id'];
                                if($row['del_status']=='0'){
                                  $ampm='AM';
                                  $fTime=explode(':', $row['time_from']);
                                  if($fTime[0]>12){ $fTime[0]=$fTime[0]-12; $ampm='PM'; } 
                                  $arrTimmings[$i]['hourFrom']=$fTime[0];
                                  $arrTimmings[$i]['minFrom']=$fTime[1];
                                  $arrTimmings[$i]['ampmFrom']=$ampm;
                                  $ampm='AM';
                                  $tTime=explode(':', $row['time_to']);
                                  if($tTime[0]>12){ $tTime[0]=$tTime[0]-12; $ampm='PM'; }
                                  $arrTimmings[$i]['hourTo']=$tTime[0];
                                  $arrTimmings[$i]['minTo']=$tTime[1];
                                  $arrTimmings[$i]['ampmTo']=$ampm;
                                }
                                $i++;
                              }
                            }
                          ?>
                          <div  id="trVoiceTimmings" class="<?php echo $timmingDisp; ?>">
                            
                            <div class="row">
                              <div class="col-xs-5"><label>From Time</label></div>
                              <div class="col-xs-5"><label>To Time</label></div>
                              <div class="col-xs-1">&nbsp;</div>
                            </div>	
                              
                            <?php for($tloop = 1; $tloop < 5; $tloop++) { ?>   
                            
                            <div class="row mb5">
                              <input type="hidden" name="timeId<?=$tloop?>" value="<?php echo $arrTimmings[$tloop]['id'];?>">
                              <div class="col-xs-5">
                                <div class="row">
                                
                                  <div class="col-xs-4">
                                    <select name="hourFrom<?=$tloop?>" id="hourFrom<?=$tloop?>" class="form-control minimal" data-width="100%" title="">
                                      <option value="" ></option>
                                      <?php echo $patient_data_obj->time_numbers(12, $arrTimmings[$tloop]['hourFrom']); ?>
                                    </select>
                                  </div>
                                  
                                  <div class="col-xs-4 ">
                                    <div class="row">
                                      <div class="col-xs-2 text-center" style="padding:0 !important;"><b>:</b></div>  	
                                      <div class="col-xs-10" style="padding:0 !important;">
                                        <select name="minFrom<?=$tloop?>" id="minFrom<?=$tloop?>" class="form-control minimal" data-width="100%"  title="">
                                        <?php echo $patient_data_obj->time_numbers(59, $arrTimmings[$tloop]['minFrom']); ?>
                                        </select>
                                      </div>
                                    </div>	    	   
                                  </div>
                                  
                                  <div class="col-xs-4">
                                    <select name="ampmFrom<?=$tloop?>" id="ampmFrom<?=$tloop?>" class="form-control minimal" data-width="100%" title="" >
                                      <option value="AM" <?php if($arrTimmings[$tloop]['ampmFrom']=='AM')echo 'selected';?> >AM</option>
                                      <option value="PM" <?php if($arrTimmings[$tloop]['ampmFrom']=='PM')echo 'selected';?>>PM</option>
                                    </select>
                                  </div>
                                  
                                </div>
                              </div>
                              
                              <div class="col-xs-5">
                                <div class="row">
                                  
                                  <div class="col-xs-4">
                                    <select name="hourTo<?=$tloop?>" id="hourTo<?=$tloop?>" title="" class="form-control minimal" data-width="100%">
                                      <option value=""></option>
                                      <?php echo $patient_data_obj->time_numbers(12, $arrTimmings[$tloop]['hourTo']); ?>
                                    </select>
                                  </div>
                                   
                                  <div class="col-xs-4">
                                    <div class="row">
                                      <div class="col-xs-2 text-center" style="padding:0 !important;"><b>:</b></div>  	
                                      <div class="col-xs-10" style="padding:0 !important;">
                                        <select name="minTo<?=$tloop?>" id="minTo<?=$tloop?>" title="" class="form-control minimal" data-width="100%">
                                        <?php echo $patient_data_obj->time_numbers(59, $arrTimmings[$tloop]['minTo']); ?>
                                        </select>
                                      </div>
                                    </div>
                                  </div>    	   
                                  
                                  <div class="col-xs-4">
                                    <select name="ampmTo<?=$tloop?>" id="ampmTo<?=$tloop?>" class="form-control minimal" title="" data-width="100%">
                                      <option value="AM" <?php if($arrTimmings[$tloop]['ampmTo']=='AM')echo 'selected';?>>AM</option>
                                      <option value="PM" <?php if($arrTimmings[$tloop]['ampmTo']=='PM')echo 'selected';?>>PM</option>
                                    </select>
                                  </div>
                                  
                                </div>
                              </div>
                              
                              <div class="col-xs-1">
                                <span id="imgDeleteTime<?=$tloop?>" class="pointer" title="Delete Call Timing" onClick="delete_call_timming('<?=$tloop?>');"><i class="glyphicon glyphicon-remove"></i></span>
                              </div>
                               
                            </div> 
                            
                            <?php } ?>   
                                                                  
                          </div>
                      
                        </div>
                      </div>
                    </div>
                      
                  </div>
                
                </div>
        
              </div>
            </div>
            
            <div class="col-sm-4">
              <div class="demobox">
                <h2 class="head">PREFERENCES</h2>
                <figure><button class="btn btn-primary btn-xs mt0" style="margin-top: -4px; padding-top: 1px;" type="button" onClick="top.popup_win('../../chart_notes/erx_patient_selection.php?loadmodule=ptdemo');" >Pharmacy Pref.</button></figure>
                
                <div class="clearfix"></div>
                
                <div class="panel-group accordion" id="accordion1" role="tablist" aria-multiselectable="true">
                  
                  <div class="panel panel-default pancolr">
                  
                    <div class="panel-heading" role="tab" id="phyFacility">
                      <h4 class="panel-title">
                        <a role="button" href="javascript:void(0)" aria-expanded="true" aria-controls="collapseOne">Physician(s) &amp; Facility</a>
                      </h4>
                    </div>
                    
                    <div id="collapsePhy" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="phyFacility">
                      <div class="panel-body ">
                        <div class="col-xs-12 mb5">
                            
                          <div class="row">
                            
                            <div class="col-sm-4">
                              <label>Primary Eye Care</label>
                              <?php																																
                                //--- GET PROVIDER ID FROM LAST APPOINTMENT IF NOT EXISTS ----sa_facility_id
                                if(empty($data->patient_data->ptProviderID) == true){
                                  $data->patient_data->ptProviderID = $last_appointment[0]['sa_doctor_id'];
                                }
                              ?>
                              <select class="form-control minimal" data-width="100%" title="<?php echo imw_msg('drop_sel'); ?>" data-header="<?php echo imw_msg('drop_sel'); ?>" name="providerID" id="providerID" data-prev-val="<?php echo $data->patient_data->ptProviderID; ?>">
                                <option value=" "> </option>
                                <?php
                                  echo $OBJCommonFunction->drop_down_providers($data->patient_data->ptProviderID,'','1');
                                ?>
                              </select>
                            </div>
                          
                            <div class="col-sm-4">
                              <label><a id="spanAddRefPhy" class="text_purple" href="javascript: void(0);" onClick="show_multi_phy(1,1);" >Referring Phy.</a></label>
                              <?php
                                $physician_name = $OBJCommonFunction->get_ref_phy_name($data->patient_data->ptReferringPhyID);
                                $ref_phy_status = $OBJCommonFunction->get_ref_phy_del_status($data->patient_data->ptReferringPhyID);
                                $ref_phy_class = "";
                                if($ref_phy_status) {
                                    $ref_phy_class = " red-font ";
                                }
                                $physician_id	= $data->patient_data->ptReferringPhyID;
                                $multi_phy_str = $physician_name;
                                $popover_content = '';
                                if( $physician_name ) {
                                  $arr = get_reffphysician_detail($physician_id,'array','Address1,Address2,ZipCode,City,State,comments,PractiseName');
                                  $format_str = str_replace("'","&#8217;",format_ref_data($arr));
                                  $popover_content .= '<span class="col-xs-12 '.($ref_phy_class).'"><b>&bull; '.str_replace("'","&#8217;",$physician_name).'</b><br>';
                                  $popover_content .= $format_str.'</span>';
		                            }
                                $tempArrId = $data->multi_rp->id;
                                $tempArrName = $data->multi_rp->name;
                                $tempArrStatus = $data->multi_rp->status;
                                $tempArrAddress = $data->multi_rp->address;

                                if( count($tempArrId) > 1 ) {
                                  $popover_content .= '<span class="col-xs-12 border-dashed"></span>';
                                }
                                for($i = 0; $i<count($tempArrId);$i++)
                                {
                                  if($tempArrId[$i] <> $physician_id && $physician_id != "") {
                                    $multi_phy_str .= "; ".$tempArrName[$i];
                                    $str = "";
                                    $str .= '<span class="col-xs-12 '.($tempArrStatus[$i]?"red-font":"").'"><b>&bull; '.str_replace("'","&#8217;",$tempArrName[$i]).'</b><br>';
                                    $str .= str_replace("'","&#8217;",$tempArrAddress[$i]).'</span>';
		                              	$str .= (($i+1) < count($tempArrId)) ? '<span class="col-xs-12 border-dashed"></span>' : "";
                                    $popover_content .= $str;
                                  }
                                }
                                
                                if(trim($physician_name) == ',')
                                {
                                  $physician_name = '';
                                }
                                $physician_name = ltrim($multi_phy_str,"; ");
                                $physician_name = trim(stripslashes($physician_name));
                              ?>
                              <!-- title="<?php echo $physician_name; ?>" -->
                              <input type="hidden" name="pcare" id="pcare" value="<?php echo $physician_id; ?>">
                              <div class="input-group">
                                <input name="pcare2" type="text" id="elem_physicianName" class="form-control <?php echo $ref_phy_class; ?>"  value="<?php echo $physician_name; ?>" data-prev-val="<?php echo addslashes($physician_name); ?>"  data-action="search_physician" data-text-box="elem_physicianName" data-id-box="pcare"  onKeyUp="top.loadPhysicians(this,'pcare');" onFocus="top.loadPhysicians(this,'pcare');" data-title= "Referring Physicians" <?php echo show_popover(''.$popover_content.'','bottom','hover');?> />
                                <label class="input-group-addon btn search_physician" data-source="elem_physicianName" >
                                  <span class="glyphicon glyphicon-search"></span>
                                </label>
                              </div>
                              <div id="divTest"></div>
                            </div>
                            
                            <div class="col-sm-4">
                              <label><a id="spanAddPCPDemo" class="text_purple" href="javascript: void(0);" onClick="show_multi_phy(1, 4);">Primary Care Phy</a></label>
                              
                              <?php
                                $physician_name = $OBJCommonFunction->get_ref_phy_name($data->patient_data->ptPriCarePhyId);
                                $physician_status = $OBJCommonFunction->get_ref_phy_del_status($data->patient_data->ptPriCarePhyId);
                                $pcp_phy_class = "";
                                if($physician_status) {
                                    $pcp_phy_class = " red-font ";
                                }
                                $physician_id	= $data->patient_data->ptPriCarePhyId;
                                $multi_phy_str = $physician_name;
                                $popover_content = '';
                                if( $physician_name ) {
                                  $arr = get_reffphysician_detail($physician_id,'array','Address1,Address2,ZipCode,City,State,comments,PractiseName');
                                  $format_str = str_replace("'","&#8217;",format_ref_data($arr));
                                  $popover_content .= '<span class="col-xs-12 '.($pcp_phy_class).'"><b>&bull; '.str_replace("'","&#8217;",$physician_name).'</b><br>';
                                  $popover_content .= $format_str.'</span>';
                                }
                                $tempArrId = $data->multi_pcp->id;
                                $tempArrName = $data->multi_pcp->name;
                                $tempArrStatus = $data->multi_pcp->status;
                                $tempArrAddress = $data->multi_pcp->address;

                                if( count($tempArrId) > 1 ) {
                                  $popover_content .= '<span class="col-xs-12 border-dashed"></span>';
                                }

                                for($i = 0; $i<count($tempArrId);$i++)
                                {
                                  if($tempArrId[$i] <> $physician_id && $physician_id != "") {
                                    $multi_phy_str .= "; ".$tempArrName[$i];
                                    $str = "";
                                    $str .= '<span class="col-xs-12 '.($tempArrStatus[$i]?"red-font":"").'"><b>&bull; '.str_replace("'","&#8217;",$tempArrName[$i]).'</b><br>';
                                    $str .= str_replace("'","&#8217;",$tempArrAddress[$i]).'</span>';
		                              	$str .= (($i+1) < count($tempArrId)) ? '<span class="col-xs-12 border-dashed"></span>' : "";
                                    $popover_content .= $str;
                                  }
                                }
                                
                                if(trim($physician_name) == ',')
                                {
                                  $physician_name = '';
                                }
                                $physician_name = ltrim($multi_phy_str,"; ");
                                $physician_name = trim(stripslashes($physician_name));
                              ?>
                          
                              <input type="hidden" name="pCarePhy" id="pCarePhy" value="<?php echo $data->patient_data->ptPriCarePhyId; ?>">	
                              <div class="input-group">
                                <input name="primaryCarePhy" type="text" id="primaryCarePhy" class="form-control <?php echo $pcp_phy_class; ?>"  value="<?php echo $physician_name; ?>" data-prev-val="<?php echo addslashes($physician_name); ?>" data-action="search_physician" data-text-box="primaryCarePhy" data-id-box="pCarePhy" onKeyUp="top.loadPhysicians(this,'pCarePhy');" onFocus="top.loadPhysicians(this,'pCarePhy');" data-title="Primary Care Providers" <?php echo show_popover(''.$popover_content.'','bottom','hover');?> />
                                <label class="input-group-addon btn search_physician" id="search_primaryCarePhy" data-source="primaryCarePhy">
                                  <span class="glyphicon glyphicon-search"></span>
                                </label>
                              </div>
                            </div>
                            
                            <?php
                            $facility_data_arr=array();
                            $vquery_t = "select a.pos_facility_id,a.facilityPracCode,b.pos_prac_code 
                                        from pos_facilityies_tbl a,pos_tbl b where a.pos_id = b.pos_id 
                                        order by a.facilityPracCode asc, a.headquarter desc";
                            $vsql_t = imw_query($vquery_t);
                            while($rs_t = imw_fetch_array($vsql_t)){
                              $facility_data_arr[$rs_t['pos_facility_id']]=$rs_t['facilityPracCode']." - ".$rs_t['pos_prac_code'];
                            }
                            
                            //-------------------- get POS facility from Users POS facility group ------------------
                            //if POS Facilty group exists and selected in logged in user
                            $user_pos_fac_arr=array();
                            if(isPosFacGroupEnabled() ){
                                $u_sql_res=imw_query("Select id,posfacilitygroup_id from users where id='".$_SESSION['authId']."' and posfacilitygroup_id!='' ");
                                $user_row=imw_fetch_assoc($u_sql_res);
                                $user_pos_id_fac_data_arr=array();
                                if(empty($user_row)==false && isset($user_row['posfacilitygroup_id']) && $user_row['posfacilitygroup_id']!='') {
                                    $posfacilitygroup_ids_arr=json_decode(html_entity_decode($user_row['posfacilitygroup_id']), true);
                                    $posfacgroup_ids_str=(empty($posfacilitygroup_ids_arr)==false)? implode(',',$posfacilitygroup_ids_arr): '';

                                    $selQry1 = "select a.pos_facility_id,a.facilityPracCode,b.pos_prac_code 
                                                from pos_facilityies_tbl a,pos_tbl b where a.pos_id = b.pos_id 
                                                and posfacilitygroup_id IN(".$posfacgroup_ids_str.") 
                                                order by a.facilityPracCode asc, a.headquarter desc";
                                    $res1 = imw_query($selQry1);
                                    while($row1 = imw_fetch_assoc($res1)){
                                        $user_pos_fac_arr[]=$row1['pos_facility_id'];	
                                    }
                                }
                            }
                            //-------------------- get POS facility from Users POS facility group ------------------
                            ?>
                              
                            <div class="col-sm-8">
                              <label>Home Facility</label>
                              <?php
                                if(empty($data->patient_data->ptDefaultFacility) == true){
                                  $facility_res = get_facility_details($last_appointment[0]['sa_facility_id']);
                                  $data->patient_data->ptDefaultFacility = $facility_res['fac_prac_code'];
                                }
                              ?>
                              <select name="default_facility" id="default_facility" class="form-control minimal" data-width="100%" title="<?php echo imw_msg('drop_sel'); ?>" data-header="<?php echo imw_msg('drop_sel'); ?>" data-prev-val="<?php echo $data->patient_data->ptDefaultFacility; ?>" >
                              <?php
                                if(empty($facility_data_arr)==false) {
                                    echo("<option value=''></option>");
                                    foreach($facility_data_arr as $key => $val){
                                        $se="";
                                        if($key==$data->patient_data->ptDefaultFacility){
                                            $se="selected";
                                        }
                                        if(empty($user_pos_fac_arr)==false && (!in_array($key,$user_pos_fac_arr)) ) {
                                            if($key == $data->patient_data->ptDefaultFacility)
                                                { /*do not skip already selected pos facility which does not exists in users pos facility group */ }
                                            else 
                                                { /*skip pos facility those does not exists in users pos facility group */
                                                continue; }
                                        }
                                        echo("<option ".$se." value='".$key."'>".$val."</option>");
                                    }
                                }
                              ?>
                              </select>
                            </div>
                            
                            <div class="col-sm-4">
                              <label><a id="spanAddCoMangPhy" class="text_purple" href="javascript:void(0);" onClick="show_multi_phy(1,2);">Co-Managed Phy</a></label>
                              <?php
                                $physician_name = $OBJCommonFunction->get_ref_phy_name($data->patient_data->co_man_phy_id);
                                $physician_status = $OBJCommonFunction->get_ref_phy_del_status($data->patient_data->co_man_phy_id);
                                $physician_id	= $data->patient_data->co_man_phy_id;
                                $multi_phy_str = $physician_name;
                                $popover_content = '';
                                if( $physician_name ) {
                                  $arr = get_reffphysician_detail($physician_id,'array','Address1,Address2,ZipCode,City,State,comments,PractiseName');
                                  $format_str = str_replace("'","&#8217;",format_ref_data($arr));
                                  $popover_content .= '<span class="col-xs-12 '.($physician_status?'red-font':'').'"><b>&bull; '.str_replace("'","&#8217;",$physician_name).'</b><br>';
                                  $popover_content .= $format_str.'</span>';
                                }
                                $tempArrId = $data->multi_cp->id;
                                $tempArrName = $data->multi_cp->name;
                                $tempArrStatus = $data->multi_cp->status;
                                $tempArrAddress = $data->multi_cp->address;
                                
                                if( count($tempArrId) > 1 ) {
                                  $popover_content .= '<span class="col-xs-12 border-dashed"></span>';
                                }
                                for($i = 0; $i<count($tempArrId);$i++)
                                {
                                  if($tempArrId[$i] <> $physician_id && $physician_id != "") {
                                    $multi_phy_str .= "; ".$tempArrName[$i];
                                    $str = "";
                                    $str .= '<span class="col-xs-12 '.($tempArrStatus[$i]?"red-font":"").'"><b>&bull; '.str_replace("'","&#8217;",$tempArrName[$i]).'</b><br>';
                                    $str .= str_replace("'","&#8217;",$tempArrAddress[$i]).'</span>';
		                              	$str .= (($i+1) < count($tempArrId)) ? '<span class="col-xs-12 border-dashed"></span>' : "";
                                    $popover_content .= $str;
                                  }
                                }
                                
                                if(trim($physician_name) == ',')
                                {
                                  $physician_name = '';
                                }
                                $physician_name = ltrim($multi_phy_str,"; ");
                                $physician_name = trim(stripslashes($physician_name));
                              ?>
                              <input type="hidden" name="co_man_phy_id" id="co_man_phy_id" value="<?php echo $data->patient_data->co_man_phy_id; ?>">
                              <div class="input-group">
                                <input class="form-control" name="co_man_phy" type="text" id="co_man_phy" value="<?php echo $physician_name; ?>" data-prev-val="<?php echo addslashes($physician_name); ?>" data-action="search_physician" data-text-box="co_man_phy" data-id-box="co_man_phy_id" onKeyUp="top.loadPhysicians(this,'co_man_phy_id');" onFocus="top.loadPhysicians(this,'co_man_phy_id');" data-title="Co-Managed Physicians" <?php echo show_popover(''.$popover_content.'','bottom','hover');?> />
                                <label class="input-group-addon btn search_physician" id="search_co_man_phy" data-source="co_man_phy">
                                  <span class="glyphicon glyphicon-search"></span>
                                </label>
                              </div>
                            </div>
                            
                          </div>
                          
                        </div>
                      </div>
                    </div>
                    
                  </div>
                </div>
                  
                <div class="panel-group accordion" id="accordion-right" role="tablist" aria-multiselectable="true">   
                      
                  <div class="panel panel-default">
                    
                    <div class="panel-heading" role="tab" id="headingLang">
                      <h4 class="panel-title">
                        <a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion-right" href="#collapseLang" aria-expanded="false" aria-controls="collapseLang">Language, Ethnicity, Race, Occupation</a>
                      </h4>
                    </div>
                    
                    <div id="collapseLang" class="panel-collapse collapse in pt-box" role="tabpanel" aria-labelledby="headingLang">
                      <div class="panel-body grid-box" tabindex="0">
                        <div class="col-xs-12 mb5">
                          
                          <div class="row">
                            <div class="col-sm-4">
                              <label class="purple-text pointer load_modal" data-modal="race_modal"><b>Race</b></label>
                              <?php
																$arrRace = $patient_data_obj->race_modal(1);
																$pt_race = explode(",",$data->patient_data->ptRace);
																$pt_race = array_filter($pt_race);
																$tmpArr  = array_diff($pt_race,$arrRace);
																$tmpNewArr=array_splice($arrRace,-1);
																$arrRace = array_merge($arrRace,$tmpArr,$tmpNewArr);
															?>
                              <select name='race[]' id="race" class="selectpicker" multiple="multiple" data-width="100%" title="<?php echo imw_msg('drop_sel'); ?>" data-header="<?php echo imw_msg('drop_sel'); ?>" data-actions-box='true'>
                              <?php 
                                 																
                                  foreach ($arrRace as $s)
                                  {																
                                    echo "<option value='".$s."' data-common = '1'";
                                    if(in_array($s,$pt_race)){
                                      echo "selected";
                                    }
                                    echo ">".ucfirst($s)."</option>";
                                  }
                              ?>
                              </select>
                            </div>
                            
                            <div class="col-sm-4">
                            	<?php
																$arrLanguage = $patient_data_obj->language_modal(1) ;
																$tmpNewArr = array();
																
																$other_language=substr($data->patient_data->language,0,5);
																if($other_language=='Other'){
                                  $other_language_val=substr($data->patient_data->language,9);
																	$data->patient_data->language = $other_language_val;
                                }
																
                                if( $data->patient_data->language && !in_array($data->patient_data->language,$arrLanguage))
																{
																	$tmpNewArr=array_splice($arrLanguage,-2);	
																	array_push($arrLanguage,$data->patient_data->language);
																	sort($arrLanguage);
																	$arrLanguage = array_merge($arrLanguage,$tmpNewArr);	
																}
															?>
                              <label class="purple-text pointer load_modal" data-modal="language_modal"><b>Language</b></label>
                              <input type="hidden" name="lang_code" id="lang_code" value="<?php echo $data->patient_data->ptLangCode; ?>" />
                              <select name='language' id="language" class="form-control minimal" data-width="100%" title="<?php echo imw_msg('drop_sel'); ?>" data-header="<?php echo imw_msg('drop_sel'); ?>" data-prev-val="<?php echo addslashes($data->patient_data->language); ?>">
                               	<?php																		
                                  foreach ($arrLanguage as $code => $s)
                                  {	
                                    echo "<option value='".$s."'";
                                    if (strtolower($s) == strtolower($data->patient_data->language)){
                                      echo " selected";
                                    }
                                    echo " data-common=\"1\" data-code=\"".$code."\">".ucfirst($s)."</option>\n";
                                  }
                                ?>
                              </select>
                              <div id="otherLanguageBox" class="hidden">
                                <div class="input-group ">
                                  <!-- onKeyUp="pt_languages_typehead(this,'pt_lang_type',event);" -->
                                  <input class="form-control" name="otherLanguage" id="otherLanguage" value="<?php echo stripslashes($other_language_val); ?>" data-prev-val="<?php echo addslashes($other_language_val); ?>"  />
                                  <label class="input-group-btn btn btn-xs btn-primary back_other" id="imgBackLanguage" data-tab-name="language">
                                    <span class="glyphicon glyphicon-arrow-left"></span>
                                  </label>
                                </div>
                              </div>
                            </div>
                            
                            <div class="col-sm-4">
                              <label class="purple-text pointer load_modal" data-modal="ethnicity_modal" ><b>Ethnicity</b></label>
                              <?php
                                  $arrEthnicity = $patient_data_obj->ethnicity_modal(1);
																	$ptEthnicity=explode(",",$data->patient_data->ptEthnicity);
																	$ptEthnicity=array_filter($ptEthnicity);
																	$tmpArr  = array_diff($ptEthnicity,$arrEthnicity);
																	$tmpNewArr=array_splice($arrEthnicity,-2);
																	$arrEthnicity = array_merge($arrEthnicity,$tmpArr,$tmpNewArr);
                              ?>
                              <select name='ethnicity[]' id="ethnicity" title="<?php echo imw_msg('drop_sel'); ?>" data-header="<?php echo imw_msg('drop_sel'); ?>" class="selectpicker form-control minimal" data-width="100%" multiple data-prev-val="<?php echo $data->patient_data->ptEthnicity;?>" data-actions-box='true'>
                                  <?php
                                    foreach ($arrEthnicity as $s) {
                                      echo "<option value='".$s."' data-common = '1'";
                                      if(in_array($s,$ptEthnicity)){
                                        echo " selected";
                                      }
                                      echo ">".ucfirst($s)."</option>\n";
                                    }
                                  ?>
                              </select>
                              <?php
                              $arrAuditTrail[] = array(
                                "Table_Name" => "patient_data",
                                "Data_Base_Field_Name" => "otherEthnicity",
                                "Filed_Label" => "otherEthnicity",
                                "Data_Base_Field_Type" => fun_get_field_type($patientDataFields, "otherEthnicity"),
                                "Old_Value" => addcslashes(addslashes($data->patient_data->ptOtherEthnicity), "\0..\37!@\177..\377")
                              );
                              ?>
                            </div>

                            <div class="col-sm-4">
                            <label class="purple-text pointer load_modal" data-modal="interpreter_modal"><b>Type of Interpreter</b></label>
                            <?php
                                  $arrInterpreter = $patient_data_obj->interpreter_modal(1);
														?>
                              <select name='interpreter_type' id="interpreter_type" class="form-control minimal" data-width="100%" title="<?php echo imw_msg('drop_sel'); ?>" data-header="<?php echo imw_msg('drop_sel'); ?>" data-prev-val="<?php echo($data->patient_data->ptInterpreter_type); ?>">
                                <option value="" selected>&nbsp;</option>
                                <option value="Sign Language" <?php echo (($data->patient_data->ptInterpreter_type =='Sign Language') ? "selected" : "") ?>>Sign Language</option>
                                <option value="Oral" <?php echo (($data->patient_data->ptInterpreter_type =='Oral') ? "selected" : "") ?>>Oral</option>
                                <option value="Qued Speech" <?php echo (($data->patient_data->ptInterpreter_type =='Qued Speech') ? "selected" : "") ?>>Qued Speech</option>
                                <option value="Tactile" <?php echo (($data->patient_data->ptInterpreter_type =='Tactile') ? "selected" : "") ?>>Tactile</option>
                                <option value="Accompanying Spouse" <?php echo (($data->patient_data->ptInterpreter_type =='Accompanying Spouse') ? "selected" : "") ?>>Accompanying Spouse</option>
                                <option value="Accompanying Child" <?php echo (($data->patient_data->ptInterpreter_type =='Accompanying Child') ? "selected" : "") ?>>Accompanying Child</option>
                              </select>
                            </div>
                            <div class="col-sm-4">
                              <label>Interpreter</label>
                              <input name="interpretter" id="interpretter" type="text" class="form-control" value="<?php echo ucfirst(stripslashes($data->patient_data->ptInterpretter));?>" data-prev-val="<?php echo($data->patient_data->ptInterpretter); ?>" />
                            </div>
                            <div class="col-sm-4">
                              <label for="occupation">Occupation</label>
                              <input name="occupation" id="occupation" type="text" class="form-control" value="<?php echo stripslashes($data->patient_data->ptOccupation); ?>" data-prev-val="<?php echo addslashes($data->patient_data->ptOccupation); ?>" />
                            </div>
                      
                            
                            
                            <div class="col-sm-6">
                              <label for="ename">Employer</label>
                              <input name="ename" id="ename" type="text" class="form-control" value="<?php echo stripslashes($data->patient_data->edPtName); ?>" data-prev-val="<?php echo addslashes($data->patient_data->edPtName); ?>" />
                            </div>
                            
                                                        
                            <div class="col-sm-6">
                              <label for="estreet">Employer Address</label>
                              <input name="estreet" id="estreet" type="text" class="form-control" value="<?php echo stripslashes($data->patient_data->edPtStreet); ?>" data-prev-val="<?php echo addslashes($data->patient_data->edPtStreet); ?>" />
                            </div>
                            
                            <div class="col-sm-4">
                              <label for="ecode"><?php getZipPostalLabel(); ?></label>
                              <div class="row">
                                <div class="col-xs-<?php echo (inter_zip_ext() ? '7':'12'); ?>">
                                  <input  name="epostal_code" maxlength="<?php echo inter_zip_length();?>" type="text" class="form-control" id="ecode" onBlur="zip_vs_state(this.value,'occupation',this);" value="<?php echo stripslashes($data->patient_data->edPtPostalCode); ?>" data-prev-val="<?php echo addslashes($data->patient_data->edPtPostalCode); ?>" />
                                </div>
                                <?php if(inter_zip_ext()){?>
                                <div class="col-xs-5">
                                  <input  name="ezip_ext" maxlength="4" type="text" class="form-control" id="ezip_ext" value="<?php echo stripslashes($data->patient_data->edPtzip_ext); ?>" data-prev-val="<?php echo addslashes($data->patient_data->edPtzip_ext); ?>" />
                                </div>
                                <?php }?>	
                              </div>
                            </div>
                                
                            <div class="col-sm-4">
                              <label for="ecity">City</label>
                              <input name="ecity" type="text" class="form-control" id="ecity"  value="<?php echo stripslashes($data->patient_data->edPtCity); ?>" data-prev-val="<?php echo addslashes($data->patient_data->edPtCity); ?>" />
                            </div>
                            
                            <div class="col-sm-4">
                              <label>State</label>
                              <input name="estate" type="text" class="form-control" id="estate"  value="<?php echo stripslashes($data->patient_data->edPtState); ?>" data-prev-val="<?php echo addslashes($data->patient_data->edPtState); ?>" maxlength="<?php echo inter_state_length();?>" />
                            </div>
                          </div>
                        
                        </div>
                      </div>
                    </div>
                  
                  </div>
                      
                  <div class="panel panel-default">
                    <div class="panel-heading" role="tab" id="headingAD">
                      <h4 class="panel-title">
                        <a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion-right" href="#collapseAD" aria-expanded="false" aria-controls="collapseAD">Restrict Access &amp; Advance Directive</a>
                      </h4>
                    </div>
                    <div id="collapseAD" class="panel-collapse collapse pt-box" role="tabpanel" aria-labelledby="headingAD">
                      <div class="panel-body grid-box" tabindex="0">
                        <div class="pdlr15">
                          <div class="row mb10">
                          <div class="col-sm-6">
                           	<label for="providersToRestrictDemographics">Select&nbsp;Provider(s)</label>
                            <?php	
                              $vrst_provider_arr = get_array_records('users','superuser','no', 'id,fname,lname,mname', ' AND delete_status = 0', 'lname');
                              $str_ids = "";
                            ?>
                            <select name='providersToRestrictDemographics[]' id="providersToRestrictDemographics" class="selectpicker" data-width="100%" multiple="multiple" data-actions-box='true' title="<?php echo imw_msg('drop_sel'); ?>" data-header="<?php echo imw_msg('drop_sel'); ?>" >
                            <?php 
                                $restrictSelectIpad = "";
                                if($blClientBrowserIsIpad == true){
                                  $restrictSelectIpad = "selected";
                                }
                                $restricted_provider = $data->patient_data->restrict_providers;
                                if(is_array($vrst_provider_arr) && count($vrst_provider_arr) > 0 )
                                {																					
                                  foreach($vrst_provider_arr as $vrst_provider)
                                  {
                                    $phyName_drop = $vrst_provider['fname'];
                                    if($vrst_provider['fname'] != '' && $vrst_provider['lname'] != ''){
                                      $phyName_drop = $vrst_provider['lname'].', '.$vrst_provider['fname'];
                                    }
                                    else if($vrs['fname'] == '' && $vrst_provider['lname'] != ''){
                                      $phyName_drop = $vrst_provider['lname'];
                                    }
                                    $sele="";
                                    if(is_array($restricted_provider)){
                                      $sele=(in_array($vrst_provider['id'],$restricted_provider)) ? "selected" :"";
                                    }
                                    $phyName_drop .= $vrst_provider['mname'];
                                    echo "<option value=\"".$vrst_provider['id']."\" ".$restrictSelectIpad." ".$sele.">".trim(ucwords($phyName_drop))."</option>";	
                                  }
                                }
                              ?>
                            </select>
                          </div>
                          
                          <div class="col-sm-6">
                            <label>Directive</label>
                            <?php
                              $arr = get_extract_record(constant("IMEDIC_SCAN_DB").'.scans','patient_id',$patient_id,'scan_id'," AND image_form = 'ptInfoAdvancedDirective'");
                              $scan_id = $arr["scan_id"];
                            ?>
                            <div class="row">
                              <div class="col-xs-<?=($scan_id > 0 ? '7' : '10')?>">
                                
                                <input type="hidden" name="hidd_prev_ado_option" id="hidd_prev_ado_option" value="<?php echo $data->patient_data->ptAdoOption;?>">
                                
                                <select class="form-control minimal" title="Advance Directives" data-width="80%" data-header"Advance Directives" name="ado_option" id="ado_option">
                                  <option <?php echo($data->patient_data->ptAdoOption == "NA" ? "selected" : ""); ?> value="NA">NA</option>
                                  <option <?php echo($data->patient_data->ptAdoOption == "No" ? "selected" : ""); ?> value="No">No</option>
                                  <option <?php echo($data->patient_data->ptAdoOption == "Living Will" ? "selected" : ""); ?> value="Living Will">Living Will</option>
                                  <option <?php echo($data->patient_data->ptAdoOption == "Power of Attorney" ? "selected" : ""); ?> value="Power of Attorney">Power of Attorney</option>
                                  <option <?php echo($data->patient_data->ptAdoOption == "Other" ? "selected" : ""); ?> value="Other">Other</option>
                                </select>
                        
                                <div id="ado_other_box" class="hidden">
                                  <div class="input-group " >
                                    <input type="text" class="form-control" id="ado_other_txt" name="ado_other_txt" value="<?php echo $data->patient_data->ptDescAdoOtherTxt; ?>" data-prev-val="<?php echo addslashes($data->patient_data->ptDescAdoOtherTxt); ?>" />
                                    <label class="input-group-btn btn btn-xs btn-primary back_other" id="imgBackAdvancedDirective" data-tab-name="ado_option">
                                      <span class="glyphicon glyphicon-arrow-left"></span>
                                    </label>
                                  </div>
                                </div>
                                
                              </div>
                               
                             	<div class="col-xs-2">    
                                <span class="glyphicon scan-icon mb0 pointer pull-right" title="Scan" onClick="ado_scan_fun('scan', 'ptInfoAdvancedDirective','<?php echo $scan_id ;?>')">&nbsp;</span>
                                
                              </div>
                              <?php	if ($scan_id > 0) {	?>
                              <div class="col-xs-3">
                              <?php	
                                $getImageRow = get_extract_record(constant("IMEDIC_SCAN_DB").'.scans','scan_id',$scan_id,'file_path,image_form');
                                if(!empty($getImageRow["file_path"]))
                                {
                                  $pth = $getImageRow["file_path"];
                                  if(file_exists(data_path() . $pth))
                                  {											
                                    //echo show_thumb_image(data_path(1) . $pth,50,35);
                              ?>
                                  
                                  <a title="View Document" class="btn btn-xs btn-success" onClick="showpdf('<?php echo $scan_id; ?>','','<?php echo $getImageRow["image_form"];?>')"><i class="glyphicon glyphicon-level-up"></i> </a>
                              <?php
                                  }
                                }
                              ?>
                              </div>
                              <?php } ?>
                            </div>
                            
                          </div>
                        
                        </div>
                        
                        </div>
                        </div>
                    </div>
                  </div>
                      
                  <div class="panel panel-default">
                  
                    <?php 
                      $resp_data	=	get_extract_record('resp_party','patient_id',$patient_id, 'id, resp_username', "AND resp_username <> '' AND resp_password <> ''");
                      $checkviewportal = ($data->patient_data->view_portal=="1") ? "checked" : '';
                      $checkupdateportal=	($data->patient_data->update_portal=="1") ? "checked" : '';
                      $checkLocked = ($data->patient_data->locked=="1" ) ? "checked" : '';
                      $checkUnLocked = ($data->patient_data->locked=="0" ) ? "checked" : '';
                      $temp_key_readonly = ($data->patient_data->ptUserName == "" && $resp_data["resp_username"] == "") ? '' : 'readonly';
                      $tempKeySize='6';
                      $tmpKeyLabel = ($data->patient_data->temp_key_expire == "yes") ? "Re-Gen. Activation Key" : "Generate Activation Key";
                    ?>
                    
                    <div class="panel-heading" role="tab" id="headingPortal">
                      <h4 class="panel-title">
                        <a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion-right" href="#collapsePortal" aria-expanded="false" aria-controls="collapsePortal">Patient Portal</a> 
                        <div class="ptport">
                          <div class="radio radio-inline">
                            <input type="radio" name="lockPatient" id="idlockPatient" value="1" <?php echo($checkLocked);?> data-prev-val="<?php echo addslashes($checkLocked); ?>" />
                            <label for="idlockPatient">Lock</label>
                          </div>
                          <div class="radio radio-inline">
                            <input type="radio" name="lockPatient" id="idunlockPatient" <?php echo($checkUnLocked);?> value="0" data-prev-val="<?php echo addslashes($checkUnLocked); ?>" />
                            <label for="idunlockPatient">Un-Lock</label>
                          </div>
                        </div>
                      </h4>
                    </div>
                    
                    <div id="collapsePortal" class="panel-collapse collapse pt-box" role="tabpanel" aria-labelledby="headingPortal">
                      <div class="panel-body grid-box" tabindex="0">
                      	<div class="pdlr15">
                        	<div class="col-xs-12">
                          	<input name="usernm" id="usernm" type="text" class="form-control hidden"  value="<?php echo $data->patient_data->ptUserName; ?>" data-prev-val="<?php echo addslashes($data->patient_data->ptUserName); ?>" >
                            <b>Login-Id :</b>&nbsp;<?php echo $data->patient_data->ptUserName; ?>
                            <div class="pull-right">
                            	<span class="text_purple pointer show_log" data-action="show_patient_access_log" data-ptrp="0" title="Patient Access Log">Patient Access Log</span>&nbsp;|&nbsp;
                              <span class="text_purple pointer show_log" data-action="login_history" data-ptrp="0" title="Login History">Login History</span>
                           	</div>
                        	</div>
                       	</div>
                        
                        <button class="btn btn-primary width-100" type="button">Patient Authorized Representative</button>
                        
                        <div class="row pdlr15">
                            <?php if(is_array($resp_data) && count($resp_data) > 0 ) { ?>
                            <div class="col-sm-4">
                              <label><b>Login-Id :</b> <b><u><?php echo $resp_data["resp_username"]; ?></u></b></label>
                            </div>
                           
                            <div class="col-sm-4 ">
                              <label data-action="show_patient_access_log" data-ptrp="1" class="show_log" title="Patient Authorised Representative Access Log">
                                Patient&nbsp;Access&nbsp;Log
                              </label>
                            </div>
                            
                            <div class="col-sm-4">
                              <label data-action="login_history" data-ptrp="1" class="show_log" title="Patient Authorised Representative Login History">Login&nbsp;History&nbsp;</label>
                            </div>
                            <?php } else { ?>
                            <div class="col-sm-12">Patient Representative Never Logged In.</div>
                            <?php } ?>
                         </div>
                       	 
                        <div class="pdlr15 mt5 mb5">
                          <div class="row">
                            <div class="col-sm-10 form-inline">
                              <a class="activation-key text_purple" href="javascript:void(0)" data-temp-key-chk="<?php echo $data->patient_data->temp_key_chk_val; ?>" data-resp-username="<?php echo $resp_data['resp_username']; ?>" data-temp-key-size="6" data-action="temp_key_generate" data-regen-key="0" data-tmp-user-pass="" ><?php echo $tmpKeyLabel;?></a>  
                              
                              <input type="text" name="temp_key" id="temp_key" <?php echo $temp_key_readonly;?> value="<?php echo trim($data->patient_data->temp_key); ?>" class="form-control" />
                            </div>
                            <div class="col-sm-2">
                              <?php $checked = ($data->patient_data->temp_key_chk_val == 1) ? 'checked' : ''; ?>
                              <div class="checkbox">
                                <input type="checkbox" id="temp_key_chk_val" name="temp_key_chk_val" value="1" <?php echo $checked;?>  data-prev-val="<?php echo addslashes($data->patient_data->temp_key_chk_val);?>" />
                                <label for="temp_key_chk_val">Given</label>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                      
                  <div class="panel panel-default" id="ReleaseInformation">
                    
                    <div class="panel-heading" role="tab" id="headingRelease">
                      <h4 class="panel-title">
                        <a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion-right" href="#collapseRelease" aria-expanded="false" aria-controls="collapseRelease">Release Information</a>
                      </h4>
                    </div>
                    
                    <div id="collapseRelease" class="panel-collapse collapse pt-box" role="tabpanel" aria-labelledby="headingRelease">
                      <div class="panel-body grid-box" tabindex="0">
                        <!-- Div container for dropdown option in release information -->
                        <div id="rel_select_picker" style="height:0px;"></div>	
                        
                        <div class="col-sm-12">
                          <div class="row" id="rel_table_parent">
                            <table class="table table-bordered table-hover table-striped scroll release-table mb5">
                              <thead >
                                <tr>
                                  <th class="col-sm-4 text-center">Name</th>
                                  <th class="col-sm-4 text-center">Phone</th>
                                  <th class="col-sm-4 text-center">Relationship</th>
                                </tr>
                              </thead>
                                
                              <tbody>
                              <?php 
                                $arrHippaRelation = get_relationship_array('hipaa_relation');
                                for($loop = 1; $loop < 5; $loop++)
                                {
                                  $name_var = $phone_var = $rel_var = $other_rel_var = '';
                                  $f_name_var = 'relInfoName'.$loop;
                                  $f_phone_var = 'relInfoPhone'.$loop;
                                  $f_rel_var = 'relInfoReletion'.$loop;
                                  $f_other_rel_var = 'otherRelInfoReletion'.$loop;
                                  
                                  $name_var = 'ptRelInfoName'.$loop;
                                  $phone_var = 'ptRelInfoPhone'.$loop;
                                  $rel_var = 'ptRelInfoReletion'.$loop;
                                  $other_rel_var = 'ptOtherRelInfoReletion'.$loop;
                                  
                                  $$f_name_var =	 $data->patient_data->$name_var;
                                  $$f_phone_var =	 $data->patient_data->$phone_var;
                                  $$f_rel_var =	 $data->patient_data->$rel_var;
                                  $$f_other_rel_var =	 $data->patient_data->$other_rel_var;
                                  
                              ?>
                                  <tr>
                                    <td data-label="Name"><input class="form-control" name="<?php echo $f_name_var; ?>" id="<?php echo $f_name_var; ?>" value="<?php echo $$f_name_var; ?>" data-prev-val="<?php echo addslashes($$f_name_var); ?>" /></td>
                                    <td data-label="Phone"><input class="form-control" name="<?php echo $f_phone_var; ?>" id="<?php echo $f_phone_var; ?>" value="<?php echo core_phone_format($$f_phone_var); ?>" data-prev-val="<?php echo addslashes(core_phone_format($$f_phone_var)); ?>" /></td>
                                    <td data-label="Relationship" class="text-nowrap">
                                      <select name='<?php echo $f_rel_var; ?>' id="<?php echo $f_rel_var; ?>" class="form-control minimal" data-container="#rel_select_picker" title="<?php echo imw_msg('drop_sel'); ?>" data-header="<?php echo imw_msg('drop_sel'); ?>" data-width="100%" data-size="10" data-tab-num="<?php echo $loop; ?>" data-dropdown-align-right="true" >
                                      <?php
                                        foreach ($arrHippaRelation as $s) {	
                                          $value_print = ucfirst(strtolower($s));
                                          echo "<option value='".$s."'";
                                          if ($s == $data->patient_data->$rel_var || empty($s)){
                                            echo " selected";
                                          }
                                          echo ">".(empty($value_print) ? '' : $value_print)."</option>\n";
                                        }																				
                                      ?>
                                      </select>
                                      <div id="otherRelInfoBox<?php echo $loop; ?>" class="hidden">
                                        <div class="input-group" >
                                          <input class="form-control" name="<?php echo $f_other_rel_var; ?>" id="<?php echo $f_other_rel_var; ?>" value="<?php echo $$f_other_rel_var; ?>" data-prev-val="<?php echo addslashes($$f_other_rel_var); ?>" />
                                          <label class="input-group-btn btn btn-xs btn-primary back_other" id="imgBackRelInfoReletion<?php echo $loop; ?>" data-tab-name="relInfoReletion" data-tab-num="<?php echo $loop; ?>" style="color:white;">
                                            <i class="glyphicon glyphicon-arrow-left"></i>
                                          </label>
                                        </div>
                                      </div> 	   
                                    </td>
                                  </tr>
                              <?php
                                }
								$arrAuditTrail[] = array(
									"Table_Name" => "patient_data",
									"Data_Base_Field_Name" => "otherRelInfoReletion3",
									"Filed_Label" => "otherRelInfoReletion3",
									"Data_Base_Field_Type" => fun_get_field_type($patientDataFields, "otherRelInfoReletion3"),
									"Old_Value" => addcslashes(addslashes($data->patient_data->ptOtherRelInfoReletion3), "\0..\37!@\177..\377")
								);
                              ?>
                              </tbody>
                            </table>
                          </div>
                        </div>
                      </div>
                    </div>
                    
                  </div>
                
                </div>
              
              </div>
            </div>
          
          </div>
          
        </div>
        

      	<div class="clearfix"></div>
        
        <!-- Start Responsible Party/Guarantor AND Family Information Grids -->
        <div class="container-fluid mt5">
        	
         	 	
        	<!-- Start Responsible Party/Guarantor Grid -->
         	<div class="col-xs-12 col-md-7 border " id="resp_container" >
          					
          	<div class="row">	
            	<div class="head">Responsible Party/Guarantor
              <?php if($data->patient_data->rpPtID <> '' && trim($data->patient_data->rpPtFname) <> "" && trim($data->patient_data->rpPtLname) <> ""){?>
                <span title="Delete Responsible Party" id="btn_del_resp_party" class="pull-right pointer" data-action="delete_resp_party" data-resp-id="<?php echo $data->patient_data->rpPtID; ?>"><i class="glyphicon glyphicon-remove"></i></span>
              <?php }?>
              </div>
          	</div>
            <?php
							$temp_var = 'no';
							if(trim($data->patient_data->rpPtFname) <> "" && trim($data->patient_data->rpPtLname) <> "")
							{ $temp_var = 'yes'; }
						?>
       			<input type="hidden" name="hid_resp_party_sel_our_sys" id="hid_resp_party_sel_our_sys" value="<?php echo $temp_var; ?>"/>
            <div class="row mb5 pt-box">
            <div class="grid-box" tabindex="0">
            
            <div class="col-xs-12 "> 
            	<!-- Title -->
              <div class="col-xs-3"	>
              	<label>Title</label>
                <br>
                <select name="title1" id="title1" class="form-control minimal" data-width="100%" data-prev-val="<?php echo addslashes($data->patient_data->rpPtTitle); ?>" title="<?php echo imw_msg('drop_sel'); ?>" data-header="<?php echo imw_msg('drop_sel'); ?>">
                <option value="" <?php if($data->patient_data->rpPtTitle == ""){echo("selected");}?>> </option>
                  <option value="Mr." <?php if($data->patient_data->rpPtTitle == "Mr."){echo("selected");}?>>Mr.</option>
                  <option value="Mrs." <?php if(($data->patient_data->status == "married" && $data->patient_data->rpPtSex == "Female")||$data->patient_data->rpPtTitle == "Mrs."){echo("selected");}?>>Mrs.</option>
                  <option value="Ms." <?php if($data->patient_data->rpPtTitle == "Ms."){echo("selected");}?>>Ms.</option>
                  <option value="Miss" <?php if($data->patient_data->rpPtTitle == "Miss"){echo("selected");}?>>Miss</option>
                  <option value="Master" <?php if($data->patient_data->rpPtTitle == "Master"){echo("selected");}?>>Master</option>
                  <option value="Prof." <?php if($data->patient_data->rpPtTitle == "Prof."){echo("selected");}?>>Prof.</option>
                  <option value="Dr." <?php if($data->patient_data->rpPtTitle == "Dr."){echo("selected");}?>>Dr.</option>
              	</select>
            	</div>
                  
              <!-- First Name -->
              <div class="col-xs-3  "	>
              	<label>First Name</label>
            		<br>
            		<input  type="text" id="fname1" name="fname1" value="<?php echo stripslashes($data->patient_data->rpPtFname);?>" class="form-control" data-prev-val="<?php echo addslashes($data->patient_data->rpPtFname); ?>" />
             	</div>
                  
             	<!-- Middle Name -->
              <div class="col-xs-3 ">
              	<label>Middle Name</label>
                <br>
                <input  type="text" id="mname1"  name="mname1" value="<?php echo stripslashes($data->patient_data->rpPtMname);?>" class="form-control" data-prev-val="<?php echo addslashes($data->patient_data->rpPtMname); ?>" />
            	</div>
              
              <!-- Last Name -->
              <div class="col-xs-3  "	>
              	<label>Last Name</label>
                <br>
                <input  type="text" id="lname1" class="form-control" name="lname1" value="<?php echo stripslashes($data->patient_data->rpPtLname);?>"data-prev-val="<?php echo addslashes($data->patient_data->rpPtLname); ?>" data-action="search_patient" data-grid="0"  />
             	</div>
            </div>
            
        		<div class="col-xs-12 ">
              <div class="row">
                <div class="col-xs-9 "> 
                  <!-- Suffix-->
                  <div class="col-xs-4  "	>
                    <label>Suffix</label>
                    <br>
                    <input  type="text" id="suffix1" name="suffix1" class="form-control" value="<?php echo stripslashes($data->patient_data->rpPtSuffix);?>" data-prev-val="<?php echo addslashes($data->patient_data->rpPtSuffix); ?>" />
                  </div>
            
                  <!-- RelationShip -->
                  <div class="col-xs-4 "	>
                    <label>RelationShip</label>
                    <br>
                    <select name='relation1' id="relation1" class="form-control minimal" title="<?php echo imw_msg('drop_sel'); ?>" data-width="100%" data-header="<?php echo imw_msg('drop_sel'); ?>">
                      <?php
                        $relats = get_relationship_array('emergency_relation');
                        foreach ($relats as $s) {
                          if($s == 'Doughter'){
                            echo "<option value='".$s."'";
                            if ($s == $data->patient_data->rpPtRelation)
                              echo " selected";
                            echo ">Daughter</option>\n";

                          }else{
                            echo "<option value='".$s."'";
                            if (strtolower($s) == strtolower($data->patient_data->rpPtRelation) || empty($s))
                              echo " selected";
                            echo ">".(empty($s) ? ' ' :ucfirst($s))."</option>\n";
                          }
                        }
                      ?>
                    </select>
                    
                    <div id="relation1_oth" class="hidden">
                      <div class="input-group ">
                        <input type="text" value="<?php echo $data->patient_data->rpPtOther1?>" id="oth" class="form-control" name="other1" data-prev-val="<?php echo addslashes($data->patient_data->rpPtOther1); ?>" />
                        <label class="input-group-btn btn btn-xs btn-primary back_other" data-tab-name="relation1">
                          <span class="glyphicon glyphicon-arrow-left"></span>
                        </label>
                      </div>
                    </div>
                          
                  </div>
                  
                  <!-- Marital Status -->
                  <div class="col-xs-4  "	>
                    <label>Marital Status</label>
                    <br>
                    <select name="status1" id="status1" class="form-control minimal" data-width="100%" data-prev-val="<?php echo $data->patient_data->rpPtMaritalStatus; ?>" title="<?php echo imw_msg('drop_sel'); ?>" data-header="<?php echo imw_msg('drop_sel'); ?>">
                      <?php
                        foreach ($defaults['marital_status'] as $s) {
                          $s = ucfirst($s);
                          echo "<option value='".ucwords($s)."'";
                          if ($s == $data->patient_data->rpPtMaritalStatus || empty($s))
                            echo " selected";
                          echo ">".(empty($s) ? ' ' : ucwords($s))."</option>";
                        }
                      ?>
                    </select>
                  </div>

                  <div class="clearfix"></div>

                  <!-- DOB -->
                  <div class="col-xs-2  "	>
                    <label>Dob&nbsp;<small>(<?php echo inter_date_format(); ?>)</small></label>
                    <br>
                    <input type="hidden" name="from_date_byram1" id="from_date_byram1" value="<?php echo(get_date_format(date("Y-m-d")));?>">
                    <?php
                      $create_date="";
                      if($data->patient_data->rpPtDOB <> "" && $data->patient_data->rpPtDOB <> "0000-00-00" && $data->patient_data->rpPtDOB <> "--"){
                        $tmp_date = $data->patient_data->rpPtDOB;
                        list($year, $month, $day) = explode('-',$tmp_date);
                        $create_date = $month."-".$day."-".$year;
                        $currentdate_ram =date("m-d-Y"); //getdate();
                        $dformat="-";
                        $patient_age=round(dateDiff($dformat,date("m-d-Y", time()), $create_date)/365, 0);
                        if(date("m")<$month){
                          $patient_age=$patient_age-1;
                        }
                        //Get Age
                        $patient_age_text = "";
                        $patient_age = get_age($data->patient_data->rpPtDOB);
                        $arrptAg = explode(" ",$patient_age);											
                        $patient_age = $arrptAg[0];
                        $patient_age_text = ucfirst($arrptAg[1]);
                      }else{
                        $create_date="";
                      }
                    ?>
                    <div class="input-group">
                      <input name="dob1" id="dob1" type="text" class="form-control datepicker" title="<?php echo inter_date_format();?>" value='<?php echo get_date_format($create_date,inter_date_format()); ?>' data-prev-val="<?php echo get_date_format($create_date,inter_date_format()); ?>" maxlength="10" />
                      <label class="input-group-addon btn" for="dob1">
                        <span class="glyphicon glyphicon-calendar"></span>
                      </label>
                    </div>
                  </div>
                  
                  <!-- Sex-->
                  <div class="col-xs-2 "	>
                    <label>Sex</label>
                    <br>
                    <select name="sex1" id="sex1" class="form-control minimal" title="<?php echo imw_msg('drop_sel'); ?>" data-width="100%" data-header="<?php echo imw_msg('drop_sel'); ?>" data-prev-val="<?php echo $data->patient_data->rpPtSex; ?>" >
                      <option value="" <?php if ($data->patient_data->rpPtSex == '') {echo "selected";};?>> </option>
                      <option value="Male" <?php if ($data->patient_data->rpPtSex == "Male") {echo "selected";};?>>Male</option>
                      <option value="Female" <?php if ($data->patient_data->rpPtSex == "Female") {echo "selected";};?>>Female</option>
                    </select>
                  </div>
                  
                  <!-- Social Security -->
                  <div class="col-xs-4 "	>
                    <label>Social Security#</label>
                    <br>
                    <input name="ss1" id="ss1" type="text" class="form-control" value="<?php echo stripslashes($data->patient_data->rpPtSS); ?>" data-prev-val="<?php echo addslashes($data->patient_data->rpPtSS);?>" maxlength="<?php echo inter_ssn_length();?>" size="<?php echo inter_ssn_length();?>">
                  </div>
                  
                  <!-- Relaese HIPAA Info -->
                  <div class="col-xs-4  form-inline"><br>
                      <?php $checked = ($data->patient_data->hippaRelSta == 1) ? 'checked' : ''; ?>
                      <div class="checkbox"><input type="checkbox" id="chkHippaRelResp" name="chkHippaRelResp" <?php echo $checked; ?> value="1" data-prev-val="<?php echo $checked; ?>"><label for="chkHippaRelResp"><span class="text-red">Release HIPAA Info</span></label></div>
                  </div>

                </div>

                <div class="col-xs-3 ">
                  <div class="row">
                      <!-- Driving License -->
                      <div class="col-xs-6 "	>
                        <label>Driving&nbsp;License#</label>
                        <br>
                        <input name="dlicence1" id="dlicence1" type="text" class="form-control" value="<?php echo stripslashes($data->patient_data->rpPtLicence); ?>" data-prev-val="<?php echo addslashes($data->patient_data->rpPtLicence); ?>" />
                        <span class="clearfix"></span>
                        <button class="btn btn-primary f-bold width-100 mt10" type="button" id="btPatRespScanDo" onClick="<?php if(core_check_privilege(array("priv_vo_pt_info")) == true){ ?> view_only_acc_call(1); return false; <?php }else{ ?>scan_licence(<?php echo $pid;?>,'rp');<?php } ?>">License</button>
                        <input name="resp_license_image" id="resp_license_image" type="hidden" class="form-control" value="<?php echo stripslashes($data->patient_data->rpPtLicenceImage); ?>" />
                      </div>
                          
                      <!-- Upload License -->
                      <div class="col-xs-6 ">
                        <div class="previmg" id="respLicDiv">
                          <?php
                              $respThumbImageSrc = '';
                              $resp_license_img = "";
															if( $data->patient_data->rp_license_image){
                                $resp_license_img = $data->patient_data->rp_license_image;
																$tmpArr = explode("/",$data->patient_data->rp_license_image);
																$lKey = count($tmpArr)-1;
																$tmpArr[$lKey] = 'thumbnail/'.end($tmpArr);
																$respThumbImageSrc = implode('/',$tmpArr);
																$respThumbImageSrc = file_exists($respThumbImageSrc) ? $respThumbImageSrc : $data->patient_data->rp_license_image;
																echo show_thumb_image($respThumbImageSrc,80,60); ?>
                              	<span class="layer" data-toggle='modal' data-target='#resp_party_license'></span>
                          <?php } ?>	
                        </div>
                      </div>
                    </div>
                </div>

              </div>
            </div>
            
            <div class="col-xs-12 "> 
                  <!-- Street1 -->
                  <div class="col-xs-5 "	>
                  	<label>Street 1</label>
                    <br>
                    <input name="street1" id="street1" type="text" class="form-control" value="<?php echo stripslashes($data->patient_data->rpPtAdd); ?>" data-prev-val="<?php echo addslashes($data->patient_data->rpPtAdd); ?>" />
                 	</div>
                  
                  <!-- Street2 -->
                  <div class="col-xs-4 "	>
            				<label>Street 2</label>
                    <br>
                    <input name="street_emp" type="text" class="form-control" id="street_emp" value="<?php echo stripslashes($data->patient_data->rpPtAddress2); ?>" data-prev-val="<?php echo addslashes($data->patient_data->rpPtAddress2); ?>" />
                 	</div>
                  
                  <!-- Zip Code -->
                  <div class="col-xs-3 "	>
                    <label><?php getZipPostalLabel(); ?></label>
                    <div class="row">
                      <div class="col-xs-<?php echo (inter_zip_ext() ? '7' : '12');?>" >
                        <input name="postal_code1" type="text" class="form-control" id="rcode" onBlur="zip_vs_state(this.value,'resp_party',this);"  value="<?php echo stripslashes($data->patient_data->rpPtZip); ?>" data-prev-val="<?php echo addslashes($data->patient_data->rpPtZip); ?>" maxlength="<?php echo inter_zip_length();?>" size="<?php echo inter_zip_length();?>">
                      </div>
											<?php if(inter_zip_ext()) { ?>
                      <div class="col-xs-5 ">
                        <input name="rzip_ext" type="text" class="form-control" id="rzip_ext" value="<?php echo stripslashes($data->patient_data->rpzip_ext); ?>" data-prev-val="<?php echo addslashes($data->patient_data->rpzip_ext); ?>" maxlength="4" />
                      </div>
                      <?php } ?>
                    </div>
                	</div>
            		</div>
                
            <div class="col-xs-12 "> 
                  <!-- City -->
                  <div class="col-xs-3 ">
                    <label>City</label>
                    <br>
                    <input name="city1"  type="text" class="form-control" id="rcity" value="<?php echo stripslashes($data->patient_data->rpPtCity); ?>" data-prev-val="<?php echo addslashes($data->patient_data->rpPtCity); ?>" />
              		</div>
                  
                  <!-- State -->
                  <div class="col-xs-2 ">
                  	<label><?php echo ucwords(inter_state_label());?></label>
                  	<br>
                    <input name="state2" type="text" class="form-control" id="rstate" maxlength="<?php if(inter_state_val() == "abb")echo '2';?>" value="<?php echo stripslashes($data->patient_data->rpPtState); ?>" data-prev-val="<?php echo addslashes($data->patient_data->rpPtState); ?>" />
                    <input name="country_code1" id="country_code1" type="hidden" class="form-control"  value="USA" disabled="disabled">
                	</div>
                  
                  <!-- Email ID -->
                  <div class="col-xs-7 "	>
                  	<label>Email-Id</label>
                    <br>
                    <input name='email1' id="email1" type='text' class="form-control" value='<?php echo $data->patient_data->rpPtEmail; ?>' data-prev-val="<?php echo addslashes($data->patient_data->rpPtEmail); ?>" />
                	</div>
                  
               	</div>
                
            <div class="col-xs-12 "> 
                	<!-- Home Phone -->
                  <div class="col-xs-4 "	>
                  	<label>Home Phone <?php getHashOrNo(); ?></label>
                  	<br>
                  	<input name='phone_home1' id="phone_home1" type='text' class="form-control" value='<?php echo stripslashes(core_phone_format($data->patient_data->rpPtHomePh)); ?>' data-prev-val="<?php echo addslashes($data->patient_data->rpPtHomePh); ?>" maxlength="<?php echo inter_phone_length();?>">
                 	</div>
                  
                  <!-- Work Phone -->
                  <div class="col-xs-4 "	>
                  	<label>Work Phone <?php getHashOrNo(); ?></label>
                    <br>
                    <input name='phone_biz1' id="phone_biz1" type='text' class="form-control" value='<?php echo stripslashes(core_phone_format($data->patient_data->rpPtWorkPh)); ?>' data-prev-val="<?php echo addslashes($data->patient_data->rpPtWorkPh); ?>" maxlength="<?php echo inter_phone_length();?>"/>
                	</div>
                  
                  <!-- Mobile Phone -->
                  <div class="col-xs-4 "	>
                  	<label>Mobile Phone <?php getHashOrNo(); ?></label>
                    <br>
                    <input name='phone_cell1' id="phone_cell1" type='text' class="form-control" value='<?php echo stripslashes(core_phone_format($data->patient_data->rpPtMobilePh)) ?>' data-prev-val="<?php echo addslashes($data->patient_data->rpPtMobilePh); ?>" maxlength="<?php echo inter_phone_length();?>"/>
                 	</div>
                  
                </div>
				
				<?php if(isERPPortalEnabled()) { ?>
					<div class="col-xs-12" style="margin-bottom:10px;"> 
                        <div class="head">Responsible Party Credentials for ERP Portal</div>
						<div class="col-xs-4">
							<label>Username</label>
							<br>
							<input name='erp_resp_username' id="erp_resp_username" type='text' class="form-control" value="<?php echo stripslashes($data->patient_data->erp_resp_username); ?>" data-prev-val="<?php echo addslashes($data->patient_data->erp_resp_username); ?>"/>
							</div>
						<div class="col-xs-4">
							<label>Password</label>
							<br>
							<input name='erp_resp_passwd' id="erp_resp_passwd" type='password' class="form-control" value="<?php echo stripslashes($data->patient_data->erp_resp_imw_password); ?>" />
                            <input name='erp_hidd_passwd' id="erp_hidd_passwd" type='hidden' class="form-control" value="<?php echo stripslashes($data->patient_data->erp_resp_imw_password); ?>" />
                            </div>    
                        <div class="col-xs-4">
							<label>Confirm Password</label>
							<br>
							<input name='erp_resp_cpasswd' id="erp_resp_cpasswd" type='password' class="form-control" value="<?php echo stripslashes($data->patient_data->erp_resp_imw_password); ?>" />
                        </div>	     
					</div>
				<?php } ?>
                
                </div>
          	</div>    
       		</div>	     
      		<!-- End Responsible Party/Guarantor Grid -->
          
          
          <!-- Start Family Information Grid -->          
          <div class="col-xs-12 col-md-5" style="padding-right:0px !important;">
						<div id="familySelectContainer" style="position:absolute;"></div>
          	<div class="col-xs-12 border" >
            <div class="row ">
            	<div class="head">
								Family Information
								<?php
                  $family_qry_res = get_array_records('patient_family_info','patient_id',$patient_id);
									$totalCount	=	count($family_qry_res);
									$index			=	$totalCount > 0 ? $totalCount : 1;
                  $lastIndex	=	$totalCount - 1;
								?>
                <span id="ImageAddRow">
                  <span id="imgAddNewRow<?=$index?>" title="Add More" onClick="add_family_info_row('<?php echo $family_qry_res[$lastIndex]['id'];?>','<?php echo $index; ?>','<?php echo $vocabulary["delete_family_info"];?>','<?php echo inter_state_label();?>');" class="pull-right pointer"><i class="glyphicon glyphicon-plus"></i></span>
                </span>
              </div>
           	</div>
            
            <div class="clearfix"></div>
            
            <div id="patient_family_table">
            	<?php
								$fm_tab_index = 501;
								$loop_cnt = count($family_qry_res) > 0 ? count($family_qry_res) : 1;
								for($i=0,$j=1;$i<$loop_cnt;$i++,$j++)
								{
									if(fmod($j,2) == 0){$altClass = ' alternate';} else {$altClass= '';}
									$marginTopClass = ($j > 1 ) ? 'margin-top-10' : '';
							?>
              <div id="table_family_information_<?php echo $j ;?>" class="family-grid pt-box<?=$altClass?> <?=$marginTopClass?>" >
              	
                <div id="family_info_name_table_<?php echo $j ;?>" class="grid-box" tabindex="0">
                	<div class="col-xs-12 "> 
                  
                  <!-- Relative -->
                  <div class="col-xs-3 " id="RelativeDiv_<?=$j?>"	>
                  	<label>Relative</label>
                    <br>
                    <input type="hidden" id="family_info_primary_key_id<?php echo $j ;?>" name="family_info_primary_key_id<?php echo $j ;?>" value="<?php echo $family_qry_res[$i]['id']; ?>">
                    <input type="hidden" id="family_info_patient_id<?php echo $j ;?>" name="family_info_patient_id<?php echo $j ;?>" value="<?php echo $family_qry_res[$i]['patient_id']; ?>">
                    <?php
											$m = trim($family_qry_res[$i]['patient_relation']);
										?>
                    <select class="form-control minimal" name="family_information_relatives<?php echo $j ;?>" id="family_information_relatives<?php echo $j ;?>" data-prev-val="<?php echo addslashes($m);?>" data-tab-num="<?php echo $j ;?>" data-width="100%" title="<?php echo imw_msg('drop_sel'); ?>" data-header="<?php echo imw_msg('drop_sel'); ?>" data-container="#familySelectContainer">
                			<?php
												$arrFamily = get_relationship_array('social_history');
												foreach($arrFamily as $key=>$s){
													$checked = (empty($s) || $m == $s) ? 'selected="selected"' : '';																
													echo "<option value='".$s."' ".$checked.">".(empty($s) ? ' ' : $s)."</option>";
												}
											?>
                   	</select>
                    <div id="family_rel_other_box_<?php echo $j; ?>" class="hidden">
                      <div class="input-group ">
                        <input type="text" class="form-control" id="family_information_relatives_other_txt<?php echo $j ;?>" name="family_information_relatives_other_txt<?php echo $j ;?>" value="<?php echo $family_qry_res[$i]['name_of_other_relation']; ?>" data-prev-val="<?php echo addslashes($family_qry_res[$i]['name_of_other_relation']); ?>" />
                        <label class="input-group-btn btn btn-xs btn-primary back_other" id="imgBackFamilyInformation<?php echo $j ;?>" data-tab-name="family_information_relatives" data-tab-num="<?php echo $j ;?>">
                          <span class="glyphicon glyphicon-arrow-left"></span>
                        </label>
                      </div>
                    </div>
                 	</div>
                
                	<!-- Title -->
                	<div class="col-xs-3 ">
                  	<label>Title</label>
                    <br>
                    <select name="title_table_family_information<?php echo $j ;?>" id="title_table_family_information<?php echo $j ;?>" class="form-control minimal" data-prev-val="<?php echo $family_qry_res[$i]["title"];?>" data-width="100%" title="<?php echo imw_msg('drop_sel'); ?>" data-header="<?php echo imw_msg('drop_sel'); ?>">
                    	<option value="" <?php echo ($family_qry_res[$i]["title"] == "" ? "selected" : "");?>> </option>
                      <option value="Mr." <?php echo ($family_qry_res[$i]["title"] == "Mr." ? "selected" : "");?>>Mr.</option>
                      <option value="Mrs." <?php echo ($family_qry_res[$i]["title"] == "Mrs." ? "selected" : "");?>>Mrs.</option>
                      <option value="Ms." <?php echo ($family_qry_res[$i]["title"] == "Ms." ? "selected" : "");?>>Ms.</option>
                      <option value="Miss" <?php if($family_qry_res[$i]["title"] == "Miss"){echo("selected");}?>>Miss</option>
                      <option value="Master" <?php if($family_qry_res[$i]["title"] == "Master"){echo("selected");}?>>Master</option>
                      <option value="Prof." <?php if($family_qry_res[$i]["title"] == "Prof."){echo("selected");}?>>Prof.</option>
                      <option value="Dr." <?php echo ($family_qry_res[$i]["title"] == "Dr." ? "selected" : "");?>>Dr.</option>
                    </select>
                 	</div>
                  
                  <!-- Delete Image -->
                  <div class="col-xs-6" id="imgRowTd<?php echo $j;?>">
                  	<?php if($totalCount > 0 ) { ?>
                    <br>
                    <span id="imgDeleteRow<?php echo $j;?>" class="pull-right pointer" title="Delete Family Information" onClick="delete_family_info('<?php echo $family_qry_res[$i]['id'];?>','<?php echo $j;?>','<?php echo $vocabulary["delete_family_info"];?>');"><i class="glyphicon glyphicon-remove"></i></span>
                    <?php } ?>
                	</div>
                  
              		</div>
                 	
                  <div class="col-xs-12 ">
                  	<!-- First Name -->
                    <div class="col-xs-4  "	>
                    	<label>First Name</label>
                      <br>
                      <input type="text" name="fname_table_family_information<?php echo $j ;?>" id="fname_table_family_information<?php echo $j ;?>" class="form-control" value="<?php echo $family_qry_res[$i]["fname"]; ?>" data-prev-val="<?php echo addslashes($family_qry_res[$i]["fname"]); ?>" />
                   	</div>
                    
                    <!-- Middle Name -->
                    <div class="col-xs-4  "	>
                    	<label>Middle Name</label>
                      <br>
                      <input type="text" name="mname_table_family_information<?php echo $j ;?>" id="mname_table_family_information<?php echo $j ;?>" class="form-control" value="<?php echo $family_qry_res[$i]["mname"]; ?>" data-prev-val="<?php echo addslashes($family_qry_res[$i]["mname"]); ?>" />
                   	</div>
                    
                    <!-- Last Name -->
                    <div class="col-xs-4  "	>
                    	<label data-toggle="modal" data-target="#search_patient_result">Last Name</label>
                      <br>
                      <input type="text" name="lname_table_family_information<?php echo $j ;?>" id="lname_table_family_information<?php echo $j ;?>" class="form-control" value="<?php echo $family_qry_res[$i]["lname"]; ?>" data-prev-val="<?php echo addslashes($family_qry_res[$i]["lname"]); ?>" data-action="search_patient" data-grid="<?php echo $j ;?>" data-fld="Active" />
                  	</div>
              		
                  </div>
                  
                  <div class="col-xs-12 "> 
                  	<!-- Suffix-->
                    <div class="col-xs-2 ">
                    	<label>Suffix</label>
                      <br>
                      <input type="text" name="suffix_table_family_information<?php echo $j ;?>" id="suffix_table_family_information<?php echo $j ;?>" class="form-control" value="<?php echo $family_qry_res[$i]["suffix"]; ?>" data-prev-val="<?php echo addslashes($family_qry_res[$i]["suffix"]); ?>" />
                  	</div>
                    
                    <!-- Relaese HIPAA Info -->
                    <div class="col-xs-5  form-inline"	> <br>
						<?php $checked = $family_qry_res[$i]["hippa_release_status"] ==1 ? "checked" : ""; ?>
						<div class="checkbox"><input type="checkbox" id="chkHippaFamilyInformation_<?php echo $j ;?>" name="chkHippaFamilyInformation_<?php echo $j ;?>" <?php echo $checked; ?> value="1" data-prev-val="<?php echo $checked; ?>"><label for="chkHippaFamilyInformation_<?php echo $j ;?>"><span class="text-red">Release HIPAA Info</span></label></div>
                  	</div>
					
					</div>
                  
                  <div class="col-xs-12 ">
                  	<!-- Street1 -->
                    <div class="col-xs-5 ">
                    	<label>Street 1</label>
                      <br>
                      <input name="street1_table_family_information<?php echo $j ;?>" id="street1_table_family_information<?php echo $j ;?>" type="text" class="form-control" value="<?php echo $family_qry_res[$i]["street1"]; ?>" data-prev-val="<?php echo addslashes($family_qry_res[$i]["street1"]); ?>" />
                   	</div>
                    
                    <!-- Street2 -->
                    <div class="col-xs-4 ">
                    	<label>Street 2</label>
                      <br>
                      <input name="street2_table_family_information<?php echo $j ;?>" id="street2_table_family_information<?php echo $j ;?>" type="text" class="form-control" value="<?php echo $family_qry_res[$i]["street2"]; ?>" data-prev-val="<?php echo addslashes($family_qry_res[$i]["street2"]); ?>" />
                   	</div>
                    
                    <!-- Zip Code -->
                    <div class="col-xs-3 "	>
                    	<label><?php getZipPostalLabel(); ?></label>
                      <div class="row">
                      
                        <div class="col-xs-<?php echo (inter_zip_ext() ? '7':'12');?> " >
                          <input name="postal_code_table_family_information<?php echo $j ;?>" type="text" class='form-control' id="code_table_family_information<?php echo $j ;?>" onChange="zip_vs_state_family_state(this.value,'<?php echo $j ;?>'); " value="<?php echo $family_qry_res[$i]["postal_code"]; ?>" data-prev-val="<?php echo addslashes($family_qry_res[$i]["postal_code"]); ?>" maxlength="<?php echo inter_zip_length();?>" size="<?php echo inter_zip_length();?>">
                        </div>
                      
												<?php if(inter_zip_ext()) { ?>
                        <div class="col-xs-5 ">
                          <input name="zip_ext_table_family_information<?php echo $j ;?>" type="text" class='form-control' id="zip_ext_table_family_information<?php echo $j ;?>" value="<?php echo $family_qry_res[$i]["zip_ext"]; ?>" data-prev-val="<?php echo addslashes($family_qry_res[$i]["zip_ext"]); ?>" maxlength="4">
                        </div>
                        <?php } ?>
                      </div>
                 		</div>
                    
                	</div>
                  
                  <div class="col-xs-12 "> 
                  	<!-- City -->
                    <div class="col-xs-3 "	>
                    	<label>City</label>
                      <br>
                      <input name="city_table_family_information<?php echo $j ;?>" type="text" class="form-control" id="city_table_family_information<?php echo $j ;?>" value="<?php echo ucwords($family_qry_res[$i]["city"]); ?>" data-prev-val="<?php echo addslashes($family_qry_res[$i]["city"]); ?>" />
                   	</div>
                    
                    <!-- State -->
                    <div class="col-xs-2 "	>
                    	<label><?php echo ucwords(inter_state_label());?></label>
                      <br>
                      <input name="state_table_family_information<?php echo $j ;?>" type="text" maxlength="<?php if(inter_state_val() == "abb")echo '2';?>" class="form-control" id="state_table_family_information<?php echo $j ;?>" value="<?php echo $family_qry_res[$i]["state"]; ?>" data-prev-val="<?php echo addslashes($family_qry_res[$i]["state"]); ?>" />
                  	</div>
                    
                    <!-- Email ID -->
                    <div class="col-xs-7 "	>
                    	<label>Email-Id</label>
                      <br>
                      <input name="email_table_family_information<?php echo $j ;?>" id="email_table_family_information<?php echo $j ;?>" type="text" class="form-control" value="<?php echo stripslashes($family_qry_res[$i]["email_id"]); ?>" data-prev-val="<?php echo addslashes($family_qry_res[$i]["email_id"]); ?>">
                   	</div>
                    
              		</div>
                  
                  <div class="col-xs-12 ">
                  	<!-- Home Phone -->
                    <div class="col-xs-4 "	>
                    	<label>Home Phone <?php getHashOrNo(); ?></label>
                      <br>
                      <input name="phone_home_table_family_information<?php echo $j ;?>" id="phone_home_table_family_information<?php echo $j ;?>" type="text" class="form-control" maxlength="<?php echo inter_phone_length();?>" value="<?php echo core_phone_format($family_qry_res[$i]["home_phone"]); ?>" data-prev-val="<?php echo addslashes(core_phone_format($family_qry_res[$i]["home_phone"])); ?>" />
                   	</div>
                    
                    <!-- Work Phone -->
                    <div class="col-xs-4 ">
                    	<label>Work Phone <?php getHashOrNo(); ?></label>
                      <br>
                      <input name="phone_work_table_family_information<?php echo $j ;?>" id="phone_work_table_family_information<?php echo $j ;?>" type="text" class="form-control" maxlength="<?php echo inter_phone_length();?>" value="<?php echo core_phone_format($family_qry_res[$i]["work_phone"]); ?>" data-prev-val="<?php echo addslashes(core_phone_format($family_qry_res[$i]["work_phone"])); ?>" />
                   	</div>
                    
                    <!-- Mobile Phone -->
                    <div class="col-xs-4 "	>
                    	<label>Mobile Phone <?php getHashOrNo(); ?></label>
                      <br>
                      <input name="phone_cell_table_family_information<?php echo $j ;?>" id="phone_cell_table_family_information<?php echo $j ;?>" type="text"  class="form-control" maxlength="<?php echo inter_phone_length();?>" value="<?php echo core_phone_format($family_qry_res[$i]["mobile_phone"]); ?>" data-prev-val="<?php echo addslashes(core_phone_format($family_qry_res[$i]["mobile_phone"])); ?>" />
                  	</div>
               	     
                	</div>
                    
                </div>
                
            	</div>
              <?php
								}
							?>
              <input type="hidden" name="last_family_inf_cnt" id="last_family_inf_cnt" value="<?php echo $i; ?>" />
          	</div>
           	
            </div> 
        	</div>
       	  <!-- End Family Information Grid --> 
       			
        </div>
        
        <!-- End Responsible Party/Guarantor AND Family Information Grids -->
       
       	<div class="clearfix"></div>
        
        <!-- Start Miscellaneous Grid -->
        <div class="container-fluid mt5">
        	<div class="col-xs-12 border">
            <div class="row">
              <div class="head">Miscellaneous</div>
            </div>
          
            <div class="row pt-box">
              <div class="col-xs-12 grid-box" tabindex="0" >
                <?php 
                  $getCustomField = "SELECT cf.id as adminControlId,cf.control_lable as adminControlLable, 
                                      cf.control_type as adminControltype,cf.cbk_default_select as adminCbkDefaultSelect,
                                      cf.default_value as adminDefaultvalue,cf.control_name as adminControlName,
                                      cf.module_section as adminModuleSection,
                                      pcf.id as patientControlId,pcf.patient_id as patientControlPatientId, 
                                      pcf.patient_control_value as patientControlVal,pcf.patient_cbk_control_value as patientCbkControlVal  
                                      FROM custom_fields cf 
                                      LEFT JOIN patient_custom_field pcf on 
                                      (cf.id = pcf.admin_control_id and pcf.patient_id = '$pid') 
                                      WHERE cf.module = 'Patient_Info' 
                                      AND cf.sub_module ='Demographics' 
                                      AND cf.status = '0' order by cf.id ";
                  $rsCustomField = imw_query($getCustomField);
                  $MiscellaneousMainShowHide = "none";
                  $MiscellaneousShowHide = "none";
                  if(imw_num_rows($rsCustomField) > 0)
                  {
                    $MiscellaneousMainShowHide = "block";
                  }
                  if($data->patient_data->ptMiscCollapseStatus == 1){
                    $MiscellaneousShowHide = "block";
                  }
                ?>
                <input type="hidden" name="patientControlPId" id="patientControlPId" value=""/>
                <?php
                  $counter= 1;
                  $controlText = "";
                  $controlLabel = "";
                  $arrCustumAuditTrail = array();	
                  $numRows=imw_num_rows($rsCustomField);
                
                  while($row = imw_fetch_assoc($rsCustomField))
                  {
                      $cbkTextBox = $cbkTextBoxLabel = $checked = $controlType = $cbkValue = "";
                      if($row['adminControltype'] == "checkbox"){
                        $controlType = "checkbox";								
                        if($row['patientCbkControlVal']){
                          if($row['patientCbkControlVal'] == "checked"){
                            $checked = "checked";
                          }									
                        }	
                        elseif($row['adminCbkDefaultSelect'] == 1){
                          $checked = "checked";
                        }	
                        
                        if($row['patientControlVal'] != ""){
                          if($row['adminDefaultvalue']){
                            $cbkValue = $row['adminDefaultvalue'];
                          }
                          else{
                            $cbkValue = $row['patientControlVal'];
                          }
                        }
                        elseif($row['adminDefaultvalue'] != ""){
                          $cbkValue = $row['adminDefaultvalue'];
                        }		
                        elseif($checked == "checked"){
                          $cbkValue = "checked";
                        }
                        else{
                          $cbkValue = "checked";
                        }			
                        
                        if($row['adminDefaultvalue']){
                          $cbkTextBoxLabel = $row['adminControlLable']."(".$row['adminDefaultvalue'].")";
                        }
                        else{
                          $cbkTextBoxLabel = $row['adminControlLable'];
                        }
                        $cbkTextBox = "<div class='checkbox'><input type=\"checkbox\" value='".$cbkValue."' name='".$row['adminControlName']."' id='".$row['adminControlName'].$counter."' $checked onClick=\"top.chk_change_in_form('$checked',this,'DemoTabDb',event);\" /><label for='".$row['adminControlName'].$counter."'>".$cbkTextBoxLabel."</label></div>";
                      }
                      elseif($row['adminControltype'] == "text"){
                        $controlType = "text";
                        $prev_val = ($row['patientControlVal'] != "") ? $row['patientControlVal'] : $row['adminDefaultvalue'];
                        $cbkTextBox = "<input type=\"text\" class=\"form-control\" value='".(($row['patientControlVal'] != "") ? $row['patientControlVal'] : $row['adminDefaultvalue'])."' name='".$row['adminControlName']."' data-prev-val=\"".$prev_val."\" />";
                        $cbkTextBoxLabel = $row['adminControlLable'];
                      }
                      $controlText .= "
                          <div class=\"col-xs-3 col-md-2 \">
                            <input type=\"hidden\" name=\"hidPatientControlPId[]\" value=".$row['patientControlId'].">
                            <input type=\"hidden\" name=\"hidcustomField[]\" value=".$row['adminControlName']."_".$row['adminControlId']."_".$controlType.">
                            <label>".($row['adminControltype'] == 'text' ? $cbkTextBoxLabel : '<br>')."</label>
                            <br>
                            ".$cbkTextBox."</div>";
                            $arrCustumAuditTrail [] = 
                                array(							
                                    "Pk_Id"=> $row["patientControlPatientId"],
                                    "Table_Name"=>"patient_custom_field",																	
                                    "Data_Base_Field_Name"=> "patient_control_value" ,
                                    "Filed_Label"=> $row['adminControlName'],
                                    "Filed_Text"=> "Patient Custom Filed ".$row['adminControlLable'],
                                    "Data_Base_Field_Type"=> fun_get_field_type($customDataFields,"patient_control_value") ,	
                                    "Action"=> "update",																							
                                    "Old_Value"=> (($row['patientControlVal'] != "") ? addcslashes(addslashes(trim($row['patientControlVal'])),"\0..\37!@\177..\377") : addcslashes(addslashes(trim($row['adminDefaultvalue'])),"\0..\37!@\177..\377"))																								
                                  );
                            $counter++;
                            /*if($counter == 4)
                            {
                              $controlText .= "<div class=\"clearfix\"></div>";
                              $counter = 1;
                            }*/
                            
                    } 
                  
                  echo $controlText;
                ?>
									<div class="clearfix mb5">&nbsp;</div>
                </div>
            </div>
          </div>   
      	</div>      
        <input type="text" style="width:0px;height:0px;border:0px!important;" name="fakeField" id="fakeField" />
        <!-- Fake field to avoid autofilling in chrome browser-->
        <?php
		$opreaterId = $_SESSION['authId'];
		if($policyStatus == 1){
			$opreaterId = $_SESSION['authId'];			
			$ip = getRealIpAddr();
			$URL = $_SERVER['PHP_SELF'];													 
			//$os = get_os_($_SERVER['HTTP_USER_AGENT']);
			$os = getOS();
			$browserInfoArr = array();
			$browserInfoArr = _browser();
			$browserInfo = $browserInfoArr['browser'] . "-" .$browserInfoArr['version'];
			$browserName = str_replace(";","",$browserInfo);													 
			$machineName = gethostbyaddr($_SERVER['REMOTE_ADDR']);
			##getting audit taril##
			$arrAuditTrail = $patient_data_obj->patient_audit();
			
			$arrAuditTrailView = array();
			$arrTable = array();
			$arrTable = makeUnique(auditViewArray($arrAuditTrail));
			foreach ($arrTable as $key => $value) {
				if(trim($arrTable [$key]["value"]) == "patient_data"){
					if (!array_key_exists('Filed_Label', $arrTable[$key])) {
						$arrTable [$key]["Filed_Label"] = "Patient Demographics Data - ".$pid;
					}
				}
				elseif(trim($arrTable [$key]["value"]) == "resp_party"){
					if (!array_key_exists('Filed_Label', $arrTable[$key])) {
						$arrTable [$key]["Filed_Label"] = "Patient Responsible Party Data - ".$pid;
					}
				}
				elseif(trim($arrTable [$key]["value"]) == "employer_data"){
					if (!array_key_exists('Filed_Label', $arrTable[$key])) {
						$arrTable [$key]["Filed_Label"] = "Patient Occupation Data - ".$pid;
					}
				}		
				elseif(trim($arrTable [$key]["value"]) == "patient_custom_field"){
					if (!array_key_exists('Filed_Label', $arrTable[$key])) {
						$arrTable [$key]["Filed_Label"] = "Patient Custom Field Data - ".$pid;
					}
				}		
			}

			foreach ($arrTable as $key => $value) {
				$arrAuditTrailView [] = array(
							"Pk_Id" => $arrTable [$key]["key"],
							"Table_Name" => $arrTable [$key]["value"],
							"Action" => "view",
							"Operater_Id" => $opreaterId,
							"Operater_Type" => getOperaterType($opreaterId),
							"IP" => $ip,
							"MAC_Address" => $_REQUEST['macaddrs'],
							"URL" => $URL,
							"Browser_Type" => $browserName,
							"OS" => $os,
							"Machine_Name" => $machineName,
							"Category" => "patient_info",
							"Filed_Label" => $arrTable [$key]["Filed_Label"],
							"Category_Desc" => "demographics",
							"pid" => $_SESSION['patient']
				);
			}
			$table = array("patient_data","resp_party","employer_data");
			$error = array($demoError,$respPartyError,$empError);
			$mergedArray = mergingArray($table,$error);
			$patientViewed = array();
			if(isset($_SESSION['Patient_Viewed'])){
				$patientViewed = $_SESSION['Patient_Viewed'];	
				if($patientViewed["Demographics"] == 0){
					if($policyStatus == 1){
						auditTrail($arrAuditTrailView,$mergedArray);
						$patientViewed["Demographics"] = 1;			
						$_SESSION['Patient_Viewed'] = $patientViewed;
					}	
				}
			}
			$result = array();
			$result = array_merge($arrAuditTrail, $arrCustumAuditTrail);
			$arrAuditTrail = array();
			$arrAuditTrail = $result;
			$serialized = serialize($arrAuditTrail);
		}
		?>
        <!-- End Miscellaneous Grid -->
        <?php include_once 'demographics_modal.php'; ?> 
        <input type="hidden" name="hidData" id="hidData" value="<?php echo urlencode($serialized); ?>">
    	</form>
  	</div>
    
    <script type="text/javascript" src="js_demographics.php"></script>
    <script>
			top.$('#acc_page_name').html('<?php echo $pg_title; ?>');
			top.fmain.show_iportal_changes_alert_data('demographics',<?php echo $patient_id; ?>);
			<?php if(count($erp_error) > 0) { ?>
				var erp_error = '<?php echo implode('<br>',$erp_error);?>';
				top.fAlert(erp_error);
			<?php } ?>
		</script>
  </body>
</html>
<?php
$out = ob_get_clean();
$out = Minify_Html($out);
echo $out;
ob_end_flush();
?>
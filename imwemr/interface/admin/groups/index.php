<?php
/*
// The MIT License (MIT)
// THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.
	Purpose: List number of Groups
	Access Type: Direct
*/

include_once('../admin_header.php');
require_once("../../../library/classes/class.language.php");
$arrGroupAlerts = array();
$objCore_lang = new core_lang();
$arrGroupAlerts = $objCore_lang->get_vocabulary("admin", "groups");
$strGroupDelMsg = $objCore_lang->get_vocabulary("admin", "groups","delGroup");

$gro_id = $_REQUEST["gro_id"];

$iframeHight = ($_SESSION['wn_height']-330);
$errMsg = '';
$alreadyInstitution =0;

$qry = "select * from groups_new WHERE del_status='0' and group_institution='1'";
$res =  imw_query($qry);
if(imw_num_rows($res) > 0)
{
	$rowGrp = imw_fetch_array($res);
	$alreadyInstitution =1;
	$alreadyInstitutionGrpId =$rowGrp['gro_id'];
}

		
if($txtSave){
	$_POST['group_Telephone'] = core_phone_unformat($_POST['group_Telephone']);
	$_POST['group_Fax'] = core_phone_unformat($_POST['group_Fax']);
	//----- Insertion Query For New Groups --------
	$loginLegalNotices = $_POST['loginLegalNotices'];
	
	$checkNameQRY = "Select gro_id from groups_new where del_status='0' and name = '".$_REQUEST["name"]."'";
	$resCheckName = imw_query($checkNameQRY);
	if(imw_num_rows($resCheckName)>0 && $gro_id == ''){
		$isErr=1;
		$err = $arrGroupAlerts['groupAlreadyExits'];
	}
	else{
		if($_REQUEST["group_institution"] == 'on'){$group_institution = 1;}
		else {$group_institution = 0;}
		
		if($_POST['allowInstitution'] == '1'){
			$group_institution = 1;
		}
		else if($_POST['allowInstitution'] == '0'){
			$group_institution = 0;
		}

		if($_REQUEST["group_anesthesia"] == 'on'){
			$group_anesthesia = 1;
			$optional_anes_npi = trim($_REQUEST['optional_anes_npi']);
		}else{
			$group_anesthesia = 0;
			$optional_anes_npi = '';	
		}
		$arr_dml["name"] =addslashes($_REQUEST["name"]); //htmlentities(addslashes($_REQUEST["name"]));
		$arr_dml["group_membership"] = $_REQUEST["group_membership"];
		$arr_dml["group_institution"] = $group_institution;
		$arr_dml["group_NPI"] = htmlentities(addslashes($_REQUEST["group_NPI"]));
		$arr_dml["group_Federal_EIN"] = htmlentities(addslashes($_REQUEST["group_Federal_EIN"]));
		$arr_dml["group_Address1"] = htmlentities(addslashes($_REQUEST["group_Address1"]));
		$arr_dml["group_Address2"] = htmlentities(addslashes($_REQUEST["group_Address2"]));
		$arr_dml["group_Zip"] = $_REQUEST["group_Zip"];
		$arr_dml["zip_ext"] = $_REQUEST["zip_ext"];
		$arr_dml["group_City"] = htmlentities(addslashes($_REQUEST["group_City"]));
		$arr_dml["group_State"] = htmlentities(addslashes($_REQUEST["group_State"]));
		$arr_dml["Contact_Name"] = htmlentities(addslashes($_REQUEST["Contact_Name"]));
		$arr_dml["group_Email"] = $_REQUEST["group_Email"];
		$arr_dml["group_Telephone"] = core_phone_unformat($_REQUEST["group_Telephone"]);
		$arr_dml["group_Telephone_ext"] = $_REQUEST["group_Telephone_ext"];
		$arr_dml["group_Fax"] = core_phone_unformat($_REQUEST["group_Fax"]);
		$arr_dml["sec_id"] = $_REQUEST["sec_id"];
		$arr_dml["rec_id"] = $_REQUEST["ReceiverId"];
		$arr_dml["sub_id"] = $_REQUEST["submitterId"];
		$arr_dml["user_id"] = $_REQUEST["EmdeonUserId"];
		$arr_dml["user_pwd"] = $_REQUEST["EmdeonPassword"];
		$arr_dml["prod_tid"] = $_REQUEST["prod_tid"];
		$arr_dml["group_color"] = $_REQUEST["group_color"];
		$arr_dml["MedicareReceiverId"] = $MedicareReceiverId;
		$arr_dml["MedicareSubmitterId"] = $MedicareSubmitterId;
		$arr_dml["THCICSubmitterId"] = $THCICSubmitterId;
		$arr_dml["site_id"] = $site_id;
		$arr_dml["group_anesthesia"] = $group_anesthesia;
		$arr_dml["optional_anes_npi"] = $optional_anes_npi;
		
		$arr_dml["rem_address1"] = htmlentities(addslashes($_REQUEST["rem_address1"]));
		$arr_dml["rem_address2"] = htmlentities(addslashes($_REQUEST["rem_address2"]));
		$arr_dml["rem_zip"] = $_REQUEST["rem_zip"];
		$arr_dml["rem_zip_ext"] = $_REQUEST["rem_zip_ext"];
		$arr_dml["rem_city"] = htmlentities(addslashes($_REQUEST["rem_city"]));
		$arr_dml["rem_state"] = htmlentities(addslashes($_REQUEST["rem_state"]));
		$arr_dml["rem_telephone"] = core_phone_unformat($_REQUEST["rem_telephone"]);
		$arr_dml["rem_telephone_ext"] = $_REQUEST["rem_telephone_ext"];
		$arr_dml["rem_fax"] = core_phone_unformat($_REQUEST["rem_fax"]);
		
		//email configuration
		$arr_edml["config_email"] 	= $_REQUEST["config_email"];
		$arr_edml["config_pwd"] 	= $_REQUEST["config_pwd"];
		$arr_edml["config_host"] 	= $_REQUEST["config_host"];
		$arr_edml["config_port"] 	= $_REQUEST["config_port"];
		
		$arr_edml["config_header"] 	= htmlentities(addslashes($_REQUEST["config_header"]));
		$arr_edml["config_footer"] 	= htmlentities(addslashes($_REQUEST["config_footer"]));
		
		if($_REQUEST['act']=='edit_group')
		{
		if(!$gro_id){
			$insertId = addRecords($arr_dml,'groups_new');
			if($insertId){
				//$err = 'Successfully Saved';
				$gro_id = $insertId;
				$err = $arrGroupAlerts['groupSaveSuccess'];
			}
			else{
				//$err = 'Not Saved Please Try Again';
				$isErr=1;
				$err = $arrGroupAlerts['groupSaveUnSuccess'];
			}
		}
		//----- Updation Query For Previous Groups --------
		else{
			$oldName = $_POST['oldName'];
			if($oldName != $_REQUEST["name"]){
				$checkNameQRY = "Select gro_id from groups_new where name = '".$_REQUEST["name"]."' AND gro_id!='".$gro_id."'";
				$resCheckName = imw_query($checkNameQRY);
				if(imw_num_rows($resCheckName)>0 && $gro_id==''){
					$isErr=1;
					$err = $arrGroupAlerts['groupAlreadyExits'];
				}
			}
			if(!$err){
				$insertId = UpdateRecords($gro_id,'gro_id',$arr_dml,'groups_new');
				if($insertId){
					//$err = 'Successfully Update Records';
					$err = $arrGroupAlerts['groupUpdateSuccess'];
					$iframeHight = 118;
				}
				else{
					$isErr=1;
					//$err = 'Not Update Records Please Try Again';
					$err = $arrGroupAlerts['groupUpdateUnSuccess'];
				}
			}
		}
		
		if($loginLegalNotices<>""){
			$r = imw_query("select loginLegalNotice from hippa_setting");
			$qry = "update users set HIPPA_STATUS = 'no'";
			imw_query($qry);
			if(imw_num_rows($r)>0){
				imw_query("update hippa_setting set loginLegalNotice='$loginLegalNotices'");
			}else{
				imw_query("insert into hippa_setting set loginLegalNotice='$loginLegalNotices'");
			}
		}
		}
		
		elseif($gro_id && $_REQUEST['act']=='config_email')
		{
			UpdateRecords($gro_id,'gro_id',$arr_edml,'groups_new');
		}
	
	}
}

//STATIC INSURANCE TYPES
$ins_type_options='';
$arr_ins_type = array("","11", "12", "13", "14", "15", "16", "17", "AE", "AM", "BL", "CH", "CI", "DS", "FI", "HM", "LM", "MA", "MB", "MC", "OF", "TV", "VA", "WC", "ZZ");
/*  foreach($arr_ins_type as $arr_ins_type_val){
	 $returnArr[] = array("id"=>$arr_ins_type_val,"type"=>$arr_ins_type_val);
	 $ins_type_options.='<option value="'.$arr_ins_type_val.'">'.$arr_ins_type_val.'</option>';
} */

//--- Get Details About Previous Group --------
if($gro_id){
	$groupDetails = (object) getRecords('groups_new','gro_id',$gro_id);
	$group_id= stripslashes($groupDetails->gro_id);
	$name = stripslashes($groupDetails->name);
	$group_institution = stripslashes($groupDetails->group_institution);
	$group_anesthesia = $groupDetails->group_anesthesia;
	$group_NPI = stripslashes($groupDetails->group_NPI);
	$group_Federal_EIN = stripslashes($groupDetails->group_Federal_EIN);
	$group_Address1 = stripslashes($groupDetails->group_Address1);
	$optional_anes_npi = stripslashes($groupDetails->optional_anes_npi);
	$group_Address2 = stripslashes($groupDetails->group_Address2);
	$group_Zip = stripslashes($groupDetails->group_Zip);
	$group_Zip_Ext = stripslashes($groupDetails->zip_ext);
	$group_City = stripslashes($groupDetails->group_City);
	$group_State = stripslashes($groupDetails->group_State);
	$Contact_Name = stripslashes($groupDetails->Contact_Name);
	$group_Email = stripslashes($groupDetails->group_Email);
	$group_Telephone = stripslashes(core_phone_format($groupDetails->group_Telephone));
	$group_Telephone_ext = $groupDetails->group_Telephone_ext;
	$group_Fax = stripslashes(core_phone_format($groupDetails->group_Fax));
	$rec_id = $groupDetails->rec_id;
	$sub_id = $groupDetails->sub_id;
	$user_id = $groupDetails->user_id;
	$user_pwd = $groupDetails->user_pwd;
	$prod_tid = $groupDetails->prod_tid;
	$group_color = $groupDetails->group_color;
	$MedicareReceiverId = $groupDetails->MedicareReceiverId;
	$MedicareSubmitterId = $groupDetails->MedicareSubmitterId;
	$THCICSubmitterId = $groupDetails->THCICSubmitterId;
	
	$rem_address1 = stripslashes($groupDetails->rem_address1);
	$rem_address2 = stripslashes($groupDetails->rem_address2);
	$rem_zip = stripslashes($groupDetails->rem_zip);
	$rem_zip_ext = stripslashes($groupDetails->rem_zip_ext);
	$rem_city = stripslashes($groupDetails->rem_city);
	$rem_state = stripslashes($groupDetails->rem_state);
	$rem_telephone = stripslashes(core_phone_format($groupDetails->rem_telephone));
	$rem_telephone_ext = $groupDetails->rem_telephone_ext;
	$rem_fax = stripslashes(core_phone_format($groupDetails->rem_fax));
	
	$site_id = $groupDetails->site_id;
	
}

if($err == 'Group Already Exists!'){
	$name = '';
}

$templateArray = array();
$templateArray['Other_Information'] =	array('AlreadyInstitution' => $alreadyInstitution,'GroupInstitution' => $group_institute,'GroupDetails' => $group_id,'GroupName' => stripslashes($groupDetails->name),'AlreadyInstitutionGrpId' => $alreadyInstitutionGrpId); 

$port='';
$port=($groupDetails->config_port)?$groupDetails->config_port:25;
$templateArray['email_config'] = array('config_email' => stripslashes($groupDetails->config_email),'config_pwd' => stripslashes($groupDetails->config_pwd),'config_host' => stripslashes($groupDetails->config_host),'config_header' => stripslashes($groupDetails->config_header),'config_footer' => stripslashes($groupDetails->config_footer),'config_port' => stripslashes($groupDetails->config_port)); 


$group_institution_checked = ($group_institution == 1) ? "checked" : '';
$group_anesthesia_checked = ($group_anesthesia == 1) ? "checked" : '';
$group_detail = ($groupDetails->sec_id > 0) ? $groupDetails->sec_id : '';

$templateArray['phone_format'] = $GLOBALS['phone_format'];
$templateArray['state_label'] = inter_state_label();
$templateArray['state_length'] = inter_state_length();
$templateArray['zip_size'] = inter_zip_length();
$templateArray['zip_ext_status'] = inter_zip_ext();
$templateArray['state_val'] = inter_state_val();
$templateArray['int_country'] = inter_country();
$zip_ext_view = inter_zip_ext() ? 'inline' : 'none';
$templateArray["zip_ext"] = $zip_ext_view;
$templateArray["zip_length"] = inter_zip_length();
	   					  
$templateArray['Group_Info' ] = array(
	'Name' => $name,
	'GroupInstitutionChecked' => $group_institution_checked,
	'GroupAnesthesiaChecked' => $group_anesthesia_checked,
	'GroupDetailSecondaryId'  => $group_detail,
	'GroupNPI' => $group_NPI,
	'GroupFederalEIN' => $group_Federal_EIN,
	'optional_anes_npi' => $optional_anes_npi,
	'GroupColor' => $group_color
);
  
$templateArray['Mailing'] = array(
	'GroupAddress1'=> $group_Address1,
	'GroupAddress2'=> $group_Address2,
	'GroupZip_Ext'=> $group_Zip_Ext,
	'GroupZip'=> $group_Zip,
	'GroupCity'=> $group_City,
	'GroupState'=> $group_State,
	'MedicareReceiverId'=> $MedicareReceiverId,
	'MedicareSubmitterId'=> $MedicareSubmitterId,
	'THCICSubmitterId'=> $THCICSubmitterId
);						   						  

$templateArray['Remittance'] = array('rem_address1' => $rem_address1,
	'rem_address2' => $rem_address2,
	'rem_zip_ext'=> $rem_zip_ext,
	'rem_zip'=> $rem_zip,
	'rem_city'=> $rem_city,
	'rem_state'=> $rem_state,
	'rem_telephone'=> $rem_telephone,
	'rem_telephone_ext'=> $rem_telephone_ext,
	'rem_fax'=> $rem_fax
);
						  		    
$templateArray['Contacts'] = array('ContactName' => $Contact_Name, 
	'GroupEmail' => $group_Email,
	'GroupTelephone' => $group_Telephone,
	'GroupTelephone_ext' => $group_Telephone_ext,
	'GroupFax' => $group_Fax
);
$templateArray['House_Info'] = array('RecId' => $rec_id, 'SubId' => $sub_id, 'SiteId' => $site_id );
$templateArray['Access'] = array('UserId' => $user_id, 'UserPwd' => $user_pwd, 'prod_tid' => $prod_tid );	

$qry = "Select loginLegalNotice from hippa_setting";
$res = imw_query($qry);
list($loginLegalNotice) = imw_fetch_array($res);
if($gro_id<>""){
	$notice = $loginLegalNotice;
}
						
$templateArray['notice'] = $notice;
$templateArray['webroot'] = $GLOBALS['webroot'];
$templateArray['wn_height'] = $_SESSION['wn_height'];
$templateArray['AlertMessages'] =	array('Name' => $arrGroupAlerts["name"],
										'Group_NPI' => $arrGroupAlerts["group_NPI"],
										'Group_NPI_Value_Length' => $arrGroupAlerts["group_NPI_value_length"],
										'Group_Federal_EIN' => $arrGroupAlerts["group_Federal_EIN"],
										'Group_Address' => $arrGroupAlerts["group_Address1"],
										'Group_Zip' => $arrGroupAlerts["group_Zip"],
										'Group_City' => $arrGroupAlerts["group_City"],
										'Group_State'	=> $arrGroupAlerts["group_State"],
										'Contact_Name' => $arrGroupAlerts["Contact_Name"],
										'Group_Telephone' => $arrGroupAlerts["group_Telephone"],
										'Group_Email'	=> $arrGroupAlerts["group_Email"],
										'Ajax_Zip_Validation'	=> $arrGroupAlerts["AjaxZipValidation"],
										'Check_Institution'	=> $arrGroupAlerts["checkInstitution"]);
										
$templateArray['delete_msg'] = $strGroupDelMsg;										
$templateArray['group_selected_id'] = $gro_id;
$templateArray['ins_drop_down'] = $arr_ins_type;									
?>
<body>
	<div class="container-fluid">
		<input type="hidden" name="preObjBack" value="">
		<textarea id="hidd_reason_text" style="display:none;"></textarea>
		<div class="whtbox" style="height:<?php echo ($_SESSION['wn_height']-305);?>px; overflow-x:hidden; overflow-y:auto;">
			<div class="table-responsive provtab">
				<table class="table table-bordered table-hover adminnw">
				<thead>
					<tr>
						<th width="1%">
							<div class="checkbox">
								<input type="checkbox" name="chk_sel_all" id="chk_sel_all" value="" autocomplete="off">
								<label for="chk_sel_all">
								</label>
							</div>
						</th>
						<th width="16%">Business Unit </th>
						<th width="8%">NPI#</th>
						<th width="6%">Color</th>
						<th width="23%">Address</th>
						<th width="12%">Email</th>
						<th width="12%">Phone</th>
						<th width="10%">Fax</th>
						<th width="7%">Contact</th>
						<th width="6%" class="text-center" colspan="2">Action</th>
					</tr>
				</thead>
				<tbody id="group_tbl_body"></tbody>
				</table>
		  </div>
		  <div class="clearfix"></div>
		</div>
	</div>

	<?php 
		 echo '<script>var grp_email = "";</script>';	
		if($_REQUEST['addnew']=='y' || $_REQUEST['act']=='edit_group'){ 
			include 'group.php';
			echo '<script>grp_email = "";</script>';	
		}
		
		if($gro_id  && $_REQUEST['act']=='email_config'){ 
			include 'group_email_config.php';
			echo '<script>grp_email = "yes";</script>';	
		}
	?>

	<!-- Multiple NPI Modal -->
	<div class="common_wrapper">
		<div id="npi_div" class="modal" role="dialog">
			<div class="modal-dialog">
				<div class="modal-content">
					<form action="" method="post" name="form_multiple_npi" id="form_multiple_npi">
						<!-- To make selectpicker go beyond modal limit -->
						<div id="select_box" style="position:absolute;"></div>
						<div class="modal-header bg-primary">
							<button type="button" class="close" data-dismiss="modal">x</button>
							<h4 class="modal-title" id="modal_title">
								Group NPI
							</h4>
						</div>
						<div class="modal-body">
							<div class="row">
								<table class="table table-bordered table-hover adminnw">
									<thead>
										<tr>
											<th>NPI</th>
											<th>Insurance Type</th>
											<th colspan="2">Default</th>
										</tr>
									</thead>
									<tbody id="table_npi"></tbody>
								</table>
							</div>
						</div>
						<div class="modal-footer">
							<input type="hidden" name="group_id" id="group_id" value="<?php echo $templateArray['Other_Information']['GroupDetails']; ?>" />	
							<input type="hidden" name="default_npi_num" id="default_npi_num" value="0">
							<input type="hidden" name="totNPIRows" id="totNPIRows" value="0">
							<input type="hidden" name="ajax_request" value="yes">
							<input type="hidden" name="npi_request" value="yes">
							<input type="hidden" name="npi_mode" value="save">
						</div>
					</form>
				</div>	
			</div>
		</div>	
	</div>	
	
	<script type="text/javascript">
		var temp_arr = <?php echo json_encode($templateArray);?>;
		//Btn --
		<?php 
			if($err != ""){
				if($isErr=='1'){ 
		?>
					top.fAlert('<?php echo $err; ?>');
		<?php 
				}else{
		?>
					top.alert_notification_show('<?php echo $err; ?>');	
					window.location.href = "index.php";			
		<?php  
				}
			}
		?>
	</script>
	<script type="text/javascript" src="<?php echo $GLOBALS['webroot']; ?>/library/js/admin/admin_group.js"></script>
<?php include_once('../admin_footer.php'); ?>
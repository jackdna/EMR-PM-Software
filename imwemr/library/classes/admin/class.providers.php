<?php
/*
	The MIT License (MIT)
	Distribute, Modify and Contribute under MIT License
	Use this software under MIT License
*/
require_once($GLOBALS['srcdir'].'/classes/class.language.php');
require_once($GLOBALS['srcdir'].'/classes/cls_common_function.php');
require_once($GLOBALS['srcdir']."/classes/audit_common_function.php");
$cls_common = new CLSCommonFunction();
class Providers extends core_lang {

	public $vocabulary = false;
	public function __construct()
	{
		parent::__construct();
		$this->vocabulary = $this->get_vocabulary("admin", "poviders");
	}

	private function get_user_listing($search_term = "",$srh_del_status=0,$sort_by = 'u.lname', $sort_type = 'ASC'){
		try{
			$where = "";
			if($search_term != ""){
				$where = " and u.lname like '%".$search_term."%' ";
			}
			$query = "SELECT u.username, u.fname, u.lname, u.mname, u.default_group, u.default_facility, u.user_group_id, u.provider_color, u.locked, u.id, u.gro_id, u.user_type, ut.user_type_name, ug.name as groupName, f.name as facilityName FROM users u
			LEFT JOIN user_type ut ON ut.user_type_id = u.user_type
			LEFT JOIN user_groups ug ON ug.id = u.user_group_id
			LEFT JOIN facility f ON f.id = u.default_facility where u.delete_status='$srh_del_status' and u.superuser='no' ".$where." order by $sort_by $sort_type";
			return get_array_records_query($query);
		}
		catch(Exception $error){
			die($error.imw_error());
		}
	}

	//to user listing
	private function get_user_all_listing(){
		try{
			$where = "";
			$query = "select username,user_npi,password from users where username is not NULL order by lname, fname";
			return get_array_records_query($query);
		}catch(Exception $error){
			die($error.imw_error());
		}
	}

	//to get provider types
	private function get_provider_types($int_user_type_id = 0){
		try{
			$where = "";
			if($int_user_type_id != 0){
				$where = " and user_type_id = '".$int_user_type_id."' ";
			}
			$sql = "select user_type_id, user_type_name, default_access_privileges, default_scheduler_status, color from `user_type` where `status` = '1' ".$where."
					order by Field(user_type_id,11,19,10,12,1) DESC, user_type_name asc ";
			return get_array_records_query($sql);
		}catch(Exception $error){
			die($error.imw_error());
		}
	}

	//to get facility name
	private function get_facility_name($int_facility_id){
		try{
			$sql = "select name from `facility` where id = '".$int_facility_id."'";
			return get_array_records_query($sql);
		}catch(Exception $error){
			die($error.imw_error());
		}
	}

	//to get user groups
	private function get_user_groups($int_user_group_id = 0){
		try{
			$where = "";
			if($int_user_group_id != 0){
				$where = " and id = '".$int_user_group_id."' ";
			}
			$sql = "select id, name, display_order, status from `user_groups` where `status` = '1' ".$where." order by `name`";
			return get_array_records_query($sql);
		}
		catch(Exception $error){
			die($error.imw_error());
		}
	}

	private function get_user_details($intUserId){
		$syncQry = (constant('REMOTE_SYNC')=='1')  ? " imwemr_id, " : '';
		try{
			$query = "select 	username as pro_user, fname as pro_fname,  mname as pro_mname,
												lname as pro_lname, user_group_id as pro_group, federaldrugid as pro_drug,
												upin as pro_upin, provider_color as pro_color, licence as pro_lic,
												additional_info as pro_add, user_type as pro_type, gro_id as groups,
												default_group as default_group, access_pri as pre_phy, default_facility as default_facility,
												max_appoint as max_appoint, sch_index, user_npi as user_npi, TaxonomyId as TaxonomyId,
												MedicareId as MedicareId, MedicaidId as MedicaidId, BlueShieldId as BlueShieldId,
												TaxId as pro_tax, superuser as superuser, locked as locked,
												eRx_user_name as erx_user_name, erx_password as erx_password, pro_title as pro_title,
												pro_suffix as pro_suffix, eRx_facility_id as eRx_facility_id,
												Enable_Scheduler as Enable_Scheduler, StopOverBooking as StopOverBooking,
												session_timeout as session_timeout, collect_refraction as collect_refraction,
												sign as elem_sign, sign_path as elem_sign_path, SLA as SLA,
												HIPPA_STATUS as HIPPA_STATUS, sla_date as sla_date, hippa_date as hippa_date,
												sch_facilities, max_day,  max_per, external_id, sign_sigplus_path, ".$syncQry."
												pt_coordinator, follow_physician, sx_physician,nicname as pro_nicname,
												email as d_email, email_password as d_pass, zeiss_username, zeiss_password, direct_access, updox_user_id,
												sso_identifier, groups_prevlgs_id, posfacilitygroup_id, view_all_provider_financials, provider_financials, portal_refill_direct_access 
												from `users` where `id` = '".$intUserId."'";
			return get_array_records_query($query);

		}catch(Exception $error){
			die($error.imw_error());
		}
	}

	public function get_user_details_all($id){
		$return_arr = array();
		$where_cond = '';
		if($id){
			$where_cond = "id != '".$id."' and";
		}

		$query = imw_query("select id,user_group_id,username as pro_user, fname as pro_fname,  mname as pro_mname,lname as pro_lname, user_group_id as pro_group,sx_physician,nicname as pro_nicname,email as d_email, email_password as d_pass from `users` where ".$where_cond." email != '' and email_password != '' and delete_status = 0");
		if(imw_num_rows($query) > 0){
			while($row = imw_fetch_assoc($query)){
				$return_arr[] = $row;
			}
		}
		return $return_arr;
	}

	//to check for unique password
	private function check_for_unique_password($str_new_pass){
		try{
			$sql = "select id from `users` where password = '".$str_new_pass."'";
			return get_array_records_query($sql);
		}catch(Exception $error){
			die($error.imw_error());
		}
	}

	//to check for unique password
	private function check_for_unique_password_ref_phy($str_new_pass){
		try{
			$sql = "select physician_Reffer_id from `refferphysician` where password = '".$str_new_pass."'";
			return get_array_records_query($sql);
		}catch(Exception $error){
			die($error.imw_error());
		}
	}

	//to get max most recent passwords to check for duplicacy
	private function max_most_recent_pass(){
		try{
			$sql = "select maxRecentlyUsedPass from `facility` where facility_type = 1 order by id limit 1";
			return get_array_records_query($sql);
		}catch(Exception $error){
			die($error.imw_error());
		}
	}

	//to get audit policy status
	private function get_audit_policy($policy_id){
		try{
			$sql = "select policy_status from `audit_policies` where policy_id = '".$policy_id."'";
			return get_array_records_query($sql);
		}catch(Exception $error){
			die($error.imw_error());
		}
	}

	//to get audit policy status
	private function get_billing_policy(){
		try{
			$sql = "select * from `copay_policies` limit 1";
			return get_array_records_query($sql);
		}catch(Exception $error){
			die($error.imw_error());
		}
	}

	private function get_name_titles(){
		return array("Mr.", "Mrs.", "Ms.", "Dr.");
	}

	//to get facilities
	private function get_facilities(){
		try{
			$sql = "select id, name from `facility` order by `name`";
			return get_array_records_query($sql);
		}catch(Exception $error){
			die($error.imw_error());
		}
	}

	//to get groups
	private function get_groups($intGroupId = "-1"){
		try{
			$sql = "select gro_id, name, group_Address1, group_Address2, group_City, group_State, group_Zip, group_Telephone, group_Fax, group_Email, group_Federal_EIN from `groups_new` where";
			if($intGroupId != "-1"){
				$sql .= " gro_id = '".$intGroupId."'";
			}
			else{
				$sql .= " del_status='0'";
			}
			return get_array_records_query($sql);
		}catch(Exception $error){
			die($error.imw_error());
		}
	}

	private function providers_specility(){

		$proId = "";
		if(isset($_REQUEST['pro_id']) && !empty($_REQUEST['pro_id'])){
			$proId = $_REQUEST['pro_id'];
		}

		//GeT Speciality Data --
		$splData="";
		$query = "SELECT * FROM admn_speciality WHERE status='0' Order By name ";
		$sql = imw_query($query);

		$i = -1;
		while($row = imw_fetch_assoc($sql) )
		{
			$i++;
			$id=$row["id"];
			$name=$row["name"];
			$chk="";

			if(!empty($proId)){
				$query_inn = "SELECT count(*) as totalRecords FROM phy_speciality WHERE phyId='".$proId."' AND spId='".$id."' AND status='0' ";
				$sql_inn = imw_query($query_inn);
				$row_inn = imw_fetch_assoc($sql_inn);
				$chk = ($row_inn['totalRecords'] > 0) ? 'selected' : '';
			}
			$splData .= '<option value="'.$id.'" '.$chk.' >'.$name.'</option>';
		}


		$str = '';
		$str = '<div id="EnableOpt0"><select tabindex="10" multiple data-size="7" data-width="100%" name="elem_spl[]" id="elem_spl" class="selectpicker" title="'.imw_msg('drop_sel').'" data-actions-box="true">';
		$str .= $splData;
		$str .= '</select>
						 <input type="hidden" id="selected_elem_spl" name="selected_elem_spl" value="" /></div>';

		return $str;
	}

	private function canvas_show()
	{
		$return = '';
		$isHtml5OK = isHtml5OK();
		if(!empty($isHtml5OK))
		{
			$return .= '
				<div class="sig_applet">
					<canvas id="sign" width="361" height="61"></canvas>
					<input type="hidden" name="sig_datasign" id="sig_datasign" />
					<input type="hidden" name="sig_imgsign" id="sig_imgsign" value="'.$sig_path_od.'" />
				</div>
				<script>
					$(document).ready(function () {
						$("canvas").each(function(){  oSimpDrw[this.id]=new SimpleDrawing(this.id); oSimpDrw[this.id].init(); });
					});
				</script>';
		}
		else{ //COMPATIBILITY
			$return .= '
					<input type="hidden" name="elem_sign" id="elem_sign" value="'.((isset($elem_sign)) ? trim($elem_sign) : "").'">
				';
		}
		return $return;

	}

	private function checkDuplicateNPI($user_npi, $int_prov_id = ""){
		try{
			if($user_npi != ""){
				$sql = "select user_npi from users where user_npi = '".addslashes(strtolower($user_npi))."'";
				if($int_prov_id != "")
					$sql .= " AND id != '".$int_prov_id."'";
				return get_array_records_query($sql);
			}else{
				return false;
			}
		}catch(Exception $error){
			die($error.imw_error());
		}
	}

	private function check_imw_key($user_npi,$imw_key){
		$ret=false;
		if($user_npi && $imw_key){
			$npi_no_key=strtoupper(substr(sha1("imw".$user_npi."idoc"),2,15));
			if(trim($imw_key)==trim($npi_no_key)){
				$ret=true;
			}
		}
		return $ret;
	}

	//to check duplicate username in ref phy tables
	private function checkDuplicateRefPhyUsername($str_username, $int_prov_id = ""){
		try{
			if($str_username != ""){
				$sql = "select physician_Reffer_id from refferphysician where userName = '".addslashes(strtolower($str_username))."'";
				if($int_prov_id != "")
					$sql .= " AND physician_Reffer_id != '".$int_prov_id."'";
				return get_array_records_query($sql);
			}else{
				return false;
			}
		}catch(Exception $error){
			die($error.imw_error());
		}
	}

	//to check duplicate username in ref phy tables
	private function checkDuplicateUsername($str_username, $int_prov_id = ""){
		try{
			if($str_username != ""){
				$sql = "select id from users where username = '".addslashes(strtolower($str_username))."' and delete_status = 0";
				if($int_prov_id != "")
					$sql .= " AND id != '".$int_prov_id."'";
				return get_array_records_query($sql);
			}else{
				return false;
			}
		}catch(Exception $error){
			die($error.imw_error());
		}
	}

	private function clear_cache(){
		global $cls_common;
		if(file_exists(data_path()."xml/Provider_Data.xml")){
			unlink(data_path()."xml/Provider_Data.xml");
		}
		$cls_common->create_provider_xml();
	}

	//to redirect to another page
	public function redirectTo($url){
		echo "<script>location.href='".$url."';</script>";
		die;
	}

	public function del_selected_provider(){
		$users_ids=$_GET['del_user_ids'];
		$str_user_name=$_GET['str_user_name'];
		$del_info="Delete Reason:".trim(addslashes($_REQUEST['delReason']))."\nDate Time:".date("Y-m-d H:i:s",time())."\ndel_operator:".$_SESSION['authId'];
		$erp_error=array();
        if(isERPPortalEnabled()) {
			try {
				$this->deleteUserOnErpPortal($users_ids);
			} catch(Exception $e) {
				$erp_error[]='Unable to connect to ERP Portal';
			}
        }

		$qry_del = "Update users set delete_status='1',del_info='".$del_info."' where id in(".$users_ids.")";
		$res_del = imw_query($qry_del)or die(imw_error());

		//auditing this action
		//get audit policy
		$arrAudit = $this->get_audit_policy(13);
		$intAuditOnOff = $arrAudit[0]['policy_status'];

		if($intAuditOnOff == 1){
			$usersDataFields = make_field_type_array("users");
			$arrAuditTrail = array();
			$opreaterId = $_SESSION['authId'];
			//$ip = $_SERVER['REMOTE_ADDR'];
			$ip = getRealIpAddr();
			$URL = $_SERVER['PHP_SELF'];
			$os = getOS();
			$browserInfoArr = array();
			$browserInfoArr = _browser();
			$browserInfo = $browserInfoArr['browser'] . "-" .$browserInfoArr['version'];
			$browserName = str_replace(";","",$browserInfo);
			$machineName = gethostbyaddr($_SERVER['REMOTE_ADDR']);

			$arrAuditTrail [] =
							array(
									"Pk_Id"=> $users_ids,
									"Table_Name"=>"users",
									"Data_Base_Field_Name"=> "id" ,
									"Data_Base_Field_Type"=> fun_get_field_type($usersDataFields,"id") ,
									"Filed_Label"=> "id",
									"Old_Value"=> $str_user_name,
									"Operater_Id"=> $opreaterId,
									"Operater_Type"=> getOperaterType($opreaterId) ,
									"IP"=> $ip,
									"MAC_Address"=> $_REQUEST['macaddrs'],
									"URL"=> $URL,
									"Browser_Type"=> $browserName,
									"OS"=> $os,
									"Machine_Name"=> $machineName,
									"Category"=> "admin",
									"Category_Desc"=> "providers",
									"Action" => "delete"
								);

			$table = array("users");
			$error = array($userError);
			$mergedArray = array();
			if(count($table) == count($error)){
				for($a=0; $a < count($table); $a++){
					$mergedArray[] = array(
											"Table_Name"=> trim($table[$a]),
											"Error"=> trim($error[$a])
										  );
				}
			}
			auditTrail($arrAuditTrail,$mergedArray,$users_ids,0,0);
		}
	}

	public function providers_listing()
	{
		$srh_del_status = (isset($_REQUEST['srh_del_status']) && $_REQUEST['srh_del_status']!='')?$_REQUEST['srh_del_status']:0;
		if(isset($_REQUEST["search_term"]) && $_REQUEST["search_term"] != ""){
			$arrUserRecords = $this->get_user_listing($_REQUEST["search_term"]);
		}else if(isset($_REQUEST["sort_term"]) && $_REQUEST["sort_term"] != ""){
			$arrUserRecords = $this->get_user_listing('',$srh_del_status,$_REQUEST["sort_term"],$_REQUEST['sort_type']);
		}else{
			$arrUserRecords = $this->get_user_listing('',$srh_del_status,'lname',$_REQUEST['sort_type']);
		}
		$arrUserRecords_chk = $this->get_user_all_listing();
		$password_arr=$username_arr=$usernpi_arr=array();
		for($row = 0; $row < count($arrUserRecords_chk); $row++){
			if($arrUserRecords_chk[$row]['username']!=""){
				$username_arr[$arrUserRecords_chk[$row]['username']]=$arrUserRecords_chk[$row]['username'];
				$password_arr[$arrUserRecords_chk[$row]['password']]=$arrUserRecords_chk[$row]['password'];
				if($arrUserRecords_chk[$row]['user_npi']!=""){
					$usernpi_arr[$arrUserRecords_chk[$row]['user_npi']]=$arrUserRecords_chk[$row]['username'];
				}
			}
		}
		$return = "";

		$intUserCnt = count($arrUserRecords);
		if(is_array($arrUserRecords) && $intUserCnt > 0){
			for($row = 0; $row < $intUserCnt; $row++){
				$str_user_type_name = " N/A ";
				if($arrUserRecords[$row]['user_type'] != ""){
					$arrGroName = $this->get_provider_types($arrUserRecords[$row]['user_type']);
					$str_user_type_name = $arrGroName[0]["user_type_name"];
				}

				$arrUserGroName= $this->get_user_groups($arrUserRecords[$row]['user_group_id']);
				$arrFacName= $this->get_facility_name($arrUserRecords[$row]['default_facility']);

				$provider_color = ($arrUserRecords[$row]["provider_color"]) ? '<div style="background-color:'.$arrUserRecords[$row]["provider_color"].'; width:95%; height:25px;"></div>' : '';

				$lock_btn = '<button class="btn btn-danger" onclick="javascript:lock_account(\'0\', \''.$arrUserRecords[$row]['id'].'\')" >Un Lock</button>';
				if($arrUserRecords[$row]['locked'] == 0)
					$lock_btn = '<button class="btn btn-success" onclick="javascript:lock_account(\'1\', \''.$arrUserRecords[$row]['id'].'\')" >Lock</button>';

				$onClick = 'onclick="load_provider(\''.$arrUserRecords[$row]['id'].'\',\''.$srh_del_status.'\');"';

				$return .= '<tr class="pointer" id="p'.$arrUserRecords[$row]['id'].'">';
				$return .= '<td class="text-center">';
				$return .= '<div class="checkbox">';
				$return .= '<input class="chk_sel" type="checkbox" value="'.$arrUserRecords[$row]['id'].'" id="chk_'.$arrUserRecords[$row]['id'].'" >';
				$return .= '<label for="chk_'.$arrUserRecords[$row]['id'].'"></label>';
				$return .= '</div>';
				$return .= '</td>';

				$return .= '<td '.$onClick.'>'.core_name_format($arrUserRecords[$row]['lname'],$arrUserRecords[$row]['fname'],$arrUserRecords[$row]['mname']).'</td>';

				$return .= '<td '.$onClick.'> '.$str_user_type_name.'</td>';
				$return .= '<td '.$onClick.'>'.($arrUserRecords[$row]['user_group_id'] == 0 ? 'N/A' : $arrUserGroName[0]["name"]).'</td>';
				$return .='	<td '.$onClick.'>'.($arrUserRecords[$row]['default_facility'] == "" ? '' : $arrFacName[0]["name"]).'</td>';

				$return .= '<td class="text-center" '.$onClick.' >'.$provider_color.'</td>';
				$return .= '<td class="text-center">';
				$return .= '<button id="chg_password_'.$arrUserRecords[$row]['id'].'" onClick="changepasswords(\''.$arrUserRecords[$row]['id'].'\');" class="btn btn-success" >Reset Password</button';
				$return .= '</td>';

				$return .='	<td class="text-center" >'.$lock_btn.'</td>';
				$return .= '</tr>';
			}
		}else{
			$return .= '<tr><td class="text-center" colspan="8">No Record Found.</td></tr>';
		}

		$json_username_arr = json_encode($username_arr);
		$return .= "<div id='json_username_div' style='display:none'>".$json_username_arr."</div>";
		$json_usernpi_arr = json_encode($usernpi_arr);
		$return .= "<div id='json_usernpi_div' style='display:none'>".$json_usernpi_arr."</div>";
		$json_password_arr = json_encode($password_arr);
		$return .= "<div id='json_password_div' style='display:none'>".$json_password_arr."</div>";

    return $return;

	}

	public function providers_account_locked()
	{
		$int_user_id = $_REQUEST["user_id"];
		$int_action = $_REQUEST["doaction"];

		$int_old_action = ($int_action == 1) ? 0 : 1;
		$str_action = ($int_action == 1) ? "user_login_f" : "update";

		$arreRxCols = array();
		$arreRxVals = array();

		$arreRxCols[] = "locked";			$arreRxVals[] = $int_action;

		$query = "Update users set locked = '".$int_action."' Where id = ".$int_user_id." ";
		$sql = imw_query($query);

		// Audit Functionality
		//auditing this action
		//get audit policy
		$arrAudit = $this->get_audit_policy(13);
		$intAuditOnOff = $arrAudit[0]['policy_status'];

		if($intAuditOnOff == 1){
			$usersDataFields = make_field_type_array("users");
			$arrAuditTrail = array();
			$opreaterId = $_SESSION['authId'];
			//$ip = $_SERVER['REMOTE_ADDR'];
			$ip = getRealIpAddr();
			$URL = $_SERVER['PHP_SELF'];
			$os = getOS();
			$browserInfoArr = array();
			$browserInfoArr = _browser();
			$browserInfo = $browserInfoArr['browser'] . "-" .$browserInfoArr['version'];
			$browserName = str_replace(";","",$browserInfo);
			$machineName = gethostbyaddr($_SERVER['REMOTE_ADDR']);

			$arrAuditTrail [] =
							array(
									"Pk_Id"=> $int_user_id,
									"Table_Name"=>"users",
									"Data_Base_Field_Name"=> "locked",
									"Data_Base_Field_Type"=> fun_get_field_type($usersDataFields,"locked") ,
									"Filed_Label"=> "Lock/Unlock",
									"Operater_Id"=> $opreaterId,
									"Operater_Type"=> getOperaterType($opreaterId) ,
									"IP"=> $ip,
									"MAC_Address"=> $_REQUEST['macaddrs'],
									"URL"=> $URL,
									"Browser_Type"=> $browserName,
									"OS"=> $os,
									"Machine_Name"=> $machineName,
									"Category"=> "admin",
									"Category_Desc"=> "provider",
									"Action" => $str_action,
									"Old_Value"=> $int_old_action,
									"New_Value"=>  $int_action
								);
			$table = array("users");
			$error = array($userError);
			$mergedArray = array();
			if(count($table) == count($error)){
				for($a=0; $a < count($table); $a++){
					$mergedArray[] = array(
											"Table_Name"=> trim($table[$a]),
											"Error"=> trim($error[$a])
										  );
				}
			}
			auditTrail($arrAuditTrail,$mergedArray,$int_user_id,0,0);
		}
	}

	public function reset_password_modal()
	{
		$jsAlertNo = 0;
		$arrUserRecords = $this->get_user_details($_REQUEST["myid"]);

		$html  = '';
		$html .= '<div id="reset_password_div" class="modal" role="dialog" style="z-index:1051;" >';
		$html .= '<div class="modal-dialog modal-sm">';
		// Modal content
		$html .= '<div class="modal-content">';
		$html .= '<form name="reset_pass_form" method="post" id="reset_pass_form" class="margin_0" action="" autocomplete="off">';
		// Modal Header
		$html .= '<div class="modal-header bg-primary">';
		$html .= '<button type="button" class="close" data-dismiss="modal">×</button>';
		$html .= '<h4 class="modal-title" id="modal_title">Reset Password for '.(core_name_format($arrUserRecords[0]['pro_fname'],$arrUserRecords[0]['pro_mname'],$arrUserRecords[0]['pro_lname'])).'</h4>';
		$html .= '</div>';
		// Modal Body
		$html .= '<div class="modal-body" style="max-height:450px; overflow:hidden; overflow-y:auto;">';
		$html .= '<input type="hidden" name="hid_save" value="save">';
		$html .= '<input type="hidden" name="pro_id" value="'.(isset($_REQUEST["myid"]) ? $_REQUEST["myid"] : "").'">';

		$html .= '<div class="row">';
		$html .= '<label for="pro_pass_new">New Password</label><br>';
		$html .= '<input name="pro_pass_new" id="pro_pass_new" type="password" tabindex="101" class="form-control" value="" onBlur="return countCharResFn(this, \'\', \'\');">';
		$arrUserRecords_chk = $this->get_user_all_listing();
		$password_arr=array();
		for($row = 0; $row < count($arrUserRecords_chk); $row++){
			if($arrUserRecords_chk[$row]['username']!=""){
				$password_arr[$arrUserRecords_chk[$row]['password']]=$arrUserRecords_chk[$row]['password'];
			}
		}
		$json_password_arr = json_encode($password_arr);
		$html .= "<div id='json_password_div' style='display:none'>".$json_password_arr."</div>";
		$html .= '<input name="pro_pass_old" id="pro_pass_old" type="hidden" value="">';
		$html .= '</div>';

		$html .= '<div class="row">';
		$html .= '<label for="pro_pass_new">Confirm Password</label><br>';
		$html .= '<input name="confirm_pass_new" id="confirm_pass_new" tabindex="102" type="password" class="form-control" value="" >';
		$html .= '</div>';

		$html .= '<div class="clearfix">&nbsp;</div>';
		$html .= '</div>';
		// Modal Footer
    $html .= '<div class="modal-footer">';
		$html .= '</div>';


		$html .= '</div>';
		//$html .= '<div id="divResetPassProAlertMsg" style="display:none;"></div>';
		$html .= '</div>';

		return $html;
	}

	public function reset_password_save()
	{
		if(isset($_REQUEST["hid_save"]) && $_REQUEST["hid_save"] == "save")
		{
			$pro_id = $_REQUEST["pro_id"];
			$pro_pass_new = hashPassword($_REQUEST["pro_pass_new"]);

			//checking for if new password is available or not
			if($this->check_for_unique_password($pro_pass_new) == false && $this->check_for_unique_password_ref_phy($pro_pass_new) == false){
				//checking as if new password has not been used in most recent (maxRecentlyUsedPass - set in HQ facility setting) passwords
				//$arr_most_recent_chk = $this->max_most_recent_pass();
				//$int_most_recent_chk = $arr_most_recent_chk[0][0];

				$qry = "Update users set password = '".$pro_pass_new."', locked = 0, passwordChanged = 1, passCreatedOn = '".date('Y-m-d')."', passwordReset = 1 Where id = '".$pro_id."' ";
				imw_query($qry);

				//  Audit Functionality
				//get audit policy
				$arrAudit = $this->get_audit_policy(13);
				$intAuditOnOff = $arrAudit[0]['policy_status'];

				//auditing the change
				if($intAuditOnOff == 1){
					$usersDataFields = make_field_type_array("users");
					$arrAuditTrail = array();
					$opreaterId = $_SESSION['authId'];
					//$ip = $_SERVER['REMOTE_ADDR'];
					$ip = getRealIpAddr();
					$URL = $_SERVER['PHP_SELF'];
					$os = getOS();
					$browserInfoArr = array();
					$browserInfoArr = _browser();
					$browserInfo = $browserInfoArr['browser'] . "-" .$browserInfoArr['version'];
					$browserName = str_replace(";","",$browserInfo);
					$machineName = gethostbyaddr($_SERVER['REMOTE_ADDR']);
					$arrAuditTrail [] = array(
										"Pk_Id"=> $pro_id,
										"Table_Name"=>"users",
										"Data_Base_Field_Name"=> "password" ,
										"Data_Base_Field_Type"=> fun_get_field_type($usersDataFields,"password") ,
										"Filed_Label"=> "pro_pass_new",
										"Operater_Id"=> $opreaterId,
										"Operater_Type"=> getOperaterType($opreaterId) ,
										"IP"=> $ip,
										"MAC_Address"=> $_REQUEST['macaddrs'],
										"URL"=> $URL,
										"Browser_Type"=> $browserName,
										"OS"=> $os,
										"Machine_Name"=> $machineName,
										"Category"=> "admin",
										"Category_Desc"=> "providers",
										"Action" => "update",
										"Old_Value"=> ($password) ? trim($password) : "",
										"New_Value"=>  hashPassword($pro_pass_new,'forcehash')
									);

					$table = array("users");
					$error = array($userError);
					$mergedArray = array();
					if(count($table) == count($error)){
						for($a=0; $a < count($table); $a++){
							$mergedArray[] = array(
												"Table_Name"=> trim($table[$a]),
												"Error"=> trim($error[$a])
											  );
						}
					}
					auditTrail($arrAuditTrail,$mergedArray,$pro_id,0,0);
				}

				return $jsAlertNo = 13;
			}else{
				return $jsAlertNo = 15;
			}
		}
	}

	public function direct_cred_modal()
	{
		$jsAlertNo = 0;
		$arrUserRecords = $this->get_user_details($_REQUEST["myid"]);
		$prov_direct_access = '';

		//Allow direct access to
		if(empty($arrUserRecords[0]['direct_access']) == false){
			$prov_direct_access = explode(',',$arrUserRecords[0]['direct_access']);
		}
		$prov_opt_str = '';
		$all_prov_arr = $this->get_user_details_all($_REQUEST["myid"]);
		foreach($all_prov_arr as $obj){
			$prov_name = core_name_format($obj['pro_lname'], $obj['pro_fname'], $obj['pro_mname']);
			$selected = '';
			if(is_array($prov_direct_access) && in_array($obj['id'],$prov_direct_access)){
				$selected = 'selected';
			}
			$prov_opt_str .= '<option value="'.$obj['id'].'" '.$selected.'>'.$prov_name.'</opiton>';
		}

		$html  = '';
		$html .= '<div id="direct_credentials_div" class="modal" role="dialog" style="z-index:1051;" >';
		$html .= '<div class="modal-dialog modal-sm">';
		// Modal content
		$html .= '<div class="modal-content">';
		$html .= '<form name="direct_cred_form" method="post" id="direct_cred_form" class="margin_0" action="" autocomplete="off">';
		// Modal Header
		$html .= '<div class="modal-header bg-primary">';
		$html .= '<button type="button" class="close" data-dismiss="modal">×</button>';
		$html .= '<h4 class="modal-title" id="modal_title">Direct Messaging Credentials</h4>';
		$html .= '</div>';
		// Modal Body
		$html .= '<div class="modal-body" style="max-height:450px; overflow:hidden; overflow-y:auto;">';
		$html .= '<input type="hidden" name="hid_save" value="save">';
		$html .= '<input type="hidden" name="pro_id" value="'.(isset($_REQUEST["myid"]) ? $_REQUEST["myid"] : "").'">';

		$html .= '<div class="row">';
		$html .= '<label for="email">Email</label><br>';
		$html .= '<input type="text" name="email" id="email" class="form-control" value="'.$arrUserRecords[0]["d_email"].'" maxlength="150" />';
		$html .= '</div>';

		$html .= '<div class="row">';
		$html .= '<label for="email_password">Password</label><br>';
		$html .= '<input type="password" name="email_password" id="email_password" class="form-control" value="'.$arrUserRecords[0]["d_pass"].'" maxlength="250" />';
		$html .= '</div>';

		$html .= '<div class="row">';
		$html .= '<label for="updox_user_id">Updox User Id</label><br>';
		$html .= '<input type="text" name="updox_user_id" id="updox_user_id" class="form-control" value="'.$arrUserRecords[0]["updox_user_id"].'" maxlength="150" />';
		$html .= '</div>';

		$html .= '<div class="row">';
			$html .= '<label for="direct_access_list">Allow Direct access:</label><br />';
			$html .= '<div style="position:fixed; z-index:1; width:100%;">
							<select name="direct_access_list[]" id="direct_access_list" class="selectpicker" data-title="Select" data-size="6" multiple>'.$prov_opt_str.'</select>
						</div>';
		$html .= '</div>';

		$html .= '<div class="clearfix">&nbsp;</div>';
		$html .= '</div>';
		// Modal Footer
    $html .= '<div class="modal-footer">';
		$html .= '</div>';


		$html .= '</div>';
		//$html .= '<div id="divResetPassProAlertMsg" style="display:none;"></div>';
		$html .= '</div>';

		return $html;
	}

	public function direct_cred_save()
	{
		$return = '';
		if(isset($_REQUEST["hid_save"]) && $_REQUEST["hid_save"] == "save")
		{
			$pro_id = $_REQUEST["pro_id"];
			$email	= trim($_REQUEST["email"]);
			$email_password = trim($_POST["email_password"]);
			$updox_user_id = trim($_POST["updox_user_id"]);

			$direct_access_list = '';
			if( isset($_POST['direct_access_list']) && count($_POST['direct_access_list']) > 0 )
				$direct_access_list = implode(',', $_POST['direct_access_list']);

			$qry = "UPDATE users SET email = '".$email."', email_password = '".$email_password."', direct_access = '".$direct_access_list."', updox_user_id = '".$updox_user_id."' WHERE id = '".$pro_id."'";
			$sql = imw_query($qry);
			if(imw_affected_rows() > 0)
			{
				$return = "Password Saved Successfully."	;
			}
		}
		return $return;
	}

	public function zeiss_cred_modal()
	{
		$jsAlertNo = 0;
		$arrUserRecords = $this->get_user_details($_REQUEST["myid"]);

		$html  = '';
		$html .= '<div id="zeiss_credentials_div" class="modal" role="dialog" style="z-index:1051;" >';
		$html .= '<div class="modal-dialog modal-sm">';
		// Modal content
		$html .= '<div class="modal-content">';
		$html .= '<form name="zeiss_cred_form" method="post" id="zeiss_cred_form" class="margin_0" action="" autocomplete="off">';
		// Modal Header
		$html .= '<div class="modal-header bg-primary">';
		$html .= '<button type="button" class="close" data-dismiss="modal">×</button>';
		$html .= '<h4 class="modal-title" id="modal_title">Zeiss Forum Credentials</h4>';
		$html .= '</div>';
		// Modal Body
		$html .= '<div class="modal-body" style="max-height:450px; overflow:hidden; overflow-y:auto;">';
		$html .= '<input type="hidden" name="hid_save" value="save">';
		$html .= '<input type="hidden" name="pro_id" value="'.(isset($_REQUEST["myid"]) ? $_REQUEST["myid"] : "").'">';

		$html .= '<div class="row">';
		$html .= '<label for="zeiss_username">Username</label><br>';
		$html .= '<input type="text" name="zeiss_username" id="zeiss_username" value="'.$arrUserRecords[0]["zeiss_username"].'" class="form-control" maxlength="50" />';
		$html .= '</div>';

		$html .= '<div class="row">';
		$html .= '<label for="zeiss_password">Password</label><br>';
		$html .= '<input type="password" name="zeiss_password" id="zeiss_password" value="'.$arrUserRecords[0]["zeiss_password"].'" class="form-control" maxlength="50" />';
		$html .= '</div>';

		$html .= '<div class="clearfix">&nbsp;</div>';

		// Modal Footer
    $html .= '<div class="modal-footer pd5">';
		$html .= '<button type="button" onclick="zeissCredSave();" id="zeiss_cdr_submit" name="zeiss_cdr_submit" class="btn btn-success">Save</button>';
		$html .= '<button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>';
		$html .= '</div>';

		$html .= '</div>';
		$html .= '</div>';
		$html .= '</div>';

		return $html;
	}

	public function zeiss_cred_save()
	{
		$return = '';
		if(isset($_REQUEST["hid_save"]) && $_REQUEST["hid_save"] == "save")
		{
			$pro_id = $_REQUEST["pro_id"];
			$zeiss_username	= trim($_REQUEST["zeiss_username"]);
			$zeiss_password = trim($_POST["zeiss_password"]);

			$qry = "UPDATE users SET zeiss_username = '".$zeiss_username."', zeiss_password = '".$zeiss_password."' WHERE id = '".$pro_id."' ";
			$sql = imw_query($qry);
			if(imw_affected_rows() > 0)
			{
				$return = "Zeiss Credentials Saved Successfully."	;
			}
		}
		return $return;
	}

	public function providers_form(){
		global $cls_common;

		//Audit
		//get audit policy
		$arrAudit = $this->get_audit_policy(13);
		$intAuditOnOff = $arrAudit[0]['policy_status'];

		//get user types
		$arrProviderTypes = $this->get_provider_types();

		//get user groups
		$arrUserGroup = $this->get_user_groups();

		//get name titles
		$arrNameTitles = $this->get_name_titles();

		//get facilities
		$arrFacilities = $this->get_facilities();

		//get groups
		$arrGroups = $this->get_groups();

		include_once($GLOBALS['srcdir']."/classes/admin/GroupPrevileges.php");		
		$oGroupPrevileges = new GroupPrevileges();

		//Get privileges array
		$menu_array = $oGroupPrevileges->fetchMenuArray();

		$arrUserDetails = false;
		$arrUserAccessPrivileges = false;
		$arrTempUserGroups = false;
		$arrUserGroups = false;
		$arrAuditUserDetails = array("erx_user_name"=>"", "erx_password"=>"");

		$sp_Clinical = 0;
		$chk_Clinical = "";
		$chk_cl_work_view = 'checked="checked"';
		$chk_cl_tests = 'checked="checked"';
		$chk_cl_medical_hx = 'checked="checked"';

		$chk_Front_Desk = "";

		$chk_Billing = "";

		$chk_Accounting = "";
		$chk_Acc_all = 'checked="checked"';
		$chk_Acc_vonly = "";

		$chk_Security = "";
		$chk_api_access = "";

		$sp_Reports = 0;
		$chk_Reports = "";
		$chk_sc_scheduler = "";
		$chk_sc_house_calls = "";
		$chk_sc_recall_fulfillment = "";
		$chk_bi_front_desk = "";
		$chk_bi_ledger = "";
		$chk_bi_prod_payroll = "";
		$chk_bi_ar = "";
		$chk_bi_statements = "";
		$chk_bi_end_of_day = "";
		$chk_cl_clinical = "";
		$chk_cl_visits = "";
		$chk_cl_ccd = "";
		$chk_cl_order_set = "";

		$sp_View_Only = 0;
		$chk_View_Only = "";
		$chk_vo_pt_info = 'checked="checked"';
		$chk_vo_acc = 'checked="checked"';
		$chk_vo_charges = 'checked="checked"';
		$chk_vo_payment = 'checked="checked"';
		$chk_sch_lock_block = 'checked="checked"';
		$chk_sch_telemedicine = '';

		$chk_Sch_Override = "";

		$chk_pt_Override = "";

		$chk_admin = "";

		$chk_break_glass = "";
		$chk_vo_clinical = "";
		$chk_Optical = "";

		$chk_iOLink = "";

		$pro_id = "";
		$_REQUEST['pro_id']=xss_rem($_REQUEST['pro_id'],1);
		if($_REQUEST['pro_id']==0){
			$_REQUEST['pro_id']="";
		}
		$collect_refraction_y = "";
		$collect_refraction_n = "checked";
		$refraction_disabled = "";
		$refraction_hidden = "no";
		$chk_no_reports = '';
		$legal_agreement_status = "<span style=\"font-weight:bold;color:red;\">&#9746;</span>";
		$hippa_notice_status = "<span style=\"font-weight:bold;color:red;\">&#9746;</span>";

		if(isset($_REQUEST['pro_id']) && !empty($_REQUEST['pro_id'])){

			$pro_id = $_REQUEST['pro_id'];

			//get user details
			$arrTMPUserDetails = $this->get_user_details($pro_id);
			$arrUserDetails = $arrTMPUserDetails[0];
			$arrAuditUserDetails = $arrUserDetails;

			if($arrUserDetails["collect_refraction"] == 1){
				$collect_refraction_y = "checked";
				$collect_refraction_n = "";
			}else if($arrUserDetails["collect_refraction"] == 0){
				$collect_refraction_y = "";
				$collect_refraction_n = "checked";
			}

			if($arrUserDetails["SLA"] == 1){
				$legal_agreement_status = "<span style=\"font-weight:bold;color:green;\">&#9745;</span>";
				if($arrUserDetails["sla_date"]!='0000-00-00 00:00:00'){
					$sla_date_chk=strtotime($arrUserDetails["sla_date"]);
					$sla_date_final=date('m-d-y h:i A',$sla_date_chk);
				}
			}else{
				$sla_date_final="Not Reviewed";
			}

			if($arrUserDetails["HIPPA_STATUS"] == "yes"){
				$hippa_notice_status = "<span style=\"font-weight:bold;color:green;\">&#9745;</span>";
				if($arrUserDetails["hippa_date"]!='0000-00-00 00:00:00'){
					$hippa_date_chk=strtotime($arrUserDetails["hippa_date"]);
					$hippa_date_final=date('m-d-y h:i A',$hippa_date_chk);
				}
			}else{
				$hippa_date_final="Not Reviewed";
			}

			//get user privileges
			//$arrUserAccessPrivileges = $this->get_user_access_privileges($pro_id);
			//echo $arrUserDetails["pre_phy"];
			$arrUserAccessPrivileges = unserialize(html_entity_decode(trim($arrUserDetails["pre_phy"])));

			$sp_Clinical = 0;
			$sp_View_Only = 0;
			$sp_Reports = 0;

			$chk_cl_work_view = "";
			if(isset($arrUserAccessPrivileges["priv_cl_work_view"]) && $arrUserAccessPrivileges["priv_cl_work_view"] == 1){
				$chk_cl_work_view = "checked=\"checked\"";
				$sp_Clinical++;
			}
			$chk_cl_tests = "";
			if(isset($arrUserAccessPrivileges["priv_cl_tests"]) && $arrUserAccessPrivileges["priv_cl_tests"] == 1){
				$chk_cl_tests = "checked=\"checked\"";
				$sp_Clinical++;
			}
			$chk_cl_medical_hx = "";
			if(isset($arrUserAccessPrivileges["priv_cl_medical_hx"]) && $arrUserAccessPrivileges["priv_cl_medical_hx"] == 1){
				$chk_cl_medical_hx = "checked=\"checked\"";
				$sp_Clinical++;
			}
			$check_priv_chart_finalize = "";
			if(isset($arrUserAccessPrivileges["priv_chart_finalize"]) && $arrUserAccessPrivileges["priv_chart_finalize"] == 1){
				$check_priv_chart_finalize = "checked=\"checked\"";
				$sp_Clinical++;
			}
			$chk_Clinical = "checked=\"checked\"";
			if($chk_cl_work_view == "" && $chk_cl_tests == "" && $chk_cl_medical_hx == ""){
				$chk_Clinical = "";
			}

			$chk_priv_scheduler_demo = "";
			if(isset($arrUserAccessPrivileges["priv_scheduler_demo"]) && $arrUserAccessPrivileges["priv_scheduler_demo"] == 1){
				$chk_priv_scheduler_demo = "checked=\"checked\"";
			}
			$chk_Front_Desk = "";
			if(isset($arrUserAccessPrivileges["priv_Front_Desk"]) && $arrUserAccessPrivileges["priv_Front_Desk"] == 1){
				$chk_Front_Desk = "checked=\"checked\"";
			}
			$chk_Billing = "";
			if(isset($arrUserAccessPrivileges["priv_Billing"]) && $arrUserAccessPrivileges["priv_Billing"] == 1){
				$chk_Billing = "checked=\"checked\"";
			}
			$chk_Accounting = "";
			if(isset($arrUserAccessPrivileges["priv_Accounting"]) && $arrUserAccessPrivileges["priv_Accounting"] == 1){
				$chk_Accounting = "checked=\"checked\"";
			}
			$chk_Acc_all = "checked=\"checked\"";
			if(isset($arrUserAccessPrivileges["priv_Acc_all"]) && $arrUserAccessPrivileges["priv_Acc_all"] == 1){
				$chk_Acc_all = "checked=\"checked\"";
			}
			$chk_Acc_vonly = "";
			if(isset($arrUserAccessPrivileges["priv_Acc_vonly"]) && $arrUserAccessPrivileges["priv_Acc_vonly"] == 1){
				$chk_Acc_vonly = "checked=\"checked\"";
				$chk_Acc_all = "";
			}
			$chk_Security = "";
			if(isset($arrUserAccessPrivileges["priv_Security"]) && $arrUserAccessPrivileges["priv_Security"] == 1){
				$chk_Security = "checked=\"checked\"";
			}

			$chk_cnfdntl_txt = "";
			if(isset($arrUserAccessPrivileges["priv_cnfdntl_txt"]) && $arrUserAccessPrivileges["priv_cnfdntl_txt"] == 1){
				$chk_cnfdntl_txt = "checked=\"checked\"";
			}

			$chk_api_access = "";
			if(isset($arrUserAccessPrivileges["priv_api_access"]) && $arrUserAccessPrivileges["priv_api_access"] == 1){
				$chk_api_access = "checked=\"checked\"";
			}
			$chk_priv_report_api_access = "";
			if(isset($arrUserAccessPrivileges["priv_report_api_access"]) && $arrUserAccessPrivileges["priv_report_api_access"] == 1){
				$chk_priv_report_api_access = "checked=\"checked\"";
			}

			$chk_priv_grp_prvlgs = (isset($arrUserAccessPrivileges["priv_grp_prvlgs"]) && $arrUserAccessPrivileges["priv_grp_prvlgs"] == 1) ? "checked=\"checked\"" : "" ;
			$chk_priv_chng_prvlgs = (isset($arrUserAccessPrivileges["priv_chng_prvlgs"]) && $arrUserAccessPrivileges["priv_chng_prvlgs"] == 1) ? "checked=\"checked\"" : "" ;
			$chk_priv_rules_mngr = (isset($arrUserAccessPrivileges["priv_rules_mngr"]) && $arrUserAccessPrivileges["priv_rules_mngr"] == 1) ? "checked=\"checked\"" : "" ;

			$chk_priv_Reports_manager="";
			if(isset($arrUserAccessPrivileges["priv_Reports_manager"]) && $arrUserAccessPrivileges["priv_Reports_manager"] == 1){
				$chk_priv_Reports_manager = "checked=\"checked\"";
			}

			$chk_priv_sc_daily="";
			if(isset($arrUserAccessPrivileges["priv_sc_daily"]) && $arrUserAccessPrivileges["priv_sc_daily"] == 1){
				$chk_priv_sc_daily = "checked=\"checked\"";
			}

			$chk_priv_acct_receivable="";
			if(isset($arrUserAccessPrivileges["priv_acct_receivable"]) && $arrUserAccessPrivileges["priv_acct_receivable"] == 1){
				$chk_priv_acct_receivable = "checked=\"checked\"";
			}

			$chk_priv_bi_analytics="";
			if(isset($arrUserAccessPrivileges["priv_bi_analytics"]) && $arrUserAccessPrivileges["priv_bi_analytics"] == 1){
				$chk_priv_bi_analytics = "checked=\"checked\"";
			}

			$chk_sc_scheduler = "";
			if(isset($arrUserAccessPrivileges["priv_sc_scheduler"]) && $arrUserAccessPrivileges["priv_sc_scheduler"] == 1){
				$chk_sc_scheduler = "checked=\"checked\"";
				$sp_Reports++;
			}
			$chk_sc_house_calls = "";
			if(isset($arrUserAccessPrivileges["priv_sc_house_calls"]) && $arrUserAccessPrivileges["priv_sc_house_calls"] == 1){
				$chk_sc_house_calls = "checked=\"checked\"";
				$sp_Reports++;
			}
			$chk_priv_report_State = "";
			if(isset($arrUserAccessPrivileges["priv_report_State"]) && $arrUserAccessPrivileges["priv_report_State"] == 1){
				$chk_priv_report_State = "checked=\"checked\"";
			}
			$chk_priv_report_Clinical = "";
			if(isset($arrUserAccessPrivileges["priv_report_Clinical"]) && $arrUserAccessPrivileges["priv_report_Clinical"] == 1){
				$chk_priv_report_Clinical = "checked=\"checked\"";
			}
			$chk_priv_report_Rules = "";
			if(isset($arrUserAccessPrivileges["priv_report_Rules"]) && $arrUserAccessPrivileges["priv_report_Rules"] == 1){
				$chk_priv_report_Rules = "checked=\"checked\"";
			}
			$chk_priv_report_iPortal = "";
			if(isset($arrUserAccessPrivileges["priv_report_iPortal"]) && $arrUserAccessPrivileges["priv_report_iPortal"] == 1){
				$chk_priv_report_iPortal = "checked=\"checked\"";
			}
			$chk_priv_billing_fun = "";
			if(isset($arrUserAccessPrivileges["priv_billing_fun"]) && $arrUserAccessPrivileges["priv_billing_fun"] == 1){
				$chk_priv_billing_fun = "checked=\"checked\"";

			}

			$chk_sc_recall_fulfillment = "";
			if(isset($arrUserAccessPrivileges["priv_sc_recall_fulfillment"]) && $arrUserAccessPrivileges["priv_sc_recall_fulfillment"] == 1){
				$chk_sc_recall_fulfillment = "checked=\"checked\"";
				$sp_Reports++;
			}

			$chk_bi_front_desk = "";
			if(isset($arrUserAccessPrivileges["priv_bi_front_desk"]) && $arrUserAccessPrivileges["priv_bi_front_desk"] == 1){
				$chk_bi_front_desk = "checked=\"checked\"";
				$sp_Reports++;
			}
			$chk_bi_ledger = "";
			if(isset($arrUserAccessPrivileges["priv_bi_ledger"]) && $arrUserAccessPrivileges["priv_bi_ledger"] == 1){
				$chk_bi_ledger = "checked=\"checked\"";
				$sp_Reports++;
			}
			$chk_bi_prod_payroll = "";
			if(isset($arrUserAccessPrivileges["priv_bi_prod_payroll"]) && $arrUserAccessPrivileges["priv_bi_prod_payroll"] == 1){
				$chk_bi_prod_payroll = "checked=\"checked\"";
				$sp_Reports++;
			}
			$chk_bi_ar = "";
			if(isset($arrUserAccessPrivileges["priv_bi_ar"]) && $arrUserAccessPrivileges["priv_bi_ar"] == 1){
				$chk_bi_ar = "checked=\"checked\"";
				$sp_Reports++;
			}
			$chk_bi_statements = "";
			if(isset($arrUserAccessPrivileges["priv_bi_statements"]) && $arrUserAccessPrivileges["priv_bi_statements"] == 1){
				$chk_bi_statements = "checked=\"checked\"";
				$sp_Reports++;
			}
			$chk_priv_bi_day_chrg_rept = "";
			if(isset($arrUserAccessPrivileges["priv_bi_day_chrg_rept"]) && $arrUserAccessPrivileges["priv_bi_day_chrg_rept"] == 1){
				$chk_priv_bi_day_chrg_rept = "checked=\"checked\"";
				$sp_Reports++;
			}
			$chk_priv_bi_edit_batch = "";
			if(isset($arrUserAccessPrivileges["priv_bi_edit_batch"]) && $arrUserAccessPrivileges["priv_bi_edit_batch"] == 1){
				$chk_priv_bi_edit_batch = "checked=\"checked\"";
				$sp_Reports++;
			}
			$chk_priv_financial_hx_cpt = "";
			if(isset($arrUserAccessPrivileges["priv_financial_hx_cpt"]) && $arrUserAccessPrivileges["priv_financial_hx_cpt"] == 1){
				$chk_priv_financial_hx_cpt = "checked=\"checked\"";
			}

			$chk_priv_purge_del_chart = "";
			if(isset($arrUserAccessPrivileges["priv_purge_del_chart"]) && $arrUserAccessPrivileges["priv_purge_del_chart"] == 1){
				$chk_priv_purge_del_chart = "checked=\"checked\"";
			}

			$chk_priv_record_release = "";
			if(isset($arrUserAccessPrivileges["priv_record_release"]) && $arrUserAccessPrivileges["priv_record_release"] == 1){
				$chk_priv_record_release = "checked=\"checked\"";
			}

			$chk_priv_proc_amend = "";
			if(isset($arrUserAccessPrivileges["priv_proc_amend"]) && $arrUserAccessPrivileges["priv_proc_amend"] == 1){
				$chk_priv_proc_amend = "checked=\"checked\"";
			}

			$chk_priv_def_wnl_stmt = "";
			if(isset($arrUserAccessPrivileges["priv_def_wnl_stmt"]) && $arrUserAccessPrivileges["priv_def_wnl_stmt"] == 1){
				$chk_priv_def_wnl_stmt = "checked=\"checked\"";
			}

			$chk_priv_edit_prescriptions = "";
			if(isset($arrUserAccessPrivileges["priv_edit_prescriptions"]) && $arrUserAccessPrivileges["priv_edit_prescriptions"] == 1){
				$chk_priv_edit_prescriptions = "checked=\"checked\"";
			}

			$chk_bi_end_of_day = "";
			if(isset($arrUserAccessPrivileges["priv_bi_end_of_day"]) && $arrUserAccessPrivileges["priv_bi_end_of_day"] == 1){
				$chk_bi_end_of_day = "checked=\"checked\"";
				$sp_Reports++;
			}

			$chk_cl_clinical = "";
			if(isset($arrUserAccessPrivileges["priv_cl_clinical"]) && $arrUserAccessPrivileges["priv_cl_clinical"] == 1){
				$chk_cl_clinical = "checked=\"checked\"";
				$sp_Reports++;
			}
			$chk_cl_visits = "";
			if(isset($arrUserAccessPrivileges["priv_cl_visits"]) && $arrUserAccessPrivileges["priv_cl_visits"] == 1){
				$chk_cl_visits = "checked=\"checked\"";
				$sp_Reports++;
			}
			$chk_cl_ccd = "";
			if(isset($arrUserAccessPrivileges["priv_cl_ccd"]) && $arrUserAccessPrivileges["priv_cl_ccd"] == 1){
				$chk_cl_ccd = "checked=\"checked\"";
				$sp_Reports++;
			}
			$chk_cl_order_set = "";
			if(isset($arrUserAccessPrivileges["priv_cl_order_set"]) && $arrUserAccessPrivileges["priv_cl_order_set"] == 1){
				$chk_cl_order_set = "checked=\"checked\"";
				$sp_Reports++;
			}
			$chk_no_reports = "";
			if(isset($arrUserAccessPrivileges["priv_no_reports"]) && $arrUserAccessPrivileges["priv_no_reports"] == 1){
				$chk_no_reports = "checked=\"checked\"";
			}
			$chk_Reports = "checked=\"checked\"";
			if($chk_sc_scheduler == "" && $chk_sc_house_calls == "" && $chk_sc_recall_fulfillment == "" && $chk_bi_front_desk == "" && $chk_bi_ledger == "" && $chk_bi_prod_payroll == "" && $chk_bi_ar == "" && $chk_bi_statements == "" && $chk_bi_end_of_day == "" && $chk_cl_clinical == "" && $chk_cl_visits == "" && $chk_cl_ccd == "" && $chk_cl_order_set == "" && $chk_no_reports == ""){
				$chk_Reports = "";
			}

			/*
			$chk_vo_clinical = "";
			if(isset($arrUserAccessPrivileges["priv_vo_clinical"]) && $arrUserAccessPrivileges["priv_vo_clinical"] == 1){
				$chk_vo_clinical = "checked=\"checked\"";
				$sp_View_Only++;
			}*/
			$chk_vo_pt_info = "";
			if(isset($arrUserAccessPrivileges["priv_vo_pt_info"]) && $arrUserAccessPrivileges["priv_vo_pt_info"] == 1){
				$chk_vo_pt_info = "checked=\"checked\"";
				$sp_View_Only++;
			}
			$chk_sch_lock_block = "";
			if(isset($arrUserAccessPrivileges["priv_sch_lock_block"]) && $arrUserAccessPrivileges["priv_sch_lock_block"] == 1){
				$chk_sch_lock_block = "checked=\"checked\"";
				$sp_View_Only++;
			}
			$chk_sch_telemedicine = "";
			if(isset($arrUserAccessPrivileges["priv_sch_telemedicine"]) && $arrUserAccessPrivileges["priv_sch_telemedicine"] == 1){
				$chk_sch_telemedicine = "checked=\"checked\"";
				$sp_View_Only++;
			}
			$chk_vo_acc = "";
			if(isset($arrUserAccessPrivileges["priv_vo_acc"]) && $arrUserAccessPrivileges["priv_vo_acc"] == 1){
				$chk_vo_acc = "checked=\"checked\"";
				$sp_View_Only++;
			}
			$chk_vo_charges = "";
			if(isset($arrUserAccessPrivileges["priv_vo_charges"]) && $arrUserAccessPrivileges["priv_vo_charges"] == 1){
				$chk_vo_charges = "checked=\"checked\"";
				$sp_View_Only++;
			}
			$chk_vo_payment = "";
			if(isset($arrUserAccessPrivileges["priv_vo_payment"]) && $arrUserAccessPrivileges["priv_vo_payment"] == 1){
				$chk_vo_payment = "checked=\"checked\"";
				$sp_View_Only++;
			}
			$chk_priv_del_charges_enc= "";
			if(isset($arrUserAccessPrivileges["priv_del_charges_enc"]) && $arrUserAccessPrivileges["priv_del_charges_enc"] == 1){
				$chk_priv_del_charges_enc = "checked=\"checked\"";
			}
			$chk_priv_del_payment = "";
			if(isset($arrUserAccessPrivileges["priv_del_payment"]) && $arrUserAccessPrivileges["priv_del_payment"] == 1){
				$chk_priv_del_payment = "checked=\"checked\"";
			}
/*
			$chk_View_Only = "checked=\"checked\"";
			if($chk_vo_clinical == "" && $chk_vo_pt_info == "" && $chk_vo_acc == "" && $chk_vo_charges == "" && $chk_vo_payment == ""){
				$chk_View_Only = "";
			}*/

			$chk_Sch_Override = "";
			if(isset($arrUserAccessPrivileges["priv_Sch_Override"]) && $arrUserAccessPrivileges["priv_Sch_Override"] == 1){
				$chk_Sch_Override = "checked=\"checked\"";
			}
			$chk_pt_Override = "";
			if(isset($arrUserAccessPrivileges["priv_pt_Override"]) && $arrUserAccessPrivileges["priv_pt_Override"] == 1){
				$chk_pt_Override = "checked=\"checked\"";
			}

			$chk_priv_ac_bill_manager = "";
			if(isset($arrUserAccessPrivileges["priv_ac_bill_manager"]) && $arrUserAccessPrivileges["priv_ac_bill_manager"] == 1){
				$chk_priv_ac_bill_manager = "checked=\"checked\"";
			}

			$chk_admin = "";
			$chk_disabled = "";
			if(isset($arrUserAccessPrivileges["priv_admin"]) && $arrUserAccessPrivileges["priv_admin"] == 1){
				$chk_admin = "checked=\"checked\"";
				//$chk_disabled= " disabled ";
			}
			$chk_admin_priv_all = "";
			$chk_disabled = "";
			if(isset($arrUserAccessPrivileges["priv_all_settings"]) && $arrUserAccessPrivileges["priv_all_settings"] == 1){
				$chk_admin_priv_all = "checked=\"checked\"";
				$chk_disabled= " disabled ";
			}
			$chk_priv_group = "";
			if(isset($arrUserAccessPrivileges["priv_group"]) && $arrUserAccessPrivileges["priv_group"] == 1){
				$chk_priv_group = "checked=\"checked\"";
			}
			$chk_priv_facility = "";
			if(isset($arrUserAccessPrivileges["priv_facility"]) && $arrUserAccessPrivileges["priv_facility"] == 1){
				$chk_priv_facility = "checked=\"checked\"";
			}
			$chk_priv_document = "";
			if(isset($arrUserAccessPrivileges["priv_document"]) && $arrUserAccessPrivileges["priv_document"] == 1){
				$chk_priv_document = "checked=\"checked\"";
			}
			$chk_priv_iols = "";
			if(isset($arrUserAccessPrivileges["priv_iols"]) && $arrUserAccessPrivileges["priv_iols"] == 1){
				$chk_priv_iols = "checked=\"checked\"";
			}
			$chk_priv_console = "";
			if(isset($arrUserAccessPrivileges["priv_console"]) && $arrUserAccessPrivileges["priv_console"] == 1){
				$chk_priv_console = "checked=\"checked\"";
			}

			$chk_priv_report_financials = "";
			if(isset($arrUserAccessPrivileges["priv_report_financials"]) && $arrUserAccessPrivileges["priv_report_financials"] == 1){
				$chk_priv_report_financials = "checked=\"checked\"";
			}
			$chk_priv_report_compliance = "";
			if(isset($arrUserAccessPrivileges["priv_report_compliance"]) && $arrUserAccessPrivileges["priv_report_compliance"] == 1){
				$chk_priv_report_compliance = "checked=\"checked\"";
			}
			$chk_priv_report_tests = "";
			if(isset($arrUserAccessPrivileges["priv_report_tests"]) && $arrUserAccessPrivileges["priv_report_tests"] == 1){
				$chk_priv_report_tests = "checked=\"checked\"";
			}
			$chk_priv_report_optical = "";
			if(isset($arrUserAccessPrivileges["priv_report_optical"]) && $arrUserAccessPrivileges["priv_report_optical"] == 1){
				$chk_priv_report_optical = "checked=\"checked\"";
			}
			$chk_priv_report_reminders = "";
			if(isset($arrUserAccessPrivileges["priv_report_reminders"]) && $arrUserAccessPrivileges["priv_report_reminders"] == 1){
				$chk_priv_report_reminders = "checked=\"checked\"";
			}
			$chk_priv_report_audit = "";
			if(isset($arrUserAccessPrivileges["priv_report_audit"]) && $arrUserAccessPrivileges["priv_report_audit"] == 1){
				$chk_priv_report_audit = "checked=\"checked\"";
			}
			$chk_priv_pt_instruction = "";
			if(isset($arrUserAccessPrivileges["priv_pt_instruction"]) && $arrUserAccessPrivileges["priv_pt_instruction"] == 1){
				$chk_priv_pt_instruction = "checked=\"checked\"";
			}
			$chk_priv_report_mur = "";
			if(isset($arrUserAccessPrivileges["priv_report_mur"]) && $arrUserAccessPrivileges["priv_report_mur"] == 1){
				$chk_priv_report_mur = "checked=\"checked\"";
			}
			$chk_priv_report_schduled = "";
			if(isset($arrUserAccessPrivileges["priv_report_schduled"]) && $arrUserAccessPrivileges["priv_report_schduled"] == 1){
				$chk_priv_report_schduled = "checked=\"checked\"";
			}

			$chk_priv_admin_billing = "";
			if(isset($arrUserAccessPrivileges["priv_admin_billing"]) && $arrUserAccessPrivileges["priv_admin_billing"] == 1){
				$chk_priv_admin_billing = "checked=\"checked\"";
			}

			$chk_priv_admin_clinical = "";
			if(isset($arrUserAccessPrivileges["priv_admin_clinical"]) && $arrUserAccessPrivileges["priv_admin_clinical"] == 1){
				$chk_priv_admin_clinical = "checked=\"checked\"";
			}
			$chk_iMedicMonitor = "";
			if(isset($arrUserAccessPrivileges["priv_iMedicMonitor"]) && $arrUserAccessPrivileges["priv_iMedicMonitor"] == 1){
				$chk_iMedicMonitor = "checked=\"checked\"";
			}
			$chk_priv_Admin_Optical = "";
			if(isset($arrUserAccessPrivileges["priv_Admin_Optical"]) && $arrUserAccessPrivileges["priv_Admin_Optical"] == 1){
				$chk_priv_Admin_Optical = "checked=\"checked\"";
			}
			$chk_priv_Admin_Reports = "";
			if(isset($arrUserAccessPrivileges["priv_Admin_Reports"]) && $arrUserAccessPrivileges["priv_Admin_Reports"] == 1){
				$chk_priv_Admin_Reports = "checked=\"checked\"";
			}

			$chk_priv_set_margin = "";
			if(isset($arrUserAccessPrivileges["priv_set_margin"]) && $arrUserAccessPrivileges["priv_set_margin"] == 1){
				$chk_priv_set_margin = "checked=\"checked\"";
			}
			$chk_priv_erx_preferences = "";
			if(isset($arrUserAccessPrivileges["priv_erx_preferences"]) && $arrUserAccessPrivileges["priv_erx_preferences"] == 1){
				$chk_priv_erx_preferences = "checked=\"checked\"";
			}
			$chk_priv_room_assign = "";
			if(isset($arrUserAccessPrivileges["priv_room_assign"]) && $arrUserAccessPrivileges["priv_room_assign"] == 1){
				$chk_priv_room_assign = "checked=\"checked\"";
			}
			$chk_priv_chart_notes = "";
			if(isset($arrUserAccessPrivileges["priv_chart_notes"]) && $arrUserAccessPrivileges["priv_chart_notes"] == 1){
				$chk_priv_chart_notes = "checked=\"checked\"";
			}
			$chk_priv_admin_scp = "";
			if(isset($arrUserAccessPrivileges["priv_admin_scp"]) && $arrUserAccessPrivileges["priv_admin_scp"] == 1){
				$chk_priv_admin_scp = "checked=\"checked\"";
			}
			$chk_priv_vs = "";
			if(isset($arrUserAccessPrivileges["priv_vs"]) && $arrUserAccessPrivileges["priv_vs"] == 1){
				$chk_priv_vs = "checked=\"checked\"";
			}
			$chk_priv_immunization = "";
			if(isset($arrUserAccessPrivileges["priv_immunization"]) && $arrUserAccessPrivileges["priv_immunization"] == 1){
				$chk_priv_immunization = "checked=\"checked\"";
			}
			$chk_priv_manage_fields = "";
			if(isset($arrUserAccessPrivileges["priv_manage_fields"]) && $arrUserAccessPrivileges["priv_manage_fields"] == 1){
				$chk_priv_manage_fields = "checked=\"checked\"";
			}
			$chk_priv_orders = "";
			if(isset($arrUserAccessPrivileges["priv_orders"]) && $arrUserAccessPrivileges["priv_orders"] == 1){
				$chk_priv_orders = "checked=\"checked\"";
			}
			$chk_priv_iportal = "";
			if(isset($arrUserAccessPrivileges["priv_iportal"]) && $arrUserAccessPrivileges["priv_iportal"] == 1){
				$chk_priv_iportal = "checked=\"checked\"";
			}


			$chk_priv_provider_management="";
			if(isset($arrUserAccessPrivileges["priv_provider_management"]) && $arrUserAccessPrivileges["priv_provider_management"] == 1){
				$chk_priv_provider_management = "checked=\"checked\"";
			}

			$chk_break_glass = "";
			if(isset($arrUserAccessPrivileges["priv_break_glass"]) && $arrUserAccessPrivileges["priv_break_glass"] == 1){
				$chk_break_glass = "checked=\"checked\"";
			}
			$chk_vo_clinical = "";
			if(isset($arrUserAccessPrivileges["priv_vo_clinical"]) && $arrUserAccessPrivileges["priv_vo_clinical"] == 1){
				$chk_vo_clinical = "checked=\"checked\"";
			}

			$chk_edit_financials = "";
			if(isset($arrUserAccessPrivileges["priv_edit_financials"]) && $arrUserAccessPrivileges["priv_edit_financials"] == 1){
				$chk_edit_financials = "checked=\"checked\"";
			}

			$chk_priv_ref_physician="";
			if(isset($arrUserAccessPrivileges["priv_ref_physician"]) && $arrUserAccessPrivileges["priv_ref_physician"] == 1){
				$chk_priv_ref_physician = "checked=\"checked\"";
			}
			$chk_priv_admin_billing="";
			if(isset($arrUserAccessPrivileges["priv_admin_billing"]) && $arrUserAccessPrivileges["priv_admin_billing"] == 1){
				$chk_priv_admin_billing = "checked=\"checked\"";
			}

			$chk_priv_admin_scheduler="";
			if(isset($arrUserAccessPrivileges["priv_admin_scheduler"]) && $arrUserAccessPrivileges["priv_admin_scheduler"] == 1){
				$chk_priv_admin_scheduler = "checked=\"checked\"";
			}
			$chk_priv_ins_management="";
			if(isset($arrUserAccessPrivileges["priv_ins_management"]) && $arrUserAccessPrivileges["priv_ins_management"] == 1){
				$chk_priv_ins_management = "checked=\"checked\"";
			}

			$chk_priv_pt_fdsk="";
			if(isset($arrUserAccessPrivileges["priv_pt_fdsk"]) && $arrUserAccessPrivileges["priv_pt_fdsk"] == 1){
				$chk_priv_pt_fdsk = "checked=\"checked\"";
			}

			$chk_priv_pt_clinical="";
			if(isset($arrUserAccessPrivileges["priv_pt_clinical"]) && $arrUserAccessPrivileges["priv_pt_clinical"] == 1){
				$chk_priv_pt_clinical = "checked=\"checked\"";
			}

			$chk_Optical = "";
			if(isset($arrUserAccessPrivileges["priv_Optical"]) && $arrUserAccessPrivileges["priv_Optical"] == 1){
				$chk_Optical = "checked=\"checked\"";
			}
			$chk_Optical_POS = "";
			if(isset($arrUserAccessPrivileges["priv_Optical_POS"]) && $arrUserAccessPrivileges["priv_Optical_POS"] == 1){
				$chk_Optical_POS = "checked=\"checked\"";
			}
			$chk_Optical_Inventory = "";
			if(isset($arrUserAccessPrivileges["priv_Optical_Inventory"]) && $arrUserAccessPrivileges["priv_Optical_Inventory"] == 1){
				$chk_Optical_Inventory = "checked=\"checked\"";
			}
			$chk_Optical_Admin = "";
			if(isset($arrUserAccessPrivileges["priv_Optical_Admin"]) && $arrUserAccessPrivileges["priv_Optical_Admin"] == 1){
				$chk_Optical_Admin = "checked=\"checked\"";
			}
			$chk_Optical_Reports = "";
			if(isset($arrUserAccessPrivileges["priv_Optical_Reports"]) && $arrUserAccessPrivileges["priv_Optical_Reports"] == 1){
				$chk_Optical_Reports = "checked=\"checked\"";
			}

			$chk_iOLink = "";
			if(isset($arrUserAccessPrivileges["priv_iOLink"]) && $arrUserAccessPrivileges["priv_iOLink"] == 1){
				$chk_iOLink = "checked=\"checked\"";
			}
			$chk_pt_coordinator="";
			if(isset($arrUserDetails["pt_coordinator"]) && $arrUserDetails["pt_coordinator"] == 1){
				$chk_pt_coordinator = "checked=\"checked\"";
			}
			$chk_cdc="";
			if($arrUserAccessPrivileges['priv_cdc']==1){
				$chk_cdc="checked=\"checked\"";
			}
			$chk_erx="";
			if($arrUserAccessPrivileges['priv_erx']==1){
				$chk_erx="checked=\"checked\"";
			}
			$chk_acchx="";
			if($arrUserAccessPrivileges['priv_acchx']==1){
				$chk_acchx="checked=\"checked\"";
			}

			$chk_priv_report_payments="";
			if(isset($arrUserAccessPrivileges["priv_report_payments"]) && $arrUserAccessPrivileges["priv_report_payments"] == 1){
				$chk_priv_report_payments = "checked=\"checked\"";
			}

			$chk_priv_report_copay_rocan="";
			if(isset($arrUserAccessPrivileges["priv_report_copay_rocan"]) && $arrUserAccessPrivileges["priv_report_copay_rocan"] == 1){
				$chk_priv_report_copay_rocan = "checked=\"checked\"";
			}

			$chk_priv_un_superbills="";
			if(isset($arrUserAccessPrivileges["priv_un_superbills"]) && $arrUserAccessPrivileges["priv_un_superbills"] == 1){
				$chk_priv_un_superbills = "checked=\"checked\"";
			}

			$chk_priv_un_encounters="";
			if(isset($arrUserAccessPrivileges["priv_un_encounters"]) && $arrUserAccessPrivileges["priv_un_encounters"] == 1){
				$chk_priv_un_encounters = "checked=\"checked\"";
			}

			$chk_priv_un_payments="";
			if(isset($arrUserAccessPrivileges["priv_un_payments"]) && $arrUserAccessPrivileges["priv_un_payments"] == 1){
				$chk_priv_un_payments = "checked=\"checked\"";
			}

			$chk_priv_report_adjustment="";
			if(isset($arrUserAccessPrivileges["priv_report_adjustment"]) && $arrUserAccessPrivileges["priv_report_adjustment"] == 1){
				$chk_priv_report_adjustment = "checked=\"checked\"";
			}

			$chk_priv_report_refund="";
			if(isset($arrUserAccessPrivileges["priv_report_refund"]) && $arrUserAccessPrivileges["priv_report_refund"] == 1){
				$chk_priv_report_refund = "checked=\"checked\"";
			}

			$chk_priv_daily_balance="";
			if(isset($arrUserAccessPrivileges["priv_daily_balance"]) && $arrUserAccessPrivileges["priv_daily_balance"] == 1){
				$chk_priv_daily_balance = "checked=\"checked\"";
			}
			$chk_priv_fd_collection="";
			if(isset($arrUserAccessPrivileges["priv_fd_collection"]) && $arrUserAccessPrivileges["priv_fd_collection"] == 1){
				$chk_priv_fd_collection = "checked=\"checked\"";
			}

			$chk_priv_report_practice_analytics="";
			if(isset($arrUserAccessPrivileges["priv_report_practice_analytics"]) && $arrUserAccessPrivileges["priv_report_practice_analytics"] == 1){
				$chk_priv_report_practice_analytics = "checked=\"checked\"";
			}

			$chk_priv_cpt_analysis="";
			if(isset($arrUserAccessPrivileges["priv_cpt_analysis"]) && $arrUserAccessPrivileges["priv_cpt_analysis"] == 1){
				$chk_priv_cpt_analysis = "checked=\"checked\"";
			}
			$chk_priv_report_refund="";
			if(isset($arrUserAccessPrivileges["priv_report_refund"]) && $arrUserAccessPrivileges["priv_report_refund"] == 1){
				$chk_priv_report_refund = "checked=\"checked\"";
			}

			$chk_priv_report_yearly="";
			if(isset($arrUserAccessPrivileges["priv_report_yearly"]) && $arrUserAccessPrivileges["priv_report_yearly"] == 1){
				$chk_priv_report_yearly = "checked=\"checked\"";
			}

			$chk_priv_report_revenue="";
			if(isset($arrUserAccessPrivileges["priv_report_refund"]) && $arrUserAccessPrivileges["priv_report_refund"] == 1){
				$chk_priv_report_revenue = "checked=\"checked\"";
			}

			$chk_priv_provider_mon="";
			if(isset($arrUserAccessPrivileges["priv_provider_mon"]) && $arrUserAccessPrivileges["priv_provider_mon"] == 1){
				$chk_priv_provider_mon = "checked=\"checked\"";
			}

			$chk_priv_ref_phy_monthly="";
			if(isset($arrUserAccessPrivileges["priv_ref_phy_monthly"]) && $arrUserAccessPrivileges["priv_ref_phy_monthly"] == 1){
				$chk_priv_ref_phy_monthly = "checked=\"checked\"";
			}

			$chk_priv_facility_monthly="";
			if(isset($arrUserAccessPrivileges["priv_facility_monthly"]) && $arrUserAccessPrivileges["priv_facility_monthly"] == 1){
				$chk_priv_facility_monthly = "checked=\"checked\"";
			}

			$chk_priv_report_ref_phy="";
			if(isset($arrUserAccessPrivileges["priv_report_ref_phy"]) && $arrUserAccessPrivileges["priv_report_ref_phy"] == 1){
				$chk_priv_report_ref_phy = "checked=\"checked\"";
			}

			$chk_priv_credit_analysis="";
			if(isset($arrUserAccessPrivileges["priv_credit_analysis"]) && $arrUserAccessPrivileges["priv_credit_analysis"] == 1){
				$chk_priv_credit_analysis = "checked=\"checked\"";
			}

			$chk_priv_report_patient="";
			if(isset($arrUserAccessPrivileges["priv_report_patient"]) && $arrUserAccessPrivileges["priv_report_patient"] == 1){
				$chk_priv_report_patient = "checked=\"checked\"";
			}

			$chk_priv_report_ins_cases="";
			if(isset($arrUserAccessPrivileges["priv_report_ins_cases"]) && $arrUserAccessPrivileges["priv_report_ins_cases"] == 1){
				$chk_priv_report_ins_cases = "checked=\"checked\"";
			}

			$chk_priv_report_eid_status="";
			if(isset($arrUserAccessPrivileges["priv_report_eid_status"]) && $arrUserAccessPrivileges["priv_report_eid_status"] == 1){
				$chk_priv_report_eid_status = "checked=\"checked\"";
			}

			$chk_priv_allowable_verify="";
			if(isset($arrUserAccessPrivileges["priv_allowable_verify"]) && $arrUserAccessPrivileges["priv_allowable_verify"] == 1){
				$chk_priv_allowable_verify = "checked=\"checked\"";
			}

			$chk_priv_vip_deferred="";
			if(isset($arrUserAccessPrivileges["priv_vip_deferred"]) && $arrUserAccessPrivileges["priv_vip_deferred"] == 1){
				$chk_priv_vip_deferred = "checked=\"checked\"";
			}

			$chk_priv_provider_rvu="";
			if(isset($arrUserAccessPrivileges["priv_provider_rvu"]) && $arrUserAccessPrivileges["priv_provider_rvu"] == 1){
				$chk_priv_provider_rvu = "checked=\"checked\"";
			}

			$chk_priv_sx_payment="";
			if(isset($arrUserAccessPrivileges["priv_sx_payment"]) && $arrUserAccessPrivileges["priv_sx_payment"] == 1){
				$chk_priv_sx_payment = "checked=\"checked\"";
			}

			$chk_priv_report_ins_cases="";
			if(isset($arrUserAccessPrivileges["priv_report_ins_cases"]) && $arrUserAccessPrivileges["priv_report_ins_cases"] == 1){
				$chk_priv_report_ins_cases = "checked=\"checked\"";
			}

			$chk_priv_net_gross="";
			if(isset($arrUserAccessPrivileges["priv_net_gross"]) && $arrUserAccessPrivileges["priv_net_gross"] == 1){
				$chk_priv_net_gross = "checked=\"checked\"";
			}

			$chk_priv_report_ins_cases="";
			if(isset($arrUserAccessPrivileges["priv_report_ins_cases"]) && $arrUserAccessPrivileges["priv_report_ins_cases"] == 1){
				$chk_priv_report_ins_cases = "checked=\"checked\"";
			}

			$chk_priv_ar_reports="";
			if(isset($arrUserAccessPrivileges["priv_ar_reports"]) && $arrUserAccessPrivileges["priv_ar_reports"] == 1){
				$chk_priv_ar_reports = "checked=\"checked\"";
			}

			$chk_priv_days_ar="";
			if(isset($arrUserAccessPrivileges["priv_days_ar"]) && $arrUserAccessPrivileges["priv_days_ar"] == 1){
				$chk_priv_days_ar = "checked=\"checked\"";
			}

			$chk_priv_receivables="";
			if(isset($arrUserAccessPrivileges["priv_receivables"]) && $arrUserAccessPrivileges["priv_receivables"] == 1){
				$chk_priv_receivables = "checked=\"checked\"";
			}

			$chk_priv_unworked_ar="";
			if(isset($arrUserAccessPrivileges["priv_unworked_ar"]) && $arrUserAccessPrivileges["priv_unworked_ar"] == 1){
				$chk_priv_unworked_ar = "checked=\"checked\"";
			}

			$chk_priv_unbilled_claims="";
			if(isset($arrUserAccessPrivileges["priv_unbilled_claims"]) && $arrUserAccessPrivileges["priv_unbilled_claims"] == 1){
				$chk_priv_unbilled_claims = "checked=\"checked\"";
			}

			$chk_priv_top_rej_reason="";
			if(isset($arrUserAccessPrivileges["priv_top_rej_reason"]) && $arrUserAccessPrivileges["priv_top_rej_reason"] == 1){
				$chk_priv_top_rej_reason = "checked=\"checked\"";
			}

			$chk_priv_new_statements="";
			if(isset($arrUserAccessPrivileges["priv_new_statements"]) && $arrUserAccessPrivileges["priv_new_statements"] == 1){
				$chk_priv_new_statements = "checked=\"checked\"";
			}

			$chk_priv_prev_statements="";
			if(isset($arrUserAccessPrivileges["priv_prev_statements"]) && $arrUserAccessPrivileges["priv_prev_statements"] == 1){
				$chk_priv_prev_statements = "checked=\"checked\"";
			}

			$chk_priv_report_payments="";
			if(isset($arrUserAccessPrivileges["priv_report_payments"]) && $arrUserAccessPrivileges["priv_report_payments"] == 1){
				$chk_priv_report_payments = "checked=\"checked\"";
			}

			$chk_priv_prev_hcfa="";
			if(isset($arrUserAccessPrivileges["priv_prev_hcfa"]) && $arrUserAccessPrivileges["priv_prev_hcfa"] == 1){
				$chk_priv_prev_hcfa = "checked=\"checked\"";
			}

			$chk_priv_statements_pay="";
			if(isset($arrUserAccessPrivileges["priv_statements_pay"]) && $arrUserAccessPrivileges["priv_statements_pay"] == 1){
				$chk_priv_statements_pay = "checked=\"checked\"";
			}

			$chk_priv_pt_statements="";
			if(isset($arrUserAccessPrivileges["priv_pt_statements"]) && $arrUserAccessPrivileges["priv_pt_statements"] == 1){
				$chk_priv_pt_statements = "checked=\"checked\"";
			}

			$chk_priv_pt_collections="";
			if(isset($arrUserAccessPrivileges["priv_pt_collections"]) && $arrUserAccessPrivileges["priv_pt_collections"] == 1){
				$chk_priv_pt_collections = "checked=\"checked\"";
			}

			$chk_priv_assessment="";
			if(isset($arrUserAccessPrivileges["priv_assessment"]) && $arrUserAccessPrivileges["priv_assessment"] == 1){
				$chk_priv_assessment = "checked=\"checked\"";
			}

			$chk_priv_collection_report="";
			if(isset($arrUserAccessPrivileges["priv_collection_report"]) && $arrUserAccessPrivileges["priv_collection_report"] == 1){
				$chk_priv_collection_report = "checked=\"checked\"";
			}

			$chk_priv_tfl_proof="";
			if(isset($arrUserAccessPrivileges["priv_tfl_proof"]) && $arrUserAccessPrivileges["priv_tfl_proof"] == 1){
				$chk_priv_tfl_proof = "checked=\"checked\"";
			}

			$chk_priv_report_rta="";
			if(isset($arrUserAccessPrivileges["priv_report_rta"]) && $arrUserAccessPrivileges["priv_report_rta"] == 1){
				$chk_priv_report_rta = "checked=\"checked\"";
			}

			$chk_priv_billing_verification="";
			if(isset($arrUserAccessPrivileges["priv_billing_verification"]) && $arrUserAccessPrivileges["priv_billing_verification"] == 1){
				$chk_priv_billing_verification = "checked=\"checked\"";
			}

			$chk_priv_patient_status="";
			if(isset($arrUserAccessPrivileges["priv_patient_status"]) && $arrUserAccessPrivileges["priv_patient_status"] == 1){
				$chk_priv_patient_status = "checked=\"checked\"";
			}

			$chk_priv_saved_scheduled="";
			if(isset($arrUserAccessPrivileges["priv_saved_scheduled"]) && $arrUserAccessPrivileges["priv_saved_scheduled"] == 1){
				$chk_priv_saved_scheduled = "checked=\"checked\"";
			}

			$chk_priv_executed_report="";
			if(isset($arrUserAccessPrivileges["priv_executed_report"]) && $arrUserAccessPrivileges["priv_executed_report"] == 1){
				$chk_priv_executed_report = "checked=\"checked\"";
			}

			$chk_priv_collection_report="";
			if(isset($arrUserAccessPrivileges["priv_collection_report"]) && $arrUserAccessPrivileges["priv_collection_report"] == 1){
				$chk_priv_collection_report = "checked=\"checked\"";
			}

			$chk_priv_cn_pending="";
			if(isset($arrUserAccessPrivileges["priv_cn_pending"]) && $arrUserAccessPrivileges["priv_cn_pending"] == 1){
				$chk_priv_cn_pending = "checked=\"checked\"";
			}

			$chk_priv_contact_lens="";
			if(isset($arrUserAccessPrivileges["priv_contact_lens"]) && $arrUserAccessPrivileges["priv_contact_lens"] == 1){
				$chk_priv_contact_lens = "checked=\"checked\"";
			}

			$chk_priv_cn_ordered="";
			if(isset($arrUserAccessPrivileges["priv_cn_ordered"]) && $arrUserAccessPrivileges["priv_cn_ordered"] == 1){
				$chk_priv_cn_ordered = "checked=\"checked\"";
			}

			$chk_priv_cn_received="";
			if(isset($arrUserAccessPrivileges["priv_cn_received"]) && $arrUserAccessPrivileges["priv_cn_received"] == 1){
				$chk_priv_cn_received = "checked=\"checked\"";
			}

			$chk_priv_cn_dispensed="";
			if(isset($arrUserAccessPrivileges["priv_cn_dispensed"]) && $arrUserAccessPrivileges["priv_cn_dispensed"] == 1){
				$chk_priv_cn_dispensed = "checked=\"checked\"";
			}

			$chk_priv_cn_reports="";
			if(isset($arrUserAccessPrivileges["priv_cn_reports"]) && $arrUserAccessPrivileges["priv_cn_reports"] == 1){
				$chk_priv_cn_reports = "checked=\"checked\"";
			}

			$chk_priv_glasses="";
			if(isset($arrUserAccessPrivileges["priv_glasses"]) && $arrUserAccessPrivileges["priv_glasses"] == 1){
				$chk_priv_glasses = "checked=\"checked\"";
			}

			$chk_priv_gl_pending="";
			if(isset($arrUserAccessPrivileges["priv_gl_pending"]) && $arrUserAccessPrivileges["priv_gl_pending"] == 1){
				$chk_priv_gl_pending = "checked=\"checked\"";
			}

			$chk_priv_gl_ordered="";
			if(isset($arrUserAccessPrivileges["priv_gl_ordered"]) && $arrUserAccessPrivileges["priv_gl_ordered"] == 1){
				$chk_priv_gl_ordered = "checked=\"checked\"";
			}

			$chk_priv_gl_received="";
			if(isset($arrUserAccessPrivileges["priv_gl_received"]) && $arrUserAccessPrivileges["priv_gl_received"] == 1){
				$chk_priv_gl_received = "checked=\"checked\"";
			}

			$chk_priv_gl_dispensed="";
			if(isset($arrUserAccessPrivileges["priv_gl_dispensed"]) && $arrUserAccessPrivileges["priv_gl_dispensed"] == 1){
				$chk_priv_gl_dispensed = "checked=\"checked\"";
			}

			$chk_priv_gl_report="";
			if(isset($arrUserAccessPrivileges["priv_gl_report"]) && $arrUserAccessPrivileges["priv_gl_report"] == 1){
				$chk_priv_gl_report = "checked=\"checked\"";
			}

			$chk_priv_documents="";
			if(isset($arrUserAccessPrivileges["priv_documents"]) && $arrUserAccessPrivileges["priv_documents"] == 1){
				$chk_priv_documents = "checked=\"checked\"";
			}

			$chk_priv_alerts="";
			if(isset($arrUserAccessPrivileges["priv_alerts"]) && $arrUserAccessPrivileges["priv_alerts"] == 1){
				$chk_priv_alerts = "checked=\"checked\"";
			}

			$chk_priv_stage_iv="";
			if(isset($arrUserAccessPrivileges["priv_stage_iv"]) && $arrUserAccessPrivileges["priv_stage_iv"] == 1){
				$chk_priv_stage_iv = "checked=\"checked\"";
			}

			$chk_priv_stage_i="";
			if(isset($arrUserAccessPrivileges["priv_stage_i"]) && $arrUserAccessPrivileges["priv_stage_i"] == 1){
				$chk_priv_stage_i = "checked=\"checked\"";
			}

			$chk_priv_stage_ii="";
			if(isset($arrUserAccessPrivileges["priv_stage_ii"]) && $arrUserAccessPrivileges["priv_stage_ii"] == 1){
				$chk_priv_stage_ii = "checked=\"checked\"";
			}

			$chk_priv_stage_iii="";
			if(isset($arrUserAccessPrivileges["priv_stage_iii"]) && $arrUserAccessPrivileges["priv_stage_iii"] == 1){
				$chk_priv_stage_iii = "checked=\"checked\"";
			}

			$chk_priv_ccd_export="";
			if(isset($arrUserAccessPrivileges["priv_ccd_export"]) && $arrUserAccessPrivileges["priv_ccd_export"] == 1){
				$chk_priv_ccd_export = "checked=\"checked\"";
			}

			$chk_priv_ccd_export="";
			if(isset($arrUserAccessPrivileges["priv_ccd_export"]) && $arrUserAccessPrivileges["priv_ccd_export"] == 1){
				$chk_priv_ccd_export = "checked=\"checked\"";
			}

			$chk_priv_ccd_import="";
			if(isset($arrUserAccessPrivileges["priv_ccd_import"]) && $arrUserAccessPrivileges["priv_ccd_import"] == 1){
				$chk_priv_ccd_import = "checked=\"checked\"";
			}

			$chk_priv_lab_import="";
			if(isset($arrUserAccessPrivileges["priv_lab_import"]) && $arrUserAccessPrivileges["priv_lab_import"] == 1){
				$chk_priv_lab_import = "checked=\"checked\"";
			}

			$chk_priv_ccr_import="";
			if(isset($arrUserAccessPrivileges["priv_ccr_import"]) && $arrUserAccessPrivileges["priv_ccr_import"] == 1){
				$chk_priv_ccr_import = "checked=\"checked\"";
			}

			$chk_priv_dat_appts="";
			if(isset($arrUserAccessPrivileges["priv_dat_appts"]) && $arrUserAccessPrivileges["priv_dat_appts"] == 1){
				$chk_priv_dat_appts = "checked=\"checked\"";
			}
			$chk_priv_recalls="";
			if(isset($arrUserAccessPrivileges["priv_recalls"]) && $arrUserAccessPrivileges["priv_recalls"] == 1){
				$chk_priv_recalls = "checked=\"checked\"";
			}
			$chk_priv_reminder_lists="";
			if(isset($arrUserAccessPrivileges["priv_reminder_lists"]) && $arrUserAccessPrivileges["priv_reminder_lists"] == 1){
				$chk_priv_reminder_lists = "checked=\"checked\"";
			}
			$chk_priv_no_shows="";
			if(isset($arrUserAccessPrivileges["priv_no_shows"]) && $arrUserAccessPrivileges["priv_no_shows"] == 1){
				$chk_priv_no_shows = "checked=\"checked\"";
			}

			$chk_ccr_exist_pat="";
			if(isset($arrUserAccessPrivileges["ccr_exist_pat"]) && $arrUserAccessPrivileges["ccr_exist_pat"] == 1){
				$chk_ccr_exist_pat = "checked=\"checked\"";
			}

			$chk_ccr_new_pat="";
			if(isset($arrUserAccessPrivileges["ccr_new_pat"]) && $arrUserAccessPrivileges["ccr_new_pat"] == 1){
				$chk_ccr_new_pat = "checked=\"checked\"";
			}

			$chk_priv_pis="";
			if(isset($arrUserAccessPrivileges["priv_pis"]) && $arrUserAccessPrivileges["priv_pis"] == 1){
				$chk_priv_pis = "checked=\"checked\"";
			}

			$chk_priv_pt_icon_imm="";
			if(isset($arrUserAccessPrivileges["priv_pt_icon_imm"]) && $arrUserAccessPrivileges["priv_pt_icon_imm"] == 1){
				$chk_priv_pt_icon_imm = "checked=\"checked\"";
			}
			$chk_priv_pt_icon_optical="";
			if(isset($arrUserAccessPrivileges["priv_pt_icon_optical"]) && $arrUserAccessPrivileges["priv_pt_icon_optical"] == 1){
				$chk_priv_pt_icon_optical = "checked=\"checked\"";
			}
			$chk_priv_pt_icon_iasclink="";
			if(isset($arrUserAccessPrivileges["priv_pt_icon_iasclink"]) && $arrUserAccessPrivileges["priv_pt_icon_iasclink"] == 1){
				$chk_priv_pt_icon_iasclink = "checked=\"checked\"";
			}
			$chk_priv_financial_dashboard="";
			if(isset($arrUserAccessPrivileges["priv_financial_dashboard"]) && $arrUserAccessPrivileges["priv_financial_dashboard"] == 1){
				$chk_priv_financial_dashboard = "checked=\"checked\"";
			}
			$chk_priv_pt_icon_support="";
			if(isset($arrUserAccessPrivileges["priv_pt_icon_support"]) && $arrUserAccessPrivileges["priv_pt_icon_support"] == 1){
				$chk_priv_pt_icon_support = "checked=\"checked\"";
			}
			$chk_priv_ar_worksheet="";
			if(isset($arrUserAccessPrivileges["priv_ar_worksheet"]) && $arrUserAccessPrivileges["priv_ar_worksheet"] == 1){
				$chk_priv_ar_worksheet = "checked=\"checked\"";
			}

			$chk_sel_settings = (isset($arrUserAccessPrivileges["el_sel_settings"]) && $arrUserAccessPrivileges["el_sel_settings"] == 1) ? "checked=\"checked\"" : "";
			$chk_sel_clinical = (isset($arrUserAccessPrivileges["el_sel_clinical"]) && $arrUserAccessPrivileges["el_sel_clinical"] == 1) ? "checked=\"checked\"" : "";
			$chk_sel_fd = (isset($arrUserAccessPrivileges["el_sel_fd"]) && $arrUserAccessPrivileges["el_sel_fd"] == 1) ? "checked=\"checked\"" : "";
			$chk_sel_acc = (isset($arrUserAccessPrivileges["el_sel_acc"]) && $arrUserAccessPrivileges["el_sel_acc"] == 1) ? "checked=\"checked\"" : "";
			$chk_sel_rprt = (isset($arrUserAccessPrivileges["el_sel_rprt"]) && $arrUserAccessPrivileges["el_sel_rprt"] == 1) ? "checked=\"checked\"" : "";
			$chk_sel_portal = (isset($arrUserAccessPrivileges["el_sel_portal"]) && $arrUserAccessPrivileges["el_sel_portal"] == 1) ? "checked=\"checked\"" : "";
			$chk_sel_icon = (isset($arrUserAccessPrivileges["el_sel_icon"]) && $arrUserAccessPrivileges["el_sel_icon"] == 1) ? "checked=\"checked\"" : "";

            /*
            if($chk_sc_scheduler=='' || $chk_priv_report_practice_analytics=='' || $chk_priv_report_financials=='' || $chk_priv_report_compliance=='' || $chk_cl_ccd=='' || $chk_priv_report_api_access=='' || $chk_priv_report_State=='' || $chk_priv_report_optical=='' || $chk_sc_house_calls=='' || $chk_cl_clinical=='' || $chk_priv_report_Rules=='' || $chk_priv_report_iPortal=='') {
                $chk_priv_Reports_manager='';
            }*/


            $check=array();
            foreach($menu_array as $tab_head) {
                foreach($tab_head as $field_name => $label) {
                    $check[$field_name] = "";
                    if(isset($arrUserAccessPrivileges[$field_name]) && $arrUserAccessPrivileges[$field_name] == 1){
                        $check[$field_name] = "checked=\"checked\"";
                    }
                }
            }
			/*$optical_stock_add = array();
			if(isset($arrUserAccessPrivileges['priv_Optical_add']) ){
				$optical_stock_add = explode(',', $arrUserAccessPrivileges['priv_Optical_add']);
			}
			$optical_stock_remove = array();
			if(isset($arrUserAccessPrivileges['priv_Optical_remove']) ){
				$optical_stock_remove = explode(',', $arrUserAccessPrivileges['priv_Optical_remove']);
			}*/

			//pre($arrUserAccessPrivileges);
			//echo count($arrUserAccessPrivileges);
			//print "<pre>";
			//print_r($arrUserAccessPrivileges);

			//get user groups
			$arrTempUserGroups = $this->get_user_groups($pro_id);
			if(is_array($arrTempUserGroups) && count($arrTempUserGroups) > 0){
				$arrUserGroups = explode(",", $arrTempUserGroups[0][0]);
			}else{
				$arrUserGroups = false;
			}
		}

		$arr_bill_pol = $this->get_billing_policy();

		if($arr_bill_pol[0]["refraction"] == "Yes"){
			$collect_refraction_y = "checked";
			$collect_refraction_n = "";
			$refraction_disabled = "disabled";
			$refraction_hidden = "yes";
		}

		$return = "";
		$done=true;
		if(isset($_REQUEST["err"]) && $_REQUEST["err"] != ""){
			if(trim($_REQUEST["err"])=="Provider details have been saved successfully."){
				$strJSErrorMsg = "top.alert_notification_show('".trim($_REQUEST["err"])."');";
				$done=false;
			}else{
				$strJSErrorMsg = "top.fAlert('".trim($_REQUEST["err"])."');";
			}
			$return .= "<script>".$strJSErrorMsg."</script>";
			$return .= "<script>top.show_loading_image('none');</script>";
		}

		if(is_array($arrUserDetails) && count($arrUserDetails) > 0){
			extract($arrUserDetails);
		}else if(is_array($_REQUEST) && count($_REQUEST) > 0){
			extract($_REQUEST);
		}
		if($sla_date_final==""){
			$sla_date_final="Not Reviewed";
		}
		if($hippa_date_final==""){
			$hippa_date_final="Not Reviewed";
		}
		if($_REQUEST['srh_del_status']!=""){
			$show_prov_drop_sel="selected";
			$show_prov_drop_req="deleted";
		}

        //POS Facility Group
        $pos_facility_group_select='';
        $pos_fac_grp_col="col-sm-12";
        if(isPosFacGroupEnabled()) {
            $pos_fac_grp_col="col-sm-6";
            $posfacilitygroup_id_arr=json_decode(html_entity_decode($posfacilitygroup_id), true);
            $pos_facility_group_options = pos_facility_group('options',$posfacilitygroup_id_arr);
            if($pos_facility_group_options!=''){
                $pos_facility_group_select.='<label for="posfacilitygroup_id">POS Facility Group</label>';
                $pos_facility_group_select.='<select name="posfacilitygroup_id[]" id="posfacilitygroup_id" class="selectpicker" multiple data-size="5" title="Please Select" data-width="100%">';
                $pos_facility_group_select.=$pos_facility_group_options;
                $pos_facility_group_select.='</select>';
            }

        }

		//echo $default_group."fdsf";
		$return .= '


				<form name="frmprovider" method="post" action="index.php?req=save" enctype="multipart/form-data" onSubmit="return checkdata();" >
					<input type="hidden" name="pro_id" id="pro_id" value="'.$pro_id.'">
					<input type="hidden" name="oldErxUsername" value="'.$arrAuditUserDetails["erx_user_name"].'">
					<input type="hidden" name="oldErxPassword" value="'.$arrAuditUserDetails["erx_password"].'">
					<input type="hidden" name="temp_changed_layout" value="">
					<input type="hidden" id="all_access_privileges" name="all_access_privileges" value="'.$strAccessPrivileges.'">
					<input type="hidden" name="refraction_set_in_policy" value="'.$refraction_hidden.'">
					<input type="hidden" id="access_pri_audit" name="access_pri_audit" value="'.$arrUserDetails["pre_phy"].'">';

					//if(($_REQUEST['pro_id'] || $_REQUEST['add_new'] || isset($_REQUEST["err"])!="") && $done==true){

		//Follow Phy. checkbox
		$fo_class="hide";
		$fo_disp="";
		if(isset($pro_type) && !empty($pro_type) && ($pro_type==3||$pro_type==13)){ //follow phy
			$fo_class="show";
			$elem_follow_physician = $arrUserDetails["follow_physician"];
			$follow_checked="";
			if(trim($elem_follow_physician)){
				$follow_checked=" checked='checked' ";
			}
		}

		$return .= '
		<div id="provider_add_edit_div" class="modal" role="dialog" >
			<div class="modal-dialog modal_90">
				<div class="modal-content">
					<div class="modal-header bg-primary">
						<button type="button" class="close" data-dismiss="modal">×</button>
						<h4 class="modal-title" id="modal_title">'.($_REQUEST['pro_id'] ?'Edit':'Add').' New Record</h4>
					</div>
					<div class="modal-body pd5" style="overflow-x:hidden; overflow-y:auto;">
						<div class="row">
							<div class="col-lg-7 col-md-7 col-sm-12">
								<div class="recbox">
									<div class="head">
										<div class="row">
											<div class="col-sm-4"><span>Provider</span></div>
											<div class="col-sm-8 provopti content_box">
												<ul>
													<li class="text-left">
														<div class="'.$fo_class.'" id="follow_physician_div">
															<div class="checkbox">
																<input type="checkbox" name="elem_follow_physician" id="elem_follow_physician" '.$follow_checked.' value="1">
																<label for="elem_follow_physician">Follow Physician</label>
															</div>
														</div>
													</li>
													<li><strong>SLA :</strong> '.$sla_date_final.'</li>
													<li><strong>HIPAA :</strong> '.$hippa_date_final.'</li>
												</ul>
											</div>
										</div>
									</div>
									<div class="clearfix"></div>
									<div class="tblBg">
										<div class="row">
											<div class="col-sm-4">
												<div class="form-group">
													<label for="pro_type">Provider Type</label>
													<select tabindex="1" id="pro_type" name="pro_type" class="selectpicker" data-width="100%" onFocus="change_layout_color(\'fac_div\', \'temp_changed_layout\', \'retain\');"  data-width="100%" onChange="change_form_values(this.value);" title="'.imw_msg('drop_sel').'" data-size="7">
														<option value="">'.imw_msg('drop_sel').'</option>';
														$pro_type_new = "";
														if(isset($pro_type) && !empty($pro_type)){
															$arr_temp_pro_type = explode("-",$pro_type);
															$pro_type_new = $arr_temp_pro_type[0];
														}
														for($row = 0; $row < count($arrProviderTypes); $row++){
															$sel = ($pro_type_new == $arrProviderTypes[$row]["user_type_id"]) ? 'selected' : '';
															$str_privileges = "";
															$arr_privileges = unserialize(html_entity_decode(trim($arrProviderTypes[$row]["default_access_privileges"])));
															if(is_array($arr_privileges) && count($arr_privileges) > 0){
																foreach($arr_privileges as $ak => $av){
																$str_privileges .= $ak.":".$av.";";
																}
															}

															if($str_privileges != ""){
																$str_privileges = substr($str_privileges, 0, -1);
															}

															//color
															$tmp = $arrProviderTypes[$row]["color"];
															if(strpos($tmp,",")!==false){
																$tmp = explode(",",$tmp);
																$tmp = $tmp[0];
															}
															$return .= '<option style="background-color:'.$tmp.';" value="'.$arrProviderTypes[$row]['user_type_id'].'-'.$str_privileges.'-'.$arrProviderTypes[$row]['default_scheduler_status'].'" '.$sel.'>'.$arrProviderTypes[$row]['user_type_name'].'</option>';
														}
													$return .= '</select>
												</div>
											</div>

											<div class="col-sm-4">
												<div class="form-group">
													<label for="pro_group">Provider Group</label>
													<select tabindex="2" name="pro_group" id="pro_group" class="selectpicker" data-width="100%" onFocus="change_layout_color(\'fac_div\', \'temp_changed_layout\', \'retain\');" title="'.imw_msg('drop_sel').'" onchange="disp_providers()" data-size="7">
													<option value="">'.imw_msg('drop_sel').'</option>';
													for($row = 0; $row < count($arrUserGroup); $row++){
														$sel = ($pro_group == $arrUserGroup[$row]["id"]) ? 'selected' : '';
														$return .= '<option value="'.$arrUserGroup[$row]['id'].'" '.$sel.'>'.$arrUserGroup[$row]['name'].'</option>';
													}
													$return .= '</select>
												</div>
											</div>

											<div class="col-sm-4">
												<div class="form-group">
													<label for="">Specialty</label>
													<div id="EnableOpt_Spl">
														'.$this->providers_specility().'
													</div>
												</div>
											</div>
											<div class="clearfix"></div>

											<div class="col-sm-3">
												<div class="row">
													<div class="col-sm-4">
														<div class="form-group">
															<label for="">Title</label>
															<select tabindex="3" name="pro_title" id="pro_title" class="selectpicker" data-width="100%" onFocus="change_layout_color(\'fac_div\', \'temp_changed_layout\', \'retain\');" title="'.imw_msg('drop_sel').'" >
															<option value="">'.imw_msg('drop_sel').'</option>';
															for($row = 0; $row < count($arrNameTitles); $row++){
																$sel = (isset($pro_title)) ? (($pro_title == $arrNameTitles[$row]) ? "selected" : "") : "";
																$return .= "<option value=\"".$arrNameTitles[$row]."\" ".$sel.">".$arrNameTitles[$row]."</option>";
															}
															$return .= '</select>
														</div>
													</div>
													<div class="col-sm-8">
														<div class="form-group">
															<label for="pro_fname">First Name</label>
															<input tabindex="4" type="text" id="pro_fname" name="pro_fname" onBlur="change_input_class(this);" onFocus="change_layout_color(\'fac_div\', \'temp_changed_layout\', \'retain\');" value="'.((isset($pro_fname)) ? $pro_fname : "").'" class="form-control">
														</div>
													</div>
												</div>
											</div>
											<div class="col-sm-3">
												<div class="form-group">
													<label for="pro_mname">Middle</label>
													<input tabindex="5" type="text" name="pro_mname" id="pro_mname" onFocus="change_layout_color(\'fac_div\', \'temp_changed_layout\', \'retain\');" value="'.((isset($pro_mname)) ? $pro_mname : "").'" class="form-control">
												</div>
											</div>
											<div class="col-sm-3">
												<div class="form-group">
													<label for="pro_lname">Last Name</label>
													<input tabindex="6" type="text" id="pro_lname" name="pro_lname" onBlur="change_input_class(this);" onFocus="change_layout_color(\'fac_div\', \'temp_changed_layout\', \'retain\');" value="'.((isset($pro_lname)) ? $pro_lname : "").'" class="form-control">
												</div>
											</div>
											<div class="col-sm-3">
												<div class="form-group">
													<label for="pro_suffix">Suffix</label>
													<input tabindex="7" type="text" name="pro_suffix" id="pro_suffix" onFocus="change_layout_color(\'fac_div\', \'temp_changed_layout\', \'retain\');" value="'.((isset($pro_suffix)) ? $pro_suffix : "").'" class="form-control">
												</div>
											</div>
											<div class="clearfix"></div>

											<div class="col-sm-5">
												<div class="row">
													<div class="col-sm-12">
														<div class="form-group">
															<label for="pro_nicname">Nick Name</label>
															<input tabindex="9" type="text" id="pro_nicname" name="pro_nicname" onBlur="change_input_class(this);" onFocus="change_layout_color(\'fac_div\', \'temp_changed_layout\', \'retain\');" size="10" value="'.((isset($pro_nicname)) ? $pro_nicname : "").'" class="form-control">
														</div>
													</div>
													<div class="col-sm-12">
														<div class="form-group">
															<label for="default_facility">Default Facility</label>
															<select tabindex="8" name="default_facility" id="default_facility" onFocus="change_layout_color(\'fac_div\', \'temp_changed_layout\', \'retain\');" class="selectpicker" data-width="100%" title="'.imw_msg('drop_sel').'" data-size="5">
																<option value="">'.imw_msg('drop_sel').'</option>';
																for($row = 0; $row < count($arrFacilities); $row++){
																	$sel = (isset($default_facility)) ? (($default_facility == $arrFacilities[$row]['id']) ? 'selected' : '') : "";
																	$return .= '<option value="'.$arrFacilities[$row]['id'].'" '.$sel.'>'.$arrFacilities[$row]['name'].'</option>';
																}
															$return .= '</select>
														</div>
													</div>
													<div class="'.$pos_fac_grp_col.'">
														<div class="form-group">
															<label for="session_timeout">Session Timeout</label>
															<select tabindex="9" name="session_timeout" id="session_timeout" class="selectpicker" data-width="100%" onBlur="change_layout_color(\'fac_div\', \'temp_changed_layout\', \'\');" onFocus="change_layout_color(\'fac_div\', \'temp_changed_layout\', \'retain\');" title="'.imw_msg('drop_sel').'" data-size="5">';
																$sel = (isset($session_timeout)) ? (($session_timeout == "1MI") ? "selected" : "") : "";
																$return .= '<option value="1MI" '.$sel.'>1 Min.</option>';
																for($row = 5; $row <= 15; $row = $row + 5){
																	$sel = (isset($session_timeout)) ? (($session_timeout == $row."MI") ? "selected" : "") : "";
																	$return .= '<option value="'.$row."MI".'" '.$sel.'>'.$row.' Mins.</option>';
																}
																for($row = 30; $row <= 50; $row = $row + 10){
																	$sel = (isset($session_timeout)) ? (($session_timeout == $row."MI") ? "selected" : "") : "";
																	if(!isset($session_timeout)) {
																		$sel = (!isset($session_timeout)) ? (($row == "30") ? "selected" : "") : "";
																	}
																	$return .= '<option value="'.$row."MI".'" '.$sel.'>'.$row.' Mins.</option>';
																}
																for($row = 1; $row <= 6; $row++){
																	$sel = (isset($session_timeout)) ? (($session_timeout == $row."HR") ? "selected" : "") : "";
																	$return .= '<option value="'.$row.'HR" '.$sel.'>'.$row.' Hrs.</option>';
																}
															$return .= '</select>
														</div>
													</div>';
                                                    if($pos_facility_group_select!=''){
                                                    $return .= '<div class="'.$pos_fac_grp_col.'"><div class="form-group">
                                                        '.$pos_facility_group_select.'
                                                        </div></div>';
                                                    }
									$return .= '</div>
											</div>
											<div class="col-sm-7">
												<div class="form-group">
													<label for="">Provider Signature</label>
													<div class="row">
														<div class="col-sm-12">
															<div class="form-group">
																<span class="sig_applet" id="td_signature_applet1" onclick="getAssessmentSign(1)">';
																if(!empty($elem_sign_path) && strpos($elem_sign_path,"UserId") !== false && file_exists(trim(substr(data_path(), 0, -1).$elem_sign_path))){
																	$return .=	'<img src="'.data_path(1).$elem_sign_path.'" alt="sign" style="width:225px; height:60px;" >';
																}
																$return .= '</span>
																<input type="hidden" name="elem_sign" id="elem_sign" value="'.((isset($elem_sign)) ? trim($elem_sign) : "").'">
																<input type="hidden" name="elem_sign_path" id="elem_sign_path" value="'.((isset($elem_sign_path)) ? trim($elem_sign_path) : "").'">
															</div>
														</div>
														<div class="col-sm-12 text-right sig_btn">
															<div class="form-group">
																<span class=" pointer">
																	<i class="glyphicon glyphicon-hand-up" title="Touch Signature" id="SignBtnTouch" name="SignBtnTouch" onclick="OnSignIpadPhy(\''.$_SESSION['patient'].'\',\'adminProvider\',\'td_signature_applet1\',\'1\')"></i>
																</span>

																<span class="pointer">
																	<i class="hand_cur glyphicon glyphicon-trash" title="Clear" onClick="getAssessmentSign(1,3)"></i>
																</span>
															</div>
														</div>
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
								<div class="recbox">
									<div class="head">
										<span>Scheduler</span>
									</div>
									<div class="clearfix"></div>
									<div class="tblBg">
										<div class="row">
											<div class="col-sm-12">
												<div class="row">
													<div class="col-sm-3">
														<div class="form-group">
															<label for="sch_fac_id">Facility</label>
															<div id="EnableOpt0">
																<select tabindex="10" multiple name="sch_fac_id[]" id="sch_fac_id"  onFocus="change_layout_color(\'Schedule_div\', \'temp_changed_layout\', \'retain\');" class="selectpicker dropup" data-dropup-auto="true" data-width="100%" data-size="7" data-actions-box="true" title="'.imw_msg('drop_sel').'">';
																$tmpArr = explode(";",$arrUserDetails['sch_facilities']);
																for($row = 0; $row < count($arrFacilities); $row++){
																	$sel = in_array($arrFacilities[$row]['id'],$tmpArr)?'selected':'';
																	$return .= "<option value=\"".$arrFacilities[$row]['id']."\" ".$sel.">".$arrFacilities[$row]['name']."</option>";
																}
																$return .= '</select>
																<input type="hidden" id="selected_sch_facs" name="selected_sch_facs" value="" />
															</div>
														</div>
													</div>';

													//Scheduler enable and stop checkbox
														$chk_enable_sch = ($Enable_Scheduler == 1) ? "checked" : "";
														$chk_over_booking = ($StopOverBooking == "Yes") ? "checked" : "";
														$enable_blur = '';
														if($chk_enable_sch){
															$enable_blur .= 'onBlur="change_layout_color(\'Schedule_div\', \'temp_changed_layout\', \'\');"';
														}

													$return .= '
													<div class="col-sm-2">
														<div class="form-group">
															<label>&nbsp;</label>
															<div class="checkbox">
																<input tabindex="11" type="checkbox" '.$enable_blur.' onFocus="change_layout_color(\'Schedule_div\', \'temp_changed_layout\', \'retain\');" name="Enable_Scheduler" id="Enable_Scheduler" '.$chk_enable_sch.' value="1" onClick="scheduler_settings_display(this);">
																<label for="Enable_Scheduler">Enable</label>
															</div>
														</div>
													</div>

													<div class="col-sm-7">
														<div class="row">
															<div class="col-sm-3">
																<div class="form-group">
																	<label for="EnableOpt10" id="EnableOpt9">Sch Index
																	<select tabindex="12" name="sch_index" id="EnableOpt10" class="selectpicker dropup" data-dropup-auto="true" data-width="100%" data-size="7" data-actions-box="true" title="'.imw_msg('drop_sel').'">';
																	if($sch_index!=100)$return .= "<option value=\"100\">Remove Index</option>";
																	if($sch_index==100)$return .= "<option value=\"100\" selected>No Index</option>";
																		for($row = 1; $row <= 20; $row++){
																			$sel = ($sch_index==$row)?'selected':'';
																			$return .= "<option value=\"$row\" $sel>$row</option>";
																		}
																		$return .= '</select></label>
																</div>
															</div>
															<div class="col-sm-2">
																<div class="form-group">
																	<label for="EnableOpt3" id="EnableOpt5">Appt / TS</label>
																	<input tabindex="12" onFocus="change_layout_color(\'Schedule_div\', \'temp_changed_layout\', \'retain\');" id="EnableOpt3" type="text" name="max_appoint" value="'.((isset($max_appoint)) ? $max_appoint : '').'" class="form-control">
																</div>
															</div>


															<div class="col-sm-3">
																<div class="form-group" id="EnableOpt7">
																	<label for="pro_color" >Color</label>
																	<!-- <div class="bfh-colorpicker" data-name="pro_color" data-color="'.$pro_color.'"></div> -->
																	<input type="text" class="grid_color_picker11 form-control" name="pro_color" value="'.$pro_color.'" />
																</div>
															</div>

															<div class="col-sm-2">
																<div class="form-group" id="EnableOpt8">
																	<label for="max_per" >Max %</label>
																	<input tabindex="15" onFocus="change_layout_color(\'Schedule_div\', \'temp_changed_layout\', \'retain\');" id="max_per" type="text" name="max_per" value="'.((isset($max_per)) ? $max_per : '').'" class="form-control">
																</div>
															</div>

															<div class="col-sm-2">
																<div class="form-group">
																	<label>&nbsp;</label>
																	<div class="checkbox">
																		<input tabindex="13" onBlur="change_layout_color(\'Schedule_div\', \'temp_changed_layout\', \'\');" onFocus="change_layout_color(\'Schedule_div\', \'temp_changed_layout\', \'retain\');" id="EnableOpt4" type="checkbox" style="cursor:hand;" name="StopOverBooking" value="Yes" '.$chk_over_booking.'>
																		<label for="EnableOpt4" id="EnableOpt6">Stop</label>
																	</div>
																</div>
															</div>
														</div>
													</div>
												</div>
											</div>
										</div>
									</div>';
									if(isset($Enable_Scheduler) && $Enable_Scheduler != 1){
										$return .= "<script>show_hide_scheduler_opts('disabled')</script>";
									}else{
										$return .= "<script>show_hide_scheduler_opts('enabled')</script>";
									}
								$return .= '</div>
							</div>';

							$dis = ($pro_type_new == '1' || $pro_type_new == '11' || $pro_type_new == '12' || $pro_type_new == '19' || $pro_type_new == '21' || $pro_type_new=='7') ? 'enabled' : 'disabled';

							$return .= '
							<div class="col-lg-5 col-md-5 col-sm-12">
								<div class="recbox">
									<div class="head">
										<div class="row">
											<div class="col-sm-7"><span>ID Information</span></div>
											<div class="col-sm-5 text-right">';
												if( is_allscripts('enabled') && $pro_id !== "") $return .= '<button type="button" class="btn btn-primary btn-sm" onClick="top.fmain.twCredSave(\'show\', \''.$pro_id.'\');">TouchWorks EHR Credentials</button>';

												// if(isDssEnable() && $pro_id !== "") $return .= '<button type="button" class="btn btn-primary btn-sm" onClick="top.fmain.dssUserInfoSave(\'show\', \''.$pro_id.'\');">DSS Info</button>';
											 $return .= '
											</div>
										</div>
									</div>
									<div class="tblBg">
										<div class="row">
											<div class="col-sm-4">
												<div class="form-group">
													<label for="user_npi">NPI#</label>
													<input tabindex="58" onFocus="change_layout_color(\'ID_div\', \'temp_changed_layout\', \'retain\');" name="user_npi_old" id="user_npi_old" type="hidden" value="'.((isset($user_npi)) ? $user_npi : "").'" >
													<input tabindex="58" onFocus="change_layout_color(\'ID_div\', \'temp_changed_layout\', \'retain\');" name="user_npi" id="user_npi" type="text" class="form-control" value="'.((isset($user_npi)) ? $user_npi : "").'">
												</div>
											</div>
											<div class="col-sm-4">
												<div class="form-group">
													<label for="TaxonomyId">Taxonomy ID</label>
													<input tabindex="59" onFocus="change_layout_color(\'ID_div\', \'temp_changed_layout\', \'retain\');" name="TaxonomyId" id="TaxonomyId" type="text" class="form-control" value="'.((isset($TaxonomyId)) ? $TaxonomyId : "").'">
												</div>
											</div>
											<div class="col-sm-4">
												<div class="form-group">
													<label for="pro_upin">UPIN#</label>
													<input tabindex="60" onFocus="change_layout_color(\'ID_div\', \'temp_changed_layout\', \'retain\');" name="pro_upin" id="pro_upin" type="text" class="form-control" value="'.((isset($pro_upin)) ? $pro_upin : "").'" >
												</div>
											</div>
											<div class="clearfix"></div>

											<div class="col-sm-4">
												<div class="form-group">
													<label for="pro_drug">Federal Drug ID</label>
													<input tabindex="62" onFocus="change_layout_color(\'ID_div\', \'temp_changed_layout\', \'retain\');" name="pro_drug" id="pro_drug" type="text" class="form-control" value="'.((isset($pro_drug)) ? $pro_drug : "").'" size="11">
												</div>
											</div>
											<div class="col-sm-4">
												<div class="form-group">
													<label for="pro_lic">Lic '.getHashOrNo(false).'</label>
													<input tabindex="63" onFocus="change_layout_color(\'ID_div\', \'temp_changed_layout\', \'retain\');" name="pro_lic" id="pro_lic" type="text" class="form-control" value="'.((isset($pro_lic)) ? $pro_lic : "").'">
												</div>
											</div>
											<div class="col-sm-4">
												<div class="form-group">
													<label for="MedicareId">Medicare ID</label>
													<input tabindex="64" onFocus="change_layout_color(\'ID_div\', \'temp_changed_layout\', \'retain\');" name="MedicareId" id="MedicareId" type="text" class="form-control" value="'.((isset($MedicareId)) ? $MedicareId : "").'" >
												</div>
											</div>
											<div class="clearfix"></div>

											<div class="col-sm-4">
												<div class="form-group">
													<label for="group_name">Group</label>
													<select tabindex="65" name="group_name" id="group_name" onFocus="change_layout_color(\'ID_div\', \'temp_changed_layout\', \'retain\');" class="selectpicker dropup" data-width="100%" data-size="5" onchange="javascript:load_fed_ein(this);" data-title="'.imw_msg('drop_sel').'" data-dropup-auto="false">
														<option value=\"\">'.imw_msg('drop_sel').'</option>';
														$int_tot_groups = count($arrGroups);
														$show_fed_ein = "";
														$sel2 = "";
														for($row = 0; $row < $int_tot_groups; $row++){
															$sel2 = (($default_group != "") && ($default_group == $arrGroups[$row]["gro_id"])) ? 'selected' : '';
															if($int_tot_groups == 1 && $sel2 == ""){$sel2 = "selected";}
															if($int_tot_groups == 1){$show_fed_ein = $arrGroups[$row]["group_Federal_EIN"];}
															$return .= '<option value="'.$arrGroups[$row]["gro_id"]."^".$arrGroups[$row]["group_Federal_EIN"].'" '.$sel2.'>'.$arrGroups[$row]["name"].'</option>';
														}
													$return .= '
													</select>
												</div>
											</div>';

											$show_fed_ein = (isset($pro_tax) && trim($pro_tax) != "") ? $pro_tax : $show_fed_ein;
											$return .='
											<div class="col-sm-4">
												<div class="form-group">
													<label for="pro_tax">Fed EIN '.getHashOrNo(false).'</label>
													<input tabindex="66" onFocus="change_layout_color(\'ID_div\', \'temp_changed_layout\', \'retain\');" name="pro_tax" id="pro_tax" type="text" class="form-control" value="'.$show_fed_ein.'" >
												</div>
											</div>
											<div class="col-sm-4">
												<div class="form-group">
													<label for="MedicaidId">Medicaid ID</label>
													<input tabindex="67" onFocus="change_layout_color(\'ID_div\', \'temp_changed_layout\', \'retain\');" name="MedicaidId" id="MedicaidId" type="text" class="form-control" value="'.((isset($MedicaidId)) ? $MedicaidId : "").'" >
												</div>
											</div>
											<div class="clearfix"></div>

											<div class="col-sm-4">
												<div class="form-group">
													<label for="external_id">SMS ID</label>
													<input tabindex="68" onFocus="change_layout_color(\'ID_div\', \'temp_changed_layout\', \'retain\');" name="external_id" id="external_id" type="text" class="form-control" value="'.((isset($external_id)) ? $external_id : "").'" >
												</div>
											</div>';

											// iMedic ID
											if(constant('REMOTE_SYNC')=='1') {
											$return .= '
											<div class="col-sm-4">
												<div class="form-group">
													<label for="imwemr_id">iMedic&nbsp;ID</label>
													<input tabindex="69" onFocus="" name="imwemr_id" id="imwemr_id" type="text" class="form-control" value="'.((isset($imwemr_id) && $imwemr_id) ? $imwemr_id : "").'">
												</div>
											</div>';
											}






											// Key
											$dis = trim($user_npi) ? 'hidden' : 'visible';
											$return .= '
											<div class="col-sm-4">
												<div class="form-group ">
													<label for="imw_key" style="visibility:'.$dis.'">Key</label>
													<input type="text" name="imw_key" id="imw_key" class="form-control"  value="" style="visibility:'.$dis.'" >
												</div>
												<input type="file" name="pro_img" id="pro_img" style="visibility:hidden; position:absolute;">
											</div>
											<div class="clearfix"></div>';

											//SSO Identifier
											$return .= '
											<div class="col-sm-4">
												<div class="form-group">
													<label for="sso_identifier">SSO&nbsp;ID</label>
													<input tabindex="69" onFocus="" name="sso_identifier" id="sso_identifier" type="text" class="form-control" value="'.((isset($sso_identifier) && $sso_identifier) ? $sso_identifier : "").'">
												</div>
											</div>';

										$return .= '
										</div>
									</div>
								</div>';

								//Group Pervlgs
								$opt_grp_prvs = $oGroupPrevileges->get_privileges_opts($groups_prevlgs_id, 1);
								if(strpos($opt_grp_prvs, "\"".$groups_prevlgs_id."\"")===false){ $groups_prevlgs_id=""; }

							
							/** get all providers same as display in reports */	
							$provider_options= $cls_common->drop_down_providers($arrUserDetails["provider_financials"], '1', '1', '', 'report');
							
							$return .='<div class="recbox">
									<div class="head ">
										<div class="row">
											<div class="col-sm-3 pointer">
												<ul class="no_style_ul">
													<li class="text-left">
														<label  for="el_privileges" '.(!empty($groups_prevlgs_id)?"":" id=\"pr_div\" onclick=\"show_privilages();\" class=\"pointer\" ").'>Privileges</label>
													</li>
													<li class="text-left">
														<select id="el_privileges" name="el_privileges" class="form-control" onchange="show_privilages(this)" >
														<option value="">-Select-</option>
														'.$opt_grp_prvs.'
														</select>
													</li>
												</ul>
											</div>
											<div class="col-sm-9 provopti content_box">
												<ul>
													<li class="text-left">';
														$sx_physician_checked = trim($arrUserDetails["sx_physician"]) ? "checked" : '';
														$return .= '
														<div>
															<div class="checkbox">
																<input type="checkbox" name="sx_physician" id="sx_physician" '.$sx_physician_checked.' value="1">
																<label for="sx_physician">Sx&nbsp;Physician</label>
															</div>
														</div>
													</li>
													<li>
														<div class="btn-group">';
															if(constant("ZEISS_FORUM") == "YES") {
															$return .= '<button type="button" class="btn btn-success btn-sm" onclick="zeissCredentials(\''.$pro_id.'\');">Zeiss Credentials</button>';
															}
															if(isset($pro_id) && $pro_id != ""){
																$return .= '
																<button type="button" class="btn btn-info btn-sm" onclick="directCredentials(\''.$pro_id.'\');">Direct Credentials</button>
																<button type="button" class="btn btn-primary btn-sm" onClick="scanPatientImage(\''.$pro_id.'\');">Update Provider Photo</button>';
															}
															if(isERPPortalEnabled() && isset($pro_id) && $pro_id != "" && $pro_type_new == '1' ){
																$return .= '<div class="clearfix pd5"></div>
																<button type="button" class="btn btn-success btn-sm" onClick="portal_refill_direct(\''.$pro_id.'\');">Refill Direct</button>';
															}
														$return .='
														</div>
													</li>
												</ul>
											</div>
										</div>

									</div>
									<div class="tblBg">
										<div class="row mt10 mb10" >
											<div class="col-sm-4" style="margin-top:20px">	
												<div class="checkbox">';
												$view_all_provider_financials = trim($arrUserDetails["view_all_provider_financials"]=='1') ? "checked" : '';	
												$return .= '
													<input type="checkbox" name="view_all_provider_financials" id="view_all_provider_financials" '.$view_all_provider_financials.' value="1" onclick="disp_providers();">
													<label for="view_all_provider_financials">View all provider\'s financials</label>
												</div>														
											</div>
											<div id="div_rpt_financial_providers" class="col-sm-8" style="visibility:hidden">
												<label for="pro_pass_news">Providers</label>	
												<select tabindex="10" multiple data-size="10" data-width="100%" name="rpt_financial_providers[]" id="rpt_financial_providers" class="selectpicker" data-actions-box="true" >	
													'.$provider_options.'
												</select>											
											</div>
										</div>

										<div class="row">
											<div class="col-sm-4">
												<div class="form-group">
													<label for="pro_user">User Name </label>
													<input name="old_pro_user" id="old_pro_user" type="hidden" value="'.((isset($pro_user)) ? $pro_user : '').'">
													<input tabindex="51" name="pro_user" id="pro_user" onFocus="change_layout_color(\'privilege_div\', \'temp_changed_layout\', \'retain\');" type="text" class="form-control" value="'.((isset($pro_user)) ? $pro_user : '').'">
												</div>
											</div>';

											if($pro_id == ""){
											$return .= '
												<div class="col-sm-4">
													<div class="form-group">
														<label for="pro_pass_news">Password</label>
														<input tabindex="52" name="pro_pass_news" id="pro_pass_news" onFocus="change_layout_color(\'privilege_div\', \'temp_changed_layout\', \'retain\');" type="password" class="form-control" onBlur="return countCharFn(this, \'\', \'\');">
														<input name="pro_pass_old" id="pro_pass_old" type="hidden" value="" >
													</div>
												</div>

												<div class="col-sm-4">
													<div class="form-group">
														<label for="confirm_pass_new">Confirm Password</label>
														<input tabindex="53" name="confirm_pass_new" id="confirm_pass_new" onFocus="change_layout_color(\'privilege_div\', \'temp_changed_layout\', \'retain\');" type="password" class="form-control" onChange="checkPassowrd(\'pro_pass_news\',\'confirm_pass_new\');" value="">
													</div>
												</div>';
											}else{
												$return .= '
												<div class="col-sm-4">
													<div class="form-group">
														<label>&nbsp;</label><br>
														<a class="btn btn-success" id="chg_password_'.$pro_id.'" onClick="changepasswords(\''.$pro_id.'\');">Reset Password</a>
													</div>
												</div>';
												if(isset($pro_id) && $pro_id != ""){
													if(file_exists(data_path()."UserId_".$pro_id."/profile_img/Provider_".$pro_id.".jpg")){
														$return .="<div class='col-sm-4 text-right'><img src='".data_path(1)."UserId_".$pro_id."/profile_img/Provider_".$pro_id.".jpg' class=\"img-responsive img-thumbnail\" width='74' height='auto'></div>";
													}
												}
											}
										$return .= '</div>';
										$return .= '<div class="row">
											<div class="col-sm-4">
												<div class="form-group">
													<label>Collect Refraction</label><br>
													<div class="radio radio-inline">
														<input tabindex="54" name="collect_refraction" onFocus="change_layout_color(\'privilege_div\', \'temp_changed_layout\', \'retain\');" id="refraction_yes" type="radio" value="1" '.$refraction_disabled.' '.$collect_refraction_y.'>
														<label for="refraction_yes">Yes</label>
													</div>
													<div class="radio radio-inline">
														<input tabindex="54" name="collect_refraction" onFocus="change_layout_color(\'privilege_div\', \'temp_changed_layout\', \'retain\');" id="refraction_no" type="radio" value="0" '.$refraction_disabled.' '.$collect_refraction_n.'>
														<label for="refraction_no">No</label>
													</div>
												</div>
											</div>';

											$return .= '
											<div class="col-sm-4">
												<div class="form-group">
													<label for="erx_user_name">eRx Username</label>
													<input tabindex="56" name="erx_user_name" onFocus="$(\'#erx_user_name\').attr(\'readonly\', false); change_layout_color(\'privilege_div\', \'temp_changed_layout\', \'retain\');" id="erx_user_name" type="text" class="form-control" value="'.((isset($erx_user_name)) ? $erx_user_name : "").'" readonly autocomplete="off">
												</div>
											</div>';

											$return .= '
											<div class="col-sm-4">
												<div class="form-group">
													<label for="erx_password">eRx Password</label>
													<input tabindex="57" onBlur="change_layout_color(\'privilege_div\', \'temp_changed_layout\', \'\');" name="erx_password" onFocus="$(\'#erx_password\').attr(\'readonly\', false); change_layout_color(\'privilege_div\', \'temp_changed_layout\', \'retain\');" id="erx_password" type="password" class="form-control" value="'.((isset($erx_password)) ? $erx_password : "").'" readonly autocomplete="off">
												</div>
											</div>';
										$return .=' </div>
									</div>
								</div>
							</div>';
						$return .= '</div>
					</div>';

					
					
					if(isset($pro_type) && !empty($pro_type)){
							$arr_temp_pro_type = explode("-",$pro_type);
							$pro_type_new = $arr_temp_pro_type[0];
							if($pro_type_new == 1 || $pro_type_new == 11 || $pro_type_new == 12 || $pro_type_new == 19 || $pro_type_new == 21 ||   $pro_type_new=='7' || $pro_type_new=='9'){
								$return .= "<script>top.fmain.show_hide_id_opts('enabled');</script>";
							}else{
								$return .= "<script>top.fmain.show_hide_id_opts('disabled');</script>";
							}
						}else{
							$return .= "<script>top.fmain.show_hide_id_opts('disabled');</script>";
						}

					// Modal Footer
					$return .= '
					<div class="modal-footer"></div>';
					$return .= '
				</div>
			</div>
		</div>';

		ob_start();
		$zflg_in_users=1;
		include($GLOBALS['incdir']."/admin/groupprivileges/permission.nic.php");
		$return .= ob_get_clean();


	/*
        $setting_str = '';
        $menuParentArr = array(
            'Admin' => 'el_main_priv',
            'Billing' => 'el_main_priv',
            'Clinical' => 'el_main_priv',
            'Documents' => 'el_main_priv',
            'iASC_Link' => 'el_main_priv',
            'iMedic_Monitor' => 'el_main_priv',
            'iPortal' => 'el_main_priv',
            'Manage_Fields' => 'el_main_priv',
            'Optical_Settings' => 'el_main_priv',
            'Setting_Reports' => 'el_main_priv',
            'Setting_Scheduler' => 'el_main_priv',
            'IOLs' => 'el_main_priv',
            'iOptical' => 'el_main_priv',
            'Scheduler' => 'div_Reports_n_reports',
            'Practice_Analytics' => 'div_Reports_n_reports',
            'Financials' => 'div_Reports_n_reports',
            'Compliance' => 'div_Reports_n_reports',
            'CCD' => 'div_Reports_n_reports',
            'API' => 'div_Reports_n_reports',
            'State' => 'div_Reports_n_reports',
            'Optical' => 'div_Reports_n_reports',
            'Reminders' => 'div_Reports_n_reports',
            'ReportClinical' => 'div_Reports_n_reports',
            'Rules' => 'div_Reports_n_reports',
            'ReportiPortal' => 'div_Reports_n_reports',
        );

        foreach ($menu_array as $menu_head => $tab_head) {
            $setting_chunk = array_chunk($tab_head, 5, true);

            $menu_title = ucfirst($menu_head);
            $setting_str .= '<div id="admin'.$menu_head.'" class="adminPrivDiv" data-parent="'.$menuParentArr[$menu_head].'">';
            $setting_str .= '<table class="table">';
            foreach ($setting_chunk as $chunk) {
                $setting_str .= '<tr>';
                foreach ($chunk as $field_name => $label) {
                    $setting_str .= '<td style="width:20%;"><div class="checkbox"><input class="privoptioncheck" type="checkbox" name="' . $field_name . '" id="' . $field_name . '" value="1" ' . $check[$field_name] . '><label for="' . $field_name . '">' . $label . '</label></div></td>';
                }
                $setting_str .= '</tr>';
            }
            $setting_str .= '</table></div>';
        }

		// Creating Priviliges Modal
		$return .= '
		<div id="new_priv_div" class="modal" role="dialog" >
			<div class="modal-dialog modal-lg">
				<div class="modal-content">
					<div class="modal-header bg-primary" id="priv_div_handler">
						<button type="button" class="close" data-dismiss="modal" id="close_priv">×</button>
						<h4 class="modal-title" id="modal_title">Privileges</h4>
					</div>

					<div class="modal-body pd5" style="overflow:hidden; overflow-y:auto;">
						<div id="priv_grant"></div>


						<div id="ele_priv_chk">
							<div id="el_main_priv" class="col-xs-12 recbox auto_height el_main_priv" >
								<div class="head"><span>Settings</span>  &nbsp; <div class="checkbox checkbox-inline" style="text-transform:none;padding-left:25px;"><input type="checkbox" value="1" '.$chk_admin_priv_all.' title="All Settings" name="priv_all_settings" id="priv_all_settings" class="priv_all_settings" data-changediv="main" onclick="javascript:selectDeselect_all_admin(\'el_main_priv\',this.checked);"><label for="priv_all_settings">All Privileges</label></div> </div>
								<table class="table privTable">
									<tr>
                                        <td style="width:160px;"><div class="checkbox"><input type="checkbox" title="Admin" name="priv_admin" id="priv_admin" value="1" '.$chk_admin.' ><label for="priv_admin" >Admin</label><i class="glyphicon glyphicon-menu-down pull-right pointer" data-whatever="Admin" data-showdiv="adminAdmin" data-toggle="modal" data-target="#priv_div_modal" id="spandiv_Admin"></i></div></td>
                                        <td style="width:160px;"><div class="checkbox"><input type="checkbox" title="Billing" name="priv_admin_billing" id="priv_admin_billing" value="1" '.$chk_priv_admin_billing.'><label for="priv_admin_billing">Billing</label><i class="glyphicon glyphicon-menu-down pull-right pointer" data-whatever="Billing" data-showdiv="adminBilling" data-toggle="modal" data-target="#priv_div_modal" id="spandiv_billing"></i></div></td>
                                        <td style="width:160px;"><div class="checkbox"><input type="checkbox" title="Clinical" name="priv_admin_clinical" id="priv_admin_clinical" value="1" '.$chk_priv_admin_clinical.' ><label for="priv_admin_clinical">Clinical</label><i class="glyphicon glyphicon-menu-down pull-right pointer" data-whatever="Clinical" data-showdiv="adminClinical" data-toggle="modal" data-target="#priv_div_modal" id="spandiv_clinical"></i></div></td>
                                        <td style="width:160px;"><div class="checkbox"><input type="checkbox" title="Document" name="priv_document" id="priv_document" value="1" '.$chk_priv_document.'><label for="priv_document">Documents</label><i class="glyphicon glyphicon-menu-down pull-right pointer" data-whatever="Documents" data-showdiv="adminDocuments" data-toggle="modal" data-target="#priv_div_modal" id="spandiv_documents"></i></div></td>
                                        <td style="width:160px;"><div class="checkbox"><input type="checkbox" title="iASC Link" name="priv_iOLink" id="priv_iOLink" value="1" '.$chk_iOLink.'><label for="priv_iOLink">iASC Link</label><i class="glyphicon glyphicon-menu-down pull-right pointer" data-whatever="iASC_Link" data-showdiv="adminiASC_Link" data-toggle="modal" data-target="#priv_div_modal" id="spandiv_iASCLink"></i></div></td>
                                        <td style="width:180px;"><div class="checkbox"><input type="checkbox" title="iMedic Monitor" name="priv_iMedicMonitor" id="priv_iMedicMonitor" value="1" '.$chk_iMedicMonitor.'><label for="priv_iMedicMonitor">iMedic Monitor</label><i class="glyphicon glyphicon-menu-down pull-right pointer" data-whatever="iMedic_Monitor" data-showdiv="adminiMedic_Monitor" data-toggle="modal" data-target="#priv_div_modal" id="spandiv_iMedicMonitor"></i></div></td>
                                    </tr>
                                    <tr>
                                        <td style="width:160px;"><div class="checkbox"><input type="checkbox" title="iPortal" name="priv_iportal" id="priv_iportal" value="1" '.$chk_priv_iportal.'><label for="priv_iportal">iPortal</label><i class="glyphicon glyphicon-menu-down pull-right pointer" data-whatever="iPortal" data-showdiv="adminiPortal" data-toggle="modal" data-target="#priv_div_modal" id="spandiv_iPortal"></i></div></td>
                                        <td style="width:160px;"><div class="checkbox"><input type="checkbox" title="Manage Fields" name="priv_manage_fields" id="priv_manage_fields" value="1" '.$chk_priv_manage_fields.'><label for="priv_manage_fields">Manage&nbsp;Fields</label><i class="glyphicon glyphicon-menu-down pull-right pointer" data-whatever="Manage_Fields" data-showdiv="adminManage_Fields" data-toggle="modal" data-target="#priv_div_modal" id="spandiv_Manage_Fields"></i></div></td>
                                        <td style="width:160px;"><div class="checkbox"><input type="checkbox" title="Optical Settings" name="priv_Admin_Optical" id="priv_Admin_Optical" value="1" '.$chk_priv_Admin_Optical.'><label for="priv_Admin_Optical">Optical Settings</label><i class="glyphicon glyphicon-menu-down pull-right pointer" data-whatever="Optical_Settings" data-showdiv="adminOptical_Settings" data-toggle="modal" data-target="#priv_div_modal" id="spandiv_Optical_Settings"></i></div></td>
                                        <td style="width:160px;"><div class="checkbox"><input type="checkbox" title="Setting Reports" name="priv_Admin_Reports" id="priv_Admin_Reports" value="1" '.$chk_priv_Admin_Reports.'><label for="priv_Admin_Reports">Reports</label><i class="glyphicon glyphicon-menu-down pull-right pointer" data-whatever="Setting_Reports" data-showdiv="adminSetting_Reports" data-toggle="modal" data-target="#priv_div_modal" id="spandiv_Setting_Reports"></i></div></td>
                                        <td style="width:160px;"><div class="checkbox"><input type="checkbox" title="IOLs" name="priv_iols" id="priv_iols" value="1" '.$chk_priv_iols.'><label for="priv_iols">IOLs</label><i class="glyphicon glyphicon-menu-down pull-right pointer" data-whatever="IOLs" data-showdiv="adminIOLs" data-toggle="modal" data-target="#priv_div_modal" id="spandiv_IOLs"></i></div></td>
                                        <td style="width:180px;"><div class="checkbox"><input type="checkbox" title="Setting Scheduler" name="priv_admin_scheduler" id="priv_admin_scheduler" value="1" '.$chk_priv_admin_scheduler.'><label for="priv_admin_scheduler">Scheduler</label><i class="glyphicon glyphicon-menu-down pull-right pointer" data-whatever="Setting_Scheduler" data-showdiv="adminSetting_Scheduler" data-toggle="modal" data-target="#priv_div_modal" id="spandiv_Setting_Scheduler"></i></div></td>
                                    </tr>
                                    <tr>
                                        <td style="width:160px;"><div class="checkbox"><input type="checkbox" title="iOptical" name="priv_Optical" id="priv_Optical" value="1" '.$chk_Optical.' ><label for="priv_Optical" >iOptical</label><i class="glyphicon glyphicon-menu-down pull-right pointer" data-whatever="iOptical" data-showdiv="adminiOptical" data-toggle="modal" data-target="#priv_div_modal" id="spandiv_iOptical"></i></div>
                                        <td style="width:160px;"><div class="checkbox"><input type="checkbox" title="Security" name="priv_Security" id="priv_Security" value="1" '.$chk_Security.'><label for="priv_Security">Security</label></div></td>

                                        <td style="width:160px;"><div class="checkbox"><input type="checkbox" title="API Access" name="priv_api_access" id="priv_api_access" value="1" '.$chk_api_access.'><label for="priv_api_access">API Access</label></div></td>

				    </tr>
							</table>
						</div>

						<div id="el_div_clinic" class="col-xs-12 recbox auto_height" >
							<div class="head"><span>Clinical</span></div>
							<table class="table">
								<tr>
									<td style="width:14%;"><div class="checkbox"><input type="checkbox" title="Work View" name="priv_cl_work_view" id="priv_cl_work_view" value="1" '.$chk_cl_work_view.$chk_disabled.' ><label for="priv_cl_work_view">Work View</label></div></td>

									<td style="width:14%;"><div class="checkbox"><input type="checkbox" title="Tests" name="priv_cl_tests" id="priv_cl_tests" value="1" '.$chk_cl_tests.$chk_disabled.' ><label for="priv_cl_tests">Tests</label></div></td>

									<td style="width:14%;"><div class="checkbox"><input type="checkbox" title="Medical Hx" name="priv_cl_medical_hx" id="priv_cl_medical_hx" value="1" '.$chk_cl_medical_hx.$chk_disabled.' ><label for="priv_cl_medical_hx">Medical Hx</label></div></td>

									<td style="width:14%;"><div class="checkbox"><input type="checkbox" title="eRx" name="erx_chk" id="erx_chk" value="1" '.$chk_erx.$chk_disabled.' ><label for="erx_chk">eRx</label></div></td>

									<td style="width:14%;"><div class="checkbox"><input type="checkbox" title="Break Glass" name="priv_break_glass" id="priv_break_glass" value="1" '.$chk_break_glass.$chk_disabled.' ><label for="priv_break_glass">Break Glass</label></div></td>
									<td style="width:14%;"><div class="checkbox"><input type="checkbox" title="Patient Information Summary" name="priv_pis" id="priv_pis" value="1" '.$chk_priv_pis.'><label for="priv_pis" title="Patient Information Summary">Pt. Info. Sum.</label></div></td>
									<td style="width:auto;"><div class="checkbox"><input type="checkbox" title="Clinical View-Only" name="priv_vo_clinical" id="priv_vo_clinical" value="1" '.$chk_vo_clinical.$chk_disabled.'><label for="priv_vo_clinical">View-Only</label></div></td>
								</tr>
								<tr>
                                    <td class="chart_final '.(($pro_type_new == '11')?' show ':' hide ').' " style="width:160px;"><div class="checkbox"><input onchange="priv_chart_finalize(this)" type="checkbox" title="Chart finalize" name="priv_chart_finalize" id="priv_chart_finalize" value="1" '.$check_priv_chart_finalize.'><label for="priv_chart_finalize">Chart Finalize</label></div></td>
                                    <td style="width:15%;"><div class="checkbox"><input type="checkbox" name="priv_financial_hx_cpt" id="priv_financial_hx_cpt" value="1" '.$chk_priv_financial_hx_cpt.$chk_disabled.'><label for="priv_financial_hx_cpt">Financial - Hx CPT</label></div></td>
				    <td style="width:15%;"><div class="checkbox"><input type="checkbox" name="priv_purge_del_chart" id="priv_purge_del_chart" value="1" '.$chk_priv_purge_del_chart.$chk_disabled.'><label for="priv_purge_del_chart">Purge/Delete Chart</label></div></td></tr>
							</table>
						</div>

						<div id="el_main_front_desk" class="col-xs-12 recbox auto_height" >
							<div class="head"><span>Front Desk</span></div>
							<table class="table">
								<tr>
										<td style="width:14%;"><div class="checkbox"><input type="checkbox" title="Manager" name="priv_Front_Desk" id="priv_Front_Desk" '.$chk_Front_Desk.$chk_disabled.' value="1" onclick="javascript:selectDeselect_all(\'el_main_front_desk\',this.checked);"><label for="priv_Front_Desk">Manager</label></div></td>

										<td style="width:14%;"><div class="checkbox"><input type="checkbox" title="Scheduler/Demo" id="priv_scheduler_demo" name="priv_scheduler_demo" '.$chk_priv_scheduler_demo.$chk_disabled.' value="1"><label for="priv_scheduler_demo">Scheduler/Demo</label></div></td>

										<td style="width:14%;"><div class="checkbox"><input type="checkbox" title="Sch. Override" name="priv_Sch_Override" id="priv_Sch_Override" value="1" '.$chk_Sch_Override.$chk_disabled.' ><label for="priv_Sch_Override">Sch. Override</label></div></td>

										<td style="width:14%;"><div class="checkbox"><input type="checkbox" name="priv_pt_Override" id="priv_pt_Override" value="1" '.$chk_pt_Override.$chk_disabled.' ><label for="priv_pt_Override">Pt. Override</label></div></td>

										<td style="width:20%;"><div class="checkbox"><input type="checkbox" id="priv_sch_lock_block" name="priv_sch_lock_block" value="1" '.$chk_sch_lock_block.'><label for="priv_sch_lock_block">Lock/Block Schedule</label></div></td>

										<td style="width:20%;"><div class="checkbox"><input type="checkbox" id="priv_sch_telemedicine" name="priv_sch_telemedicine" value="1" '.$chk_sch_telemedicine.'><label for="priv_sch_telemedicine">Telemedicine</label></div></td>

										<td style="width:auto;"><div class="checkbox"><input type="checkbox" id="priv_vo_pt_info" name="priv_vo_pt_info" value="1" '.$chk_vo_pt_info.$chk_disabled.'><label for="priv_vo_pt_info">View-Only</label></div></td>

								</tr>
							</table>
						</div>

						<div id="div_acc_bll" class="col-xs-12 recbox auto_height" >
							<div class="head"><span>Account/Billing</span></div>
							<table class="table">
								<tr>
									<td style="width:15%;"><div class="checkbox"><input type="checkbox" title="Manager" name="priv_ac_bill_manager" id="priv_ac_bill_manager" value="1" '.$chk_priv_ac_bill_manager.$chk_disabled.' onclick="javascript:selectDeselect_all(\'div_acc_bll\',this.checked);"><label for="priv_ac_bill_manager">Manager</label></div></td>
									<td style="width:15%;"><div class="checkbox"><input type="checkbox" title="Accounting" name="priv_Accounting" id="priv_Accounting" value="1" '.$chk_Accounting.$chk_disabled.' ><label for="priv_Accounting">Accounting</label></div></td>
									<td style="width:15%;"><div class="checkbox"><input type="checkbox" title="Billing" name="priv_Billing" id="priv_Billing" value="1" '.$chk_Billing.'><label for="priv_Billing">Billing</label></div></td>

									<td style="width:15%;"><div class="checkbox"><input type="checkbox" name="priv_edit_financials" id="priv_edit_financials" value="1" '.$chk_edit_financials.' ><label for="priv_edit_financials">Edit Financials</label></div></td>
									<td style="width:20%;"><div class="checkbox"><input type="checkbox" title="Ins. Management" name="priv_ins_management" id="priv_ins_management" value="1" '.$chk_priv_ins_management.' ><label for="priv_ins_management">Ins. Management</label></div></td>
									<td style="width:20%;"><div class="checkbox"><input type="checkbox" title="Account History" name="priv_acchx" id="priv_acchx" value="1" '.$chk_acchx.$chk_disabled.'><label for="priv_acchx">Account History</label></div></td>
								</tr>

								<tr>
										<td style="width:15%;"><div class="checkbox"><input type="checkbox" title="Charges" name="priv_vo_charges" id="priv_vo_charges" value="1" '.$chk_vo_charges.$chk_disabled.' ><label for="priv_vo_charges">Charges</label></div></td>

										<td style="width:15%;"><div class="checkbox"><input type="checkbox" title="Payment" name="priv_vo_payment" id="priv_vo_payment" value="1" '.$chk_vo_payment.$chk_disabled.' ><label for="priv_vo_payment">Payment</label></div></td>

										<td style="width:15%;"><div class="checkbox"><input type="checkbox" name="priv_bi_statements" id="priv_bi_statements" value="1" '.$chk_bi_statements.$chk_disabled.'><label for="priv_bi_statements">Statements</label></div></td>

                                        <td style="width:15%;"><div class="checkbox"><input type="checkbox" name="priv_bi_day_chrg_rept" id="priv_bi_day_chrg_rept" value="1" '.$chk_priv_bi_day_chrg_rept.'><label for="priv_bi_day_chrg_rept">Day Charges</label></div></td>

										<td style="width:35%;" colspan="2" class="hide"><div class="checkbox"><input type="checkbox" title="Delete Payments" name="priv_del_payment" id="priv_del_payment" value="1" '.$chk_priv_del_payment.' ><label for="priv_del_payment">Delete Payments</label></div></td>
										<td style="width:20%;" class="hide"><div class="checkbox"><input type="checkbox" title="Delete Charges/Enc" name="priv_del_charges_enc" id="priv_del_charges_enc" value="1" '.$chk_priv_del_charges_enc.'><label for="priv_del_charges_enc">Delete Charges/Enc</label></div></td>
								</tr>
							</table>
						</div>

                        <div id="div_Reports_n_reports" class="col-xs-12 recbox auto_height el_main_priv" >
                            <div class="head"><span>Reports</span>  &nbsp; <div class="checkbox checkbox-inline" style="text-transform:none;padding-left:25px;"><input type="checkbox" title="Manager"  name="priv_Reports_manager" id="priv_Reports_manager" class="priv_all_settings" value="1" '.$chk_priv_Reports_manager .' onclick="javascript:selectDeselect_all(\'div_Reports_n_reports\',this.checked);"><label for="priv_Reports_manager">Manager</label></div> </div>
							<table class="table privTable">
								<tr>
                                    <td style="width:160px;"><div class="checkbox"><input type="checkbox" title="Scheduler" name="priv_sc_scheduler" id="priv_sc_scheduler" value="1" '.$chk_sc_scheduler.'><label for="priv_sc_scheduler">Scheduler</label><i class="glyphicon glyphicon-menu-down pull-right pointer" data-whatever="Scheduler" data-showdiv="adminScheduler" data-toggle="modal" data-target="#priv_div_modal" id="spandiv_Scheduler"></i></div></td>
                                    <td style="width:180px;"><div class="checkbox"><input type="checkbox" title="Practice Analytics" name="priv_report_practice_analytics" id="priv_report_practice_analytics" value="1" '.$chk_priv_report_practice_analytics.' ><label for="priv_report_practice_analytics">Practice&nbsp;Analytics</label><i class="glyphicon glyphicon-menu-down pull-right pointer" data-whatever="Practice_Analytics" data-showdiv="adminPractice_Analytics" data-toggle="modal" data-target="#priv_div_modal" id="spandiv_Practice_Analytics"></i></div></td>
                                    <!-- <td style="width:180px;"><div class="checkbox"><input type="checkbox" title="Practice Analytics" name="priv_report_practice_analytics" id="priv_report_practice_analytics" value="1" '.$chk_priv_report_practice_analytics.' ><label for="priv_report_practice_analytics">Practice&nbsp;Analytics</label></div></td> -->
                                    <td style="width:160px;"><div class="checkbox"><input type="checkbox" title="Financials" name="priv_report_financials" id="priv_report_financials" value="1"  '.$chk_priv_report_financials.' ><label for="priv_report_financials">Financials</label><i class="glyphicon glyphicon-menu-down pull-right pointer" data-whatever="Financials" data-showdiv="adminFinancials" data-toggle="modal" data-target="#priv_div_modal" id="spandiv_Financials"></i></div></td>
                                    <td style="width:160px;"><div class="checkbox"><input type="checkbox" title="Compliance" name="priv_report_compliance" id="priv_report_compliance" value="1"  '.$chk_priv_report_compliance.' ><label for="priv_report_compliance">Compliance</label><i class="glyphicon glyphicon-menu-down pull-right pointer" data-whatever="Compliance" data-showdiv="adminCompliance" data-toggle="modal" data-target="#priv_div_modal" id="spandiv_Compliance"></i></div></td>
                                    <td style="width:160px;"><div class="checkbox"><input type="checkbox" title="CCD" name="priv_cl_ccd" id="priv_cl_ccd" value="1" '.$chk_cl_ccd.'><label for="priv_cl_ccd">CCD</label><i class="glyphicon glyphicon-menu-down pull-right pointer" data-whatever="CCD" data-showdiv="adminCCD" data-toggle="modal" data-target="#priv_div_modal" id="spandiv_CCD"></i></div></td>
                                    <td style="width:160px;"><div class="checkbox"><input type="checkbox" title="API Access" name="priv_report_api_access" id="priv_report_api_access" value="1" '.$chk_priv_report_api_access.'><label for="priv_report_api_access">API</label><i class="glyphicon glyphicon-menu-down pull-right pointer" data-whatever="API" data-showdiv="adminAPI" data-toggle="modal" data-target="#priv_div_modal" id="spandiv_API"></i></div></td>
								</tr>
								<tr>
                                    <td style="width:160px;"><div class="checkbox"><input type="checkbox" title="State" name="priv_report_State" id="priv_report_State" value="1" '.$chk_priv_report_State.'><label for="priv_report_State">State</label><i class="glyphicon glyphicon-menu-down pull-right pointer" data-whatever="State" data-showdiv="adminState" data-toggle="modal" data-target="#priv_div_modal" id="spandiv_State"></i></div></td>
                                    <td style="width:180px;"><div class="checkbox"><input type="checkbox" title="Optical" name="priv_report_optical" id="priv_report_optical" value="1" '.$chk_priv_report_optical.'><label for="priv_report_optical">Optical</label><i class="glyphicon glyphicon-menu-down pull-right pointer" data-whatever="Optical" data-showdiv="adminOptical" data-toggle="modal" data-target="#priv_div_modal" id="spandiv_Optical"></i></div></td>
                                    <td style="width:160px;"><div class="checkbox"><input type="checkbox" title="House Calls" name="priv_sc_house_calls" id="priv_sc_house_calls" value="1" '.$chk_sc_house_calls.'><label for="priv_sc_house_calls">Reminders</label><i class="glyphicon glyphicon-menu-down pull-right pointer" data-whatever="Reminders" data-showdiv="adminReminders" data-toggle="modal" data-target="#priv_div_modal" id="spandiv_Reminders"></i></div></td>
                                    <td style="width:160px;"><div class="checkbox"><input type="checkbox" title="Clinical" name="priv_cl_clinical" id="priv_cl_clinical" value="1" '.$chk_cl_clinical.'><label for="priv_cl_clinical">Clinical</label><i class="glyphicon glyphicon-menu-down pull-right pointer" data-whatever="ReportClinical" data-showdiv="adminReportClinical" data-toggle="modal" data-target="#priv_div_modal" id="spandiv_ReportClinical"></i></div></td>
                                    <td style="width:160px;"><div class="checkbox"><input type="checkbox" title="Rules" name="priv_report_Rules" id="priv_report_Rules" value="1" '.$chk_priv_report_Rules.'><label for="priv_report_Rules">Rules</label><i class="glyphicon glyphicon-menu-down pull-right pointer" data-whatever="Rules" data-showdiv="adminRules" data-toggle="modal" data-target="#priv_div_modal" id="spandiv_Rules"></i></div></td>
                                    <td style="width:160px;"><div class="checkbox"><input type="checkbox" title="iPortal" name="priv_report_iPortal" id="priv_report_iPortal" value="1" '.$chk_priv_report_iPortal.'><label for="priv_report_iPortal">iPortal</label><i class="glyphicon glyphicon-menu-down pull-right pointer" data-whatever="ReportiPortal" data-showdiv="adminReportiPortal" data-toggle="modal" data-target="#priv_div_modal" id="spandiv_ReportiPortal"></i></div></td>
								</tr>
                            </table>
												</div>



					<div id="div_Reports_pt_portal" class="col-xs-12 recbox auto_height" >
							<div class="head"><span>Pt Portal</span></div>
							<table class="table">
								<tr>
									<td style="width:20%;"><div class="checkbox"><input type="checkbox" title="Front Desk" name="priv_pt_fdsk" id="priv_pt_fdsk" value="1" '.$chk_priv_pt_fdsk.'><label for="priv_pt_fdsk">Front Desk</label></div></td>
									<td style="width:20%;"><div class="checkbox"><input type="checkbox" title="Clinical" name="priv_pt_clinical" id="priv_pt_clinical" value="1" '.$chk_priv_pt_clinical.'><label for="priv_pt_clinical">Clinical</label></div></td>
									<td style="width:20%;"><div class="checkbox"><input type="checkbox" title="Clinical" '.$chk_pt_coordinator.' name="priv_pt_coordinate" id="priv_pt_coordinate" value="1"><label for="priv_pt_coordinate">Message Coordinator</label></div></td>
									<td style="width:40%;">&nbsp;</td>
								</tr>
							</table>
						</div>

						<div id="div_Reports_pt_icons" class="col-xs-12 recbox auto_height" >
							<div class="head"><span>Icons</span></div>
							<table class="table">
								<tr>
									<td style="width:20%;"><div class="checkbox"><input type="checkbox" title="iMedicMonitor" name="priv_pt_icon_imm" id="priv_pt_icon_imm" value="1" '.$chk_priv_pt_icon_imm.'><label for="priv_pt_icon_imm">iMedicMonitor</label></div></td>
									<td style="width:20%;"><div class="checkbox"><input type="checkbox" title="Optical" name="priv_pt_icon_optical" id="priv_pt_icon_optical" value="1" '.$chk_priv_pt_icon_optical.'><label for="priv_pt_icon_optical">Optical</label></div></td>

									<td style="width:20px;"><div class="checkbox"><input type="checkbox" title="iASC Link" name="priv_pt_icon_iasclink" id="priv_pt_icon_iasclink" value="1" '.$chk_priv_pt_icon_iasclink.'><label for="priv_pt_icon_iasclink">iASC Link</label></div></td>

									<td style="width:20px;"><div class="checkbox"><input type="checkbox" title="Financial Dashboard" name="priv_financial_dashboard" id="priv_financial_dashboard" value="1" '.$chk_priv_financial_dashboard.'><label for="priv_financial_dashboard">Financial Dashboard</label></div></td>

									<td style="width:20px;"><div class="checkbox"><input type="checkbox" title="Support" name="priv_pt_icon_support" id="priv_pt_icon_support" value="1" '.$chk_priv_pt_icon_support.'><label for="priv_pt_icon_support">Support</label></div></td>
								</tr>
							</table>
						</div>
					</div>

				</div>
				<div id="pri_btn_con" class="modal-footer"></div>
			</div>
     	</div>
   	</div>

    <div id="priv_div_modal" class="modal" role="dialog" style="margin-top:50px;">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary">
                    <button type="button" class="close" data-dismiss="modal" id="close_priv_modal" onclick="resotreOldpriv();">x</button>
                    <h4 class="modal-title" id="modal_title"> Privileges</h4>
                </div>
                <div class="modal-body pd5" style="overflow:hidden; overflow-y:auto;">
                    <style>.checkbox.checkboxcolor input[type="checkbox"]:checked + label::before {background-color:#f4b81f!important;border-color:#f4b81f!important;}</style>
                    <div class="pd10">
                        <input type="hidden" name="popupsection" id="popupsection" value="" />
                        <input type="hidden" name="popupoldvals" id="popupoldvals" value="" />
                        <div class="checkbox checkbox-inline"><input type="checkbox" title="Select All" name="priv_select_all" id="priv_select_all" ><label for="priv_select_all">Select All</label></div>
                    </div>
                    <div id="allprivdivs">'.$setting_str.'</div>
                </div>
                <div id="module_buttons" class="modal-footer ad_modal_footer">
                    <div class="row mdl_btns_dp">
                        <div class="col-sm-12 text-center">
                            <button type="button" class="btn btn-success" data-dismiss="modal" onclick="privcheckboxcolor();">Done</button>
                            <button type="button" class="btn btn-danger" data-dismiss="modal" onclick="resotreOldpriv();">Close</button>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <script>
        //Binding Events on checkbox checked
        $(".privTable input[type=checkbox]").on("click", function(){
            if($(this).is(":checked") == true) {
                $(this).siblings("i").trigger("click");
            }
        });


        $("#priv_div_modal").on("shown.bs.modal", function(event) {
            $(".adminPrivDiv").hide();

            var button = $(event.relatedTarget);
            var recipient = button.data("whatever");
            var showdiv = button.data("showdiv");
            if(showdiv) {
                $("#"+showdiv).show();
            }
            var modal = $(this);
            var title_recipient=recipient.replace("_", " ");
            modal.find(".modal-title").text(title_recipient+" Privileges");
            modal.find("#popupsection").val(recipient);

            if ($("#admin"+recipient).find("input:checkbox:not(:checked)").length == 0) {
                $("#priv_select_all").prop("checked", true);
            } else {
                $("#priv_select_all").prop("checked", false);
            }
            var oldvals="";
            $($("#admin"+recipient).find("input:checkbox")).each(function(key, elem) {
                if($("#"+elem.id).is(":checked")) {
                    oldvals+=elem.id+"::"+1+",";
                } else {
                    oldvals+=elem.id+"::"+0+",";
                }
            });
            document.getElementById("popupoldvals").value = oldvals;

        });

        function resotreOldpriv() {
            var oldvals = document.getElementById("popupoldvals").value;
            var result = oldvals.split(",");
            $(result).each(function(key, valu) {
                var privvalu = valu.split("::");
                if(privvalu[1]==1) {
                    $("#"+privvalu[0]).prop("checked", true);
                } else {
                    $("#"+privvalu[0]).prop("checked", false);
                }
            });
        }

        $("#priv_select_all").on("click", function() {
            var section = $("#popupsection").val();
            if($("#priv_select_all").is(":checked")) {
                $("#admin"+section).find("input:checkbox").prop("checked", true);
            } else {
                $("#admin"+section).find("input:checkbox").prop("checked", false);
            }
        });

        $(".priv_all_settings").on("click", function() {
            var parentId = $(this).closest(".el_main_priv").attr("id");

            var propVal = false;
            if($(this).is(":checked")) {
                propVal = true;
            }

            var elemLength = $("#allprivdivs").find("[data-parent=\""+parentId+"\"]");
            //if(elemLength > 0){
                $("#allprivdivs").find("[data-parent=\""+parentId+"\"]").find("input[type=checkbox]").prop("checked", propVal);
            //}

            privcheckboxcolor();
        });

        $(".privoptioncheck").on("click", function() {
            if($(".privoptioncheck").not(":checked")) {
                $("#priv_select_all").prop("checked", false);
                //$("#priv_all_settings").prop("checked", false);
            }
        });

        $("#new_priv_div").on("shown.bs.modal", function(event) {
             privcheckboxcolor();
        });

        function privcheckboxcolor() {
            $(".el_main_priv").each(function(key, parentelm) {
                var parentelem=$(parentelm);
                $("#allprivdivs").children().each(function(key, elem) {
                var id=elem.id;
                if ($("#"+id).find("input:checkbox:not(:checked)").length == 0 ) {
                    NewString = id.replace("admin", "");
                        parentelem.find("input[type=checkbox]").each(function(key1, elem1) {
                        var id1 = elem1.id

                        var section1 = $("#"+id1).parent("div").children("i").data("whatever");
                        if(section1==NewString) {
                            $("#"+id1).parent("div").removeClass("checkboxcolor");
                        }
                    });
                } else {
                    NewString = id.replace("admin", "");
                        parentelem.find("input[type=checkbox]").each(function(key1, elem1) {
                        var id1 = elem1.id

                        var section1 = $("#"+id1).parent("div").children("i").data("whatever");
                        if(section1==NewString) {
                            $("#"+id1).parent("div").addClass("checkboxcolor");
                        }
                    });
                }
            });
            });
        }
    </script>
';*/

		if($intAuditOnOff == 1){
			$return .= "<input type=\"hidden\" name=\"hidDataProvider\" value=\"".$this->providers_audit($pro_id, $arrAuditUserDetails)."\">";
		}else{
			$return .= "<input type=\"hidden\" name=\"hidDataProvider\" value=\"no_audit\">";
		}

		$return .= '</form>';
		$return .= '<script>disp_providers();</script>';		
		return $return;
	}

	public function providers_save(){
		$pro_id = "";
		$erx_msg = "";
		$return_err = array();
		if(isset($_REQUEST["pro_id"]) && !empty($_REQUEST["pro_id"])){
			$pro_id = $_REQUEST["pro_id"];
		}
		$pro_id=imw_real_escape_string($pro_id);

		if($_REQUEST['show_prov_drop']=="deleted"){
			$srh_del_status_inc = "srh_del_status=1&";
		}

		$str_query_string = "";
		if(is_array($_REQUEST) && count($_REQUEST) > 0){
			foreach($_REQUEST as $k=>$v){
				if($k != "hidDataProvider" && $k != "elem_sign" && $k != "pro_type" && $k != "getlink" && $k != "access_pri_audit")
					$str_query_string .= "&".$k."=".$v;
			}
		}

		//check for duplicate NPI
		$provider_type_get="";
		list($provider_type_get)=explode("-",$_REQUEST["pro_type"]);
		$arrDupNPI = $this->checkDuplicateNPI($_REQUEST["user_npi"],$pro_id);
		$arrImwKey=true;
		if(trim($_REQUEST["imw_key"]) || trim($_REQUEST["imw_key"])=="0" ){
			$arrImwKey = $this->check_imw_key($_REQUEST["user_npi"],$_REQUEST["imw_key"],$pro_id);
		}
		if(is_array($arrDupNPI) && count($arrDupNPI) > 0){
			if($pro_id != ""){
				$return_err['page_url'] = 'index.php?'.$srh_del_status_inc.'pro_id='.$pro_id;
				$return_err['err'] = trim($this->get_vocabulary("admin", "poviders","voc_npi_exists"));
				$return_err['focus'] = 'pro_user';
				return json_encode($return_err);
				//$this->redirectTo("index.php?".$srh_del_status_inc."err=".trim($this->get_vocabulary("admin", "poviders","voc_npi_exists"))."&focus=pro_user&pro_id=".$pro_id);
			}else{
				$return_err['page_url'] = 'index.php?'.$srh_del_status_inc;
				$return_err['err'] = trim($this->get_vocabulary("admin", "poviders","voc_npi_exists"));
				//$return_err['focus'] = 'pro_user'.$str_query_string;
				$return_err['focus'] = 'pro_user';
				return json_encode($return_err);
				//$this->redirectTo("index.php?".$srh_del_status_inc."err=".trim($this->get_vocabulary("admin", "poviders","voc_npi_exists"))."&focus=pro_user".$str_query_string);
			}
		}else{
			if($arrImwKey==false && ($provider_type_get=='1' || $provider_type_get=='11' || $provider_type_get=='12')){
				$return_err['page_url'] = 'index.php?'.$srh_del_status_inc.'pro_id='.$pro_id;
				$return_err['err'] = trim($this->get_vocabulary("admin", "poviders","imw_key_err"));
				$return_err['focus'] = 'pro_user';
				return json_encode($return_err);
				//$this->redirectTo("index.php?".$srh_del_status_inc."err=".trim($this->get_vocabulary("admin", "poviders","imw_key_err"))."&focus=pro_user&pro_id=".$pro_id);
			}
		}


		//check for duplicate username
		if(isset($_REQUEST["pro_user"]) && !empty($_REQUEST["pro_user"])){

			if($pro_id != ""){
				//$arrDupRFUN = $this->checkDuplicateRefPhyUsername($_REQUEST["pro_user"], $pro_id);
				$arrDupUN = $this->checkDuplicateUsername($_REQUEST["pro_user"], $pro_id);
			}else{
				//$arrDupRFUN = $this->checkDuplicateRefPhyUsername($_REQUEST["pro_user"]);
				$arrDupUN = $this->checkDuplicateUsername($_REQUEST["pro_user"]);
			}


			if(is_array($arrDupRFUN) && count($arrDupRFUN) > 0){
				if($pro_id != ""){
					$return_err['page_url'] = 'index.php?'.$srh_del_status_inc.'pro_id='.$pro_id;
					$return_err['err'] = trim($this->get_vocabulary("admin", "poviders","voc_username_exists"));
					$return_err['focus'] = 'pro_user';
					return json_encode($return_err);
					//$this->redirectTo("index.php?".$srh_del_status_inc."err=".trim($this->get_vocabulary("admin", "poviders","voc_username_exists"))."&focus=pro_user&pro_id=".$pro_id);
				}else{
					$return_err['page_url'] = 'index.php?'.$srh_del_status_inc;
					$return_err['err'] = trim($this->get_vocabulary("admin", "poviders","voc_username_exists"));
					$return_err['focus'] = 'pro_user'.$str_query_string;
					return json_encode($return_err);
					//$this->redirectTo("index.php?".$srh_del_status_inc."err=".trim($this->get_vocabulary("admin", "poviders","voc_username_exists"))."&focus=pro_user".$str_query_string);
				}
			}

			if(is_array($arrDupUN) && count($arrDupUN) > 0){
				if($pro_id != ""){
					$return_err['page_url'] = 'index.php?'.$srh_del_status_inc.'pro_id='.$pro_id;
					$return_err['err'] = trim($this->get_vocabulary("admin", "poviders","voc_username_exists"));
					$return_err['focus'] = 'pro_user';
					return json_encode($return_err);
					//$this->redirectTo("index.php?".$srh_del_status_inc."err=".trim($this->get_vocabulary("admin", "poviders","voc_username_exists"))."&focus=pro_user&pro_id=".$pro_id);
				}else{
					$return_err['page_url'] = 'index.php?'.$srh_del_status_inc.'pro_id='.$pro_id;
					$return_err['err'] = trim($this->get_vocabulary("admin", "poviders","voc_username_exists"));
					$return_err['focus'] = 'pro_user';
					//$this->redirectTo("index.php?".$srh_del_status_inc."err=".trim($this->get_vocabulary("admin", "poviders","voc_username_exists"))."&focus=pro_user&pro_id=".$pro_id);
				}
			}

		}

		//CHECK IF FNAME AND LNAME NOT BLANK
		if(empty($_REQUEST["pro_fname"])==true || empty($_REQUEST["pro_lname"])==true){
			$return_err['page_url'] = 'index.php?'.$srh_del_status_inc;
			$return_err['err'] = trim($this->get_vocabulary("admin", "poviders","voc_first_last_name"));
			$return_err['focus'] = 'pro_user'.$str_query_string;
			return json_encode($return_err);
			//$this->redirectTo("index.php?".$srh_del_status_inc."err=".trim($this->get_vocabulary("admin", "poviders","voc_first_last_name"))."&focus=pro_user".$str_query_string);
		}

		$arrCols = array();
		$arrVals = array();

		//name
		if(isset($_REQUEST["pro_type"]) && !empty($_REQUEST["pro_type"])){
			$arr_temp_pro_type = explode("-",$_REQUEST["pro_type"]);
			$arrCols[] = "user_type";			$arrVals[] = $arr_temp_pro_type[0];
		}

		if(isset($_REQUEST["pro_group"]) && !empty($_REQUEST["pro_group"])){
			$arrCols[] = "user_group_id";			$arrVals[] = $_REQUEST["pro_group"];
		}

		if(get_magic_quotes_gpc()){
			$_REQUEST["pro_fname"]=stripslashes($_REQUEST["pro_fname"]);
			$_REQUEST["pro_mname"]=stripslashes($_REQUEST["pro_mname"]);
			$_REQUEST["pro_lname"]=stripslashes($_REQUEST["pro_lname"]);
			$_REQUEST["pro_nicname"]=stripslashes($_REQUEST["pro_nicname"]);
		}

		$arrCols[] = "pro_title";		$arrVals[] = $_REQUEST["pro_title"];
		$arrCols[] = "fname";				$arrVals[] = $_REQUEST["pro_fname"];
		$arrCols[] = "mname";				$arrVals[] = $_REQUEST["pro_mname"];
		$arrCols[] = "lname";				$arrVals[] = $_REQUEST["pro_lname"];
		$arrCols[] = "pro_suffix";	$arrVals[] = $_REQUEST["pro_suffix"];
		$arrCols[] = "nicname";			$arrVals[] = $_REQUEST["pro_nicname"];

		$pro_fname = $_REQUEST["pro_fname"];
		$pro_lname = $_REQUEST["pro_lname"];
		//access priv
		include_once($GLOBALS['srcdir']."/classes/admin/GroupPrevileges.php");
		$oGroupPrevileges = new GroupPrevileges();
		list($str_access_priv, $privileges) = $oGroupPrevileges->get_posted_previliges(1);

		/*
		$priv_clinial = 0;
		if(isset($_REQUEST["priv_clinical"])){
			$priv_clinial = $_REQUEST["priv_clinical"];
		}
		$priv_cdc=0;
		if(isset($_REQUEST["privilege_cdc"])){
			$priv_cdc=$_REQUEST["privilege_cdc"];
		}
		$priv_erx=0;
		if(isset($_REQUEST["erx_chk"])){
			$priv_erx=$_REQUEST["erx_chk"];
		}

		$priv_cl_work_view = 0;
		$priv_cl_tests = 0;
		$priv_cl_medical_hx = 0;
		$priv_chart_finalize = 1;

		if(trim($_REQUEST["priv_cl_work_view"])){
			$priv_cl_work_view = $_REQUEST["priv_cl_work_view"];
		}
		if(trim($_REQUEST["priv_cl_tests"])){
			$priv_cl_tests = $_REQUEST["priv_cl_tests"];
		}
		if(trim($_REQUEST["priv_cl_medical_hx"])){
			$priv_cl_medical_hx = $_REQUEST["priv_cl_medical_hx"];
		}


		if($provider_type_get=='11'){
            $priv_chart_finalize = 0;
            if(trim($_REQUEST["priv_chart_finalize"])){
                $priv_chart_finalize = $_REQUEST["priv_chart_finalize"];
            }
		}

		$priv_Front_Desk = 0;
		if(trim($_REQUEST["priv_Front_Desk"])){
			$priv_Front_Desk = $_REQUEST["priv_Front_Desk"];
		}

		$priv_sch_lock_block=0;
		if(trim($_REQUEST["priv_sch_lock_block"])){
			$priv_sch_lock_block = $_REQUEST["priv_sch_lock_block"];
		}

		$priv_sch_telemedicine=0;
		if(trim($_REQUEST["priv_sch_telemedicine"])){
			$priv_sch_telemedicine = $_REQUEST["priv_sch_telemedicine"];
		}

		$priv_scheduler_demo=0;
		if(trim($_REQUEST["priv_scheduler_demo"])){
			$priv_scheduler_demo = $_REQUEST["priv_scheduler_demo"];
		}
		$priv_Billing = 0;
		if(isset($_REQUEST["priv_Billing"])){
			$priv_Billing = $_REQUEST["priv_Billing"];
		}
		$priv_Accounting = 0;
		if(isset($_REQUEST["priv_Accounting"])){
			$priv_Accounting = $_REQUEST["priv_Accounting"];
		}

		if(isset($_REQUEST["priv_Acc_all"])){
			$priv_Acc_all = $_REQUEST["priv_Acc_all"];
		}
		if(isset($_REQUEST["priv_Acc_vonly"])){
			$priv_Acc_vonly = $_REQUEST["priv_Acc_vonly"];
		}

		$priv_Security = 0;
		if(isset($_REQUEST["priv_Security"])){
			$priv_Security = $_REQUEST["priv_Security"];
		}

		$priv_Reports_manager = 0;
		if(isset($_REQUEST["priv_Reports_manager"])){
			$priv_Reports_manager = $_REQUEST["priv_Reports_manager"];
		}

		$priv_sc_daily = 0;
		if(isset($_REQUEST["priv_sc_daily"])){
			$priv_sc_daily = $_REQUEST["priv_sc_daily"];
		}
		$priv_acct_receivable = 0;
		if(isset($_REQUEST["priv_acct_receivable"])){
			$priv_acct_receivable = $_REQUEST["priv_acct_receivable"];
		}
		$priv_bi_analytics = 0;
		if(isset($_REQUEST["priv_bi_analytics"])){
			$priv_bi_analytics = $_REQUEST["priv_bi_analytics"];
		}


		//========================//
		$priv_report_payments = 0;
		if(trim($_REQUEST["priv_report_payments"])){
			$priv_report_payments = $_REQUEST["priv_report_payments"];
		}

		$priv_report_copay_rocan = 0;
		if(trim($_REQUEST["priv_report_copay_rocan"])){
			$priv_report_copay_rocan = $_REQUEST["priv_report_copay_rocan"];
		}

		$priv_un_superbills = 0;
		if(trim($_REQUEST["priv_un_superbills"])){
			$priv_un_superbills = $_REQUEST["priv_un_superbills"];
		}

		$priv_un_encounters = 0;
		if(isset($_REQUEST["priv_un_encounters"])){
			$priv_un_encounters = $_REQUEST["priv_un_encounters"];
		}

		$priv_un_payments = 0;
		if(trim($_REQUEST["priv_un_payments"])){
			$priv_un_payments = $_REQUEST["priv_un_payments"];
		}

		$priv_report_adjustment = 0;
		if(trim($_REQUEST["priv_report_adjustment"])){
			$priv_report_adjustment = $_REQUEST["priv_report_adjustment"];
		}

		$priv_report_refund = 0;
		if(trim($_REQUEST["priv_report_refund"])){
			$priv_report_refund = $_REQUEST["priv_report_refund"];
		}

		$priv_daily_balance = 0;
		if(trim($_REQUEST["priv_daily_balance"])){
			$priv_daily_balance = $_REQUEST["priv_daily_balance"];
		}

		$priv_fd_collection = 0;
		if(trim($_REQUEST["priv_fd_collection"])){
			$priv_fd_collection = $_REQUEST["priv_fd_collection"];
		}

		$priv_report_practice_analytics = 0;
		if(trim($_REQUEST["priv_report_practice_analytics"])){
			$priv_report_practice_analytics = $_REQUEST["priv_report_practice_analytics"];
		}

		$priv_cpt_analysis = 0;
		if(isset($_REQUEST["priv_cpt_analysis"])){
			$priv_cpt_analysis = $_REQUEST["priv_cpt_analysis"];
		}

		$priv_report_yearly = 0;
		if(trim($_REQUEST["priv_report_yearly"])){
			$priv_report_yearly = $_REQUEST["priv_report_yearly"];
		}

		$priv_report_revenue = 0;
		if(trim($_REQUEST["priv_report_revenue"])){
			$priv_report_revenue = $_REQUEST["priv_report_revenue"];
		}

		$priv_provider_mon = 0;
		if(trim($_REQUEST["priv_provider_mon"])){
			$priv_provider_mon = $_REQUEST["priv_provider_mon"];
		}

		$priv_ref_phy_monthly = 0;
		if(trim($_REQUEST["priv_ref_phy_monthly"])){
			$priv_ref_phy_monthly = $_REQUEST["priv_ref_phy_monthly"];
		}

		$priv_facility_monthly = 0;
		if(trim($_REQUEST["priv_facility_monthly"])){
			$priv_facility_monthly = $_REQUEST["priv_facility_monthly"];
		}

		$priv_report_ref_phy = 0;
		if(trim($_REQUEST["priv_report_ref_phy"])){
			$priv_report_ref_phy = $_REQUEST["priv_report_ref_phy"];
		}

		$priv_credit_analysis = 0;
		if(trim($_REQUEST["priv_credit_analysis"])){
			$priv_credit_analysis = $_REQUEST["priv_credit_analysis"];
		}

		$priv_report_patient = 0;
		if(trim($_REQUEST["priv_report_patient"])){
			$priv_report_patient = $_REQUEST["priv_report_patient"];
		}

		$priv_report_ins_cases = 0;
		if(trim($_REQUEST["priv_report_ins_cases"])){
			$priv_report_ins_cases = $_REQUEST["priv_report_ins_cases"];
		}

		$priv_report_eid_status = 0;
		if(trim($_REQUEST["priv_report_eid_status"])){
			$priv_report_eid_status = $_REQUEST["priv_report_eid_status"];
		}

		$priv_allowable_verify = 0;
		if(trim($_REQUEST["priv_allowable_verify"])){
			$priv_allowable_verify = $_REQUEST["priv_allowable_verify"];
		}

		$priv_vip_deferred = 0;
		if(trim($_REQUEST["priv_vip_deferred"])){
			$priv_vip_deferred = $_REQUEST["priv_vip_deferred"];
		}

		$priv_provider_rvu = 0;
		if(trim($_REQUEST["priv_provider_rvu"])){
			$priv_provider_rvu = $_REQUEST["priv_provider_rvu"];
		}

		$priv_sx_payment = 0;
		if(trim($_REQUEST["priv_sx_payment"])){
			$priv_sx_payment = $_REQUEST["priv_sx_payment"];
		}

		$priv_net_gross = 0;
		if(trim($_REQUEST["priv_net_gross"])){
			$priv_net_gross = $_REQUEST["priv_net_gross"];
		}

		$priv_ar_reports = 0;
		if(trim($_REQUEST["priv_ar_reports"])){
			$priv_ar_reports = $_REQUEST["priv_ar_reports"];
		}

		$priv_days_ar = 0;
		if(trim($_REQUEST["priv_days_ar"])){
			$priv_days_ar = $_REQUEST["priv_days_ar"];
		}

		$priv_receivables = 0;
		if(trim($_REQUEST["priv_receivables"])){
			$priv_receivables = $_REQUEST["priv_receivables"];
		}

		$priv_unworked_ar = 0;
		if(trim($_REQUEST["priv_unworked_ar"])){
			$priv_unworked_ar = $_REQUEST["priv_unworked_ar"];
		}

		$priv_unbilled_claims = 0;
		if(trim($_REQUEST["priv_unbilled_claims"])){
			$priv_unbilled_claims = $_REQUEST["priv_unbilled_claims"];
		}

		$priv_top_rej_reason = 0;
		if(trim($_REQUEST["priv_top_rej_reason"])){
			$priv_top_rej_reason = $_REQUEST["priv_top_rej_reason"];
		}

		$priv_new_statements = 0;
		if(trim($_REQUEST["priv_new_statements"])){
			$priv_new_statements = $_REQUEST["priv_new_statements"];
		}

		$priv_prev_statements = 0;
		if(trim($_REQUEST["priv_prev_statements"])){
			$priv_prev_statements = $_REQUEST["priv_prev_statements"];
		}

		$priv_prev_hcfa = 0;
		if(trim($_REQUEST["priv_prev_hcfa"])){
			$priv_prev_hcfa = $_REQUEST["priv_prev_hcfa"];
		}

		$priv_statements_pay = 0;
		if(trim($_REQUEST["priv_statements_pay"])){
			$priv_statements_pay = $_REQUEST["priv_statements_pay"];
		}

		$priv_pt_statements = 0;
		if(trim($_REQUEST["priv_pt_statements"])){
			$priv_pt_statements = $_REQUEST["priv_pt_statements"];
		}

		$priv_pt_collections = 0;
		if(trim($_REQUEST["priv_pt_collections"])){
			$priv_pt_collections = $_REQUEST["priv_pt_collections"];
		}

		$priv_assessment = 0;
		if(trim($_REQUEST["priv_assessment"])){
			$priv_assessment = $_REQUEST["priv_assessment"];
		}

		$priv_collection_report = 0;
		if(trim($_REQUEST["priv_collection_report"])){
			$priv_collection_report = $_REQUEST["priv_collection_report"];
		}

		$priv_tfl_proof = 0;
		if(trim($_REQUEST["priv_tfl_proof"])){
			$priv_tfl_proof = $_REQUEST["priv_tfl_proof"];
		}

		$priv_report_rta = 0;
		if(trim($_REQUEST["priv_report_rta"])){
			$priv_report_rta = $_REQUEST["priv_report_rta"];
		}

		$priv_billing_verification = 0;
		if(trim($_REQUEST["priv_billing_verification"])){
			$priv_billing_verification = $_REQUEST["priv_billing_verification"];
		}

		$priv_patient_status = 0;
		if(trim($_REQUEST["priv_patient_status"])){
			$priv_patient_status = $_REQUEST["priv_patient_status"];
		}

		$priv_saved_scheduled = 0;
		if(trim($_REQUEST["priv_saved_scheduled"])){
			$priv_saved_scheduled = $_REQUEST["priv_saved_scheduled"];
		}

		$priv_executed_report = 0;
		if(trim($_REQUEST["priv_executed_report"])){
			$priv_executed_report = $_REQUEST["priv_executed_report"];
		}

		$priv_cn_pending = 0;
		if(trim($_REQUEST["priv_cn_pending"])){
			$priv_cn_pending = $_REQUEST["priv_cn_pending"];
		}

		$priv_contact_lens = 0;
		if(trim($_REQUEST["priv_contact_lens"])){
			$priv_contact_lens = $_REQUEST["priv_contact_lens"];
		}

		$priv_cn_ordered = 0;
		if(trim($_REQUEST["priv_cn_ordered"])){
			$priv_cn_ordered = $_REQUEST["priv_cn_ordered"];
		}

		$priv_cn_received = 0;
		if(trim($_REQUEST["priv_cn_received"])){
			$priv_cn_received = $_REQUEST["priv_cn_received"];
		}

		$priv_cn_dispensed = 0;
		if(trim($_REQUEST["priv_cn_dispensed"])){
			$priv_cn_dispensed = $_REQUEST["priv_cn_dispensed"];
		}

		$priv_cn_reports = 0;
		if(trim($_REQUEST["priv_cn_reports"])){
			$priv_cn_reports = $_REQUEST["priv_cn_reports"];
		}

		$priv_glasses = 0;
		if(trim($_REQUEST["priv_glasses"])){
			$priv_glasses = $_REQUEST["priv_glasses"];
		}
		$priv_gl_pending = 0;
		if(trim($_REQUEST["priv_gl_pending"])){
			$priv_gl_pending = $_REQUEST["priv_gl_pending"];
		}

		$priv_gl_ordered = 0;
		if(trim($_REQUEST["priv_gl_ordered"])){
			$priv_gl_ordered = $_REQUEST["priv_gl_ordered"];
		}

		$priv_gl_received = 0;
		if(trim($_REQUEST["priv_gl_received"])){
			$priv_gl_received = $_REQUEST["priv_gl_received"];
		}

		$priv_gl_dispensed = 0;
		if(trim($_REQUEST["priv_gl_dispensed"])){
			$priv_gl_dispensed = $_REQUEST["priv_gl_dispensed"];
		}

		$priv_gl_report = 0;
		if(trim($_REQUEST["priv_gl_report"])){
			$priv_gl_report = $_REQUEST["priv_gl_report"];
		}

		$priv_alerts = 0;
		if(trim($_REQUEST["priv_alerts"])){
			$priv_alerts = $_REQUEST["priv_alerts"];
		}
		$priv_stage_iv = 0;
		if(trim($_REQUEST["priv_stage_iv"])){
			$priv_stage_iv = $_REQUEST["priv_stage_iv"];
		}
		$priv_stage_i = 0;
		if(trim($_REQUEST["priv_stage_i"])){
			$priv_stage_i = $_REQUEST["priv_stage_i"];
		}
		$priv_stage_ii = 0;
		if(trim($_REQUEST["priv_stage_ii"])){
			$priv_stage_ii = $_REQUEST["priv_stage_ii"];
		}

		$priv_stage_iii = 0;
		if(trim($_REQUEST["priv_stage_iii"])){
			$priv_stage_iii = $_REQUEST["priv_stage_iii"];
		}

		$priv_ccd_export = 0;
		if(trim($_REQUEST["priv_ccd_export"])){
			$priv_ccd_export = $_REQUEST["priv_ccd_export"];
		}

		$priv_ccd_import = 0;
		if(trim($_REQUEST["priv_ccd_import"])){
			$priv_ccd_import = $_REQUEST["priv_ccd_import"];
		}

		$priv_lab_import = 0;
		if(trim($_REQUEST["priv_lab_import"])){
			$priv_lab_import = $_REQUEST["priv_lab_import"];
		}

		$priv_ccr_import = 0;
		if(trim($_REQUEST["priv_ccr_import"])){
			$priv_ccr_import = $_REQUEST["priv_ccr_import"];
		}

		$priv_dat_appts = 0;
		if(trim($_REQUEST["priv_dat_appts"])){
			$priv_dat_appts = $_REQUEST["priv_dat_appts"];
		}

		$priv_recalls = 0;
		if(trim($_REQUEST["priv_recalls"])){
			$priv_recalls = $_REQUEST["priv_recalls"];
		}

		$priv_reminder_lists = 0;
		if(trim($_REQUEST["priv_reminder_lists"])){
			$priv_reminder_lists = $_REQUEST["priv_reminder_lists"];
		}

		$priv_no_shows = 0;
		if(trim($_REQUEST["priv_no_shows"])){
			$priv_no_shows = $_REQUEST["priv_no_shows"];
		}

		$ccr_exist_pat = 0;
		if(trim($_REQUEST["ccr_exist_pat"])){
			$ccr_exist_pat = $_REQUEST["ccr_exist_pat"];
		}

		$ccr_new_pat = 0;
		if(trim($_REQUEST["ccr_new_pat"])){
			$ccr_new_pat = $_REQUEST["ccr_new_pat"];

		}

		//========================//
		$priv_sc_scheduler = 0;
		$priv_sc_house_calls = 0;
		$priv_sc_recall_fulfillment = 0;

		$priv_bi_front_desk = 0;
		$priv_bi_ledger = 0;
		$priv_bi_prod_payroll = 0;
		$priv_bi_ar = 0;
		$priv_bi_statements = 0;
		$priv_bi_day_chrg_rept = 0;
		$priv_financial_hx_cpt = 0;
		$priv_purge_del_chart = 0;
		$priv_bi_end_of_day = 0;

		$priv_cl_clinical = 0;
		$priv_cl_visits = 0;
		$priv_cl_ccd = 0;
		$priv_cl_order_set = 0;

		if(isset($_REQUEST["priv_sc_scheduler"])){
			$priv_sc_scheduler = $_REQUEST["priv_sc_scheduler"];
		}
		if(isset($_REQUEST["priv_sc_house_calls"])){
			$priv_sc_house_calls = $_REQUEST["priv_sc_house_calls"];
		}
		$priv_billing_fun=0;
		if(isset($_REQUEST["priv_billing_fun"])){
			$priv_billing_fun = $_REQUEST["priv_billing_fun"];
		}
		if(isset($_REQUEST["priv_sc_recall_fulfillment"])){
			$priv_sc_recall_fulfillment = $_REQUEST["priv_sc_recall_fulfillment"];
		}

		if(isset($_REQUEST["priv_bi_front_desk"])){
			$priv_bi_front_desk = $_REQUEST["priv_bi_front_desk"];
		}
		if(isset($_REQUEST["priv_bi_ledger"])){
			$priv_bi_ledger = $_REQUEST["priv_bi_ledger"];
		}
		if(isset($_REQUEST["priv_bi_prod_payroll"])){
			$priv_bi_prod_payroll = $_REQUEST["priv_bi_prod_payroll"];
		}
		if(isset($_REQUEST["priv_bi_ar"])){

			$priv_bi_ar = $_REQUEST["priv_bi_ar"];
		}
		if(isset($_REQUEST["priv_bi_statements"])){
			$priv_bi_statements = $_REQUEST["priv_bi_statements"];
		}
		if(isset($_REQUEST["priv_bi_day_chrg_rept"])){
			$priv_bi_day_chrg_rept = $_REQUEST["priv_bi_day_chrg_rept"];
		}
		if(isset($_REQUEST["priv_financial_hx_cpt"])){
			$priv_financial_hx_cpt = $_REQUEST["priv_financial_hx_cpt"];
		}
		if(isset($_REQUEST["priv_purge_del_chart"])){
			$priv_purge_del_chart = $_REQUEST["priv_purge_del_chart"];
		}
		if(isset($_REQUEST["priv_bi_end_of_day"])){
			$priv_bi_end_of_day = $_REQUEST["priv_bi_end_of_day"];
		}

		if(isset($_REQUEST["priv_cl_clinical"])){
			$priv_cl_clinical = $_REQUEST["priv_cl_clinical"];
		}
		if(isset($_REQUEST["priv_cl_visits"])){
			$priv_cl_visits = $_REQUEST["priv_cl_visits"];
		}
		if(isset($_REQUEST["priv_cl_ccd"])){
			$priv_cl_ccd = $_REQUEST["priv_cl_ccd"];
		}
		if(isset($_REQUEST["priv_cl_order_set"])){
			$priv_cl_order_set = $_REQUEST["priv_cl_order_set"];
		}
		if(isset($_REQUEST["priv_no_reports"])){
			$priv_no_reports = $_REQUEST["priv_no_reports"];
		}


		$priv_View_Only = 0;
		if(isset($_REQUEST["priv_View_Only"])){
			$priv_View_Only = $_REQUEST["priv_View_Only"];
		}

		$priv_vo_clinical = 0;
		$priv_vo_pt_info = 0;
		$priv_vo_acc = 0;
		$priv_vo_charges = 0;
		$priv_vo_payment = 0;

		if(isset($_REQUEST["priv_vo_clinical"])){
			$priv_vo_clinical = $_REQUEST["priv_vo_clinical"];
		}

		if(isset($_REQUEST["priv_vo_pt_info"])){
			$priv_vo_pt_info = $_REQUEST["priv_vo_pt_info"];
		}
		if(isset($_REQUEST["priv_vo_acc"])){
			$priv_vo_acc = $_REQUEST["priv_vo_acc"];
		}
		if(isset($_REQUEST["priv_vo_charges"])){
			$priv_vo_charges = $_REQUEST["priv_vo_charges"];
		}
		if(isset($_REQUEST["priv_vo_payment"])){
			$priv_vo_payment = $_REQUEST["priv_vo_payment"];
		}
		$priv_del_charges_enc=0;
		if(isset($_REQUEST["priv_del_charges_enc"])){
			$priv_del_charges_enc = $_REQUEST["priv_del_charges_enc"];
		}

		$priv_del_payment=0;
		if(isset($_REQUEST["priv_del_payment"])){
			$priv_del_payment = $_REQUEST["priv_del_payment"];
		}
		$priv_Sch_Override = 0;
		if(isset($_REQUEST["priv_Sch_Override"])){
			$priv_Sch_Override = $_REQUEST["priv_Sch_Override"];
		}
		$priv_pt_Override = 0;
		if(isset($_REQUEST["priv_pt_Override"])){
			$priv_pt_Override = $_REQUEST["priv_pt_Override"];
		}

		$priv_ac_bill_manager=0;
		if(isset($_REQUEST["priv_ac_bill_manager"])){
			$priv_ac_bill_manager = $_REQUEST["priv_ac_bill_manager"];
		}
		$priv_admin = 0;
		if(isset($_REQUEST["priv_admin"])){
			$priv_admin = $_REQUEST["priv_admin"];
		}
		$priv_all_settings = 0;
		if(isset($_REQUEST["priv_all_settings"])){
			$priv_all_settings = $_REQUEST["priv_all_settings"];
		}

		$priv_group=0;
		if(isset($_REQUEST["priv_group"])){
			$priv_group = $_REQUEST["priv_group"];
		}
		$priv_facility=0;
		if(isset($_REQUEST["priv_facility"])){
			$priv_facility = $_REQUEST["priv_facility"];
		}
		$priv_document=0;
		if(isset($_REQUEST["priv_document"])){
			$priv_document = $_REQUEST["priv_document"];
		}
		$priv_iols=0;
		if(isset($_REQUEST["priv_iols"])){
			$priv_iols = $_REQUEST["priv_iols"];
		}
		$priv_console=0;
		if(isset($_REQUEST["priv_console"])){
			$priv_console = $_REQUEST["priv_console"];
		}
		$priv_report_financials=0;
		if(isset($_REQUEST["priv_report_financials"])){
			$priv_report_financials = $_REQUEST["priv_report_financials"];
		}
		$priv_report_tests=0;
		if(isset($_REQUEST["priv_report_tests"])){
			$priv_report_tests = $_REQUEST["priv_report_tests"];
		}
		$priv_report_optical=0;
		if(isset($_REQUEST["priv_report_optical"])){
			$priv_report_optical = $_REQUEST["priv_report_optical"];
		}
		$priv_report_reminders=0;
		if(isset($_REQUEST["priv_report_reminders"])){
			$priv_report_reminders = $_REQUEST["priv_report_reminders"];
		}
		$priv_report_audit=0;
		if(isset($_REQUEST["priv_report_audit"])){
			$priv_report_audit = $_REQUEST["priv_report_audit"];
		}
		$priv_pt_instruction=0;
		if(isset($_REQUEST["priv_pt_instruction"])){
			$priv_pt_instruction = $_REQUEST["priv_pt_instruction"];
		}
		$priv_report_mur=0;
		if(isset($_REQUEST["priv_report_mur"])){
			$priv_report_mur = $_REQUEST["priv_report_mur"];
		}
		$priv_report_schduled=0;
		if(isset($_REQUEST["priv_report_schduled"])){
			$priv_report_schduled = $_REQUEST["priv_report_schduled"];
		}

		$priv_admin_billing=0;
		if(isset($_REQUEST["priv_admin_billing"])){
			$priv_admin_billing = $_REQUEST["priv_admin_billing"];
		}
		$priv_set_margin=0;
		if(isset($_REQUEST["priv_set_margin"])){
			$priv_set_margin = $_REQUEST["priv_set_margin"];
		}
		$priv_erx_preferences=0;
		if(isset($_REQUEST["priv_erx_preferences"])){
			$priv_erx_preferences = $_REQUEST["priv_erx_preferences"];
		}
		$priv_room_assign=0;
		if(isset($_REQUEST["priv_room_assign"])){
			$priv_room_assign = $_REQUEST["priv_room_assign"];
		}
		$priv_chart_notes=0;
		if(isset($_REQUEST["priv_chart_notes"])){
			$priv_chart_notes = $_REQUEST["priv_chart_notes"];
		}
		$priv_admin_scp=0;
		if(isset($_REQUEST["priv_admin_scp"])){
			$priv_admin_scp = $_REQUEST["priv_admin_scp"];
		}
		$priv_vs=0;
		if(isset($_REQUEST["priv_vs"])){
			$priv_vs = $_REQUEST["priv_vs"];
		}
		$priv_immunization=0;
		if(isset($_REQUEST["priv_immunization"])){
			$priv_immunization = $_REQUEST["priv_immunization"];
		}
		$priv_manage_fields=0;
		if(isset($_REQUEST["priv_manage_fields"])){
			$priv_manage_fields = $_REQUEST["priv_manage_fields"];
		}
		$priv_orders=0;
		if(isset($_REQUEST["priv_orders"])){
			$priv_orders = $_REQUEST["priv_orders"];
		}
		$priv_iportal=0;
		if(isset($_REQUEST["priv_iportal"])){
			$priv_iportal = $_REQUEST["priv_iportal"];
		}
		$priv_pis=0;
		if(isset($_REQUEST["priv_pis"])){
			$priv_pis = $_REQUEST["priv_pis"];
		}

		$priv_provider_management=0;
		if(isset($_REQUEST["priv_provider_management"])){
			$priv_provider_management = $_REQUEST["priv_provider_management"];
		}
		$priv_break_glass = 0;
		if(isset($_REQUEST["priv_break_glass"])){
			$priv_break_glass = $_REQUEST["priv_break_glass"];
		}
		$priv_edit_financials = 0;
		if(isset($_REQUEST["priv_edit_financials"])){
			$priv_edit_financials = $_REQUEST["priv_edit_financials"];
		}
		$priv_ref_physician=0;
		if(isset($_REQUEST["priv_ref_physician"])){
			$priv_ref_physician = $_REQUEST["priv_ref_physician"];
		}
		$priv_admin_scheduler=0;
		if(isset($_REQUEST["priv_admin_scheduler"])){
			$priv_admin_scheduler = $_REQUEST["priv_admin_scheduler"];
		}
		$priv_admin_billing=0;
		if(isset($_REQUEST["priv_admin_billing"])){
			$priv_admin_billing = $_REQUEST["priv_admin_billing"];
		}
        $priv_admin_clinical=0;
		if(isset($_REQUEST["priv_admin_clinical"])){
			$priv_admin_clinical = $_REQUEST["priv_admin_clinical"];
		}
        $priv_iMedicMonitor=0;
		if(isset($_REQUEST["priv_iMedicMonitor"])){
			$priv_iMedicMonitor = $_REQUEST["priv_iMedicMonitor"];
		}
        $priv_Admin_Optical=0;
		if(isset($_REQUEST["priv_Admin_Optical"])){
			$priv_Admin_Optical = $_REQUEST["priv_Admin_Optical"];
		}
        $priv_Admin_Reports=0;
		if(isset($_REQUEST["priv_Admin_Reports"])){
			$priv_Admin_Reports = $_REQUEST["priv_Admin_Reports"];
		}
		$priv_ins_management=0;
		if(isset($_REQUEST["priv_ins_management"])){
			$priv_ins_management = $_REQUEST["priv_ins_management"];
		}
		$priv_Optical = 0;
		if(isset($_REQUEST["priv_Optical"])){
			$priv_Optical = $_REQUEST["priv_Optical"];
		}
		$priv_Optical_POS = 0;
		if(isset($_REQUEST["priv_Optical_POS"])){
			$priv_Optical_POS = $_REQUEST["priv_Optical_POS"];
		}
		$priv_Optical_Inventory = 0;
		if(isset($_REQUEST["priv_Optical_Inventory"])){
			$priv_Optical_Inventory = $_REQUEST["priv_Optical_Inventory"];
		}
		$priv_Optical_Admin = 0;
		if(isset($_REQUEST["priv_Optical_Admin"])){
			$priv_Optical_Admin = $_REQUEST["priv_Optical_Admin"];
		}
		$priv_Optical_Reports = 0;
		if(isset($_REQUEST["priv_Optical_Reports"])){
			$priv_Optical_Reports = $_REQUEST["priv_Optical_Reports"];
		}

		$priv_iOLink = 0;
		if(isset($_REQUEST["priv_iOLink"])){
			$priv_iOLink = $_REQUEST["priv_iOLink"];
		}
		$priv_acchx = 0;
		if(isset($_REQUEST["priv_acchx"])){
			$priv_acchx = $_REQUEST["priv_acchx"];
		}

		$priv_pt_fdsk=0;
		if(isset($_REQUEST["priv_pt_fdsk"])){
			$priv_pt_fdsk = $_REQUEST["priv_pt_fdsk"];
		}

		$priv_pt_clinical=0;
		if(isset($_REQUEST["priv_pt_clinical"])){
			$priv_pt_clinical = $_REQUEST["priv_pt_clinical"];
		}
		$priv_documents=0;
		if(isset($_REQUEST["priv_documents"])){
			$priv_documents=$_REQUEST["priv_documents"];
		}
		$priv_alerts=0;
		if(isset($_REQUEST["priv_alerts"])){
			$priv_alerts=$_REQUEST["priv_alerts"];
		}
		$priv_pt_icon_imm=0;
		if(isset($_REQUEST["priv_pt_icon_imm"])){
			$priv_pt_icon_imm = $_REQUEST["priv_pt_icon_imm"];
		}
		$priv_pt_icon_optical=0;
		if(isset($_REQUEST["priv_pt_icon_optical"])){
			$priv_pt_icon_optical = $_REQUEST["priv_pt_icon_optical"];
		}
		$priv_pt_icon_iasclink=0;
		if(isset($_REQUEST["priv_pt_icon_iasclink"])){
			$priv_pt_icon_iasclink = $_REQUEST["priv_pt_icon_iasclink"];
		}
		$priv_financial_dashboard=0;
		if(isset($_REQUEST["priv_financial_dashboard"])){
			$priv_financial_dashboard = $_REQUEST["priv_financial_dashboard"];
		}
		$priv_pt_icon_support=0;
		if(isset($_REQUEST["priv_pt_icon_support"])){
			$priv_pt_icon_support = $_REQUEST["priv_pt_icon_support"];
		}

		$priv_api_access = 0;
		if(trim($_REQUEST["priv_api_access"])){
			$priv_api_access = $_REQUEST["priv_api_access"];
		}

		$priv_grp_prvlgs = !empty($_REQUEST["priv_grp_prvlgs"]) ? $_REQUEST["priv_grp_prvlgs"] : 0;
		$priv_chng_prvlgs = !empty($_REQUEST["priv_chng_prvlgs"]) ? $_REQUEST["priv_chng_prvlgs"] : 0;
		$priv_rules_mngr = !empty($_REQUEST["priv_rules_mngr"]) ? $_REQUEST["priv_rules_mngr"] : 0;

		$priv_report_compliance = 0;
		if(trim($_REQUEST["priv_report_compliance"])){
			$priv_report_compliance = $_REQUEST["priv_report_compliance"];
		}

		$priv_report_State = 0;
		if(trim($_REQUEST["priv_report_State"])){
			$priv_report_State = $_REQUEST["priv_report_State"];
		}
		$priv_report_api_access = 0;
		if(trim($_REQUEST["priv_report_api_access"])){
			$priv_report_api_access = $_REQUEST["priv_report_api_access"];
		}
		$priv_report_Rules = 0;
		if(trim($_REQUEST["priv_report_Rules"])){
			$priv_report_Rules = $_REQUEST["priv_report_Rules"];
		}
		$priv_report_iPortal = 0;
		if(trim($_REQUEST["priv_report_iPortal"])){
			$priv_report_iPortal = $_REQUEST["priv_report_iPortal"];
		}

        $menu_array = $this->fetchMenuArray();

        $privCheck=array();
        foreach($menu_array as $tab_head) {
            foreach($tab_head as $field_name => $label) {
                $privCheck[$field_name] = 0;
                if(trim($_REQUEST[$field_name])){
                    $privCheck[$field_name] = $_REQUEST[$field_name];
                }
            }
        }

        $privileges_temp = array();
        foreach($privCheck as $field => $priv) {
            $privileges_temp[$field] = intval($privCheck[$field]);
        }

		$privileges = array(
								"priv_api_access" => intval($priv_api_access),
								"priv_grp_prvlgs" => intval($priv_grp_prvlgs),
								"priv_chng_prvlgs" => intval($priv_chng_prvlgs),
								"priv_rules_mngr" => intval($priv_rules_mngr),
								"priv_cl_work_view" => intval($priv_cl_work_view),
								"priv_cl_tests" => intval($priv_cl_tests),
								"priv_cl_medical_hx" => intval($priv_cl_medical_hx),
								"priv_chart_finalize" => intval($priv_chart_finalize),

								"priv_Front_Desk" => intval($priv_Front_Desk),
								"priv_sch_lock_block" => intval($priv_sch_lock_block),
								"priv_sch_telemedicine" => intval($priv_sch_telemedicine),
								"priv_scheduler_demo" => intval($priv_scheduler_demo),
								"priv_Billing" => intval($priv_Billing),
								"priv_Accounting" => intval($priv_Accounting),
								"priv_Acc_all" => intval($priv_Acc_all),
								"priv_Acc_vonly" => intval($priv_Acc_vonly),
								"priv_Security" => intval($priv_Security),

								"priv_Reports_manager" => intval($priv_Reports_manager),
								"priv_sc_daily" => intval($priv_sc_daily),
								"priv_acct_receivable" => intval($priv_acct_receivable),
								"priv_bi_analytics" => intval($priv_bi_analytics),
								"priv_sc_scheduler" => intval($priv_sc_scheduler),
								"priv_sc_house_calls" => intval($priv_sc_house_calls),
								"priv_billing_fun" => intval($priv_billing_fun),

								"priv_report_compliance" => intval($priv_report_compliance),
								"priv_report_State" => intval($priv_report_State),
								"priv_report_api_access" => intval($priv_report_api_access),
								"priv_report_Rules" => intval($priv_report_Rules),
								"priv_report_iPortal" => intval($priv_report_iPortal),

								"priv_sc_recall_fulfillment" => intval($priv_sc_recall_fulfillment),
								"priv_bi_front_desk" => intval($priv_bi_front_desk),
								"priv_bi_ledger" => intval($priv_bi_ledger),
								"priv_bi_prod_payroll" => intval($priv_bi_prod_payroll),
								"priv_bi_ar" => intval($priv_bi_ar),
								"priv_bi_statements" => intval($priv_bi_statements),
								"priv_bi_day_chrg_rept" => intval($priv_bi_day_chrg_rept),
								"priv_financial_hx_cpt" => intval($priv_financial_hx_cpt),
								"priv_purge_del_chart" => intval($priv_purge_del_chart),
								"priv_bi_end_of_day" => intval($priv_bi_end_of_day),
								"priv_cl_clinical" => intval($priv_cl_clinical),
								"priv_cl_visits" => intval($priv_cl_visits),
								"priv_cl_ccd" => intval($priv_cl_ccd),
								"priv_cl_order_set" => intval($priv_cl_order_set),

								"priv_vo_clinical" => intval($priv_vo_clinical),
								"priv_vo_pt_info" => intval($priv_vo_pt_info),
								"priv_vo_acc" => intval($priv_vo_acc),
								"priv_vo_charges" => intval($priv_vo_charges),
								"priv_vo_payment" => intval($priv_vo_payment),
								"priv_del_charges_enc" => intval($priv_del_charges_enc),

								"priv_del_payment" => intval($priv_del_payment),


								"priv_Sch_Override" => intval($priv_Sch_Override),
								"priv_pt_Override" => intval($priv_pt_Override),
								"priv_ac_bill_manager" => intval($priv_ac_bill_manager),

								"priv_admin" => intval($priv_admin),
								"priv_all_settings" => intval($priv_all_settings),
								"priv_provider_management" => intval($priv_provider_management),

								"priv_group" => intval($priv_group),
								"priv_facility" => intval($priv_facility),
								"priv_document" => intval($priv_document),
								"priv_admin_billing" => intval($priv_admin_billing),
								"priv_admin_clinical" => intval($priv_admin_clinical),
                                "priv_iMedicMonitor" => intval($priv_iMedicMonitor),
                                "priv_Admin_Optical" => intval($priv_Admin_Optical),
                                "priv_Admin_Reports" => intval($priv_Admin_Reports),
								"priv_set_margin" => intval($priv_set_margin),
								"priv_erx_preferences" => intval($priv_erx_preferences),
								"priv_room_assign" => intval($priv_room_assign),
								"priv_chart_notes" => intval($priv_chart_notes),
								"priv_admin_scp" => intval($priv_admin_scp),
								"priv_vs" => intval($priv_vs),
								"priv_immunization" => intval($priv_immunization),
								"priv_manage_fields" => intval($priv_manage_fields),
								"priv_orders" => intval($priv_orders),
								"priv_iportal" => intval($priv_iportal),
								"priv_iols" => intval($priv_iols),
								"priv_console"=>intval($priv_console),
								"priv_report_financials"=>intval($priv_report_financials),
								"priv_report_tests"=>intval($priv_report_tests),
								"priv_report_optical"=>intval($priv_report_optical),
								"priv_report_reminders"=>intval($priv_report_reminders),
								"priv_report_audit"=>intval($priv_report_audit),
								"priv_pt_instruction"=>intval($priv_pt_instruction),
								"priv_report_mur"=>intval($priv_report_mur),
								"priv_report_schduled"=>intval($priv_report_schduled),

								"priv_Optical" => intval($priv_Optical),
								"priv_Optical_POS" => intval($priv_Optical_POS),
								"priv_Optical_Inventory" => intval($priv_Optical_Inventory),
								"priv_Optical_Admin" => intval($priv_Optical_Admin),
								"priv_Optical_Reports" => intval($priv_Optical_Reports),

								"priv_iOLink" => intval($priv_iOLink),
								"priv_break_glass" => intval($priv_break_glass),
								"priv_edit_financials" => intval($priv_edit_financials),
								"priv_ref_physician" => intval($priv_ref_physician),
								"priv_admin_scheduler" => intval($priv_admin_scheduler),
								"priv_admin_billing" => intval($priv_admin_billing),
								"priv_ins_management" => intval($priv_ins_management),

								"priv_pt_fdsk" => intval($priv_pt_fdsk),
								"priv_pt_clinical" => intval($priv_pt_clinical),
								"priv_no_reports" => intval($priv_no_reports),
								"priv_cdc" => intval($priv_cdc),
								"priv_acchx" => intval($priv_acchx),
								"priv_erx" => intval($priv_erx),

								"priv_pt_icon_imm"			=>	intval($priv_pt_icon_imm),
								"priv_pt_icon_optical"		=>	intval($priv_pt_icon_optical),
								"priv_pt_icon_iasclink"		=>	intval($priv_pt_icon_iasclink),
								"priv_financial_dashboard"	=>	intval($priv_financial_dashboard),
								"priv_pt_icon_support"		=>	intval($priv_pt_icon_support),

								"priv_report_payments" => intval($priv_report_payments),
								"priv_report_copay_rocan" => intval($priv_report_copay_rocan),
								"priv_un_superbills" => intval($priv_un_superbills),
								"priv_un_encounters" => intval($priv_un_encounters),
								"priv_un_payments" => intval($priv_un_payments),
								"priv_report_adjustment" => intval($priv_report_adjustment),
								"priv_report_refund" => intval($priv_report_refund),
								"priv_daily_balance" => intval($priv_daily_balance),
								"priv_fd_collection" => intval($priv_fd_collection),
								"priv_report_practice_analytics" => intval($priv_report_practice_analytics),
								"priv_cpt_analysis" => intval($priv_cpt_analysis),
								"priv_report_yearly" => intval($priv_report_yearly),
								"priv_report_revenue" => intval($priv_report_revenue),
								"priv_provider_mon" => intval($priv_provider_mon),
								"priv_ref_phy_monthly" => intval($priv_ref_phy_monthly),
								"priv_facility_monthly" => intval($priv_facility_monthly),
								"priv_report_ref_phy" => intval($priv_report_ref_phy),
								"priv_credit_analysis" => intval($priv_credit_analysis),
								"priv_report_patient" => intval($priv_report_patient),
								"priv_report_ins_cases" => intval($priv_report_ins_cases),
								"priv_report_eid_status" => intval($priv_report_eid_status),
								"priv_allowable_verify" => intval($priv_allowable_verify),
								"priv_vip_deferred" => intval($priv_vip_deferred),
								"priv_provider_rvu" => intval($priv_provider_rvu),
								"priv_sx_payment" => intval($priv_sx_payment),
								"priv_net_gross" => intval($priv_net_gross),
								"priv_ar_reports" => intval($priv_ar_reports),
								"priv_days_ar" => intval($priv_days_ar),
								"priv_receivables" => intval($priv_receivables),
								"priv_unworked_ar" => intval($priv_unworked_ar),
								"priv_unbilled_claims" => intval($priv_unbilled_claims),
								"priv_top_rej_reason" => intval($priv_top_rej_reason),
								"priv_new_statements" => intval($priv_new_statements),
								"priv_prev_statements" => intval($priv_prev_statements),
								"priv_report_payments" => intval($priv_report_payments),
								"priv_prev_hcfa" => intval($priv_prev_hcfa),
								"priv_statements_pay" => intval($priv_statements_pay),
								"priv_pt_statements" => intval($priv_pt_statements),
								"priv_pt_collections" => intval($priv_pt_collections),
								"priv_assessment" => intval($priv_assessment),
								"priv_collection_report" => intval($priv_collection_report),
								"priv_tfl_proof" => intval($priv_tfl_proof),
								"priv_report_rta" => intval($priv_report_rta),
								"priv_billing_verification" => intval($priv_billing_verification),
								"priv_patient_status" => intval($priv_patient_status),
								"priv_saved_scheduled" => intval($priv_saved_scheduled),
								"priv_executed_report" => intval($priv_executed_report),
								"priv_cn_pending" => intval($priv_cn_pending),
								"priv_contact_lens" => intval($priv_contact_lens),
								"priv_cn_ordered" => intval($priv_cn_ordered),
								"priv_cn_received" => intval($priv_cn_received),
								"priv_cn_dispensed" => intval($priv_cn_dispensed),
								"priv_cn_reports" => intval($priv_cn_reports),
								"priv_glasses" => intval($priv_glasses),
								"priv_gl_pending" => intval($priv_gl_pending),
								"priv_gl_ordered" => intval($priv_gl_ordered),
								"priv_gl_received" => intval($priv_gl_received),
								"priv_gl_dispensed" => intval($priv_gl_dispensed),
								"priv_gl_report" => intval($priv_gl_report),
								"priv_documents" => intval($priv_documents),
								"priv_alerts" => intval($priv_alerts),
								"priv_stage_iv" => intval($priv_stage_iv),
								"priv_stage_i" => intval($priv_stage_i),
								"priv_stage_ii" => intval($priv_stage_ii),
								"priv_stage_iii" => intval($priv_stage_iii),
								"priv_ccd_export" => intval($priv_ccd_export),
								"priv_ccd_import" => intval($priv_ccd_import),
								"priv_lab_import" => intval($priv_lab_import),
								"priv_ccr_import" => intval($priv_ccr_import),
								"priv_dat_appts" => intval($priv_dat_appts),
								"priv_recalls" => intval($priv_recalls),
								"priv_reminder_lists" => intval($priv_reminder_lists),
								"priv_no_shows" => intval($priv_no_shows),
								"ccr_exist_pat"=>intval($ccr_exist_pat),
								"ccr_new_pat"=>intval($ccr_exist_pat),
								"priv_pis" => intval($priv_pis)
							);

        $privileges = array_merge($privileges, $privileges_temp);


		//patch # 20100901 to accomodate the fact that admin has all privileges by default - starts here
		if($privileges["priv_all_settings"] == 1){
		//if($privileges["priv_admin"] == 1){
			$privileges = array(
								"priv_api_access" => intval($priv_api_access),
								"priv_grp_prvlgs" => intval($priv_grp_prvlgs),
								"priv_chng_prvlgs" => intval($priv_chng_prvlgs),
								"priv_rules_mngr" => intval($priv_rules_mngr),
								"priv_cl_work_view" => 1,
								"priv_cl_tests" => 1,
								"priv_cl_medical_hx" => 1,
								"priv_chart_finalize" => intval($priv_chart_finalize),

								"priv_Front_Desk" => 1,
								"priv_sch_lock_block" =>  1,
								"priv_sch_telemedicine" => 0,
								"priv_scheduler_demo" => 1,
								"priv_Billing" => intval($priv_Billing),
								"priv_Accounting" => 1,
								"priv_Acc_all" => 1,
								"priv_Acc_vonly" => 0,
								"priv_Security" => intval($priv_Security),

								"priv_sc_scheduler" => intval($priv_sc_scheduler),
								"priv_sc_house_calls" => intval($priv_sc_house_calls),
								"priv_sc_recall_fulfillment" => intval($priv_sc_recall_fulfillment),

								"priv_bi_ledger" => 1,
								"priv_bi_prod_payroll" => 1,
								"priv_bi_ar" => 1,

								"priv_bi_end_of_day" => 1,
								"priv_cl_clinical" => intval($priv_cl_clinical),
								"priv_cl_visits" => 1,
								"priv_cl_ccd" => intval($priv_cl_ccd),
								"priv_cl_order_set" => 1,

								"priv_vo_clinical" => 0,
								"priv_vo_pt_info" => 0,
								"priv_vo_acc" => 0,
								"priv_vo_charges" => 1,
								"priv_vo_payment" => 1,
								"priv_del_payment" => intval($priv_del_payment),
								"priv_del_charges_enc" => intval($priv_del_charges_enc),


								"priv_group" => intval($priv_group),
								"priv_facility" => intval($priv_facility),
								"priv_document" => intval($priv_document),
								"priv_admin_billing" => intval($priv_admin_billing),
								"priv_admin_clinical" => intval($priv_admin_clinical),
								"priv_iMedicMonitor" => intval($priv_iMedicMonitor),
								"priv_Admin_Optical" => intval($priv_Admin_Optical),
								"priv_Admin_Reports" => intval($priv_Admin_Reports),
								"priv_set_margin" => intval($priv_set_margin),
								"priv_erx_preferences" => intval($priv_erx_preferences),
								"priv_room_assign" => intval($priv_room_assign),
								"priv_chart_notes" => intval($priv_chart_notes),
								"priv_admin_scp" => intval($priv_admin_scp),
								"priv_vs" => intval($priv_vs),
								"priv_immunization" => intval($priv_immunization),
								"priv_manage_fields" => intval($priv_manage_fields),
								"priv_orders" => intval($priv_orders),
								"priv_iportal" => intval($priv_iportal),
								"priv_iols" => ($priv_iols),
								"priv_console"=>intval($priv_console),
								"priv_report_financials"=>intval($priv_report_financials),
								"priv_report_tests"=>intval($priv_report_tests),
								"priv_report_optical"=>intval($priv_report_optical),
								"priv_report_reminders"=>intval($priv_report_reminders),
								"priv_report_audit"=>intval($priv_report_audit),
								"priv_pt_instruction"=>intval($priv_pt_instruction),
								"priv_report_mur"=>intval($priv_report_mur),
								"priv_report_schduled"=>intval($priv_report_schduled),


								"priv_Sch_Override" => 1,
								"priv_pt_Override" => 1,

								"priv_ac_bill_manager" => 1,

								"priv_admin" => 1,
								"priv_all_settings" => 1,
								"priv_provider_management" => intval($priv_provider_management),

								"priv_Optical" => intval($priv_Optical),
								"priv_Optical_POS" => intval($priv_Optical_POS),
								"priv_Optical_Inventory" => intval($priv_Optical_Inventory),
								"priv_Optical_Admin" => intval($priv_Optical_Admin),
								"priv_Optical_Reports" => intval($priv_Optical_Reports),

								"priv_iOLink" => intval($priv_iOLink),
								"priv_break_glass" => 1,
								"priv_edit_financials" => intval($priv_edit_financials),
								"priv_ref_physician" => intval($priv_ref_physician),
								"priv_admin_scheduler" =>intval($priv_admin_scheduler),
								"priv_admin_billing" =>intval($priv_admin_billing),
								"priv_ins_management" => intval($priv_ins_management),
								"priv_cdc" => 1,

								"priv_Reports_manager" => intval($priv_Reports_manager),
								"priv_sc_daily" => intval($priv_sc_daily),
								"priv_acct_receivable" => intval($priv_acct_receivable),
								"priv_bi_analytics" => intval($priv_bi_analytics),
								"priv_bi_statements" => 1,
                                "priv_bi_day_chrg_rept" => intval($priv_bi_day_chrg_rept),
                                "priv_financial_hx_cpt" => intval($priv_financial_hx_cpt),
				"priv_purge_del_chart" => intval($priv_purge_del_chart),
								"priv_bi_front_desk" => 1,
								"priv_billing_fun" => intval($priv_billing_fun),
								"priv_pt_fdsk" => intval($priv_pt_fdsk),
								"priv_pt_clinical" => intval($priv_pt_clinical),

								"priv_erx" => 1,
								"priv_acchx" => 1,
								"pt_coordinator"=>1,

								"priv_pt_icon_imm"			=>	intval($priv_pt_icon_imm),
								"priv_pt_icon_optical"		=>	intval($priv_pt_icon_optical),
								"priv_pt_icon_iasclink"		=>	intval($priv_pt_icon_iasclink),
								"priv_financial_dashboard"	=>	intval($priv_financial_dashboard),
								"priv_pt_icon_support"		=>	intval($priv_pt_icon_support),

                                "priv_report_compliance" => intval($priv_report_compliance),
								"priv_report_State" => intval($priv_report_State),
								"priv_report_api_access" => intval($priv_report_api_access),
								"priv_report_Rules" => intval($priv_report_Rules),
								"priv_report_iPortal" => intval($priv_report_iPortal),

								"priv_report_payments" => intval($priv_report_payments),
								"priv_report_copay_rocan" => intval($priv_report_copay_rocan),
								"priv_un_superbills" => intval($priv_un_superbills),
								"priv_un_encounters" => intval($priv_un_encounters),
								"priv_un_payments" => intval($priv_un_payments),
								"priv_report_adjustment" => intval($priv_report_adjustment),
								"priv_report_refund" => intval($priv_report_refund),
								"priv_daily_balance" => intval($priv_daily_balance),
								"priv_fd_collection" => intval($priv_fd_collection),
								"priv_report_practice_analytics" => intval($priv_report_practice_analytics),
								"priv_cpt_analysis" => intval($priv_cpt_analysis),
								"priv_report_yearly" => intval($priv_report_yearly),
								"priv_report_revenue" => intval($priv_report_revenue),
								"priv_provider_mon" => intval($priv_provider_mon),
								"priv_ref_phy_monthly" => intval($priv_ref_phy_monthly),
								"priv_facility_monthly" => intval($priv_facility_monthly),
								"priv_report_ref_phy" => intval($priv_report_ref_phy),
								"priv_credit_analysis" => intval($priv_credit_analysis),
								"priv_report_patient" => intval($priv_report_patient),
								"priv_report_ins_cases" => intval($priv_report_ins_cases),
								"priv_report_eid_status" => intval($priv_report_eid_status),
								"priv_allowable_verify" => intval($priv_allowable_verify),
								"priv_vip_deferred" => intval($priv_vip_deferred),
								"priv_provider_rvu" => intval($priv_provider_rvu),
								"priv_sx_payment" => intval($priv_sx_payment),
								"priv_net_gross" => intval($priv_net_gross),
								"priv_ar_reports" => intval($priv_ar_reports),
								"priv_days_ar" => intval($priv_days_ar),
								"priv_receivables" => intval($priv_receivables),
								"priv_unworked_ar" => intval($priv_unworked_ar),
								"priv_unbilled_claims" => intval($priv_unbilled_claims),
								"priv_top_rej_reason" => intval($priv_top_rej_reason),
								"priv_new_statements" => intval($priv_new_statements),
								"priv_prev_statements" => intval($priv_prev_statements),
								"priv_report_payments" => intval($priv_report_payments),
								"priv_prev_hcfa" => intval($priv_prev_hcfa),
								"priv_statements_pay" => intval($priv_statements_pay),
								"priv_pt_statements" => intval($priv_pt_statements),
								"priv_pt_collections" => intval($priv_pt_collections),
								"priv_assessment" => intval($priv_assessment),
								"priv_collection_report" => intval($priv_collection_report),
								"priv_tfl_proof" => intval($priv_tfl_proof),
								"priv_report_rta" => intval($priv_report_rta),
								"priv_billing_verification" => intval($priv_billing_verification),
								"priv_patient_status" => intval($priv_patient_status),
								"priv_saved_scheduled" => intval($priv_saved_scheduled),
								"priv_executed_report" => intval($priv_executed_report),
								"priv_cn_pending" => intval($priv_cn_pending),
								"priv_contact_lens" => intval($priv_contact_lens),
								"priv_cn_ordered" => intval($priv_cn_ordered),
								"priv_cn_received" => intval($priv_cn_received),
								"priv_cn_dispensed" => intval($priv_cn_dispensed),
								"priv_cn_reports" => intval($priv_cn_reports),
								"priv_glasses" => intval($priv_glasses),
								"priv_gl_pending" => intval($priv_gl_pending),
								"priv_gl_ordered" => intval($priv_gl_ordered),
								"priv_gl_received" => intval($priv_gl_received),
								"priv_gl_dispensed" => intval($priv_gl_dispensed),
								"priv_gl_report" => intval($priv_gl_report),
								"priv_documents" => intval($priv_documents),
								"priv_alerts" => intval($priv_alerts),
								"priv_stage_iv" => intval($priv_stage_iv),
								"priv_stage_i" => intval($priv_stage_i),
								"priv_stage_ii" => intval($priv_stage_ii),
								"priv_stage_iii" => intval($priv_stage_iii),
								"priv_ccd_export" => intval($priv_ccd_export),
								"priv_ccd_import" => intval($priv_ccd_import),
								"priv_lab_import" => intval($priv_lab_import),
								"priv_ccr_import" => intval($priv_ccr_import),
								"priv_dat_appts" => intval($priv_dat_appts),
								"priv_recalls" => intval($priv_recalls),
								"priv_reminder_lists" => intval($priv_reminder_lists),
								"priv_no_shows" => intval($priv_no_shows),
								"ccr_exist_pat"=>intval($ccr_exist_pat),
								"ccr_new_pat"=>intval($ccr_exist_pat),
								"priv_pis" => intval($priv_pis)
							);

            $privileges = array_merge($privileges, $privileges_temp);
		}
		//patch # 20100901 ends here
		if($privileges["priv_api_access"] == 1){
            $privileges["priv_report_api_access"] = 1;
            $privileges["priv_report_Access_Log"] = 1;
            $privileges["priv_report_Call_Log"] = 1;
        }

		$str_access_priv = serialize($privileges);
		*/
		//=================Trap User Priviliges=========================//
			$pt_coodinator_priv=0;
			if($_REQUEST["priv_pt_coordinate"]){
				$pt_coodinator_priv=1;
			}

			$qry_select_prev_priv="Select access_pri,if(pt_coordinator='',0,1) as pt_coordinator from users where id='".$pro_id."'";
			$res_select_prev_priv=imw_query($qry_select_prev_priv);
			$row_select_prev_priv=imw_fetch_assoc($res_select_prev_priv);
			$user_priviliges_prev=unserialize(html_entity_decode(trim($row_select_prev_priv['access_pri'])));
			$user_priviliges_prev['priv_pt_coordinate']=$row_select_prev_priv['pt_coordinator'];
			$privileges['priv_pt_coordinate']=($pt_coodinator_priv);
			$core_array=array("user_id"=>$pro_id,"session_user"=>$_SESSION["authId"],"user_priviliges"=>$privileges,
							  "previous_priv"=>$user_priviliges_prev,"server_info"=>$_SERVER,"save_date"=>date("Y-m-d"),
							  "save_time"=>date("h:i:s A"));
			$serilize_core_array=serialize($core_array);
			set_users_log_details("users",$serilize_core_array);
		//=============================================================//
		$_REQUEST["access_pri_audit"] = urlencode(htmlentities(serialize($privileges)));
		$arrCols[] = "access_pri";			$arrVals[] = $str_access_priv;

		//login info
		$arrCols[] = "username";			$arrVals[] = $_REQUEST["pro_user"];
		if($pro_id == ""){
			$arrCols[] = "password";		$arrVals[] = hashPassword($_REQUEST["pro_pass_news"],'forcehash');
		}

		//erx login info
		$arrCols[] = "eRx_user_name";		$arrVals[] = $_REQUEST["erx_user_name"];
		$arrCols[] = "erx_password";		$arrVals[] = $_REQUEST["erx_password"];

		//facility and groups
		$arrCols[] = "default_facility";	$arrVals[] = $_REQUEST["default_facility"];

		//scheduler facilities
		$arrCols[] = "sch_facilities";	$arrVals[] = str_replace(",", ";", $_REQUEST["selected_sch_facs"]);

		$str_groups = "";
		if(isset($_REQUEST["groups"]) && is_array($_REQUEST["groups"]) && count($_REQUEST["groups"]) > 0){
			$str_groups = implode(",",	$_REQUEST["groups"]);
		}
		$arrCols[] = "gro_id";				$arrVals[] = $str_groups;


		//if($_REQUEST["refraction_set_from_policy"] != "yes"){
			$arrCols[] = "collect_refraction";				$arrVals[] = $_REQUEST["collect_refraction"];
		//}

		//scheduler settings
		$int_enable_scheduler = 0;
		if(isset($_REQUEST["Enable_Scheduler"]) && $_REQUEST["Enable_Scheduler"] == 1){
			$int_enable_scheduler = $_REQUEST["Enable_Scheduler"];

			$arrCols[] = "Enable_Scheduler";	$arrVals[] = $int_enable_scheduler;
			$arrCols[] = "provider_color";		$arrVals[] = $_REQUEST["pro_color"];
			$arrCols[] = "max_appoint";			$arrVals[] = $_REQUEST["max_appoint"];
			$arrCols[] = "sch_index";			$arrVals[] = $_REQUEST["sch_index"];

			$str_stop_over_booking = "No";
			if(isset($_REQUEST["StopOverBooking"]) && $_REQUEST["StopOverBooking"] == "Yes"){
				$str_stop_over_booking = $_REQUEST["StopOverBooking"];
			}
			$arrCols[] = "StopOverBooking";		$arrVals[] = $str_stop_over_booking;
		}else{
			if($pro_id != ""){
				$arrCols[] = "Enable_Scheduler";	$arrVals[] = 0;
				$arrCols[] = "provider_color";		$arrVals[] = "";
				$arrCols[] = "max_appoint";			$arrVals[] = "";
				$arrCols[] = "StopOverBooking";		$arrVals[] = "No";
				$arrCols[] = "sch_index";		$arrVals[] = 0;
			}
		}

		$arrCols[] = "max_day";				$arrVals[] = $_REQUEST["max_day"];
		$arrCols[] = "max_per";				$arrVals[] = $_REQUEST["max_per"];


		//specimen signature
		$str_specimen_sign = $_REQUEST["elem_sign"];
		$str_specimen_sign_path = $_REQUEST["elem_sign_path"];
		$arrCols[] = "sign";		$arrVals[] = $str_specimen_sign;
		$arrCols[] = "sign_path";		$arrVals[] = $str_specimen_sign_path;

		//session settings
		$arrCols[] = "session_timeout";		$arrVals[] = $_REQUEST["session_timeout"];

		//important id numbers
		$arrCols[] = "external_id";			$arrVals[] = $_REQUEST["external_id"];
		$arrCols[] = "user_npi";			$arrVals[] = $_REQUEST["user_npi"];
		$arrCols[] = "upin";				$arrVals[] = $_REQUEST["pro_upin"];
		$arrCols[] = "MedicareId";			$arrVals[] = $_REQUEST["MedicareId"];
		$arrCols[] = "TaxonomyId";			$arrVals[] = $_REQUEST["TaxonomyId"];
		$arrCols[] = "TaxId";				$arrVals[] = $_REQUEST["pro_tax"];
		$arrCols[] = "MedicaidId";			$arrVals[] = $_REQUEST["MedicaidId"];
		$arrCols[] = "federaldrugid";		$arrVals[] = $_REQUEST["pro_drug"];
		$arrCols[] = "licence";				$arrVals[] = $_REQUEST["pro_lic"];
											$arrGroupName = explode("^",$_REQUEST["group_name"]);
		$arrCols[] = "default_group";		$arrVals[] = $arrGroupName[0];

		//additional info
		$arrCols[] = "additional_info";		$arrVals[] = $_REQUEST["pro_add"];
		if(constant('REMOTE_SYNC')=='1') {
			$arrCols[] = "imwemr_id";	$arrVals[] = $_REQUEST["imwemr_id"];
		}
		$_REQUEST["sso_identifier"]=trim($_REQUEST["sso_identifier"]);
		$arrCols[] = "sso_identifier";	$arrVals[] = ($this->is_uniq_sso($_REQUEST["sso_identifier"], $pro_id)) ? $_REQUEST["sso_identifier"] : "" ;
		//internal system settings
		if($pro_id != ""){

		}else{
			$arrCols[] = "created_on";			$arrVals[] = date("Y-m-d H:i:s");
			$arrCols[] = "created_by";			$arrVals[] = $_SESSION["authId"];
			$arrCols[] = "delete_status";		$arrVals[] = 0;
			$arrCols[] = "locked";				$arrVals[] = 0;
			$arrCols[] = "HIPPA_STATUS";		$arrVals[] = "no";
			$arrCols[] = "passwordChanged";		$arrVals[] = 0;
			$arrCols[] = "passCreatedOn";		$arrVals[] = date("Y-m-d");
			$arrCols[] = "superuser";			$arrVals[] = "no";
			$arrCols[] = "passwordReset";		$arrVals[] = 0;
		}

		$arrCols[] = "modified_on";				$arrVals[] = date("Y-m-d H:i:s");
		$arrCols[] = "modified_by";				$arrVals[] = $_SESSION["authId"];
		$arrCols[] = "pt_coordinator";			$arrVals[] = $_REQUEST["priv_pt_coordinate"];
		$arrCols[] = "follow_physician";		$arrVals[] = $_REQUEST["elem_follow_physician"];
		$arrCols[] = "sx_physician";			$arrVals[] = $_REQUEST["sx_physician"];
		$arrCols[] = "groups_prevlgs_id";			$arrVals[] = (!empty($_REQUEST["el_privileges"]) && $_REQUEST["el_privileges"]>0) ? $_REQUEST["el_privileges"] : 0 ;
		$arrCols[] = "posfacilitygroup_id";			$arrVals[] = (!empty($_REQUEST["posfacilitygroup_id"]) && $_REQUEST["posfacilitygroup_id"]!='') ? json_encode($_REQUEST["posfacilitygroup_id"]) : '' ;
		$arrCols[] = "view_all_provider_financials";	$arrVals[]=$_REQUEST['view_all_provider_financials'];
		$arrCols[] = "provider_financials";		$arrVals[]=(sizeof($_REQUEST['rpt_financial_providers'])>0) ? implode(',', $_REQUEST['rpt_financial_providers']) : '';

		$strQry = make_query("add_update", $pro_id, "users", $arrCols, $arrVals, "id = '".$pro_id."'", "yes", "yes", 0);
		$erp_error=array();
 		$result = imw_query($strQry);
        if($result !== false){
			if($pro_id == ""){
				$pro_insert_id = imw_insert_id();
				if(!empty($pro_insert_id) && !empty($_REQUEST['elem_sign_path']) && strpos($_REQUEST['elem_sign_path'],"sign")!==false){

					require_once($GLOBALS['srcdir']."/classes/SaveFile.php");
					$oSaveFile = new SaveFile($pro_insert_id,1); //User
					$tmp_sign_pth  = $oSaveFile->copySign($_REQUEST['elem_sign_path']);

					//update path
					if(!empty($tmp_sign_pth)){
						$sql= "UPDATE users SET sign_path = '".imw_real_escape_string($tmp_sign_pth)."' WHERE id = '".$pro_insert_id."' ";
						$row=get_row_record_query($sql);
					}

				}
			}

            $insert_pro_id = ($pro_id) ? $pro_id : $pro_insert_id;
            if(isERPPortalEnabled() && $insert_pro_id != '') {
				try {
					$this->addUpdateUserOnErpPortal($_REQUEST,$insert_pro_id,$privileges);
				} catch(Exception $e) {
					$erp_error[]='Unable to connect to ERP Portal';
				}
            }

			//Save Speciality ---
			if(count($_REQUEST["elem_spl"])>0){

				$str_spl=implode(",", $_REQUEST["elem_spl"]);
				$sql="UPDATE phy_speciality SET status='2' WHERE phyId='".$pro_id."' AND spId NOT IN ($str_spl) ";
				$row=get_row_record_query($sql);

				foreach($_REQUEST["elem_spl"] as $key=>$val){
					$sql = "SELECT * FROM phy_speciality WHERE phyId='".$pro_id."' AND spId='".$val."' ";
					$row=get_row_record_query($sql);
					if($row==false){
						$sql = "INSERT INTO phy_speciality(id, phyId, spId) VALUES (NULL, '".$pro_id."', '".$val."') ";
						$row=get_row_record_query($sql);
					}
				}

			}
			else{
				//DELETE All
				$sql="UPDATE phy_speciality SET status='2' WHERE phyId='".$pro_id."' ";
				$row=get_row_record_query($sql);
			}

			//Save Speciality ---

			$erx_msg = trim($this->get_vocabulary("admin", "poviders","voc_provider_update_success"));
			if($pro_id == ""){
				$pro_temp_id = imw_insert_id();
			}

			$pro_erx_id = ($pro_id != "") ? $pro_id : $pro_temp_id;

			//eRx - emedeon registration
			if(isset($_REQUEST["erx_user_name"]) && !empty($_REQUEST["erx_user_name"]) && isset($_REQUEST["erx_password"]) && !empty($_REQUEST["erx_password"])){

				//getting emedeon url
				$arr_bill_pol = $this->get_billing_policy();
				$EmdeonUrl = $arr_bill_pol[0]["EmdeonUrl"];
				$Allow_erx_medicare  = $arr_bill_pol[0]["Allow_erx_medicare"];

				if($Allow_erx_medicare  == "Yes"){

					$bl_erx_registration = false;
					if(isset($_REQUEST["oldErxUsername"]) && !empty($_REQUEST["oldErxUsername"]) && isset($_REQUEST["oldErxPassword"]) && !empty($_REQUEST["oldErxPassword"])){
						//check for re-registration
						if($_REQUEST["oldErxUsername"] != $_REQUEST["erx_user_name"]){
							$bl_erx_registration = true;
						}

						//check for change in password - updating erx password
						if($bl_erx_registration == false && $_REQUEST["oldErxPassword"] != $_REQUEST["erx_password"]){
							$bl_erx_pass_changed = false;
							$cookie_file = data_path().'users/cookie_'.$pro_erx_id.'.txt';
							$fileInfoVar = 	pathinfo($cookie_file);
							if(!is_dir($fileInfoVar['dirname'])) mkdir($fileInfoVar['dirname'], 0777, true);

							$cur = curl_init();
							$url = $EmdeonUrl."/servlet/DxLogin?userid=".$_REQUEST["erx_user_name"]."&PW=".$_REQUEST["oldErxPassword"]."&newPW=".$_REQUEST["erx_password"]."&target=html/ChangePasswordSuccess.html&changePassword=true";
							curl_setopt($cur,CURLOPT_URL,$url);
							curl_setopt ($cur, CURLOPT_SSL_VERIFYHOST, false);
							curl_setopt ($cur, CURLOPT_SSL_VERIFYPEER, false);
							curl_setopt($cur, CURLOPT_RETURNTRANSFER, true);
							curl_setopt($cur, CURLOPT_COOKIEJAR, $cookie_file);
							curl_setopt($cur, CURLOPT_FOLLOWLOCATION, true);
							$data = curl_exec($cur);
							curl_close($cur);
							preg_match('/Change Password Success/',$data,$success_chk_arr);

							if(count($success_chk_arr)>0){
								//$erx_msg .= "<br>".$this->objVocabulary->voc_erx_password_success;
								$erx_msg .= "<br>".trim($this->get_vocabulary("admin", "poviders","voc_erx_password_success"));
								$bl_erx_pass_changed = true;
							}else{
								$cur = curl_init();
								$url = "$EmdeonUrl/servlet/DxLogin?userid=".$_REQUEST["erx_user_name"]."&PW=".$_REQUEST["erx_password"]."&target=html/LoginSuccess.html&testLogin=true";
								curl_setopt($cur,CURLOPT_URL,$url);
								curl_setopt ($cur, CURLOPT_SSL_VERIFYHOST, false);
								curl_setopt ($cur, CURLOPT_SSL_VERIFYPEER, false);
								curl_setopt($cur, CURLOPT_RETURNTRANSFER, true);
								curl_setopt($cur, CURLOPT_COOKIEJAR, $cookie_file);
								curl_setopt($cur, CURLOPT_FOLLOWLOCATION, true);

								$data = curl_exec($cur);
								curl_close($cur);
								preg_match('/Login Error/',$data,$login_error);
								print_r($login_error);

								if(count($login_error) == 0){
									$erx_msg .= "<br>".trim($this->get_vocabulary("admin", "poviders","voc_erx_password_success"));
									$bl_erx_pass_changed = true;
								}
								else{
									$erx_msg .= "<br>".trim($this->get_vocabulary("admin", "poviders","voc_erx_password_failed"));
									$bl_erx_pass_changed = false;
								}

							}
							@unlink($cookie_file);

							if($bl_erx_pass_changed == false){
								$arreRxCols = array();
								$arreRxVals = array();

								$arreRxCols[] = "erx_password";			$arreRxVals[] = $_REQUEST["oldErxPassword"];

								$streRxQry2 = make_query("add_update", $pro_erx_id, "users", $arreRxCols, $arreRxVals, "id = '".$pro_erx_id."'", "yes", "yes", 0);
								$result = imw_query($streRxQry2);
							}
						}
					}else{
						//new registration
						$bl_erx_registration = true;
					}

					if($bl_erx_registration == true){
						$bl_erx_registered = false;
						//--- Log in from emdeon erx --------
						$cookie_file = data_path().'users/cookie_'.$pro_erx_id.'.txt';
						$fileInfoVar = 	pathinfo($cookie_file);
						if(!is_dir($fileInfoVar['dirname'])) mkdir($fileInfoVar['dirname'], 0777, true);

						$cur = curl_init();
						$url = $EmdeonUrl."/servlet/DxLogin?userid=".$_REQUEST["erx_user_name"]."&PW=".$_REQUEST["erx_password"]."&apiLogin=true&target=html/LoginSuccess.html&testLogin=true";
						curl_setopt($cur,CURLOPT_URL,$url);
						curl_setopt($cur, CURLOPT_SSL_VERIFYHOST, false);
						curl_setopt($cur, CURLOPT_SSL_VERIFYPEER, false);
						curl_setopt($cur, CURLOPT_RETURNTRANSFER, true);
						curl_setopt($cur, CURLOPT_COOKIEJAR, $cookie_file);
						curl_setopt($cur, CURLOPT_FOLLOWLOCATION, true);
						$data = curl_exec($cur);
						curl_close($cur);
						//-- Get Facility Id  ---------------
						$url = $EmdeonUrl."/servlet/servlets.apiPersonServlet?actionCommand=getFacility&apiuserid=".$_REQUEST["erx_user_name"];
						$cur = curl_init();
						curl_setopt($cur,CURLOPT_URL,$url);
						curl_setopt($cur, CURLOPT_SSL_VERIFYHOST, false);
						curl_setopt($cur, CURLOPT_SSL_VERIFYPEER, false);
						curl_setopt($cur,CURLOPT_COOKIEFILE,$cookie_file);
						curl_setopt($cur, CURLOPT_RETURNTRANSFER, true);
						curl_setopt($cur, CURLOPT_FOLLOWLOCATION, true);
						$facility_data = curl_exec($cur);
						$data_arr1 = explode(' ',$facility_data);
						$data_arr = explode('><',$data_arr1[1]);
						$erx_facility_id_user = $data_arr[0];
						//$erx_facility_id = 21638164;
						if(is_numeric($erx_facility_id_user)){
							$erx_msg .= "<br>".trim($this->get_vocabulary("admin", "poviders","voc_erx_auth_success"));
							$bl_erx_registered = true;
						}else{
							$erx_facility_id_user = '';
							$erx_msg .= "<br>".trim($this->get_vocabulary("admin", "poviders","voc_erx_auth_failed"));
							$bl_erx_registered = false;
						}
						
						/*******ALLOCATE CAREGIVER ID BY MATCHING NPI********/
						$url = $EmdeonUrl."/servlet/servlets.apiPersonServlet?actionCommand=listCaregivers&apiuserid=".$_REQUEST["erx_user_name"]."&facilityobjid=".$erx_facility_id_user;
						$cur = curl_init();
						curl_setopt($cur,CURLOPT_URL,$url);
						curl_setopt($cur, CURLOPT_SSL_VERIFYHOST, false);
						curl_setopt($cur, CURLOPT_SSL_VERIFYPEER, false); 
						curl_setopt($cur,CURLOPT_COOKIEFILE,$cookie_file);
						curl_setopt($cur, CURLOPT_RETURNTRANSFER, true);
						curl_setopt($cur, CURLOPT_FOLLOWLOCATION, true); 
						$cg_data = curl_exec($cur);
						//file_put_contents(data_path()."users/caregiverssss_".date('Ymd').".txt",$cg_data);
						preg_match('<--BEGIN CAREGIVER LIST>',$cg_data,$correct_cg_data);
						if($correct_cg_data){
							$cg_data = preg_replace('/<--BEGIN CAREGIVER LIST>/','',$cg_data);
							$cg_data = preg_replace('/<--END CAREGIVER LIST>/','',$cg_data);
						//	file_put_contents(data_path()."users/caregivers_".date('Ymd').".txt",$cg_data);
							$cg_data_arr = preg_split('/'.chr(10).'/', $cg_data, -1, PREG_SPLIT_NO_EMPTY);	//pre($cg_data_arr);
							if(count($cg_data_arr)>=3){
								$cg_erx_provider_id = $cg_erx_provider_name = $cg_erx_provider_npi = '';
								for($i=0;$i<count($cg_data_arr);$i++){
									$cg_curr_line = $cg_data_arr[$i];
									$cg_curr_line_arr = explode('=',$cg_curr_line);
									$cgfield = $cg_curr_line_arr[0];
									$cgvalue = $cg_curr_line_arr[1];
									switch($cgfield){
										case 'CAREGIVEROBJID'	:	$cg_erx_provider_id		= $cgvalue; break;
										case 'CAREGIVERNAME'	:	$cg_erx_provider_name	= $cgvalue; break;
										case 'CAREGIVERNPI'		:	$cg_erx_provider_npi	= $cgvalue; break;
									}
									
									if($cg_erx_provider_id != '' && $cg_erx_provider_name != '' && $cg_erx_provider_npi != '' && ($_REQUEST["user_npi"]==$cg_erx_provider_npi)){
										$sql1 = "UPDATE users SET eRx_prescriber_id='".$cg_erx_provider_id."' 
												WHERE id = '$insert_pro_id' 
												AND user_npi <> '' LIMIT 1";//echo $sql1.'<br>';
										$res1 = imw_query($sql1);
								//		file_put_contents(data_path()."users/caregivers_".date('Ymd').".txt","\r\n".$sql1,FILE_APPEND);
										$cg_erx_provider_id = $cg_erx_provider_name = $cg_erx_provider_npi = '';
										break;
									}else{
								//		file_put_contents(data_path()."users/caregivers_".date('Ymd').".txt","\r\n"."Data validation error",FILE_APPEND);
									}

									
								}
							}						
						}
						/***END OF ALLOCATING CAREGIVER ID BY MATCHING NPI***/
						

						//--- Log out from emdeon erx --------
						$cur = curl_init();
						$url = $EmdeonUrl."/servlet/lab.security.DxLogout?userid=".$_REQUEST["erx_user_name"]."&BaseUrl=".$EmdeonUrl."&LogoutPath=/html/AutoPrintFinished.html";
						curl_setopt($cur,CURLOPT_URL,$url);
						curl_setopt ($cur, CURLOPT_SSL_VERIFYHOST, false);
						curl_setopt ($cur, CURLOPT_SSL_VERIFYPEER, false);
						curl_setopt($cur, CURLOPT_RETURNTRANSFER, true);
						curl_setopt($cur, CURLOPT_COOKIEJAR, $cookie_file);
						curl_setopt($cur, CURLOPT_FOLLOWLOCATION, true);
						$data = curl_exec($cur);
						curl_close($cur);
						unlink($cookie_file);

						if($bl_erx_registered == false){
							$arreRxCols = array();
							$arreRxVals = array();

							$arreRxCols[] = "eRx_user_name";			$arreRxVals[] = "";
							$arreRxCols[] = "erx_password";				$arreRxVals[] = "";
							$arreRxCols[] = "eRx_facility_id";			$arreRxVals[] = "";

							$streRxQry = make_query("add_update", $pro_erx_id, "users", $arreRxCols, $arreRxVals, "id = '".$pro_erx_id."'", "no", "no", 0);
							$result = imw_query($streRxQry);
						}

						if($bl_erx_registered == true){
							$arreRxCols = array();
							$arreRxVals = array();

							$arreRxCols[] = "eRx_facility_id";			$arreRxVals[] = $erx_facility_id_user;

							$streRxQry1 = make_query("add_update", $pro_erx_id, "users", $arreRxCols, $arreRxVals, "id = '".$pro_erx_id."'", "yes", "yes", 0);
							$result = imw_query($streRxQry1);
						}
					}
				}
			}

			//audit
			$arrAuditTrail = urldecode($_REQUEST["hidDataProvider"]);
			if($arrAuditTrail != "no_audit"){
				$arrAuditTrail = unserialize($arrAuditTrail);
				if($pro_id != ""){
					$pro_audit_id = $pro_id;
					foreach ($arrAuditTrail as $key => $value) {
						$arrAuditTrail [$key]["Pk_Id"] = $pro_audit_id;
						$arrAuditTrail [$key]["Action"] = "update";
					}
				}else{
					$pro_audit_id = $pro_temp_id;
					foreach ($arrAuditTrail as $key => $value) {
						$arrAuditTrail [$key]["Pk_Id"] = $pro_audit_id;
						$arrAuditTrail [$key]["Action"] = "add";
						$arrAuditTrail [$key]["Old_Value"] = "";
					}
				}
				$table = array("users");
				$error = array($userError);
				$mergedArray = array();
				$vv=explode('-',$_REQUEST['pro_type']);
				$_REQUEST['pro_type']=$vv[0];
				if(count($table) == count($error)){
					for($a=0; $a < count($table); $a++){
						$mergedArray[] = array(
											"Table_Name"=> trim($table[$a]),
											"Error"=> trim($error[$a])
										);
					}
				}
				//setting request vars for audit
				$_REQUEST['pre_phy'] = $str_access_priv;
				$_REQUEST['groups']  = $str_groups;
				if($_REQUEST["elem_sign"] == "0-0-0:;"){
					$_REQUEST["elem_sign"] = "";
				}
				auditTrail($arrAuditTrail,$mergedArray,$pro_audit_id,0,0);
			}


			// ADD REF PHYSICIAN IF NOT EXISTS
			addRefPhysician($pro_erx_id, $pro_fname, $pro_lname);

		}

		$pro_id_populate = ($pro_id != "") ? $pro_id : $pro_temp_id;
		$this->clear_cache();

		if($pro_id_populate == $_SESSION["authId"]){
			$_SESSION["sess_privileges"] = $privileges;
		}
		$return_err['page_url'] = 'index.php?'.$srh_del_status_inc.'pro_id='.$pro_id_populate.'&done=true';
		$return_err['err'] = trim($erx_msg);
		$return_err['focus'] = '';
		//$this->redirectTo("index.php?".$srh_del_status_inc."pro_id=".$pro_id_populate."&err=".$erx_msg);

		return json_encode($return_err);
	}

	function is_uniq_sso($sso_idn, $proid=0){
		$ret=true;
		if(!empty($sso_idn)){
			$sql = "select count(*) as num from users where sso_identifier='".$sso_idn."' AND delete_status = '0' ";
			if(!empty($proid)){ $sql .= " AND id!='".$proid."' "; }
			$row = sqlQuery($sql);
			if($row!=false && $row["num"] > 0){
				$ret=false;
			}
		}
		return $ret;
	}


	function providers_audit($pro_id = "", $arr_user_details){
		if(is_array($arr_user_details) && count($arr_user_details) > 0){
			foreach($arr_user_details as $key=>$val){
				if(is_numeric($key) === false){
					$$key = $val;
				}
			}
		}
		//$usersDataFields = $this->audit_make_get_field_types("select * from users LIMIT 0 , 1");
		$usersDataFields = make_field_type_array("users");
		$arrAuditTrail = array();
		$opreaterId = $_SESSION['authId'];
		//$ip = $_SERVER['REMOTE_ADDR'];
		$ip = getRealIpAddr();
		$URL = $_SERVER['PHP_SELF'];
		$os = getOS();
		$browserInfoArr = array();
		$browserInfoArr = _browser();
		$browserInfo = $browserInfoArr['browser'] . "-" .$browserInfoArr['version'];
		$browserName = str_replace(";","",$browserInfo);
		$machineName = gethostbyaddr($_SERVER['REMOTE_ADDR']);
		//PROVIDER INIT NAME
		$modProv=substr($pro_fname,0,1).substr($pro_lname,0,1);

		$arrAuditTrail[] = array(
								"Pk_Id"=> $pro_id,
								"Table_Name"=>"users",
								"Data_Base_Field_Name"=> "user_type" ,
								"Data_Base_Field_Type"=> fun_get_field_type($usersDataFields,"user_type") ,
								"Filed_Text"=> $modProv."-Provider Type",
								"Filed_Label"=> "pro_type",
								"Operater_Id"=> $opreaterId,
								"Operater_Type"=> getOperaterType($opreaterId) ,
								"IP"=> $ip,
								"MAC_Address"=> $_REQUEST['macaddrs'],
								"URL"=> $URL,
								"Browser_Type"=> $browserName,
								"OS"=> $os,
								"Machine_Name"=> $machineName,
								"Category"=> "admin",
								"Category_Desc"=> "providers",
								"Old_Value"=> ($pro_type) ? trim($pro_type) : ""
							);
		$arrAuditTrail [] = array(
								"Data_Base_Field_Name"=> "user_group_id" ,
								"Filed_Text"=> $modProv."-Provider Group",
								"Filed_Label"=> "pro_group",
								"Data_Base_Field_Type"=> fun_get_field_type($usersDataFields,"user_group_id") ,
								"Old_Value"=> addcslashes(addslashes(trim($pro_group)),"\0..\37!@\177..\377")
							);
		$arrAuditTrail [] = array(
								"Data_Base_Field_Name"=> "pro_title" ,
								"Filed_Text"=> $modProv."-Title",
								"Filed_Label"=> "pro_title",
								"Data_Base_Field_Type"=> fun_get_field_type($usersDataFields,"pro_title") ,
								"Old_Value"=> addcslashes(addslashes(trim($pro_title)),"\0..\37!@\177..\377")
							);
		$arrAuditTrail [] = array(
								"Data_Base_Field_Name"=> "fname" ,
								"Filed_Text"=> $modProv."-First Name",
								"Filed_Label"=> "pro_fname",
								"Data_Base_Field_Type"=> fun_get_field_type($usersDataFields,"fname") ,
								"Old_Value"=> addcslashes(addslashes(trim($pro_fname)),"\0..\37!@\177..\377")
							);
		$arrAuditTrail [] = array(
								"Data_Base_Field_Name"=> "mname" ,
								"Filed_Text"=> $modProv."-Middle Name",
								"Filed_Label"=> "pro_mname",
								"Data_Base_Field_Type"=> fun_get_field_type($usersDataFields,"mname") ,
								"Old_Value"=> addcslashes(addslashes(trim($pro_mname)),"\0..\37!@\177..\377")
							);
		$arrAuditTrail [] = array(
								"Data_Base_Field_Name"=> "lname" ,
								"Filed_Text"=> $modProv."-Last Name",
								"Filed_Label"=> "pro_lname",
								"Data_Base_Field_Type"=> fun_get_field_type($usersDataFields,"lname") ,
								"Old_Value"=> addcslashes(addslashes(trim($pro_lname)),"\0..\37!@\177..\377")
							);
		$arrAuditTrail [] = array(
								"Data_Base_Field_Name"=> "pro_suffix" ,
								"Filed_Text"=> $modProv."-Suffix",
								"Filed_Label"=> "pro_suffix",
								"Data_Base_Field_Type"=> fun_get_field_type($usersDataFields,"pro_suffix") ,
								"Old_Value"=> addcslashes(addslashes(trim($pro_suffix)),"\0..\37!@\177..\377")
							);
		//if(count($pre_phy1)>0){
			//$userAccesses = implode(",",$pre_phy1);
		//}
		$arrAuditTrail [] = array(
								"Data_Base_Field_Name"=> "access_pri" ,
								"Filed_Text"=> $modProv."-Access Privileges",
								"Filed_Label"=> "access_pri_audit",
								"Data_Base_Field_Type"=> fun_get_field_type($usersDataFields,"access_pri") ,
								"Old_Value"=> urlencode($pre_phy)
							);
		$arrAuditTrail [] = array(
								"Data_Base_Field_Name"=> "username" ,
								"Filed_Text"=> $modProv."-Username",
								"Filed_Label"=> "pro_user",
								"Data_Base_Field_Type"=> fun_get_field_type($usersDataFields,"username") ,
								"Old_Value"=> addcslashes(addslashes(trim($pro_user)),"\0..\37!@\177..\377")
							);
		$arrAuditTrail [] = array(
								"Data_Base_Field_Name"=> "password" ,
								"Filed_Text"=> $modProv."-Password",
								"Filed_Label"=> "pro_pass_news",
								"Data_Base_Field_Type"=> fun_get_field_type($usersDataFields,"password") ,
								"Old_Value"=> addcslashes(addslashes(trim($pro_pass)),"\0..\37!@\177..\377")
							);
		$arrAuditTrail [] = array(
								"Data_Base_Field_Name"=> "eRx_user_name" ,
								"Filed_Text"=> $modProv."-eRx UserName",
								"Filed_Label"=> "erx_user_name",
								"Data_Base_Field_Type"=> fun_get_field_type($usersDataFields,"eRx_user_name") ,
								"Old_Value"=> addcslashes(addslashes(trim($erx_user_name)),"\0..\37!@\177..\377")
							);
		$arrAuditTrail [] = array(
								"Data_Base_Field_Name"=> "erx_password" ,
								"Filed_Text"=> $modProv."-eRx Password",
								"Filed_Label"=> "erx_password",
								"Data_Base_Field_Type"=> fun_get_field_type($usersDataFields,"erx_password") ,
								"Old_Value"=> addcslashes(addslashes(trim($erx_password)),"\0..\37!@\177..\377")
							);
		$arrAuditTrail [] = array(
								"Data_Base_Field_Name"=> "default_facility" ,
								"Filed_Text"=> $modProv."-Default Faciltiy",
								"Filed_Label"=> "default_facility",
								"Data_Base_Field_Type"=> fun_get_field_type($usersDataFields,"default_facility") ,
								"Old_Value"=> addcslashes(addslashes(trim($default_facility)),"\0..\37!@\177..\377")
							);
		if(count($gro)>0){
			$userGroup = implode(',',$gro);
		}
		$arrAuditTrail [] = array(
								"Data_Base_Field_Name"=> "gro_id" ,
								"Filed_Text"=> $modProv."-Groups",
								"Filed_Label"=> "groups",
								"Data_Base_Field_Type"=> fun_get_field_type($usersDataFields,"gro_id") ,
								"Old_Value"=> addcslashes(addslashes(trim($userGroup)),"\0..\37!@\177..\377")
							);
		$arrAuditTrail [] = array(
								"Data_Base_Field_Name"=> "Enable_Scheduler" ,
								"Filed_Text"=> $modProv."-Enable Scheduler",
								"Filed_Label"=> "Enable_Scheduler",
								"Data_Base_Field_Type"=> fun_get_field_type($usersDataFields,"Enable_Scheduler") ,
								"Old_Value"=> ($Enable_Scheduler) ? $Enable_Scheduler : ""
							);
		$arrAuditTrail [] = array(
								"Data_Base_Field_Name"=> "provider_color" ,
								"Filed_Text"=> $modProv."-Color",
								"Filed_Label"=> "pro_color",
								"Data_Base_Field_Type"=> fun_get_field_type($usersDataFields,"provider_color") ,
								"Old_Value"=> addcslashes(addslashes(trim($pro_color)),"\0..\37!@\177..\377")
							);
		if($StopOverBooking=="Yes"){
			$auditStopOverBooking = $StopOverBooking;
		}else{
			$auditStopOverBooking = "";
		}
		$arrAuditTrail [] = array(
								"Data_Base_Field_Name"=> "StopOverBooking" ,
								"Filed_Text"=> $modProv."-Stop Over Booking",
								"Filed_Label"=> "StopOverBooking",
								"Data_Base_Field_Type"=> fun_get_field_type($usersDataFields,"StopOverBooking") ,
								"Old_Value"=> ($auditStopOverBooking) ? $auditStopOverBooking : ""
							);
		$arrAuditTrail [] = array(
								"Data_Base_Field_Name"=> "sign" ,
								"Filed_Text"=> $modProv."-Signature",
								"Filed_Label"=> "elem_sign",
								"Data_Base_Field_Type"=> fun_get_field_type($usersDataFields,"sign") ,
								"Old_Value"=> (trim($elem_sign)=='0-0-0:;')? '' :addcslashes(addslashes(trim($elem_sign)),"\0..\37!@\177..\377")
							);
		$arrAuditTrail [] = array(
								"Data_Base_Field_Name"=> "session_timeout" ,

								"Filed_Text"=> $modProv."-Session Timeout",
								"Filed_Label"=> "session_timeout",
								"Data_Base_Field_Type"=> fun_get_field_type($usersDataFields,"session_timeout") ,
								"Old_Value"=> addcslashes(addslashes(trim($session_timeout)),"\0..\37!@\177..\377")
							);
		$arrAuditTrail [] = array(
								"Data_Base_Field_Name"=> "user_npi" ,
								"Filed_Text"=> $modProv."-NPI#",
								"Filed_Label"=> "user_npi",
								"Data_Base_Field_Type"=> fun_get_field_type($usersDataFields,"user_npi") ,
								"Old_Value"=> addcslashes(addslashes(trim($user_npi)),"\0..\37!@\177..\377")
							);
		$arrAuditTrail [] = array(
								"Data_Base_Field_Name"=> "upin" ,
								"Filed_Text"=> $modProv."-UPIN#",
								"Filed_Label"=> "pro_upin",
								"Data_Base_Field_Type"=> fun_get_field_type($usersDataFields,"upin") ,
								"Old_Value"=> addcslashes(addslashes(trim($pro_upin)),"\0..\37!@\177..\377")
							);
		$arrAuditTrail [] = array(
								"Data_Base_Field_Name"=> "MedicareId" ,
								"Filed_Text"=> $modProv."-Medicare ID#",
								"Filed_Label"=> "MedicareId",
								"Data_Base_Field_Type"=> fun_get_field_type($usersDataFields,"MedicareId") ,
								"Old_Value"=> addcslashes(addslashes(trim($MedicareId)),"\0..\37!@\177..\377")
							);
		$arrAuditTrail [] = array(
								"Data_Base_Field_Name"=> "TaxonomyId" ,
								"Filed_Text"=> $modProv."-Taxonomy ID",
								"Filed_Label"=> "TaxonomyId",
								"Data_Base_Field_Type"=> fun_get_field_type($usersDataFields,"TaxonomyId") ,
								"Old_Value"=> addcslashes(addslashes(trim($TaxonomyId)),"\0..\37!@\177..\377")
							);
		$arrAuditTrail [] = array(
								"Data_Base_Field_Name"=> "federaltaxid" ,
								"Filed_Text"=> $modProv."-Federal Tax ID",
								"Filed_Label"=> "pro_tax",
								"Data_Base_Field_Type"=> fun_get_field_type($usersDataFields,"federaltaxid") ,
								"Old_Value"=> addcslashes(addslashes(trim($pro_tax)),"\0..\37!@\177..\377")
							);
		$arrAuditTrail [] = array(
								"Data_Base_Field_Name"=> "MedicaidId" ,
								"Filed_Text"=> $modProv."-Medicaid ID",
								"Filed_Label"=> "MedicaidId",
								"Data_Base_Field_Type"=> fun_get_field_type($usersDataFields,"MedicaidId") ,
								"Old_Value"=> addcslashes(addslashes(trim($MedicaidId)),"\0..\37!@\177..\377")
							);
		$arrAuditTrail [] = array(
								"Data_Base_Field_Name"=> "imwemr_id" ,
								"Filed_Text"=> "iMedic ID",
								"Filed_Label"=> "imwemr_id",
								"Data_Base_Field_Type"=> fun_get_field_type($usersDataFields,"imwemr_id") ,
								"Old_Value"=> addcslashes(addslashes(trim($imwemr_id)),"\0..\37!@\177..\377")
							);
		$arrAuditTrail [] = array(
								"Data_Base_Field_Name"=> "federaldrugid" ,
								"Filed_Text"=> $modProv."-Federal Drug ID",
								"Filed_Label"=> "pro_drug",
								"Data_Base_Field_Type"=> fun_get_field_type($usersDataFields,"federaldrugid") ,
								"Old_Value"=> addcslashes(addslashes(trim($pro_drug)),"\0..\37!@\177..\377")
							);
		$arrAuditTrail [] = array(
								"Data_Base_Field_Name"=> "licence" ,
								"Filed_Text"=> $modProv."-License Number",
								"Filed_Label"=> "pro_lic",
								"Data_Base_Field_Type"=> fun_get_field_type($usersDataFields,"licence") ,
								"Old_Value"=> addcslashes(addslashes(trim($pro_lic)),"\0..\37!@\177..\377")
							);
		$arrAuditTrail [] = array(
								"Data_Base_Field_Name"=> "additional_info" ,
								"Filed_Text"=> $modProv."-Additional Info",
								"Filed_Label"=> "pro_add",
								"Data_Base_Field_Type"=> fun_get_field_type($usersDataFields,"additional_info") ,
								"Old_Value"=> addcslashes(addslashes(trim($pro_add)),"\0..\37!@\177..\377")
							);
		return urlencode(serialize($arrAuditTrail));
	}

	//Returns Allscript TouchWorks credentials based on request
	function manage_as_cred($callFrom = '', $provId = '', $params = array()){
		if(empty($callFrom) || empty($provId)) return false;
		$returnData = array();
		$user = $pass = $entryCode = '';

		switch($callFrom){
			case 'show':
				/*as_password,*/
				$sqlQry = imw_query("SELECT username, as_username, as_entry_code FROM users WHERE id = '".$provId."'");
				if($sqlQry && imw_num_rows($sqlQry) > 0){
					$rowFetch = imw_fetch_assoc($sqlQry);
					$returnData = $rowFetch;
				}
			break;

			case 'save':
				if(count($params) > 0){
					$user = (isset($params['as_username']) && empty($params['as_username']) == false) ? $params['as_username'] : '';
					/*$pass = (isset($params['as_password']) && empty($params['as_password']) == false) ? $params['as_password'] : '';*/
					$entryCode = (isset($params['as_entry_code']) && empty($params['as_entry_code']) == false) ? $params['as_entry_code'] : '';
					$username = (isset($params['imwUser']) && empty($params['imwUser']) == false) ? $params['imwUser'] : '';

					if(empty($user) == false && empty($provId) == false){
						/*as_password = '".$pass."', */
						$rq = "UPDATE users SET as_username = '".$user."', `as_entry_code`='".$entryCode."' WHERE id = '".$provId."'";
						$rq_obj = imw_query($rq);
						if($rq_obj){
							$returnData['as_username'] = $user;
							/*$returnData['as_password'] = $pass;*/
							$returnData['entryCode'] = $entryCode;
							$returnData['username'] = $username;
							$returnData['Success'] = 'Credentials Saved Successfully.';
						}
					}

				}
			break;
		}

		if(count($returnData) == 0) $returnData = false;
		return json_encode($returnData);
	}

	// DSS user info
	function manage_dss_user_info($callFrom = '', $provId = '', $params = array()){
		if(empty($callFrom) || empty($provId)) return false;
		$returnData = array();

		switch($callFrom){
			case 'show':
				$sqlQry = imw_query("SELECT dss_elec_sign FROM users WHERE id = '".$provId."'");
				if($sqlQry && imw_num_rows($sqlQry) > 0){
					$rowFetch = imw_fetch_assoc($sqlQry);
					$returnData = $rowFetch;
				}
			break;

			case 'save':
				if(count($params) > 0){
					$elecSign = (isset($params['electronicSignature']) && empty($params['electronicSignature']) == false) ? $params['electronicSignature'] : '';

					if(empty($elecSign) == false && empty($provId) == false){

						// Validate ESignature on DSS
						include_once( $GLOBALS['srcdir'].'/dss_api/dss_core.php' );
						$objDss = new Dss_core();

						$sqlQuery = "SELECT sso_identifier FROM users WHERE id = ".$provId;
						$sqlQueryRes = imw_query($sqlQuery);
						$row = imw_fetch_assoc($sqlQueryRes);
						$duz = $row['sso_identifier'];
						if(!empty($duz)) {
							$validated = $objDss->validateESignature($duz, $elecSign);
							if( $validated == 1) {
								$rq = "UPDATE users SET dss_elec_sign = '".$elecSign."' WHERE id = '".$provId."'";
								$rq_obj = imw_query($rq);
								if($rq_obj){
									// $returnData['electronicSignature'] = $elecSign;
									$returnData['Success'] = 'Data Saved Successfully.';
								}
							} else {
								$returnData['Error'] = 'Invalid ESignature';
							}
						} else {
							$returnData['Error'] = 'User DUZ not found';
						}
					}
				}
			break;
		}

		if(count($returnData) == 0) $returnData = false;
		return json_encode($returnData);
	}
/*
     function fetchMenuArray() {
        $setting = array();
        $setting['priv_group'] = 'Business Unit';
        $setting['priv_facility'] = 'Facilities';
        $setting['priv_admin_Heard_About_Us'] = 'Heard About Us';
        $setting['priv_admin_Provider_Groups'] = 'Provider Groups';
        $setting['priv_ref_physician'] = 'Ref. Physician';
        $setting['priv_provider_management'] = 'Users';
        $setting['priv_admin_CDS_Intervention'] = 'CDS Intervention';
        $setting['priv_admin_Updox'] = 'Updox';
        $setting['priv_grp_prvlgs'] = 'Group Privileges';
        $setting['priv_chng_prvlgs'] = 'Change Privileges';
        $setting['priv_rules_mngr'] = 'Rules Manager';
        $setting['priv_Office_Hours_Settings'] = 'Office Hours Settings';

        $billing = array();
        $billing['priv_billing_Adjustment_Codes'] = 'Adjustment Codes';
        $billing['priv_billing_Cases'] = 'Cases';
        $billing['priv_billing_CL_Charges'] = 'CL Charges';
        $billing['priv_billing_CPT'] = 'CPT';
        $billing['priv_billing_Department'] = 'Department';
        $billing['priv_billing_Discount_Codes'] = 'Discount Codes';
        $billing['priv_billing_Dx_Codes'] = 'Dx Codes';
        $billing['priv_billing_Fee_Table'] = 'Fee Table';
        $billing['priv_billing_ICD_10'] = 'ICD-10';
        $billing['priv_billing_Insurance'] = 'Insurance';
        $billing['priv_billing_Insurance_Groups'] = 'Insurance Groups';
        $billing['priv_billing_Messages'] = 'Messages';
        $billing['priv_billing_Modifiers'] = 'Modifiers';
        $billing['priv_billing_Phrases'] = 'Phrases';
        $billing['priv_billing_POE'] = 'POE';
        $billing['priv_billing_Policies'] = 'Policies';
        $billing['priv_billing_POS_Codes'] = 'POS Codes';
        $billing['priv_billing_POS_Facilities'] = 'POS Facilities';
        $billing['priv_billing_Pre_Auth_Templates'] = 'Pre Auth Templates';
        $billing['priv_billing_Proc_Codes'] = 'Proc Codes';
		$billing['priv_billing_Reason_Codes'] = 'Reason Codes';
        $billing['priv_billing_Revenue_Codes'] = 'Revenue Codes';
		$billing['priv_billing_Status'] = 'Status';
		$billing['priv_billing_Test_CPT_Preference'] = 'Test CPT Preference';
		$billing['priv_billing_Type_Of_Service'] = 'TOS (Type of Service)';
		$billing['priv_billing_Write_Off_Codes'] = 'Write Off Codes';
		$billing['priv_billing_Zip_Codes'] = 'Zip Codes';
        $billing['priv_billing_Payment_Methods'] = 'Payment Methods';
        $billing['priv_billing_Manage_POS'] = 'Manage POS';

        $clinical = array();
        $clinical['priv_admn_clinical_Allergies'] = 'Allergies';
        $clinical['priv_admn_clinical_AP_Policies'] = 'AP Policies';
        $clinical['priv_admn_clinical_Botox'] = 'Botox';
        $clinical['priv_admn_clinical_Exam_Extensions'] = 'Clinical Exam Extensions';
        $clinical['priv_admn_clinical_Custom_HPI'] = 'Custom HPI';
        $clinical['priv_admn_clinical_Drawings'] = 'Drawings';
        $clinical['priv_admn_clinical_Epost'] = 'Epost';
        $clinical['priv_erx_preferences'] = 'eRx Preferences';
        $clinical['priv_admn_clinical_FU'] = 'F/U';
        $clinical['priv_immunization'] = 'Immunization';
        $clinical['priv_admn_clinical_Labs_Rad'] = 'Labs/Rad';
        $clinical['priv_admn_clinical_Med'] = 'Med.';
        $clinical['priv_admn_clinical_Ophth_Drops'] = 'Ophth. Drops';
        $clinical['priv_admn_clinical_Order'] = 'Order';
        $clinical['priv_admn_clinical_Order_Sets'] = 'Order Sets';
        $clinical['priv_admn_clinical_Order_Templates'] = 'Order Templates';
        $clinical['priv_admn_clinical_Phrases'] = 'Phrases';
        $clinical['priv_admn_clinical_Procedures'] = 'Procedures';
        $clinical['priv_admn_clinical_Pt_Chart_Locked'] = 'Pt Chart Locked';
        $clinical['priv_admn_clinical_Rx_Template'] = 'Rx Template';
        $clinical['priv_admn_clinical_SCP_Reasons'] = 'SCP Reasons';
        $clinical['priv_admin_scp'] = 'Site Care Plan';
        $clinical['priv_admn_clinical_Specialty'] = 'Specialty';
        $clinical['priv_admn_clinical_Sx'] = 'Sx';
        $clinical['priv_admn_clinical_Sx_Planning'] = 'Sx Planning';
        $clinical['priv_admn_clinical_Template'] = 'Template';
        $clinical['priv_admn_clinical_Test_Template'] = 'Test Template';
        $clinical['priv_admn_clinical_Visit'] = 'Visit';
        $clinical['priv_vs'] = 'VS';
        $clinical['priv_admn_clinical_WNL'] = 'WNL';

        $documents = array();
        $documents['priv_admn_docs_Collection'] = 'Collection';
        $documents['priv_admn_docs_Consent'] = 'Consent';
        $documents['priv_admn_docs_Consult'] = 'Consult';
        $documents['priv_admn_docs_Education'] = 'Education';
        $documents['priv_admn_docs_Instructions'] = 'Instructions';
        $documents['priv_admn_docs_Logos'] = 'Logos';
        $documents['priv_admn_docs_Op_Notes'] = 'Op Notes';
        $documents['priv_admn_docs_Package'] = 'Package';
        $documents['priv_admn_docs_Panels'] = 'Panels';
        $documents['priv_admn_docs_Prescriptions'] = 'Prescriptions';
        $documents['priv_admn_docs_Pt_Docs'] = 'Pt. Docs';
        $documents['priv_admn_docs_Recalls'] = 'Recalls';
        $documents['priv_admn_docs_Scan_Upload_Folders'] = 'Scan/Upload Folders';
        $documents['priv_set_margin'] = 'Set Margin';
        $documents['priv_admn_docs_Smart_Tags'] = 'Smart Tags';
        $documents['priv_admn_docs_Statements'] = 'Statements';

        $iasc_link = array();
        $iasc_link['priv_admn_iasc_iASC_Link_Settings'] = 'iASC Link Settings';
        $iasc_link['priv_admn_iasc_Surgery_Consent_Form'] = 'Surgery Consent Form';

        $iMedic_Monitor = array();
        $iMedic_Monitor['priv_admn_imm_iMedic_Monitor'] = 'iMedic Monitor';
        $iMedic_Monitor['priv_room_assign'] = 'Room Assign';
        $iMedic_Monitor['priv_Manage_Columns'] = 'Manage Columns';

        $iPortal = array();
        $iPortal['priv_admn_ipl_Auto_Responder'] = 'Auto Responder';
        $iPortal['priv_admn_ipl_iPortal_Settings'] = 'iPortal Settings';
        $iPortal['priv_admn_ipl_Preferred_Images'] = 'Preferred Images';
        $iPortal['priv_admn_ipl_Print_Preferences'] = 'Print Preferences';
        $iPortal['priv_admn_ipl_Security_Questions'] = 'Security Questions';
        $iPortal['priv_admn_ipl_Set_Survey'] = 'Set Survey';
        $iPortal['priv_admn_ipl_Survey'] = 'Survey';

        $Manage_Fields = array();
        $Manage_Fields['priv_admn_mcf_Custom_Fields'] = 'Custom Fields';
        $Manage_Fields['priv_admn_mcf_General_Health_Questns'] = 'General Health Questions';
        $Manage_Fields['priv_admn_mcf_Ocular_Questions'] = 'Ocular Questions';
        $Manage_Fields['priv_admn_mcf_Practice_Fields'] = 'Practice Fields';
        $Manage_Fields['priv_admn_mcf_Tech_Fields'] = 'Tech. Fields';

        $Admin_Optical = array();
        $Admin_Optical['priv_admn_optical_Frames'] = 'Frames';
        $Admin_Optical['priv_admn_optical_Lenses'] = 'Lenses';
        $Admin_Optical['priv_admn_optical_Vendor'] = 'Vendor';
        $Admin_Optical['priv_admn_optical_Contact_Lens'] = 'Contact Lens';
        $Admin_Optical['priv_admn_optical_Color'] = 'Color';
        $Admin_Optical['priv_admn_optical_Lens_Codes'] = 'Lens Codes';
        $Admin_Optical['priv_admn_optical_Make'] = 'Make';

        $Admin_iOptical = array();
        $Admin_iOptical['priv_Optical_POS'] = 'POS';
        $Admin_iOptical['priv_Optical_Inventory'] = 'Inventory';
        $Admin_iOptical['priv_Optical_Admin'] = 'Admin';
        $Admin_iOptical['priv_Optical_Reports'] = 'Reports';

        $Admin_Reports = array();
        $Admin_Reports['priv_admn_reports_Audit_Policies'] = 'Audit Policies';
        $Admin_Reports['priv_admn_reports_Compliance'] = 'Compliance';
        $Admin_Reports['priv_admn_reports_CPT_Groups'] = 'CPT Groups';
        $Admin_Reports['priv_admn_reports_Fac_Groups'] = 'Fac. Groups';
        $Admin_Reports['priv_admn_reports_Financials'] = 'Financials';
        $Admin_Reports['priv_admn_reports_Practice_Analytic'] = 'Practice Analytic';
        $Admin_Reports['priv_admn_reports_Ref_Groups'] = 'Ref. Groups';
        $Admin_Reports['priv_admn_reports_Scheduler'] = 'Scheduler';

        $Admin_Scheduler = array();
        $Admin_Scheduler['priv_admn_sch_Available'] = 'Available';
        $Admin_Scheduler['priv_admn_sch_Chain_Event'] = 'Chain Event';
        $Admin_Scheduler['priv_admn_sch_Procedure_Templates'] = 'Procedure Templates';
        $Admin_Scheduler['priv_admn_sch_Provider_Schedule'] = 'Provider Schedule';
        $Admin_Scheduler['priv_admn_sch_Schedule_Reasons'] = 'Schedule Reasons';
        $Admin_Scheduler['priv_admn_sch_Schedule_Status'] = 'Schedule Status';
        $Admin_Scheduler['priv_admn_sch_Schedule_Templates'] = 'Schedule Templates';
        //$Admin_Scheduler['priv_admn_sch_Setting'] = 'Setting';

        $IOLs = array();
        $IOLs['priv_admn_iols_Manage_Lenses'] = 'Manage Lenses';
        $IOLs['priv_admn_iols_Lens_Calculators'] = 'Lens Calculators';
        $IOLs['priv_admn_iols_IOL_Users_Lens'] = 'IOL Users Lens';

         //Reports Tabs Privileges Starts
        $scheduler=array();
        $scheduler = $this->fetchByReportType('scheduler');
        $scheduler['priv_report_Patient_Monitor'] = 'Patient Monitor';
        $scheduler['priv_report_Day_Face_Sheet'] = 'Day Face Sheet';
        $scheduler['priv_report_Appointment_Report'] = 'Appointment Report';
        $scheduler['priv_report_Appointment_information'] = 'Appointment information';
        $scheduler['priv_report_Patient_Document'] = 'Patient Document';
        $scheduler['priv_report_Sx_Planning_Sheet'] = 'Sx Planning Sheet';
        $scheduler['priv_sc_recall_fulfillment'] = 'Recall Fulfillment';
        $scheduler['priv_report_Consult_Letters'] = 'Consult Letters';
        $scheduler['priv_report_Scheduler_Report'] = 'Scheduler Report';
        $scheduler['priv_report_Patients_CSV_Export'] = 'Patients CSV Export';
        $scheduler['priv_report_Surgery_Appointments'] = 'Surgery Appointments';
        $scheduler['priv_report_RTA_Query'] = 'RTA Query';
        $scheduler['priv_report_Clinical_Productivity'] = 'Clinical Productivity';
        $scheduler['priv_report_Providers_Report'] = 'Providers Report';
        $scheduler['priv_report_Procedures_Report'] = 'Procedures Report';

        $analytic_report = array();
        $analytic_report = $this->fetchByReportType('practice_analytic');

        $financial_report = array();
        $financial_report = $this->fetchByReportType('financial');
        $financial_report['priv_prev_hcfa'] = 'Previous HCFA';
        $financial_report['priv_report_eid_status'] = 'EID Status';
        $financial_report['priv_report_EID_Payments'] = 'EID Payments';
        $financial_report['priv_tfl_proof'] = 'TFL Proof';
        //Statements
        $financial_report['priv_new_statements'] = 'New Statement';
        $financial_report['priv_prev_statements'] = 'Previous Statement';
        $financial_report['priv_statements_pay'] = 'Statement Payments';
        //Scheduled Reports
        $financial_report['priv_saved_scheduled'] = 'Saved Schedules';
        $financial_report['priv_executed_report'] = 'Executed Reports';
        $financial_report['priv_pt_status'] = 'PT Status';
		$financial_report['priv_Ins_Enc'] = 'Institutional Encounters';

        $compliance_report = array();
        $compliance_report = $this->fetchByReportType('compliance');
        $compliance_report['priv_report_QRDA'] = 'QRDA';
        $compliance_report['priv_report_CQM_Import'] = 'CQM Import';

        $CCD_report = array();
        $CCD_report['priv_ccd_export'] = 'CCD Export';

        $API_report = array();
        $API_report['priv_report_Access_Log'] = 'Access Log';
        $API_report['priv_report_Call_Log'] = 'Call Log';

        $State_report = array();
        $State_report['priv_report_KY_State_Report'] = 'KY State Report';
        $State_report['priv_report_TN_State_Report'] = 'TN State Report';
        $State_report['priv_report_NC_State_Report'] = 'NC State Report';
        $State_report['priv_report_IL_State_Report'] = 'IL State Report';
		$State_report['priv_report_PA_State_Report'] = 'PA State Report';
        $State_report['priv_report_TX_State_Report'] = 'TX State Report';
		$State_report['priv_report_ASC_State_Report'] = 'ASC State Report';
		$State_report['priv_report_new_state_report'] = 'New State Report';

        $opticals_report = array();
        $opticals_report['priv_cn_reports'] = 'Contact Lens Report';
        $opticals_report['priv_contact_lens'] = 'Contact Lens Orders';
        $opticals_report['priv_glasses'] = 'Glasses';

        $reminders_report = array();
        $reminders_report['priv_dat_appts'] = 'Day Appts';
        $reminders_report['priv_recalls'] = 'Recalls';
        $reminders_report['priv_reminder_lists'] = 'Reminder Lists';

        $clinical_report = array();
        $clinical_report = $this->fetchByReportType('clinical');
		$clinical_report['priv_report_Clinical_Report'] = 'Clinical Report';
        $clinical_report['priv_report_Auto_Finalize_Charts_Report'] = 'Auto Finalize Charts Report';


        $rules_report = array();
        $rules_report['priv_report_A_R_Aging_Rules'] = 'A/R Aging Rules';

        $iportal_report = array();
        $iportal_report['priv_report_Survey'] = 'Survey';
        //Reports Tabs Privileges Ends

        $menu_arr = array();
        $menu_arr['Admin'] = $setting;
        $menu_arr['Billing'] = $billing;
        $menu_arr['Clinical'] = $clinical;
        $menu_arr['Documents'] = $documents;
        $menu_arr['iASC_Link'] = $iasc_link;
        $menu_arr['iMedic_Monitor'] = $iMedic_Monitor;
        $menu_arr['iPortal'] = $iPortal;
        $menu_arr['Manage_Fields'] = $Manage_Fields;
        $menu_arr['Optical_Settings'] = $Admin_Optical;
        $menu_arr['Setting_Reports'] = $Admin_Reports;
        $menu_arr['Setting_Scheduler'] = $Admin_Scheduler;
        $menu_arr['IOLs'] = $IOLs;
        $menu_arr['iOptical'] = $Admin_iOptical;

        $menu_arr['Scheduler'] = $scheduler;
        $menu_arr['Practice_Analytics'] = $analytic_report;
        $menu_arr['Financials'] = $financial_report;
        $menu_arr['Compliance'] = $compliance_report;
        $menu_arr['CCD'] = $CCD_report;
        $menu_arr['API'] = $API_report;
        $menu_arr['State'] = $State_report;
        $menu_arr['Optical'] = $opticals_report;
        $menu_arr['Reminders'] = $reminders_report;
        $menu_arr['ReportClinical'] = $clinical_report;
        $menu_arr['Rules'] = $rules_report;
        $menu_arr['ReportiPortal'] = $iportal_report;

        return $menu_arr;
    }

    function fetchByReportType($report_type) {
        $report=array();
        $query = imw_query("SELECT * FROM `custom_reports` WHERE `report_type` = '".$report_type."' and `delete_status` = 0 and `default_report`=1 ");
        if(imw_num_rows($query) > 0){
            while($row = imw_fetch_assoc($query)){
                $report_id = $row['id'];
                $report_name = $row['template_name'];
                $report_name = str_replace(array(" ", "/","-"), "_", $report_name);
                $report_name=strtolower($report_name);
                $report['priv_report_'.$report_name.$report_id] = $row['template_name'];
            }
        }
        return $report;
    }
*/
    function resetPrivilegesCheckbox(){
        $pro_id = "";
		if(isset($_REQUEST["provId"]) && !empty($_REQUEST["provId"])){
			$pro_id = $_REQUEST["provId"];
		}
		$pro_id=imw_real_escape_string($pro_id);

        $query_rs=imw_query("Select id,access_pri from users where id=$pro_id ");
        $row = imw_fetch_assoc($query_rs);
        $user_priviliges=unserialize(html_entity_decode(trim($row['access_pri'])));
        return json_encode(array('user_priviliges'=>$user_priviliges));
    }


    //Add Update Users to Erp Portal
    function addUpdateUserOnErpPortal($request=array(),$pro_id=0,$privileges=array()) {
        $userDetails = $doctorDetails = array();

        if( count($request)>0 && ($pro_id>0 || $pro_id!='') ) {
            if(isset($request["pro_type"]) && !empty($request["pro_type"])){
                $arr_temp_pro_type = explode("-",$request["pro_type"]);
                $user_type = $arr_temp_pro_type[0];
            }

            if(isset($request["pro_group"]) && !empty($request["pro_group"])){
                $user_group_id = $request["pro_group"];
            }

            $default_facility = (isset($request["default_facility"]) && !empty($request["default_facility"]))? $request["default_facility"] : "";

            $pro_lname = (isset($request["pro_lname"]) && !empty($request["pro_lname"])) ? trim($request["pro_lname"]) : "";
            $pro_fname = (isset($request["pro_fname"]) && !empty($request["pro_fname"])) ? trim($request["pro_fname"]) : "";
            $pro_mname = (isset($request["pro_mname"]) && !empty($request["pro_mname"])) ? trim($request["pro_mname"]) : "";
            $pro_fullname = $pro_lname." ".$pro_fname;

            $pro_username = (isset($request["pro_user"]) && !empty($request["pro_user"])) ? trim($request["pro_user"]) : "";
            $pro_password = (isset($request["pro_pass_news"]) && !empty($request["pro_pass_news"])) ? trim($request["pro_pass_news"]) : "";


            $locations=$locations_arr=array();
            $fac_sql="SELECT id FROM facility WHERE id IN(".$request['selected_sch_facs'].") and erp_id!='' ";
            $fac_res=imw_query($fac_sql);
            if($fac_res && imw_num_rows($fac_res)>0){
                while( $facility = imw_fetch_assoc($fac_res) ) {
                    $locations_arr[$facility['id']]=$facility['id'];
                }
            }

            $portal_access=false;
            if($privileges['el_sel_portal']==1) {
                $portal_access=true;
            }

            $prov_active=true;
            $erp_external_id=$pro_id;
            $erp_user_postalAddresses_id=$erp_user_contact_id=$erp_user_id=$erp_doctor_id="";

            $prov_sql="SELECT id,delete_status,erp_user_id,erp_doctor_id,erp_user_contact_id,erp_user_postaladdresses_id,default_facility FROM users WHERE id=".$pro_id." ";
            $prov_res=imw_query($prov_sql);
            if($prov_res && imw_num_rows($prov_res)>0){
                while( $user = imw_fetch_assoc($prov_res) ) {
                    $erp_user_id = $user['erp_user_id'];
                    $erp_doctor_id = $user['erp_doctor_id'];
                    $erp_external_id = $user['id'];
                    $erp_user_contact_id=$user['erp_user_contact_id'];
                    $erp_user_postalAddresses_id=$user['erp_user_postaladdresses_id'];
                    if(count($locations_arr)>0) {
                        $locations[]=$locations_arr[$user['default_facility']];
                    }
                    if($user['delete_status']==1) {
                        $prov_active=false;
                    }

                }
            }

            if( $user_type==1 ){
                $data=array();
                $data["lastName"]= $pro_lname;
                $data["firstName"]= $pro_fname;
                $data["alias"]= "";
                $data["active"]= $prov_active;
                $data["inHouse"]= true;
                $data["locationExternalId"]= "";
                $data["secureRecipientExternalId"]= $erp_external_id;
                $data["emailAddress"]= "";
                $data["locations"]= $locations;
                $data["id"]= $erp_doctor_id;
                $data["externalId"]= $erp_external_id;

                require_once($GLOBALS['srcdir']."/erp_portal/doctors.php");
                $obj_doctors = new Doctors();

                $doctorDetails = $obj_doctors->addUpdateDoctor($data);

                if(count($doctorDetails)>0) {
                    $erp_doctor_external_id=$doctorDetails['externalId']; //this is IMW provider id
                    $erp_doctor_id=$doctorDetails['id'];

                    $update_sql = "UPDATE users SET erp_doctor_id='".$erp_doctor_id."' WHERE id=".$pro_id." ";
                    imw_query($update_sql);
                }
            }

        }
    }


    function deleteUserOnErpPortal($users_ids="") {
        if(empty($users_ids)==false) {
            $prov_sql="SELECT id,user_type,erp_user_id,erp_doctor_id FROM users WHERE id IN(".$users_ids.") ";
            $prov_res=imw_query($prov_sql);
            if($prov_res && imw_num_rows($prov_res)>0){
                while( $user = imw_fetch_assoc($prov_res) ) {
                    $data=$userDetails=array();
                    $data['externalId'] = $user['id'];

                    if($user['user_type']==1 && $user['erp_doctor_id']!='') {
                        include_once($GLOBALS['srcdir']."/erp_portal/doctors.php");
                        $obj_doctors = new Doctors();
                        $doctorDetails = $obj_doctors->deleteDoctor($data);
                    }

                }
            }
        }
    }

	public function portal_refill_direct_modal()
	{
		$jsAlertNo = 0;
		$arrUserRecords = $this->get_user_details($_REQUEST["provid"]);

		$portal_refill_direct_access = array();
		//Allow direct access to
		if(empty($arrUserRecords[0]['portal_refill_direct_access']) == false){
			$portal_refill_direct_access = explode(',',$arrUserRecords[0]['portal_refill_direct_access']);
		}
		
		$prov_opt_str = '';
		$all_prov_arr = $this->get_refill_user_details_all($_REQUEST["provid"]);
		foreach($all_prov_arr as $obj){
			$prov_name = core_name_format($obj['pro_lname'], $obj['pro_fname'], $obj['pro_mname']);
			$selected = '';
			if(is_array($portal_refill_direct_access) && in_array($obj['id'],$portal_refill_direct_access)){
				$selected = 'selected';
			}
			$prov_opt_str .= '<option value="'.$obj['id'].'" '.$selected.'>'.$prov_name.'</opiton>';
		}

		$html  = '';
		$html .= '<div id="portal_refill_direct_div" class="modal" role="dialog" style="z-index:1051;" >';
		$html .= '<div class="modal-dialog modal-sm">';
		// Modal content
		$html .= '<div class="modal-content">';
		$html .= '<form name="refill_direct_form" method="post" id="refill_direct_form" class="margin_0" action="" autocomplete="off">';
		// Modal Header
		$html .= '<div class="modal-header bg-primary">';
		$html .= '<button type="button" class="close" data-dismiss="modal">×</button>';
		$html .= '<h4 class="modal-title" id="modal_title">Allow re-quest re-direct to</h4>';
		$html .= '</div>';
		// Modal Body
		$html .= '<div class="modal-body" style="max-height:450px; overflow:hidden; overflow-y:auto;">';
		$html .= '<input type="hidden" name="hid_save" value="save">';
		$html .= '<input type="hidden" name="pro_id" value="'.(isset($_REQUEST["provid"]) ? $_REQUEST["provid"] : "").'">';


		$html .= '<div class="row">';
		$html .= '<label for="refill_direct_access">Allow Refill Direct access:</label><br />';
		$html .= '<div style="position:fixed; z-index:1; width:100%;">
					<select name="refill_direct_access[]" id="refill_direct_access" class="selectpicker" data-title="Select" data-size="6" data-live-search="true" multiple>'.$prov_opt_str.'</select>
				</div>';
		$html .= '</div>';

		$html .= '<div class="clearfix">&nbsp;</div>';
		$html .= '</div>';
		// Modal Footer
		$html .= '<div class="modal-footer">';
		$html .= '</div>';

		$html .= '</div>';
		$html .= '</div>';

		return $html;
	}
	
	
	public function save_portal_refill_direct()
	{
		$return = '';
		if(isset($_REQUEST["hid_save"]) && $_REQUEST["hid_save"] == "save")
		{
			$pro_id = $_REQUEST["pro_id"];
			
			$refill_direct_access = '';
			if( isset($_POST['refill_direct_access']) && count($_POST['refill_direct_access']) > 0 )
				$refill_direct_access = implode(',', $_POST['refill_direct_access']);

			$qry = "UPDATE users SET portal_refill_direct_access = '".$refill_direct_access."' WHERE id = '".$pro_id."'";
			$sql = imw_query($qry);
			if(imw_affected_rows() > 0)
			{
				$return = "Refill Direct Access Saved Successfully."	;
			}
		}
		return $return;
	}
	
	public function get_refill_user_details_all($id){
		$return_arr = array();
		$where_cond = '';
		if($id){
			$where_cond = "id != '".$id."' and";
		}

		$query = imw_query("select id, fname as pro_fname, mname as pro_mname,lname as pro_lname from `users` where ".$where_cond." delete_status = 0");
		if(imw_num_rows($query) > 0){
			while($row = imw_fetch_assoc($query)){
				$return_arr[] = $row;
			}
		}
		return $return_arr;
	}

}
?>

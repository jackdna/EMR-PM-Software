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
Purpose: Add/Modify/Delete facility
Access Type: Direct
*/

set_time_limit(600);
require_once("../../../config/globals.php");

$_REQUEST['so'] = xss_rem($_REQUEST['so'], 2, 'sanitize');	/* Sanitize unwanted characters - Security Fix */

$task	= isset($_REQUEST['task']) ? trim($_REQUEST['task']) : '';
$s		= isset($_REQUEST['s']) ? trim($_REQUEST['s']) : '';
$so		= isset($_REQUEST['so']) ? trim($_REQUEST['so']) : 'name';
$soAD	= (strtoupper($_REQUEST['soAD'])=='DESC') ? 'DESC' : 'ASC';	/* Prevent arbitrary values - Security Fix */

$p		= isset($_REQUEST['p']) ? trim($_REQUEST['p']) : '';
$f		= isset($_REQUEST['f']) ? trim($_REQUEST['f']) : '';

$uploadPath = $GLOBALS['fileroot']."/data/".constant('PRACTICE_PATH')."/facilitylogo/";
$uploadPath_web = $GLOBALS['webroot']."/data/".constant('PRACTICE_PATH')."/facilitylogo/";
if($_FILES['files']!=''){
	$file_info = pathinfo($_FILES[ 'files' ]['name'][0]);
	$logoName = $_SESSION['authId'].'_'.date('mdYHis').'_'.$file_info['filename'].'.'.$file_info['extension'];
	$rs=imw_query("Select id,logo FROM facility WHERE facility_type='1'");
	$res=imw_fetch_array($rs);
	$fac_id = $res['id'];
	$oldLogo = $res['logo'];
	
	
	$_FILES[ 'files' ]['name'][0] = $logoName;
	
	require_once($GLOBALS['srcdir'].'/upload/server/php/UploadHandler.php');
	
	// Change Default Option for upload handler before calling class constructor
	$options = array(
		'script_url' => $GLOBALS['php_server'].'/interface/admin/facility/upload_win.php',
		'upload_dir' => $uploadPath,
		'upload_url' => $uploadPath_web,
		'access_control_allow_origin' => '*','access_control_allow_credentials' => false,
		'access_control_allow_methods' => array('OPTIONS','HEAD','GET','POST','PUT','PATCH','DELETE'),
		'access_control_allow_headers' => array('Content-Type','Content-Range','Content-Disposition'),
		'inline_file_types' => '/\.(gif|jpe?g|png)$/i', 'accept_file_types' => '/\.(jpe?g)$/i',
		'max_file_size' => null,'min_file_size' => 1,'max_number_of_files' => null,'max_width'=>null,'max_height'=>null,'min_width'=>1,'min_height'=>1,
		'discard_aborted_uploads'=>true,'orient_image'=>false,'image_versions'=>array()
	);
	
	$upload_handler = new UploadHandler($options,true);
	
	$response = (object) $upload_handler->response;
		
		if( $response->files )
		{
			
			foreach($response->files as $file)
			{
				if($file->type && !$file->error && $file->url )
				{
					if($fac_id>0){
						$rs1=imw_query("Update facility SET logo='".$file->name."' WHERE id='".$fac_id."'");
						if($rs1){
							if($oldLogo!=''){
								if(file_exists($uploadPath.$oldLogo)){
									unlink($uploadPath.$oldLogo);
								}
							}
						}
					}
				}
			}
		}
	
}
switch($task){
	case 'confirm_schedule':
		$id = $_POST['pos_id'];
		//check do this facilities have any active appointment in future
		$q="select id from schedule_appointments where sa_facility_id IN (".$id.") and sa_test_id = 0 and sa_patient_app_status_id NOT IN (203,201,18,19,20) AND IF( sa_patient_app_status_id =271, sa_patient_app_show =0, sa_patient_app_show <>2 ) and sa_app_start_date>='".date('Y-m-d')."'";
		$r = imw_query($q);
		$total_appointment=imw_num_rows($r);
		echo ($total_appointment>0)?$total_appointment:0;
	case 'delete':
		require_once('../../../library/classes/admin/scheduler_admin_func.php');
		$id = $_POST['pkId'];
		if(!$id)return false;
		$del_reason=trim(addslashes($_REQUEST['hidd_reason_text']));
		$select_qry="SELECT * FROM facility WHERE id IN ($id)";
		$res_qry=imw_query($select_qry);
		$arr_data=array();
		while($row_qry=imw_fetch_assoc($res_qry)){
			$arr_data[]=$row_qry;
		}
        if(count($arr_data) > 0){
            foreach($arr_data as &$obj){
                if(is_array($obj)){
                    foreach($obj as $key => $val){
                        $obj[$key] = addslashes(htmlentities($val));
                    }
                }
            }
        }
		$full_data=trim(serialize($arr_data));
		$qry_del="INSERT INTO deleted_table_log set table_name='facility',data='".$full_data."',del_reason='".$del_reason."',del_operator='".$_SESSION['authId']."', del_date_time='".date("Y-m-d H:i:s",time())."'";
		$res=imw_query($qry_del) or die(imw_error().$qry_del);
		if($res){
			//MOVE ALL FUTURE APPT'S TO RESCHEDULE LIST
			$q="select id from schedule_appointments where sa_facility_id IN (".$id.") and sa_test_id = 0 and sa_patient_app_status_id NOT IN (203,201,18,19,20) AND IF( sa_patient_app_status_id =271, sa_patient_app_show =0, sa_patient_app_show <>2 ) and sa_app_start_date>='".date('Y-m-d')."'";
			$res 	= imw_query($q);
			while($row = imw_fetch_assoc($res)){

				//logging this action in previous status table
				logApptChangedStatus($row['id'], "", "", "", "201", "", "", $_SESSION['authUser'], "Provider schedule deleted by removing related facility.", "", false);

				//updating schedule appointments details
				updateScheduleApptDetails($row['id'], "", "", "", "201", "", "", $_SESSION['authUser'], "Provider schedule deleted by removing related facility.", "", false);
			}
			//REMOVE ALL FUTURE AND PAST SCHEDULES
			imw_query("DELETE FROM provider_schedule_tmp_child WHERE facility IN ($id)");
			imw_query("DELETE FROM provider_schedule_tmp WHERE facility IN ($id)");
			//REMOVE FACILITY 
			$q1 = "DELETE FROM facility WHERE id IN ($id) and facility_type!='1'";
			$res 	= imw_query($q1);
			$erp_error=array();
			if($res){
				if( $id && isERPPortalEnabled() )
				{
					try {
						// send request to delete location from Eye Reach Patient API
						require_once(dirname(__FILE__) . "/../../../library/erp_portal/locations.php");
						$objLoc = new Locations();
						$objLoc->deleteLocation($id);
					} catch(Exception $e) {
						$erp_error[]='Unable to connect to ERP Portal';
					}
				}
				echo '1';
			}else{
				echo '0';
			}
		}
		break;
	case 'save_update':
		$id = $_POST['id'];
		unset($_POST['id']);
		unset($_POST['task']);
		$query_part = "";
		$apply_providers=0;
		$ptinfodiv=0;
		$mur_audit =0;
		$hqFac = 0;
		$fd_pc = 0;
		$enable_hp = 0;
		if(!$_POST['cbk_include_in_app_export']){
			$_POST['cbk_include_in_app_export']="0";
		}
		if(!$_POST['enable_hp']){
			$_POST['enable_hp']=$enable_hp;
		}
		$erx_entry = $_POST['erx_entry'];
		$Allow_erx_medicare = $_POST['Allow_erx_medicare'];
		
		unset($_POST['erx_entry']);
		unset($_POST['Allow_erx_medicare']);
		
		foreach($_POST as $k=>$v){
			if($k!='MAX_FILE_SIZE' && $k!='apply_providers' && $k!='Confidential_psw1' && $k!='mur_audit' && $k!='EmdeonUrl' 
			&& $k!='ptinfodiv' && $k!='fd_pc' && $k!='ele_txt_1'){
				if($k=='Confidential_psw' && $v!=''){
					$v = md5($v);
				}
				$query_part .= $k."='".addslashes($v)."', ";
				
				if($k=='facility_type' && $v=='1'){ $hqFac=1;}
			}

			if($k=='apply_providers' && $v=='1'){ $apply_providers='1'; }
			if($k=='mur_audit' && $v=='1'){ $mur_audit='1'; }
			if($k=='EmdeonUrl'){ $EmdeonUrl= $v; }
			if($k=='ptinfodiv' && $v=='1'){ $ptinfodiv= 1; }
			if($k=='fd_pc' && $v=='1'){ $fd_pc= 1; }
			if($k == 'ele_txt_1'){ $query_part .= 'email'."='".addslashes($v)."', ";}
		}
		$query_part = substr($query_part,0,-2);
		if($id==''){
			$q = "INSERT INTO facility SET ".$query_part;
		}else{
			$q = "UPDATE facility SET ".$query_part." WHERE id='".$id."'";
			
			//SET ENCOUNTER ID
			$vquery_c = "Select encounterId from facility where id='".$id."'";
			$vsql_c = imw_query($vquery_c);				
			$vsql_data = imw_fetch_array($vsql_c);

			if($hqFac=='1' && $vsql_data['encounterId']==''){
				$rs=imw_query("Select id,encounterId FROM facility WHERE encounterId>0");
				$res=imw_fetch_array($rs);
				$oldHQFac=$res['id'];
				$eId=$res['encounterId'];
				if($eId<=0 || $eId==''){
					$encRs = imw_query("Select MAX(encounterId) AS mxEncounterId from chart_master_table");
					if(imw_num_rows($encRs)>0){
						$encRes = imw_fetch_array($encRs);
						$eId = $encRes["mxEncounterId"]+1;
					}		
				}
				$rs1=imw_query("Update facility SET encounterId='".$eId."' WHERE id='".$id."'");
				if($rs1){
					if($oldHQFac>0){
						$rs2=imw_query("Update facility SET encounterId='' WHERE id='".$oldHQFac."'");
					}
				}
			}
		}
		$res = imw_query($q);
		if($id==''){
			$id  =imw_insert_id();
		}
		// SET SCHEDULE PROVIDERS
		if($id>0)
		{
			$req_qry = sprintf("SELECT id,sch_facilities FROM users WHERE delete_status=0");
			$req_qry_obj = imw_query($req_qry);
			while($users_data = imw_fetch_assoc($req_qry_obj))
			{
				$user_id_val = $users_data["id"];
				$sch_facilities_data = 	$users_data["sch_facilities"];
				$sch_facilities_data_arr=array();
				if($user_id_val != "")
				{
					if($apply_providers=='1'){
						if($sch_facilities_data!=""){
							$sch_facilities_data_arr = explode(";",$sch_facilities_data);
						}
						if(!in_array($id,$sch_facilities_data_arr))
						{
							$sch_facilities_data_arr[] = $id;	
						}
					}else{
						if($sch_facilities_data!=""){
							$sch_facilities_data_exp = explode(";",$sch_facilities_data);
						}
						foreach($sch_facilities_data_exp as $key => $val){
							if($id!=$val){
								$sch_facilities_data_arr[] = $val;
							}
						}
					}
					$target_sch_facilities_data = implode(";",$sch_facilities_data_arr);
					$update_users = sprintf("UPDATE users SET sch_facilities='%s' WHERE id = '%d' ",$target_sch_facilities_data,$user_id_val);
					imw_query($update_users);
				}
			}
		}
		$erp_error=array();
		if( $id > 0 && isERPPortalEnabled() )
		{
			try {
				addUpdateERPLocation($_POST,$id);	
			} catch(Exception $e) {
				$erp_error[]='Unable to connect to ERP Portal';
			}
		}
		if($hqFac){
			// CHECK COPAY POLICY
			//Updating CHC Link Type
			$typeNumber = 0;
			switch(strtolower($EmdeonUrl)){
				case 'https://cli-cert.changehealthcare.com':
					$typeNumber = 0;
				break;
				
				case 'https://clinician.changehealthcare.com':
					$typeNumber = 1;
				break;
			}
		
			$qry = "update patient_data set erx_entry = '$erx_entry'";
			if($erx_entry == 0){
				$qry .= " where erx_patient_id = ''";
			} 
			$que = imw_query($qry);
			$rs = imw_query("Select policies_id FROM copay_policies where policies_id='1' limit 0,1");
			$res=imw_fetch_array($rs);
			$policies_id = $res['policies_id'];
			if($policies_id == "0"){
				$query_copay_update = "Insert into copay_policies (ptinfodiv,EmdeonUrl,mur_audit,fd_pc,erx_entry,Allow_erx_medicare,emdeon_test_pro) VALUES('".$ptinfodiv."', '".$EmdeonUrl."', '".$mur_audit."', '".$fd_pc."','".$erx_entry."','".$Allow_erx_medicare."','".$typeNumber."')";
			}
			else{
				$query_copay_update ="Update copay_policies set ptinfodiv = '".$ptinfodiv."', EmdeonUrl='".$EmdeonUrl."', mur_audit='".$mur_audit."', fd_pc='".$fd_pc."', erx_entry='".$erx_entry."', Allow_erx_medicare='".$Allow_erx_medicare."', emdeon_test_pro ='".$typeNumber."' where policies_id='".$policies_id."'";
			}
			imw_query($query_copay_update);		
		}
		
		if($res){
			echo 'Record Saved Successfully.';
		}else{
			echo 'Record Saving failed.';//.imw_error()."\n".$q;
		}
		break;
	case 'show_list':
		$q = "Select id, facility.name, CONCAT_WS(',', postal_code, facility.zip_ext, street, city, state) as 'location', 
		groups_new.name as 'groupName', out_ofoffice, if(facility.phone_ext!='',concat(facility.phone,'<br>&nbsp;Ext. ',facility.phone_ext),facility.phone) as facility_phone, phone, phone_ext, facility_type,
		fac_prac_code, pam_code, server_name, erx_facility_id, street, city, state, postal_code, facility.zip_ext, fax, email AS ele_txt_1, default_group, facility_npi, 
		mess_of_day, facility_color, Confidential_psw, cbk_include_in_app_export, waiting_timer, chart_timer, chart_finalize, 
		regular_time_slot, maxRecentlyUsedPass, maxLoginAttempts, maxPassExpiresDays, billing_location, accepts_assignment, 
		billing_attention, encounterId,server_location, show_in_ptportal,refdig,fac_tin,fac_tax,enable_hp,facility.clia 
		FROM facility LEFT JOIN groups_new ON(groups_new.gro_id = facility.default_group and groups_new.del_status='0') 
		ORDER BY $so $soAD";
		$r = imw_query($q);
		$rs_set = array();
		$hq_exists=0;
		//==============IN CASE OF 'EmdeonUrl' BLANK IN copay_policies TABLE===============//
		$q_copay="SELECT policies_id,emdeon_test_pro FROM copay_policies WHERE EmdeonUrl=''";
		$r_copay=imw_query($q_copay);
		if(imw_num_rows($r_copay)>0){
			$res_copay=imw_fetch_assoc($r_copay);
			$copay_id=$res_copay['policies_id'];
			$copay_test_prod=$res_copay['emdeon_test_pro'];
			if($copay_id){
				$emdeon_url_set="https://cli-cert.changehealthcare.com";
				if($copay_test_prod=="1"){
					$emdeon_url_set="https://clinician.changehealthcare.com";	
				}
				$q_copay_i="UPDATE `copay_policies` set EmdeonUrl='".$emdeon_url_set."' where policies_id='".$copay_id."'";
				$r_copay_i=imw_query($q_copay_i);	
			}
		}
		//===========================================================================//
		$usr_fac_arr=array();
		if($r && imw_num_rows($r)>0){
			$sel_usr=imw_query("select sch_facilities,id from users where delete_status='0' and sch_facilities!=''");
			while($row_usr=imw_fetch_array($sel_usr)){
				$usr_fac_arr[$row_usr['id']]=$row_usr['sch_facilities'];
			}
			while($rs = imw_fetch_assoc($r)){
				$location='';
				$loc= explode(',', $rs['location']);
				
				if($loc[0]){
					$zip = $loc[0]; 
					if($rs[1]){
						$zip .= "-".$loc[1];
					}	
				}
            	if ($loc[2]) {	
                        $location.=$loc[2];
                }
                if(($loc[3]<>""))
                {							
                    $location.='<br>'.$zip.'  '.$loc[3].', ';
                }
                if(($loc[4]<>""))
                {							
                    $location.=$loc[4];
                }
				$rs['location'] = $location;					
				$rs['phone'] = ($rs['phone']=='') ? '-' : core_phone_format($rs['phone']);
				$rs['fax'] = ($rs['fax']!='') ? core_phone_format($rs['fax']) : '';
				$rs['groupName'] = stripslashes($rs['groupName']);
				$arr_replace_mess = array("<br />");
				$rs['mess_of_day'] = str_replace($arr_replace_mess,"",$rs['mess_of_day']);				

				if($rs['facility_type']=='1') { $hq_exists=1; }
				$rs['apply_providers']='0';
				if(count($usr_fac_arr)>0){
					foreach($usr_fac_arr as $key => $val){
						$val_exp=explode(';',$val);
						if(in_array($rs['id'],$val_exp)){
							$rs['apply_providers']='1';
						}
					}
				}
				$rs_set[] = $rs;
			}
		}
		
		$pos_facilities = pos_facilities();
		$copayPolicies  = getCopayPolicies();
		
		$optServers='';
		if(REMOTE_SCH==1){
			$globalServerKeys = array_keys($GLOBALS["REMOTE_SCH_SERVER"]);
			$optServers='';
			for($i=0; $i<sizeof($globalServerKeys); $i++){
				$servName = $globalServerKeys[$i];
				$selected='';
				if(strtolower($servName)!=strtolower($GLOBALS["LOCAL_SERVER"])){
					if(strtolower($servName)==strtolower($server_name)) {  $selected="SELECTED"; }
					$optServers.='<option value="'.$servName.'" '.$selected.'>'.ucwords($servName).'</option>';
				}
			}		
		}
		
		$emdeon_Facs = getEmdeonFacilities();
		
		echo json_encode(array('records'=>$rs_set, 'pos_facilities'=>$pos_facilities, 'hq_exists'=>$hq_exists, 
		'copayPolicies'=>$copayPolicies, 'REMOTE_SCH'=>REMOTE_SCH, 'optServers'=>$optServers, 'emdeon_facilities'=>$emdeon_Facs));
		break;
	case 'show_none_pos_fac':
		$q = "Select name from facility where fac_prac_code<='0' order by name";
		$r = imw_query($q);
		$pos_rs_set_arr = array();
		$pos_rs_set="";
		if($r && imw_num_rows($r)>0){
			$pos_rs_set_arr[]="<b>POS Facility is not associated with following Facilities.</b>"; 
			while($rs = imw_fetch_assoc($r)){
				$pos_rs_set_arr[]= '- '.$rs['name'];
			}
			$pos_rs_set=implode('<br>',$pos_rs_set_arr);
		}
		echo $pos_rs_set;		
		break;
	case 'refresh_emd_facs':
		$msg = array();
		$res1 = imw_query("select Allow_erx_medicare, EmdeonUrl from copay_policies LIMIT 0,1");
		$copay_policies_res 	= imw_fetch_assoc($res1);
		$Allow_erx_medicare 	= $copay_policies_res['Allow_erx_medicare'];
		$EmdeonUrl 				= $copay_policies_res['EmdeonUrl'];
		$username				= trim($_SESSION['authId']);
		if($username==''){die('Unable to select user.');}
		$res2 = imw_query("select eRx_user_name, erx_password from users where id = '$username' limit 1");
		$userRes = imw_fetch_assoc($res2);
		$eRx_user_name = $userRes['eRx_user_name'];
		$erx_password = $userRes['erx_password'];

		if(strtolower($Allow_erx_medicare) == 'yes' && $erx_password != '' && $eRx_user_name != ''){	
			$cookie_file = $GLOBALS['fileroot']."/data/".constant('PRACTICE_PATH')."/tmp/cookie_erx.txt";
			$cur = curl_init();
			$url = "$EmdeonUrl/servlet/DxLogin?userid=$eRx_user_name&PW=$erx_password&target=html/LoginSuccess.html&testLogin=true";
			curl_setopt($cur,CURLOPT_URL,$url);
			curl_setopt ($cur, CURLOPT_SSL_VERIFYHOST, false);
			curl_setopt ($cur, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($cur, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($cur, CURLOPT_COOKIEJAR, $cookie_file);
			curl_setopt($cur, CURLOPT_FOLLOWLOCATION, true);
			$data = curl_exec($cur);
			preg_match('/Login Error/',$data,$login_error);
			if(count($login_error) == 0){
		
				//-- list all practice Facility Ids  ---------------
				$url = $EmdeonUrl."/servlet/servlets.apiPersonServlet?actionCommand=listFacilities&apiuserid=".$eRx_user_name;
				//echo '<b>URL Executed:</b> '.$url.'<br>';
				$cur = curl_init();
				curl_setopt($cur,CURLOPT_URL,$url);
				curl_setopt($cur, CURLOPT_SSL_VERIFYHOST, false);
				curl_setopt($cur, CURLOPT_SSL_VERIFYPEER, false); 
				curl_setopt($cur,CURLOPT_COOKIEFILE,$cookie_file);
				curl_setopt($cur, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($cur, CURLOPT_FOLLOWLOCATION, true); 
				$facility_data = curl_exec($cur);
				preg_match('<--BEGIN FACILITY LIST>',$facility_data,$correct_fac_data);
				if($correct_fac_data){
					$facility_data = preg_replace('/<--BEGIN FACILITY LIST>/','',$facility_data);
					$facility_data = preg_replace('/<--END FACILITY LIST>/','',$facility_data);
					file_put_contents($GLOBALS['fileroot']."/data/".constant('PRACTICE_PATH')."/tmp/erx_facilities_".date('Ymd').".txt",$facility_data);
					$fac_data_arr = preg_split('/'.chr(10).'/', $facility_data, -1, PREG_SPLIT_NO_EMPTY);
					
					if(count($fac_data_arr)>=2){
						$erx_facility_id = $erx_facility_name = $erx_fac_key_id = '';
						for($i=0;$i<count($fac_data_arr);$i++){
							$curr_line = $fac_data_arr[$i];
							$curr_line_arr = explode('=',$curr_line);
							$field = $curr_line_arr[0];
							$value = $curr_line_arr[1];
							switch($field){
								case 'FACILITYOBJID'	:	$erx_facility_id	= $value; break;
								case 'FACILITYNAME'		:	$erx_facility_name	= $value; break;
							}
							//echo $erx_facility_id.' :: '.$erx_facility_name;die;
							if($erx_facility_id != '' && $erx_facility_name != ''){
								//echo $erx_facility_id.' :: '.$erx_facility_name;die;
								$find_res = imw_query("SELECT id FROM facilities_emdeon WHERE fac_obj_id='$erx_facility_id' AND del_status='0' LIMIT 0,1");
								if($find_res && imw_num_rows($find_res)==1){
									$find_rs = imw_fetch_assoc($find_res);
									$erx_fac_key_id = $find_rs['id'];
								}else if($find_res && imw_num_rows($find_res)==0){
									$insert_res = imw_query("INSERT INTO facilities_emdeon (fac_name,fac_obj_id,created_on) VALUES('$erx_facility_name','$erx_facility_id','".date('Y-m-d H:i:s')."')");
									$erx_fac_key_id = imw_insert_id();
								}
								if($erx_fac_key_id != ''){
									$msg[] = 'Record added/updated for "'.$erx_facility_name.'" in master table "facilities_emdeon".';
									$sql1 = "UPDATE facility SET erx_facility_id='".$erx_fac_key_id."' 
											WHERE LOWER(name)='".strtolower($erx_facility_name)."' 
											LIMIT 1";//echo $sql1.'<br>';
									$res1 = imw_query($sql1);
									
									$res3 = imw_query("SELECT name FROM facility WHERE erx_facility_id='".$erx_fac_key_id."'");
									if($res1 && $res3 && imw_num_rows($res3)>0){
									//	$msg[] = 'CHC facility ID updated for "'.$erx_facility_name.' in IMW Facility Table"';
									}else{
									//	$msg[] = '<font color="red">CHC facility ID NOT updated for "'.$erx_facility_name.' in IMW Facility Table"</font>';
									}
									$erx_facility_id = $erx_facility_name = $erx_fac_key_id = '';
								}						
							}
							
						}		
					}
				}else{
					die('Unable to LIST CHC Facilities. Contact administrator.');
				}

				//--- Log out from emdeon erx --------
				$cur = curl_init();
				$url = "$EmdeonUrl/servlet/lab.security.DxLogout?userid=$eRx_user_name&BaseUrl=$EmdeonUrl&LogoutPath=/html/AutoPrintFinished.html";
				curl_setopt($cur,CURLOPT_URL,$url);
				curl_setopt ($cur, CURLOPT_SSL_VERIFYHOST, false);
				curl_setopt ($cur, CURLOPT_SSL_VERIFYPEER, false); 
				curl_setopt($cur, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($cur, CURLOPT_COOKIEJAR, $cookie_file);
				curl_setopt($cur, CURLOPT_FOLLOWLOCATION, true); 
				$data = curl_exec($cur);
			}
			else{
				die("There is an error with your eRx login");
			}
		}else{
			die('Either eRx is not allowed or Invalid user credentials.');
		}
		echo getEmdeonFacilities();
		//if(count($msg)>0){echo '<li>'.implode('</li><li>',$msg).'</li>';}
		break;
	default: 
}

function addUpdateERPLocation($request,$fac_id)
{
	$fac_id = (int)$fac_id;
	// get id related to Eye Reach Patient API
	$qryERP = "Select erp_id, erp_contact_id, erp_email_id, erp_phone_id, erp_fax_id, erp_address_id, erp_country_id From facility Where id = ".(int)$fac_id." ";
	$sqlERP = imw_query($qryERP);
	$resERP = imw_fetch_assoc($sqlERP);
	
	// create array of data to send
	$contact = $email = $address = $Phone = $data = array();
	
	$email[] = array(
					'id' => $resERP['erp_email_id'],
					'alias' => '',
					'address' => $request['ele_txt_1'],
					'default' =>  true,
					'sortOrder' => "0" );
	
	$address[] = array(	"id" => $resERP['erp_address_id'],
						"alias" => "",
						"address1" => $request['street'],
						"address2" =>  "",
						"city" => $request['city'],
						"state" => $request['state'],
						"countryId" => $resERP['erp_country_id'],
						"countryCode" => "USA",
						"countryName" => "United States",
						"zip" => $request['postal_code'] . ($request['zip_ext'] ? '-'.$request['zip_ext'] : '') ,
						"default" =>  true );

	$phone[] = array( "id" => $resERP['erp_phone_id'],
					"alias" => "Phone",
					"number" => $request['phone'].($request['phone_ext'] ? $request['phone_ext'] : ''),
					"default" => true,
					"useForSms" => false,
					"sortOrder" => 0);
	
	$phone[] = array( "id" => $resERP['erp_fax_id'],
					"alias" => "Fax",
					"number" => $request['fax'],
					"default" => false,
					"useForSms" => false,
					"sortOrder" => 1);

	list($fname,$lname) = explode(" ", $request['out_ofoffice']);
	$contact = array(	"id" => $resERP['erp_contact_id'],
						"firstName" => $fname,
						"middleName" => "",
						"lastName" => $lname,
						"suffix" => "",
						"prefix" => "",
						"fullName" => $fname.' '. $lname,
						"companyName" => "",
						"jobTitle" => "",
						"emailAddresses" => $email,
						"phoneNumbers" => $phone,
						"postalAddresses" => $address,
						"notes" => "" );

	$data['id']	= $resERP['erp_id'];
	$data['name'] = $request['name'];
	$data['alias'] = '';
	$data['active'] = true;
	$data['notes'] = '';
	$data['contact'] = $contact;
	$data['externalId'] = $fac_id;	
	
	// send request to Save/Update facility data at Eye Reach Patient API
	require_once(dirname(__FILE__) . "/../../../library/erp_portal/locations.php");
	$objLoc = new Locations();
	$result = $objLoc->addUpdateLocation($data);
		
	//Update erp ids into relative fields
	if( $result )
	{
		$erp_fac_id = $result['id'];
		$erp_contact_id = $result['contact']['id'];
		$erp_email_id = $result['contact']['emailAddresses'][0]['id'];
		
		$erp_phone_id = "";
		if( $result['contact']['phoneNumbers'][0]['alias'] == 'Phone' )
			$erp_phone_id = $result['contact']['phoneNumbers'][0]['id'];
		else if( $result['contact']['phoneNumbers'][1]['alias'] == 'Phone')
			$erp_phone_id = $result['contact']['phoneNumbers'][1]['id'];
		
		$erp_fax_id = "";
		if( $result['contact']['phoneNumbers'][0]['alias'] == 'Fax' )
			$erp_fax_id = $result['contact']['phoneNumbers'][0]['id'];
		else if( $result['contact']['phoneNumbers'][1]['alias'] == 'Fax')
			$erp_fax_id = $result['contact']['phoneNumbers'][1]['id'];

		$erp_address_id = $result['contact']['postalAddresses'][0]['id'];

		$qryERP_U = "Update facility Set erp_id = '$erp_fac_id', 
										 erp_contact_id = '".$erp_contact_id."', 
										 erp_email_id = '".$erp_email_id."', 
										 erp_phone_id = '".$erp_phone_id."', 
										 erp_fax_id = '".$erp_fax_id."', 
										 erp_address_id = '".$erp_address_id."' 
								Where id = ".$fac_id." ";

		$sqlERP_U = imw_query($qryERP_U) ;
	}
}

function pos_facilities(){
	$vquery_t = "Select a.pos_facility_id,a.facilityPracCode,b.pos_prac_code from pos_facilityies_tbl a,pos_tbl b
				where a.pos_id = b.pos_id order by facilityPracCode asc";
	$vsql_t = imw_query($vquery_t);
	if(imw_num_rows($vsql_t)>0){
		while($rs_t = imw_fetch_array($vsql_t)){
			$rs_t['facilityPracCode'] = (strlen($rs_t['facilityPracCode']) > 18) ? substr($rs_t['facilityPracCode'], 0 , 15)."..." : $rs_t['facilityPracCode'];
			$rs_t['facilityPracCode'].= '-'.$rs_t['pos_prac_code'];
			$result[]=$rs_t;
		}
		return $result;
	}
	return false;
}

function getCopayPolicies(){
	$query_copay = "Select ptinfodiv,EmdeonUrl,mur_audit,fd_pc,erx_entry, Allow_erx_medicare FROM copay_policies where policies_id='1' limit 0,1";
	$result_copay = imw_query($query_copay);
	if($result_copay && imw_num_rows($result_copay) > 0){
		$result = imw_fetch_array($result_copay);
		return $result;
	}
	return false;	
}

function getEmdeonFacilities(){
	$query = "Select id,fac_name FROM facilities_emdeon WHERE del_status='0' ORDER BY fac_name";
	$result = imw_query($query);
	$dd = false;
	if($result && imw_num_rows($result) > 0){
		$dd = '<option value="0">--SELECT--</option>';
		while($rs = imw_fetch_array($result)){
			$dd .= '<option value="'.$rs['id'].'">'.$rs['fac_name'].'</option>';
		}
		return $dd;
	}
	return false;	
}
?>
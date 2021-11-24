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
*/
require_once("../../../config/globals.php");
require_once('../../../library/classes/cls_common_function.php'); 
$OBJCommonFunction = new CLSCommonFunction;

$hl7 = false;
$logHl7 = false;
if( defined('HL7_MFN_REF_PHY') && constant('HL7_MFN_REF_PHY') === true && isset($GLOBALS['HL7_MFN_SEGMENTS']) && is_array($GLOBALS['HL7_MFN_SEGMENTS']) && count($GLOBALS['HL7_MFN_SEGMENTS']) > 0 )
{
	require(dirname(__FILE__).'/../../../hl7sys/hl7GP/hl7FeedData.php');
	
	$hl7 = new hl7FeedData();
	$hl7->msgtypes['MFN']['segments'] = $GLOBALS['HL7_MFN_SEGMENTS'];
	$hl7->msgtypes['MFN']['trigger_event'] = "M02";
	$hl7->msgtype = "Update_Referring_Phisician";
}

$task	= isset($_REQUEST['ajax_task']) ? trim($_REQUEST['ajax_task']) : '';
$so		= isset($_REQUEST['so']) ? trim($_REQUEST['so']) : 'to_do_id';
$soAD	= isset($_REQUEST['soAD']) ? trim($_REQUEST['soAD']) : 'ASC';
$table	= "refferphysician";
$pkId	= "physician_Reffer_id";
$chkFieldAlreadyExist="task";
$interDateFormat= phpDateFormat();

//-------BEGIN CALCULATE RECORDS LIMIT----------
	$page = (isset($_REQUEST['page']) && $_REQUEST['page']!="")?$_REQUEST['page']:1;
	$record_limit = (isset($_REQUEST['record_limit']) && $_REQUEST['record_limit']!="")?$_REQUEST['record_limit']:50;
	$offset = ($page-1) * $record_limit;
	$count = $record_limit;
	
//-------END CALCULATE RECORDS LIMIT----------

switch($task){
	//Delete Ref. Phy.
	case 'delete':
		$id = $_POST['pkId'];
		$q 		= "DELETE FROM ".$table." WHERE ".$pkId." IN (".$id.")";
		$q 		= "UPDATE ".$table." SET delete_status = '1' WHERE ".$pkId." IN (".$id.")";
		$res 	= imw_query($q);
		if($res){
			echo '1';
		}else{
			echo '0';//.imw_error()."\n".$q;
		}
		$OBJCommonFunction -> create_ref_phy_xml();
		$logHl7 = true;
	break;
	//Saving procedure po options for Ref. Phy. 
	case 'save_update_po':
		$ref_id=$_POST['physician_Reffer_id_for_po'];
		if($_POST['op_option'] && $ref_id)
		{
			$proc_id_str=implode(',',$_POST['op_option']);
			//get already saved records
			$q=imw_query("select proc_id, id from slot_procedures_linked_op where ref_id=$ref_id and proc_id IN($proc_id_str)")or die(imw_error());
			while($d=imw_fetch_object($q))
			{
				$saved_data[$d->proc_id]=$d->id;
			}
			foreach($_POST['op_option'] as $proc_id)
			{
				$op_str=implode(',',$_POST["op_option_$proc_id"]);
				if($saved_data[$proc_id])
				{
					//update
					imw_query("update slot_procedures_linked_op set linked_op='$op_str' where id=".$saved_data[$proc_id])or die(imw_error());
				}else
				{
					//insert new record
					imw_query("insert into slot_procedures_linked_op set ref_id=$ref_id, proc_id=$proc_id, linked_op='$op_str'")or die(imw_error());
				}
			}
			echo 'Mapping Saved Successfully.';
		}
	break;
	
	//Saving Ref. Phy. Info	
	case 'save_update':
		$id = $_POST[$pkId];
		unset($_POST[$pkId]);
		unset($_POST['ajax_task']);
		$_POST['created_date'] = date('y-m-d');
		$_POST['source'] = '1';
		$query_part = "";
		//pre($_POST);
		foreach($_POST as $k=>$v){
			switch($k){
				case "ref_phy_group":
					$ref_phy_grp = $_POST['ref_phy_group'];
				break;
				case "confirm_password":
				case "id_address":
				case "hid_password":
				break;
				case "password":
				if($v!=""){
					$query_part .= $k."='".md5($v)."', ";
				}
				break;
				case "start_date":
				case "end_date":
				$query_part .= $k."='".getDateFormatDB($v)."', ";
				break;
				case "access_pri":
				$access_pri = implode(",",$v);
				$query_part .= $k."='".$access_pri."', ";
				break;
				case "referedPhysician":
				$query_part .= $k."='".implode(",",$v)."', ";
				break;
				case "default_facility":
				$query_part .= $k."='".implode(",",$v)."', ";
				break;
				case "PractiseName":
				$query_part .= "PractiseName='".imw_real_escape_string($v[0])."', ";
				break;
				case "Address1":
				$query_part .= "Address1='".imw_real_escape_string($v[0])."', ";
				break;
				case "Address2":
				$query_part .= "Address2='".imw_real_escape_string($v[0])."', ";
				break;
				case "ZipCode":
				$query_part .= "ZipCode='".imw_real_escape_string($v[0])."', ";
				break;
				case "zip_ext":
				$query_part .= "zip_ext ='".imw_real_escape_string($v[0])."', ";
				break;
				case "City":
				$query_part .= "City ='".imw_real_escape_string($v[0])."', ";
				break;
				case "State":
				$query_part .= "State ='".imw_real_escape_string($v[0])."', ";
				break;
				case "country":
				$query_part .= "country ='".imw_real_escape_string($v[0])."', ";
				break;
				case "physician_fax":
				$query_part .= "physician_fax ='".imw_real_escape_string($v[0])."', ";
				break;
				case "physician_phone":
				$query_part .= "physician_phone='".core_phone_unformat($v[0])."', ";
				break;
				case "physician_email":
				$query_part .= "physician_email='".imw_real_escape_string($v[0])."', ";
				break;
				case "specialty":
				$query_part .= "specialty='".imw_real_escape_string($v[0])."', ";
				break;
				case "address_del_id":
				break;
				default:
				$query_part .= $k."='".imw_real_escape_string($v)."', ";
			}
		}
		$query_part = substr($query_part,0,-2);
		$qry_con = "";
			if($id){$qry_con=" AND ".$pkId."!='".$id."' ";}
		
			if($id==''){
				$q = "INSERT INTO ".$table." SET ".$query_part;
				$res = imw_query($q) or die(imw_error());
				$insert_id = imw_insert_id();
				update_ref_phy_group($insert_id,$ref_phy_grp);
				$_POST['id_address'][0] = $insert_id;
				
				/*Hl7 Message Type*/
                if($hl7 && $logHl7)
                    $hl7->msgtype = "Add_Referring_Phisician";
				$id = $insert_id;
			}else{
				$q = "UPDATE ".$table." SET ".$query_part." WHERE ".$pkId." = '".$id."'";
				$res = imw_query($q) or die(imw_error());
				update_ref_phy_group($id,$ref_phy_grp);
				$insert_id = $id;
				$_POST['id_address'][0] = $insert_id;
			}
			//---------BEGIN ADDRESSES---------------
			for($i = 0; $i<= count($_POST['Address1']); $i++){
				if($_POST['ZipCode'][$i]!=""){
					if(($_POST['id_address'][$i] != $insert_id) && ($_POST['id_address'][$i] == "" || $_POST['id_address'][$i] <= 0)){	
						$qry = "INSERT INTO refferphysician 
							  SET 
							  Title = '".imw_real_escape_string($_POST['Title'])."',
							  FirstName = '".imw_real_escape_string($_POST['FirstName'])."',
							  MiddleName = '".imw_real_escape_string($_POST['MiddleName'])."',
							  LastName = '".imw_real_escape_string($_POST['LastName'])."',
							  PractiseName = '".imw_real_escape_string($_POST['PractiseName'][$i])."',
							  NPI = '".imw_real_escape_string($_POST['NPI'])."',
							  Address1 = '".imw_real_escape_string($_POST['Address1'][$i])."',
							  Address2 = '".imw_real_escape_string($_POST['Address2'][$i])."',
							  ZipCode = '".imw_real_escape_string($_POST['ZipCode'][$i])."',
							  zip_ext = '".imw_real_escape_string($_POST['zip_ext'][$i])."',
							  City = '".imw_real_escape_string($_POST['City'][$i])."',
							  State = '".imw_real_escape_string($_POST['State'][$i])."',
							  country = '".imw_real_escape_string($_POST['country'][$i])."',
							  physician_phone = '".imw_real_escape_string($_POST['physician_phone'][$i])."',
							  physician_fax = '".imw_real_escape_string($_POST['physician_fax'][$i])."',
							  physician_email = '".imw_real_escape_string($_POST['physician_email'][$i])."',
							  specialty = '".imw_real_escape_string($_POST['specialty'][$i])."',
							  primary_id  = '".$insert_id."',
							  comments  = '".imw_real_escape_string($_POST['comments'])."'
							";	
						imw_query($qry) or die(imw_error());
						$address_id = imw_insert_id();	
					}else if($_POST['id_address'][$i] > 0){
						$changed=false;
						//get old data to compare
						$getQ=imw_query("select Address1,Address2,ZipCode,zip_ext,City,State,country,physician_phone,physician_fax from refferphysician WHERE physician_Reffer_id  = '".$_POST['id_address'][$i]."'");
						$getD=imw_fetch_object($getQ);
						if($getD->Address1!=$_POST['Address1'][$i])$changed=true;
						elseif($getD->Address2!=$_POST['Address2'][$i])$changed=true;
						elseif($getD->ZipCode!=$_POST['ZipCode'][$i])$changed=true;
						elseif($getD->zip_ext!=$_POST['zip_ext'][$i])$changed=true;
						elseif($getD->City!=$_POST['City'][$i])$changed=true;
						elseif($getD->State!=$_POST['State'][$i])$changed=true;
						elseif($getD->country!=$_POST['country'][$i])$changed=true;
						elseif($getD->physician_phone!=$_POST['physician_phone'][$i])$changed=true;
						elseif($getD->physician_fax!=$_POST['physician_fax'][$i])$changed=true;
						
						imw_query("update schedule_appointments sa INNER JOIN patient_data pd ON sa.sa_patient_id=pd.id set sa.ref_phy_changed=1, sa.ref_phy_comments='".imw_real_escape_string($_POST['comments'])."' where pd.primary_care_id = ".$_POST['id_address'][$i]." and CONVERT(CONCAT(sa.sa_app_start_date,' ',sa.sa_app_starttime),DATETIME) > '".date('Y-m-d H:i:s')."' AND sa.sa_app_start_date >= DATE('".date('Y-m-d H:i:s')."')") ;
					
						$qry = "UPDATE refferphysician 
							  SET 
							  Title = '".imw_real_escape_string($_POST['Title'])."',
							  FirstName = '".imw_real_escape_string($_POST['FirstName'])."',
							  MiddleName = '".imw_real_escape_string($_POST['MiddleName'])."',
							  LastName = '".imw_real_escape_string($_POST['LastName'])."',
							  PractiseName = '".imw_real_escape_string($_POST['PractiseName'][$i])."',
							  NPI = '".imw_real_escape_string($_POST['NPI'])."',
							  Address1 = '".imw_real_escape_string($_POST['Address1'][$i])."',
							  Address2 = '".imw_real_escape_string($_POST['Address2'][$i])."',
							  ZipCode = '".imw_real_escape_string($_POST['ZipCode'][$i])."',
							  zip_ext = '".imw_real_escape_string($_POST['zip_ext'][$i])."',
							  City = '".imw_real_escape_string($_POST['City'][$i])."',
							  State = '".imw_real_escape_string($_POST['State'][$i])."',
							  country = '".imw_real_escape_string($_POST['country'][$i])."',
							  physician_phone = '".imw_real_escape_string($_POST['physician_phone'][$i])."',
							  physician_fax = '".imw_real_escape_string($_POST['physician_fax'][$i])."',
							  physician_email = '".imw_real_escape_string($_POST['physician_email'][$i])."',
							  specialty = '".imw_real_escape_string($_POST['specialty'][$i])."',
							  comments  = '".imw_real_escape_string($_POST['comments'])."'
							  WHERE physician_Reffer_id  = '".$_POST['id_address'][$i]."'
							";	
						imw_query($qry) or die(imw_error());
						$address_id = $_POST['id_address'][$i];
					}
					
					if($i == 0)$default_address_id = $address_id;
					if($_POST['default_address'][$i] == 1){
						$default_address_id = $address_id;
						$qry_ins = "UPDATE refferphysician
									  SET PractiseName = '".imw_real_escape_string($_POST['PractiseName'][$i])."',
									  Address1 = '".imw_real_escape_string($_POST['Address1'][$i])."',
									  Address2 = '".imw_real_escape_string($_POST['Address2'][$i])."',
									  ZipCode = '".imw_real_escape_string($_POST['ZipCode'][$i])."',
									  zip_ext = '".imw_real_escape_string($_POST['zip_ext'][$i])."',
									  City = '".imw_real_escape_string($_POST['City'][$i])."',
									  State = '".imw_real_escape_string($_POST['State'][$i])."',
									  country = '".imw_real_escape_string($_POST['country'][$i])."',
									  physician_phone = '".imw_real_escape_string($_POST['physician_phone'][$i])."',
							  		  physician_fax = '".imw_real_escape_string($_POST['physician_fax'][$i])."',
									  physician_email = '".imw_real_escape_string($_POST['physician_email'][$i])."',
									  specialty = '".imw_real_escape_string($_POST['specialty'][$i])."'
									  WHERE physician_Reffer_id  = '".$insert_id."'";
						imw_query($qry_ins) or die(imw_error());
						
						$qry_ins2 = "UPDATE refferphysician
									  SET PractiseName = '".imw_real_escape_string($_POST['PractiseName'][$i])."',
									  Address1 = '".imw_real_escape_string($_POST['Address1'][0])."',
									  Address2 = '".imw_real_escape_string($_POST['Address2'][0])."',
									  ZipCode = '".imw_real_escape_string($_POST['ZipCode'][0])."',
									  zip_ext = '".imw_real_escape_string($_POST['zip_ext'][0])."',
									  City = '".imw_real_escape_string($_POST['City'][0])."',
									  State = '".imw_real_escape_string($_POST['State'][0])."',
									  country = '".imw_real_escape_string($_POST['country'][0])."',
									  physician_phone = '".imw_real_escape_string($_POST['physician_phone'][0])."',
							  		  physician_fax = '".imw_real_escape_string($_POST['physician_fax'][0])."',
									  physician_email = '".imw_real_escape_string($_POST['physician_email'][$i])."',
									  specialty = '".imw_real_escape_string($_POST['specialty'][$i])."'
									  WHERE physician_Reffer_id  = '".$default_address_id."'";
						imw_query($qry_ins2) or die(imw_error());
					}
				}
			}
			imw_query("UPDATE refferphysician SET default_address = '".$default_address_id."' WHERE physician_Reffer_id  = '".$insert_id."'") or die(imw_error());
			//---------END ADDRESSES---------------
			
			//----BEGIN DELETE CROSSED ADDRESSES--------------
			$address_del_id = trim($_REQUEST['address_del_id'],",");
			if($address_del_id != ""){
				imw_query("UPDATE refferphysician SET delete_status = 1 WHERE physician_Reffer_id IN (".$address_del_id.")");
			}
			//----END DELETE CROSSED ADDRESSES--------------
			if($res){
				echo 'Record Saved Successfully.';
				$fileName = preg_replace('/[^A-Za-z]/','',imw_real_escape_string($_POST['LastName']));
				$OBJCommonFunction -> create_ref_phy_main_xml(strtolower(substr(trim($fileName),0,2)));
				$OBJCommonFunction -> create_ref_phy_xml();
			}else{
				echo 'Record Saving failed.'.imw_error()."\n".$q;
			}
		$logHl7 = true;
	break;
	
	//Set lock or unlock status
	case "lock_unlock":
		$id = $_REQUEST['physician_Reffer_id'];
		$sql = "UPDATE refferphysician SET locked = ".$_REQUEST['locked']." WHERE physician_Reffer_id = ".$_REQUEST['physician_Reffer_id'];
		imw_query($sql);
		$OBJCommonFunction -> create_ref_phy_xml();
		$logHl7 = true;
	break;
	
	//Set delete status
	case "set_status":
		$id = $_REQUEST['physician_Reffer_id'];
		$sql = "UPDATE refferphysician SET delete_status = ".$_REQUEST['status']." WHERE physician_Reffer_id = ".$_REQUEST['physician_Reffer_id'];
		imw_query($sql);
		$OBJCommonFunction -> create_ref_phy_xml();
		$logHl7 = true;
	break;
	
	//Get Ref. Physicians info
	case 'show_list':
		$q = "SELECT 
				physician_Reffer_id,
				CONCAT(IF(Title!='',CONCAT(Title,' '),''),LastName,', ', FirstName ,' ',MiddleName) AS name,
				CONCAT(Address1 ,' ',Address2,IF(City!='',CONCAT(', ',City),''),IF(State!='',CONCAT(', ',State),''),IF(country!='',CONCAT(', ',country),''),IF(ZipCode!='',CONCAT(' ',ZipCode),''),IF(zip_ext!='',CONCAT('-',zip_ext),'')) AS address,
				NPI, MDCR, MDCD, Texonomy, start_date, end_date, password, locked, delete_status, 
				refferphysician.*
				FROM refferphysician 
				WHERE primary_id = 0 ";
		$delete_status = (isset($_REQUEST['s']) && $_REQUEST['s'] !="")?$_REQUEST['s']:"0";
		if($delete_status == "0")
		$q .= " AND (delete_status = 0 || delete_status IS NULL)";
		else if($delete_status != 'all')
		$q .= " AND delete_status = ".$delete_status;
		if(isset($_REQUEST['alpha']) && $_REQUEST['alpha'] !="" && $_REQUEST['searchStr']=="" && $_REQUEST['alpha'] != 'az'){
			if($delete_status == "0" || $delete_status != 'all'){
			$q .= " AND ";
			$q .= "  LastName LIKE '".$_REQUEST['alpha']."%'";
			}
		}
		if(isset($_REQUEST['searchStr']) && $_REQUEST['searchStr']!=""){
			$q .= " AND (LastName LIKE '".$_REQUEST['searchStr']."%' OR FirstName LIKE '".$_REQUEST['searchStr']."%')";
		}
		$q .= " ORDER BY $so $soAD ";
		$total_pages = ceil(imw_num_rows(imw_query($q))/$count);
        if(isset($_REQUEST['alpha']) && $_REQUEST['alpha'] != 'az') {
            $q .= "LIMIT $offset,$count";
        }
		//echo $q; die();
		$r = imw_query($q);
		$rs_set = $fu_arr=array();
		if($r && imw_num_rows($r)>0){
			while($rs = imw_fetch_assoc($r)){
				$count =0;
				$rs['ref_phy_group'] = getGroupofRefPhy($rs['physician_Reffer_id']);
				$rs['PractiseName['.$count.']'] = $rs['PractiseName'];
				$rs['Address1['.$count.']'] = $rs['Address1'];
				$rs['Address2['.$count.']'] = $rs['Address2'];
				$rs['ZipCode['.$count.']'] = $rs['ZipCode'];
				$rs['zip_ext['.$count.']'] = $rs['zip_ext'];
				$rs['City['.$count.']'] = $rs['City'];
				$rs['State['.$count.']'] = $rs['State'];
				$rs['country['.$count.']'] = $rs['country'];
				$rs['physician_phone['.$count.']'] = $rs['physician_phone'];
				$rs['physician_fax['.$count.']'] = $rs['physician_fax'];
				$rs['physician_email['.$count.']'] = $rs['physician_email'];
				$rs['id_address['.$count.']'] = $rs['physician_Reffer_id'];
				$rs['specialty['.$count.']'] = $rs['specialty'];
				if($rs['locked']==0 && trim($rs['userName'])!=""){
					$rs['locked']='<button type="button" onClick="lock_unlock(1,'.$rs['physician_Reffer_id'].');" class="btn btn-success btn-sm"><strong>Un&nbsp;Lock</strong></button>';
				 }else if($rs['locked']==1 && trim($rs['userName'])!=""){
					$rs['locked']='<button type="button" onClick="lock_unlock(0,'.$rs['physician_Reffer_id'].');" class="btn btn-danger btn-sm" ><strong>Locked</strong></button>';
				 }else{
					 $rs['locked'] = 'N/A';
				 }
				 if(trim($rs['userName'])!=""){
					$rs['password_td'] = '<button type="button" onclick="show_chg_password_div('.$rs['physician_Reffer_id'].')" class="btn btn-success btn-sm" id="chg_password_'.$rs['physician_Reffer_id'].'"><strong>Reset Password </strong></button>';
				}
				else{
					$rs['password_td'] ='N/A';
				}
				if($rs['delete_status']== 0){
					$rs['delete_status_td'] = '<span class="glyphicon glyphicon-stop text-success" onclick="set_status(1,'.$rs['physician_Reffer_id'].')" title="Click to make In-Active"></span>';
				}
				elseif($rs['delete_status'] == 1){
					$rs['delete_status_td'] = '<span onclick="set_status(0,'.$rs['physician_Reffer_id'].')" class="glyphicon glyphicon-stop text-danger" title="Click to make Active"></span>';
				}
				else{
					$rs['delete_status_td'] = '';
				}
				$rs['start_date'] = ($rs['start_date'] != '0000-00-00')?date($interDateFormat, strtotime($rs['start_date'])):"";
				$rs['end_date'] = ($rs['end_date'] != '0000-00-00')?date($interDateFormat, strtotime($rs['end_date'])):"";
				$rs['ref_phy_group'] = getGroupofRefPhy($rs['physician_Reffer_id']);
				$rs['hid_password'] = $rs['password'];
				$rs['confirm_password'] = '';
				$rs['password'] = '';
				$rs['access_pri[]'] = explode(",",$rs['access_pri']);
				$arr_referredPhysician = get_referred_physician_name($rs['referedPhysician']);
				$rs['referedPhysician[]'] = $arr_referredPhysician;
				$arr_default_facility = get_default_facility_name($rs['default_facility']);
				$rs['default_facility[]'] = $arr_default_facility;
				
				$res_add = imw_query("SELECT * FROM refferphysician WHERE primary_id = '".$rs['physician_Reffer_id']."' AND delete_status = 0");
				$arrAddress = array();
				while($row_add = imw_fetch_assoc($res_add)){
					$count++;
						$arr = array();
						$arr['PractiseName['.$count.']'] = $row_add['PractiseName'];
						$arr['Address1['.$count.']'] = $row_add['Address1'];
						$arr['Address2['.$count.']'] = $row_add['Address2'];
						$arr['ZipCode['.$count.']'] = $row_add['ZipCode'];
						$arr['zip_ext['.$count.']'] = $row_add['zip_ext'];
						$arr['City['.$count.']'] = $row_add['City'];
						$arr['State['.$count.']'] = $row_add['State'];
						$arr['country['.$count.']'] = $row_add['country'];
						$arr['physician_phone['.$count.']'] = $row_add['physician_phone'];
						$arr['physician_fax['.$count.']'] = $row_add['physician_fax'];
						$arr['physician_email['.$count.']'] = $row_add['physician_email'];
						$arr['id_address['.$count.']'] = $row_add['physician_Reffer_id'];
						$arr['specialty['.$count.']'] = $row_add['specialty'];
						$arrAddress[] = $arr;
				}
				$rs['Addresses'] = $arrAddress;
				$rs_set[] = $rs;
			}
		}
		$ref_grp_arr = ref_grp_arr();
		$notice_days_arr = notice_days_arr();
		$default_grp_arr = default_grp_arr();
		$all_referedPhysician = all_referedPhysician();
		$all_facility = all_facility();
		$arr_texonomy = all_texonomy();
		$arr_practiceInfo = all_practiceInfo();
		echo json_encode(array('records'=>$rs_set,'ref_grp_arr'=>$ref_grp_arr,'notice_days_arr'=>$notice_days_arr,'default_grp_arr'=>$default_grp_arr,'all_referedPhysician'=>$all_referedPhysician,'all_referedPhysician'=>$all_referedPhysician,'all_facility'=>$all_facility,'arr_texonomy'=>$arr_texonomy,"arr_practiceName"=>$arr_practiceInfo['name'],"arr_practiceAddress"=>$arr_practiceInfo['val'],"total_pages"=>$total_pages,"search_str"=>$_REQUEST['searchStr']),JSON_INVALID_UTF8_IGNORE);
	break;
	
	//Creating XML for all ref. phy.
	case "createAllXML":
		$OBJCommonFunction -> create_ref_phy_main_xml();
		echo "Referring physician cache created successfully";
	break;
	
	//Changing passwords
	case "chg_password":
		$q = "SELECT COUNT(".$pkId.") AS pass_count FROM ".$table."  
				WHERE password = md5('".$_REQUEST['chg_password']."')";
		$r = imw_query($q);
		$row = imw_fetch_assoc($r);
		if($row['pass_count'] == 0){
			$q = "UPDATE ".$table." SET password = '".md5($_REQUEST['chg_password'])."' WHERE ".$pkId." = '".$_REQUEST['pkId']."'";
			$res = imw_query($q);
			if($res)
			echo "Password updated successfully";
		}
		else{
			echo 'Password matched with used passwords<br> - Choose another password';
		}
			
	break;
	
	//Exporting ref. physician data
	case 'export_csv':
		$filename = data_path() . '/Refering_physician.csv';
		if(file_exists($filename)){@unlink($filename);}
		$fp1=fopen($filename,'w');

		$query="select Title,credential,FirstName,MiddleName,LastName,Address1,Address2,NPI,MDCR,MDCD,Texonomy,Initialdate,Lastdate,ZipCode,City,State,physician_phone,physician_fax,physician_email from refferphysician where delete_status = 0 AND FirstName != '' order by trim(LastName)";
		$result=imw_query($query);

		$data=array();

		$data_head[]=array('Title','Credential','First_name','Middle_name','Last_name','Address1','Address2','NPI','MDCR','MDCD','Texonomy','I.R.Date','L.R.Date','zip code','city','state','physician_phone','physician_fax','physician_email');

		while($row=imw_fetch_assoc($result)){
			$data[]=$row;
		}

		foreach ($data_head as $fields1){
			fputcsv($fp1, $fields1);
		}
		foreach ($data as $fields){
			fputcsv($fp1, $fields);
		}
		fclose($fp1);
		$csv_text = file_get_contents($filename);
		$csv_text = htmlentities($csv_text);
		$csv_text=html_entity_decode($csv_text);
		$csv_text=str_replace('&nbsp;', ' ', $csv_text);
		downloadFiles($filename,$csv_text);
	break;

	//Get Multi direct email
	case 'get_multi_direct':
		$returnArr = array();
		$refId = (isset($_REQUEST['refId']) && empty($_REQUEST['refId']) == false) ? trim($_REQUEST['refId']) : '';
		$returnType = (isset($_REQUEST['returnType']) && empty($_REQUEST['returnType']) == false) ? trim($_REQUEST['returnType']) : '';
		
		
		if(empty($refId) == false){
			$prevMail = '';
			$mailFind = false;

			$findArr = strpos($refId, ',');

			$searchMain = "physician_Reffer_id = ".$refId;
			$searchNew = "ref_id = ".$refId;

			$arrReturn = false;	
			if($findArr === false){
				
			}else{
				$searchMain = "physician_Reffer_id IN (".$refId.")";
				$searchNew = "ref_id IN (".$refId.")";
				$arrReturn = true;
			}

			$getSql = " SELECT * FROM ref_multi_direct_mail WHERE ".$searchNew." AND del_status = 0 ORDER BY id ASC ";
			$resSql = imw_query($getSql) or die(imw_error());

			if($resSql && imw_num_rows($resSql) > 0){
				$counter = 0;
				while($rowFetch = imw_fetch_assoc($resSql)){
					array_push($returnArr, $rowFetch);
					$counter++;
				}
			}
		}

		if($arrReturn == true || (empty($returnType) == false && $returnType == 'array')){
			$mainArr = array();
			foreach($returnArr as $obj){
				$tmpArr = array();
				$tmpArr = array('email' => $obj['email'], 'id' => $obj['id']);
				
				if(!is_array($mainArr[$obj['ref_id']])) $mainArr[$obj['ref_id']] = array();
				array_push($mainArr[$obj['ref_id']], $tmpArr);
			}

			foreach($mainArr as $refId => &$obj){
				$sql = imw_query(" SELECT direct_email FROM refferphysician WHERE physician_Reffer_id = ".$refId." ");
				if($sql && imw_num_rows($sql) > 0){
					$rowFetch = imw_fetch_assoc($sql);
					$prevMail = (empty($rowFetch['direct_email']) == false) ? $rowFetch['direct_email'] : '';

					if(empty($prevMail) == false){
						$obj['default'] = $prevMail;
					}
				}
			}

			if(count($mainArr) > 0) $returnArr = $mainArr;
		}
		echo json_encode($returnArr);
	break;

	//Delete Direct mail row
	case 'del_multi_direct_mail':
		$returnVal = false;
		$delRow = (isset($_REQUEST['rowId']) && empty($_REQUEST['rowId']) == false) ? trim($_REQUEST['rowId']) : '';

		if(empty($delRow) == false){
			$getSql = " UPDATE ref_multi_direct_mail SET del_status = 1, del_by = ".$_SESSION['authId']." WHERE id = ".$delRow." ";
			$resSql = imw_query($getSql) or die(imw_error());

			if($resSql) $returnVal = true;
		}

		echo json_encode($returnVal);
	break;

	//Save Direct mails
	case 'save_multi_direct':
		$returnVal = false;

		$refId = (isset($_REQUEST['directRefId']) && empty($_REQUEST['directRefId']) == false) ? trim($_REQUEST['directRefId']) : '';
		$directMailArr = (isset($_REQUEST['direct_mail']) && is_array($_REQUEST['direct_mail']) && count($_REQUEST['direct_mail']) > 0) ? $_REQUEST['direct_mail'] : '';
		$directmailId = (isset($_REQUEST['direct_row_id']) && is_array($_REQUEST['direct_row_id']) && count($_REQUEST['direct_row_id']) > 0) ? $_REQUEST['direct_row_id'] : '';
		$defaultDirect = (isset($_REQUEST['defaultdirect']) && empty($_REQUEST['defaultdirect']) == false) ? $_REQUEST['defaultdirect'] : '';

		if(empty($refId) == false && empty($directmailId) == false && empty($directMailArr) == false){
			$counter = 0;
			foreach($directmailId as $key => $directRowId){
				$directEmail = ($directMailArr[$key] && empty($directMailArr[$key]) == false) ? $directMailArr[$key] : '';
				if(empty($directEmail) || (empty($defaultDirect) == false && $defaultDirect == $directEmail)) continue;

				if(empty($directRowId) == false && is_numeric($directRowId)){
					//Update direct mail
						$getSql = " UPDATE ref_multi_direct_mail SET email = '".$directEmail."' WHERE id = ".$directRowId." ";
						$resSql = imw_query($getSql) or die(imw_error());
				}else{
					//Insert direct mail
						$getSql = " INSERT INTO ref_multi_direct_mail SET email = '".$directEmail."', ref_id = ".$refId." ";
						$resSql = imw_query($getSql) or die(imw_error());
				}

				if($counter == 0 && empty($defaultDirect) && empty($directEmail) == false){
					$defaultDirect = $directEmail;
				}
				$counter++;
			}

			//Entering default direct mail for user
			//if(empty($defaultDirect) == false){
				$refSql = imw_query(" SELECT physician_Reffer_id, direct_email FROM refferphysician WHERE physician_Reffer_id = ".$refId." ") or die(imw_error());
				if($refSql && imw_num_rows($refSql) > 0){
					$rowData = imw_fetch_assoc($refSql);
	
					$directEmail = $rowData['direct_email'];
					$rowId = $rowData['physician_Reffer_id'];

					if($directEmail == $defaultDirect){

					}else{
						$updateSql = " UPDATE refferphysician set direct_email = '".$defaultDirect."' WHERE physician_Reffer_id = ".$rowId." ";
						imw_query($updateSql);
					}
				}
			//}
			
			$returnVal = true;
		}

		echo json_encode($returnVal);
	break;

	//Checking Current Ref Phy direct mail
	case 'check_direct_mail':
		$returnVal = array();

		$refId = (isset($_REQUEST['refId']) && empty($_REQUEST['refId']) == false) ? trim($_REQUEST['refId']) : '';
		$directMail = (isset($_REQUEST['directMail']) && empty($_REQUEST['directMail']) == false) ? trim($_REQUEST['directMail']) : '';

		$newMail = '';
		$totalCount = 0;

		if(empty($refId) == false && empty($directMail)){
			//Checking in Ref Multi Direct table
			$sql = imw_query(" SELECT count(id) as totalCount FROM ref_multi_direct_mail WHERE ref_id = ".$refId." AND del_status = 0 ORDER BY id ASC LIMIT 0,1 ");
			if($sql && imw_num_rows($sql) > 0){
				$rowFetch = imw_fetch_assoc($sql);
				$totalCount = (empty($rowFetch['totalCount']) == false) ? $rowFetch['totalCount'] : '';
			}

			//Getting Default Email
			$sqlRow = imw_query(" SELECT direct_email FROM refferphysician WHERE physician_Reffer_id = ".$refId." ");
			if($sqlRow && imw_num_rows($sqlRow) > 0){
				$rowFet = imw_fetch_assoc($sqlRow);
				$newMail = (empty($rowFet['direct_email']) == false) ? $rowFet['direct_email'] : '';
                if($newMail) {
                    $totalCount=$totalCount+1;
                }
			}

		}
		if(empty($newMail) == false) $returnVal['mailText'] = $newMail;
		$returnVal['totalCount'] = $totalCount;

		echo json_encode($returnVal);
	break;
	case 'show_po_proc':
		if($_REQUEST[ref_id]){
			$q=imw_query("select proc_id,linked_op from slot_procedures_linked_op where ref_id=$_REQUEST[ref_id]")or die(imw_error());
			while($d=imw_fetch_object($q))
			{
				$saved_data[$d->proc_id]=$d->linked_op;
			}
		}
		$sqlRow = imw_query("SELECT sp1.id, sp1.proc, sp1.acronym, sp2.times FROM slot_procedures sp1 LEFT JOIN slot_procedures sp2 ON sp1.proc_time = sp2.id WHERE sp1.times = '' AND sp1.proc != '' AND sp1.doctor_id = 0 AND sp1.active_status!='del' and sp1.source='' ORDER BY sp1.proc")or die(imw_error());
		if($sqlRow && imw_num_rows($sqlRow) > 0){
			echo"<table class=\"table table-bordered table-hover table-striped adminnw\">
			<thead><tr><th>Sr.</th><th>Procedure</th><th>Post Op</th></tr></thead>";
			while($rowFet = imw_fetch_assoc($sqlRow))
			{
				$row++;
				$id=$rowFet['id'];
				$day=$week=$month=$saved_str='';
				$saved_str=$saved_data[$id];
				if(strstr($saved_str, 'Day'))$day='checked';
				if(strstr($saved_str, 'Week'))$week='checked';
				if(strstr($saved_str, 'Month'))$month='checked';
				
				echo"<tr><td class='col-sm-1 parent'>
				<div class='checkbox checkbox-inline'>
				   <input type='checkbox' name='op_option[]' value='$id' id='".$id."_main' class=''>
				   <label for='".$id."_main'>$row</label>
				  </div>
				</td><td class='col-sm-7'>$rowFet[proc]</td><td style='valign:top' class='col-sm-4 child'>";
				
				echo '<div class="checkbox checkbox-inline">
                       <input type="checkbox" name="op_option_'.$id.'[]" value="1 Day" id="'.$row.'_'.$id.'_day" '.$day.'>
                       <label for="'.$row.'_'.$id.'_day">1 Day</label>
					  </div>
					  
					  <div class="checkbox checkbox-inline">
                       <input type="checkbox" name="op_option_'.$id.'[]" value="1 Week" id="'.$row.'_'.$id.'_week" '.$week.'>
                       <label for="'.$row.'_'.$id.'_week">1 Week</label>
					  </div>
					  
					  <div class="checkbox checkbox-inline">
                       <input type="checkbox" name="op_option_'.$id.'[]" value="1 Month" id="'.$row.'_'.$id.'_month" '.$month.'>
                       <label for="'.$row.'_'.$id.'_month">1 Month</label>
					  </div>';
				echo"</td></tr>";
			}
			echo"</table>";
		}
	break;

	default: 
}

if( $hl7 !== false && $logHl7 === true)
{
	if( isset($GLOBALS['HL_RECEIVING']) && is_array($GLOBALS['HL_RECEIVING']) )
	{
		$hl7RecApp = ( isset($GLOBALS['HL_RECEIVING']['APPLICATION']) ) ? $GLOBALS['HL_RECEIVING']['APPLICATION'] : '';
		$hl7RecFac = ( isset($GLOBALS['HL_RECEIVING']['FACILITY']) ) ? $GLOBALS['HL_RECEIVING']['FACILITY'] : '';
		$hl7->setReceivingFacility($hl7RecApp, $hl7RecFac);
	}
	
	$hl7->insertSegment('REFPHY', '', array('ref_phy_id'=>$id));
	$hl7->log_message();
}

//Required functions
function ref_grp_arr(){
	$qry = "SELECT ref_group_id,ref_group_name FROM ref_group_tbl WHERE ref_group_status='0' ORDER BY ref_group_name";
	$res = imw_query($qry);
	$return_rows=array();
	if(imw_num_rows($res)>0){
		while($row=imw_fetch_assoc($res)){
			$return_rows[]=$row;
		}	
	}
	return $return_rows;		
}
function facility_arr(){
	$qry = "select id,name from facility order by name";
	$res = imw_query($qry);
	$return_rows=array();
	if(imw_num_rows($res)>0){
		while($row=imw_fetch_assoc($res)){
			$return_rows[]=$row;
		}	
	}
	return $return_rows;		
}
function notice_days_arr(){
	for($dayCount=0;$dayCount<=14;$dayCount++){			
		if($dayCount==0){
			$label="None";
		}
		else{
			$label=$dayCount;
		}
		$return_rows[] = array("dayCount"=>$dayCount,"label"=>$label);
	}	
	return $return_rows;
}
function default_grp_arr(){
	$qry = "select gro_id, name from groups_new where del_status='0'";
	$res = imw_query($qry);
	$return_rows=array();
	if(imw_num_rows($res)>0){
		while($row=imw_fetch_assoc($res)){
			$return_rows[]=$row;
		}	
	}
	return $return_rows;
}
function all_referedPhysician($orderBy = "lname, fname"){
	
	$prividerDataArr = array();
	$q = "select id,fname,mname,lname,Enable_Scheduler,user_type,delete_status from users order by ".$orderBy." ";
	$rs = imw_query($q); 
	while($row = imw_fetch_assoc($rs)){
		$nameStr = '';
		$nameStr  .= (!empty($row['lname']))?$row['lname']:"";
		$nameStr .= (!empty($row['fname']))?", ".$row['fname']:"";
		$nameStr .= ((!empty($row['mname']) && !empty($row['fname']))?" ".$row['mname']:"");
		if(trim($nameStr) != "")
		$prividerDataArr[] = array("id"=>$row['id'],"name"=>ucwords($nameStr));
		
	}
	return $prividerDataArr;
}
function all_facility(){
	$returnArr = array();
	$q = "select id,name from facility where name !='' and name is not null order by trim(name)";
	$re = imw_query($q);
	while($row = imw_fetch_assoc($re)){
		$returnArr[] = $row;
	}
	return $returnArr;
}
function get_referred_physician_name($ids){
	$retArrRefPhy = array();
	$q_usr = "SELECT id,fname,lname,mname FROM users WHERE id IN (".$ids.")";
	$res_usr = imw_query($q_usr);
	$retArrRefPhy = array();
	while($row_usr = imw_fetch_assoc($res_usr)){
		$name = $row_usr['lname'];
		$name .= ", ".$row_usr['fname'];
		$retArrRefPhy[] = array("id"=>$row_usr['id'],"name"=>$name);
	}
	return $retArrRefPhy;
}
function get_default_facility_name($ids){
	$retArr = array();
	$q_usr = "SELECT id,name FROM facility WHERE id IN (".$ids.")";
	$res_usr = imw_query($q_usr);
	while($row_usr = imw_fetch_assoc($res_usr)){
		$retArr[] = array("id"=>$row_usr['id'],"name"=>$row_usr['name']);
	}
	return $retArr;
}

//------------------------	START TAXONOMY NUMBER ARRAY FOR AUTO COMPLETE	------------------------//
function all_texonomy(){
	$strTaxonomy = "select * from refferphysician group by Texonomy";
	$qryTaxonomy = imw_query($strTaxonomy);
	$retArr = array();
	while($row = imw_fetch_array($qryTaxonomy)){
		$taxonomyNumber = $row['Texonomy'];
		$retArr[] = str_replace("'","",$taxonomyNumber);
	}
	return $retArr;
}

//======================FUNCTION TO GET PRACTICE NAME & ADDRESS================================//
function all_practiceInfo(){
	$strpracticeInfo="";
	$strpracticeInfo = "SELECT PractiseName,Address1,Address2,ZipCode,zip_ext,City,State from refferphysician where PractiseName!='' AND delete_status!='1' GROUP BY PractiseName";
	$qrypracticeInfo = imw_query($strpracticeInfo);
	$practiceInfo_name_arr = array();
	while($row = imw_fetch_assoc($qrypracticeInfo)){
		$p_val = array();
		$p_val = $row;
		$prac_name = implode("~|~",$p_val);
		$practiceInfo_name_arr[] = trim($row["PractiseName"]);
		$practiceInfo_val_arr[]= trim($prac_name);
	}
	$return = array();
	$return['name'] = $practiceInfo_name_arr;
	$return['val'] = $practiceInfo_val_arr;
	
	return $return;
}
?>
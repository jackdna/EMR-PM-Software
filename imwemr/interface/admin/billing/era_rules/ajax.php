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

require_once("../../../../config/globals.php");

//error_reporting(E_ALL & ~E_NOTICE);
//ini_set("display_errors",1);

$task	= isset($_REQUEST['task']) ? trim($_REQUEST['task']) : '';
$so		= isset($_REQUEST['so']) ? trim($_REQUEST['so']) : 'era_trans_method';
$soAD	= isset($_REQUEST['soAD']) ? trim($_REQUEST['soAD']) : 'ASC';
$table	= "era_rules";
$pkId	= "era_rule_id";
$chkFieldAlreadyExist = "era_trans_method";
switch($task){
	case 'delete':
		$id = $_POST['pkId'];
		//$q 		= "DELETE FROM ".$table." WHERE ".$pkId." IN (".$id.")";
		$q 		= "UPDATE ".$table." SET del_status='1', del_date_time='".date("Y-m-d H:i:s")."', del_by='".$_SESSION["authId"]."' WHERE ".$pkId." IN (".$id.")";
		$res 	= imw_query($q);
		if($res){
			echo '1';
		}else{
			echo '0';//.imw_error()."\n".$q;
		}
		break;
	case 'save_update':
		$id = $_POST[$pkId];
		unset($_POST[$pkId]);
		unset($_POST['task']);
		
		$era_cas_code_check = "";
		
		$ar_era_cas_code = $_POST["era_cas_code"];		
		if(count($ar_era_cas_code) > 0){
			foreach($ar_era_cas_code as $k => $v){
				$v = trim($v);
				if(!empty($v)){
					if(!empty($era_cas_code_check)){ $era_cas_code_check.=" OR "; }
					$era_cas_code_check .= ' era_cas_code LIKE \'%"'.$v.'"%\' '; 
				}
			}
		}
		if(!empty($era_cas_code_check)){ $era_cas_code_check = "(".$era_cas_code_check.")"; }		
		
		$era_cas_code_tm = serialize($_POST["era_cas_code"]);
		unset($_POST['era_cas_code']);
		$_POST['era_cas_code'] = $era_cas_code_tm;
		
		$query_part = "";
		foreach($_POST as $k=>$v){						
			$query_part .= $k."='".addslashes($v)."', ";
			
		}
		$query_part = substr($query_part,0,-2);
		$qry_con = " AND del_status='0' ";
		if(!empty($id)){$qry_con.=" AND ".$pkId."!='".$id."'";}
		$q_c="SELECT ".$pkId." from ".$table." WHERE ".$chkFieldAlreadyExist."='".$_POST[$chkFieldAlreadyExist]."' ".$qry_con;
		
		$r_c=imw_query($q_c);
		if(imw_num_rows($r_c)==0){		
			$flg=0; $nm_mthd="";
			//Code check
			if(!empty($era_cas_code_check) && $_POST[$chkFieldAlreadyExist]!="Adjustment"){
				
				$q_c = "SELECT ".$pkId.", era_trans_method, era_cas_code from ".$table." WHERE era_trans_method!='Adjustment' AND ".$era_cas_code_check." ".$qry_con;
				$r_c=imw_query($q_c);
				$flg = imw_num_rows($r_c);
				
				if($flg>0){
				while($r_s = imw_fetch_assoc($r_c)){
					foreach($ar_era_cas_code as $k => $v){
						$ar_tmp = unserialize($r_s["era_cas_code"]);
						if(in_array($v, $ar_tmp)){
							$nm_mthd[$r_s["era_trans_method"]][] = $v;
						}
					}
				}
				}
					
			}
			
			if($flg==0){
				if($id==''){
					$q = "INSERT INTO ".$table." SET entered_date_time='".date("Y-m-d H:i:s")."', entered_by='".$_SESSION["authId"]."', ".$query_part;
				}else{
					$q = "UPDATE ".$table." SET ".$query_part." WHERE ".$pkId." = '".$id."'";
					
					//Log
					$sql = "SELECT * FROM ".$table." WHERE ".$pkId." = '".$id."' ";
					$res = imw_query($sql);
					$flg = imw_num_rows($res);
					$row_prev = ($flg>0) ? imw_fetch_assoc($res) : false;
					
				}
				$res = imw_query($q);
				if($res){
					echo 'Record Saved Successfully.';
					
					//Log
					if(strpos($q, "UPDATE")!==false && $row_prev!==false){						
						if($row_prev["era_trans_method"]!=$_POST['era_trans_method'] || $row_prev["era_cas_code"]!=$_POST['era_cas_code']){	//check if values changed					
							//insert
							$q = "INSERT INTO era_rules_log (era_rules_log_id, modify_by, mod_date_time, 
													era_rule_id, era_trans_method, era_cas_code, 
													entered_date_time, entered_by, 
													del_status, del_date_time, del_by)
											VALUES(NULL, '".$_SESSION["authId"]."', '".date("Y-m-d H:i:s")."', 
													'".$row_prev["era_rule_id"]."', '".$row_prev["era_trans_method"]."', '".$row_prev["era_cas_code"]."',
													'".$row_prev["entered_date_time"]."', '".$row_prev["entered_by"]."', 
													'".$row_prev["del_status"]."', '".$row_prev["del_date_time"]."', '".$row_prev["del_by"]."' ) ";
							imw_query($q);
						}
					}
					
				}else{
					echo 'Record Saving failed.'.imw_error()."\n".$q;
				}
			}else{
				$er_str_codes="";
				if(count($nm_mthd) > 0){
					foreach($nm_mthd as $k => $v){
						if(count($v)>0){
							$er_str_codes .= "CAS code(s) ".implode(",", $v)." already exists in ".$k." method.<br>";
						}	
					
					}
				}
			
				echo "enter_unique_2:".$er_str_codes;
			}			
			
		}else {
			echo "enter_unique";	
		}
		break;
	case 'show_list':
		$q = "SELECT era_rule_id,era_trans_method, era_cas_code FROM ".$table." where del_status='0' ORDER BY $so $soAD";
		$r = imw_query($q);
		$rs_set = array();
		if($r && imw_num_rows($r)>0){

			while($rs = imw_fetch_assoc($r)){
				
				$rs["era_cas_code"] = implode(", ", unserialize($rs["era_cas_code"]));
 				
				$rs_set[] = $rs;
			}
		}
		echo json_encode(array('records'=>$rs_set));
		break;


		//Returns the history log for the provided id
		case 'getHistory':

			//Return response values
			$validRequest = true;
			$returnMsg = null;

			$rowId = ($_REQUEST['rowId']) ? filter_var($_REQUEST['rowId'], FILTER_VALIDATE_INT) : false;

			//If row id is empty or false
			if(!$rowId){
				$validRequest = false;
				$returnMsg = 'Invalid parameters provided';
			}
			if($validRequest){
				$getRecords = " SELECT * FROM era_rules_log WHERE era_rule_id = ".$rowId." ORDER BY era_rules_log_id DESC ";
				$resRecords = imw_query($getRecords);

				if(!$resRecords || imw_num_rows($resRecords) == 0){
					$validRequest = false;
					$returnMsg = "No records found";
				}else{

					//Fetching the required data
					$returnArr = array();
					while($rowFetch = imw_fetch_assoc($resRecords)){
						$userName = ($rowFetch['modify_by']) ? $rowFetch['modify_by'] : 'NA';
						
						//Get user full name 
						if($userName){
							$usrdetails = getUserDetails($userName, 'fname,lname,mname');
							if($usrdetails) $userName =  core_name_format($usrdetails['lname'], $usrdetails['fname'], $usrdetails['mname']);
						}

						$method = ($rowFetch['era_trans_method']) ? $rowFetch['era_trans_method'] : 'NA';
						$modifiedDate = ($rowFetch['mod_date_time']) ? date('m-d-Y H:i:s A ', strtotime($rowFetch['mod_date_time'])) : 'NA';

						$casCodesTmp = ($rowFetch['era_cas_code']) ? unserialize($rowFetch['era_cas_code']) : null;
						$CASCodes = ($casCodesTmp && is_array($casCodesTmp)) ? implode(', ', $casCodesTmp) : 'NA';
						
						$tmpArr = array();
						if($userName) $tmpArr['username'] = $userName;
						if($method) $tmpArr['method'] = $method;
						if($CASCodes) $tmpArr['codes'] = $CASCodes;
						if($modifiedDate) $tmpArr['date'] = $modifiedDate;

						if(count($tmpArr) > 0) $returnArr[] = $tmpArr;
					}

					if(count($returnArr) > 0) $returnMsg = $returnArr;
				}
			}

			//Return response
			$returnVal = array('status' => $validRequest, 'data' => $returnMsg);
			echo json_encode($returnVal);
			die;

		break;
	default: 
}
?>
<?php
/*
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
*/
?>
<?php
/*
File: ChartPtLock.php
Coded in PHP7
Purpose: This Class provides Patient Chart Lock functionality.
Access Type : Include file
*/
?>
<?php

class ChartPtLock{

	private $tbl;
	private $db;
	private $pId;
	private $uId;
	private $lock_prd;
	private $tab;
	
	function __construct($uid=0,$pid=0){
		//$this->db = $GLOBALS['adodb']['db'];
		$this->tbl = "chart_pt_lock";		
		$this->pId = !empty($pid) ? $pid : $_SESSION["patient"];
		$this->uId = !empty($uid) ? $uid : $_SESSION["authId"];
		$this->lock_prd = $GLOBALS['Timeout_chart'];
		$this->tab="WorkView";
	}
	
	function isPtLocked($flgChkUser=0){
		if($flgChkUser == 2){
			$sql = "SELECT id FROM ".$this->tbl." WHERE userId = '".$this->uId."' AND pt_id = '".$this->pId."' "; // userid and pt id
		}elseif($flgChkUser == 1){
			$sql = "SELECT id FROM ".$this->tbl." WHERE userId = '".$this->uId."' AND pt_id = '".$this->pId."' AND tab='".$this->tab."' "; // userid and pt id and tab
		}else{
			$sql = "SELECT id FROM ".$this->tbl." WHERE pt_id ='".$this->pId."' AND userId != '".$this->uId."' AND tab='".$this->tab."' "; // ptid and NOT user id and tab
		}
		
		$res = imw_query($sql); 	
		if($res != false){
			$num = imw_num_rows($res);		
			return ($num > 0) ? true : false ;
			
		}else{
			print imw_error();
		}
		return false;
	}	
	
	//Release any patient locked with current user.
	//so one can lock only one patient at a time.	
	function releaseUsersPastPt($u_id=0){
		//Check 
		$u_id = ($u_id != 0) ? $u_id : $this->uId;
		$sql = "DELETE FROM ".$this->tbl." WHERE userId = '".$u_id."'  ";		
		$res = sqlQuery($sql); // or die("Error in query: ".$this->db->errorMsg());
		if($res != false){
			return false;
		}else{
			print imw_error();
		}
	}
	
	function releasePtTabLock(){
		$sql = "DELETE FROM ".$this->tbl." WHERE pt_id = '".$this->pId."' AND tab='".$this->tab."'  ";
		$res = sqlQuery($sql); //or die("Error in query: ".$this->db->errorMsg());
		if($res != false){
			return false;
		}else{
			print imw_error();
		}
	}
	
	function lockThisPt(){
		//Check if Lock Exists		
		$flg = $this->isPtLocked(1);
		$num = ($flg==true) ? 1 : 0;
		
		//Insert if user NOT Locked with current patient
		if($num == 0){
			//Release any past lock
			$this->releaseUsersPastPt();
			
			//Release any past lock for  this patient for this tb
			$this->releasePtTabLock();
			
			//Get Session ID
			$sid=session_id();
			
			//lock new one		
			$sql = "INSERT INTO ".$this->tbl." (id, pt_id, userId,sid,sessTime, tab) ".
					"VALUE (NULL, '".$this->pId."', '".$this->uId."','".sqlEscStr($sid)."','".date("Y-m-d H:i:s")."','".$this->tab."') ";
					
					
			$res = sqlQuery($sql); //or die("Error in query: ".$this->db->errorMsg());
			if($res != false){
				return true;
			}else{
				print imw_error();
			}
		}
		
		return false;
	}

	function transferLock($id){
		
		//Check if previous user has any Lock		
		$flg = $this->isPtLocked(2);
		$num = ($flg==true) ? 1 : 0;		
		
		//Update if  previous User is LOCKED with current patient
		if($num == 1){
			//Delete if curr user has any lock
			$this->releaseUsersPastPt($id);
			
			//Release any past lock for  this patient for this tb
			//$this->releasePtTabLock();
			
			//Get Session ID
			$sid=session_id();

			//Update previous lock			
			$sql = "UPDATE ".$this->tbl." ".
				 "SET userId = '".$id."', sid = '".imw_real_escape_string($sid)."',sessTime='".date("Y-m-d H:i:s")."' ".
				 "WHERE pt_id = '".$this->pId."' AND userId = '".$this->uId."' ";
			
			$res = sqlQuery($sql); //or die("Error in query: ".$this->db->errorMsg());
			if($res != false){
				return true;
			}else{
				print imw_error();
			}
		}
	}
	
	function transferLock_PT(){
		//Check if previous user has any Lock		
		$flg = $this->isPtLocked();
		$num = ($flg==true) ? 1 : 0;
		
		//Update if  any User is LOCKED with current patient
		if($num == 1){			
			
			//Delete if curr user has any lock
			$this->releaseUsersPastPt();
			
			//Get Session ID
			$sid=session_id();

			//Update previous lock			
			$sql = "UPDATE ".$this->tbl." ".
				 "SET userId = '".$this->uId."', sid = '".imw_real_escape_string($sid)."',sessTime='".wv_dt('now')."' ".
				 "WHERE pt_id = '".$this->pId."' AND tab='".$this->tab."' ";				 
			$res = sqlQuery($sql); //or die("Error in query: ".$this->db->errorMsg());
			if($res != false){
				return true;
			}else{
				print imw_error();
			}
		}
	}
	
	function getLockedRecords(){
		$arr = array();		
		$sql = "SELECT ".
			 "patient_data.fname AS pt_fname, patient_data.lname AS pt_lname,patient_data.mname AS pt_mname, ".
			 "users.fname AS ur_fname, users.mname AS ur_mname, users.lname AS ur_lname, ".
			 "".$this->tbl.".id, ".$this->tbl.".tab ".
			 "FROM ".$this->tbl." ".
			 "LEFT JOIN patient_data ON patient_data.id = ".$this->tbl.".pt_id ".
			 "LEFT JOIN users ON users.id = ".$this->tbl.".userId ".
			 "ORDER BY pt_fname, ur_fname ";		
		$res = sqlStatement($sql); //or die("Error in query: ".$this->db->errorMsg());
		if($res != false){			
			while($row=sqlFetchArray($res)){
				$ptNm = $urNm = "";				
				$id = $row["id"];
				$tab = $row["tab"];
				$ptNm .= !empty($row["pt_fname"]) ? $row["pt_fname"]." " : "";
				$ptNm .= !empty($row["pt_mname"]) ? $row["pt_mname"]." " : "";
				$ptNm .= !empty($row["pt_lname"]) ? $row["pt_lname"]." " : "";				
				
				$urNm .= !empty($row["ur_fname"]) ? $row["ur_fname"]." " : "";
				$urNm .= !empty($row["ur_mname"]) ? $row["ur_mname"]." " : "";
				$urNm .= !empty($row["ur_lname"]) ? $row["ur_lname"]." " : "";				
				// 
				$arr[] = array("id"=>$id, "Pt"=>$ptNm, "User"=>$urNm, "tab"=>$tab);
				
			}
			
		}else{
			print imw_error();
		}
		return $arr;
	}
	
	function releaseRecords($arr){
		//return if empty
		if(count($arr) <= 0){
			return false;
		}
		
		//Implode array ids
		$str = implode(",",$arr);
		
		//Check if any Locked patient is released
		//if yes, release lock in session also
		if(isset($_SESSION["lockedChart"]) && !empty($_SESSION["lockedChart"])){
			$sql = "SELECT id FROM ".$this->tbl." WHERE id in (".$str.") AND pt_id = '".$_SESSION["lockedChart"]."'";
			$res = sqlStatement($sql) ;//or die("Error in delete: ".$this->db->errorMsg());
			if($res != false){
				$num = imw_num_rows($res);
				if($num >= 1){
					//Unset Previous lock key session		
					$_SESSION["lockedChart"]="";
					$_SESSION["lockedChart"]=NULL;
					unset($_SESSION["lockedChart"]);
				}
			}
		}	
		
		//Delete All
		$sql = "DELETE FROM ".$this->tbl." WHERE id in (".$str.") ";
		$res = sqlQuery($sql); //or die("Error in delete: ".$this->db->errorMsg());
	}	
		
	function isLockedSessExists(){
		$ret = true;		
		$res = $this->getLockedPtInfo();
		if($res != false){			
			
			$sid = $res["sid"];
			$userId = $res["userId"];
			$sessTime = $res["sessTime"];					

			$ssTm = strtotime($sessTime);
			$crDt = strtotime(date("Y-m-d H:i:s"));
			$minDiff = floor(($crDt-$ssTm)/60);
			/*
			echo "<br>SessTime: ".$sessTime;
			echo "<br>CurTime: ".date("Y-m-d H:i:s")." - ".$crDt;
			echo "<br>mindiff: ".$minDiff;
			echo "<br>SetLimit: ".$this->lock_prd;
			*/
			if($minDiff > $this->lock_prd){
				$ret = false; //false
				//Release Lock
				$this->releaseUsersPastPt($userId);
				
				//echo "Relaesed Lock";

			}						
			
			/*
			$dir = session_save_path();			
			if(!empty($sid)){
				if(!is_file($dir."/sess_".$sid)){
					$ret = false; //false
					//Release Lock
					$this->releaseUsersPastPt($userId);
				}
			}
			*/
			
			/*
			$ssTm = $res->fields["sessTime"];				
			if(!empty($ssTm)){
				//TEST
				
				$sessTmOut = ini_get('session.gc_maxlifetime');
				
				$dtTm1 = new DateTime();
				$dtTm2 = new DateTime($ssTm); //				
				
				$str1 = $dtTm1->format('Y-m-d H:i:s');
				$dtTm2->add(new DateInterval("PT".$sessTmOut."S"));
				$str2 = $dtTm2->format('Y-m-d H:i:s');
				
				if($str1 > $str2){
					//echo "Release Lock";
					$ret = false;
				}

				//TEST
			}
			*/
		}

		return $ret;
	}
	
	//return Lock info of patient
	function getLockedPtInfo(){
		$sql = "SELECT userId,sid,sessTime FROM ".$this->tbl." WHERE pt_id = '".$this->pId."' AND tab='".$this->tab."' ";
		$res = sqlQuery($sql); // or die("Error in query: ".$this->db->errorMsg());
		return $res;
	}

	//Get User Id
	function getlockedUserId(){
		$res = $this->getLockedPtInfo();
		if($res != false){			
			$userId = $res["userId"];
		}
		
		if(empty($userId)) {
			$userId = false;
		}
		
		return $userId;
	}

	function upSessTime(){
		//Update previous lock			
		$sql = "UPDATE ".$this->tbl." ".
		   	   "SET sessTime='".date("Y-m-d H:i:s")."' ".
			   "WHERE pt_id = '".$this->pId."' and userId = '".$this->uId."' AND tab='".$this->tab."' ";
		$res = sqlQuery($sql); // or die("Error in query: ".$this->db->errorMsg());
	}
	
	function setTabName($str){if(!empty($str)){$this->tab = $str;}  }
	
	function get_view_access($scva_flgWorkView="", $scva_lock_exam=""){
		$elem_per_vo = 0;
		
		$chart_showLock="";
		// Check View Only Permission
		if(core_check_privilege(array("priv_vo_clinical")) == true){
			$elem_per_vo = 1;
		}else if(isset($this->pId) && !empty($this->pId)){

		//Check User Type for Valid CN User ----------
		if(!in_array($_SESSION["logged_user_type"],$GLOBALS['arrValidCNPhy']) && 
			!in_array($_SESSION["logged_user_type"],$GLOBALS['arrValidCNTech'])	
		  ){
			$elem_per_vo = 1;
		}

		//Check User Type for Valid CN User ----------

		//

		//print("Permit: ".$_SESSION["permitChart"]." - Locked: ".$_SESSION["lockedChart"]." - PtId: ".$_SESSION["patient"]." - UId: ".$_SESSION["authId"]);

		//		

			if(defined("V_CHART_LOCK") && (V_CHART_LOCK == "1") && $elem_per_vo != 1){
			
				//if test		
				if(preg_match("/tests|test\_|ascan|iol_master|ophtha/i", $_SERVER["REQUEST_URI"])){ $scva_lock_exam="Tests"; }
			
				//Key is asigned to lock or permit any chart
				$lockKey = $this->pId."-".$this->uId."-".$scva_lock_exam;
				
				//Check database if work view is opened
				//Set scva_flag
				if(isset($scva_flgWorkView) && !empty($scva_flgWorkView)){		
				}else{
					$scva_flgWorkView = 0;
				}

				//Obj Chart Lock
				$oPtLock = $this; //new ChartPtLock($_SESSION['authId'],$_SESSION["patient"]);
				
				//
				$oPtLock->setTabName($scva_lock_exam);
				
				if(($_SESSION["permitChart"] == $lockKey) && ($scva_flgWorkView != 1)){
					
					//1 Check Permit again
					//1. if user permit is valid update activity sess time
					//2. if user permit is obselete
					//2.1 Release lock of patient
					//2.2 provide view only access
					//2.3 rediect to referer page			
					
					//Check to give Lock/Permit to user for the patient for chart notes ------			
					$tmpFlg = $oPtLock->isPtLocked(1);			

					if($tmpFlg){
						//update sesstime
						$oPtLock->upSessTime();

					}else{
						
						//Check if patient with some another
						$tmpFlg = $oPtLock->isPtLocked();			
						
						//Check if User Session Still Active, release patient if NOT
						if(!$oPtLock->isLockedSessExists()){
							$tmpFlg = false;
						}

						if($tmpFlg == true){
							// Provide View Only Permit
							$elem_per_vo = 1;
							//remove permit
							$_SESSION["permitChart"] ="";
							$_SESSION["permitChart"] =NULL;
							unset($_SESSION["permitChart"]);

							////Set Session Locked
							$_SESSION["lockedChart"] = $lockKey; //Locked
							
							//
							$lockUsrId = $oPtLock->getlockedUserId();

						}else{
							//Lock patient and Permit for Full Access
							$oPtLock->lockThisPt();	
						}			
					}

				}else	if(($_SESSION["lockedChart"] == $lockKey) && ($scva_flgWorkView != 1)){
					$elem_per_vo = 1;

					//Check to give Lock/Permit to user for the patient for chart notes ------
					$lockUsrId = $oPtLock->getlockedUserId();

				}else{		
					//Unset Previous lock key session		
					$_SESSION["lockedChart"]="";
					$_SESSION["lockedChart"]=NULL;
					unset($_SESSION["lockedChart"]);
					
					$_SESSION["permitChart"] ="";
					$_SESSION["permitChart"] =NULL;
					unset($_SESSION["permitChart"]);	
					//---------------------------------------

					//Check to give Lock/Permit to user for the patient for chart notes ------			
					$tmpFlg = $oPtLock->isPtLocked();
					
					//echo "CH1: ".$tmpFlg.",";
					
					if($tmpFlg == true){				
						
						//Check if Pt is locked with Current User
						$tmpFlgCur = $oPtLock->isPtLocked(1);
						if($tmpFlgCur == true){
							//update sesstime
							$oPtLock->upSessTime();
						}else{
							
							//echo "Ch11";
							
							//Check if User Session Still Active
							if(!$oPtLock->isLockedSessExists()){
								$tmpFlg = false;
							}else{
								$lockUsrId = $oPtLock->getlockedUserId();
							}
						}
					}
					
					//echo "CH2: ".$tmpFlg.",";
					
					if($tmpFlg == true){				
						//Provide view only access
						$elem_per_vo = 1;
						//Set Session
						$_SESSION["lockedChart"] = $lockKey; //Locked 			
					}else{				
						//Lock patient and Permit for Full Access
						$oPtLock->lockThisPt();
						$_SESSION["permitChart"] = $lockKey; // Permitted
					}
					//Check to give Lock/Permit to user for the patient for chart notes ------

				}
				
				if(!empty($_SESSION["lockedChart"])){
					$chart_showLock = true;
				}	
			}
		}		
		
		//
		$_SESSION["elem_per_vo"] = $elem_per_vo;
		
		// Check View Only Permission		
		return array($elem_per_vo, $lockUsrId, $chart_showLock, $scva_lock_exam);
		
	}
	
	//------------- Get Pt Chart Lock HTML

	function getPtChartLockHtml($lockUsrId, $tab){	
		$strTb = ($tab=="Tests") ? "Test" : "Chart note";
		$echo = "";
		$tmp = "Chart notes of this patient are locked.\nPlease contact administrator.";				
				//echo "<img src=\"".checkUrl4Remote($GLOBALS['webroot']."/chart_notes/images/chart_lock.png")."\" id=\"icoPtLock\" alt=\"".$tmp."\" onclick=\"lock_showPassPrompt();\">";
				//$echo .=  "<span class=\"glyphicon glyphicon-lock\" id=\"icoPtLock\" alt=\"".$tmp."\" onclick=\"lock_showPassPrompt();\" ></span>";
				
				//
				if(HASH_METHOD=="MD5"){
					$str ="<script language=\"javascript\" src=\"".$GLOBALS['webroot']."/library/js/md5.js\"></script>";
				}else{				
					$str ="<script language=\"javascript\" src=\"".$GLOBALS['webroot']."/library/js/js_crypto_sha256.js\"></script>";
				}
				
				//Div For Admin Pass
				$str .= "<div id=\"divLockPassPrompt\">";
				
				//Div For Msg
				if(isset($lockUsrId) && !empty($lockUsrId)){
					$oUser = new User($lockUsrId);
					$lockUsrNm = $oUser->getName(3);
					$str .="<p id=\"divLockMsg\">Patient ".$strTb." is being open for edit by ".trim($lockUsrNm).".</p>";
				}
					 
				$str .= "Enter administrator or logged in physician's password to open chart notes lock.<br/><br/>".
				
					 "<form name=\"frmlock\" onsubmit=\"lock_checkPass('".HASH_METHOD."');return false;  \" class=\"form-inline\">".				 
						"Password: ".
						"<input type=\"password\" id=\"elem_lockAdminPass\" name=\"elem_lockAdminPass\" value=\"\" class=\"form-control\" > ".					
						"<input name=\"elem_btnLock\" type=\"submit\"  class=\"dff_button btn btn-success\" id=\"elem_btnLock\" value=\"GO!\" />".
						"<input name=\"elem_btnRoA\" type=\"button\"  class=\"dff_button btn btn-info\" id=\"elem_btnRoA\" value=\"Read Only Access\" onClick=\"$('#divLockPassPrompt').hide();\" />".
						"<input type=\"hidden\" id=\"elem_tab\" name=\"elem_tab\" value=\"".$tab."\" > ".
					 "</form>".
					 "</div>";
		
		$echo .= $str;
		return $echo;
	}

	//-------------
	
	function unlock_handler(){
		$echo = "0";
		$chkStr = trim($_POST["elem_lockAdminPass"]);
		$chkStr = hashPassword($chkStr);
		$tab = trim($_POST["elem_tab"]);			
		
		$sql = "SELECT access_pri, user_type FROM users
				WHERE password = '".$chkStr."' ";

		$row = sqlQuery($sql);
		if($row != false){
			
			$arr_privileges = unserialize(html_entity_decode(trim($row["access_pri"])));
			if($arr_privileges["priv_admin"] == 1){
				$echo = "1";
			}
			
		}
		
		if($echo == "0"){
			$isCnPhy = in_array($_SESSION["logged_user_type"],$GLOBALS['arrValidCNPhy']);
			if($isCnPhy){
				$sql = "SELECT access_pri, user_type FROM users
						WHERE password = '".$chkStr."' AND id='".$this->uId."' ";
				$row = sqlQuery($sql);
				if($row != false){
					$echo = "1";
				}
			}
		}

		//if yes, release lock
		if($echo == "1"){
			$oPtLock = $this; //new ChartPtLock($_SESSION["authId"],$_SESSION["patient"]);
			$oPtLock->setTabName($tab);
			$oPtLock->transferLock_PT();

			//Clear Session
			//Unset Previous lock key session
			$_SESSION["lockedChart"]="";
			$_SESSION["lockedChart"]=NULL;
			unset($_SESSION["lockedChart"]);
		}
		print($echo);
	}

	static function is_viewonly_permission(){
		$ret = 0;
		if(isset($_SESSION["lockedChart"]) && !empty($_SESSION["lockedChart"])){
			$ret = 1;
		}else{
			if(isset($_SESSION["elem_per_vo"]) && !empty($_SESSION["elem_per_vo"])){
				$ret = 1;
			}
		}
		return $ret;
	}

}

?>
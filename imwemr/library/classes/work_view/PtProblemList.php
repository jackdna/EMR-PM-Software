<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
/*	
File: PtProblemList.php
Purpose: Class for problem list defined
Access Type: Include
*/
//PtProblemList.php

class PtProblemList{

	private $tbl;
	private $db;
	private $pId;
	private $tbllog;
	function __construct($pid){
		//$this->db = $GLOBALS['adodb']['db'];
		$this->tbl = "pt_problem_list";
		$this->tbllog = "pt_problem_list_log";
		$this->pId = $pid;
	}

	public function getProbListArray($type="", $idquery="", $readonly = "", $ordrby="ASC",$limit=""){
		//Type
		if(!empty($type) && ($type != "All")){
			
			if($type == "Active"){ //ADD Other in Active					 
				$type = "AND (status != 'Inactive' AND status != 'Resolved' AND status != 'Unobserved' AND status != 'Deleted')";
			}else{
				$type = "AND status = '".$type."' ";
			}			

		}else{
			$type = "";	
		}
		
		$arrRet = array();
		$sql = "SELECT *, Date_Format(onset_date,'".get_sql_date_format()."') as new_date, TIME_FORMAT(OnsetTime,'%h:%i %p') as new_OnsetTime 
				FROM ".$this->tbl." WHERE ";
		if($readonly == "yes"){
			$sql .= "problem_name != '' AND";
		}
		$sql .= " pt_id = '".$this->pId."' $idquery ".$type." ORDER BY onset_date ".$ordrby.", id ".$ordrby." ".$limit;
		
		$res = sqlStatement($sql); // or die("Error in query: ".$sql." - ".$this->db->errorMsg());
		$c=0;
		if($res != false){
			while($row=sqlFetchArray($res)){
				$arrRet[$c]["id"] = $row["id"];
				$arrRet[$c]["user_id"] = $row["user_id"];
				$arrRet[$c]["problem_name"] = stripslashes($row["problem_name"]);
				$arrRet[$c]["comments"] = stripslashes($row["comments"]);
				$arrRet[$c]["onset_date"] = $row["onset_date"];
				$arrRet[$c]["new_date"] = $row["new_date"];
				$arrRet[$c]["status"] = $row["status"];
				$arrRet[$c]["signerId"] = $row["signerId"];
				$arrRet[$c]["coSignerId"] = $row["coSignerId"];
				$arrRet[$c]["OnsetTime"] = $row["OnsetTime"];
				$arrRet[$c]["new_OnsetTime"] = $row["new_OnsetTime"];
				$arrRet[$c]["prob_type"] = $row["prob_type"];
				$arrRet[$c]["ccda_code"] = $row["ccda_code"];
				$arrRet[$c]["timestamp"] = $row["timestamp"];
				$c++;
				
			}
		}
		return $arrRet;
	}
	
	public function checkDate($dt){
		if($dt == "" || $dt == "0000-00-00"){
			$dt = date("Y-m-d");
		}
		return $dt;
	}

	public function updateRec($arr){		
		$arr["problem_name"] = trim($arr["problem_name"]);		
		$arr["onset_date"] = $this->checkDate($arr["onset_date"]);			
		if($arr["OnsetTime"]==""){
			$arr["OnsetTime"]=date('H:i:s');
		}
		if(!empty($arr["id"]) && !empty($arr["problem_name"])){
			//--- CHECK PREVIOUS STATUS ---
			$prev_status_flag = false;
			$query = "select * from ".$this->tbl." where id = '".$arr["id"]."'";
			$statusRes = sqlQuery($query);
			$tableStatus = $statusRes['status'];
			$problem_name = $statusRes['problem_name'];
			$comments = $statusRes['comments'];
			$onset_date = $statusRes['onset_date'];
			$OnsetTime = $statusRes['OnsetTime'];
			$prob_type = $statusRes['prob_type'];
			$ccda_code = $statusRes['ccda_code'];
			if(strtolower($tableStatus) != strtolower($arr["status"]) || strtolower($problem_name) != strtolower($arr["problem_name"]) || strtolower($comments) != strtolower($arr["comments"]) || strtolower($onset_date) != strtolower($arr["onset_date"]) || strtolower($OnsetTime) != strtolower($arr["OnsetTime"]) || strtolower($prob_type) != strtolower($arr["prob_type"]) || strtolower($ccda_code) != strtolower($arr["ccda_code"])){
				$prev_status_flag = true;
			}
			
			//if update from work view
			if(!empty($arr["form_id"])){	$phrs_form_id = " , form_id='".$arr["form_id"]."' ";	}else{ $phrs_form_id=""; }
			if(isset($arr["ccda_code"])){ $phrs_ccda_code = " , ccda_code = '".$arr["ccda_code"]."' "; }else{ $phrs_ccda_code = "";  }
			
			//Remove  attached  dx codes
			//$arr["problem_name"] = remSiteDxFromAssessment($arr["problem_name"]);
			
			//Nov 2, 2016:: Discussed with Arun, if DX Code is same, then date in problem list should not be changed. Also make an update to correct previous records
			if(isset($arr["dx"])){//will work when wv saved
			if(!empty($arr["dx"])){
				if(strpos($problem_name, $arr["dx"])!==false){
					$arr["onset_date"] = $onset_date;
					$arr["OnsetTime"] = $OnsetTime;
				}
			}else{
				if(trim($problem_name) == trim($arr["problem_name"])){
					$arr["onset_date"] = $onset_date;
					$arr["OnsetTime"] = $OnsetTime;
				}
			}
			}
			//--
			
			$sql = "UPDATE ".$this->tbl." ".
					"SET ".
					"user_id = '".$arr["user_id"]."', ".
					"problem_name = '".$arr["problem_name"]."', ".
					"comments = '".$arr["comments"]."', ".
					"onset_date = '".$arr["onset_date"]."', ".
					"status = '".$arr["status"]."', ".
					"signerId = '".$arr["signerId"]."', ".
					"coSignerId = '".$arr["coSignerId"]."', ".
					"OnsetTime = '".$arr["OnsetTime"]."', ".
					"prob_type = '".$arr["prob_type"]."' ".
					$phrs_ccda_code.
					$phrs_form_id.
					"WHERE id = '".$arr["id"]."' ";
			$res = sqlQuery($sql); // or die("Error in query: ".$sql." ".$this->db->errorMsg());


			if($prev_status_flag == true){
				$sqllog ="insert into  ".$this->tbllog." ".
						"SET ".
						"problem_id = '".$arr["id"]."', ".
						"pt_id = '".$arr["pt_id"]."', ".
						"user_id = '".$_SESSION["authId"]."', ".
						"problem_name = '".$arr["problem_name"]."', ".
						"comments = '".$arr["comments"]."', ".
						"onset_date = '".$arr["onset_date"]."', ".
						"status = '".$arr["status"]."', ".
						"signerId = '".$arr["signerId"]."', ".
						"coSignerId = '".$arr["coSignerId"]."', ".
						"OnsetTime = '".$arr["OnsetTime"]."',
						ccda_code = '".$arr["ccda_code"]."',
						prob_type='".$arr["prob_type"]."',statusDateTime='".wv_dt('now')."'" ;
				$resLog = sqlQuery($sqllog); // or die("Error in query: ".$sqllog." ".$this->db->errorMsg());
			}
		}
	}

	public function insertRec($arr){
		$tempID=0;
		$arr["onset_date"] = $this->checkDate($arr["onset_date"]);
		if($arr["OnsetTime"]==""){
			$arr["OnsetTime"]=date('H:i:s');
		}
		if(!empty($arr["problem_name"])){
			//Remove  attached  dx codes
			//$arr["problem_name"] = remSiteDxFromAssessment($arr["problem_name"]);
			$sql = "INSERT INTO ".$this->tbl." (id,pt_id, user_id,problem_name,comments,onset_date,status,
												signerId, coSignerId, OnsetTime,prob_type,form_id,ccda_code) ".
					"VALUES (NULL, '".$arr["pt_id"]."', '".$arr["user_id"]."',  '".$arr["problem_name"]."','".$arr["comments"]."','".$arr["onset_date"]."','".$arr["status"]."',
					'".$arr["signerId"]."','".$arr["coSignerId"]."','".$arr["OnsetTime"]."','".$arr["prob_type"]."','".$arr["form_id"]."','".$arr["ccda_code"]."')";
			$tempID= sqlInsert($sql); // or die("Error in query: ".$sql." ".$this->db->errorMsg());
			
////By Ram To Log All Changes//			
$sqlLog = "INSERT INTO ".$this->tbllog." (id,problem_id,pt_id, user_id,problem_name,comments,onset_date,status,
												signerId, coSignerId, OnsetTime,statusDateTime,prob_type,ccda_code)".
					"VALUES (NULL,'".$tempID."','".$arr["pt_id"]."', '".$_SESSION["authId"]."',  '".$arr["problem_name"]."','".$arr["comments"]."','".$arr["onset_date"]."','".$arr["status"]."',
					'".$arr["signerId"]."','".$arr["coSignerId"]."','".$arr["OnsetTime"]."','".wv_dt('now')."','".$arr["prob_type"]."','".$arr["ccda_code"]."')";
			$resLog = sqlQuery($sqlLog); // or die("Error in query: ".$sqlLog." ".$this->db->errorMsg());
////By Ram To Log All Changes//

		}
		return $tempID;
	}

	public function deleteNotInArr($arr){
		if(count($arr) > 0){
			$str = "";
			foreach($arr as $key => $val){
				if(!empty($val)){
					$str .= (!empty($str)) ? "," : ""; 
					$str .= "".$val;
				}
			}
			
			if($str != ""){
				//$sql = "DELETE FROM ".$this->tbl." WHERE id NOT IN (".$str.") AND pt_id = '".$this->pId."'  ";	
				$sql = "UPDATE ".$this->tbl." SET status = 'Deleted' WHERE id NOT IN (".$str.") AND pt_id = '".$this->pId."'  ";	
				$res = sqlQuery($sql); // or die("Error in query: ".$sql." ".$this->db->errorMsg());
			}
		}
	}
	
	public function deleteProblem($id){
		if(!empty($id)){
			//$sql = "DELETE FROM ".$this->tbl." WHERE id = '".$id."' AND pt_id = '".$this->pId."'  ";				
			$sql = "UPDATE ".$this->tbl." SET status = 'Deleted' WHERE id = '".$id."' AND pt_id = '".$this->pId."'  ";				
			$res = sqlQuery($sql); // or die("Error in query: ".$sql." ".$this->db->errorMsg());
		}
	}

	public function isProblemExists($prob,$flgRetId="0"){
		$ret = ($flgRetId == "1") ? 0 : 1;
		if(!empty($prob)){
			$ret = 0;
			
			//Remove  attached  dx codes
			$prob = remSiteDxFromAssessment($prob);			
			
			$prob = addslashes($prob);
			$sql = "SELECT id FROM ".$this->tbl." WHERE problem_name LIKE '".$prob."%' AND pt_id = '".$this->pId."' ";
			$res = sqlStatement($sql); // or die("Error in query: ".$sql." ".$this->db->errorMsg());
			if($res != false){
				$num = imw_num_rows($res);
				if($num > 0){
					$row = sqlFetchArray($res);
					$id = $row["id"];
					$ret = ($flgRetId == "1") ? $id : $ret;
				}
			}	
		}
		return $ret;
	}

	public function setStatus($tmpid,$st){
		$sql = "UPDATE ".$this->tbl." ".
			   "SET ".
			   "status = '".$st."' ".
			   "WHERE id = '".$tmpid."' ";
		$res = sqlQuery($sql); // or die("Error in query: ".$sql." ".$this->db->errorMsg());
	}
	
	public function getVisitProblems($form_id){
		$arrRet=array();
		$sql = "SELECT problem_name FROM ".$this->tbl."  WHERE  pt_id = '".$this->pId."' AND form_id = '".$form_id."' ";
		$res = sqlStatement($sql); // or die("Error in query: ".$sql." - ".$this->db->errorMsg());
		$c=0;
		if($res != false){
			while($row=sqlFetchArray($res)){				
				$arrRet[$c] = stripslashes($row["problem_name"]);				
				$c++;
			}
		}
		return $arrRet;
	}

	public function updateVisitProblems($asmt,$form_id){
		//$sql = "DELETE FROM ".$this->tbl."  WHERE  pt_id = '".$this->pId."' AND form_id = '".$form_id."' AND problem_name='".imw_real_escape_string($asmt)."' ";
		$sql = "UPDATE ".$this->tbl." SET status = 'Deleted'  WHERE  pt_id = '".$this->pId."' AND form_id = '".$form_id."' AND problem_name='".imw_real_escape_string($asmt)."' ";
		$res = sqlQuery($sql); // or die("Error in query: ".$sql." - ".$this->db->errorMsg());
	}

	function getProblemName($prbId, $flgArr="0"){
		$strRet= (!empty($flgArr)) ? array() : "";
		$sql = "SELECT problem_name, onset_date  FROM ".$this->tbl."  WHERE  pt_id = '".$this->pId."' AND id = '".$prbId."' ";
		$res = sqlStatement($sql); // or die("Error in query: ".$sql." - ".$this->db->errorMsg());
		if($res != false){
			$num = imw_num_rows($res);
			if($num > 0){
				$row = sqlFetchArray($res);
				if((!empty($flgArr))){
					$strRet["nm"] = stripslashes($row["problem_name"]);
					$strRet["odt"] = $row["onset_date"];										
				}else{
					$strRet = stripslashes($row["problem_name"]);
				}	
			}
		}		
		return $strRet;
	}
	
	function upProbNameInProblem($problem_name, $prbId, $oldProbName){
		if(!empty($problem_name)){
			$sql = "UPDATE ".$this->tbl." SET problem_name = '".imw_real_escape_string($problem_name)."'  WHERE  pt_id = '".$this->pId."' AND id = '".$prbId."' ";
			//echo "<br/>".$sql;
			$res = sqlQuery($sql); // or die("Error in query: ".$sql." - ".$this->db->errorMsg());
			
			$sql = "UPDATE ".$this->tbllog." SET problem_name = '".imw_real_escape_string($problem_name)."'  WHERE  pt_id = '".$this->pId."' AND problem_id = '".$prbId."' AND problem_name LIKE '%".imw_real_escape_string($oldProbName)."%' ";
			//echo "<br/>".$sql;
			$res = sqlQuery($sql); // or die("Error in query: ".$sql." - ".$this->db->errorMsg());
			
		}
	}

	function upOnset($dt, $prbId, $flg_up=0){
		$sql = "UPDATE ".$this->tbl." SET onset_date = '".imw_real_escape_string($dt)."'  WHERE  pt_id = '".$this->pId."' AND id = '".$prbId."' ";			
		if($flg_up==1){	
			$res = sqlQuery($sql); // or die("Error in query: ".$sql." - ".$this->db->errorMsg());
		}else{
			echo "<br/>".$sql;
		}
		
		$sql = "UPDATE ".$this->tbllog." SET onset_date = '".imw_real_escape_string($dt)."'  WHERE  pt_id = '".$this->pId."' AND problem_id = '".$prbId."'  ";
			//echo "<br/>".$sql;
		if($flg_up==1){
			$res = sqlQuery($sql); // or die("Error in query: ".$sql." - ".$this->db->errorMsg());
		}else{
			echo "<br/>".$sql;
		}
	}
	
}

function getProbelmHistory($problemID){
	$return .= '<table class="table_collapse">';
	$selQuery = "select date_format( statusDateTime, '".get_sql_date_format('','y')."' ) as statusdate,  
				date_format( statusDateTime, '%h:%i %p' ) as statustime,`status` , `user_id` 
				from pt_problem_list_log where `problem_id` = '$problemID' order by id desc ";
	$res = imw_query($selQuery);
	$numRows = imw_num_rows($res);
	if($numRows>0){
		$counter=1;
		while($resRow=imw_fetch_array($res)){
			$ousr = new User($resRow["user_id"]);
			$tmpName = $ousr->getName($resRow["user_id"],2);
			$user_name_exp = explode(',',$tmpName[0]);
			$user_name = ucfirst(substr(trim($user_name_exp[1]),0,1)).ucfirst(substr(trim($user_name_exp[0]),0,1));
			if($counter==1){
				$styleDisplay=" style='display:block; cursor:hand;' onClick=\"expandHiddenRows('".$problemID."',$numRows,'block');\"";
			}else{
				$styleDisplay=" style='display:none; cursor:hand;' onClick=\"expandHiddenRows('".$problemID."',$numRows,'none');\"";
			}
			$return .="<tr $styleDisplay id='trIDS".$problemID."_".$counter."'>
						<td class='text_10' style=\"width:34px; text-align:center;border:none;width:60px\">".ucfirst(substr($resRow["status"],0,1))."</td>		
						<td class='text_10' style=\"text-align:center;border:none;width:110px\">".$resRow["statusdate"]."</td>
						<td class='text_10' style=\"text-align:left;border:none;width:100px\">".$resRow["statustime"]."</td>
						<td class='text_10' style=\"text-align:center;border:none;width:60px\">".$user_name."</td>";
						if($counter == 1 and $numRows > 1){
							$return .= "<td style=\"border:none;width:20px\" class='text_10' id=\"tdImageArrow_".$problemID."\">";
							$return .= "<img src=\"../../../images/arr_dn.gif\">";
							$return .= "</td>";
						}
						else{
							$return .= "<td class='text_10' style=\"border:none;width:20px;\">";
							$return .= "&nbsp;&nbsp;";
							$return .= "</td>";
						}
			$return .= "</tr>";
			$counter++;
		}
	}
	$return.="</table>";	
	return $return;
}
?>